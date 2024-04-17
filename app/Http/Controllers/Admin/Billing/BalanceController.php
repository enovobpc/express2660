<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Invoice;
use App\Models\PaymentCondition;
use App\Models\PriceTable;
use App\Models\ProviderCategory;
use App\Models\PurchaseInvoiceType;
use App\Models\Route;
use App\Models\User;
use App\Models\InvoiceGateway\Base;
use App\Models\Provider;
use App\Models\PurchaseInvoice;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\CustomerBalance;
use App\Models\PaymentMethod;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Html, Setting, DB, Auth, View, Excel;

class BalanceController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customers_balance';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers_balance']);
        validateModule('customers_balance');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $totalUnpaid = Customer::filterSource()
            ->filterAgencies()
            ->filterSeller()
            ->sum('balance_total');

        $totalExpired = Customer::filterSource()
            ->filterAgencies()
            ->filterSeller()
            ->sum('balance_expired_count');

        $purchaseInvoices = PurchaseInvoice::filterSource()
            ->where('is_deleted', 0)
            ->whereNull('is_scheduled')
            ->where('total_unpaid', '>', '0.00')
            ->get(['total_unpaid', 'due_date']);

        $providersTotalUnpaid  = $purchaseInvoices->sum('total_unpaid');
        $providersTotalExpired = $purchaseInvoices->filter(function ($item) {
            return $item->due_date < date('Y-m-d');
        })->count();

        $categories = ProviderCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $agencies = Auth::user()->listsAgencies();

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            //->isSeller(true)
            ->isOperator(false)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $recipientCounties = [];
        $recipientDistrict = $request->get('fltr_recipient_district');
        if ($request->has('fltr_recipient_district')) {
            $recipientCounties = trans('districts_codes.counties.pt.' . $recipientDistrict);
        }

        $data = compact(
            'totalUnpaid',
            'totalExpired',
            'agencies',
            'sellers',
            'providersTotalUnpaid',
            'providersTotalExpired',
            'categories',
            'recipientCounties',
            'types',
            'routes',
            'pricesTables',
            'paymentConditions'
        );

        return $this->setContent('admin.billing.balance.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator(false)
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $series = Invoice::groupBy('doc_series')
            ->where('doc_series_id', '<>', '')
            ->pluck('doc_series', 'doc_series_id')
            ->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $years  = yearsArr(2016, date('Y'), true);
        $months = array_reverse(trans('datetime.list-month'), true);


        if ($request->get('source') == 'providers') {

            $provider = Provider::filterSource()->findOrFail($id);

            $balance = PurchaseInvoice::where('provider_id', $provider->id)
                ->where('is_settle', 0)
                ->where('is_draft', 0)
                ->where('is_deleted', 0)
                ->get(['sense', 'total_unpaid', 'due_date']);

            $totalCredit   = $balance->filter(function ($item) {
                return $item->sense == 'credit';
            })->sum('total_unpaid');
            $totalDebit    = $balance->filter(function ($item) {
                return $item->sense == 'debit';
            })->sum('total_unpaid');
            $totalExpired  = $balance->filter(function ($item) {
                return $item->due_date < date('Y-m-d');
            })->count();
            $totalUnpaid   = ($totalDebit * -1) - $totalCredit;

            $data = compact(
                'provider',
                'purchasesTypes',
                'totalExpired',
                'totalUnpaid',
                'operators',
                'series',
                'totalCredit',
                'totalDebit',
                'paymentConditions'
            );

            return view('admin.billing.balance.show_provider', $data)->render();
        } else {
            $customer = Customer::filterSource()->findOrFail($id);

            $data = compact(
                'customer',
                'totalExpired',
                'totalUnpaid',
                'operators',
                'series',
                'paymentConditions',
                'paymentMethods',
                'years',
                'months'
            );

            return view('admin.billing.balance.show', $data)->render();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function datatable(Request $request)
    {

        if ($request->get('source') == 'providers') {
            return $this->datatableProviders($request);
        }

     
        $data = Customer::filterSource()
            ->filterAgencies()
            ->filterSeller()
            ->isDepartment(false)
             ->select([
                'customers.*',
                DB::raw('(select max(date) from shipments where shipments.customer_id = customers.id and deleted_at is null limit 0,1) as last_shipment'),
                DB::raw('(select count(date) from shipments where shipments.customer_id = customers.id and deleted_at is null) as total_shipments'),
            ]); 

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            $data = $data->where('agency_id', $value);
        }

        //filter seller
        $value = $request->seller;
        if ($request->has('seller')) {
            $data = $data->where('seller_id', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->where('payment_method', $value);
        }

        //filter divergence
        $value = $request->divergence;
        if ($request->has('divergence')) {
            if ($value == 1) {
                $data = $data->where('balance_divergence', '>', 0.00);
            } else {
                $data = $data->where(function ($q) {
                    $q->where('balance_divergence', 0.00);
                    $q->orWhereNull('balance_divergence');
                });
            }
        }

        if ($request->has('unpaid')) {
            if ($value == '1') {
                $data = $data->where(function ($q) {
                    $q->where('balance_unpaid_total', '=', '0.00');
                    $q->orWhere('balance_unpaid_total', '');
                    $q->orWhereNull('balance_unpaid_total');
                });
            } else {
                $data = $data->where('balance_unpaid_total', '>', '0.00');
            }
        }

        //filter is expired
        $value = $request->expired;
        if ($request->has('expired')) {
            if ($value == '1') {
                $data = $data->where(function ($q) {
                    $q->where('balance_expired_count', '>', '0');
                    $q->where('balance_expired_count', '<>', '0');
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->where('balance_expired_count', '=', '0');
                    $q->orWhere('balance_expired_count', '');
                    $q->orWhereNull('balance_expired_count');
                });
            }
        }

        //filter active
        $value = $request->active;
        if ($request->has('active')) {
            $data = $data->where('is_active', $value);
        }

        //filter validated
        $value = $request->validated;
        if ($request->has('validated')) {
            $data = $data->where('is_validated', $value);
        }

        //filter code
        $value = $request->code;
        if ($request->has('code')) {
            $data = $data->where('code', $value);
        }

        //filter type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->where('type_id', $value);
        }

        //filter country
        $value = $request->country;
        if ($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            $data = $data->where('agency_id', $value);
        }

        //filter seller
        $value = $request->seller;
        if ($request->has('seller')) {
            $data = $data->where('seller_id', $value);
        }

        //filter payment method
        $value = $request->payment_condition;
        if ($request->has('payment_condition')) {
            $data = $data->where('payment_method', $value);
        }

        //filter particular
        $value = $request->particular;
        if ($request->has('particular')) {
            if ($value == '-1') {
                $data = $data->where('is_particular', 0);
            } else {
                $data = $data->where('is_particular', 1);
            }
        }

        //filter prices
        $value = $request->prices;
        if ($request->has('prices')) {
            if ($value == '-1') {
                $data = $data->where('has_prices', 0);
            } elseif ($value == '0') {
                $data = $data->where('has_prices', 1)->whereNull('price_table_id');
            } else {
                $data = $data->where('price_table_id', $value);
            }
        }

        //filter route
        $value = $request->route;
        if ($request->has('route')) {
            if ($value == '0') {
                $data = $data->whereNull('route_id');
            } else {
                $data = $data->where('route_id', $value);
            }
        }

        //filter billing country
        $value = $request->billing_country;
        if ($request->has('billing_country')) {
            $data = $data->where('billing_country', $value);
        }

        //filter last_shipment
        $value = $request->last_shipment;
        if ($request->has('last_shipment')) {
            $days = Setting::get('alert_max_days_without_shipments');
            $limitDate = Date::today()->subDays($days)->format('Y-m-d');

            if ($value == 1) { // <= N days
                $data = $data->having(DB::raw('last_shipment'), '>', $limitDate);
            } elseif ($value == 2) { // > N days
                $data = $data->having(DB::raw('last_shipment'), '<=', $limitDate);
            } elseif ($value == 3) { //empty shipments
                $data = $data->whereDoesntHave('shipments');
            }
        }

        //filter recipient district
        $district = $request->get('district');
        $county   = $request->get('county');
        if ($request->has('district') || $request->has('county')) {

            $zipCodes = ZipCode::remember(config('cache.query_ttl'))
                ->where('district_code', $district)
                ->where('country', Setting::get('app_country'));

            if ($county) {
                $zipCodes = $zipCodes->where('county_code', $county);
            }

            $zipCodes = $zipCodes->groupBy('zip_code')
                ->pluck('zip_code')
                ->toArray();

            $data = $data->where(function ($q) use ($zipCodes) {
                $q->where('country', Setting::get('app_country'));
                $q->whereIn(DB::raw('SUBSTRING(`zip_code`, 1, 4)'), $zipCodes);
            });
        }

        $agencies = Agency::get(['code', 'color', 'name', 'id']);
        $agencies = $agencies->groupBy('id')->toArray();

        $currency = Setting::get('app_currency');

        return Datatables::of($data)
            ->edit_column('code', function ($row) use ($agencies) {
                return view('admin.billing.balance.datatables.code', compact('row', 'agencies'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.billing.balance.datatables.name', compact('row'))->render();
            })
            ->edit_column('payment_method', function ($row) {
                return @$row->payment_condition->name;
            })
            ->edit_column('balance_divergence', function ($row) {
                return view('admin.billing.balance.datatables.divergence', compact('row'))->render();
            })
            ->edit_column('balance_expired_count', function ($row) {
                return view('admin.billing.balance.datatables.count_expired', compact('row', 'balance'))->render();
            })
            ->edit_column('balance_total', function ($row) use ($currency) {
                return view('admin.billing.balance.datatables.total_unpaid', compact('row', 'balance', 'currency'))->render();
            })
            ->edit_column('balance_last_update', function ($row) {
                return view('admin.billing.balance.datatables.last_update', compact('row', 'balance'))->render();
            })
            ->edit_column('last_shipment', function ($row) {
                return view('admin.billing.balance.datatables.last_shipment', compact('row'))->render();
            })
            ->edit_column('balance_total_credit', function ($row) use ($currency) {
                return view('admin.billing.balance.datatables.credit', compact('row', 'currency'))->render();
            })
            ->edit_column('balance_total_debit', function ($row) use ($currency) {
                return view('admin.billing.balance.datatables.debit', compact('row', 'currency'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.balance.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function datatableProviders(Request $request)
    {

        $data = Provider::filterSource()
            ->select([
                'providers.*',
                DB::raw('(select count(total) from purchase_invoices where is_settle=0 and is_draft=0 and is_deleted=0 and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as count_unpaid'),
                DB::raw('(select count(total) from purchase_invoices where is_settle=0 and is_draft=0 and is_deleted=0 and due_date<"' . date('Y-m-d') . '" and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as balance_count_expired'),
                DB::raw('(select sum(total_unpaid) from purchase_invoices where sense="debit" and is_settle=0 and is_draft=0 and is_deleted=0 and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as debit'),
                DB::raw('(select sum(total_unpaid) from purchase_invoices where sense="credit" and is_settle=0 and is_draft=0 and is_deleted=0 and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as credit'),
                DB::raw('(select sum(total_unpaid) from purchase_invoices where is_settle=0 and is_draft=0  and is_deleted=0 and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as balance_total_unpaid'),
                DB::raw('(select max(doc_date) from purchase_invoices where purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as last_invoice'),
            ]);

        //filter sense
        $value = $request->sense;
        if ($request->has('sense')) {
            $data = $data->where('sense', $value);
        }

        //filter unpaid
        $value = $request->unpaid;
        if ($request->has('unpaid')) {
            if ($value == '1') {
                $data = $data->where(function ($q) {
                    $q->where('balance_total_unpaid', '=', '0.00');
                    $q->orWhere('balance_total_unpaid', '');
                    $q->orWhereNull('balance_total_unpaid');
                });
            } else {
                $data = $data->where('balance_total_unpaid', '>', '0.00');
            }
        }

        //filter is expired
        $value = $request->expired;
        if ($request->has('expired')) {
            if ($value == '1') {
                $data = $data->where(function ($q) {
                    $q->whereRaw('(select count(total) from purchase_invoices where is_settle=0 and is_draft=0 and is_deleted=0 and due_date<"' . date('Y-m-d') . '" and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) > 0');
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->whereRaw('(select count(total) from purchase_invoices where is_settle=0 and is_draft=0 and is_deleted=0 and due_date<"' . date('Y-m-d') . '" and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) = 0');
                });
            }
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $dateUnity = 'doc_date';
            if ($request->has('date_unity')) {
                if ($request->date_unity == 'due') {
                    $dateUnity = 'due_date';
                } elseif ($request->date_unity == 'pay') {
                    $dateUnity = 'payment_date';
                }
            }

            $data = $data->whereBetween($dateUnity, [$dtMin, $dtMax]);
        }

        //filter sense
        $value = $request->sense;
        if ($request->has('sense')) {
            $data = $data->where('sense', $value);
        }

        //filter paid
        $value = $request->paid;
        if ($request->has('paid')) {
            if ($value) {
                $data = $data->where('is_settle', 1);
            } else {
                $data = $data->where('is_settle', 0);
            }
        }

        //filter ignore invoice
        $value = $request->ignore_stats;
        if ($request->has('ignore_stats')) {
            $data = $data->where('ignore_stats', $value);
        }

        //filter type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->whereIn('type_id', $value);
        }

        //filter doc id
        $value = $request->doc_id;
        if ($request->has('doc_id')) {
            $data = $data->where('reference', $value);
        }

        //filter doc type
        $value = $request->doc_type;
        if ($request->has('doc_type')) {
            $data = $data->whereIn('doc_type', $value);
        }

        //filter payment method
        $value = $request->payment_condition;
        if ($request->has('payment_condition')) {
            $value = explode(',', $value);
            $data = $data->whereIn('payment_method', $value);
        }

        //filter deleted
        $value = $request->deleted;
        if ($request->has('deleted') && empty($value)) {
            $data = $data->where('is_deleted', $value);
        }

        return Datatables::of($data)
            ->edit_column('code', function ($row) {
                return view('admin.billing.balance.datatables.code_providers', compact('row'))->render();
            })
            ->edit_column('company', function ($row) {
                return view('admin.billing.balance.datatables.company', compact('row'))->render();
            })
            ->edit_column('category_id', function ($row) {
                return view('admin.billing.balance.datatables.category', compact('row'))->render();
            })
            ->add_column('payment_method', function ($row) {
                return view('admin.billing.balance.datatables.payment_condition', compact('row'))->render();
            })
            ->edit_column('debit', function ($row) {
                if ($row->debit == 0.00) {
                    return '<span class="text-muted">0,00' . Setting::get('app_currency') . '</span>';
                } else {
                    return money($row->debit * -1, Setting::get('app_currency'));
                }
            })
            ->edit_column('credit', function ($row) {
                if ($row->credit == 0.00) {
                    return '<span class="text-muted">0,00' . Setting::get('app_currency') . '</span>';
                } else {
                    return money($row->credit, Setting::get('app_currency'));
                }
            })
            ->edit_column('balance_count_expired', function ($row) {
                return view('admin.billing.balance.datatables.count_expired', compact('row', 'balance'))->render();
            })
            ->edit_column('balance_total_unpaid', function ($row) {
                return view('admin.billing.balance.datatables.total_unpaid_provider', compact('row', 'balance'))->render();
            })
            ->edit_column('last_invoice', function ($row) {
                return view('admin.billing.balance.datatables.last_invoice', compact('row', 'balance'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.balance.datatables.actions_providers', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading customer balance table data
     *
     * @return Datatables
     */
    public function datatableBalance(Request $request, $customerId)
    {

        if ($request->get('source') == 'providers') {
            return $this->datatableBalanceProviders($request, $customerId);
        }

        $data = CustomerBalance::whereHas('customer', function ($q) {
                $q->filterAgencies();
            })
            ->withInvoice()
            ->where('customers_balance.customer_id', $customerId)
            ->select([
                'customers_balance.*',
                'invoices.created_by',
                'invoices.payment_method',
                DB::raw('(select doc_total_pending from invoices where invoices.customer_id=customers_balance.customer_id and invoices.doc_type=customers_balance.doc_type and invoices.doc_id=customers_balance.doc_id and invoices.doc_series_id=customers_balance.doc_serie_id and (doc_total_pending is not null or doc_total_pending <> "") limit 0,1) as pending')
            ]);

        //filter hide payment notes or receipts
        $value = $request->hide_payments;
        if ($request->has('hide_payments')) {
            if ($value) {
                //$data = $data->whereNotIn('customers_balance.doc_type', ['payment-note','receipt','regularization']);
                $data = $data->where(function ($q) {
                    $q->whereNotIn('customers_balance.doc_type', ['payment-note', 'receipt', 'regularization']);
                    $q->whereRaw('not(customers_balance.doc_type = "invoice-receipt" and sense = "credit")'); //adicionado em 2023/03/23 e substituido o abaixo
                    //$q->orWhereRaw('(customers_balance.doc_type = "invoice-receipt" and sense = "debit")'); //oculta os registos negativos de pagamento das FR (linha de registo do recibo)
                });
            }
        }

        //filter sense
        $value = $request->sense;
        if ($request->has('sense')) {
            if ($value == 'hidden') {
                $data = $data->where('customers_balance.is_hidden', 1);
            } else {
                $data = $data->where('sense', $value)
                    ->where('customers_balance.is_hidden', 0);
            }
        }

        //filter is paid
        $value = $request->paid;
        if ($request->has('paid')) {
            if ($value == '2') {
                $data = $data->withTrashed()
                    ->whereNotNull('invoices.deleted_at');
            } elseif ($value == '3') {
                $data = $data->where('invoices.doc_total_pending', '>', '0.00')
                    ->whereRaw('invoices.doc_total_pending <> doc_total');
            } else {
                if ($value == 1) {
                    $data = $data->where('is_paid', $value);
                } else {
                    $data = $data->where('is_paid', $value)
                        ->whereNotIn('customers_balance.doc_type', ['receipt', 'regularization']);
                }
            }
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }


        //filter expired
        $value = $request->expired;
        if ($request->has('expired')) {
            $data = $data->where('customers_balance.due_date', '<', $value);
        }

        //filter serie
        $value = $request->serie;
        if ($request->has('serie')) {
            $data = $data->whereIn('doc_serie_id', $value);
        }

        //filter year
        $value = $request->year;
        if ($request->has('year')) {
            $data = $data->whereRaw('YEAR(date) = ' . $value);
        }

        //filter month
        $value = $request->month;
        if ($request->has('month')) {
            $data = $data->whereRaw('MONTH(date) = ' . $value);
        }

        //filter doc id
        $value = $request->doc_id;
        if ($request->has('doc_id')) {
            $data = $data->where('customers_balance.doc_id', $value);
        }

        //filter doc type
        $value = $request->doc_type;
        if ($request->has('doc_type')) {
            $data = $data->whereIn('customers_balance.doc_type', $value);
        } else {
            $data = $data->where('customers_balance.doc_type', '<>', 'nodoc');
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->whereIn('invoices.created_by', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->whereIn('invoices.payment_method', $value);
        }

        //filter deleted
        $value = $request->deleted;
        if ($request->has('deleted') && empty($value)) {
            $data = $data->where('canceled', $value);
        }


        $today = Carbon::today();

        return Datatables::of($data)
            ->edit_column('date', function ($row) {
                $date = new Date($row->date);
                return $date->format('d F Y');
            })
            ->add_column('debit', function ($row) {
                return view('admin.billing.balance.datatables.documents.debit', compact('row'))->render();
            })
            ->add_column('credit', function ($row) {
                return view('admin.billing.balance.datatables.documents.credit', compact('row'))->render();
            })
            ->edit_column('doc_serie', function ($row) {
                return view('admin.billing.balance.datatables.documents.serie', compact('row'))->render();
            })
            ->edit_column('doc_type', function ($row) {
                return view('admin.billing.balance.datatables.documents.type', compact('row'))->render();
            })
            ->edit_column('is_paid', function ($row) {
                return view('admin.billing.balance.datatables.documents.paid', compact('row'))->render();
            })
            ->edit_column('due_date', function ($row) use ($today) {
                return view('admin.billing.balance.datatables.documents.due_date', compact('row', 'today'))->render();
            })
            ->edit_column('total', function ($row) {
                return view('admin.billing.balance.datatables.documents.total', compact('row', 'today'))->render();
            })
            ->add_column('pending', function ($row) {
                return view('admin.billing.balance.datatables.documents.pending', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.balance.datatables.documents.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading customer balance table data
     *
     * @return Datatables
     */
    public function datatableBalanceProviders(Request $request, $providerId)
    {

        $scheduled = $request->get('scheduled', false);

        $data = PurchaseInvoice::filterSource()
            ->where('provider_id', $providerId)
            ->with('payment_notes')
            ->with('provider', 'user')
            ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $dateUnity = 'doc_date';
            if ($request->has('date_unity')) {
                if ($request->date_unity == 'due') {
                    $dateUnity = 'due_date';
                } elseif ($request->date_unity == 'pay') {
                    $dateUnity = 'payment_date';
                }
            }

            $data = $data->whereBetween($dateUnity, [$dtMin, $dtMax]);
        }

        //filter hide payment notes or receipts
        $value = $request->hide_payments;
        if ($request->has('hide_payments')) {
            if ($value) {
                $data = $data->whereNotIn('doc_type', ['payment-note', 'receipt', 'regularization']);
            }
        }

        //filter sense
        $value = $request->sense;
        if ($request->has('sense')) {
            $data = $data->where('sense', $value);
        }

        //filter paid
        $value = $request->paid;
        if ($request->has('paid')) {
            if ($value == 0) {
                $data = $data->where('is_settle', 0);
            } elseif ($value == 1) {
                $data = $data->where('is_settle', 1);
            } elseif ($value == 3) {
                $data = $data->where(function ($q) {
                    $q->where('is_settle', 0);
                    $q->where('total_unpaid', '>', '0.00');
                    $q->whereRaw('total_unpaid < total');
                });
            }
        }

        //filter expired
        $value = $request->expired;
        if ($request->has('expired')) {
            if ($value) {
                $data = $data->where('due_date', '<', date('Y-m-d'));
            } else {
                $data = $data->where('due_date', '>=', date('Y-m-d'));
            }
        }

        //filter ignore invoice
        $value = $request->ignore_stats;
        if ($request->has('ignore_stats')) {
            $data = $data->where('ignore_stats', $value);
        }

        //filter assigned targets
        $value = $request->assigned_targets;
        if ($request->has('assigned_targets')) {
            if ($value > 0) {
                $data = $data->whereNotNull('ignore_stats');
            } else {
                $data = $data->whereNull('ignore_stats');
            }
        }

        //filter target
        $value = $request->target;
        if ($request->has('target')) {
            $data = $data->where('target', $value);
        }

        //filter target id
        $value = $request->target_id;
        if ($request->has('target_id')) {
            $data = $data->where('target_id', $value);
        }

        //filter type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->whereIn('type_id', $value);
        }

        //filter doc id
        $value = $request->doc_id;
        if ($request->has('doc_id')) {
            $data = $data->where('reference', $value);
        }

        //filter doc type
        $value = $request->doc_type;
        if ($request->has('doc_type')) {
            $data = $data->whereIn('doc_type', $value);
        }

        //filter provider
        $value = $request->provider;
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter operator
        $value = $request->created_by;
        if ($request->has('created_by')) {
            $data = $data->where('created_by', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->whereIn('payment_method', $value);
        }

        if ($scheduled) {
            $data = $data->whereNotNull('is_scheduled');
        } else {
            $data = $data->whereNull('is_scheduled');
        }

        //filter deleted
        $value = $request->deleted;
        if ($request->has('deleted') && empty($value)) {
            $data = $data->where('is_deleted', $value);
        }

        return Datatables::of($data)
            ->edit_column('code', function ($row) {
                return view('admin.invoices.purchases.datatables.code', compact('row'))->render();
            })
            ->edit_column('doc_date', function ($row) {
                return view('admin.invoices.purchases.datatables.doc_date', compact('row'))->render();
            })
            ->edit_column('doc_type', function ($row) {
                return view('admin.invoices.purchases.datatables.doc_type', compact('row'))->render();
            })
            ->edit_column('due_date', function ($row) {
                return view('admin.invoices.purchases.datatables.due_date', compact('row'))->render();
            })
            ->edit_column('payment_date', function ($row) {
                return view('admin.invoices.purchases.datatables.status', compact('row'))->render();
            })
            ->edit_column('payment_method', function ($row) {
                return view('admin.invoices.purchases.datatables.payment_date', compact('row'))->render();
            })
            ->edit_column('provider_id', function ($row) {
                return view('admin.invoices.purchases.datatables.provider', compact('row'))->render();
            })
            ->edit_column('description', function ($row) {
                return view('admin.invoices.purchases.datatables.description', compact('row'))->render();
            })
            ->edit_column('total', function ($row) {
                return view('admin.invoices.purchases.datatables.total', compact('row'))->render();
            })
            ->add_column('credit', function ($row) {
                return view('admin.invoices.purchases.datatables.credit', compact('row'))->render();
            })
            ->add_column('debit', function ($row) {
                return view('admin.invoices.purchases.datatables.debit', compact('row'))->render();
            })
            ->edit_column('vat_total', function ($row) {
                return view('admin.invoices.purchases.datatables.vat_total', compact('row'))->render();
            })
            ->edit_column('total_unpaid', function ($row) {
                return view('admin.invoices.purchases.datatables.unpaid', compact('row'))->render();
            })
            ->edit_column('assigned_targets', function ($row) {
                return view('admin.invoices.purchases.datatables.assigned_targets', compact('row'))->render();
            })
            ->edit_column('ignore_stats', function ($row) {
                return view('admin.invoices.purchases.datatables.ignore_stats', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('is_scheduled', function ($row) {
                return view('admin.invoices.purchases.datatables.scheduled', compact('row'))->render();
            })
            ->add_column('print_button', function ($row) {
                return view('admin.invoices.purchases.datatables.print_button', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.invoices.purchases.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Update payment status of all documents for a customer
     * @param $customerId
     */
    public function updatePaymentStatus($customerId = null, $returnTotals = true)
    {
        return CustomerBalance::updatePaymentStatus($customerId, $returnTotals, true);
    }

    /**
     * Sync customer balance
     * @param $customerId
     */
    public function syncBalance($customerId = null, $returnTotals = true)
    {
        return CustomerBalance::syncBalance($customerId, $returnTotals, true);
    }

    /**
     * Sync customer balance and payment status
     * @param $customerId
     */
    public function syncBalanceAll($customerId = null)
    {
        return CustomerBalance::syncBalanceAll($customerId);
    }

    /**
     * Sync customer balance
     * @param $customerId
     */
    public function massSyncBalance($customerId = null, $returnTotals = true)
    {

        try {
            $class   = Base::getNamespaceTo('Customer');
            $history = new $class();
            $totalImported = $history->massSyncCustomerHistory($customerId);
        } catch (\Exception $e) {
            return [
                'result'    => false,
                'feedback'  => $e->getMessage()
            ];
        }

        $totalExpired = $totalUnpaid = 0;
        if ($returnTotals) {
            $balance = CustomerBalance::filterAgencies();

            if ($customerId) {
                $balance = $balance->where('customer_id', $customerId);
            }

            $balance = $balance->where('is_paid', 0)
                ->where('sense', 'debit')
                ->get(['total', 'due_date']);

            $totalUnpaid = $balance->sum('total');

            $totalExpired = $balance->filter(function ($item) {
                return $item->due_date < date('Y-m-d');
            })->count();
        }

        return [
            'result'        => true,
            'totalImported' => $totalImported,
            'totalExpired'  => $totalExpired . ' Documentos',
            'totalUnpaid'   => money($totalUnpaid, Setting::get('app_currency')),
            'feedback'      => 'Importados ' . $totalImported . ' novos registos.',
        ];
    }

    /**
     * Get invoice
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getInvoice($id)
    {

        $customerBalance = CustomerBalance::whereHas('customer', function ($q) {
            $q->filterAgencies();
        })
            ->findOrNew($id);
            

        $class    = Base::getNamespaceTo('Document');
        $document = new $class();
        $document = $document->getDocumentPdf($customerBalance->doc_id, $customerBalance->doc_type, $customerBalance->doc_serie_id);

        $data = base64_decode($document);
        header('Content-Type: application/pdf');
        echo $data;
    }

    /**
     * Open modal to send custmer email account balance
     *
     * @param Request $request
     * @param [type] $customerId
     * @return void
     */
    public function editEmailBalance(Request $request, $customerId)
    {
        $customer = Customer::filterSource()
            ->where('id', $customerId)
            ->firstOrFail();

        return view('admin.billing.balance.modals.send_email_balance', compact('customer'))->render();
    }

    /**
     * Send email with current account
     * @param $customerId
     */
    public function sendEmailBalance(Request $request, $customerId)
    {

        $email = $request->email;

        $customer = Customer::filterSource()
            ->findOrFail($customerId);
             
        try {
            $result = $customer->sendEmailAccountBalance($email);
        } catch (\Exception $e) {
            $result = [ 
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }
    
        if ($result) {
            if ($request->ajax()) {
                return response()->json([
                    'result'   => true,
                    'feedback' => 'E-mail enviado com sucesso.'
                ]);
            }
            return Redirect::back()->with('success', 'E-mail enviado com sucesso.');
        }

        if ($request->ajax()) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Falha ao enviar e-mail.'
            ]);
        }
        
        return Redirect::back()->with('error', 'Falha ao enviar e-mail.');
    }

    /**
     * Send email with current account for all customers with pending values
     */
    public function massSendEmailBalance(Request $request)
    {
        $ids   = explode(',', $request->ids);

        $customers = Customer::whereIn('id', $ids)->get();

        foreach ($customers as $customer) {
            try {
                $customer->sendEmail();
            } catch (\Exception $e) {}
        }

        return Redirect::back()->with('success', 'E-mails enviados com sucesso.');
    }


    /**
     * Print summary of billing
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printPdf(Request $request, $customerId)
    {

        $customer  = Customer::filterAgencies()->findOrFail($customerId);

        $documents = CustomerBalance::getPendingDocuments($customerId)->get();

        $totalDocuments = $documents->sum('total');
        $countDocuments = $documents->count();

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $layout = 'pdf';

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'documents'         => $documents,
            'totalDocuments'    => $totalDocuments,
            'countDocuments'    => $countDocuments,
            'documentTitle'     => 'Resumo de Conta Corrente',
            'documentSubtitle'  => $customer->name . ' em ' . date('Y-m-d'),
            'view' => 'admin.billing.balance.pdf.summary'
        ];

        $mpdf->WriteHTML(View::make('admin.layouts.' . $layout, $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Conta Corrente - ' . $customer->name . '(' . date('Y-m-d') . ').pdf', 'I'); //output to screen
    }

    /**
     * Import excel file with current account
     * @param $customerId
     */
    public function uploadDivergences(Request $request)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $excel = Excel::load($request->file->getRealPath());

        if (!$excel) {
            return Redirect::back()->with('error', 'O ficheiro carregado não é suportado.');
        }

        if (!$excel->first()->has('contribuinte') && !$excel->first()->has('saldo')) {
            return Redirect::back()->with('error', 'O ficheiro carregado não é um ficheiro Key Invoice ou não é o ficheiro correto.');
        }

        $errors = [];
        $totalSuccess = 0;

        Excel::load($request->file->getRealPath(), function ($reader)  use ($request, &$errors, &$totalSuccess) {

            $reader->each(function ($row) use ($request, &$errors, &$totalSuccess) {

                $row = $row->toArray();

                $row['contribuinte'] = (string) $row['contribuinte'];
                $row['saldo']        = (float) str_replace('€', '', $row['saldo']);

                if (!empty($row['contribuinte'])) {
                    $customer = Customer::filterSource()->where('vat', $row['contribuinte'])->first();

                    if ($customer && $row['saldo'] != $customer->balance_total_unpaid) {
                        $customer->balance_divergence = $row['saldo'];
                        $customer->save();
                    }
                }
            });
        });

        return Redirect::back()->with('success', 'Ficheiro importado com sucesso.');
    }
}
