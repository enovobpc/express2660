<?php

namespace App\Models;

use DB, Setting, Date, Auth;
use Mockery\Exception;

class Backup extends BaseModel
{

    //use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'backups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'name', 'ids', 'min_date', 'max_date', 'restored_at', 'restored_by'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'source'   => 'required',
        'min_date' => 'required',
        'max_date' => 'required',
    );

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when
    | getting or setting them. Simply define a 'getFooAttribute' method on your model
    | to declare an accessor. Keep in mind that the methods should follow camel-casing,
    | even though your database columns are snake-case.
    |
    */
    public function setIdsAttribute($value) {
        $this->attributes['ids'] = empty($value) ? null : json_encode($value);
    }

    public function getIdsAttribute($value) {
        return empty($value) ? [] : json_decode($value);
    }

    public function getMinDateAttribute($value) {
        return new Date($value);
    }

    public function getMaxDateAttribute($value) {
        return new Date($value);
    }

    public function getRestoredAtAttribute($value) {
        return empty($value) ? null : new Date($value);
    }


    /**
     * Resets a previously restored backups
     *
     * @return array
     */
    public static function resetBackups($backupId = null) {

        if(!is_null($backupId)) {
            $backups = Backup::filterSource()->whereNotNull('restored_at')->where('id', $backupId)->get(['id']);
        } else {
            $backups = Backup::filterSource()->whereNotNull('restored_at')->get(['id']);
        }

        if(!$backups->isEmpty()) {
            foreach ($backups as $backup) {
                $result = self::executeBackup($backup->id);
            }
        }

        return $result;
    }


    /**
     * Runs the backup process
     *
     * @param $backupId Restore only the shipments assigned to this backup
     * @return bool
     */
    public static function executeBackup($backupId = null, $ids = null, $date = null) {

        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 0);

        $backupPeriod = 3; //3 months

        $agencies  = Agency::filterSource()->pluck('id')->toArray();

        if(!is_null($backupId)) {
            $backup         = Backup::filterSource()->findOrFail($backupId);
            $ids            = (array) $backup->ids;
            $shipmentsIds   = @$ids['shipments'];
            $billingIds     = @$ids['billing'];
            $budgetsIds     = @$ids['budgets'];
            $airWaybillsIds = @$ids['air_waybills'];
            $lastDate       = $backup->min_date;
            $startDate      = $backup->max_date;
        } else if(is_null($ids)) {
            $backup = new Backup();

            $today     = Date::today();
            $startDate = $today->subMonths($backupPeriod);
            $startDate = $startDate->format('Y-m-d');
            $startDate = '2018-07-31';

            //get shipments to backup
            $shipmentsIds = Shipment::whereIn('agency_id', $agencies)
                        ->where('date', '<=', $startDate)
                        ->withTrashed()
                        ->pluck('id')
                        ->toArray();

dd($shipmentsIds);
            //get billing data to backup
            $dt = new Date($startDate);
            $billingIds = CustomerBilling::whereHas('customer', function($q) use($agencies) {
                            $q->whereIn('agency_id', $agencies);
                        })
                        ->where('year',  '<=', $dt->year)
                        ->where('month', '<=', $dt->month)
                        ->withTrashed()
                        ->pluck('id')
                        ->toArray();

            //get budgets to backup
            $budgetsIds = [];
            if(hasModule('budgets')) {
                $budgetsIds = Budget::filterSource()
                    ->where('date', '<=', $startDate)
                    ->withTrashed()
                    ->pluck('id')
                    ->toArray();
            }

            //get air waybills to backup
            $airWaybills = [];
            if(hasModule('waybills')) {
                $airWaybillsIds = AirWaybill\Waybill::filterSource()
                    ->where('date', '<=', $startDate)
                    ->withTrashed()
                    ->pluck('id')
                    ->toArray();
            }

            if($shipmentsIds) {
                $lastDate = Shipment::whereIn('agency_id', $agencies)
                    ->where('date', '<=', $startDate)
                    ->withTrashed()
                    ->orderBy('date', 'asc')
                    ->first();

                $lastDate = $lastDate ? $lastDate->date : null;
            }

        }


        if(count($shipmentsIds) >= 40000) {
            throw new Exception('Impossível realizar backup. Mais de 40.000 resultados.');
        }

        if($shipmentsIds) {
            DB::beginTransaction();

            //execute backup
            try {
                $backup->backupTableByIds('shipments', $shipmentsIds);
                $backup->backupTableByIds('shipments_assigned_expenses', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('shipments_packs_dimensions', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('shipments_history', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('shipments_incidences_resolutions', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('shipments_traceability', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('shipments_warnings_ignored', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('refunds_control', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('refunds_control_agencies', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('payments_at_recipient_control', $shipmentsIds, 'shipment_id');
                $backup->backupTableByIds('customers_billing', $billingIds);

                if(hasModule('budgets')) {
                    $backup->backupTableByIds('budgets', $budgetsIds);
                    $backup->backupTableByIds('budgets_messages', $budgetsIds, 'budget_id');
                    $backup->backupTableByIds('budgets_proposes', $budgetsIds, 'budget_id');
                }

                if(hasModule('waybills')) {
                    $backup->backupTableByIds('air_waybills', $airWaybillsIds);
                    $backup->backupTableByIds('air_waybills_assigned_expenses', $airWaybillsIds, 'waybill_id');
                }

            } catch (\Exception $e) {
                DB::rollback();
                throw new Exception($e->getMessage());
            }

            DB::commit();

            //execute clean of production database
            if(hasModule('budgets')) {
                $backup->cleanOriginalTable('budgets_proposes', $budgetsIds, 'budget_id');
                $backup->cleanOriginalTable('budgets_messages', $budgetsIds, 'budget_id');
                $backup->cleanOriginalTable('budgets', $budgetsIds);
            }

            $backup->cleanOriginalTable('customers_billing', $billingIds);
            $backup->cleanOriginalTable('payments_at_recipient_control', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('refunds_control_agencies', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('refunds_control', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('shipments_warnings_ignored', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('shipments_traceability', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('shipments_incidences_resolutions', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('shipments_history', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('shipments_packs_dimensions', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('shipments_assigned_expenses', $shipmentsIds, 'shipment_id');
            $backup->cleanOriginalTable('shipments', $shipmentsIds);


            if(hasModule('waybills')) {
                $backup->cleanOriginalTable('air_waybills_assigned_expenses', $airWaybillsIds, 'waybill_id');
                $backup->cleanOriginalTable('air_waybills', $airWaybillsIds);
            }

            $ids = [
                'shipments'     => @$shipmentsIds,
                'billing'       => @$billingIds,
                'budgets'       => @$budgetsIds,
                'air_waybills'  => @$airWaybillsIds,
            ];

            $backup->source         = config('app.source');
            $backup->min_date       = $lastDate;
            $backup->max_date       = $startDate;
            $backup->restored_at    = null;
            $backup->restored_by    = null;
            $backup->ids            = $ids;
            $backup->save();

            return true;
        }

        return false;
    }

    /**
     * Run the backup restore process
     *
     * @param $backupId
     * @return bool
     */
    public static function restoreBackup($backupId) {

        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 0);

        $backup = Backup::filterSource()->findOrFail($backupId);

        $ids            = (array) $backup->ids;
        $shipmentsIds   = @$ids['shipments'];
        $billingIds     = @$ids['billing'];
        $budgetsIds     = @$ids['budgets'];
        $airWaybillsIds = @$ids['air_waybills'];

        DB::beginTransaction();
        try {

            $backup->restoreBackupTable('shipments', $shipmentsIds);
            $backup->restoreBackupTable('shipments_assigned_expenses', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('shipments_packs_dimensions', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('shipments_history', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('shipments_incidences_resolutions', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('shipments_traceability', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('shipments_warnings_ignored', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('refunds_control', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('refunds_control_agencies', $shipmentsIds, 'shipment_id');
            $backup->restoreBackupTable('customers_billing', $billingIds);

            if(hasModule('budgets')) {
                $backup->restoreBackupTable('budgets', $budgetsIds);
                $backup->restoreBackupTable('budgets_messages', $budgetsIds, 'budget_id');
                $backup->restoreBackupTable('budgets_proposes', $budgetsIds, 'budget_id');
            }

            if(hasModule('waybills')) {
                $backup->restoreBackupTable('air_waybills', $airWaybillsIds);
                $backup->restoreBackupTable('air_waybills_assigned_expenses', $airWaybillsIds, 'waybill_id');
            }

        } catch (\Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
        DB::commit();

        //execute clean of production database
        if(hasModule('budgets')) {
            $backup->cleanBackupTable('budgets_proposes', $budgetsIds, 'budget_id');
            $backup->cleanBackupTable('budgets_messages', $budgetsIds, 'budget_id');
            $backup->cleanBackupTable('budgets', $budgetsIds);
        }

        if(hasModule('waybills')) {
            $backup->cleanBackupTable('air_waybills_assigned_expenses', $airWaybillsIds, 'waybill_id');
            $backup->cleanBackupTable('air_waybills', $airWaybillsIds);
        }

        $backup->cleanBackupTable('customers_billing', $billingIds);
        $backup->cleanBackupTable('payments_at_recipient_control', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('refunds_control_agencies', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('refunds_control', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('shipments_warnings_ignored', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('shipments_traceability', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('shipments_incidences_resolutions', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('shipments_history', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('shipments_packs_dimensions', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('shipments_assigned_expenses', $shipmentsIds, 'shipment_id');
        $backup->cleanBackupTable('shipments', $shipmentsIds);


        //change backup status
        $backup->restored_at = Date::now();
        $backup->restored_by = Auth::user()->id;
        $backup->save();

        return true;
    }

    /**
     * Execute backup of a single table
     *
     * @param $table
     * @param $ids
     * @param string $field
     * @return mixed
     */
    public function backupTableByIds($table, $ids, $field = 'id') {

        $ids = implode(',', $ids);
        $backupTableByIds   = env('DB_DATABASE_BACKUP'). '.' . $table;
        $originalTable = env('DB_DATABASE'). '.' . $table;

        if(!empty($ids)) {
            try {
                $sql = 'INSERT ' . $backupTableByIds .'
                     SELECT * FROM ' . $originalTable .'
                     WHERE ' . $originalTable . '.' . $field . ' in ('.$ids.')';

                $result = DB::insert($sql);

            } catch (\Exception $e) {
                dd($e->getMessage());
                throw new Exception($e->getMessage());
            }

            return $result;
        }
    }

    /**
     * Execute backup shipments
     * @return mixed
     */
    public function cleanOriginalTable($table, $ids, $field = 'id') {

        $ids = implode(',', $ids);
        $originalTable = env('DB_DATABASE'). '.' . $table;

        if(!empty($ids)) {
            try {
                $sql = 'DELETE FROM ' . $originalTable . '
                    WHERE ' . $originalTable . '.' . $field . ' in (' . $ids . ')';

                $result = DB::delete($sql);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
            return $result;
        }
        return true;
    }

    /**
     * Execute backup shipments
     * @return mixed
     */
    public function cleanBackupTable($table, $ids, $field = 'id') {

        $ids = implode(',', $ids);
        $backupTableByIds = env('DB_DATABASE_BACKUP'). '.' . $table;

        if(!empty($ids)) {
            try {

                $sql = 'DELETE FROM ' . $backupTableByIds . '
                 WHERE ' . $backupTableByIds . '.' . $field . ' in (' . $ids . ')';

                $result = DB::delete($sql);

            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
            return $result;
        }

        return true;
    }

    /**
     * Execute backup shipments
     * @return mixed
     */
    public function restoreBackupTable($table, $ids, $field = 'id') {

        $ids = implode(',', $ids);
        $backupTableByIds   = env('DB_DATABASE_BACKUP'). '.' . $table;
        $originalTable = env('DB_DATABASE'). '.' . $table;

        if(!empty($ids)) {
            try {
                $sql = 'INSERT ' . $originalTable . '
                 SELECT * FROM ' . $backupTableByIds . '
                 WHERE ' . $backupTableByIds . '.' . $field . ' in (' . $ids . ')';

                $result = DB::insert($sql);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
            return $result;
        }
        return true;
    }
    
}
