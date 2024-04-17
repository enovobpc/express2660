<?php

namespace App\Http\Controllers\Admin\Sms;

use App\Models\Customer;
use App\Models\Sms\Pack;
use App\Models\Sms\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Response;

class SmsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'sms';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',sms']);
        validateModule('sms');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $remainingSms = Pack::countAvailableSms();

        return $this->setContent('admin.sms.index', compact('remainingSms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $sms = new Sms();

        if($request->has('to')) {
            $sms->to = $request->get('to');
        }

        if($request->has('message')) {
            $sms->message = $request->get('message');
        }

        $action = 'Enviar SMS';

        $formOptions = array('route' => array('admin.sms.store'), 'method' => 'POST', 'class' => 'form-sms');

        $remainingSms = Pack::countAvailableSms();

        $phones = Customer::filterSource()
                ->filterAgencies()
                ->where(function($q){
                    $q->whereNotNull('mobile');
                    $q->where('mobile', '<>', '');
                })
                ->isProspect(false)
                ->isActive()
                ->pluck('name', 'mobile')
                ->toArray();

        $data = compact(
            'sms',
            'action',
            'formOptions',
            'remainingSms',
            'phones'
        );

        return view('admin.sms.partials.sms.edit', $data)->render();
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $sms = Sms::findOrfail($id);

        $action = 'Editar SMS';

        $formOptions = array('route' => array('admin.sms.update', $sms->id), 'method' => 'PUT', 'class' => 'form-sms');

        $remainingSms = Pack::countAvailableSms();

        $phones = Customer::filterSource()
            ->filterAgencies()
            ->where(function($q){
                $q->whereNotNull('mobile');
                $q->where('mobile', '<>', '');
            })
            ->isProspect(false)
            ->isActive()
            ->pluck('name', 'mobile')
            ->toArray();

        $data = compact(
            'sms',
            'action',
            'formOptions',
            'remainingSms',
            'phones'
        );

        return view('admin.sms.partials.sms.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Sms::flushCache(Sms::CACHE_TAG);
        Pack::flushCache(Pack::CACHE_TAG);

        $input = $request->all();
        $input['to'] = implode(';', $request->get('to'));

        //valida pack
        $sms = Sms::filterSource()->findOrNew($id);

        if ($sms->validate($input)) {

            try {
                $sms->fill($input);
                $sms->source = config('app.source');
                $sms->send();

                return Redirect::back()->with('success', 'Mensagem enviada com sucesso.');

            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }

        }
        
        return Redirect::back()->withInput()->with('error', $sms->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Sms::flushCache(Sms::CACHE_TAG);

        $result = Sms::filterSource()
                    ->whereId($id)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a SMS');
        }

        return Redirect::route('admin.sms.index')->with('success', 'SMS eliminado com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Sms::flushCache(Sms::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Sms::filterSource()
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
    public function datatable(Request $request) {

        $data = Sms::filterSource()
                    ->select();

        if(config('app.source') == 'corridadotempo') {
            $data = $data->groupBy('message');
        }

        return Datatables::of($data)
            ->add_column('source', function($row) {
                return view('admin.sms.datatables.history.source', compact('row'))->render();
            })
            ->edit_column('to', function($row) {
                return view('admin.sms.datatables.history.to', compact('row'))->render();
            })
            ->edit_column('sms_parts', function($row) {
                return view('admin.sms.datatables.history.sms_parts', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.sms.datatables.history.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
