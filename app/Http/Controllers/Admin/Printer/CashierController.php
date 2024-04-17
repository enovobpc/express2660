<?php

namespace App\Http\Controllers\Admin\Printer;

use Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Cashier\Movement;

class CashierController extends \App\Http\Controllers\Admin\Controller {

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
     * Print refunds controll summary list
     *
     * @param type $shipmentId
     * @return type
     */
    public function printMovements(Request $request){
        try {

            $groupedBy = $request->grouped;

            $ids = $request->id;

            if(empty($ids)) {

                $movements = Movement::with('customer', 'provider')
                    ->with(['operator' => function ($q) {
                        $q->withTrashed();
                    }])
                    ->filterSource()
                    ->select();

                //filter date min
                $dtMin = $request->get('date_min');
                if ($request->has('date_min')) {
                    $dtMax = $dtMin;
                    if ($request->has('date_max')) {
                        $dtMax = $request->get('date_max');
                    }
                    $movements = $movements->whereBetween('date', [$dtMin, $dtMax]);
                }

                //filter operator
                $value = $request->get('operator');
                if ($request->has('operator')) {
                    $movements = $movements->where('operator_id', $value);
                }

                //filter type
                $value = $request->get('type');
                if ($request->has('type')) {
                    $movements = $movements->where('type_id', $value);
                }

                //filter customer
                $value = $request->get('customer');
                if ($request->has('customer')) {
                    $movements = $movements->where('customer_id', $value);
                }

                //filter provider
                $value = $request->get('provider');
                if ($request->has('provider')) {
                    $movements = $movements->where('provider_id', $value);
                }

                //filter sense
                $value = $request->get('sense');
                if ($request->has('sense')) {
                    $movements = $movements->where('sense', $value);
                }

                //filter payment method
                $value = $request->get('payment_method');
                if ($request->has('payment_method')) {
                    $movements = $movements->where('payment_method', $value);
                }

                $ids = $movements->pluck('id')->toArray();

            }

            return Movement::printSummary($ids, $groupedBy);

        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar PDF. ' . $e->getMessage());
        }
    }
}
