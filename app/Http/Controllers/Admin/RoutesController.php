<?php

namespace App\Http\Controllers\Admin;

use Response, DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Provider;
use App\Models\User;
use App\Models\Route;
use App\Models\Agency;
use App\Models\Service;
use App\Models\RouteGroup;

class RoutesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'routes';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',routes']);
        validateModule('routes');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $agencies = Agency::remember(config('cache.query_ttl'))
                        ->cacheTags(Agency::CACHE_TAG)
                        ->whereSource(config('app.source'))
                        ->pluck('name', 'id')
                        ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
                        ->cacheTags(User::CACHE_TAG)
                        ->filterAgencies()
                        ->ignoreAdmins()
                        ->orderBy('source', 'asc')
                        ->orderBy('code', 'asc')
                        ->orderBy('name', 'asc')
                        ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $providers = Provider::remember(config('cache.query_ttl'))
                        ->cacheTags(Provider::CACHE_TAG)
                        ->filterAgencies()
                        ->isCarrier()
                        ->ordered()
                        ->pluck('name', 'id')
                        ->toArray();

        return $this->setContent('admin.routes.index', compact('operators', 'providers', 'agencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $route = new Route();

        $agencies = Agency::remember(config('cache.query_ttl'))
                        ->cacheTags(Agency::CACHE_TAG)
                        ->filterSource()
                        ->get();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
                        ->cacheTags(User::CACHE_TAG)
                        ->filterAgencies()
                        ->ignoreAdmins()
                        ->orderBy('source', 'asc')
                        ->orderBy('code', 'asc')
                        ->orderBy('name', 'asc')
                        ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

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
            ->toArray() ?? [];

        $routesGroups = RouteGroup::remember(config('cache.query_ttl'))
            ->cacheTags(RouteGroup::CACHE_TAG)
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $hours = listHours(5);

        $action = 'Adicionar Rota';

        $formOptions = array('route' => array('admin.routes.store'), 'method' => 'POST');

        $data = compact(
            'route',
            'action',
            'formOptions',
            'agencies',
            'operators',
            'providers',
            'services',
            'hours',
            'routesGroups'
        );

        return view('admin.routes.edit', $data)->render();
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

        $route = Route::findOrfail($id);

        $route->zip_codes_str = !empty($route->zip_codes) ? implode(',', $route->zip_codes) : '';

        $agencies = Agency::remember(config('cache.query_ttl'))
                    ->cacheTags(Agency::CACHE_TAG)
                    ->filterSource()
                    ->get();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
                    ->cacheTags(User::CACHE_TAG)
                    ->filterAgencies()
                    ->ignoreAdmins()
                    ->orderBy('source', 'asc')
                    ->orderBy('code', 'asc')
                    ->orderBy('name', 'asc')
                    ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

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
            ->toArray() ?? [];

        $hours = listHours(5);

        $routesGroups = RouteGroup::remember(config('cache.query_ttl'))
            ->cacheTags(RouteGroup::CACHE_TAG)
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $action = 'Editar Rota';

        $formOptions = array('route' => array('admin.routes.update', $route->id), 'method' => 'PUT');

        $data = compact(
            'route',
            'action',
            'formOptions',
            'agencies',
            'operators',
            'providers',
            'services',
            'hours',
            'routesGroups'
        );

        return view('admin.routes.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Route::flushCache(Route::CACHE_TAG);

        $input = $request->all();
        $input['zip_codes'] = explode(',', @$input['zip_codes']);
        $input['zip_codes'] = array_map('trim', $input['zip_codes']);
        $input['zip_codes'] = array_filter($input['zip_codes']);
        $input['services']  = $request->get('services');

        $route = Route::findOrNew($id);

        if ($route->validate($input)) {
            $schedules = [];
            for ($i = 0; $i < count($input['schedules']['min_hour'] ?? []); $i++) {
                $minHour  = @$input['schedules']['min_hour'][$i];
                $maxHour  = @$input['schedules']['max_hour'][$i];
                $operator = @$input['schedules']['operator'][$i];
                $provider = @$input['schedules']['provider'][$i];

                if (empty($minHour) || empty($maxHour)) {
                    continue;
                }

                $schedules[] = [
                    'min_hour' => $minHour,
                    'max_hour' => $maxHour,
                    'operator' => $operator,
                    'provider' => $provider,
                ];
            }

            $input['schedules'] = $schedules;

            $route->fill($input);
            $route->source = config('app.source');
            $route->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $route->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Route::flushCache(Route::CACHE_TAG);

        $route = Route::with('customers')
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->find($id);

        if($route->customers->count() > 0) {
            return Redirect::back()->with('error', 'Não é possível eliminar a rota porque existem clientes associados. Associe primeiro os clientes a outra rota antes de eliminar a rota.');
        }

        $result = $route->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a rota');
        }

        return Redirect::route('admin.routes.index')->with('success', 'Rota removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Route::flushCache(Route::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $routes = Route::filterAgencies()
            ->whereSource(config('app.source'))
            ->with('customers')
            ->whereIn('id', $ids)
            ->get();

        $result = true;
        foreach ($routes as $route) {
            if($route->customers->count()) {
                $result = false;
            } else {
                $route->delete();
            }
        }
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover um ou mais registos porque porque existem clientes associados. Associe primeiro os clientes a outra rota antes de eliminar a rota.');
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
            ->toArray() ?? [];


        $data = Route::with(['operator' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
            }])
            ->with(['provider' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
                $q->select(['id', 'name', 'color']);
            }])
            ->filterSource()
            ->filterAgencies()
            ->select([
                '*',
                DB::raw('(select count(*) from customers where route_id = routes.id and deleted_at is null) as customers')
            ]);

        //filter operator
        $value = $request->operator;
        if($request->has('operator')) {
            $data = $data->filterOperator($value);
        }

        //filter provider
        $value = $request->provider;
        if($request->has('provider')) {
            $data = $data->filterProvider($value);
        }

        //filter agency
        $values = $request->agency;
        if($request->has('agency')) {
            $data = $data->where(function($q) use($values) {
                foreach ($values as $value) {
                    $q->orWhere('agencies', 'like', '%"'.$value.'"%');
                }
            });
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
                        ->cacheTags(Agency::CACHE_TAG)
                        ->get(['id','code', 'name', 'color']);

        $agencies = $agencies->groupBy('id')->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id'])
            ->keyBy('id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get(['source', 'id', 'name', 'color'])
            ->keyBy('id')
            ->toArray();

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('admin.routes.datatables.code', compact('row'))->render();
            })
            ->edit_column('type', function($row) {
                return view('admin.routes.datatables.type', compact('row'))->render();
            })
            ->edit_column('name', function($row) {
                return view('admin.routes.datatables.name', compact('row'))->render();
            })
            ->edit_column('agencies', function($row) use ($agencies) {
                return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
            })
            ->edit_column('zip_codes', function($row) use ($agencies) {
                return view('admin.routes.datatables.zip_codes', compact('row'))->render();
            })
            ->edit_column('services', function ($row) use ($services) {
                return view('admin.routes.datatables.services', compact('row', 'services'))->render();
            })
            // ->edit_column('provider_id', function($row) use ($agencies) {
            //     return view('admin.routes.datatables.provider', compact('row'))->render();
            // })
            ->edit_column('customers', function($row) {
                return view('admin.routes.datatables.customers', compact('row'))->render();
            })
            // ->edit_column('operator_id', function($row) use ($agencies) {
            //     return @$row->operator->name;
            // })
            // ->add_column('vehicle', function($row) use ($agencies) {
            //     return @$row->operator->vehicle;
            // })
            ->edit_column('schedules', function($row) use ($operators, $providers) {
                return view('admin.routes.datatables.schedules', compact('row', 'operators', 'providers'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.routes.datatables.actions', compact('row'))->render();
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

        $items = Route::filterSource()
                    ->orderBy('sort')
                    ->get(['id', 'name']);

        $route = route('admin.routes.sort.update');

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

        Route::flushCache(Route::CACHE_TAG);

        try {
            Route::setNewOrder($request->ids);
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

}
