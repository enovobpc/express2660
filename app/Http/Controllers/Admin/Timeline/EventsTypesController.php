<?php

namespace App\Http\Controllers\Admin\Timeline;

use App\Models\Timeline\EventType;
use Illuminate\Http\Request;
use Html, Cache, Response, Auth, Redirect;

class EventsTypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'cargo_planning';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',cargo_planning']);
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $type = new EventType();

        $action = 'Novo tipo de evento';

        $formOptions = array('route' => array('admin.timeline.types.store'), 'method' => 'POST');

        $colors = trans('admin/global.colors');

        $data = compact(
            'type',
            'action',
            'formOptions',
            'colors'
        );

        return view('admin.timeline.types.edit', $data)->render();
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

        $type = EventType::filterSource()->findOrfail($id);

        $action = 'Editar tipo de evento';

        $formOptions = array('route' => array('admin.timeline.types.update', $type->id), 'method' => 'PUT');

        $colors = trans('admin/global.colors');

        $data = compact(
            'type',
            'action',
            'formOptions',
            'colors'
        );

        return view('admin.timeline.types.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        EventType::flushCache(EventType::CACHE_TAG);

        $input = $request->all();

        $type = EventType::filterSource()->findOrNew($id);

        if ($type->validate($input)) {
            $type->fill($input);
            $type->source = config('app.source');
            $type->save();

            return Redirect::back()->with('success', 'Tipo evento gravado com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $type->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        EventType::flushCache(EventType::CACHE_TAG);

        $result = EventType::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o tipo de evento');
        }

        return Redirect::back()->with('success', 'Tipo de evento eliminado com sucesso.');
    }
}
