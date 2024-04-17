<?php

namespace App\Http\Controllers\Admin\Prospects;

use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Customer;
use App\Models\User;
use Html, Auth, Croppa, Response, Setting;

class MeetingsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'meetings';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',meetings']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $status = array(
            '1' => 'Ativo',
            '0' => 'Bloqueado'
        );
        
        $agencies = Auth::user()->listsAgencies();
        
        $sellers = User::remember(5)
                        ->filterAgencies()
                        ->isSeller()
                        ->where('id', '>', 1)
                        ->pluck('name', 'id')
                        ->toArray();

        return $this->setContent('admin.meetings.index', compact('status', 'types', 'agencies', 'sellers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Nova Reunião';

        $meeting = new Customer;

        $customer = null;
        if($request->has('customer')) {
            $customer = Customer::find($request->get('customer'));
        }

        $hours = listHours(5, 1, 7, 0, 23);

        $formOptions = array('route' => array('admin.meetings.store'), 'class' => 'form-meetings');
        
        $sellers = User::remember(5)
                        ->filterAgencies()
                        ->where('id', '>', 1)
                        ->pluck('name', 'id')
                        ->toArray();


        return view('admin.meetings.edit', compact('action', 'formOptions', 'meeting', 'sellers', 'customer', 'hours'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//        
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Reunião';
        
        $meeting = Meeting::with('customer')
                        ->whereHas('customer', function($q) {
                            $q->filterAgencies();
                        })
                        ->findOrFail($id);

        $formOptions = array('route' => array('admin.meetings.update', $meeting->id), 'method' => 'PUT', 'class' => 'form-meetings');

        $hours = listHours(5, 1, 7, 0, 23);

        $sellers = User::withTrashed()
                        ->filterAgencies()
                        ->isSeller()
                        ->where('id', '>', 1)
                        ->pluck('name', 'id')
                        ->toArray();

        return view('admin.meetings.edit', compact('meeting', 'action', 'formOptions', 'sellers', 'hours'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) {
        
        $user = Auth::user();
        $input = $request->all();
        $input['save_on_calendar'] = $request->get('save_on_calendar', false);

        $customer = Customer::filterAgencies()
                            ->findOrFail($input['customer_id']);

        $input['is_prospect'] = $customer->is_prospect;
        $input['date']      = $input['date'].' '.$input['hour'].':00';
        $input['seller_id'] = $request->get('seller_id', null);
        $input['seller_id'] = ($user->hasRole(config('permissions.role.seller')) && isset($input['seller_id'])) ? $user->id : $input['seller_id'];

        $meeting  = Meeting::findOrNew($id);

        if(!$meeting->exists) {
            $input['created_by'] = Auth::user()->id;
        }
        
        if ($meeting->validate($input)) {
            $meeting->fill($input);
            $meeting->save();

            if($input['save_on_calendar']) {
                $meeting->setOnCalendar();
            }

            return [
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.'
            ];
        }

        return [
            'result'   => false,
            'feedback' => $meeting->errors()->first()
        ];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $meeting = Meeting::whereId($id)
                        ->first();

        $meeting->deleteFromCalendar();

        $result = $meeting->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o cliente.');
        }

        return Redirect::route('admin.meetings.index')->with('success', 'Cliente removido com sucesso.');
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
        
        $result = Meeting::whereIn('id', $ids)
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
    public function datatable(Request $request) {

        $data = Meeting::with('customer')
                        ->with(['seller' => function($q){
                            $q->withTrashed();
                        }])
                        ->whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->filterSeller()
                        ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {

            $dtMax = $dtMin;

            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter agency
        $value = $request->agency;
        if($request->has('agency')) {
            $data = $data->whereHas('customer', function($q) use($value) {
                $q->where('agency_id', $value);
            });
        }

        //filter prospect
        $value = $request->prospect;
        if($request->has('prospect')) {
            $data = $data->where('is_prospect', $value);
        }

        //filter customer
        $value = $request->customer;
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }
        
        //filter seller
        $value = $request->seller;
        if($request->has('seller')) {
            $data = $data->where('seller_id', $value);
        }

        if(Auth::user()->isGuest()) {
            $data = $data->where('customer_id', '99999'); //hide data to gest agency role
        }

        return Datatables::of($data)
                        ->edit_column('date', function($row) {
                            return view('admin.meetings.datatables.date', compact('row'))->render();
                        })
                        ->edit_column('customer_id', function($row) {
                            return view('admin.meetings.datatables.customer', compact('row'))->render();
                        })
                        ->edit_column('seller_id', function($row) {
                            return view('admin.meetings.datatables.seller', compact('row'))->render();
                        })
                        ->edit_column('objectives', function($row) {
                            return view('admin.meetings.datatables.objectives', compact('row'))->render();
                        })
                        ->edit_column('occurrences', function($row) {
                            return view('admin.meetings.datatables.occurrences', compact('row'))->render();
                        })
                        ->edit_column('charges', function($row) {
                            return view('admin.meetings.datatables.charges', compact('row'))->render();
                        })
                        ->edit_column('status', function($row) {
                            return view('admin.meetings.datatables.status', compact('row'))->render();
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.meetings.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $customers = Customer::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->get(['name', 'code', 'id', 'is_prospect']);

            if($customers) {

                $results = array();
                foreach($customers as $customer) {

                    $text = $customer->code;
                    $text.= $customer->code ? ' - ' : '';
                    $text.= str_limit($customer->name, 40);
                    $text.= $customer->is_prospect ? ' (Prospect)' : ' (Cliente)';

                    $results[]=array('id'=> $customer->id, 'text' => $text);
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }
    
}
