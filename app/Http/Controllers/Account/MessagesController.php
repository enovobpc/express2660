<?php

namespace App\Http\Controllers\Account;

use App\Models\CustomerMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;
use DB, View, Response;

class MessagesController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'messages';

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
        return $this->setContent('account.messages.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $customer = Auth::guard('customer')->user();

        $customerId = $customer->id;
        if($customer->customer_id) {
            $customerId = $customer->customer_id;
        }

        $message = CustomerMessage::whereHas('customers', function($q) use($customerId) {
                $q->where('customers.id', $customerId);
            })
            ->where('id', $id)
            ->firstOrFail();

        return view('account.messages.show', compact('message'))->render();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $customer = Auth::guard('customer')->user();

        $customerId = $customer->id;
        if($customer->customer_id) {
            $customerId = $customer->customer_id;
        }

        $message = CustomerMessage::whereId($id)
            ->with(['customers' => function($q) use($customerId) {
                $q->where('customers.id', $customerId);
            }])
            ->whereHas('customers', function($q) use($customerId) {
                $q->where('customers.id', $customerId);
            })
            ->firstOrFail();

        $message = $message->customers->first();
        $message->pivot->deleted_at = new Date();
        $message->pivot->save();

        return Redirect::back()->with('success', 'Mensagem eliminada com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();

        $customerId = $customer->id;
        if($customer->customer_id) {
            $customerId = $customer->customer_id;
        }

        $data = CustomerMessage::whereHas('customers', function($q) use($customerId) {
                $q->where('customers.id', $customerId);
            })
            ->select();

        return Datatables::of($data)
            ->edit_column('created_at', function($row) {
                return $row->created_at->format('Y-m-d');
            })
            ->edit_column('subject', function($row) {
                return view('account.messages.datatables.subject', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('account.messages.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Set message as read
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setRead(Request $request, $id) {

        $customer = Auth::guard('customer')->user();

        $customerId = $customer->id;
        if($customer->customer_id) {
            $customerId = $customer->customer_id;
        }

        $message = CustomerMessage::whereId($id)
                    ->with(['customers' => function($q) use($customerId) {
                        $q->where('customers.id', $customerId);
                    }])
                    ->whereHas('customers', function($q) use($customerId) {
                        $q->where('customers.id', $customerId);
                    })
                    ->first();

        $message = $message->customers->first();
        $message->pivot->is_read = $request->is_read;
        $message->pivot->save();

        return [
            'result' => true
        ];
    }
}