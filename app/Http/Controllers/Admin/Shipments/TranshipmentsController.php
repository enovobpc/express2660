<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\PackType;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Response, Auth;

class TranshipmentsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'transhipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($shipmentId) {

        $parentShipment = Shipment::findOrFail($shipmentId);

        $shipment = $parentShipment->replicate();
        $shipment->provider_id  = null;
        $shipment->volumes      = null;
        $shipment->weight       = null;
        $shipment->ldm          = null;

        $action = 'Adicionar Transbordo';

        $formOptions = array('route' => array('admin.shipments.transhipments.store', $shipmentId), 'method' => 'POST');

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->isShipment()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);
        $hours    = listHours(10);

        $data = compact(
            'shipment',
            'action',
            'formOptions',
            'services',
            'providers',
            'vehicles',
            'trailers',
            'hours'
        );

        return view('admin.shipments.shipments.edit.transhipment', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $shipmentId) {
        return $this->update($request, $shipmentId, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $shipmentId, $id) {

        $shipment = Shipment::findOrFail($shipmentId);

        $action = 'Editar Transbordo #'.$shipment->tracking_code;

        $formOptions = array('route' => array('admin.shipments.transhipments.update', $shipmentId, $id), 'method' => 'PUT');

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->isShipment()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);
        $hours    = listHours(10);

        $data = compact(
            'shipment',
            'action',
            'formOptions',
            'services',
            'providers',
            'vehicles',
            'trailers',
            'hours'
        );
        return view('admin.shipments.shipments.edit.transhipment', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        PackType::flushCache(PackType::CACHE_TAG);

        $input = $request->all();


        $exists = PackType::where('code', $input['code'])->first();
        if($exists) {
            return Redirect::back()->with('errpr', 'Já existe outro tipo com o código indicado.');
        }

        $packType = PackType::findOrNew($id);

        if ($packType->validate($input)) {
            $packType->fill($input);
            $packType->source = config('app.source');
            $packType->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $packType->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        PackType::flushCache(PackType::CACHE_TAG);

        $result = PackType::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::route('admin.pack-types.index')->with('success', 'Registo removido com sucesso.');
    }
}
