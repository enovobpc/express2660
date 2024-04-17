<?php

namespace App\Http\Controllers\Admin\Customers;

use App\Models\CustomerBilling;
use Html, Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\CustomerCovenant;
use App\Models\Service;

class CovenantsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customer_covenants';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customer_covenants']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($customerId) {
        
        $action = 'Adicionar avença mensal';
        
        $covenant = new CustomerCovenant;

        $services = Service::remember(config('cache.query_ttl'))
                        ->cacheTags(Service::CACHE_TAG)
                        ->filterAgencies()
                        ->ordered()
                        ->pluck('name', 'id')
                        ->toArray();

        $formOptions = array('route' => array('admin.customers.covenants.store', $customerId), 'method' => 'POST');
        
        return view('admin.customers.customers.partials.covenants.edit', compact('covenant', 'action', 'formOptions', 'services'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $customerId) {
        return $this->update($request, $customerId, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($customerId, $id) {
        
        $action = 'Editar avença mensal';
        
        $covenant = CustomerCovenant::where('customer_id', $customerId)
                                ->findOrfail($id);

        $services = Service::remember(config('cache.query_ttl'))
                            ->cacheTags(Service::CACHE_TAG)
                            ->filterAgencies()
                            ->ordered()
                            ->pluck('name', 'id')
                            ->toArray();

        $formOptions = array('route' => array('admin.customers.covenants.update', $covenant->customer_id, $covenant->id), 'method' => 'PUT');

        return view('admin.customers.customers.partials.covenants.edit', compact('covenant', 'action', 'formOptions', 'services'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customerId, $id) {
        
        $input = $request->all();
        
        $covenant = CustomerCovenant::whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->where('customer_id', $customerId)
                        ->findOrNew($id);
        
        
        if ($covenant->validate($input)) {
            $covenant->fill($input);
            $covenant->customer_id = $customerId;
            $covenant->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $covenant->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($customerId, $id) {
        
        $result = CustomerCovenant::whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->where('customer_id', $customerId)
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a avença.');
        }

        return Redirect::back()->with('success', 'Avença removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request, $customerId) {
        
        $ids = explode(',', $request->ids);
        
        $result = CustomerCovenant::whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->where('customer_id', $customerId)
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
    public function datatable(Request $request, $customerId) {

        $billedCovenants = [];
        if($request->source = 'billing') {

            $billing = CustomerBilling::where('customer_id', $customerId)
                ->where('month',$request->month)
                ->where('year', $request->year)
                ->where('period',$request->period)
                ->get(['invoice_type', 'invoice_id', 'invoice_doc_id', 'covenants', 'billing_type']);

            foreach ($billing as $row) {
                if($row->covenants) {
                    foreach ($row->covenants as $covenantId) {
                        $billedCovenants[$covenantId] = [
                            'invoice_id'     => $row->invoice_id,
                            'invoice_doc_id' => $row->invoice_doc_id,
                            'invoice_type'   => $row->invoice_type,
                            'api_key'        => $row->api_key,
                            'billing_type'   => $row->billing_type
                        ];
                    }
                }
            }
        }

        $data = CustomerCovenant::with(['service' => function($q){
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Service::CACHE_TAG);
                }])
                ->whereHas('customer', function($q){
                    $q->filterAgencies();
                })
                ->where('customer_id', $customerId)
                ->select();

        return Datatables::of($data)
                ->edit_column('type', function($row) {
                    return trans('admin/global.covenants-types.'.$row->type);
                })
                ->edit_column('max_shipments', function($row) {
                    if($row->type == 'variable') {
                        return $row->max_shipments;
                    }
                })
                ->add_column('service', function($row) {
                    return @$row->service->name;
                })
                ->edit_column('amount', function($row) {
                    return money($row->amount, Setting::get('app_currency'));
                })
                ->edit_column('start_date', function($row) {
                    return $row->start_date->format('Y-m-d');
                })
                ->edit_column('end_date', function($row) {
                    return $row->end_date->format('Y-m-d');
                })
                ->add_column('invoice_id', function($row) use($billedCovenants) {
                    return view('admin.billing.customers.datatables.covenants.invoice', compact('row', 'billedCovenants'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.customers.customers.datatables.covenants.actions', compact('row'))->render();
                })
                ->make(true);
    }

}