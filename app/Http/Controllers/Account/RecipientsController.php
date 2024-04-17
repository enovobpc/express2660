<?php

namespace App\Http\Controllers\Account;

use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\CustomerRecipient;
use DB, Excel;


class RecipientsController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'recipients';

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

        return $this->setContent('account.recipients.index', compact('departments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Adicionar Destinatário';

        $recipient = new CustomerRecipient;

        $formOptions = array('route' => array('account.recipients.store'), 'method' => 'POST');

        return view('account.recipients.edit', compact('recipient', 'action', 'formOptions'))->render();
    }

    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $action = 'Editar Destinatário';

        $customer = Auth::guard('customer')->user();

        $recipient = CustomerRecipient::where(function($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
                /*if($customer->customer_id && $customer->view_parent_shipments) {
                    $q->orWhere('customer_id', $customer->customer_id);
                }*/
            })
            ->findOrfail($id);

        $formOptions = array('route' => array('account.recipients.update', $recipient->id), 'method' => 'PUT');

        return view('account.recipients.edit', compact('recipient', 'action', 'formOptions'))->render();
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

        $customer = Auth::guard('customer')->user();

        $recipient = CustomerRecipient::where(function($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
                /*if($customer->customer_id && $customer->view_parent_shipments) {
                    $q->orWhere('customer_id', $customer->customer_id);
                }*/
            })
            ->findOrNew($id);

        $customerId = $customer->id;
        if($customer->customer_id) {
            $customerId = $customer->customer_id;
        }

        if ($recipient->validate($input)) {
            $recipient->customer_id = $customerId;
            $recipient->fill($input);
            $recipient->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $recipient->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $customer = Auth::guard('customer')->user();

        $result = CustomerRecipient::where(function($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
                /*if($customer->customer_id && $customer->view_parent_shipments) {
                    $q->orWhere('customer_id', $customer->customer_id);
                }*/
            })
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o destinatário.');
        }

        return Redirect::back()->with('success', 'Destinatário removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $customer = Auth::guard('customer')->user();

        $ids = explode(',', $request->ids);

        $result = CustomerRecipient::where('customer_id', $customer->id)
                    ->whereIn('id', $ids)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', trans('account.feedback.mass-destroy.error'));
        }

        return Redirect::back()->with('success', trans('account.feedback.mass-destroy.success'));
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();

        $bindings = [
            'customers_recipients.*',
            DB::raw('(select count(*) from shipments where recipient_id = customers_recipients.id) as shipments')
        ];

        $data = CustomerRecipient::where(function($q) use($customer) {
                if($customer->customer_id) {
                    if(config('app.source') == 'aveirofast') {
                        $q->where('customer_id', $customer->id); //departamento só vê os seus destinatários
                    } else {
                        $q->where('customer_id', $customer->customer_id); //departamento vê todos os destinatarios do cliente mãe
                    }
                } else {
                    $q->where('customer_id', $customer->id);
                    $q->orWhere('customer_id', $customer->customer_id);
                }
            })
            ->select($bindings);

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('account.recipients.datatables.name', compact('row'))->render();
            })
            ->edit_column('shipments', function($row) {
                return view('account.recipients.datatables.shipments', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('account.recipients.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Import customer recipients
     *
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request) {

        $customer = Auth::guard('customer')->user();

        $customerId = $customer->id;
        $customerId = $request->has('customer_id') ? $request->get('customer_id') : $customerId;

        $mapAttrs = config('customers_mapping.recipients.default');

        $excel = Excel::load($request->file->getRealPath());

        if(!$excel) {
            return Redirect::back()->with('error', 'O ficheiro carregado não é suportado.');
        }

        $errors = [];
        $importedIds = [];
        $rowCount = 1;

        Excel::load($request->file->getRealPath(), function($reader)  use($mapAttrs, $request, $customerId, &$errors, &$rowCount, &$importedIds){

            $reader->each(function($row) use($mapAttrs, $request, $customerId, &$errors, &$totalSuccess, &$rowCount, &$importedIds) {

                try {
                    $row = mapArrayKeys($row->toArray(), $mapAttrs);

                    if(strlen($row['country']) > 2) {
                        return Redirect::back()->with('error', 'O código do país em uma ou mais linhas está errado. O campo "País" deve conter o código do país (Ex: PT, ES, FR,..)');
                    }

                    $row['zip_code'] = str_replace(' ', '',  trim($row['zip_code']));
                    $row['address'] = empty(@$row['address2']) ? @$row['address'] :  @$row['address'] . ' ' . @$row['address2'];
                    $row['address'] = trim($row['address']);
                    $row['country'] = empty(@$row['country']) ? Shipment::countryFromZipCode(@$row['zip_code']) : @$row['country'];
                    $row['country'] = strtolower(trim($row['country']));

                    if(@$row['phone']) {
                        $row['phone'] = formatPhone($row['phone']);
                    }

                    if(@$row['mobile']) {
                        $row['mobile'] = formatPhone($row['mobile']);
                    }

                    if(@$row['vat']) {
                        $row['vat'] = str_replace(' ', '', trim($row['vat']));
                        $row['vat'] = str_replace('.', '', $row['vat']);
                    }

                    if(@$row['email']) {
                        $row['email'] = trim(strtolower($row['email']));
                        $validEmail = validateNotificationEmails($row['email']);
                        $row['email'] = @$validEmail['valid']['valid'];
                    }

                    if(empty($errors) && !empty($row['name']) && !empty($row['address']) && !empty($row['zip_code']) && !empty($row['city']) && !empty($row['country'])) {
                        foreach ($row as $key => $value) {
                            if (empty($value)) {
                                $row[$key] = null;
                            }
                        }
                        
                        unset($row['address2']);
                        $row['customer_id'] = $customerId;
                        $recipient = CustomerRecipient::firstOrNew($row);
                        $recipient->save();

                        $importedIds[] = $recipient->id;
                    } else {
                        CustomerRecipient::whereIn('id', $importedIds)->forceDelete();
                        return Redirect::back()->with('error', 'Uma ou mais linhas possuem um dos campos (nome, morada, cód. postal, localidade ou país) em branco.');
                    }
                } catch (\Exception $e) {
                    CustomerRecipient::whereIn('id', $importedIds)->forceDelete();
                    return Redirect::back()->with('error', $e->getMessage());
                }

                $rowCount++;
            });
        });


        return Redirect::back()->with('success', 'Ficheiro importado com sucesso.');

    }

}