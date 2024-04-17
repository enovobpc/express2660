<?php

namespace App\Http\Controllers\Admin\Webservices;

use App\Models\Customer;
use App\Models\CustomerWebservice;
use App\Models\Service;
use App\Models\Webservice\Base;
use Illuminate\Http\Request;
use App\Models\WebserviceConfig;
use App\Models\WebserviceMethod;
use App\Models\Agency;
use App\Models\PickupPoint;
use App\Models\Provider;
use Illuminate\Support\Facades\Redirect;

use Html, Response, File, Datatables, Auth;

class WebservicesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'webservices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',webservices']);
        validateModule('webservices');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->setContent('admin.webservices.global.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $webservice = new WebserviceConfig;

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        $agencies = Auth::user()->listsAgencies(false);

        $action = 'Adicionar Webservice Global';

        $formOptions = ['route' => ['admin.webservices.store'], 'method' => 'POST'];

        $data = compact(
            'webservice',
            'action',
            'formOptions',
            'providers',
            'webserviceMethods',
            'agencies'
        );

        return view('admin.webservices.global.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $webservice = WebserviceConfig::filterSource()->findOrfail($id);


        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        $services = Service::ordered()->get();

        $agencies = Auth::user()->listsAgencies(false);

        $listProviderServices = null;
        if(is_array(trans('admin/webservices.services.'.$webservice->method))) {
            $listProviderServices = [''=>''] + trans('admin/webservices.services.'.$webservice->method);
        }

        $action = 'Editar Webservice Global';

        $formOptions = ['route' => ['admin.webservices.update', $webservice->id], 'method' => 'PUT'];

        $data = compact(
            'webservice',
            'action',
            'formOptions',
            'providers',
            'webserviceMethods',
            'agencies',
            'services',
            'listProviderServices'
        );

        return view('admin.webservices.global.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        WebserviceConfig::flushCache(WebserviceConfig::CACHE_TAG);
        CustomerWebservice::flushCache(CustomerWebservice::CACHE_TAG);

        $input = $request->all();

        $input['active']      = $request->get('active', false);
        $input['auto_enable'] = $request->get('auto_enable', false);

        $webservice         = WebserviceConfig::filterSource()->findOrNew($id);
        $originalWebservice = clone $webservice;

        if ($webservice->validate($input)) {
            $webservice->fill($input);
            $webservice->source = config('app.source');
            $webservice->save();

            $customers = Customer::filterSource();

            if ($input['agency_id']) {
                $customers = $customers->where('agency_id', $input['agency_id']);
            }

            $customers = $customers->get();

            foreach ($customers as $customer) {
                $customerWebservice = CustomerWebservice::firstOrNew([
                    'customer_id' => $customer->id,
                    'provider_id' => $input['provider_id'],
                    'method'      => $input['method']
                ]);

                /**
                 * Only change customer webservice
                 * if the customer webservice credentials were never changed
                 */
                $changeCredentials = true;
                if ($originalWebservice->exists && $customerWebservice->exists) {
                    if ($customerWebservice->agency != $originalWebservice->agency || $customerWebservice->user != $originalWebservice->user || $customerWebservice->password != $originalWebservice->password || $customerWebservice->session_id != $originalWebservice->session_id) {
                        $changeCredentials = false;
                    }
                }
                
                if ($changeCredentials) {
                    $customerWebservice->user       = $input['user'];
                    $customerWebservice->password   = $input['password'];
                    $customerWebservice->session_id = $input['session_id'];
                    $customerWebservice->agency     = $input['agency'];
                }

                $customerWebservice->customer_id  = $customer->id;
                $customerWebservice->provider_id  = $input['provider_id'];
                $customerWebservice->method       = $input['method'];
                $customerWebservice->endpoint     = @$input['endpoint'];
                $customerWebservice->active       = $input['active'];
                $customerWebservice->force_sender = @$input['force_sender'];
                $customerWebservice->settings     = @$input['settings'];
                $customerWebservice->save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $webservice->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $result = WebserviceConfig::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o webservice global.');
        }

        return Redirect::back()->with('success', 'Webservice global removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $result = WebserviceConfig::filterSource()
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
    public function datatable(Request $request)
    {

        $data = WebserviceConfig::filterSource()
            ->with('provider', 'source_agency', 'webservice_method')
            ->select();

        return Datatables::of($data)
            ->edit_column('provider_id', function ($row) {
                return @$row->provider->name;
            })
            ->edit_column('agency_id', function ($row) {
                if ($row->agency_id) {
                    return @$row->source_agency->name;
                } else {
                    return 'Todas';
                }
            })
            ->edit_column('method', function ($row) {
                return view('admin.webservices.global.datatables.method', compact('row'))->render();
            })
            ->edit_column('force_sender', function ($row) {
                return view('admin.webservices.global.datatables.force_sender', compact('row'))->render();
            })
            ->edit_column('auto_enable', function ($row) {
                return view('admin.webservices.global.datatables.auto_enable', compact('row'))->render();
            })
            ->edit_column('session_id', function ($row) {
                return view('admin.webservices.global.datatables.session_id', compact('row'))->render();
            })
            ->edit_column('active', function ($row) {
                return view('admin.webservices.global.datatables.active', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.webservices.global.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Edit webservice connections
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editWebservices()
    {

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code')
            ->get());

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')->toArray();

        $compact = compact(
            'agencies',
            'webserviceMethods',
            'providers'
        );

        return view('admin.webservices.global.mass_config', $compact)->render();
    }

    /**
     * Store new webservice
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeWebservices(Request $request)
    {

        $input = $request->all();

        if ($input['action'] == 'create') {

            $customers = Customer::where('agency_id', $input['agency_id'])->get();

            foreach ($customers as $customer) {

                $webservice = CustomerWebservice::firstOrNew([
                    'customer_id' => $customer->id,
                    'method'      => $input['method'],
                    'provider_id' => $input['provider_id'],
                ]);
                $webservice->customer_id = $customer->id;
                $webservice->agency      = $input['agency'];
                $webservice->user        = empty($input['user']) ? $customer->code : $input['user'];
                $webservice->password    = $input['password'];
                $webservice->session_id  = $input['session_id'];
                $webservice->endpoint    = @$input['endpoint'];
                $result = $webservice->save();

                $customer->has_webservices = true;
                $customer->save();
            }

            return Redirect::back()->with('success', 'Métodos adicionados com sucesso.');
        } else {

            $customers = Customer::withTrashed()
                ->where('agency_id', $input['agency_id'])
                ->get();

            foreach ($customers as $customer) {

                $webservice = CustomerWebservice::where('customer_id', $customer->id)
                    ->where('method', $input['method']);

                if ($input['provider_id']) {
                    $webservice->where('provider_id', $input['provider_id']);
                }

                $result = $webservice->forceDelete();
            }

            return Redirect::back()->with('success', 'Métodos eliminados com sucesso.');
        }
    }

    /**
     * Sincronize shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncHistory(Request $request)
    {

        $ids = $request->get('ids', null);
        if (!empty($ids)) {
            $ids = explode(',', $ids);
        }

        try {
            $webservice = new Base();
            $webservice->syncShipmentsHistory(null, null, null, $ids);

            $result = [
                'result'    => true,
                'feedback'  => 'Sincronização com sucesso.'
            ];
        } catch (\Exception $e) {
            $result = [
                'result' => false,
                'feedback' => $e->getMessage()
            ];
        }

        return Response::json($result);
    }

    /**
     * Open modal to sync shipments
     *
     * @return mixed|null|string
     */
    public function editSyncShipment()
    {

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        $data = compact('webserviceMethods');

        return view('admin.webservices.sync.shipments', $data)->render();
    }

    /**
     * Sincronize shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncShipments(Request $request)
    {

        $startDate = $endDate = date('Y-m-d');

        if ($request->has('start_date')) {
            $startDate = $request->start_date;
        }

        if ($request->has('end_date')) {
            $endDate = $request->end_date;
        }

        $date = [$startDate, $endDate];

        $method = null;
        if ($request->has('webservice')) {
            $method = $request->webservice;
        }

        $customer = null;
        if ($request->has('customer')) {
            $customer = $request->customer;
        }

        $syncAllCustomers = $request->get('onlyActive', false);
        $syncAllCustomers = $syncAllCustomers == 'true' ? true : false;
        $syncAllCustomers = !$syncAllCustomers;

        $webservice = new Base();
        $result = $webservice->syncShipments($date, $method, $customer, false, null, null, $syncAllCustomers);

        return Response::json($result);
    }

    /**
     * Get all Pickup Points Delivery over (PuDo)
     *
     * @return json 
     */
    public function syncPudos(Request $request)
    {
        $method = $request->has('provider_id') ? $request->provider_id : null;
        $provider = Provider::filterSource()->findOrFail($method);
        try {
            $webservice = new \App\Models\Webservice\Base();
            $data = $webservice->getPickupPoints($provider->webservice_method);
            if (!empty($data['points'])) {
                foreach ($data['points'] as $key => $point) {
                    $pickupPoint = PickupPoint::firstOrNew([
                        'provider_id' => $provider->id,
                        'provider_code' => $point['code'],
                    ]);

                    $pickupPoint->fill($point);
                    $pickupPoint->provider_code = $point['code'];
                    $pickupPoint->source = config('app.source');
                    $pickupPoint->is_active = true;
                    $pickupPoint->save();
                }
            } else {
                return Redirect::back()->with('error', 'Sem pontos de recolha.');
            }
        } catch (\Exception $e) {
            return [
                'result'   => false,
                'points'   => [],
                'feedback' => $e->getMessage(),
            ];
        }

        return Redirect::back()->with('sucess', 'Pontos de recolha carregados.');
    }
}
