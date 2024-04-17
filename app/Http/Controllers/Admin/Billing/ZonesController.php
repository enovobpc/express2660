<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Models\PackType;
use App\Models\Service;
use Html, Cache, Response, Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\ZipCode;
use App\Models\BillingZone;

class ZonesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing-zones';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',billing-zones']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.billing.zones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $billingZone = new BillingZone;
        $billingZone->unity = 'zip_code';

        $country  = Setting::get('app_country');
        $district = null;

        $action = 'Adicionar Zona de Faturação';

        $formOptions = array('route' => array('admin.billing.zones.store'), 'method' => 'POST');

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $compact = compact(
            'billingZone',
            'country',
            'district',
            'action',
            'formOptions',
            'packTypes'
        );

        return view('admin.billing.zones.edit', $compact)->render();
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

        $billingZone = BillingZone::filterSource()->findOrfail($id);

        //$billingZone->country = $billingZone->mapping;
        if($billingZone->unity == 'zip_code' || $billingZone->unity == 'pack_zip_code') {
            //$billingZone->country   = null;
            $billingZone->zip_codes = implode(",", $billingZone->mapping);
        } elseif(($billingZone->unity == 'matrix' || $billingZone->unity == 'pack_matrix') && is_array($billingZone->mapping)) {
            $billingZone->zip_codes = implode(",\n", $billingZone->mapping);
        }

        $country = Setting::get('app_country');
        $district = null;

        $action = 'Editar Zona de Faturação';

        $formOptions = array('route' => array('admin.billing.zones.update', $billingZone->id), 'method' => 'PUT');

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $compact = compact(
            'billingZone',
            'country',
            'district',
            'action',
            'formOptions',
            'packTypes'
        );

        return view('admin.billing.zones.edit', $compact)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        BillingZone::flushCache(BillingZone::CACHE_TAG);
        
        $input = $request->all();
        $input['code'] = strtolower($input['code']);
        $input['zip_codes'] = explode(',', @$input['zip_codes']);
        $input['country'] = @$input['zone_country'];

        $input['mapping'] = @$input['countries'];

        if($input['unity'] == 'zip_code' || $input['unity'] == 'pack_zip_code') {
            $input['mapping'] = $input['zip_codes'];
        } elseif($input['unity'] == 'pack_type') {
            $input['mapping'] = $input['pack_types'];
        }

        if(empty($input['mapping'])) {
            $input['mapping'] = [Setting::get('app_country')];
        }

        if(!empty(@$input['matrix']['origins']) && in_array($input['unity'], ['matrix', 'pack_matrix'])) {

            //limpa caixas de input vazias e organiza o array
            $matrix = [];
            $origins = array_filter(@$input['matrix']['origins']);
            foreach ($origins as $key => $origin) {
                $destValues = @$input['matrix']['destinations'][$key];
                $direction  = @$input['matrix']['dir'][$key];

                if ($destValues) {
                    $destValues = explode(',',str_replace(' ','', $destValues));
                    $destValues = array_filter($destValues);
                    $destValues = implode(',', $destValues);

                    $matrix['origins'][] = $origin;
                    $matrix['dir'][] = $direction;
                    $matrix['destinations'][] = $destValues;
                }
            }
            $input['matrix'] = $matrix;

            //prepara os dados da variavel mapping
            if(!empty($matrix)) {
                foreach ($matrix['origins'] as $rowId => $origins) {

                    $originZipCodes = explode(',', $origins);
                    foreach ($originZipCodes as $originZipCode) {
                        if(!empty($originZipCode)) {
                            $originZipCode = trim($originZipCode);

                            //se só tem 1 codigo postal, converte em range de codigo postal
                            if (!str_contains($originZipCode, '-')) {
                                $originZipCode = $originZipCode . '-' . $originZipCode;
                            }

                            //percorre cada destino
                            $destZipCodes = $matrix['destinations'][$rowId];
                            $destZipCodes = explode(',', $destZipCodes);
                            $direction    = $matrix['dir'][$rowId];
                            foreach ($destZipCodes as $destZipCode) {
                                if (!empty($destZipCode)) {
                                    $destZipCode = trim($destZipCode);
                                    if (!str_contains($destZipCode, '-')) {
                                        $destZipCode = $destZipCode . '-' . $destZipCode;
                                    }

                                    $arr[] = $originZipCode . $direction . $destZipCode;
                                }
                            }
                        }
                    }
                }

                $arr = array_unique($arr);
                $input['mapping'] = $arr;
            }
        }

        $billingZone = BillingZone::filterSource()->findOrNew($id);

        $oldZipCodes = $billingZone->mapping;

        if ($billingZone->validate($input)) {
            $billingZone->fill($input);
            $billingZone->source = config('app.source');
            $billingZone->save();

            //verifica todos os serviços que tenham esta zona e atualiza os seus códigos postais.
            if($billingZone->unity == 'zip_code') {

                Service::flushCache(Service::CACHE_TAG);
                $services = Service::where('zones', 'LIKE', '%"'.strtolower($billingZone->code).'"%')->get();

                foreach ($services as $service) {

                    //Remove no serviço os codigos postais antigos do serviço
                    $zipCodes = $service->zip_codes ? explode(',', $service->zip_codes) : [];
                    $excludeZipCodes = array_diff($zipCodes, $oldZipCodes);

                    $service->zip_codes = implode(',', $excludeZipCodes);
                    $service->save();

                    $billingZones = BillingZone::whereIn('code', $service->zones)->get();
                    $zipCodes = Service::getZipCodesFromZones($billingZones, $service->zip_codes);
                    $zipCodes = implode(',', $zipCodes);

                    $service->zip_codes = $zipCodes;
                    $service->save();
                }
            }

            return Redirect::route('admin.billing.zones.index')->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::route('admin.billing.zones.index')->withInput()->with('error', $billingZone->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        BillingZone::flushCache(BillingZone::CACHE_TAG);

        $result = BillingZone::filterSource()
                            ->whereId($id)
                            ->delete();

        if (!$result) {
            return Redirect::route('admin.billing.zones.index')->with('error', 'Ocorreu um erro ao tentar remover a zona de faturação.');
        }

        return Redirect::route('admin.billing.zones.index')->with('success', 'Zona de faturação removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        BillingZone::flushCache(BillingZone::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = BillingZone::whereSource(config('app.source'))
                        ->whereIn('id', $ids)
                        ->delete();
        
        if (!$result) {
            return Redirect::route('admin.billing.zones.index')->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::route('admin.billing.zones.index')->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function replicate(Request $request, $id) {

        $originalBillingZone = BillingZone::filterSource()->findOrfail($id);

        $billingZone = $originalBillingZone->replicate();
        $billingZone->code = '';
        $billingZone->name = '';
        $billingZone->save();

        return redirect()->route('admin.billing.zones.index', ['zoneid' => $billingZone->id])->with('success', 'Zona duplicada com sucesso.');
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = BillingZone::filterSource()->select();
        
        //filter type
        $value = $request->unity;
        if($request->has('unity')) {
            $data = $data->where('unity', $value);
        }

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return $row->code ? strtoupper($row->code) : '<i class="fas fa-exclamation-triangle text-red"></i>';
            })
            ->edit_column('name', function($row) {
                return view('admin.billing.zones.datatables.name', compact('row'))->render();
            })
            ->edit_column('country', function($row) {
                return view('admin.billing.zones.datatables.country', compact('row'))->render();
            })
            ->edit_column('unity', function($row) {
                return view('admin.billing.zones.datatables.unity', compact('row'))->render();
            })
            ->edit_column('mapping', function($row) {
                return view('admin.billing.zones.datatables.mapping', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.billing.zones.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search zip codes by given province
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchZipCodes(Request $request) {

        $country      = $request->get('country');
        $districtCode = $request->get('district');
        $countyCode   = $request->get('county');

        $zipCodes = ZipCode::where('country', $country);

        if(!empty($districtCode)) {
            $zipCodes = $zipCodes->where('district_code', $districtCode);
        }

        if(!empty($countyCode)) {
            $zipCodes = $zipCodes->where('county_code', $countyCode);
        }

        $zipCodes = $zipCodes->groupBy('zip_code')->get();

        return view('admin.zip_codes.agencies.partials.search_result', compact('zipCodes'))->render();
    }
}
