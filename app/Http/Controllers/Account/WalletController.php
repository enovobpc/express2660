<?php

namespace App\Http\Controllers\Account;

use App\Models\Customer;
use App\Models\GatewayPayment\Base;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;
use App\Models\CustomerRecipient;
use DB, Excel, Setting;


class WalletController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'wallet';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request) {

        $customer = Auth::guard('customer')->user();

        $departments = Customer::where('customer_id', $customer->id)
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('account.wallet.index', compact('departments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $customer = Auth::guard('customer')->user();

        $formOptions = array('route' => array('account.wallet.store'), 'method' => 'POST');

        $amounts = Setting::get('wallet_amounts') ? explode(',', Setting::get('wallet_amounts')) : ['50', '100', '250', '500'];

        return view('account.wallet.create', compact('customer', 'formOptions', 'amounts'))->render();
    }


    /**
     * Store new Account charging
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {

        $customer = Auth::guard('customer')->user();

        $method = $request->get('method', 'mb');
        $amount = (float) $request->amount;
        $reference = 'Carregamento' . $customer->code;

        $gateway = new Base();

        //MBWAY
        if($method == 'mbway') {

            $data = [
                'customer_id' => $customer->id,
                'target'      => 'Wallet',
                'sense'       => 'credit',
                'reference'   => $reference,
                'phone'       => $request->phone,
                'value'       => $amount,
                'description' => 'Carregamento Conta #' . $customer->code
            ];

            $response = $gateway->createPayment('mbway', $data);

            $result = [
                'result'        => $response['result'],
                'feedback'      => $response['feedback'],
                'id'            => @$response['payment']['id'],
                'phone'         => @$response['payment']['mbway_phone'],
                'amount'        => money($amount),
                'wallet_amount' => $customer->wallet_balance
            ];

        }

        //VISA/MASTERCARD
        elseif($method == 'visa') {

            $data = [
                'customer_id' => $customer->id,
                'sense'       => 'credit',
                'target'      => 'Wallet',
                'reference'   => $reference,
                'value'       => $amount,
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'card'        => $request->card,
                'cvc'         => $request->cvc,
                'month'       => $request->month,
                'year'        => $request->year,
                'description' => 'Carregamento Conta #' . $customer->code
            ];

            $response = $gateway->createPayment('cc', $data);

            $result = [
                'result'        => $response['result'],
                'feedback'      => $response['feedback'],
                'id'            => @$response['payment']['id'],
                'entity'        => @$response['payment']['mb_entity'],
                'reference'     => @$response['payment']['mb_reference'],
                'amount'        => money($amount),
                'wallet_amount' => $customer->wallet_balance,
                'conclude_url'  => @$response['conclude_url']
            ];

        }

        //MULTIBANCO
        else {

            $data = [
                'customer_id' => $customer->id,
                'sense'       => 'credit',
                'target'      => 'Wallet',
                'reference'   => $reference,
                'value'       => $amount,
                'description' => 'Carregamento Conta #' . $customer->code
            ];

            $response = $gateway->createPayment('mb', $data);

            $result = [
                'result'        => $response['result'],
                'feedback'      => $response['feedback'],
                'id'            => @$response['payment']['id'],
                'entity'        => @$response['payment']['mb_entity'],
                'reference'     => chunk_split(@$response['payment']['mb_reference'], 3, ' '),
                'amount'        => money($amount),
                'wallet_amount' => $customer->wallet_balance
            ];
        }

        return response()->json($result);
    }

    /**
     * Check if mbw already paid
     * @param Request $request
     * @return mixed
     */
    public function checkPaymentStatus(Request $request) {

        $customer = Auth::guard('customer')->user();

        if($customer) {

            try {
                $payment = Base::where('customer_id', $customer->id)
                    ->where('id', $request->id)
                    ->first();

                $timeout = false;
                if ($payment) {

                    $now = Date::now();
                    $limit = new Date($payment->expires_at);

                    if ($limit->lte($now)) {
                        $timeout = true;
                        $payment->status = Base::STATUS_REJECTED;
                        $payment->save();
                    }
                }

                //atualiza valor em saldo
                if ($payment->status == Base::STATUS_SUCCESS) {
                    //$customer->addWallet($payment->value); //ja adiciona na wallet quando o gateway de pagamentos

                    $date = Date::now();
                    $payment->paid_at = $date;
                    $payment->save();
                }

                $isPaid = false;
                if($payment->status == Base::STATUS_SUCCESS) {
                    $isPaid = true;
                }

                $result = [
                    'paid'          => $isPaid,
                    'paid_at'       => $payment->paid_at,
                    'timeout'       => $timeout,
                    'wallet_amount' => money($customer->wallet_balance)
                ];
            } catch (\Exception $e) {
                $payment->status = Base::STATUS_REJECTED;
                $payment->save();
            }
        }

        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $customer = Auth::guard('customer')->user();

        $payment = Base::where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();

        return view('account.wallet.show', compact('payment'))->render();
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();

        $data = Base::where(function($q) use($customer) {
                $q->where('customer_id', $customer->id);
                if($customer->customer_id) {
                    $q->orWhere('customer_id', $customer->customer_id);
                }
            })
            ->select();

        return Datatables::of($data)
            ->edit_column('method', function($row) {
                return view('account.wallet.datatables.method', compact('row'))->render();
            })
            ->edit_column('description', function($row) {
                return view('account.wallet.datatables.description', compact('row'))->render();
            })
            ->edit_column('value', function($row) {
                return view('account.wallet.datatables.value', compact('row'))->render();
            })
            ->edit_column('status', function($row) {
                return view('account.wallet.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('account.wallet.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}