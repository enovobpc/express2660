<?php

namespace App\Models;

use App\Models\Billing\ApiKey;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\CustomerResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Rememberable\Rememberable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Traits\FileTrait;
use Jenssegers\Date\Date;
use Validator, Setting, Hash, Auth, DB, File, Mail, Mpdf\Mpdf;

class Customer extends Authenticatable
{
    use Notifiable,
        HasApiTokens,
        SoftDeletes,
        Rememberable,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * Validator errors
     *
     * @var array
     */
    protected $errors;

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/customers';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_login', 'login_created_at', 'balance_last_update'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'code_abbrv', 'company_id', 'agency_id', 'type_id', 'name', 'contact_email', 'phone', 'mobile', 'address', 'zip_code', 'state', 'city', 'country',
        'vat', 'responsable', 'billing_name', 'billing_address', 'billing_zip_code', 'billing_city', 'billing_state', 'billing_country', 'default_invoice_type',
        'display_name', 'email', 'password', 'uncrypted_password', 'last_login', 'ip', 'active', 'enabled_services', 'hide_old_shipments', 'login_created_at',
        'seller_id', 'operator_id', 'payment_method', 'currency', 'courier', 'is_prospect', 'is_mensal', 'obs', 'obs_shipments', 'billing_code', 'website',
        'iban_refunds', 'has_prices', 'has_webservices', 'enabled_providers', 'enabled_pudo_providers', 'default_print', 'average_weight',
        'custom_expenses', 'custom_volumetries', 'assigned_recipient_id', 'price_table_id', 'prices_tables', 'has_billing_info', 'view_parent_shipments',
        'balance_unpaid_count', 'balance_expired_count','balance_total_credit', 'balance_total_debit', 'balance_total',
        'balance_total_expired', 'balance_last_update', 'balance_total_unpaid', 'balance_count_expired', 'balance_count_unpaid', 'balance_divergence', 
        'daily_report', 'daily_report_email', 'locale', 'map_lat', 'map_lng', 'map_preview', 'refunds_email', 'billing_email', 'billing_reference',
        'unpaid_invoices_limit', 'unpaid_invoices_credit', 'monthly_plafound', 'shipments_daily_limit_hour', 'distance_km', 'distance_from_agency',
        'shipments_features', 'shipments_format', 'shipments_service', 'pickup_schedule', 'pickup_daily', 'shipments_qtd_day', 'shipments_qtd_week', 'shipments_qtd_month',
        'shipments_qtd_local', 'shipments_qtd_nacional', 'shipments_qtd_spain', 'shipments_qtd_internacional', 'shipments_qtd_islands',
        'shipments_qtd_devolutions', 'shipments_qtd_pickups', 'shipments_qtd_charge', 'shipments_average_weight', 'obs_business', 'business_status',
        'route_id', 'hide_billing', 'hide_budget_btn', 'locale', 'hide_incidences_menu', 'hide_products_sales', 'enabled_packages', 'insurance_tax', 'fuel_tax', 'insurance_tax',
        'shipping_services_notify', 'shipping_status_notify', 'shipping_status_notify_recipient', 'shipping_status_notify_method', 'customer_sms_text', 'ignore_mass_billing',
        'is_active', 'avg_cost', 'is_particular', 'show_reference', 'always_cod', 'is_validated', 'sms_enabled', 'default_service', 'default_printer_a4', 'default_printer_labels', 'other_name',
        'bank_code', 'bank_name', 'bank_iban', 'bank_swift', 'bank_mandate', 'bank_mandate_date', 'product_price', 'billing_discount_value', 'time_assembly', 'time_delivering', 'is_commercial', 'hide_btn_shipments',
        'default_payment_method_id', 'is_independent', 'custom_billing_items'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * Validator rules
     *
     * @var array
     */
    public $rules = [
        'agency_id' => 'required',
        'type_id'   => 'required',
        'name'      => 'required',
    ];

    /**
     * Validator custom attributes
     *
     * @var array
     */
    protected $customAttributes = [
        'agency_id' => 'Agência',
        'code'      => 'Código',
        'name'      => 'Designação Social',
    ];

    /**
     * Set code
     * @param bool $save
     */
    public function setCode($save = true)
    {

        if (!@$this->code) {
            $prefix = empty(Setting::get('customers_code_prefix')) ? '' : Setting::get('customers_code_prefix');

            if (Setting::get('customers_use_empty_codes')) {

                $allCodes = Customer::filterSource()
                    ->where('code', '<>', 'CFINAL')
                    ->orderByRaw('CAST(REPLACE(code, "' . $prefix . '", "") as unsigned) asc')
                    ->select([DB::raw('(REPLACE(code, "' . $prefix . '", "")) as code')])
                    ->pluck('code')
                    ->toArray();


                $allCodes = array_values(array_filter(array_map('intval', $allCodes)));
                $allCodes = array_unique($allCodes);
                $maxCode = end($allCodes);
                $maxCode = $maxCode > 999999 ? 999999 : $maxCode;
                $possibleCodes = range(1, $maxCode);

                $diff = array_diff($possibleCodes, $allCodes);

                if (empty($diff)) {
                    $code = end($allCodes) + 1;
                } else {
                    $code = @array_values($diff)[0];
                }
            } else {
                $lastCode = Customer::filterSource()
                    ->where('code', '<>', 'CFINAL')
                    ->orderByRaw('CAST(REPLACE(code, "' . $prefix . '", "") as unsigned) desc')
                    ->first([DB::raw('(REPLACE(code, "' . $prefix . '", "")) as code')]);

                if (empty($lastCode)) {
                    $code = 1;
                } else {
                    if (empty($prefix)) {
                        $lastCode->code = preg_replace('/[^0-9]/', '', (@$lastCode->code ? $lastCode->code : 0));
                    }

                    $code = empty($lastCode->code) ? 0 : $lastCode->code + 1;
                }
            }

            $padLength = intval(Setting::get('customers_code_pad_left'));
            $code = str_pad($code, $padLength, '0', STR_PAD_LEFT);
            $code = Setting::get('customers_code_prefix') . $code;

            if (strlen($code) > 8) {
                $code = null;
            }

            if ($save) {
                $this->code = $code;
                $this->save();
            }

            return $code;
        } elseif ($save) {
            $this->save();
        }

        return $this->code;
    }

    /**
     * Store customer on database
     * @return mixed
     */
    public function storeOnCoreDB()
    {

        if (env('APP_ENV') != 'local' && empty($this->customer_id) && $this->code != 'CFINAL' && !$this->final_consumer) { //ignora departamentos

            $customer = $this;

            $dataArr = [
                'source' => config('app.source'),
                'source_id' => $customer->id,
                'agency_id' => $customer->agency_id,
                'code' => $customer->code,
                'code_abbrv' => $customer->code_abbrv,
                'name' => $customer->name,
                'address' => $customer->address,
                'zip_code' => $customer->zip_code,
                'city' => $customer->city,
                'state' => $customer->state,
                'country' => $customer->country,
                'map_lat' => $customer->map_lat,
                'map_lng' => $customer->map_lng,
                'vat' => $customer->vat,
                'billing_code' => $customer->billing_code,
                'billing_name' => $customer->billing_name,
                'billing_address' => $customer->billing_address,
                'billing_zip_code' => $customer->billing_zip_code,
                'billing_city' => $customer->billing_city,
                'billing_state' => $customer->billing_state,
                'billing_country' => $customer->billing_country,
                'billing_email' => $customer->billing_email,
                'refunds_email' => $customer->refunds_email,
                'responsable' => $customer->responsable,
                'contact_email' => $customer->contact_email,
                'phone' => $customer->phone,
                'mobile' => $customer->mobile,
                'website' => $customer->website,
                'is_particular' => $customer->is_particular,
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at,
            ];

            $coreCustomer = DB::connection(env('DB_CONNECTION_CORE'))
                ->table('customers')
                ->where('source', config('app.source'))
                ->where('source_id', $customer->id)
                ->first();

            if ($coreCustomer) {
                return DB::connection(env('DB_CONNECTION_CORE'))
                    ->table('customers')
                    ->where('source', config('app.source'))
                    ->where('source_id', $customer->id)
                    ->update($dataArr);
            } else {
                return DB::connection(env('DB_CONNECTION_CORE'))
                    ->table('customers')
                    ->insert($dataArr);
            }
        }

        return true;
    }

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setBankMandateCode($save = false)
    {
        $maxMandate = Customer::withTrashed()->where('bank_mandate', 'like',  date('y') . '%')->max('bank_mandate');
        $maxMandate = (int) substr($maxMandate, 4);
        $maxMandate++;

        $code = date('ym') . str_pad($maxMandate, 4, "0", STR_PAD_LEFT);

        if ($save) {
            $this->bank_mandate = $code;
            $this->save();
        }

        return $code;
    }

    /**
     * Return next model ID
     * @param $query
     * @return mixed
     */
    public function nextId()
    {
        return Customer::filterAgencies()
            ->isDepartment(false)
            ->isProspect(false)
            ->where('id', '>', $this->id)
            ->min('id');
    }

    /**
     * Return previous model ID
     * @param $query
     * @return mixed
     */
    public function previousId()
    {
        return Customer::filterAgencies()
            ->isDepartment(false)
            ->isProspect(false)
            ->where('id', '<', $this->id)
            ->max('id');
    }

    /**
     * Print SEPA Authorixzation
     *
     * @param $proformaId
     * @return mixed
     */
    public static function printSepaAuthorization($customerId, $outputFormat = 'I') {

        ini_set("pcre.backtrack_limit", "5000000");

        $customer = Customer::find($customerId);
        $proposal = null;

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 28,
            'margin_bottom' => 20,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'customer'      => $customer,
            'documentTitle' => 'Autorização Débito Direto',
            'documentSubtitle' => $customer->name,
            'view'          => 'admin.printer.customers.sepa_authorization_simple'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render());

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Autorização Débito Direto - '.$customer->name . '.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Store map preview
     * @return null|string
     */
    public function storeMapPreview()
    {

        $filepath = null;

        try {
            $folder = '/uploads/customers/maps/';
            $path   = public_path($folder);

            if (!File::exists($path)) {
                File::makeDirectory($path);
            }

            if ($this->map_lat && $this->map_lng) {
                $url      = static_map($this->map_lat, $this->map_lng, '400x250', 13);
                $filepath = $folder . $this->id . '_' . time() . '.png';
                file_put_contents(public_path($filepath), file_get_contents($url));
            }
        } catch (\Exception $e) {
            //dd($e->getMessage(). ' file '. $e->getFile(). ' Line '. $e->getLine());
        }

        return $filepath;
    }

    /**
     * Validate rules for this model
     *
     * @param array $data
     * @return boolean
     */
    public function validate($data)
    {
        $rules = array_intersect_key($this->rules, $data);
        $validator = Validator::make($data, $rules, [], $this->customAttributes);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * Get errors from validator
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Force syncronize and update balance account
     *
     * @param  string  $token
     * @return void
     */
    public function updateBalance($debug = false)
    {

        //atualiza todas as faturas
        /* \DB::statement('update invoices set doc_total_debit=doc_total where customer_id='.$this->id.' and doc_type in ("invoice", "invoice-receipt", "simplified-invoice", "proforma-invoice", "internal-doc", "nodoc", "debit-note")');
        \DB::statement('update invoices set doc_total_credit = doc_total where customer_id='.$this->id.' and doc_type in ("credit-note", "regularization", "receipt")');
        \DB::statement('update invoices set doc_total_credit = (doc_total*-1), is_settle=1 where customer_id='.$this->id.' and doc_type in ("invoice-receipt", "simplified-invoice")');
        \DB::statement('update invoices set doc_total_balance = COALESCE((COALESCE(doc_total_credit, 0) + COALESCE(doc_total_debit, 0)), 0) where customer_id='.$this->id);
        */

        $invoices = Invoice::where('customer_id', $this->id)
                ->filterBalanceDocs()
                ->whereNull('deleted_at')
                ->orderBy('doc_date', 'asc')
                ->orderBy('id', 'asc')
                ->get([
                    'id', 'doc_date', 'due_date', 'is_settle', 'doc_type','doc_id', 'doc_total_pending',
                    'doc_total', 'doc_total_debit', 'doc_total_credit', 'doc_total_balance',
                    'customer_balance', 'sort'
                ]);


        $balanceSum     = 0;
        $balanceUnpaid  = 0;
        $balanceExpired = 0;
        $balanceCredits = 0;
        $balanceDebits  = 0;
        $today          = date('Y-m-d');

        if(!$invoices->isEmpty()) {
            foreach($invoices as $invoice) {

                if($invoice->doc_total > 0.00 && 
                ($invoice->doc_type == Invoice::DOC_TYPE_NC 
                || $invoice->doc_type == Invoice::DOC_TYPE_RC 
                || $invoice->doc_type == Invoice::DOC_TYPE_RG)) {
                    $invoice->doc_total = $invoice->doc_total * -1;
                } 

                if(!$invoice->is_settle) {

                     //conta não pagos
                    $balanceUnpaid++; 

                     //conta expirados
                    if($invoice->due_date < $today) {
                        $balanceExpired++;
                    }

                    $totalPending = $invoice->doc_total_pending ? $invoice->doc_total_pending : $invoice->doc_total; //o valor de conta corrente tem de observar o valor pendente.

                    //conta creditos
                    if($invoice->doc_total_credit < 0.00) {
                        $balanceCredits+= $totalPending;
                    }
                    
                    //conta debitos
                    if($invoice->doc_total_debit > 0.00) {
                        $balanceDebits+= $totalPending;
                    }  
                }

                $balanceSum+= $invoice->doc_total_balance;

                $updateArr = [];
                $updateArr['customer_balance'] = $balanceSum;

                $invoiceSort = '';
                if(!$invoice->sort) {
                    $invoiceSort = str_replace('-', '', $invoice->doc_date); 
                    $updateArr['sort'] = $invoiceSort . '_'.$invoice->id;
                }
                
                $invoice->update($updateArr);
            }
        }

        //garante apenas que variavel vai ter sempre sinal negativo
        $balanceCredits = $balanceCredits > 0.00 ? ($balanceCredits * -1) : $balanceCredits;

        //atualiza conta corrente cliente
        $updateArr = [
            'balance_total_debit'   => $balanceDebits,
            'balance_total_credit'  => $balanceCredits,
            'balance_unpaid_count'  => $balanceUnpaid,
            'balance_expired_count' => $balanceExpired,
            //'balance_total'         => $balanceSum
            'balance_total'         => $balanceDebits + $balanceCredits //isto não funciona porque considera o total dos documentos e não o que já está pago
        ];

        if(!$debug) {
            $this->update($updateArr); 
        }

        if($debug) {
            echo '<table>';
            echo '<tr>';
            echo '<th style="width:20px">ID</th>';
            echo '<th style="width:100px">Data</th>';
            echo '<th style="width:150px">Tipo</th>';
            echo '<th>Nº</th>';
            echo '<th style="width:120px">doc_total</th>';
            echo '<th></th>';
            echo '<th style="width:100px">doc_credit</th>';
            echo '<th style="width:100px">doc_debit</th>';
            echo '<th style="width:100px">doc_balance</th>';
            echo '<th style="width:100px">balance</th>';
            echo '<th style="width:200px">sort</th>';
            echo '</tr>';

            $balanceSum = 0;
            foreach($invoices as $invoice) {

                $balanceSum+= $invoice->doc_total_balance;

                echo '<tr>';
                echo '<td>'.$invoice->id.'</td>';
                echo '<td>'.$invoice->doc_date.'</td>';
                echo '<td>'.$invoice->doc_type.'</td>';
                echo '<td>'.$invoice->doc_id.'</td>';
                echo '<td style="text-align:right"><b>'.$invoice->doc_total.'</b></td>';
                echo '<td>|</td>';
                echo '<td style="text-align:right">'.$invoice->doc_total_credit.'</td>';
                echo '<td style="text-align:right">'.$invoice->doc_total_debit.'</td>';
                echo '<td style="text-align:right"><b>'.$invoice->doc_total_balance.'</b></td>';
                echo '<td style="text-align:right">'.$balanceSum.'</td>';
                echo '<td style="text-align:right"><b>'.$invoice->sort.'</b></td>';
                echo '</tr>';
            }
        
            echo '<tr>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td style="text-align:right"></td>';
            echo '<td></td>';
            echo '<td style="text-align:right">'.$balanceCredits.'</td>';
            echo '<td style="text-align:right">'.$balanceDebits.'</td>';
            echo '<td style="text-align:right"><b>'.money($balanceCredits + $balanceDebits).'</b></td>';
            echo '<td style="text-align:right">'.$balanceSum.'</td>';
            echo '</tr>';
            echo '</table>';
            echo '<h4>Saldo '.$balanceSum.'</h4>';
        }

        return $updateArr;
    }


    /**
     * Send e-mail with corrent account
     *
     * @param $customerId
     * @param $emails
     */
    public function sendEmailAccountBalance($email = null)
    {
        $customer   = $this;
        $email      = empty($email) ? ($customer->billing_email ? $customer->billing_email : $customer->contact_email) : $email;
        $customerId = str_pad($customer->id, 8, '0', STR_PAD_LEFT);
        $createdAt  = str_replace(' ', '', str_replace(':', '', str_replace('-', '', $customer->created_at)));

        $hash = base64_encode($customerId . $createdAt);
        $today = \Date::today();

        $invoices = Invoice::getPendingDocuments($customerId);

        $email = validateNotificationEmails($email);
        $email = $email['valid'];

        if ($customer->balance_total > 0.00 && !$invoices->isEmpty() && !empty($email)) {
            Mail::send('emails.billing.balance', compact('customer', 'invoices',  'today', 'hash'), function ($message) use ($email) {
                $message->to($email);
                $message->from(config('mail.from.address'), config('mail.from.name'));

                if(Setting::get('billing_email_cc')) {
                    $message->replyTo(Setting::get('billing_email_cc'));
                }
                    
                $message->subject('Extrato de Conta Corrente');
            });
        }

        return true;
    }

    public function getOldestUnpaidInvoice() {
        return Invoice::where('customer_id', $this->id)
            ->whereIn('doc_type', [Invoice::DOC_TYPE_FT, Invoice::DOC_TYPE_FS])
            ->where('is_settle', 0)
            ->where('is_deleted', 0)
            ->where('is_reversed', 0)
            ->where('is_scheduled', 0)
            ->orderBy('doc_date', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Set notification of signup new customer
     *
     * @return int
     */
    public function setSignupNotification($channel = null)
    {
        $source = null;

        $message = 'Novo cliente registado: ' . $this->code . ' - ' . $this->name;
        $sourceClass = 'Customer';
        $sourceId = $this->id;

        $agencies = [$this->agency_id];

        //get notification recipients
        $recipients = \App\Models\User::where(function ($q) use ($agencies) {
            $q->where(function ($q) use ($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                }
            });
        })
            ->where(function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->whereName('administrator');
                })
                    ->orWhereHas('roles.perms', function ($query) {
                        $query->whereName('customers');
                    });
            })
            ->get(['id']);


        foreach ($recipients as $user) {
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

        if ($notification) {
            $notification->setPusher(BroadcastPusher::getGlobalChannel($source));
        }

        return true;
    }

    /**
     * Add wallet
     *
     * @param $value
     * @return mixed
     */
    public function addWallet($value, $save = true)
    {
        $value = (float) $value;
        $wallet = $this->wallet_balance + $value;

        if ($save) {
            $this->wallet_balance = $wallet;
            $this->save();
        }

        return $wallet;
    }

    /**
     * Sub wallet
     *
     * @param $value
     * @return mixed
     */
    public function subWallet($value, $save = true)
    {
        $value = (float) $value;
        $wallet = round(($this->wallet_balance - $value), 2);

        if ($save) {
            $this->wallet_balance = $wallet;
            $this->save();
        }

        return $wallet;
    }

    public function getBillingApiKey() {
        return ApiKey::getDefaultKey($this->agency->company_id, true);
    }

    /**
     * Limit query to seller
     *
     * @return type
     */
    public function scopeFilterSeller($query)
    {
        $user = Auth::user();
        if ($user->isSeller()) {
            return $query->where(function ($q) use ($user) {
                $q->whereNull('seller_id');
                $q->orWhere('seller_id', $user->id);
            });
        }
    }

    /**
     * Limit query to user agencies
     *
     * @return type
     */
    public function scopeFilterAgencies($query, $agencies = null)
    {

        $user = Auth::user();

        if ($user) {
            if (!$agencies) {
                $agencies = $user->agencies;
            }

            if (!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
                $query->where(function ($q) use ($agencies) {
                    $q->orWhereIn('agency_id', $agencies);
                });
            }
        }
    }

    public function scopeFilterSource($query)
    {
        return $query->where('source', config('app.source'));
    }

    /**
     * Filter is customer is active
     *
     * @return type
     */
    public function scopeIsActive($query, $isActive = true)
    {
        return $query->where('is_active', $isActive);
    }

    /**
     * Filter filnal consumer
     *
     * @return type
     */
    public function scopeIsFinalConsumer($query, $isFinalConsumer = true)
    {
        return $query->where('final_consumer', $isFinalConsumer);
    }

    /**
     * Filter filnal consumer
     *
     * @return type
     */
    public function scopeIsProspect($query, $isProspect = true)
    {
        return $query->where('is_prospect', $isProspect);
    }

    /**
     * Filter is department
     *
     * @return type
     */
    public function scopeIsDepartment($query, $isDepartment = true)
    {

        if ($isDepartment) {
            return $query->whereNotNull('customer_id');
        } else {
            return $query->whereNull('customer_id');
        }
    }

    public function scopeExcludeColumns($query, $value = array())
    {
        $columns = array_merge(['id'], $this->fillable);
        $columns = array_diff($columns, (array) $value);
        return $query->select($columns);
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency');
    }

    public function route()
    {
        return $this->belongsTo('App\Models\Route', 'route_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\CustomerType', 'type_id');
    }

    public function billing()
    {
        return $this->hasMany('App\Models\CustomerBilling', 'customer_id');
    }

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'customers_assigned_services', 'customer_id', 'service_id')
            ->withPivot('min', 'max', 'price', 'origin_zone', 'zone', 'is_adicional', 'adicional_unity');
    }

    public function futureServices()
    {
        return $this->belongsToMany('App\Models\Service', 'customers_future_services', 'customer_id', 'service_id')
            ->withPivot('min', 'max', 'price', 'zone', 'is_adicional', 'adicional_unity');
    }

    public function parent_customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

    public function recipients()
    {
        return $this->hasMany('App\Models\CustomerRecipient', 'customer_id');
    }

    public function webservices()
    {
        return $this->hasMany('App\Models\CustomerWebservice', 'customer_id');
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\User', 'seller_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\CustomerContact');
    }

    public function departments()
    {
        return $this->hasMany('App\Models\Customer', 'customer_id');
    }

    public function covenants()
    {
        return $this->hasMany('App\Models\CustomerCovenant');
    }

    public function productsBought()
    {
        return $this->hasMany('App\Models\ProductSale', 'customer_id');
    }

    public function shipments()
    {
        return $this->hasMany('App\Models\Shipment', 'customer_id');
    }

    public function last_shipment()
    {
        return $this->hasMany('App\Models\Shipment', 'customer_id')
            ->orderBy('date')
            ->limit(0, 1);
    }

    public function logistic_products()
    {
        return $this->hasMany('App\Models\Logistic\Product', "customer_id", 'id');
    }

    public function price_table()
    {
        return $this->belongsTo('App\Models\PriceTable', 'price_table_id');
    }

    public function ranking()
    {
        return $this->belongsTo('App\Models\CustomerRanking', 'customer_id');
    }

    public function payment_condition()
    {
        return $this->belongsTo('App\Models\PaymentCondition', 'payment_method', 'code');
    }

    public function paymentCondition()
    {
        return $this->belongsTo('App\Models\PaymentCondition', 'payment_method', 'code');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\BankInstitution', 'bank_code', 'code');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'customer_id');
    }

    public function unpaid_invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'customer_id')
            ->where('is_settle', 0)
            ->where('is_deleted', 0)
            ->where('is_scheduled', 0)
            ->where('due_date', '<', date('Y-m-d'))
            ->where('doc_type', 'invoice')
            ->orderBy('due_date', 'desc');
    }

    public function last_invoice()
    {
        return $this->hasOne('App\Models\Invoice', 'customer_id')
            ->where('is_settle', 0)
            ->where('doc_total_credit', '>', 0.00)
            ->where('is_deleted', 0)
            ->orderBy('doc_date', 'asc');
    }

    public function sind_invoice()
    {
        return $this->hasOne('App\Models\Invoice', 'customer_id', 'id')
            ->where('doc_type', Invoice::DOC_TYPE_SIND)
            ->where('is_deleted', 0);
    }

    public function sinc_invoice()
    {
        return $this->hasOne('App\Models\Invoice', 'customer_id', 'id')
            ->where('doc_type', Invoice::DOC_TYPE_SINC)
            ->where('is_deleted', 0);
    }

    public function payment_method() {
        return $this->belongsTo('App\Models\PaymentMethod', 'default_payment_method_id', 'id');
    }

    /**
     * @param $textType
     * @param null $defaultText
     * @return null
     */
    public function getSmsText($textType, $defaultText = null)
    {
        $textType = 'sms_text_' . $textType;
        $text = @$this->customer_sms_text[$textType];
        return $text ? $text : (Setting::get($textType) ? Setting::get($textType) : $defaultText);
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
    public function setCompanyAttribute($value)
    {
        $this->attributes['company_id'] = empty($value) ? null : $value;
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(nospace($value));
    }

    public function setZipCodetableAttribute($value)
    {
        $this->attributes['zip_code'] = trim($value);
    }

    public function setBillingZipCodetableAttribute($value)
    {
        $this->attributes['billing_zip_code'] = trim($value);
    }

    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = ip2long($value);
    }

    public function setCustomerSmsTextAttribute($value)
    {
        $this->attributes['customer_sms_text'] = empty($value) ? null : json_encode($value);
    }

    public function setEnabledServicesAttribute($value)
    {
        $this->attributes['enabled_services'] = empty($value) ? null : json_encode($value);
    }

    public function setEnabledProvidersAttribute($value)
    {
        $this->attributes['enabled_providers'] = empty($value) ? null : json_encode($value);
    }

    public function setEnabledPackagesAttribute($value)
    {
        $this->attributes['enabled_packages'] = empty($value) ? null : json_encode($value);
    }

    public function setEnabledPudoProvidersAttribute($value)
    {
        $this->attributes['enabled_pudo_providers'] = empty($value) ? null : json_encode($value);
    }

    public function setCustomBillingItemsAttribute($value)
    {
        $this->attributes['custom_billing_items'] = empty($value) ? null : json_encode($value);
    }

    public function setCustomExpensesAttribute($value)
    {
        $this->attributes['custom_expenses'] = empty($value) ? null : json_encode($value);
    }

    public function setProductPriceAttribute($value)
    {
        $this->attributes['product_price'] = empty($value) ? null : json_encode($value);
    }

    public function setShipmentsFeaturesAttribute($value)
    {
        $this->attributes['shipments_features'] = empty($value) ? null : json_encode($value);
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = empty($value) ? null : json_encode($value);
    }

    public function setCustomVolumetriesAttribute($value)
    {
        $this->attributes['custom_volumetries'] = empty($value) ? null : json_encode($value);
    }

    public function setShipmentsFormatAttribute($value)
    {
        $this->attributes['shipments_format'] = empty($value) ? null : json_encode($value);
    }

    public function setShipmentsServiceAttribute($value)
    {
        $this->attributes['shipments_service'] = empty($value) ? null : json_encode($value);
    }

    public function setShippingServicesNotifyAttribute($value)
    {
        $this->attributes['shipping_services_notify'] = empty($value) ? null : json_encode($value);
    }

    public function setShippingStatusNotifyAttribute($value)
    {
        $this->attributes['shipping_status_notify'] = empty($value) ? null : json_encode($value);
    }

    public function setShippingStatusNotifyRecipientAttribute($value)
    {
        $this->attributes['shipping_status_notify_recipient'] = empty($value) ? null : json_encode($value);
    }

    public function setSellerIdAttribute($value)
    {
        $this->attributes['seller_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setPriceTableIdAttribute($value)
    {
        $this->attributes['price_table_id'] = empty($value) ? null : $value;
    }

    public function setPricesTablesAttribute($value)
    {
        $this->attributes['prices_tables'] = empty($value) ? null : json_encode($value);
    }

    public function setRouteIdAttribute($value)
    {
        $this->attributes['route_id'] = empty($value) ? null : $value;
    }

    public function setCodeAbbrvAttribute($value)
    {
        $this->attributes['code_abbrv'] = empty($value) ? null : $value;
    }

    public function getPricesTablesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getEnabledServicesAttribute($value)
    {
        return json_decode($value);
    }

    public function getEnabledPackagesAttribute($value)
    {
        return json_decode($value);
    }

    public function getEnabledPudoProvidersAttribute($value)
    {
        return json_decode($value);
    }

    public function getCustomerSmsTextAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getSettingsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getProductPriceAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getShippingServicesNotifyAttribute($value)
    {
        if ($value) {
            $value = json_decode($value, true);
            $value = array_map('intval', $value);
        }
        return $value;
    }

    public function getShippingStatusNotifyAttribute($value)
    {
        if ($value) {
            $value = json_decode($value, true);
            $value = array_map('intval', $value);
        }
        return $value;
    }

    public function getShippingStatusNotifyRecipientAttribute($value)
    {
        if ($value) {
            try {
                $value = json_decode($value, true);
                $value = array_map('intval', $value);
            } catch (\Exception $e) {
            }
        }
        return $value;
    }

    public function setInsuranceTaxAttribute($value)
    {
        $this->attributes['insurance_tax'] = empty($value) ? null : $value;
    }

    public function setFuelTaxAttribute($value)
    {
        $this->attributes['fuel_tax'] = empty($value) ? null : $value;
    }

    public function setVatAttribute($value)
    {
        $this->attributes['vat'] = empty($value) ? null : str_replace(' ', '', trim($value));
    }

    public function setBankIbanAttribute($value)
    {
        $this->attributes['bank_iban'] = empty($value) ? null : str_replace(' ', '',$value);
    }

    public function setBankMandateDateAttribute($value)
    {
        $this->attributes['bank_mandate_date'] = empty($value) ? null : $value;
    }

    public function setBillingNameAttribute($value)
    {
        $this->attributes['billing_name'] = empty($value) ? null : $value;
    }

    public function setBillingAddressAttribute($value)
    {
        $this->attributes['billing_address'] = empty($value) ? null : $value;
    }

    public function setBillingZipCodeAttribute($value)
    {
        $this->attributes['billing_zip_code'] = empty($value) ? null : $value;
    }

    public function setBillingCityAttribute($value)
    {
        $this->attributes['billing_city'] = empty($value) ? null : $value;
    }

    public function setMonthlyPlafoundAttribute($value)
    {
        $this->attributes['monthly_plafound'] = empty($value) ? null : $value;
    }

    public function setUnpaidInvoicesLimitAttribute($value)
    {
        $this->attributes['unpaid_invoices_limit'] = empty($value) ? null : $value;
    }

    public function setUnpaidInvoicesCreditAttribute($value)
    {
        $this->attributes['unpaid_invoices_credit'] = empty($value) ? null : $value;
    }

    public function setMapPreviewAttribute($value)
    {
        $this->attributes['map_preview'] = empty($value) ? null : $value;
    }

    public function setBalanceTotalUnpaidAttribute($value)
    {
        if (str_contains($value, ',')) { //32,48
            if (str_contains($value, '.')) { //ex: 1.723,32
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '.', $value);
            }

            $this->attributes['balance_total_unpaid'] = $value;
        } else {
            $this->attributes['balance_total_unpaid'] = $value;
        }
    }

    public function setDefaultPaymentMethodIdAttribute($value) {
        $this->attributes['default_payment_method_id'] = empty($value) ? null : $value;
    }

    public function getIgnoreMassBillingAttribute($value) {
        return $value || empty($this->vat) || $this->vat == '999999990' || $this->vat == '999999999';
    }

    public function getBillingNameAttribute($value)
    {
        return empty($value) ? $this->name : $value;
    }

    public function getBillingAddressAttribute($value)
    {
        return empty($value) ? $this->address : $value;
    }

    public function getBillingZipCodeAttribute($value)
    {
        return empty($value) ? $this->zip_code : $value;
    }

    public function getBillingCityAttribute($value)
    {
        return empty($value) ? $this->city : $value;
    }

    public function getBillingCountryAttribute($value)
    {
        return empty($value) ? $this->country : $value;
    }

    public function getBillingEmailAttribute($value)
    {
        return empty($value) ? $this->contact_email : $value;
    }

    public function getRefundsEmailAttribute($value)
    {
        return empty($value) ? $this->contact_email : $value;
    }

    public function getEnabledProvidersAttribute($value)
    {
        return json_decode($value);
    }

    public function getCustomBillingItemsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCustomExpensesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCustomVolumetriesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getShipmentsFeaturesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getShipmentsFormatAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getShipmentsServiceAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getIpAttribute()
    {
        return long2ip($this->attributes['ip']);
    }

    public function getWalletBalanceAttribute($value)
    {
        return (float) $value;
    }

    public function getShowBillingAttribute($value)
    {
        $value = $this->hide_billing;

        if (empty($value)) {
            return !Setting::get('customers_hide_billing');
        } else if ($value == '2') {
            return false;
        } else {
            return true;
        }
    }

    public function getShowReferenceColumnAttribute($value)
    {

        $value = $this->show_reference;

        if (empty($value)) {
            return Setting::get('show_customers_reference');
        } else if ($value == '2') {
            return false;
        } else {
            return true;
        }
    }

    public function setIsIndependent($value) {
        $this->attributes['is_independent'] = empty($value) ? false : $value;
    }

    public function getIsShippingBlockedAttribute () {

        $limitDays     = $this->unpaid_invoices_limit;
        $limitCredit   = $this->unpaid_invoices_credit;
        $limitPlafound = $this->monthly_plafound;

        if($limitCredit || $limitDays || $limitPlafound) {

            //bloqueio por limite credito
            if($limitCredit && $this->balance_total > $limitCredit) { 
                return [
                    'reason' => 'credit',
                    'limit'  => $limitCredit
                ]; 
            } 
            
            //bloqueio por limite de dias
            elseif($limitDays) {

                $oldestInvoice = $this->getOldestUnpaidInvoice();

                if($oldestInvoice && @$oldestInvoice->due_date_days_left > $limitDays && @$oldestInvoice->due_date < date('Y-m-d')) {
                    return [
                        'reason' => 'days',
                        'limit'  => $limitDays
                    ]; 
                }
            } 
            
            //bloqueio por limite de plafound
            elseif($limitPlafound && !hasModule('invoices')) {

                if(!hasModule('invoices') && !empty($this->monthly_plafound)) { //bloqueio manual para a lognow
                    return [
                        'reason' => 'plafound',
                        'limit'  => $limitPlafound
                    ]; 
                }
            }
        }

        return false;
    }
}
