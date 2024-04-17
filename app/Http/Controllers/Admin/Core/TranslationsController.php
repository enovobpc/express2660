<?php

namespace App\Http\Controllers\Admin\Core;

use App\Models\Core\Translation;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Response;

class TranslationsController extends \App\Http\Controllers\Admin\Controller {
    
    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'translations';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //a permission é so para o caso de querermos dar acesso direto ao link a algum cliente.
        //so nos temos acesso ao link por isso não haverá problemas com permissões
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']); 
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index() {

        return $this->setContent('admin.core.translations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create() {
        
        $action      = 'Adicionar tradução';

        $translation = new Translation();

        $formOptions = array('route' => array('core.translations.store'), 'method' => 'POST');
        
        $data = compact(
            'translation', 
            'action',
            'formOptions'
        );

        return view('admin.core.translations.edit', $data)->render();
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
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function edit($id) {
        
        $action      = 'Editar tradução';

        $translation = Translation::findOrFail($id);

        $formOptions = array('route' => array('core.translations.update', $id), 'method' => 'PUT');
        
        $data = compact(
            'translation', 
            'action',
            'formOptions'
        );

        return view('admin.core.translations.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) {

        $input = $request->all();

        $translation = Translation::findOrNew($id);
        $key = $translation->key;

        if($request->target == 'field') {
            $translation->value        = $request->value;
            $translation->is_published = 0;
            $translation->save();

            return response()->json([
                'result'   => true,
                'feedback' => 'Gravado com sucesso'
            ]);

        } else {
        
            foreach($input['value'] as $locale => $value) {

                $translation = Translation::firstOrNew([
                    'key' => $key,
                    'locale' => $locale
                ]);

                $translation->key          = $key;
                $translation->value        = $value;
                $translation->locale       = $locale;
                $translation->is_published = 0;
                $translation->save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = Translation::whereId($id)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
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
        $ids    = explode(',', $request->ids);
        $result = Translation::whereIn('id', $ids)->delete();
        
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

        $data = Translation::select();

        //filter published
        $value = $request->published;
        if ($request->has('published')) {
            $data = $data->where('is_published', $value);
        }

        //filter locale
        $value = $request->locale;
        if ($request->has('locale')) {
            $data = $data->where('locale', $value);
        }

        //filter translated
        $value = $request->translated;
        if ($request->has('translated')) {
            if($value == '1') {
                $data = $data->where('value', '<>', '');
            } else if($value == '0') {
                $data = $data->where(function($q){
                    $q->where('value', '');
                    $q->orWhereNull('value');
                });
            }
            
        }

        return Datatables::of($data)
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->editColumn('key', function($row) {
                return htmlspecialchars($row->key);
            })
            ->editColumn('value', function($row) {
                return view('admin.core.translations.datatables.value', compact('row'));
            })
            ->editColumn('locale', function($row) {
                return view('admin.core.translations.datatables.locale', compact('row'));
            })
            ->editColumn('is_published', function($row) {
                return view('admin.core.translations.datatables.published', compact('row'));
            })
            ->addColumn('actions', function($row) {
                return view('admin.core.translations.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Find new translations
     *
     * @param Request $request
     * @return void
     */
    public function importTranslations(Request $request) {

        $result = Translation::import2DB();

        return Redirect::back()->with('success', 'Traduções importadas com sucesso.');
    }
   
    /**
     * Find new translations
     *
     * @param Request $request
     * @return void
     */
    public function findTranslations(Request $request) {

        $result = Translation::findTranslations();

        if(empty($result)) {
            return Redirect::back()->with('success', 'Não há foram encontradas novas traduções em sistema.');
        }

        return Redirect::back()->with('success', 'Há novas traduções em sistema.');
    }

        /**
     * Find new translations
     *
     * @param Request $request
     * @return void
     */
    public function publishTranslations(Request $request) {

        try {
            Translation::publishTranslations();

            return Redirect::back()->with('success', 'Traduções publicadas com sucesso.');
        } catch(\Exception $e) {
            dd($e->getMessage());
            return Redirect::back()->with('error', 'Não foi possível publicadas os ficheiros tradução');
        }
        
    }
}
