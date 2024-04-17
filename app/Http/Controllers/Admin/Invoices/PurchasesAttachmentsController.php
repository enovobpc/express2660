<?php

namespace App\Http\Controllers\Admin\Invoices;

use App\Models\FileRepository;
use App\Models\PurchaseInvoice;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Html, Response, Cache, Setting, Date, Auth, File;

class PurchasesAttachmentsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'purchase_invoices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',purchase_invoices']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $invoiceId) {

        $action = 'Adicionar anexo';

        $attachment = new FileRepository();
        $attachment->source_class = $request->type ?? 'PurchaseInvoice';

        $formOptions = ['route' => ['admin.invoices.purchase.attachments.store', $invoiceId], 'method' => 'POST', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => true, 'files' => true, 'data-replace-with' => '.attachments-content'];

        return view('admin.invoices.purchases.modals.attachment', compact('attachment', 'action', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $invoiceId) {
        return $this->update($request, $invoiceId, null);
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
    public function edit($invoiceId, $id) {

        $action = 'Editar Anexo';

        $attachment = FileRepository::whereHas('purchase_invoice', function($q){
                $q->filterSource();
            })
            ->whereIn('source_class', ['PurchaseInvoice', 'PurchasePaymentNote'])
            ->where('source_id', $invoiceId)
            ->where('id', $id)
            ->findOrfail($id);

        $formOptions = ['route' => ['admin.invoices.purchase.attachments.update', $attachment->source_id, $attachment->id], 'method' => 'PUT', 'files' => true, 'data-toggle' => 'ajax-form', 'data-replace-with' => '.attachments-content'];

        return view('admin.invoices.purchases.modals.attachment', compact('attachment', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $invoiceId = null, $id = null) {

        FileRepository::flushCache(FileRepository::CACHE_TAG);

        $input = $request->all();
        $sourceClass = $request->source_class ?? 'PurchaseInvoice';

        $invoice = PurchaseInvoice::findOrFail($invoiceId);

        $attachment = FileRepository::firstOrNew([
            'source_id'    => $invoiceId,
            'source_class' => $sourceClass,
            'id' => $id
        ]);

        if ($attachment->validate($input)) {
            $attachment->fill($input);
            $attachment->parent_id    = FileRepository::FOLDER_PURCHASE_INVOICES;
            $attachment->source_class = $sourceClass;
            $attachment->source_id    = $invoiceId;
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

            $invoiceAttachments = FileRepository::filterSource()
                ->where('source_class', $sourceClass)
                ->where('source_id', $attachment->source_id)
                ->orderBy('name', 'asc')
                ->get();

            if($request->ajax()) {

                $html = view('admin.invoices.purchases.partials.tabs.attachments_content', compact('invoiceAttachments', 'invoice'))->render();
                if($sourceClass == 'PurchasePaymentNote') {
                    $html = view('admin.invoices.purchases.partials.tabs.attachments_content_payments', compact('invoiceAttachments', 'invoice'))->render();
                }


                return Response::json([
                    'result'    => true,
                    'type'      => 'success',
                    'feedback'  => 'Anexo gravado com sucesso.',
                    'html'      => $html
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
    public function destroy($invoiceId, $id) {

        FileRepository::flushCache(FileRepository::CACHE_TAG);

        $attachment = FileRepository::whereHas('purchase_invoice', function($q){
                $q->filterAgencies();
            })
            ->whereIn('source_class', ['PurchaseInvoice', 'PurchasePaymentNote'])
            ->where('source_id', $invoiceId)
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

        $attachments = FileRepository::whereHas('purchase_invoice', function ($q) {
                $q->filterAgencies();
            })
            ->where('source_class', 'PurchaseInvoice')
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
