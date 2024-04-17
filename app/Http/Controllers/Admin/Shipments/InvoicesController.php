<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\Agency;
use App\Models\Billing\Item;
use App\Models\Customer;
use App\Models\CustomerBilling;
use App\Models\CustomerType;
use App\Models\Invoice;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Setting, Auth;

class InvoicesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',billing|invoices']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id) {

        if($request->get('doc-type') == 'transport-guide') {
            return $this->createTransportGuide($request, $id);
        }

        $billingData = Invoice::getDataFromShipment($id);
        $customer     = $billingData['customer'];
        $billing      = $billingData['billing'];
        $shipment     = $billingData['shipment'];
        $billingDate  = $billingData['billing_date'];
        $docDate      = $billingData['doc_date'];
        $docLimitDate = $billingData['doc_limit_date'];
        $month        = $billingData['month'];
        $billingMonth = false;
        $year         = $billingData['year'];
        $period       = $billingData['period'];
        $newCustomerCode = $customer->setCode(false);

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $apiKeys  = Invoice::getApiKeys();
        $vatTaxes = Invoice::getVatTaxes();

        $paymentConditions = PaymentCondition::filterSource()
            ->where('code', '<>', 'sft')
            ->isActive()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();


        $appCountry = Setting::get('app_country');

        $customerCategories = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = ['route' => ['admin.invoices.store', 'customer' => @$customer->id, 'target' => 'CustomerBilling', 'month' => $month, 'year' => $year, 'period' => $period], 'method' => 'POST', 'class' => 'form-billing'];

        $action = 'Faturar Envio ' . $shipment->tracking_code;

        $data = compact(
            'billing',
            'billingMonth',
            'customer',
            'year',
            'month',
            'docDate',
            'docLimitDate',
            'apiKeys',
            'period',
            'vatTaxes',
            'agencies',
            'action',
            'formOptions',
            'newCustomerCode',
            'paymentConditions',
            'paymentMethods',
            'appCountry',
            'customerCategories'
        );

        return view('admin.invoices.sales.edit', $data)->render();
    }

    /**
     * Create transport guide
     *
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function createTransportGuide(Request $request, $id)
    {
        $shipment = Shipment::filterAgencies()->find($id);
        $customer = @$shipment->customer;

        $formOptions = ['route' => ['admin.invoices.store', 'doc-type' => 'transport-guide', 'customer' => @$customer->id, 'shipment' => $id, 'target' => 'Shipment'], 'method' => 'POST', 'class' => 'form-billing'];

        $shippingDate = $shipment->shipping_date->format('Y-m-d');
        $deliveryDate = new Date($shipment->delivery_date);
        $dueDate      = $deliveryDate->format('Y-m-d');

        $apiKeys  = Invoice::getApiKeys();
        $vatTaxes = Invoice::getVatTaxes();

        $lines = [];
        if($shipment->pack_dimensions->isEmpty()) {
            $lines[] = [
                'description' => 'Items diversos',
                'qty'         => $shipment->volumes,
                'price'       => $shipment->total_price + $shipment->total_expenses,
                'vat'         => empty($shipment->vat_rate_id) ? $shipment->getVatRate(true) : $shipment->vat_rate
            ];
        } else {
            foreach ($shipment->pack_dimensions as $dimension) {
                $lines[] = [
                    'description' => $dimension->description ? $dimension->description : 'Artigos diversos',
                    'qty'         => $dimension->qty,
                    'price'       => 0,
                    'vat'         => empty($shipment->vat_rate_id) ? $shipment->getVatRate(true) : $shipment->vat_rate
                ];
            }
        }


        $data = compact(
            'shipment',
            'customer',
            'formOptions',
            'dueDate',
            'apiKeys',
            'vatTaxes',
            'lines'
        );

        return view('admin.invoices.sales.edit_transport_guide', $data)->render();
    }
}
