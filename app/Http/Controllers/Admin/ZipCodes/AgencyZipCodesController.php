<?php

namespace App\Http\Controllers\Admin\ZipCodes;

use App\Models\Agency;
use App\Models\Service;
use App\Models\Provider;
use App\Models\ZipCode;
use App\Models\ZipCode\AgencyZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Setting;


class AgencyZipCodesController extends \App\Http\Controllers\Admin\Controller {

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
    public function index() {

        $agenciesList = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $sources = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->orderBy('source')
            ->pluck('source', 'source')
            ->toArray();

        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $country  = Setting::get('app_country');

        $district = null;

        $data = compact(
            'agenciesList',
            'providersList',
            'country',
            'district',
            'sources',
            'allProviders',
            'servicesList'
        );

        return $this->setContent('admin.zip_codes.agencies.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        

        $zipCode = new AgencyZipCode;

        $zipCode->provider_id = @$request->provider;

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $action = 'Adicionar Código Postal';

        $formOptions = ['route' => array('admin.zip-codes.agencies.store'), 'method' => 'POST'];

        $data = compact(
            'zipCode',
            'agencies',
            'providers',
            'services',
            'action',
            'formOptions'
        );

        return view('admin.zip_codes.agencies.edit', $data)->render();
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

        $zipCode = AgencyZipCode::filterSource()
                        ->findOrfail($id);

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $action = 'Editar Código Postal';

        $formOptions = ['route' => array('admin.zip-codes.agencies.update', $zipCode->id), 'method' => 'PUT'];

        $data = compact(
            'zipCode',
            'agencies',
            'providers',
            'services',
            'action',
            'formOptions'
        );

        return view('admin.zip_codes.agencies.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        AgencyZipCode::flushCache(AgencyZipCode::CACHE_TAG);

        $input = $request->all();
        $input['services']    = $request->get('services', null);
        $input['is_regional'] = $request->get('is_regional', false);
        
        $zipCode = AgencyZipCode::filterSource()
                        ->findOrNew($id);

        if(!$zipCode->exists) {
            $exists = AgencyZipCode::filterSource()
                ->where('zip_code', $input['zip_code'])
                ->first();

            if($exists) {
                return Redirect::back()->with('error', 'O código postal já se encontra associado a outro fornecedor ou agência.');
            }
        }

        if ($zipCode->validate($input)) {
            $zipCode->fill($input);
            $zipCode->source = config('app.source');
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

        AgencyZipCode::flushCache(AgencyZipCode::CACHE_TAG);

        $result = AgencyZipCode::filterSource()
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

        AgencyZipCode::flushCache(AgencyZipCode::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = AgencyZipCode::filterSource()
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

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $data = AgencyZipCode::with(['agency' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(Agency::CACHE_TAG);
                        $q->select(['id', 'name', 'color']);
                    }])
                    ->with(['provider' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(Provider::CACHE_TAG);
                        $q->select(['id', 'name', 'color']);
                    }])
                    ->filterSource()
                    ->select();

        //filter agency
        $value = $request->get('agency');
        if($request->has('agency')) {
            $data = $data->whereIn('agency_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            if($value == '-1') {
                $data = $data->where(function($q) use($value) {
                    $q->whereNull('provider_id');
                    $q->orWhereIn('provider_id', $value);
                });
            } else {
                $data = $data->whereIn('provider_id', $value);
            }
        }

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

        //filter regional
        $value = $request->get('regional');
        if($request->has('regional')) {
            $data = $data->where('is_regional', $value);
        }
        return Datatables::of($data)
            ->edit_column('zip_code', function($row) {
                return view('admin.zip_codes.agencies.datatables.zip_code', compact('row'))->render();
            })
            ->edit_column('agency', function($row) {
                return @$row->agency->name;
            })
            ->edit_column('country', function($row) {
                return view('admin.zip_codes.zip_codes.datatables.country', compact('row'))->render();
            })
            ->edit_column('provider_id', function($row) {
                return view('admin.zip_codes.agencies.datatables.provider', compact('row'))->render();
            })
            ->edit_column('services', function($row) use($services) {
                return view('admin.zip_codes.agencies.datatables.services', compact('row', 'services'))->render();
            })
            ->edit_column('is_regional', function($row) use($services) {
                return view('admin.zip_codes.agencies.datatables.is_regional', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.zip_codes.agencies.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function importZipCodes(Request $request) {

        AgencyZipCode::flushCache(AgencyZipCode::CACHE_TAG);

        $input = $request->all();
        $input['is_regional'] = $request->get('is_regional', false);

        if(!$request->has('zip_code')) {
            return Redirect::back()->with('error', 'Não selecionou nenhum Código Postal para associar à agência.');
        }

        $zipCodes = ZipCode::whereIn('zip_code', $input['zip_code'])
                            ->groupBy('zip_code')
                            ->get();

        foreach ($zipCodes as $zipCode) {
            $agencyZipCode = new AgencyZipCode();
            $agencyZipCode->agency_id   = $input['agency_id'];
            $agencyZipCode->zip_code    = $zipCode->zip_code;
            $agencyZipCode->city        = $zipCode->postal_designation;
            $agencyZipCode->country     = $zipCode->country;
            $agencyZipCode->zone        = $request->zone;
            $agencyZipCode->provider_id = $request->provider_id;
            $agencyZipCode->kms         = $request->kms;
            $agencyZipCode->services    = $request->services;
            $agencyZipCode->is_regional = $input['is_regional'];
            $agencyZipCode->source      = config('app.source');
            $agencyZipCode->save();
        }

        return Redirect::back()->with('success', 'Códigos postais importados com sucesso.');
    }

    /**
     * Search zip codes by given province
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {

        $country      = $request->get('country');
        $districtCode = $request->get('district');
        $countyCode   = $request->get('county');

        $usedZipCodes = AgencyZipCode::filterSource()
                            ->groupBy('zip_code')
                            ->pluck('zip_code')
                            ->toArray();

        $zipCodes = ZipCode::where('country', $country)
                        ->whereNotIn('zip_code', $usedZipCodes);

        if(!empty($districtCode)) {
            if($districtCode == 'ac' || $districtCode == 'md') {

                if($districtCode == 'md') {
                    $groupDistricts = ['31', '32'];
                } else {
                    $groupDistricts = ['41','42','43','44','45','46','47','48','49'];
                }

                $zipCodes = $zipCodes->whereIn('district_code', $groupDistricts);
            } else {
                $zipCodes = $zipCodes->where('district_code', $districtCode);
            }
        }

        if(!empty($countyCode)) {
            $zipCodes = $zipCodes->where('county_code', $countyCode);
        }

        $zipCodes = $zipCodes->groupBy('zip_code')
                             ->get();

        return view('admin.zip_codes.agencies.partials.search_result', compact('zipCodes'))->render();
    }

    /**
     * Assign same prices table to all selected customers
     * GET /admin/zip-codes/selected/mass-assign-provider
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request) {

        AgencyZipCode::flushCache(AgencyZipCode::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $update = [];
        if($request->get('assign_agency_id')) {
            $update['agency_id'] = $request->assign_agency_id;

            if($request->assign_agency_id == '-1') {
                $update['agency_id'] = null;
            }
        }

        if($request->get('assign_provider_id')) {
            $update['provider_id'] = $request->assign_provider_id;

            if($request->assign_provider_id == '-1') {
                $update['provider_id'] = null;
            }
        }

        if($request->get('assign_is_regional') != '-1') {
            $update['is_regional'] = $request->assign_is_regional;
        }

        if($request->get('kms')) {
            $update['kms'] = $request->kms;
        }

        if($request->get('assign_services')) {

            $allServices = false;
            $selectedServices = $request->assign_services;

            if(count($selectedServices) == 1 && @$selectedServices[0] == '-1') {
                $allServices = true;
            }

            if($allServices) {
                $update['services'] = null;
            } else {
                $update['services'] = json_encode($selectedServices);
            }

        }

        if(empty($update)) {
            return Redirect::back()->with('error', 'Não selecionou campos a alterar.');
        }

        $result = AgencyZipCode::filterSource()
                        ->whereIn('id', $ids)
                        ->update($update);

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível alterar os registos selecionados.');
        }

        return Redirect::back()->with('success', 'Registos selecionados associados com sucesso.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFilters(Request $request) {

        $country  = $request->get('country', Setting::get('app_country'));
        $district = $request->get('district', false);

        return view('admin.zip_codes.agencies.partials.filters', compact('country', 'district'))->render();
    }

    /**
     * Import zip codes from agency
     *
     * @return \Illuminate\Http\Response
     */
    public function importZipCodesFromAgency(Request $request) {

        AgencyZipCode::flushCache(AgencyZipCode::CACHE_TAG);

        $zipCodes = AgencyZipCode::where('source', $request->source);

        if($request->source_agency) {
            $zipCodes = $zipCodes->where('agency_id', $request->source_agency);
        }

        if($request->source_provider) {
            $zipCodes = $zipCodes->where('provider_id', $request->source_provider);
        }

        $zipCodes = $zipCodes->get();

        try {
            foreach ($zipCodes as $code) {
                $newZipCode = AgencyZipCode::firstOrNew([
                                    'source'   => $request->recipient_source,
                                    'zip_code' => $code
                                ]);

                $newZipCode->source      = $request->recipient_source;
                $newZipCode->agency_id   = $code->agency_id;
                $newZipCode->zip_code    = $code->zip_code;
                $newZipCode->city        = $code->city;
                $newZipCode->country     = $code->country;
                $newZipCode->zone        = $code->zone;
                $newZipCode->provider_id = $request->provider_id;
                $newZipCode->services    = $request->services;
                $newZipCode->is_regional = $request->is_regional;
                $newZipCode->save();
            }

            return Redirect::back()->with('success', 'Registos selecionados importados com sucesso.');

        } catch(\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
