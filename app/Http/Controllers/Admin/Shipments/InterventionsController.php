<?php

namespace App\Http\Controllers\Admin\Shipments;


use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Models\Shipment;
use App\Models\ShipmentIntervention;
use Html, Response, Cache, Setting, Date, Auth;


class InterventionsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $shipmentId)
    {
        $action = 'Registar Intervenção';

        $intervention = new ShipmentIntervention();

        $formOptions = ['route' => ['admin.shipments.interventions.store', $shipmentId], 'method' => 'POST', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => true, 'files' => true, 'data-replace-with' => '.customer-support'];

        $data = compact(
            'intervention',
            'action',
            'formOptions'
        );

        return view('admin.shipments.shipments.edit.intervention', $data)->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $shipmentId)
    {
        return $this->update($request, $shipmentId, null);
    }

    /**
     * Edit the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $shipmentId, $id)
    {
        $intervention = ShipmentIntervention::findOrfail($id);

        $action = 'Editar Intervenção';

        $formOptions = ['route' => ['admin.shipments.interventions.update', $shipmentId, $intervention->id], 'method' => 'PUT', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => true, 'files' => true, 'data-replace-with' => '.customer-support'];

        $data = compact(
            'intervention',
            'action',
            'formOptions'
        );

        return view('admin.shipments.shipments.edit.intervention', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $shipmentId, $id=null)
    {
        $input = $request->all();

        $shipment = Shipment::findOrFail($shipmentId);

        $intervention = ShipmentIntervention::where('shipment_id', $shipmentId)
                            ->findOrNew($id);
        $intervention->fill($input);
        $intervention->shipment_id = $shipment->id;

        if(!$intervention->exists) {
            $intervention->user_id = Auth::user()->id;
        }

        $intervention->save();

        if ($request->ajax()) {
            return Response::json([
                'result'    => true,
                'type'      => 'success',
                'feedback'  => 'Intervenção gravada com sucesso.',
                'html'      => view('admin.shipments.shipments.partials.show.interventions', compact('intervention', 'shipment'))->render()
            ]);

        } else {
            return Redirect::back()->with('success', 'Intervenção registada com sucesso.');
        }
    }
}
