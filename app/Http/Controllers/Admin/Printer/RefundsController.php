<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\Agency;
use App\Models\PaymentAtRecipientControl;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\RefundControl;

class RefundsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',refunds_customers|refunds_agencies']);
    }

    /**
     * Create refund Proof
     *
     * @param type $shipmentId
     * @return type
     */
    public function customersProof(Request $request, $id = null){

        try {

            $ids = $request->id;

            if($id == 'customer') {
                $ids = RefundControl::getCustomerAvailableRefunds($request->get('customer'));
            } elseif($id == 'selectedlist') {

                $sourceAgencies = Agency::where('source', config('app.source'))
                        ->pluck('id')
                        ->toArray();

                $ids = $data = Shipment::where('is_collection', 0)
                    ->whereNotNull('charge_price')
                    ->whereIn('shipments.agency_id', $sourceAgencies)
                    ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
                    ->applyRefundsRequestFilters($request)
                    ->pluck('id')
                    ->toArray();
            }

            if(!empty($id) && empty($ids)) {
                $ids = [$id];
            }

            RefundControl::printProof($ids);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar PDF. ' . $e->getMessage());
        }
    }


    /**
     * Print refunds controll summary list
     *
     * @param type $shipmentId
     * @return type
     */
    public function customersSummary(Request $request){
        try {
            $ids = $request->id;

            if($request->has('customer')) {
                $ids = RefundControl::getCustomerAvailableRefunds($request->get('customer'));
            }

            if(!$request->has('id')) {
                $sourceAgencies = Agency::where('source', config('app.source'))
                        ->pluck('id')
                        ->toArray();

                $ids = $data = Shipment::where('is_collection', 0)
                    ->whereNotNull('charge_price')
                    ->whereIn('shipments.agency_id', $sourceAgencies)
                    ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
                    ->applyRefundsRequestFilters($request)
                    ->pluck('id')
                    ->toArray();
            }

            RefundControl::printSummary($ids);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar PDF. ' . $e->getMessage());
        }
    }

    /**
     * Print refunds controll summary list
     *
     * @param type $shipmentId
     * @return type
     */
    public function agenciesSummary(Request $request){
        try {
            $ids = $request->id;
            RefundControl::printSummary($ids, true);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar PDF. ' . $e->getMessage());
        }
    }

    /**
     * Create Delivery Manifest
     *
     * @param type $shipmentId
     * @return type
     */
    public function codSummary(Request $request){

        try {
            $ids = $request->id;
            PaymentAtRecipientControl::printSummary($ids);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar PDF. ' . $e->getMessage());
        }
    }


    /**
     * Create Delivery Manifest
     *
     * @param type $shipmentId
     * @return type
     */
    public function devolutionsSummary(Request $request){

        try {
            $ids = $request->id;
            Shipment::printDevolutionsSummary($ids);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar PDF. ' . $e->getMessage());
        }
    }
}
