<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\FileRepository;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Html, Response, Cache, Setting, Date, Auth, File;

class AttachmentsController extends \App\Http\Controllers\Admin\Controller {

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
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $shipmentId) {

        $action = 'Adicionar anexo';

        $attachment = new FileRepository();

        $formOptions = ['route' => ['admin.shipments.attachments.store', $shipmentId], 'method' => 'POST', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => true, 'files' => true, 'data-replace-with' => '.attachments-content'];

        return view('admin.shipments.shipments.edit.attachment', compact('attachment', 'action', 'formOptions'))->render();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($shipmentId, $id) {

        $action = 'Editar Anexo';

        $attachment = FileRepository::whereHas('shipment', function($q){
                $q->filterAgencies();
            })
            ->where('source_class', 'Shipment')
            ->where('source_id', $shipmentId)
            ->where('id', $id)
            ->findOrfail($id);

        $formOptions = ['route' => ['admin.shipments.attachments.update', $attachment->source_id, $attachment->id], 'method' => 'PUT', 'data-toggle' => 'ajax-form', 'data-replace-with' => '.attachments-content'];

        return view('admin.shipments.shipments.edit.attachment', compact('attachment', 'action', 'formOptions'))->render();
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

        $input = $request->all();
        $input['operator_visible'] = $request->get('operator_visible', false);
        $input['customer_visible'] = $request->get('customer_visible', false);

        $shipment = Shipment::findOrFail($shipmentId);

        $attachment = FileRepository::firstOrNew([
            'source_id'    => $shipmentId,
            'source_class' => 'Shipment',
            'id' => $id
        ]);

        if ($attachment->validate($input)) {
            $attachment->fill($input);
            $attachment->parent_id    = FileRepository::FOLDER_SHIPMENTS;
            $attachment->source_class = 'Shipment';
            $attachment->source_id    = $shipmentId;
            $attachment->user_id      = Auth::user()->id;

            if($request->hasFile('file')) {
                if ($attachment->exists && !empty($attachment->filepath)) {
                    File::delete($attachment->filepath);
                }

                if (!$attachment->upload($request->file('file'), true, 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                }
            } else {
                $attachment->save();
            }

            $shipmentAttachments = FileRepository::filterSource()
                ->where('source_class', 'Shipment')
                ->where('source_id', $attachment->source_id)
                ->orderBy('name', 'asc')
                ->get();

            if($request->ajax()) {
                return Response::json([
                    'result'    => true,
                    'type'      => 'success',
                    'feedback'  => 'Anexo gravado com sucesso.',
                    'html'      => view('admin.shipments.shipments.partials.show.attachments_content', compact('shipmentAttachments', 'shipment'))->render()
                ]);
            } else {
                return Redirect::back()->with('success', 'Anexo carregado com sucesso.');
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

        $attachment = FileRepository::whereHas('shipment', function($q){
                $q->filterAgencies();
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
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o anexo.');
        }

        return Redirect::back()->with('success', 'Anexo removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $result = false;

        $ids = explode(',', $request->ids);

        $attachments = FileRepository::whereHas('shipment', function ($q) {
                $q->filterAgencies();
            })
            ->where('source_class', 'Shipment')
            ->whereIn('id', $ids)
            ->get();

        foreach ($attachments as $attachment) {
            $result = File::delete($attachment->filepath);
            $attachment->delete();
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
}
