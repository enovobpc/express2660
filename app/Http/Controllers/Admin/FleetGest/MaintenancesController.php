<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\Billing\Item;
use App\Models\Billing\ItemStockHistory;
use App\Models\FleetGest\Maintenance;
use App\Models\FleetGest\Service;
use App\Models\FleetGest\Vehicle;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, File, Response, Setting;
use Mpdf\Mpdf;

class MaintenancesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_maintenances';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_maintenances']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryMechanic()
            ->orderBy('name', 'asc')
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

        $parts = Item::remember(config('cache.query_ttl'))
            ->cacheTags(Item::CACHE_TAG)
            ->filterSource()
            ->isFleetPart()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'vehicles',
            'providers',
            'operators',
            'services',
            'parts'
        );

        return $this->setContent('admin.fleet.maintenances.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $action = 'Registar Manutenção';

        $maintenance = new Maintenance;

        $formOptions = array('route' => array('admin.fleet.maintenances.store'), 'method' => 'POST', 'files' => true);

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        if ($request->has('vehicle')) {
            $vehicles = [$request->vehicle => @$vehicles[$request->vehicle]];
        }

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryMechanic()
            ->orderBy('name', 'asc')
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

        /*$parts = Part::remember(config('cache.query_ttl'))
            ->cacheTags(Part::CACHE_TAG)
            ->select(['id', 'name', 'category', 'stock'])
            ->get();

        $partsList = $parts->groupBy('category');*/

        $partsList = $maintenance->parts;

        $data = compact(
            'maintenance',
            'vehicles',
            'providers',
            'operators',
            'partsList',
            'action',
            'formOptions'
        );

        return view('admin.fleet.maintenances.edit', $data)->render();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //    public function show($id) {
    //    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $action = 'Editar Manutenção';

        $formOptions = array('route' => array('admin.fleet.maintenances.update', $id), 'method' => 'PUT', 'files' => true);

        $maintenance = Maintenance::whereHas('vehicle', function ($q) {
            $q->filterSource();
            $q->filterAgencies();
        })->findOrNew($id);

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryMechanic()
            ->orderBy('name', 'asc')
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

        /*$parts = Part::remember(config('cache.query_ttl'))
            ->cacheTags(Part::CACHE_TAG)
            ->select(['id', 'name', 'category'])
            ->get();

        $partsList = $parts->groupBy('category');

        $selectedParts = $maintenance->parts->pluck('id')->toArray();
        */

        $partsList =  $maintenance->parts;

        //dd($partsList->toArray());
        $vehicles = [$maintenance->vehicle_id => @$maintenance->vehicle->name];

        $data = compact(
            'maintenance',
            'operators',
            'providers',
            'vehicles',
            'partsList',
            'action',
            'formOptions'
        );

        return view('admin.fleet.maintenances.edit', $data)->render();
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

        Maintenance::flushCache(Maintenance::CACHE_TAG);

        $input = $request->all();
        //$input['parts'] = $request->get('parts', []);


        $maintenance = Maintenance::filterSource()
            ->findOrNew($id);

        $maintenanceExists = $maintenance->exists;
        if ($maintenance->validate($input)) {

            //create service if service_id is empty
            $service = Service::firstOrNew([
                'name' => trim($input['title']),
                'type' => 'maintenance'
            ]);

            if (empty($input['service_id'])) {
                $service->source = config('app.source');
                $service->save();
            }

            $input['service_id'] = $service->id;

            $maintenance->fill($input);
            $maintenance->save();

            // Restore used fleet parts so we can update with the new information
            if ($maintenanceExists) {
                ItemStockHistory::deleteByTarget(ItemStockHistory::TARGET_MAINTENANCE, $maintenance->id);
            }
            //--

            if(@$input['part_id']) {
                foreach ($input['part_id'] as $key => $partId) {
                    $partQty = $input['part_qty'][$key];
                    ItemStockHistory::decreaseByTarget($partId, $partQty, ItemStockHistory::TARGET_MAINTENANCE, $maintenance->id, null, $maintenance->created_at);
                }
            }

            //delete file
            if ($request->delete_file && !empty($maintenance->filepath)) {
                File::delete(public_path() . '/' . $maintenance->filepath);
                $maintenance->filepath = null;
                $maintenance->filename = null;
            }

            //upload file
            if ($request->hasFile('file')) {

                if ($maintenance->exists && !empty($maintenance->filepath)) {
                    File::delete(storage_path() . '/' . $maintenance->filepath);
                }

                if (!$maintenance->upload($request->file('file'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o ficheiro.');
                }
            } else {
                $maintenance->save();
            }

            Maintenance::updateVehicleCounters($maintenance->vehicle_id);

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $maintenance->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Maintenance::flushCache(Maintenance::CACHE_TAG);

        // Restore used fleet parts
        ItemStockHistory::deleteByTarget(ItemStockHistory::TARGET_MAINTENANCE, $id);
        $result = Maintenance::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a manutenção.');
        }

        return Redirect::back()->with('success', 'Manutenção removida com sucesso.');
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

        Maintenance::flushCache(Maintenance::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $rows = Maintenance::filterSource()
            ->whereIn('id', $ids)
            ->get();

        foreach($rows as $row) {
            // Restore used fleet parts
            ItemStockHistory::deleteByTarget(ItemStockHistory::TARGET_MAINTENANCE, $row->id);
            $result = $row->delete();
        }

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

        $data = Maintenance::with('vehicle', 'provider', 'operator', 'parts', 'invoice')
            ->filterSource()
            ->select();

        //filter parts
        $value = $request->get('parts');

        if($request->has('parts')) {
            $data = $data->whereHas('parts', function($q) use($value) {
                $q->whereIn('billing_product_id', $value);
            });
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter vehicle
        $value = $request->get('vehicle');
        if ($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter operator
        $value = $request->get('operator');
        if ($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter service
        $value = $request->get('service');
        if ($request->has('service')) {
            $data = $data->where('service_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('date', function ($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('title', function ($row) {
                return view('admin.fleet.maintenances.datatables.title', compact('row'))->render();
            })
            ->edit_column('vehicle_id', function ($row) {
                return view('admin.fleet.maintenances.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('km', function ($row) {
                return $row->km ? money($row->km, '', 0) : 0;
            })
            ->edit_column('total', function ($row) {
                return '<b data-total="' . $row->total . '">' . ($row->total == '0.00' ? '' : money($row->total, Setting::get('app_currency'))) . '</b>';
            })
            ->edit_column('provider_id', function ($row) {
                return view('admin.fleet.maintenances.datatables.provider', compact('row'))->render();
            })
            ->edit_column('assigned_invoice_id', function ($row) {
                return @$row->invoice->reference;
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.fleet.maintenances.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search fleet maintenance services
     * GET /admin/fleet/maintenances/search/services
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchServices(Request $request)
    {

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $services = Service::filterSource()
                ->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', $search);
                })
                ->get(['name', 'id']);

            if ($services) {

                $results = array();
                foreach ($services as $service) {
                    $results[] = [
                        'data' => $service->id,
                        'value' => str_limit($service->name, 50, '')
                    ];
                }
            } else {
                $results = ['Nenhum serviço encontrado.'];
            }
        } catch (\Exception $e) {
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * Search fleet parts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchParts(Request $request)
    {
        $index      = $request->index;
        $search     = trim($request->get('query'));
        $search     = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $parts = Item::filterSource()
                ->isActive()
                ->isFleetPart()
                ->where(function ($q) use ($search) {
                    $q->where('reference', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->orderBy('stock_total', 'desc')
                ->take(20)
                ->get();

            if (!$parts->isEmpty()) {
                $results = array();
                foreach ($parts as $row) {
                    $results[] = [
                        'index'         => $index,
                        'data'          => $row->id,
                        'value'         => strtoupper(trim($row->name)),
                        'part'          => $row->id,
                        'name'          => trim($row->name),
                        'reference'     => trim($row->reference),
                        'stock_total'   => $row->stock_total,
                        'brand_name'    => @$row->brand->name ?? '',
                        'price'         => $row->price
                    ];
                }
            } else {
                $results = ['Nenhuma peça encontrada.'];
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * PRINT A MAINTENANCE 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printMaintenance($id)
    {
        $maintenance = Maintenance::with('vehicle', 'provider', 'operator', 'parts', 'invoice')
            ->with(['parts' => function ($q) {
                $q->with(['product' => function ($q) {
                    $q->with('brand', 'brandModel', 'provider');
                }]);
            }])
            ->where('id', $id)
            ->first();

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;
        $mpdf->debug = true;

        $documentTitle = 'Manutenção ' . $maintenance->title;


        $data = [
            'documentTitle'     => $documentTitle,
            'documentSubtitle'  => 'Manutenção viatura ' . $maintenance->vehicle->license_plate,
            'maintenance'       => $maintenance,
            'view'              => 'admin.fleet.printer.maintenance'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        return $mpdf->Output('Manutencao_' . $maintenance->vehicle->license_plate . '_ ' . date('Y-m-d') . '.pdf', 'I'); //output to screen
        exit;
    }
}
