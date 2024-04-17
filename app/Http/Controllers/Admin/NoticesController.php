<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Croppa, Response, File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Core\Source;
use App\Models\Notice;

class NoticesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'notices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',notices']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $sources = Source::remember(config('cache.query_ttl'))
            ->cacheTags(Source::CACHE_TAG)
            ->pluck('name', 'source')
            ->toArray();

        return $this->setContent('admin.notices.index', compact('sources'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $notice = new Notice;

        $sources = Source::remember(config('cache.query_ttl'))
            ->cacheTags(Source::CACHE_TAG)
            ->get();

        $action = 'Adicionar Aviso ou Notificação';

        $formOptions = array('route' => array('admin.notices.store'), 'method' => 'POST', 'files' => true);

        $data = compact(
            'notice',
            'action',
            'formOptions',
            'sources'
        );

        return $this->setContent('admin.notices.edit', $data);
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

        $notice = Notice::findOrfail($id);

        $sources = Source::remember(config('cache.query_ttl'))
            ->cacheTags(Source::CACHE_TAG)
            ->get();

        $formOptions = array('route' => array('admin.notices.update', $notice->id), 'method' => 'PUT', 'files' => true);

        $action = 'Editar Notícia';

        $data = compact(
            'notice',
            'action',
            'formOptions',
            'sources'
        );
        
        return $this->setContent('admin.notices.edit', $data);
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
        $input['notify'] = $request->get('notify', false);
        
        $notice = Notice::findOrNew($id);

        if ($notice->validate($input)) {
            $notice->fill($input);
            
            //delete image
            if ($request->delete_photo && !empty($notice->filepath)) {
                Croppa::delete($notice->filepath);
                $notice->filepath = null;
                $notice->filename = null;
            }
            
            //upload image
            if($request->hasFile('image')) {

                if ($notice->exists && !empty($notice->filepath)) {
                    Croppa::delete($notice->filepath);
                }

                if (!$notice->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
                
            } else {
                $notice->save();
            }
            
            if($input['notify']) {
                $notice->setNotification();
            }

            return Redirect::route('admin.notices.edit', $notice->id)->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $notice->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $notice = Notice::findOrFail($id);

        $notice->deleteNotification();

        $result = $notice->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o aviso ou notificação.');
        }

        return Redirect::route('admin.notices.index')->with('success', 'Aviso ou notificação removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/brands/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = Notice::whereIn('id', $ids)->delete();
        
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

        $data = Notice::select();

        $sources = Source::remember(config('cache.query_ttl'))
            ->cacheTags(Source::CACHE_TAG)
            ->pluck('name', 'source')
            ->toArray();

        return Datatables::of($data)
                ->add_column('photo', function($row) {
                    return view('admin.partials.datatables.photo', compact('row'))->render();
                })
                ->edit_column('date', function($row) {
                    return $row->date->format('Y-m-d');
                })
                ->edit_column('title', function($row) {
                    return view('admin.notices.datatables.title', compact('row'))->render();
                })
                ->edit_column('sources', function($row) use ($sources) {
                    return view('admin.partials.datatables.sources', compact('row', 'sources'))->render();
                })
                ->edit_column('published', function($row) {
                    return view('admin.notices.datatables.published', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.notices.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function views($id) {

        $notice = Notice::with(['users' => function($q) {
                $q->orderBy('readed', 'asc');
            }])
            ->whereId($id)
            ->firstOrFail();

        $usersGrouped = $notice->users->groupBy('source');

        $sources = Source::remember(config('cache.query_ttl'))
            ->cacheTags(Source::CACHE_TAG)
            ->pluck('name', 'source')
            ->toArray();

        $data = compact(
            'usersGrouped',
            'sources'
        );

        return view('admin.notices.views', $data)->render();
    }
}
