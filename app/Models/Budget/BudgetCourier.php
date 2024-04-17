<?php

namespace App\Models\Budget;

use App\Models\Agency;
use App\Models\BroadcastPusher;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Setting, Date, Mail;
use Mpdf\Mpdf;

class BudgetCourier extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_budgets';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'budgets_courier';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'budget_no', 'customer_id', 'name', 'address', 'zip_code', 'city', 'country', 'email', 'phone',
        'animals', 'airports', 'services', 'intro', 'transport_info', 'payment_conditions', 'geral_conditions', 'customer_request',
        'total', 'total_vat', 'pickup_address', 'delivery_address', 'status', 'budget_date', 'status_date', 'validity_days', 'validity_date',
        'locale', 'model_id', 'type', 'goods', 'obs', 'box_dimensions', 'geral_conditions_separated'
    ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = ['status_date', 'validity_date'];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'  => 'required',
        'email' => 'required',
        'phone' => 'required',
    );

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setBudgetCode()
    {
        $this->save();

        $total = BudgetCourier::filterSource()->withTrashed()->count();
        $code = date('ym');
        $code.= str_pad($total, 5, "0", STR_PAD_LEFT);

        $this->budget_no = $code;
        $this->save();
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel, $message)
    {
        $sourceClass = 'BudgetAnimal';
        $sourceId    = $this->id;

        $agencies = Agency::filterSource()->pluck('id')->toArray();

        //get notification recipients
        $recipients = \App\Models\User::where(function($q) use($agencies) {
            $q->where(function($q) use ($agencies){
                    foreach($agencies as $agency) {
                        $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                    }
                });
            })
            ->whereHas('roles.perms', function($query) {
                $query->whereName('budgets');
            })
            ->get(['id']);

        foreach($recipients as $user) {
            $notification = Notification::firstOrNew([
                'source_class'  => $sourceClass,
                'source_id'     => $sourceId,
                'recipient'     => $user->id
            ]);

            $notification->source_class = $sourceClass;
            $notification->source_id    = $sourceId;
            $notification->recipient    = $user->id;
            $notification->message      = $message;
            $notification->alert_at     = date('Y-m-d H:i:s');
            $notification->read         = false;
            $notification->save();
        }

        if($notification)  {
            $notification->setPusher($channel ? $channel : BroadcastPusher::getGlobalChannel());
        }

        return true;
    }


    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function deleteNotification()
    {
        return Notification::where('source_class', 'BudgetCourier')
            ->where('source_id', $this->id)
            ->delete();
    }

    /**
     * Create adhesive labels
     *
     * @param \App\Models\type $shipmentsIds
     * @param \App\Models\type $useAgenciesLogo
     * @param type $source [admin|customer]
     * @return type
     */
    public static function printBudget($ids, $outputMode = 'I'){

        $budgets = self::filterSource()
            ->whereIn('id', $ids)
            ->get();

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 8,
            'margin_right'  => 8,
            'margin_top'    => 34,
            'margin_bottom' => 25,
            'margin_header' => 0,
            'margin_footer' => 0,

            /*'format'        => 'A4',
            'margin_left'   => 8,
            'margin_right'  => 8,
            'margin_top'    => 34,
            'margin_bottom' => 22,
            'margin_header' => 0,
            'margin_footer' => 0,*/
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($budgets as $budget) {

            if($budget->locale != 'pt') {
                $docTitle = 'Quote '.$budget->budget_no;
                $services = BudgetCourierService::filterSource()->get()->pluck('name_' . $budget->locale, 'id')->toArray();
            } else {
                $docTitle = 'Orçamento '.$budget->budget_no;
                $services = BudgetCourierService::filterSource()->get()->pluck('name', 'id')->toArray();
            }

            $courierServices = Service::filterAgencies()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $background = '/uploads/pdf/bg_v.png';
            if($budget->type == 'animals') {
                $background = '/uploads/pdf/budget_animal_bg.png';
            }

            $budgetDate   = new \Carbon\Carbon($budget->budget_date);
            $validityDate = new \Carbon\Carbon($budget->validity_date);

            $data = [
                'courierServices' => $courierServices,
                'budget'        => $budget,
                'services'      => $services,
                'documentTitle' => $docTitle,
                'background'    => $background,
                'budgetDate'    => $budgetDate,
                'validityDate'  => $validityDate,
                'page'          => null
            ];





            $data['view'] = 'admin.budgets.budgets_courier.pdf.budget';
            $mpdf->WriteHTML(view('admin.budgets.budgets_courier.pdf.layouts.budget', $data)->render());


            if($budget->geral_conditions_separated && $budget->geral_conditions) {
                $data['page'] = 'geral_conditions';

                $mpdf->AddPageByArray([
                    'format'        => 'A4',
                    'margin_left'   => 8,
                    'margin_right'  => 8,
                    'margin_top'    => 34,
                    'margin_bottom' => 22,
                    'margin_header' => 0,
                    'margin_footer' => 0,
                ]);
                $mpdf->shrink_tables_to_fit = true;
                $mpdf->WriteHTML(view('admin.budgets.budgets_courier.pdf.layouts.budget', $data)->render()); //write
            }

        }

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output($docTitle.'.pdf', $outputMode); //output to screen

        exit;
    }

    /**
     * Send cancelation information email
     *
     * @param null $date
     */
    public function sendReminderEmail(){

        $budget = $this;

        $locale = $budget->locale;

        $subject = $budget->name.', aguardamos notícias suas.';
        if($budget->locale == 'en') {
            $subject = $budget->name.', we wainting for news.';
        }

        $emails = validateNotificationEmails($budget->email);
        $emails = $emails['valid'];

        if (!empty($emails)) {
            Mail::send('emails.budgets.reminder', compact('budget', 'locale'), function ($message) use ($emails, $budget, $subject) {
                $message->to($emails);
                $message->subject('[ORC-'.$budget->budget_no.'] ' . $subject);
            });

            if (count(Mail::failures()) > 0) {
                throw new Exception('Falhou o envio do e-mail para o remetente. Tente de novo.');
            }
        }
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public static function reminderBudget($date = null)
    {
        $date = new Date($date);

        $limitDays = explode(',', Setting::get('budgets_mail_reminder_days'));

        $dates = [];
        foreach ($limitDays as $day) {
            $dates[] = $date->copy()->addDays($day)->format('Y-m-d');
        }

        $budgetsReminder = BudgetCourier::filterSource()
            ->whereIn('validity_date', $dates)
            ->whereIn('status', [Budget::STATUS_WAINTING_CUSTOMER])
            ->get();

        foreach ($budgetsReminder as $budget) {
            if(Setting::get('budgets_mail_reminder_active') && Setting::get('budgets_mail_reminder_html')) {
                $budget->sendReminderEmail();
            }
        }
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public static function cancelOutdated($date = null)
    {
        if(empty($date)) {
            $date = new Date();
            $date = $date->subDays(1)->format('Y-m-d');
        }

        $budgetsOutdated = BudgetCourier::filterSource()
                ->where('validity_date', $date)
                ->whereIn('status', [Budget::STATUS_WAINTING_CUSTOMER, Budget::STATUS_PENDING])
                ->get();

        foreach ($budgetsOutdated as $budget) {

            $history = new BudgetCourierHistory();
            $history->budget_id   = $budget->id;
            $history->status      = Budget::STATUS_OUTDATED;
            $history->operator_id = $budget->operator_id;
            $history->save();


            $budget->status       = Budget::STATUS_OUTDATED;
            $budget->status_date  = date('Y-m-d H:i:s');
            $budget->save();

            //update budget email report
            if(hasModule('budgets')) {
                $budgetEmail = Budget::where('courier_budget_id', $budget->id)->first();
                if(!empty($budgetEmail)) {
                    $budgetEmail->status = $budget->status;
                    $budgetEmail->save();
                }
            }
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function history()
    {
        return $this->hasMany('App\Models\Budget\BudgetCourierHistory', 'budget_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */
    public function scopeFilterAgencies($query)
    {
        return $query->whereSource(config('app.source'));
    }

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
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = empty($value) ? null : $value;
    }

    public function setTotalVatAttribute($value)
    {
        $this->attributes['total_vat'] = empty($value) ? null : $value;
    }

    public function setGoodsAttribute($value)
    {
        $this->attributes['goods'] = empty($value) ? null : json_encode($value);
    }

    public function setAnimalsAttribute($value)
    {
        $this->attributes['animals'] = empty($value) ? null : json_encode($value);
    }

    public function setAirportsAttribute($value)
    {
        $this->attributes['airports'] = empty($value) ? null : json_encode($value);
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = empty($value) ? null : json_encode($value);
    }

    public function getGoodsAttribute($value)
    {
        return json_decode($value);
    }

    public function getAnimalsAttribute($value)
    {
        return json_decode($value);
    }

    public function getAirportsAttribute($value)
    {
        return json_decode($value);
    }

    public function getServicesAttribute($value)
    {
        return json_decode($value);
    }

}
