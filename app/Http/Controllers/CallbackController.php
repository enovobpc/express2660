<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\Invoices\SalesController;
use App\Models\Billing\ApiKey;
use App\Models\GatewayPayment\Base;
use App\Models\Invoice;
use App\Models\LogViewer;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShipmentPayment;
use App\Models\ShippingStatus;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Redirect, Setting;

class CallbackController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @param string $gateway
     * @param string $paymentType
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $gateway = null, $paymentType = null)
    {
        $gateway = $gateway ? strtolower($gateway) : 'eupago';

        //Log callback notification
        $trace = LogViewer::getTrace(null, 'Payment Callback Notification');
        Log::info(br2nl($trace));


        if($gateway == 'eupago') {
            $response = $this->euPago($request);
        } else if ($gateway == 'ifthenpay') {
            $response = $this->ifThenPay($request, $paymentType);

            /**
             * Atenção que a ifthenpay quer uma resposta
             * com o código 200 se tudo tiver sido feito com sucesso
             * ou um código 4xx/5xx caso alguma coisa não tenha sido feita
             * isto porque a ifthenpay irá tentar de novo caso aconteça algum problema
             */

            if ($response['result']) {
                return response('OK', 200);
            } else {
                Log::warning($response['feedback']);
                return response('ERROR', 500);
            }
        }


        if($response['result']) {
            return redirect()->route('account.index')->with('success', 'Pagamento recebido com sucesso.');
        }

        return redirect()->route('account.index')->with('error', 'Não foi possível concluir o pagamento.');
    }

    /**
     * EuPago callback
     * @param Request $request
     */
    public function euPago(Request $request) {

        $result = true;
        $status = Base::STATUS_SUCCESS;
        $alreadyPaid = false;

        if($request->has('eupago_status') && $request->get('eupago_status') == 'nok') {
            $result = false;
            $status = Base::STATUS_REJECTED;
        }

        //find payment and update payment
        $paymentCode = $request->get('identificador');
        $paymentCode = str_replace('PAG', '', $paymentCode);
        $payment     = Base::where('code', $paymentCode)->first();

        if(!$payment) {
            $payment = new Base();
        }

        if($payment->status == Base::STATUS_SUCCESS) {
            return ['result' => true];
        }

        // Verifica se já foi pago (caso exista 2x resposta da eupago)
        if(!empty($payment->paid_at)) {
            $alreadyPaid = true;
        }

        $payment->paid_at = date('Y-m-d H:i:s');
        $payment->status  = $status;
        $payment->save();

        // Handles Payment
        if(!$alreadyPaid) {
            $this->handlePaymentOnSuccess($payment);
        }
        return [
            'result' => $result
        ];
    }

    /**
     * ifThenPay callback
     * @param Request $request
     */
    public function ifThenPay(Request $request, $paymentType) {

        $result = true;
        $status = Base::STATUS_SUCCESS;
        $alreadyPaid = false;

        // Check if callback request is valid by verifying the anti pishing key provided to ifthenpay
        // and if the payment was successfull.
        if (!$request->has('chave') || $request->get('chave') != env('IFTHENPAY_ANTI_PHISHING_KEY') || 
            !$request->has('estado') || $request->get('estado') != 'PAGO') {
            return [
                'result'   => false,
                'feedback' => 'ANTI_PHISING_KEY errada ou estado de pagamento inválido.'
            ];
        }

        // Find payment 
        if ($paymentType == 'mbway') {
            $paymentCode = $request->get('referencia');
        } elseif ($paymentType == 'mb') {
            $paymentCode = $request->get('reference');
        }        

        $paymentCode = str_replace('PAG', '', $paymentCode);
        $payment     = Base::where('code', $paymentCode)->first();

        if(!$payment) {
            return [
                'result'   => false,
                'feedback' => 'Não existe nenhum pagamento com a referência indicada no sistema.'
            ];
        } 
        
        if(!empty($payment->paid_at)) {
            return [
                'result'   => false,
                'feedback' => 'O pagamento já foi pago anteriormente.'
            ];
        }

        if($payment->status == Base::STATUS_SUCCESS) {
            return ['result' => true];
        }

        if ($paymentType == 'mbway') {
            $paidValue = (float)$request->get('valor', 0.0);
            $paidAt    = $request->get('datahorapag');
        } elseif ($paymentType == 'mb') {
            $paidValue = (float)$request->get('amount', 0.0);
            $paidAt    = $request->get('payment_datetime');
        }

        $payment->paid_at = $paidAt;
        $payment->status  = $status;
        $payment->save();
        
        $this->handlePaymentOnSuccess($payment);
    }

    /**
     * Handles payment and shipment changes on success
     * @param Base $payment
     */
    public function handlePaymentOnSuccess($payment) {
        
        // Send confirmation e-mail
        // $payment->sendEmail();

        // Update customer wallet balance (when target is null)
        if(@$payment->customer && $payment->target == 'Wallet') {
            if($payment->sense == 'credit') {
                $payment->customer->addWallet($payment->value);
                Log::info('Pagamento [' . $payment->reference . '] (Wallet) recebido com sucesso.');
            } else {
                $payment->customer->subWallet($payment->value);
            }

            // Emitir fatura
            if(@$payment->invoice_id) {
                $this->autoInvoice('Wallet');
            }
        }

        // Update shipment status and history
        if($payment->target == 'Shipment') {
            $shipment = Shipment::where('customer_id', $payment->customer_id)
                ->where('id', $payment->target_id)
                ->first();

            if (!$shipment) {
                Log::error('Envio [' . $payment->target_id . '] para pagamento não encontrado.');
                return Redirect::route('home.index')->with('error', 'Envio para pagamento não encontrado.');
            }

            if ($payment->status == Base::STATUS_SUCCESS) {
                $shipmentStatus = Setting::get('shipment_status_after_create', ShippingStatus::ACCEPTED_ID);
            } else if ($payment->status == Base::STATUS_REJECTED) {
                $shipmentStatus = ShippingStatus::CANCELED_ID;
            }

            $history = new ShipmentHistory();
            $history->shipment_id = $shipment->id;
            $history->status_id   = $shipmentStatus;
            $history->save();

            $shipment->status_id = $shipmentStatus;
            $shipment->ignore_billing = true;
            $shipment->save();

            Log::info('Pagamento [' . $payment->reference . '] [TRK' . $shipment->tracking_code . '] recebido com sucesso.');

            //$this->sendShipmentEmail($shipment);

            // EMITIR FATURA
            if(@!$payment->invoice_id) {
                $invoiceResult = $this->autoInvoice('Shipment', $shipment->id);

                if($invoiceResult['result'])
                    Log::info($invoiceResult['feedback'] . $invoiceResult['invoice_info']);
                elseif (!$invoiceResult['result'])
                    Log::error($invoiceResult['feedback'] . $invoiceResult['invoice_info']);
            }
        }
        
    }

    /**
     * Send Shipment e-mail
     * @param Shipment $shipment
     * 
     * @return void
     */
    public function sendShipmentEmail($shipment) {
        Mail::send('emails.payments.received', compact('shipment', 'customer'), function($message) use($shipment) {
            $message->to($shipment->customer->email)
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Envio TRK'.$shipment->tracking_code.' - Pagamento recebido.');
        });
    }

    /**
     * Generate a invoice after successfull payment
     * @param $target
     * 
     * @return array
     */
    public function autoInvoice($target, $targetId = null) {
        // CHECKS IF TARGET EXISTS
        if (!$target) {
            return [
                'result'       => false,
                'feedback'     => 'Não é possível gerar a fatura: Target em falta.',
                'invoice_info' => ''
            ];
        }

        if ($target == 'Shipment') {

            $billingData = Invoice::getDataFromShipment($targetId, true);
            $customer    = $billingData['customer'];
            $apiKey      = $customer->getBillingApiKey();

            if (!$apiKey) {
                return [
                    'result'       => false,
                    'feedback'     => 'Não é possível gerar a fatura: Chave de API em falta.',
                    'invoice_info' => ''
                ];
            }

            $request = new Request([
                'line'                      => @$billingData['billing']['lines'],
                'total_discount'            => @$billingData['billing']['billing_discount_value'],
                'document_subtotal'         => @$billingData['billing']['document_subtotal'],
                'total_month'               => @$billingData['billing']['total_month'],
                'total_month_vat'           => @$billingData['billing']['total_month_vat'],
                'total_month_no_vat_saved'  => @$billingData['billing']['total_month_no_vat'],
                "customer_id"               => @$billingData['billing']['id'],
                "billing_name"              => @$billingData['billing']['name'],
                "billing_code"              => @$billingData['billing']['code'],
                "billing_address"           => @$billingData['billing']['address'],
                "billing_zip_code"          => @$billingData['billing']['zip_code'],
                "billing_city"              => @$billingData['billing']['city'],
                "billing_country"           => @$billingData['billing']['billing_country'],
                "billing_email"             => @$billingData['billing']['billing_date'],
                "billing_type"              => @$billingData['billing']['billing_type'],
                "agency_id"                 => @$billingData['billing']['agency_id'],
                "vat"                       => @$billingData['billing']['vat'],
                "docdate"                   => @$billingData['doc_date'],
                "duedate"                   => @$billingData['doc_limit_date'],
                "payment_date"              => @$billingData['billing_date'],
                "payment_method"            => @$billingData['billing']['payment_condition'],
                "month"                     => @$billingData['month'],
                "year"                      => @$billingData['year'],
                "period"                    => @$billingData['period'],
                "obs"                       => @$billingData['billing']['obs'],
                "customer"                  => @$billingData['billing']['id'],
                "shipments"                 => implode(@$billingData['billing']['shipments'], ','),
                "covenants"                 => "",
                "products"                  => "",
                "doc_after_payment"         => "",
                "empty_vat"                 => true,
                "submit_confirmed"          => false,
                "draft"                     => false,
                "target"                    => Invoice::TARGET_CUSTOMER_BILLING,
                "doc_type"                  => Invoice::DOC_TYPE_FR,
                "api_key"                   => $apiKey->token,
                "docref"                    => 'TRK' . @$billingData['shipment']['tracking_code']
            ]);
    
            $createInvoiceResult = (new SalesController)->update($request)->getData(true);

            $invoiceInfo = ' [invoice_id: ' . $createInvoiceResult['invoice_id'] . ', invoice_doc_id: ' . $createInvoiceResult['invoice_doc_id'] . ']';

            return [
                'result'       => $createInvoiceResult['result'],
                'feedback'     => $createInvoiceResult['feedback'],
                'invoice_info' => $invoiceInfo
            ];

        } elseif ($target == 'wallet') {
            // GERAR FATURA DE WALLET
            return [];
        }

        return [
            'result'       => false,
            'feedback'     => 'Target inválido',
            'invoice_info' => ''
        ];
    }
}
