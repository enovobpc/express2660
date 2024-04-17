<?php

namespace App\Http\Controllers\Admin\ZipCodes;


use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Auth;

class ZipCodesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'zip_codes';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',zip_codes']);
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
        
        $zipCode = new ZipCode;

        $action = 'Adicionar Código Postal';

        $formOptions = ['route' => array('admin.zip-codes.store'), 'method' => 'POST'];

        $data = compact(
            'zipCode',
            'action',
            'formOptions'
        );

        return view('admin.zip_codes.zip_codes.edit', $data)->render();
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

        $zipCode = ZipCode::findOrfail($id);

        if($zipCode->country == 'pt') {
            $zipCode->zip_code = $zipCode->zip_code.'-'.$zipCode->zip_code_extension;
        }

        $action = 'Editar Código Postal';

        $formOptions = ['route' => array('admin.zip-codes.update', $zipCode->id), 'method' => 'PUT'];

        $data = compact(
            'zipCode',
            'action',
            'formOptions'
        );

        return view('admin.zip_codes.zip_codes.edit', $data)->render();
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
        $input['zip_code'] = trim($input['zip_code']);

        $zipCode = ZipCode::findOrNew($id);
  
        if($zipCode->exists && !Auth::user()->isAdmin() && $zipCode->source != config('app.source')) {
            return Redirect::back()->with('error', 'Não está autorizado a alterar este código postal.');
        }

        if($input['country'] == 'pt') {
            $parts = str_replace(' ', '', $input['zip_code']);
            $parts = explode('-', $input['zip_code']);

            if($parts[0] < 1000 || $parts[0] > 9999 || strlen(@$parts[1]) > 3 || strlen(@$parts[1]) < 3) {
                return redirect()->back()->with('error', 'O código postal tem de ter o formato 0000-000');
            }

            $input['zip_code'] = $parts[0];
            $input['zip_code_extension'] = $parts[1];

        } elseif(!$zipCode->isValid(strtoupper($input['country']), $input['zip_code'])) {
            $format = $zipCode->getFormats(strtoupper($input['country']));
            $format = $format[0];
            $format = str_replace('@', 'A', $format);
            $format = str_replace('#', '9', $format);
            return redirect()->back()->with('error', 'Codigo postal - formato incorreto para '.strtoupper($input['country']).'. Formato correto: '.$format);
        }


        if(!$zipCode->exists) {

            $zipCodeExists = ZipCode::where('country', $input['country'])
                                ->where('zip_code', $input['zip_code']);

            if($input['country'] == 'pt') {
                $zipCodeExists = $zipCodeExists->where('zip_code_extension', $input['zip_code_extension']);
            }

            $zipCodeExists = $zipCodeExists->first();

            if($zipCodeExists) {
                return redirect()->back()->with('error', 'Codigo postal já existente');
            }
        }

        if ($zipCode->validate($input)) {
            $zipCode->fill($input);
            
            if(!Auth::user()->isAdmin()) {
                $zipCode->source = config('app.source');
            }
            
            $zipCode->created_by = Auth::user()->id;
            $zipCode->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $zipCode->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = ZipCode::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o Código Postal.');
        }

        return Redirect::back()->with('success', 'Códigos Postais removidos com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        ZipCode::flushCache(ZipCode::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = ZipCode::whereIn('id', $ids)->delete();
        
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

        $data = ZipCode::select();

        //filter country
        $value = $request->get('country');
        if($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter district
        $value = $request->get('district');
        if($request->has('district')) {

            $districtZipCodes = ZipCode::where('district_code', $value)
                                    ->groupBy('zip_code')
                                    ->pluck('zip_code')
                                    ->toArray();

            $data = $data->whereIn('zip_code', $districtZipCodes);
        }

        //filter county
        /*$value = $request->get('county');
        if($request->has('county')) {
            $data = $data->where('county', $value);
        }*/

        return Datatables::of($data)
            ->edit_column('zip_code', function($row) {
                return view('admin.zip_codes.zip_codes.datatables.zip_code', compact('row'))->render();
            })
            ->edit_column('country', function($row) {
                return view('admin.zip_codes.zip_codes.datatables.country', compact('row'))->render();
            })
            ->edit_column('district_code', function($row) {
                return view('admin.zip_codes.zip_codes.datatables.district', compact('row'))->render();
            })
            ->edit_column('county_code', function($row) {
                return view('admin.zip_codes.zip_codes.datatables.county', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.zip_codes.zip_codes.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
