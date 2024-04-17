<?php

namespace App\Http\Controllers\Account;

use App\Models\FileRepository;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use File, Response;


class ShipmentsAttachmentsController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $shipmentId) {

        $action = trans('account/shipments.modal-attachments.title');

        $attachment = new FileRepository();

        $formOptions = ['route' => ['account.shipments.attachments.store', $shipmentId], 'method' => 'POST', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => true, 'files' => true, 'data-replace-with' => '.attachments-content'];

        return view('account.shipments.modals.edit_attachment', compact('attachment', 'action', 'formOptions'))->render();
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
    public function edit($shipmentId, $id) {

        $action = trans('account/shipments.modal-attachments.edit-title');

        $attachment = FileRepository::whereHas('shipment', function($q){
            $q->filterAgencies();
        })
            ->where('source_class', 'Shipment')
            ->where('source_id', $shipmentId)
            ->where('id', $id)
            ->findOrfail($id);

        $formOptions = ['route' => ['account.shipments.attachments.update', $attachment->source_id, $attachment->id], 'method' => 'PUT', 'data-toggle' => 'ajax-form', 'data-replace-with' => '.attachments-content'];

        return view('account.shipments.modals.edit_attachment', compact('attachment', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $shipmentId = null, $id = null) {

        FileRepository::flushCache(FileRepository::CACHE_TAG);

        $customer = Auth::guard('customer')->user();
        $input = $request->all();

        $shipment = Shipment::findOrFail($shipmentId);

        $attachment = FileRepository::firstOrNew([
            'source_id'    => $shipmentId,
            'source_class' => 'Shipment',
            'id'           => $id
        ]);

        if ($attachment->validate($input)) {
            $attachment->fill($input);
            $attachment->parent_id    = FileRepository::FOLDER_SHIPMENTS;
            $attachment->source_class = 'Shipment';
            $attachment->source_id    = $shipmentId;
            $attachment->customer_id  = $customer->id;
            $attachment->customer_visible = true;

            if($request->hasFile('file')) {
                if ($attachment->exists && !empty($attachment->filepath)) {
                    File::delete($attachment->filepath);
                }

                if (!$attachment->upload($request->file('file'), true, 40)) {
                    return Redirect::back()->withInput()->with('error', trans('account/shipments.modal-attachments.feedback.save.error'));
                }
            } else {
                $attachment->save();
            }

            $shipmentAttachments = FileRepository::filterSource()
                ->where('customer_visible', 1)
                ->where('source_class', 'Shipment')
                ->where('source_id', $attachment->source_id)
                ->orderBy('name', 'asc')
                ->get();

            if($request->ajax()) {
                return Response::json([
                    'result'    => true,
                    'type'      => 'success',
                    'feedback'  => trans('account/shipments.modal-attachments.feedback.save.success'),
                    'html'      => view('account.shipments.partials.show.attachments', compact('shipmentAttachments', 'shipment', 'customer'))->render()
                ]);
            } else {
                return Redirect::back()->with('success', trans('account/shipments.modal-attachments.feedback.save.success'));
            }
        }

        if($request->ajax()) {
            return Response::json([
                'result'    => false,
                'type'      => 'error',
                'feedback'  => $attachment->errors()->first()
            ]);
        } else {
            return Redirect::back()->withInput()->with('error', $attachment->errors()->first());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shipmentId, $id) {

        FileRepository::flushCache(FileRepository::CACHE_TAG);

        $customer = Auth::guard('customer')->user();

        $attachment = FileRepository::whereHas('shipment', function($q) use($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->where('source_class', 'Shipment')
            ->where('source_id', $shipmentId)
            ->where('id', $id)
            ->firstOrFail();

        if(File::exists($attachment->filepath)) {
            $result = File::delete($attachment->filepath);
        } else {
            $result = true;
        }

        if($result) {
            $result = $attachment->delete();
        }

        if (!$result) {
            return Redirect::back()->with('error', trans('account/shipments.modal-attachments.feedback.destroy.error'));
        }

        return Redirect::back()->with('success', trans('account/shipments.modal-attachments.feedback.destroy.success'));
    }
}