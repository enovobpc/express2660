<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Trip\Trip;
use App\Models\ShippingStatus;
use App\Models\Equipment\Equipment;
use Illuminate\Http\Request;
use Auth, Excel, File, DB, Date, Response;
use Mockery\Exception;
use Illuminate\Support\Facades\Redirect;

class TripsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $lastRow = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',delivery_management']);
    }

    /**
     * Export index controller
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        try {
            return $this->currentList($request);
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function currentList(Request $request)
    {
        $ids = $request->id;

        $data = Trip::filterAgencies()
            ->with('operator', 'provider', 'vehicleData', 'shipments', 'expenses', 'period', 'delivery_route', 'pickup_route')->filterSource();

        if (!empty($ids)) {
            $data = $data->whereIn('id', $ids);
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max') . ' 00:00:00';
            }
            $data = $data->whereBetween('pickup_date', [$dtMin . ' 00:00:00', $dtMax . ' 23:59:59']);
        }

        //filter start country
        $value = $request->start_country;
        if ($request->has('start_country')) {
            $data = $data->whereIn('start_country', $value);
        }

        //filter end country
        $value = $request->end_country;
        if ($request->has('end_country')) {
            $data = $data->whereIn('end_country', $value);
        }

        //filter vehicle
        $value = $request->vehicle;
        if ($request->has('vehicle')) {
            $data = $data->whereIn('vehicle', $value);
        }

        //filter trailer
        $value = $request->trailer;
        if ($request->has('trailer')) {
            $data = $data->whereIn('trailer', $value);
        }
        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter provider
        $value = $request->provider;
        if ($request->has('provider')) {
            $data = $data->whereIn('provider_id', $value);
        }

        //filter pickup route
        $value = $request->pickup_route;
        if ($request->has('pickup_route')) {
            $data = $data->whereIn('pickup_route_id', $value);
        }

        //filter delivery route
        $value = $request->delivery_route;
        if ($request->has('delivery_route')) {
            $data = $data->whereIn('delivery_route_id', $value);
        }

        //filter concluded
        $value = $request->concluded;
        if ($request->has('concluded')) {
            if ($value == '1') {
                $data = $data->whereHas('shipments', function ($q) {
                    $q->whereIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID]);
                });
            } else {
                $data = $data->whereHas('shipments', function ($q) {
                    $q->whereNotIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID]);
                });
            }
        }

        //filter agency
        $values = $request->agency;
        if ($request->has('agency')) {

            $data = $data->whereHas('operator', function ($q) use ($values) {
                $q->where(function ($q) use ($values) {
                    foreach ($values as $value) {
                        $q->orWhere('agencies', 'like', '%"' . $value . '"%');
                    }
                });
            });
        }

        $value = $request->assistant;
        if ($request->has('assistant')) {
            $data = $data->where('assistants', 'like', '%"' . $value . '"%');
        }

        Trip::exportExcel($data->get());
    }
}
