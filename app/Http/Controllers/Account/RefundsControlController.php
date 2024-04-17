<?php

namespace App\Http\Controllers\Account;

use App\Models\RefundControl;
use App\Models\RefundControlRequest;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use DB, View, Excel, Response, Setting;

class RefundsControlController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'refunds';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $totalUnconfirmed = RefundControl::whereHas('shipment', function ($q) use ($customer) {
            $q->filterCustomer();
        })
            ->whereNotNull('payment_date')
            ->where('confirmed', 0)
            ->count();

        $view = 'account.refunds.index';

        if (Setting::get('refunds_request_mode')) {
            $view = 'account.refunds.index_requests';
        }

        return $this->setContent($view, compact('totalUnconfirmed'));
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = Shipment::with('refund_control', 'status')
            ->filterCustomer()
            ->whereNotNull('charge_price')
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->select();

        //filter confirmed
        $value = $request->get('confirmed');
        if ($request->has('confirmed')) {
            $data = $data->whereHas('refund_control', function ($q) use ($value) {
                $q->where('confirmed', $value);
            });
        }

        //filter payment method
        $value = $request->get('payment_method');
        if ($request->has('payment_method')) {
            $data = $data->whereHas('refund_control', function ($q) use ($value) {
                $q->where('payment_method', $value);
            });
        }

        //filter shipment date
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter paymentDate
        $dtMin = $request->get('payment_date_min');
        if ($request->has('payment_date_min')) {
            $dtMax = $dtMin;
            if ($request->has('payment_date_max')) {
                $dtMax = $request->get('payment_date_max');
            }

            $data = $data->whereHas('refund_control', function ($q) use ($dtMin, $dtMax) {
                $q->whereBetween('payment_date', [$dtMin, $dtMax]);
            });
        }

        //filter status
        $value = $request->get('status');
        if ($request->has('status')) {
            if ($value == '1') { //pending
                $data = $data->where(function ($q) {
                    if (Setting::get('refunds_request_mode')) {
                        $q->where('status_id', 5);
                        $q->has('refund_control', '=', 0)
                            ->orWhereHas('refund_control', function ($q) {
                                $q->where('received_method', 'claimed');
                                $q->whereNull('payment_method');
                            });
                    } else {
                        $q->has('refund_control', '=', 0)
                            ->orWhereHas('refund_control', function ($q) {
                                $q->where('received_method', 'claimed');
                                $q->whereNull('payment_method');
                            });
                    }
                });
            } else if ($value == '2') { //received and not paid
                $data = $data->whereHas('refund_control', function ($q) {
                    $q->whereNotNull('received_method');
                    $q->whereNull('payment_method');
                });
            } else if ($value == '3') { //paid
                $data = $data->whereHas('refund_control', function ($q) {
                    //$q->whereNotNull('received_method'); //comentado em 1/fev. Mostra tudo o que está pago mesmo que não seja recebido
                    $q->whereNotNull('payment_method');
                });
            } else if ($value == '4') { //request available
                $customer = Auth::guard('customer')->user();


                $authorizedCustomers = [
                    '7308', '9151', '9214', '8958', '9158', '9146',
                    '9213', '9132', '7354', '7411', '9098', '9150',
                    '7364', '9123', '9153', '9161', '9168', '9155',
                    '9156', '9188', '9138', '9157', '9191', '9193',
                    '9194', '9204', '9216', '9217', '9190', '9233',
<<<<<<< HEAD
                    '9238', '9253', '9260', '9267'
=======
                    '9238', '9253', '9260'
>>>>>>> e8644f7a559cb07419b1c79426116a70691b41a1
                ];

                if (in_array($customer->id, $authorizedCustomers)) { //RLR - PODE PEDIR REEMBOLSOS QUANDO O ENVIO ESTIVER ENTREGUE
                    $data = $data->where(function ($q) {
                        $q->has('refund_control', '=', 0);
                        $q->orWhereHas('refund_control', function ($q) {
                            $q->whereNotNull('received_method');
                            $q->whereNull('payment_method');
                            $q->whereNull('requested_method');
                        });
                        $q->orWhereHas('refund_control', function ($q) {
                            $q->whereNull('received_method');
                            $q->whereNull('payment_method');
                            $q->whereNull('requested_method');
                        });
                    })
                        ->whereIn('status_id', [ShippingStatus::DELIVERED_ID]);
                } else { //SÓ PODE PEDIR REEMBOLSOS QUANDO MARCADO COMO RECEBIDO
                    $data = $data->whereHas('refund_control', function ($q) {
                        $q->whereNotNull('received_method');
                        $q->where('received_method', '<>', 'claimed');
                        $q->whereNull('payment_method');
                        $q->whereNull('requested_method');
                    });
                }
            } else if ($value == '5') { //requested
                $data = $data->whereHas('refund_control', function ($q) {
                    $q->whereNotNull('requested_method');
                    $q->whereNotNull('requested_method');
                });
            }
        }

        return Datatables::of($data)
            ->edit_column('date', function ($row) {
                return view('account.refunds.datatables.tracking', compact('row'))->render();
            })
            ->edit_column('status_id', function ($row) {
                return view('account.refunds.datatables.status', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('account.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('charge_price', function ($row) {
                return view('account.refunds.datatables.price', compact('row'))->render();
            })
            ->add_column('requested_method', function ($row) {
                return view('account.refunds.datatables.requested_method', compact('row'))->render();
            })
            ->add_column('received_method', function ($row) {
                return view('account.refunds.datatables.received_method', compact('row'))->render();
            })
            ->edit_column('payment_method', function ($row) {
                return view('account.refunds.datatables.payment_method', compact('row'))->render();
            })
            ->add_column('confirmed', function ($row) {
                return view('account.refunds.datatables.confirmed', compact('row'))->render();
            })
            ->edit_column('customer_obs', function ($row) {
                return view('account.refunds.datatables.obs', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Mass update
     *
     * @param type $shipmentId
     * @return type
     */
    public function massExport(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $input = $request->all();
        $ids = @$input['id'];

        $data = Shipment::with('provider', 'department', 'refund_control')
            ->filterAgencies()
            ->filterCustomer()
            ->whereNotNull('charge_price')
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhere('department_id', $customer->id);
            })
            ->select();


        if ($ids) {
            $data = $data->whereIn('id', $ids);
        } else {
            //filter confirmed
            $value = $request->get('confirmed');
            if ($request->has('confirmed')) {
                $data = $data->whereHas('refund_control', function ($q) use ($value) {
                    $q->where('confirmed', $value);
                });
            }

            //filter shipment date
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }

            //filter paymentDate
            $dtMin = $request->get('payment_date_min');
            if ($request->has('payment_date_min')) {
                $dtMax = $dtMin;
                if ($request->has('payment_date_max')) {
                    $dtMax = $request->get('payment_date_max');
                }

                $data = $data->whereHas('refund_control', function ($q) use ($dtMin, $dtMax) {
                    $q->whereBetween('payment_date', [$dtMin, $dtMax]);
                });
            }

            //filter status
            $value = $request->get('status');
            if ($request->has('status')) {
                if ($value == '1') { //pending
                    $data = $data->has('refund_control', '=', 0);
                } else if ($value == '2') { //received and not paid
                    $data = $data->whereHas('refund_control', function ($q) {
                        $q->whereNotNull('received_method');
                        $q->whereNull('payment_method');
                    });
                } else if ($value == '3') { //received and paid
                    $data = $data->whereHas('refund_control', function ($q) {
                        $q->whereNotNull('received_method');
                        $q->whereNotNull('payment_method');
                    });
                }
            }
        }

        $data = $data->get();

        $header = [
            'Data Envio',
            'TRK',
            'Referência',
            'Cod. Departamento',
            'Departamento',
            'Cod. Serviço',
            'Serviço',
            'Remetente',
            'Morada Remetente',
            'Cod Post Remetente',
            'Localidade Remetente',
            'Pais Remetente',
            'Contacto Remetente',
            'Destinatário',
            'P. Contacto',
            'Morada Destinatário',
            'Cod Post Destinatário',
            'Localidade Destinatário',
            'Pais Destinatário',
            'Contacto Destinatário',
            'Volumes',
            'Peso',
            'Último Estado',
            'Data Último Estado',
            'Observações Expedição',
            'Cobrança'
        ];

        if (!Setting::get('refunds_control_customers_hide_received_column')) {
            $header[] = 'Forma Recebimento';
            $header[] = 'Data Recebimento';
        }

        if (!Setting::get('refunds_control_customers_hide_paid_column')) {
            $header[] = 'Forma Devolução';
            $header[] = 'Data Devolução';
        }

        $header[] = 'Observações Reembolso';


        Excel::create('Listagem de Reembolsos', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#1f2c33');
                    $row->setFontColor('#ffffff');
                });

                foreach ($data as $shipment) {

                    $rowData = [
                        $shipment->date,
                        $shipment->tracking_code,
                        $shipment->reference,
                        @$shipment->department->code,
                        @$shipment->department->name,
                        @$shipment->service->display_code,
                        @$shipment->service->name,
                        $shipment->sender_name,
                        $shipment->sender_address,
                        $shipment->sender_zip_code,
                        $shipment->sender_city,
                        $shipment->sender_country,
                        str_replace(' ', '', $shipment->sender_phone),
                        $shipment->recipient_name,
                        $shipment->recipient_attn,
                        $shipment->recipient_address,
                        $shipment->recipient_zip_code,
                        $shipment->recipient_city,
                        $shipment->recipient_country,
                        str_replace(' ', '', $shipment->recipient_phone),
                        $shipment->volumes,
                        $shipment->weight,
                        @$shipment->status->name,
                        @$shipment->status->created_at,
                        $shipment->obs,
                        $shipment->charge_price ? money($shipment->charge_price) : '',
                    ];

                    if (!Setting::get('refunds_control_customers_hide_received_column')) {
                        $rowData[] = @$shipment->refund_control->received_method ? trans('admin/refunds.payment-methods.' . @$shipment->refund_control->received_method) : '';
                        $rowData[] = @$shipment->refund_control->received_date;
                    }

                    if (!Setting::get('refunds_control_customers_hide_paid_column')) {
                        $rowData[] = @$shipment->refund_control->pay_method ? trans('admin/refunds.payment-methods.' . @$shipment->refund_control->payment_method) : '';
                        $rowData[] = @$shipment->refund_control->payment_date;
                    }

                    $rowData[] = @$shipment->refund_control->obs;

                    $sheet->appendRow($rowData);
                }
            });
        })->export('xls');
    }

    /**
     * Mass update
     *
     * @param type $shipmentId
     * @return type
     */
    public function massPrint(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $input = $request->all();
        $ids = @$input['id'];

        $data = Shipment::with('provider', 'department', 'refund_control')
            ->filterAgencies()
            ->filterCustomer()
            ->whereNotNull('charge_price')
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhere('department_id', $customer->id);
            })
            ->select();


        if ($ids) {
            $data = $data->whereIn('id', $ids);
        } else {
            //filter confirmed
            $value = $request->get('confirmed');
            if ($request->has('confirmed')) {
                $data = $data->whereHas('refund_control', function ($q) use ($value) {
                    $q->where('confirmed', $value);
                });
            }

            //filter shipment date
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }

            //filter paymentDate
            $dtMin = $request->get('payment_date_min');
            if ($request->has('payment_date_min')) {
                $dtMax = $dtMin;
                if ($request->has('payment_date_max')) {
                    $dtMax = $request->get('payment_date_max');
                }

                $data = $data->whereHas('refund_control', function ($q) use ($dtMin, $dtMax) {
                    $q->whereBetween('payment_date', [$dtMin, $dtMax]);
                });
            }

            //filter status
            if ($request->has('status')) {
                if ($value == '1') { //pending
                    $data = $data->has('refund_control', '=', 0);
                } else if ($value == '2') { //received and not paid
                    $data = $data->whereHas('refund_control', function ($q) {
                        $q->whereNotNull('received_method');
                        $q->whereNull('payment_method');
                    });
                } else if ($value == '3') { //received and paid
                    $data = $data->whereHas('refund_control', function ($q) {
                        $q->whereNotNull('received_method');
                        $q->whereNotNull('payment_method');
                    });
                }
            }
        }

        $data = $data->get();

        return RefundControl::printSummary(null, null, $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function updateField(Request $request, $id)
    {

        $input = $request->all();

        $view = implode(array_keys($input));

        $data = [$view => $input[$view]];

        $method = RefundControl::where('shipment_id', $id)
            ->firstOrFail();

        if ($method->validate($input)) {
            $method->fill($data);
            $method->save();

            $row = $method->shipment;

            return Response::json(array(
                'feedback' => 'Alterações gravadas com sucesso.',
                'html'     => view('account.refunds.datatables.' . $view, compact('row'))->render()
            ));
        } else {
            return Redirect::back()->withInput()->with('error', $method->errors()->first());
        }
    }

    /**
     * Close all selected shipments
     * GET /admin/users/selected/close-shipment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massConfirm(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $ids = explode(',', $request->ids);

        $refunds = RefundControl::whereHas('shipment', function ($q) use ($customer) {
            $q->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhere('requested_by', $customer->id)
                    ->orWhere('department_id', $customer->id);
            });
        })
            ->whereIn('shipment_id', $ids)
            ->get();

        foreach ($refunds as $refund) {
            $refund->confirmed = true;
            $refund->save();
        }

        return Redirect::back()->with('success', trans('account/refunds.feedback.confirm.success'));
    }



    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableRequests(Request $request)
    {

        $data = RefundControlRequest::filterCustomer()
            ->orderBy('id', 'desc')
            ->select();

        //filter requested method
        $value = $request->get('requested_method');
        if ($request->has('requested_method')) {
            $data = $data->where('requested_method', $value);
        }

        //filter shipment date
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin . ' 23:59:59';
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max') . ' 23:59:59';
            }
            $data = $data->whereBetween('created_at', [$dtMin, $dtMax]);
        }

        return Datatables::of($data)
            ->add_column('count_shipments', function ($row) {
                return count($row->shipments);
            })
            ->edit_column('total', function ($row) {
                return view('account.refunds.datatables.requests.total', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('account.refunds.datatables.requests.status', compact('row'))->render();
            })
            ->edit_column('requested_method', function ($row) {
                return view('account.refunds.datatables.requests.requested_method', compact('row'))->render();
            })
            ->edit_column('payment_method', function ($row) {
                return view('account.refunds.datatables.requests.payment_method', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('account.refunds.datatables.requests.actions', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Close all selected shipments
     * GET /admin/users/selected/close-shipment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massRequest(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $ids = explode(',', $request->ids);


        $shipments = Shipment::whereIn('id', $ids)
            ->where('customer_id', $customer->id)
            ->get();

        $notDelivered = $shipments->filter(function ($item) {
            return !in_array($item->status_id, [ShippingStatus::DELIVERED_ID]);
        })->count();

        if ($notDelivered) {
            return Redirect::back()->with('error', 'Não é possível solicitar os reembolsos porque selecionou existem envios ainda não entregues.');
        }

        $refundRequest = new RefundControlRequest();
        $refundRequest->requested_method = $request->get('requested_method');
        $refundRequest->customer_id = $customer->id;
        $refundRequest->shipments   = $shipments->pluck('id')->toArray();
        $refundRequest->total       = $shipments->sum('charge_price');
        $refundRequest->save();


        foreach ($ids as $shipmentId) {
            $refundControl = RefundControl::firstOrNew([
                'shipment_id' => $shipmentId
            ]);

            $refundControl->shipment_id = $shipmentId;
            $refundControl->requested_method = $refundRequest->requested_method;
            $refundControl->requested_date = date('Y-m-d');
            $refundControl->save();
        }

        return Redirect::back()->with('success', trans('account/refunds.feedback.confirm.success'));
    }

    /**
     * Show request details
     *
     * @param  int  $id
     * @return Response
     */
    public function showRequests(Request $request, $id)
    {

        $customer = Auth::guard('customer')->user();

        $refundRequest = RefundControlRequest::where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();

        $shipments = Shipment::whereIn('id', $refundRequest->shipments)->get();

        return view('account.refunds.modals.request_details', compact('refundRequest', 'shipments'))->render();
    }

    /**
     * Destroy request details
     *
     * @param  int  $id
     * @return Response
     */
    public function destroyRequests(Request $request, $id)
    {

        $customer = Auth::guard('customer')->user();

        $refundRequest = RefundControlRequest::where('customer_id', $customer->id)
            ->find($id);

        RefundControl::whereIn('shipment_id', $refundRequest->shipments)
            ->whereNull('payment_method')
            ->update([
                'requested_method' => null,
                'requested_date'   => null,
            ]);

        $result = $refundRequest->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o pedido de reembolso.');
        }

        return Redirect::back()->with('success', 'Pedido de reembolso anulado com sucesso.');
    }
}
