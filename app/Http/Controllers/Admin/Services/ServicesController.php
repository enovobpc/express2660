<?php

namespace App\Http\Controllers\Admin\Services;

use App\Models\BillingZone;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Agency;
use App\Models\Billing\Item;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ServiceGroup;
use App\Models\TransportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Response, Setting, Croppa, File;

class ServicesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'services';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',services']);
        validateModule('services');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $agencies = Auth::user()->listsAgencies();

        $billingZonesCollection = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->get();

        $billingZones = $billingZonesCollection->groupBy('unity');

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $pickupServices = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->isCollection()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $servicesGroups = ServiceGroup::remember(config('cache.query_ttl'))
            ->cacheTags(ServiceGroup::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $types      = trans('admin/global.services.unities.selectbox');
        $features   = trans('admin/global.services.features');
        $hours      = listHours(5);

        $data = compact(
            'agencies',
            'providers',
            'billingZones',
            'billingZonesCollection',
            'types',
            'features',
            'hours',
            'pickupServices',
            'servicesGroups'
        );

        return $this->setContent('admin.services.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($service = null) {

        if (!$service) {
            $service = new Service;

            $service->custom_prices     = 1;
            $service->pickup_weekdays   = [1,2,3,4,5,6];
            $service->delivery_weekdays = [1,2,3,4,5,6];
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->get();

        $billingZonesList = $billingZones->pluck('name', 'code')->toArray();

        $billingZones = $billingZones->groupBy('unity');

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $pickupServices = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->isCollection()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $provider = Provider::filterSource()
            ->whereIn('id', array_keys($providers))
            ->first();

        $servicesGroups = ServiceGroup::remember(config('cache.query_ttl'))
            ->cacheTags(ServiceGroup::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $servicesTypes = TransportType::remember(config('cache.query_ttl'))
            ->cacheTags(TransportType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $billingItems = Item::remember(config('cache.query_ttl'))
            ->cacheTags(Item::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];  

        $action = 'Adicionar Serviço';

        $formOptions = array('route' => array('admin.services.store'), 'method' => 'POST', 'files' => true);

        $vatRates    = Invoice::getVatTaxes();
        $types       = trans('admin/global.services.unities.selectbox');
        $priorLevels = trans('admin/global.services.priorities-levels');
        $hours       = listHours(5);
        $customers   = [];
        $assignedCustomers = [];
        $country     = Setting::get('app_country');
        $country     = in_array($country, ['ptmd', 'ptac']) ? 'pt' : $country;
        $district    = null;

        $data = compact(
            'service',
            'action',
            'formOptions',
            'agencies',
            'types',
            'providers',
            'billingZones',
            'provider',
            'servicesGroups',
            'hours',
            'vatRates',
            'pickupServices',
            'priorLevels',
            'customers',
            'assignedCustomers',
            'country',
            'district',
            'billingZonesList',
            'servicesTypes',
            'billingItems'
        );

        return view('admin.services.edit', $data)->render();
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

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->get();

        $pickupServices = $services->filter(function($item) use ($id) {
            return $item->is_collection == 1;
        })->pluck('name', 'id')->toArray();

        $service = $services->filter(function($item) use ($id) {
            return $item->id == $id;
        })->first();

        $services = $services->pluck('name', 'id')->toArray();

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->get();

        $billingZonesList = $billingZones->pluck('name', 'code')->toArray();

        $billingZones = $billingZones->groupBy('unity');

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $provider = Provider::filterSource()
            ->whereIn('id', array_keys($providers))
            ->first();

        $servicesGroups = ServiceGroup::remember(config('cache.query_ttl'))
            ->cacheTags(ServiceGroup::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $servicesTypes = TransportType::remember(config('cache.query_ttl'))
            ->cacheTags(TransportType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $billingItems = Item::remember(config('cache.query_ttl'))
            ->cacheTags(Item::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];  

        $assignedCustomers = array_map('intval', ($service->customers ? $service->customers : []));
        $customers = [];
        if($service->customers) {
            $customers = Customer::remember(config('cache.query_ttl'))
                ->cacheTags(BillingZone::CACHE_TAG)
                ->whereIn('id', $assignedCustomers)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();
        }

        $action = 'Editar Serviço';

        $formOptions = array('route' => array('admin.services.update', $service->id), 'method' => 'PUT', 'files' => true);

        $vatRates    = Invoice::getVatTaxes();
        $types       = trans('admin/global.services.unities.selectbox');
        $priorLevels = trans('admin/global.services.priorities-levels');
        $hours       = listHours(5);
        $country     = Setting::get('app_country');
        $country     = in_array($country, ['ptmd', 'ptac']) ? 'pt' : $country;
        $district    = null;

        $data = compact(
            'service',
            'action',
            'formOptions',
            'agencies',
            'types',
            'providers',
            'billingZones',
            'services',
            'servicesGroups',
            'hours',
            'pickupServices',
            'priorLevels',
            'customers',
            'assignedCustomers',
            'country',
            'district',
            'billingZonesList',
            'provider',
            'vatRates',
            'servicesTypes',
            'billingItems'
        );

        return view('admin.services.edit', $data)->render();
    }

    /**
     * Show form to duplicate service
     * 
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Service $service) {
        return $this->create($service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $redirectBack = true) {

        Service::flushCache(Service::CACHE_TAG);

        $service = Service::filterAgencies()
            ->findOrNew($id);

        $input = $request->all();
        $input['is_collection']       = $request->get('is_collection', false);
        $input['is_import']           = $request->get('is_import', false);
        $input['is_return']           = $request->get('is_return', false);
        $input['is_mail']             = $request->get('is_mail', false);
        $input['is_internacional']    = $request->get('is_internacional', false);
        $input['is_maritime']         = $request->get('is_maritime', false);
        $input['is_air']              = $request->get('is_air', false);
        $input['is_courier']          = $request->get('is_courier', false);
        $input['is_regional']         = $request->get('is_regional', false);
        $input['custom_prices']       = $request->get('custom_prices', false);
        $input['price_per_volume']    = $request->get('price_per_volume', false);
        $input['price_per_pack']      = $request->get('price_per_pack', false);
        $input['multiply_price']      = $request->get('multiply_price', false);
        $input['dimensions_required'] = $request->get('dimensions_required', false);
        $input['allow_kms']           = $request->get('allow_kms', false);
        $input['allow_docs']          = $request->get('allow_docs', false);
        $input['allow_boxes']         = $request->get('allow_boxes', false);
        $input['allow_pallets']       = $request->get('allow_pallets', false);
        $input['allow_out_standard']  = $request->get('allow_out_standard', false);
        $input['allow_pudos']         = $request->get('allow_pudos', false);
        $input['pickup_weekdays']     = $request->get('pickup_weekdays', []);
        $input['delivery_weekdays']   = $request->get('delivery_weekdays', []);
        $input['customers']           = $request->get('customers');
        /*$input['matrix_arr']          = null;
        $input['matrix_from']         = array_filter($input['matrix_from']);
        $input['matrix_to']           = array_filter($input['matrix_to']);*/
        $input['priority_color']      = @$input['priority_level'] ? trans('admin/global.services.priorities-colors.'.$input['priority_level']) : null;
        @$input['zones']              = empty(@$input['zones']) ? [Setting::get('app_country')] : @$input['zones'];

        //serviço por tipo de embalagem
        $billingZonesUnities = BillingZone::whereIn('code', @$input['zones'])->pluck('unity')->toArray();
        if(in_array('pack_type', $billingZonesUnities) || in_array('pack_zip_code', $billingZonesUnities) || in_array('pack_matrix', $billingZonesUnities)) {
            $input['price_per_pack'] = true;
        } else {
            $input['price_per_pack'] = false;
        }

        $input['zones_provider']    = $request->get('zones_provider', []);
        //dd($input['zones_provider']);
        $providerZones = !empty($service->zones_provider) ? array_filter($service->zones_provider) : [];
        unset($providerZones[@$input['zone_provider_id']]); //apaga as zonas do fornecedor que se está a gravar para inserir de limpo se for o caso


        if(!empty($providerZones)) {
            $providerZones+= $input['zones_provider'];
            $input['zones_provider'] = $providerZones;
        }

        $input['webservice_mapping']    = $request->get('webservice_mapping', []);
        $webservicesMapping = !empty($service->webservice_mapping) ? array_filter($service->webservice_mapping) : [];
        unset($webservicesMapping[@$input['zone_provider_id']]); //apaga as zonas do fornecedor que se está a gravar para inserir de limpo se for o caso

        if(!empty($webservicesMapping)) {
            $webservicesMapping+= $input['webservice_mapping'];
            $input['webservice_mapping'] = $webservicesMapping;
        }


        //Matriz de distancias deve ser no formato:
        /*
         [
            origem1 => [
                destino1 => zona,
                destino2 => zona
                destino3 => zona
            ],
            origem2 => [
                destino1 => zona,
                destino2 => zona
                destino3 => zona
            ]
         ]
        */
        /*if(!empty($input['matrix_from'])) {
            foreach ($input['matrix_from'] as $key => $from) {
                $to = $input['matrix_to'][$key];
                $fromArrs = explode(',', $from);
                $toArrs   = explode(',', $to);

                $combinations = combinations([$fromArrs, $toArrs]);

                foreach ($combinations as $combination) {
                    if(@$input['matrix_zones'][$key]) { //so adiciona se tiver zona
                        $from = strval(@$combination[0]);
                        $to   = strval(@$combination[1]);
                        $input['matrix_arr'][$from][$to] = $input['matrix_zones'][$key];
                        //dd($input['matrix_arr']);
                    }
                }
            }

            //dd($input['matrix_arr']);
        }*/

        //get all selected zones
        $billingZones = BillingZone::whereIn('code', @$input['zones'])->get();
        $input['zip_codes'] = Service::getZipCodesFromZones($billingZones, @$input['zip_codes']);
        $input['zip_codes'] = implode(',', $input['zip_codes']);

        if ($service->validate($input)) {
            $service->fill($input);

            //delete image
            if (@$input['delete_photo'] && !empty($service->filepath)) {
                Croppa::delete($service->filepath);
                $service->filepath = null;
                $service->filename = null;
            }

            //upload image
            if($request->hasFile('image')) {
                if ($service->exists && !empty($service->filepath) && File::exists(public_path(). '/'.$service->filepath)) {
                    Croppa::delete($service->filepath);
                }

                if (!$service->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem do serviço.');
                }
            }

            $service->save();

            if($redirectBack) {
                return Redirect::back()->with('success', 'Dados gravados com sucesso.');
            } else {
                return [
                    'result'   => true,
                    'feedback' => 'Dados gravados com sucesso.',
                    'service'  => $service
                ];
            }

        }

        if($redirectBack) {
            return Redirect::back()->withInput()->with('error', $service->errors()->first());
        } else {
            return [
                'result'   => false,
                'feedback' => $service->errors()->first(),
                'service'  => $service
            ];
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Service::flushCache(Service::CACHE_TAG);

        $result = Service::filterAgencies()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o serviço.');
        }

        return Redirect::route('admin.services.index')->with('success', 'Serviço removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Service::flushCache(Service::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Service::whereIn('id', $ids)
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

        $data = Service::filterAgencies()
                    ->with('provider')
                    ->select();
        
        $agencies = Agency::get(['id','code', 'name', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();

        //filter group
        $value = $request->group;
        if($request->has('group')) {
            $data = $data->where('group', $value);
        }

        //filter unity
        $value = $request->unity;
        if($request->has('unity')) {
            $data = $data->where('unity', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if(!empty($value)) {
            $data = $data->whereIn('provider_id', $value);
        }

        //filter zone
        $value = $request->get('zone');
        if(!empty($value)) {
            $data = $data->where(function ($q) use($value) {
                foreach ($value as $item) {
                    $q->orWhere('zones', 'LIKE', '%"'.$item.'"%');
                }
            });
        }

        //filter agencies
        $value = $request->get('agency');
        if(!empty($value)) {
            $data = $data->where(function ($q) use($value) {
                foreach ($value as $item) {
                    $q->orWhere('agencies', 'LIKE', '%"'.$item.'"%');
                }
            });
        }

        //filter features
        $values = $request->get('feature');
        if(!empty($values)) {
            foreach($values as $key) {
                $data = $data->where($key, 1);
            };
        }


        return Datatables::of($data)
                ->edit_column('agencies', function($row) use ($agencies) {
                    return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
                })
                ->edit_column('display_code', function($row) {
                    return view('admin.services.datatables.code', compact('row'))->render();
                })
                ->edit_column('name', function($row) {
                    return view('admin.services.datatables.name', compact('row'))->render();
                })
                ->edit_column('unity', function($row) {
                    return view('admin.services.datatables.unity', compact('row'))->render();
                })
                ->edit_column('provider_id', function($row) {
                    return view('admin.services.datatables.provider', compact('row'))->render();
                })
                ->edit_column('min_hour', function($row) {
                    return view('admin.services.datatables.horary', compact('row'))->render();
                })
                ->edit_column('max_weight', function($row) {
                    return view('admin.services.datatables.max_weight', compact('row'))->render();
                })
                ->edit_column('transit_time', function($row) {
                    return view('admin.services.datatables.transit_time', compact('row'))->render();
                })
                ->edit_column('customers', function($row) {
                    return view('admin.services.datatables.customers', compact('row'))->render();
                })
                ->edit_column('group', function($row) {
                    return @$row->serviceGroup->name;
                })
                ->edit_column('custom_prices', function($row) {
                    return view('admin.services.datatables.visible', compact('row'))->render();
                })
                ->add_column('features', function($row) {
                    return view('admin.services.datatables.features', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.services.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = Service::filterAgencies()
                            ->orderBy('sort')
                            ->get(['id', 'name']);

        $route = route('admin.services.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        Service::flushCache(Service::CACHE_TAG);

        try {
            Service::setNewOrder($request->ids);
            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request) {

        Service::flushCache(Service::CACHE_TAG);

        $input = $request->all();
        $ids = explode(',', $request->ids);

        $update = [];

        if(!empty($input['min_hour']) && !empty($input['max_hour'])) {
            $update['min_hour'] = $input['min_hour'];
            $update['max_hour'] = $input['max_hour'];
        }

        if(!empty($input['assign_provider_id'])) {
            if($input['assign_provider_id'] == '-1') {
                $input['assign_provider_id'] = null;
            }
            $update['provider_id'] = $input['assign_provider_id'];
        }

        if(!empty($input['assign_zones'])) {
            $update['zones'] = json_encode($input['assign_zones']);
        }

        if(!empty($input['assign_unity'])) {
            $update['unity'] = $input['assign_unity'];
        }

        if(!empty($input['assign_group'])) {
            $update['group'] = $input['assign_group'];
        }

        if(!empty($input['pickup_weekdays'])) {
            $update['pickup_weekdays'] = json_encode($input['pickup_weekdays']);
        }

        if(!empty($input['delivery_weekdays'])) {
            $update['delivery_weekdays'] = json_encode($input['delivery_weekdays']);
        }

        if(!empty($input['assign_service_id'])) {
            $update['assigned_service_id'] = $input['assign_service_id'];
        }

        if (!empty($input['pickup_hour_difference'])) {
            $update['pickup_hour_difference'] = $input['pickup_hour_difference'];
        }

        if($update) {
            $result = Service::whereIn('id', $ids)->update($update);

            if (!$result) {
                return Redirect::back()->with('error', 'Não foi possível atualizar os registos selecionados');
            }
            return Redirect::back()->with('success', 'Registos atualizados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', 'Não selecionou nenhum campo para atualizar.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massReplicate(Request $request) {

        Service::flushCache(Service::CACHE_TAG);

        $input = $request->all();
        $ids = explode(',', $request->ids);

        $services = Service::whereIn('id', $ids)->get();

        if(!$services) {
            return Redirect::back()->withInput()->with('error', 'Não selecionou nenhum serviço para duplicar.');
        }

        foreach ($services as $service) {

            $newService = $service->replicate();

            if(!empty($input['code'])) {
                $newService->display_code = $input['code'];
            }

            if(!empty($input['name'])) {
                $newService->name = $input['name'];
            }

            if(!empty($input['source'])) {
                $newService->source = $input['source'];
            }

            if(!empty($input['agencies'])) {
                $newService->agencies = $input['agencies'];
            }

            $newService->save();
        }

        return Redirect::back()->with('success', 'Registos duplicados com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function providerDetails(Request $request, $serviceId) {

        $providerId = $request->get('providerId');

        //dd($request->toArray());
        $this->update($request, $serviceId, false); //grava os dados

        $service  = Service::filterSource()->find($serviceId);

        $provider = Provider::filterSource()->findOrFail($providerId);

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->get();

        $billingZonesList = $billingZones->pluck('name', 'code')->toArray();

        $billingZones = $billingZones->groupBy('unity');

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'provider',
            'providers',
            'service',
            'billingZones',
            'billingZonesList'
        );

        return view('admin.services.partials.provider_options', $data)->render();
    }
}
