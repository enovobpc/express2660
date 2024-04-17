<?php

namespace App\Http\Controllers\Admin\Refunds;

use App\Models\RefundControlRequest;
use Auth, File, Setting, Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\ShippingStatus;
use App\Models\RefundControl;
use App\Models\Shipment;
use App\Models\Provider;
use App\Models\Customer;
use App\Models\Agency;
use App\Models\User;

class CustomersRequestsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'refunds_customers';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',refunds_customers']);
        validateModule('refunds_customers');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $refundRequest = RefundControlRequest::whereHas('customer', function($q) {
            $q->filterSource();
            $q->filterAgencies();
        })->findOrFail($id);

        $shipments = Shipment::where('customer_id', $refundRequest->customer_id)
            ->whereIn('id', $refundRequest->shipments)
            ->get();

        $printUrl = route('admin.printer.refunds.customers.summary', ['id[]' => $refundRequest->shipments]);

        return view('admin.refunds.customers.requests.show', compact('refundRequest', 'shipments', 'printUrl'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $refundRequest = RefundControlRequest::whereHas('customer', function($q) {
            $q->filterSource();
            $q->filterAgencies();
        })->findOrFail($id);

        $shipments = Shipment::where('customer_id', $refundRequest->customer_id)
            ->whereIn('id', $refundRequest->shipments)
            ->get();

        return view('admin.refunds.customers.requests.edit', compact('refundRequest', 'shipments'))->render();
    }
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $input = $request->all();
        $input['send_email']    = $request->get('send_email', false);
        $input['print_proof']   = $request->get('print_proof', false);
        $input['print_summary'] = $request->get('print_summary', false);

        if(!empty($input['send_email']) && !empty($input['email'])) {
            $emails = validateNotificationEmails($input['email']);
            if (!empty($emails['error'])) {
                return Response::json([
                    'result'   => false,
                    'feedback' => 'Não é possível gravar as alterações porque um ou mais e-mails introduzidos são inválidos.'
                ]);
            }
        }

        $refundRequest = RefundControlRequest::whereHas('customer', function($q) {
            $q->filterSource();
            $q->filterAgencies();
        })->findOrFail($id);

        if ($refundRequest->validate($input)) {
            $refundRequest->fill($input);
            $refundRequest->status = 'refunded';
            $refundRequest->save();

            if ($request->hasFile('attachment')) {

                if(!empty($refundRequest->filepath)) {
                    File::delete(public_path() . '/' . $refundRequest->filepath);
                }

                if (!$refundRequest->upload($input['attachment'], true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Erro ao carregar o anexo.');
                }
            } else {
                $refundRequest->save();
            }

            //set all shipments as paid
            foreach ($refundRequest->shipments as $shipmentId) {

                $refundControl = RefundControl::firstOrNew([
                    'shipment_id' => $shipmentId
                ]);

                $refundControl->shipment_id      = $shipmentId;
                $refundControl->requested_method = $refundRequest->requested_method;
                $refundControl->requested_date   = $refundRequest->created_at;
                $refundControl->payment_method   = $refundRequest->payment_method;
                $refundControl->payment_date     = $refundRequest->payment_date;
                $refundControl->customer_obs     = $refundRequest->customer_obs;
                $refundControl->obs              = $refundRequest->obs;
                $refundControl->canceled         = 0;
                $refundControl->request_id       = $refundRequest->id;
                $refundControl->save();
            }


            //send email
            if (!empty($input['send_email']) && !empty($input['email'])) {
                $shipments = Shipment::whereIn('id', $refundRequest->shipments)->get();
                RefundControl::sendEmail($emails['valid'], $shipments);
            }


            $redirect = Redirect::back();

            $printProof = $printSummary = null;
            if($input['print_proof']) {
                $queryStr = implode('&id[]=', $refundRequest->shipments);
                $printProof = route('admin.printer.refunds.customers.proof') . '?id[]=' . $queryStr;
                $redirect = $redirect->with('printProof', $printProof);
            }

            if($input['print_summary']) {
                $queryStr = implode('&id[]=', $refundRequest->shipments);
                $printSummary = route('admin.printer.refunds.customers.summary').'?id[]=' . $queryStr;
                $redirect = $redirect->with('printSummary', $printSummary);
            }

            $result = [
                'result'        => true,
                'feedback'      => 'Estado do reembolso gravado com sucesso.',
                'printProof'    => $printProof,
                'printSummary'  => $printSummary,
                'html'          => view('admin.shipments.shipments.modals.popup_denied')->render()
            ];
        } else {
            $result = [
                'result'   => false,
                'feedback' => $refundRequest->errors()->first()
            ];
        }
        
        return Response::json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $refundRequest = RefundControlRequest::whereHas('customer', function($q) {
            $q->filterSource();
            $q->filterAgencies();
        })->findOrFail($id);

        //delete refund control for all shipments selected
        RefundControl::whereIn('shipment_id', $refundRequest->shipments)
            ->whereNull('payment_method')
            ->update([
                'requested_method' => null,
                'requested_date'   => null,
            ]);

        $result = $refundRequest->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar anular o pedido de reembolso.');
        }

        return Redirect::back()->with('success', 'Pedido de reembolso anulado com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $ids = explode(',', $request->ids);

        $refundRequests = RefundControlRequest::whereHas('customer', function($q) {
                $q->filterSource();
                $q->filterAgencies();
            })
            ->whereIn('id', $ids)
            ->get();


        $errors = [];
        foreach ($refundRequests as $refundRequest) {

            RefundControl::whereIn('shipment_id', $refundRequest->shipments)
                ->whereNull('payment_method')
                ->get();

            $result = $refundRequest->delete();

            if(!$result) {
                $errors[] = $refundRequest->id;
            }
        }

        if (empty($errors)) {
            return Redirect::back()->with('success', 'Pedidos de reembolso selecionados anulados com sucesso.');
        }

        return Redirect::back()->with('error', 'Não foi possível anular um ou mais pedidos de reembolso');
    }

    /**
     * Loading table data
     *
     * @param Request $request
     * @return mixed
     */
    public function datatable(Request $request) {

        $data = RefundControlRequest::with('customer')
            ->whereHas('customer', function($q) {
                $q->filterSource();
                $q->filterAgencies();
            })
            ->select();

        //filter requested method
        $value = $request->get('requested_method');
        if($request->has('requested_method')) {
            $data = $data->where('requested_method', $value);
        }

        //filter shipment date
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin. ' 23:59:59';
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max') .' 23:59:59';
            }
            $data = $data->whereBetween('created_at', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter payment_method
        $value = $request->get('payment_method');
        if($request->has('payment_method')) {
            $data = $data->whereHas('refund_control', function($q) use($value) {
                $q->whereIn('payment_method', $value);
            });
        }

        //filter payment date
        $value = $request->get('payment_date');
        if($request->has('payment_date')) {
            $data = $data->whereHas('refund_control', function($q) use($value) {
                $q->where('payment_date', $value);
            });
        }

        //filter customer
        $value = $request->get('customer');
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('customer_id', function($row) {
                return view('admin.refunds.customers.datatables.requests.customer', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return $row->created_at->format('Y-m-d H:i');
            })
            ->add_column('count_shipments', function($row) {
                return count($row->shipments);
            })
            ->edit_column('total', function($row) {
                return view('admin.refunds.customers.datatables.requests.total', compact('row'))->render();
            })
            ->edit_column('status', function($row) {
                return view('admin.refunds.customers.datatables.requests.status', compact('row'))->render();
            })
            ->edit_column('requested_method', function($row) {
                return view('admin.refunds.customers.datatables.requests.requested_method', compact('row'))->render();
            })
            ->edit_column('payment_method', function($row) {
                return view('admin.refunds.customers.datatables.requests.payment_method', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.refunds.customers.datatables.requests.actions', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->make(true);
    }
}
