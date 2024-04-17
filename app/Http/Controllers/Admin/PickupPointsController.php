<?php

namespace App\Http\Controllers\Admin;

use App\Models\Provider;
use Illuminate\Http\Request;
use App\Models\PickupPoint;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;


class PickupPointsController extends \App\Http\Controllers\Admin\Controller
{
    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'pickup_points';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',pickup_points']);
        validateModule('pudos');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $syncProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->whereNotNull('webservice_method')
            ->Where('webservice_method', '<>', '')
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $recipientCounties = [];
        $recipientDistrict = $request->get('fltr_district');
        if ($request->has('fltr_district')) {
            $recipientCounties = trans('districts_codes.counties.pt.' . $recipientDistrict);
        }

        $data = compact(
            'providers',
            'syncProviders',
            'recipientCounties'
        );

        return $this->setContent('admin.pickup_points.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $pickupPoint = new PickupPoint();

        $action = 'Adicionar Ponto Pickup';

        $formOptions = array('route' => array('admin.pickup-points.store'), 'method' => 'POST', 'class' => 'form-pickup_points');

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $hours = listHours(10);

        $data = compact(
            'pickupPoint',
            'action',
            'formOptions',
            'providers',
            'hours'
        );

        return view('admin.pickup_points.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $pickupPoint = PickupPoint::findOrfail($id);

        $action = 'Editar Ponto Pickup';

        $formOptions = array('route' => array('admin.pickup-points.update', $pickupPoint->id), 'method' => 'PUT', 'class' => 'form-pickup_points');

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $provider = $pickupPoint
            ->provider()
            ->pluck('id')
            ->toArray();

        $hours = listHours(10);

        $data = compact(
            'pickupPoint',
            'action',
            'formOptions',
            'providers',
            'provider',
            'hours'
        );

        return view('admin.pickup_points.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        PickupPoint::flushCache(PickupPoint::CACHE_TAG);

        $input = $request->all();

        $pickupPoint = PickupPoint::filterSource()->findOrNew($id);

        $input['is_active']         = $request->get('is_active', false);
        $input['delivery_saturday'] = $request->get('delivery_saturday', false);
        $input['delivery_sunday']   = $request->get('delivery_sunday', false);

        $input['horary'] = [
            'start_morning'     => $input['start_morning_hour'],
            'start_afternoon'   => $input['start_afternoon_hour'],
            'end_morning'       => $input['end_morning_hour'],
            'end_afternoon'     => $input['end_afternoon_hour'],
        ];

        if ($pickupPoint->validate($input)) {
            $pickupPoint->fill($input);
            $pickupPoint->source = config('app.source');
            $pickupPoint->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $pickupPoint->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PickupPoint::flushCache(PickupPoint::CACHE_TAG);

        $result = PickupPoint::filterSource()->destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o ponto pickup');
        }

        return Redirect::route('admin.pickup-points.index')->with('success', 'Ponto pickup removido com sucesso.');
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

        PickupPoint::flushCache(PickupPoint::CACHE_TAG);

        $ids = explode(',', $request->ids);
        $result = PickupPoint::filterSource()
            ->whereIn('id', $ids)
            ->delete();

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
        $data = PickupPoint::filterSource()
            ->select();

        //filter country
        $value = $request->get('country');
        if ($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter active
        $value = $request->get('active');
        if ($request->has('active')) {
            $data = $data->where('is_active', $value);
        }

        // get all zip codes from district and search
        if ($request->has('district') || $request->has('county')) {
            $district = $request->get('district');
            $county   = $request->get('county');

            $zipCodes = \App\Models\ZipCode::remember(config('cache.query_ttl'))
                ->cacheTags(\App\Models\ShippingStatus::CACHE_TAG)
                ->where('district_code', $district)
                ->where('country', 'pt');

            if ($county) {
                $zipCodes = $zipCodes->where('county_code', $county);
            }

            $zipCodes = $zipCodes->groupBy('zip_code')
                ->pluck('zip_code')
                ->toArray();

            $data = $data->where(function ($q) use ($zipCodes) {
                $q->where('country', 'pt');
                $q->whereIn(DB::raw('SUBSTRING(`zip_code`, 1, 4)'), $zipCodes);
            });
        }

        // Zip code mapping a '3500,9500,etc' to a array
        if ($request->has('zip_code')) {
            $values = explode(',', $request->get('zip_code'));
            $zipCodes = array_map(function ($item) {
                return str_contains($item, '-') ? $item : substr($item, 0, 4) . '%';
            }, $values);
            $data = $data->where(function ($q) use ($zipCodes) {
                foreach ($zipCodes as $zipCode) {
                    $q->orWhere('zip_code', 'like', $zipCode . '%');
                }
            });
        }

        $value = $request->get('provider');
        if ($request->has('provider')) {
            $data = $data->whereIn('provider_id', $value);
        }


        //dd($data->fullSql());

        return Datatables::of($data)
            ->edit_column('provider_id', function ($row) {
                return view('admin.pickup_points.datatables.provider', compact('row'))->render();
            })
            ->edit_column('code', function ($row) {
                return view('admin.pickup_points.datatables.code', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.pickup_points.datatables.name', compact('row'))->render();
            })
            ->edit_column('address', function ($row) {
                return view('admin.pickup_points.datatables.address', compact('row'))->render();
            })
            ->edit_column('contact', function ($row) {
                return view('admin.pickup_points.datatables.contacts', compact('row'))->render();
            })
            ->edit_column('country', function ($row) {
                return view('admin.pickup_points.datatables.country', compact('row'))->render();
            })
            ->edit_column('saturday', function ($row) {
                $status = $row->delivery_saturday;
                return view('admin.pickup_points.datatables.status', compact('status'))->render();
            })
            ->edit_column('sunday', function ($row) {
                $status = $row->delivery_sunday;
                return view('admin.pickup_points.datatables.status', compact('status'))->render();
            })
            ->edit_column('is_ative', function ($row) {
                $status = $row->is_active;
                return view('admin.pickup_points.datatables.status', compact('status'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.pickup_points.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
