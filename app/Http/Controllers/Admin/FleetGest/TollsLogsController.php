<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Vehicle;
use App\Models\FleetGest\TollLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Excel, Response, Setting, DB;
use Carbon\Carbon;

class TollsLogsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_tolls_log';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_tolls_log']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //    public function index() {
    //    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //    public function create(Request $request) {
    //    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //    public function store(Request $request) {
    //    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $date = $request->get('date');

        $logs = TollLog::filterSource()
            ->where('vehicle_id', $id)
            ->whereRaw('DATE(entry_date) = "' . $date . '"')
            ->get();

        return view('admin.fleet.tolls.show', compact('logs', 'date'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //    public function edit($id) {
    //    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //    public function update(Request $request, $id) {
    //    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        TollLog::flushCache(TollLog::CACHE_TAG);

        $date = $request->get('date');

        $result = TollLog::filterSource()
            ->where('vehicle_id', $id)
            ->whereRaw('DATE(entry_date) = "' . $date . '"')
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        TollLog::flushCache(TollLog::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $rows = TollLog::filterSource()
            ->whereIn('id', $ids)
            ->get();

        foreach ($rows as $row) {
            $result = $row->delete();
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = TollLog::with('vehicle')
            ->filterSource()
            ->groupBy(DB::raw('CONCAT(DATE(entry_date), vehicle_id)'))
            ->select([
                '*',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as sum_total')
            ]);

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('entry_date', [$dtMin . ' 00:00:00', $dtMax . ' 23:59:59']);
        }

        //filter vehicle
        $value = $request->get('vehicle');
        if ($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('vehicle_id', function ($row) {
                return view('admin.fleet.tolls.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('entry_date', function ($row) {
                return $row->entry_date->format('Y-m-d');
            })
            ->add_column('entry_point', function ($row) {
                return $row->entry_point;
            })
            ->add_column('exit_point', function ($row) {
                return $row->exit_point;
            })
            ->edit_column('provider_id', function ($row) {
                return view('admin.fleet.tolls.datatables.provider', compact('row'))->render();
            })
            ->add_column('count', function ($row) {
                return $row->count;
            })
            ->edit_column('total', function ($row) {
                $row->total = $row->sum_total;
                return view('admin.fleet.vehicles.datatables.total', compact('row'))->render();
            })
            ->add_column('class', function ($row) {
                return $row->vehicle->class;
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.fleet.tolls.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Import Via Verde excel file
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {

        config([
            //'excel.import.startRow' => 8,
            'excel.import.encoding.input' => 'ISO-8859-1',
        ]);

        $providerId = $request->get('provider_id');

        $excel = Excel::load($request->file->getRealPath());

        if (!$excel) {
            return Redirect::back()->with('error', 'O ficheiro carregado não é suportado.');
        }

        if (!($excel->first()->has('entrada') || $excel->first()->has('license_plate'))) {
            if ($request->ajax()) {
                return Response::json([
                    'result'      => false,
                    'feedback'    => 'O ficheiro carregado não é um ficheiro Via Verde.',
                    'totalErrors' => 0,
                    'errors'      => null
                ]);
            }
            return Redirect::back()->with('error', 'O ficheiro carregado não é um ficheiro Via Verde.');
        }

        $errors = [];
        $totalSuccess = 0;

        Excel::load($request->file->getRealPath(), function ($reader)  use ($request, &$errors, &$totalSuccess, $providerId) {

            $reader->each(function ($row) use ($request, &$errors, &$totalSuccess, $providerId) {

                $fileEN = true;
                if (!@$row['license_plate']) {
                    $fileEN = false;
                } else {
                    $row = json_encode($row);
                    $row = json_decode($row, true);
                }

                if (!$fileEN) {
                    $row = mapArrayKeys($row->toArray(), config('webservices_mapping.viaverde'));
                }

                $vehicle = Vehicle::filterSource()
                    ->where('license_plate', $row['license_plate'])
                    ->first();

                if (empty($vehicle)) {
                    $errors[] = [
                        'code'    => $row['license_plate'],
                        'message' => 'Viatura não encontrada na aplicação.'
                    ];
                } else {

                    if (!$fileEN) {
                        /* $row['exit_date'] = $row['exit_date'] . ' ' . $row['exit_hour'] . ':00';
                         if (empty($row['entry_date'])) {
                             $row['entry_date'] = $row['exit_date'];
                             $row['entry_point'] = $row['exit_point'];
                         } else {
                             if (empty(@$row['entry_hour'])) {
                                 $row['entry_hour'] = '00:00';
                             }

                             if (@$row['entry_hour']) {
                                 $row['entry_date'] = $row['entry_date'] . ' ' . $row['entry_hour'] . ':00';
                             }
                         }*/
                        $row['entry_date'] = empty($row['entry_date']) ? $row['exit_date'] : $row['entry_date'];
                    } else {
                        $row['total'] = $row['value'];
                    }

                    if (strlen(@$row['toll_provider']) == 2) {
                        $row['toll_provider'] = $this->getTollProviderName(@$row['toll_provider']);
                    }

                    $row['total'] = str_replace(',', '.', $row['total']);
                    try {
                        if (!empty($row['entry_date']) && !empty($row['exit_date'])) {

                            if (!empty($row['entry_date']['date'])) {
                                $row['entry_date']  = $row['entry_date']['date'];
                                $row['exit_date']   = $row['exit_date']['date'];
                            }

                            $entryDate  = Carbon::createFromTimestamp(strtotime($row['entry_date']));
                            $exitDate   = Carbon::createFromTimestamp(strtotime($row['exit_date']));

                            if (empty($row['entry_hour'])) {
                                $row['entry_date'] = $entryDate->format('Y-m-d H:i:s');
                            } else {
                                $row['entry_date'] = $entryDate->format('Y-m-d') . ' ' . $row['entry_hour'] . ':00';
                            }

                            if (empty($row['exit_hour'])) {
                                $row['exit_date']  = $exitDate->format('Y-m-d H:i:s');
                            } else {
                                $row['exit_date']  = $exitDate->format('Y-m-d') . ' ' . $row['exit_hour'] . ':00';
                            }

                            $toll = TollLog::firstOrNew([
                                'vehicle_id'    => $vehicle->id,
                                'entry_date'    => $row['entry_date'],
                                'exit_date'     => $row['exit_date']
                            ]);

                            $toll->fill($row);
                            $toll->provider_id = $providerId;
                            $toll->save();

                            $totalSuccess++;
                        }
                    } catch (\Exception $e) {
                        dd($row);
                    }
                };
            });
        });

        if ($request->ajax()) {

            $totalErrors = count($errors);

            $result = empty($totalErrors) ? true : false;

            return Response::json([
                'result'      => $result,
                'feedback'    => 'Ficheiro importado com sucesso.',
                'html'        => view('admin.fleet.tolls.partials.import_errors', compact('errors', 'totalSuccess', 'totalErrors'))->render(),
                'totalErrors' => $totalErrors
            ]);
        }

        return Redirect::back()->with('success', 'Ficheiro importado com sucesso.');
    }

    /**
     * Return list of toll provider names
     * @param $tollProviderCode
     * @return mixed
     */
    public function getTollProviderName($tollProviderCode)
    {

        $operators = [
            'BR' => 'Brisa',
            'DL' => 'AEDL',
            'AA' => 'Autoestradas Atlântico',
            'BL' => 'Brisal',
            'LS' => 'Lusoponte',
            'VV' => 'Via Verde Portugal',
            'S1' => 'Scoutvias',
        ];

        if (isset($operators[$tollProviderCode])) {
            return $operators[$tollProviderCode];
        }

        return 'Infraestruturas Portugal';
    }
}
