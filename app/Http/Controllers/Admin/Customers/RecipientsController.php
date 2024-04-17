<?php

namespace App\Http\Controllers\Admin\Customers;

use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\CustomerRecipient;
use Html, Excel, DB, Auth;

class RecipientsController extends \App\Http\Controllers\Admin\Controller
{

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
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->setContent('admin.customers.recipients.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($customerId)
    {

        $action = 'Adicionar Destinatário';

        $recipient = new CustomerRecipient;

        $formOptions = array('route' => array('admin.customers.recipients.store', $customerId), 'method' => 'POST');

        return view('admin.customers.customers.partials.recipients.edit', compact('recipient', 'action', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $customerId)
    {
        return $this->update($request, $customerId, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($customerId, $id)
    {

        $action = 'Editar Destinatário';

        $recipient = CustomerRecipient::with('assigned_customer')
            ->where('customer_id', $customerId)
            ->findOrfail($id);

        $formOptions = array('route' => array('admin.customers.recipients.update', $recipient->customer_id, $recipient->id), 'method' => 'PUT');

        return view('admin.customers.customers.partials.recipients.edit', compact('recipient', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customerId, $id)
    {

        $input = $request->all();
        $input['assigned_customer_id'] = $request->get('assigned_customer_id');
        $input['always_cod']           = $request->get('always_cod');

        $recipient = CustomerRecipient::where('customer_id', $customerId)
            ->findOrNew($id);

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
    public function destroy($customerId, $id)
    {

        $result = CustomerRecipient::where('customer_id', $customerId)
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
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $result = CustomerRecipient::whereIn('id', $ids)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerMassDestroy(Request $request, $customerId)
    {

        $ids = explode(',', $request->ids);

        $result = CustomerRecipient::where('customer_id', $customerId)
            ->whereIn('id', $ids)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = CustomerRecipient::with('customer')
            ->whereHas('customer', function ($q) {
                $q->filterSource();
                $q->filterSeller();
            })
            ->select();

        if (Auth::user()->isGuest()) {
            $data = $data->where('customer_id', '999999'); //hide data to gest agency role
        }

        //filter country
        $value = $request->country;
        if ($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter phone
        $value = $request->phone;
        if ($request->has('phone')) {
            if ($value == 0) {
                $data = $data->where(function ($q) {
                    $q->whereNull('phone');
                    $q->orWhere('phone', '');
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->whereNotNull('phone');
                    $q->where('phone', '<>', '');
                });
            }
        }

        //filter email
        $value = $request->email;
        if ($request->has('email')) {
            if ($value == 0) {
                $data = $data->where(function ($q) {
                    $q->whereNull('email');
                    $q->orWhere('email', '');
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->whereNotNull('email');
                    $q->where('email', '<>', '');
                });
            }
        }

        //filter vat
        $value = $request->vat;
        if ($request->has('vat')) {
            if ($value == 0) {
                $data = $data->where(function ($q) {
                    $q->whereNull('vat');
                    $q->orWhere('vat', '');
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->whereNotNull('vat');
                    $q->where('vat', '<>', '');
                });
            }
        }

        return Datatables::of($data)
            ->edit_column('code', function ($row) {
                return view('admin.customers.recipients.datatables.code', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.customers.recipients.datatables.name', compact('row'))->render();
            })
            ->edit_column('address', function ($row) {
                return view('admin.customers.recipients.datatables.address', compact('row'))->render();
            })
            ->add_column('contacts', function ($row) {
                return view('admin.customers.recipients.datatables.contacts', compact('row'))->render();
            })
            ->edit_column('country', function ($row) {
                return view('admin.customers.recipients.datatables.country', compact('row'))->render();
            })
            ->edit_column('customer_id', function ($row) {
                return view('admin.customers.recipients.datatables.customer', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.customers.customers.datatables.recipients.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function customerDatatable(Request $request, $customerId)
    {

        $departments = Customer::where('customer_id', $customerId)->pluck('id')->toArray();
        $departments[] = $customerId;

        $data = CustomerRecipient::with('customer', 'assigned_customer')
            ->whereIn('customer_id', $departments)
            ->select();

        return Datatables::of($data)
            ->add_column('department', function ($row) use ($customerId) {
                return view('admin.customers.customers.datatables.recipients.department', compact('row', 'customerId'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.customers.customers.datatables.recipients.name', compact('row'))->render();
            })
            ->edit_column('address', function ($row) {
                return view('admin.customers.customers.datatables.recipients.address', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.customers.customers.datatables.recipients.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Import customer recipients
     *
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request, $customerId)
    {

        $customerId = $request->has('customer_id') ? $request->get('customer_id') : $customerId;

        //$mapAttrs = config('customers_mapping.recipients.envialia');
        $mapAttrs = [
            'codigo'        => 'code',
            'nome'          => 'name',
            'morada'        => 'address',
            'codigo_postal' => 'zip_code',
            'localidade'    => 'city',
            'pais'          => 'country',
            'telefone'      => 'phone',
            'e_mail'        => 'email',
            'responsavel'   => 'responsable',
            'nif'           => 'vat',
            'obs'           => 'obs'
        ];

        $excel = Excel::load($request->file->getRealPath());

        if (!$excel) {
            return Redirect::back()->with('error', 'O ficheiro carregado não é suportado.');
        }

        $errors = [];
        $totalSuccess = 0;

        Excel::load($request->file->getRealPath(), function ($reader)  use ($mapAttrs, $request, $customerId, &$allExpenses, &$errors, &$totalSuccess) {

            $reader->each(function ($row) use ($mapAttrs, $request, $customerId, &$allExpenses, &$errors, &$totalSuccess) {

          
                $row = mapArrayKeys($row->toArray(), $mapAttrs);

                $row['address'] = empty(@$row['address2']) ? @$row['address'] :  @$row['address'] . ' ' . @$row['address2'];
                $row['country'] = empty(@$row['country']) ? Shipment::countryFromZipCode(@$row['zip_code']) : @$row['country'];

                if (!empty($row['name']) && !empty($row['address']) && !empty($row['zip_code']) && !empty($row['city']) && !empty($row['country'])) {
                    foreach ($row as $key => $value) {
                        if (empty($value)) {
                            $row[$key] = null;
                        }
                    }
                    
                    unset($row['address2']);
                    $row['customer_id'] = $customerId;
                    $recipient = CustomerRecipient::firstOrNew($row);
                    $recipient->save();
                }
            });
        });

        if ($request->ajax()) {

            $result = empty($errors) ? true : false;
            $totalErrors = count($errors);

            return Response::json([
                'result'      => $result,
                'feedback'    => 'Ficheiro importado com sucesso.',
                'html'        => '', //view('admin.shipments.partials.import_expenses_errors', compact('errors', 'totalSuccess', 'totalErrors'))->render(),
                'totalErrors' => $totalErrors
            ]);
        }

        return Redirect::back()->with('success', 'Ficheiro importado com sucesso.');
    }

    /**
     * Destroy duplicate addresses
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyDuplicates($id)
    {

        $bindings = ['id', 'code', 'name', 'address', 'zip_code', 'city'];

        try {
            $duplicateRecipients = CustomerRecipient::where('customer_id', $id)->getDuplicates();
            $duplicateRecipients = $duplicateRecipients->toArray();
            $recipientIds = array_column($duplicateRecipients, 'id');

            $recipients = CustomerRecipient::whereIn('id', $recipientIds)->get($bindings);

            foreach ($recipients as $recipient) {

                //get all copies of current recipient
                $copies = CustomerRecipient::where('name', $recipient->name)
                    ->where('address', $recipient->address)
                    ->where('zip_code', $recipient->zip_code)
                    ->where('city', $recipient->city)
                    ->get($bindings);

                //unset copies with customer code
                $masterRecipientId = null;
                foreach ($copies as $key => $item) {
                    if (!empty($item->code) && empty($masterRecipientId)) {
                        $masterRecipientId = $item->id;
                        unset($copies[$key]);
                    }
                }

                if (empty($masterRecipientId)) { //if dont have a master recipient id, assume by default first record
                    $masterRecipientId = $copies[0]['id'];
                    unset($copies[0]);
                }

                $deleteIds = array_column($copies->toArray(), 'id');
                Shipment::withTrashed()->whereIn('recipient_id', $deleteIds)->update(['recipient_id' => $masterRecipientId]);
                CustomerRecipient::whereIn('id', $deleteIds)->delete();
            }

            return Redirect::back()->with('success', 'Moradas duplicadas eliminadas com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request)
    {

        $ids = explode(',', $request->ids);

        $update = [];
        if ($request->has('assign_assigned_customer_id')) {
            $customerId = $request->get('assign_assigned_customer_id');
            if ($customerId == '-1') {
                $update['assigned_customer_id'] = null;
            } else {
                $update['assigned_customer_id'] = $customerId;
            }
        }

        if ($request->has('assign_country')) {
            $country = $request->get('assign_country');
            $update['country'] = $country;
        }


        if (!empty($update)) {
            $result = CustomerRecipient::whereHas('customer', function ($q) {
                $q->filterAgencies();
            })
                ->whereIn('id', $ids)
                ->update($update);

            if (!$result) {
                return Redirect::back()->with('error', 'Não foi possível atualizar os registos selecionados');
            }
        }

        return Redirect::back()->with('success', 'Registos selecionados alterados com sucesso.');
    }
}
