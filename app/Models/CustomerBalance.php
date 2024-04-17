<?php

namespace App\Models;

use App\Models\Billing\ApiKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Date, Setting, DB, Mail;
use Mpdf\Mpdf;

class CustomerBalance extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_balance';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_balance';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date', 'due_date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'gateway', 'doc_type', 'doc_id', 'doc_serie_id', 'doc_serie', 'total', 'sense', 'date', 'due_date', 'reference', 'divergence'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'customer_id'  => 'required',
        'doc_type'     => 'required',
        'doc_id'       => 'required',
        'doc_serie_id' => 'required'
    );

    /**
     * Sync customer balance
     *
     * @param $customerId
     * @param bool $returnTotals if set to false, dont execute query to get totals
     * @return array
     */
    public static function syncBalance($customerId, $returnTotals = true, $validateCustomer = false)
    {

        if (in_array(Setting::get('invoice_software'), ['SageX3', 'EnovoTms'])) {
            return [
                'result'    => true,
                'feedback'  => 'Conta corrente atualizada.'
            ];
        }

        if ($validateCustomer) {
            $agencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();
            $customer = Customer::findOrFail($customerId);
            if (!in_array($customer->agency_id, $agencies)) {
                return [
                    'result'    => false,
                    'feedback'  => 'Conta corrente não atualizada. Este cliente não pertence à plataforma atual.',
                ];
            }
        }

        try {

            $apiKey = ApiKey::getDefaultKey();
            $customer = Customer::findOrFail($customerId);

            if(config('app.source') == 'grupinter') {
                if($customer->agency_id == 192) { //transgeres
                    $apiKey = Setting::get('invoice_apikey_02');
                }
            } elseif(config('app.source') == 'massivepurple') {
                if($customer->agency_id == 226) { //massive purple (logistica)
                    $apiKey = Setting::get('invoice_apikey_02');
                }
            }

            $class = InvoiceGateway\Base::getNamespaceTo('Customer');
            $history = new $class($apiKey);
            $totalImported = $history->syncCustomerHistory($customerId);
        } catch (\Exception $e) {
            return [
                'result'    => false,
                'feedback'  => $e->getMessage()
            ];
        }


        $totalExpired = $totalUnpaid = $totalDocsUnpaid = 0;

        if ($returnTotals) {
            $balance = CustomerBalance::filterAgencies();

            if ($customerId) {
                $balance = $balance->where('customer_id', $customerId);
            }

            $balance = $balance->where('is_paid', 0)
                ->where('is_hidden', 0)
                ->where('canceled', 0)
                ->where('sense', 'debit')
                ->get(['total', 'due_date']);

            $totalUnpaid     = $balance->sum('total');
            $totalDocsUnpaid = $balance->count('total');
            $totalExpired    = $balance->filter(function ($item) {
                return $item->due_date < date('Y-m-d');
            })->count();
        }


        $response = [
            'result'        => true,
            'totalImported' => $totalImported,
            'totalExpired'  => $totalExpired . ' Documentos',
            'totalUnpaid'   => money($totalUnpaid, Setting::get('app_currency')),
            'countDocsUnpaid'  => $totalDocsUnpaid,
            'countDocsExpired' => $totalExpired,
            'feedback'      => 'Importados ' . $totalImported . ' novos registos.',
        ];

        return $response;
    }

    /**
     * Sync invoices payment status
     *
     * @param $customerId
     * @param bool $returnTotals if set to false, dont execute query to get totals
     * @return array
     */
    public static function updatePaymentStatus($customerId, $returnTotals = true, $validateCustomer = false)
    {

        $customer = Customer::findOrFail($customerId);

        if ($validateCustomer) {
            $agencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();
            if (!in_array($customer->agency_id, $agencies)) {
                return [
                    'result'    => false,
                    'feedback'  => 'Conta corrente não atualizada. Este cliente não pertence à plataforma atual.',
                ];
            }
        }

        $documents = CustomerBalance::whereHas('customer', function ($q) {
            $q->filterAgencies();
        });

        if ($customerId) {
            $documents = $documents->where('customer_id', $customerId);
        }

        $documents = $documents->where('is_paid', 0)
            ->where(function ($q) {
                $q->where('sense', '=', 'debit');
                $q->orWhere('doc_type', '=', 'credit-note');
            })
            ->where('is_hidden', 0)
            ->where('canceled', 0)
            ->get(['id', 'doc_id', 'doc_serie_id', 'doc_serie', 'date', 'doc_type']);


        //força todas as faturas-recibo a estarem pagas.
        //como as FR ja sao importadas como pagas, não é disparado o atualizador.
        //caso por erro alguma FR esteja como não paga, força a ser paga
        $invoiceReceipt = CustomerBalance::where('doc_type', 'invoice-receipt');
        if ($customerId) {
            $invoiceReceipt = $invoiceReceipt->where('customer_id', $customerId);
        }
        $invoiceReceipt->update(['is_paid' => 1]);

        Invoice::where('doc_type', 'invoice-receipt')
            ->where('is_settle', 0)
            ->update(['is_settle' => 1]);
        

        if ($documents->isEmpty()) {
            return [
                'result'       => true,
                'totalExpired' => '0 Documentos',
                'totalUnpaid'  => money(0, Setting::get('app_currency')),
                'countDocsUnpaid'  => 0,
                'countDocsExpired' => 0,
                'feedback'     => 'Nada para atualizar. Todos os documentos estão regularizados.',
            ];
        }

        //obtem chave API correta mediante o cliente selecionado
        //casos em que a empresa tem +1 licença keyinvoice
        $apiKey = ApiKey::getDefaultKey();
        if(config('app.source') == 'grupinter') {
            if($customer->agency_id == 192) { //transgeres
                $apiKey = Setting::get('invoice_apikey_02');
            }
        } elseif(config('app.source') == 'massivepurple') {
            if($customer->agency_id == 226) { //massive purple (logistica)
                $apiKey = Setting::get('invoice_apikey_02');
            }
        }
    
        //check if is paid
        $payedIds = $unpayedIds = $unpayedInvoicesData = [];
        foreach ($documents as $document) {

   /*          if($document->doc_id == 18) {
                dd($document->toArray());
            }
                */
            $class = InvoiceGateway\Base::getNamespaceTo('Document');
            $history = new $class($apiKey);

            $isPaid = 0;
            if ($document->doc_serie == 'SIND') {
                $isPaid = 0; //força a que o Saldo Inicial seja sempre não liquidado
            } else {
                //dd($documents->toArray());
                if ($document->doc_type != 'credit-note') { //não atualiza notas de crédito
                    $isPaid = $history->checkIfPaid($document->doc_id, $document->doc_serie_id);
                }
            }

            if ($isPaid) {
                $payedIds[] = $document->id;

                //Update Invoices
                try {

                    /*if($document->doc_id == '92') {
                        dd($isPaid);
                    }*/

                    Invoice::filterSource()
                        ->where('doc_type', $document->doc_type)
                        ->where('doc_id', $document->doc_id)
                        ->where('doc_date', $document->date->format('Y-m-d'))
                        ->update(['is_settle' => $isPaid]);
                } catch (\Exception $e) { }
            } else {
                $unpayedIds[] = $document->id;
                $unpayedInvoicesData[] = [
                    'doc_id'       => $document->doc_id,
                    'doc_serie_id' => $document->doc_serie_id,
                    'doc_serie'    => $document->doc_serie
                ];
            }


            unset($document);
        }

   
        //Update status of payment - documents Paid
        if ($payedIds) {
            $result = CustomerBalance::whereIn('id', $payedIds)->update(['is_paid' => true]);
        }


        //Update status of payment - documents Unpaid
        if ($unpayedIds) {
            $result = CustomerBalance::whereIn('id', $unpayedIds)->update(['is_paid' => false]);

            //marca todas as faturas como pagas excepto as notas de crédito (o metodo check if settle nao devolve se a nota esta paga ou nao)
            /*$update = Invoice::where('customer_id', $customerId);

            if($customer->vat != '999999990') { //23/03/2022 - se o nif do cliente é diferente de consumidor final, exclui faturas do cliente que sejam emitidas em consumidor final
                $update->where('vat', '<>', '999999990');
            }

            $update->where('doc_type', '<>', 'credit-note')
                ->where('is_deleted', 0) //não atualiza se estiver apagada
                ->update(['is_settle' => 1]);*/

            foreach ($unpayedInvoicesData as $data) { //ignora

                $invoice = Invoice::where('customer_id', $customerId)
                    ->where('doc_id', $data['doc_id'])
                    ->where(function ($q) use ($data) {
                        $q->where('doc_series_id', $data['doc_serie_id']);
                        $q->orWhereNull('doc_series_id');
                    })
                    ->update([
                        'is_settle' => 0,
                        'doc_series_id' => $data['doc_serie_id'],
                        'doc_series' => $data['doc_serie']
                    ]);


                //dd($invoice->toArray());

                /* ->update([
                        'is_settle'     => 0,
                        'doc_series_id' => $data['doc_serie_id'],
                        'doc_series'    => $data['doc_serie']
                    ]);*/

                // dd($data);
            }
        }

        //Update totals
        $totalExpired = $totalUnpaid = $totalDocumentsUnpaid = 0;
        if ($returnTotals) {

            $balance = CustomerBalance::filterAgencies();

            if ($customerId) {
                $balance = $balance->where('customer_id', $customerId);
            }

            $balance = $balance->where('is_hidden', 0)
                ->where('canceled', 0)
                ->get(['total', 'due_date', 'sense', 'is_paid', 'balance']);

            $balanceDebit = $balance->filter(function ($item) {
                return $item->sense == 'debit';
            });

            $balanceCredit = $balance->filter(function ($item) {
                return $item->sense == 'credit';
            });

            $totalUnpaid = CustomerBalance::calcBalance($balance);
            $totalDocumentsUnpaid = $balanceDebit->filter(function ($item) {
                return $item->is_paid == 0;
            })->count();
            $totalExpired = $balanceDebit->filter(function ($item) {
                return $item->is_paid == 0 && $item->due_date < date('Y-m-d');
            })->count();
        }

        if ($result) {
            return [
                'result'           => true,
                'totalExpired'     => $totalExpired . ' Documentos',
                'totalUnpaid'      => money($totalUnpaid, Setting::get('app_currency')),
                'countDocsUnpaid'  => $totalDocumentsUnpaid,
                'countDocsExpired' => $totalExpired,
                'valueUnpaid'      => $totalUnpaid,
                'feedback'         => 'Sincronizado com sucesso.',
                'customer_id'      => $customerId
            ];
        }

        return [
            'result'            => true,
            'totalExpired'      => $totalExpired . ' Documentos',
            'totalUnpaid'       => money($totalUnpaid, Setting::get('app_currency')),
            'countDocsUnpaid'   => $totalDocumentsUnpaid,
            'countDocsExpired'  => $totalExpired,
            'valueUnpaid'       => $totalUnpaid,
            'feedback'          => 'Não há alterações ao estado dos pagamentos.',
            'customer_id'       => $customerId
        ];
    }

    /**
     * Sync customer balance and invoices payment status simultaneously
     *
     * @param $customerId
     * @param bool $returnTotals if set to false, dont execute query to get totals
     * @return array
     */
    public static function syncBalanceAll($customerId)
    {

   
        if (in_array(Setting::get('invoice_software'), ['SageX3', 'EnovoTms'])) {
            return [
                'result'    => true,
                'feedback'  => 'Conta corrente atualizada.'
            ];
        }

        $agencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

        $customer = Customer::findOrFail($customerId);
        if (!in_array($customer->agency_id, $agencies)) {
            return [
                'result'           => false,
                'totalImported'    => 0,
                'totalExpired'     => 0,
                'totalUnpaid'      => 0,
                'valueUnpaid'      => 0,
                'countDocsUnpaid'  => 0,
                'countDocsExpired' => 0,
                'feedback'         => 'Conta corrente não atualizada. Este cliente não pertence à plataforma atual.',
            ];
        }

        $result = self::syncBalance($customerId, false);

        $totalImported = @$result['totalImported'];


        if (@$result['result']) {
            $result = self::updatePaymentStatus($customerId);
        }

        $totalExpired     = @$result['totalExpired'];
        $totalUnpaid      = @$result['totalUnpaid'];
        $countDocsUnpaid  = @$result['countDocsUnpaid'];
        $countDocsExpired = @$result['countDocsExpired'];
        $divergence       = null;

        $updateFields = [
            'balance_count_unpaid'  => $countDocsUnpaid,
            'balance_count_expired' => $countDocsExpired,
            'balance_total_unpaid'  => str_replace(',', '.', str_replace(Setting::get('app_currency'), '', str_replace('.', '', $totalUnpaid))),
            'balance_last_update'   => date('Y-m-d H:i:s'),
            'balance_divergence'    => $divergence
        ];

        //dd($updateFields);
        $customer->update($updateFields);

        $response = [
            'result'           => true,
            'totalImported'    => $totalImported,
            'totalExpired'     => $totalExpired,
            'totalUnpaid'      => $totalUnpaid,
            'valueUnpaid'      => $updateFields['balance_total_unpaid'],
            'countDocsUnpaid'  => $countDocsUnpaid,
            'countDocsExpired' => $countDocsExpired,
            'feedback'         => 'Conta Corrente atualizada.' . ($totalImported ? $totalImported . ' novos registos.' : ''),
        ];

        return $response;
    }

    /**
     *
     *
     * @param $balanceArr
     */
    public static function calcBalance($balanceArr)
    {

        $total = 0;
        foreach ($balanceArr as $item) {

            if ($item->sense == 'debit') {
                $total += $item->total;
            } else {
                $total -= $item->total;
            }
        }

        return $total;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
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

    public function scopeWithInvoice($query)
    {

        return $query->leftJoin('invoices', function ($join) {
            $join->on('customers_balance.customer_id', '=', 'invoices.customer_id')
                ->on('customers_balance.doc_id', '=', 'invoices.doc_id')
                ->on('customers_balance.doc_serie_id', '=', 'invoices.doc_series_id')
                ->on('customers_balance.doc_type', '=', 'invoices.doc_type');
        });
    }

    public function scopeGetPendingDocuments($query, $customerId)
    {

        return $query->where('customer_id', $customerId)
            ->whereHas('customer', function ($q) {
                //$q->filterAgencies();
                $q->filterSource();
                $q->filterSeller();
            })
            ->whereNotIn('doc_serie', ['SIND', 'SINC', 'RC'])
            ->where('is_paid', 0)
            ->where('is_hidden', 0)
            ->where('canceled', 0)
            ->where('sense', 'debit');
    }

    /**
     * Get the oldest unpaid invoice
     * @param $query
     */
    public function scopeGetOldestUnpaidInvoice($query)
    {

        $oldest = $query->where('is_paid', 0)
            ->where('sense', 'debit')
            ->whereNotIn('doc_serie', ['SIND', 'SINC', 'RC'])
            ->where('is_hidden', 0)
            ->orderBy('date', 'asc')
            ->first();

        if ($oldest) {
            $today = Date::today();
            $date  = new Date($oldest->due_date);
            $oldest->days_late = $date->diffInDays($today);
            return $oldest;
        }

        //block query when all invoices are paid
        return $this->where('customer_id', '999999999')->first();
    }

    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterAgencies($query)
    {

        $user = Auth::user();

        $agencies = [];
        if ($user) {
            $agencies = $user->agencies;
        }

        if (($user && !$user->hasRole([config('permissions.role.admin')])) || !empty($agencies)) {

            return $query->whereHas('customer', function ($q) use ($agencies) {
                $q->whereIn('agency_id', $agencies);
            });
        }
    }

    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterSource($query)
    {
        return $query->whereHas('customer', function ($q) {
            $q->where('source', config('app.source'));
        });
    }
}
