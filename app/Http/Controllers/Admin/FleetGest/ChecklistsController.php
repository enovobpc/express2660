<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Checklist;
use App\Models\FleetGest\ChecklistItem;
use App\Models\FleetGest\ChecklistAnswer;
use App\Models\FleetGest\Vehicle;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;
use Auth, DB;

class ChecklistsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_checklists';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_checklists']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->pluck('license_plate', 'id')
            ->toArray();

        return $this->setContent('admin.fleet.checklists.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Adicionar formulário controlo';
  
        $checklist = new Checklist();

        $formOptions = array('route' => array('admin.fleet.checklists.store'), 'method' => 'POST', 'files' => true);

        return view('admin.fleet.checklists.edit', compact('checklist', 'action', 'formOptions'))->render();
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
    public function show($id) {

        $vehicles = ChecklistAnswer::with('vehicle')
            ->where('checklist_id', $id)
            ->groupBy('vehicle_id')
            ->get();

        $answers = ChecklistAnswer::with('vehicle')
            ->where('vehicle_id', @$vehicles->first()->vehicle_id)
            ->where('checklist_id', $id)
            ->groupBy('control_hash')
            ->orderBy('created_at', 'desc')
            ->get([
                '*',
                DB::raw('min(answer) as status')
            ]);

        $data = compact(
            'vehicles',
            'answers'
        );

        return view('admin.fleet.checklists.show', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $checklist = Checklist::filterSource()
                    ->findOrfail($id);

        $action = 'Editar formulário controlo';

        $formOptions = array('route' => array('admin.fleet.checklists.update', $checklist->id), 'method' => 'PUT', 'files' => true);

        return view('admin.fleet.checklists.edit', compact('checklist', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Checklist::flushCache(Checklist::CACHE_TAG);

        $input = $request->all();


        $checklist = Checklist::filterSource()
                        ->findOrNew($id);

        if ($checklist->validate($input)) {
            $checklist->fill($input);
            $checklist->source = config('app.source');
            $checklist->save();

            foreach($input['item_name'] as $key => $itemName) {
                if(!empty($itemName)) {
                    $checklistItem = ChecklistItem::findOrNew(@$input['item_id'][$key]);
                    $checklistItem->checklist_id = $id;
                    $checklistItem->name = $itemName;
                    $checklistItem->type = @$input['item_type'][$key];
                    $checklistItem->description = @$input['item_obs'][$key];
                    $checklistItem->is_active = 1;
                    $checklistItem->save();
                } elseif(!empty(@$input['item_id'][$key])) {
                    ChecklistItem::destroy(@$input['item_id'][$key]);
                }
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $checklist->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Checklist::flushCache(Checklist::CACHE_TAG);

        $cost = Cost::filterSource()->findOrfail($id);

        if (!$cost->delete()) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Checklist::flushCache(Checklist::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Checklist::filterSource()
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

        $data = Checklist::with(['answers' => function($q){
                $q->groupBy('control_hash');
            }])
            ->filterSource()
            ->select();

        return Datatables::of($data)
            ->edit_column('title', function($row) {
                return view('admin.fleet.checklists.datatables.title', compact('row'))->render();
            })
            ->edit_column('is_active', function($row) {
                return view('admin.fleet.checklists.datatables.is_active', compact('row'))->render();
            })
            ->edit_column('answers', function($row) {
                return view('admin.fleet.checklists.datatables.answers', compact('row'))->render();
            })
            ->add_column('answer', function($row) {
                return view('admin.fleet.checklists.datatables.new_answer', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.checklists.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
 * Show form to answer a question
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function answerEdit($checklistId) {

        $checklist = Checklist::filterSource()
            ->findOrfail($checklistId);

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'checklist',
            'vehicles',
            'operators'
        );

        return view('admin.fleet.checklists.create_answer', $data)->render();
    }

    /**
     * Show form to answer a question
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function answerShow($checklistId, $hash) {

        $answers = ChecklistAnswer::where('checklist_id', $checklistId)
            ->where('control_hash', $hash)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.fleet.checklists.show_answer', compact('answers'))->render();
    }

    /**
     * Show form to answer a question
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function answerDestroy($checklistId, $hash) {

        $result = ChecklistAnswer::where('checklist_id', $checklistId)
            ->where('control_hash', $hash)
            ->delete();

        if($result) {
            return Redirect::back()->with('success', 'Registo eliminado com sucesso.');
        }

        return Redirect::back()->with('error', 'Erro ao eliminar o registo.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function answerStore(Request $request, $checklistId) {

        Checklist::flushCache(Checklist::CACHE_TAG);

        $input = $request->all();

        try {

            $controlHash = str_random(5);
            foreach($input['item'] as $itemId => $row) {

                $answer = new ChecklistAnswer();
                $answer->control_hash = $controlHash;
                $answer->checklist_id = $checklistId;
                $answer->checklist_item_id = $itemId;
                $answer->vehicle_id   = $input['vehicle_id'];
                $answer->operator_id  = $input['operator_id'];
                $answer->km           = $input['km'];
                $answer->checklist_id = $checklistId;
                $answer->created_by   = Auth::user()->id;
                $answer->answer = @$row['answer'];
                $answer->obs    = @$row['obs'];
                $answer->save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        } catch(\Exception $e) {
            return Redirect::back()->withInput()->with('error', $answer->errors()->first());
        }
    }

    /**
     * Load answers table
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function answersLoad(Request $request) {

        $answers = ChecklistAnswer::with('vehicle')
            ->where('vehicle_id', @$request->vehicle)
            ->groupBy('control_hash')
            ->orderBy('created_at', 'desc')
            ->get([
                '*',
                DB::raw('min(answer) as status')
            ]);

        return view('admin.fleet.checklists.partials.answers_table', compact('answers'))->render();
    }
}
