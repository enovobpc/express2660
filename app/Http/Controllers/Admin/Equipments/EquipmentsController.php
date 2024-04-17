<?php

namespace App\Http\Controllers\Admin\Equipments;

use App\Http\Controllers\Admin\FilesImporter\ImporterController;
use App\Models\Customer;
use App\Models\Equipment\Equipment;
use App\Models\Equipment\Category;
use App\Models\Equipment\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Equipment\Warehouse;
use App\Models\Equipment\Location;
use Setting, DB, Auth, Date, Response, Excel;

class EquipmentsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'equipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',equipments']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $warehouses = Warehouse::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $allCategories = Category::filterSource()
            ->orderBy('code', 'asc')
            ->get();

        $categoriesList = $allCategories->pluck('name', 'id')->toArray();

        $stats = $this->getStats(
            $request->get('stats_date_min'),
            $request->get('stats_date_max'),
            $request->get('location')
        );

        $allStatus = trans('admin/equipments.equipments.status');

        $data = compact(
            'warehouses',
            'locations',
            'allCategories',
            'categoriesList',
            'stats',
            'allStatus'
        );

        return $this->setContent('admin.equipments.equipments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $action = 'Novo equipamento';

        $equipment = new Equipment();

        $formOptions = array('route' => array('admin.equipments.store'), 'method' => 'POST', 'class' => 'form-equipment');

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $categories = Category::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'equipment',
            'action',
            'formOptions',
            'locations',
            'categories'
        );

        return view('admin.equipments.equipments.edit', $data)->render();
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
    public function show($id)
    {

        $equipment = Equipment::filterSource()
            ->findOrfail($id);

        $data = compact(
            'equipment'
        );

        return view('admin.equipments.equipments.show', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $action = 'Editar equipamento';

        $equipment = Equipment::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.equipments.update', $equipment->id), 'method' => 'PUT', 'class' => 'form-location');

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $categories = Category::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'equipment',
            'action',
            'formOptions',
            'locations',
            'categories'
        );

        return view('admin.equipments.equipments.edit', $data)->render();
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

        $input = $request->all();

        $location = Location::filterSource()
            ->whereId($input['location_id'])
            ->first();

        $equipment = Equipment::filterSource()
            ->findOrNew($id);

        $exists = $equipment->exists;

        if (!$exists && empty($input['stock_total'])) {
            $input['status'] = 'outstock';
        }

        if ($equipment->validate($input)) {
            $equipment->fill($input);
            $equipment->warehouse_id = @$location->warehouse_id;
            $equipment->source       = config('app.source');
            $equipment->save();

            if (!$exists) {
                $equipmentHistory = new History();
                $equipmentHistory->equipment_id = $equipment->id;
                $equipmentHistory->location_id  = $equipment->location_id;
                $equipmentHistory->action       = 'reception';
                $equipmentHistory->operator_id  = Auth::user()->id;
                //$equipmentHistory->stock = $input['stock_total'];
                $equipmentHistory->save();
            }

            return Redirect::back()->with('success', 'Equipamento gravado com sucesso.');
        }

        return Redirect::back()->with('error', $location->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $result = Equipment::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o equipamento.');
        }

        return Redirect::back()->with('success', 'Artigo removido com sucesso.');
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

        $result = Equipment::filterSource()
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

        $data = Equipment::with('warehouse', 'location', 'category', 'history')
            ->filterSource()
            ->select();

        //filter type file
        $value = $request->type_file;
        if ($request->has('type_file')) {
            $data = $data->whereHas('history', function ($q) use ($value) {
                $q->where('obs', 'like', '%' . $value . '%');
            });
        }

        //filter operation
        $value = $request->action;
        if ($request->has('action')) {
            $data = $data->whereHas('history', function ($q) use ($value) {
                $q->where('action',  $value);
            });
        }


        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter warehouse
        $value = $request->images;
        if ($request->has('images')) {
            if ($value) {
                $data = $data->whereNotNull('filepath');
            } else {
                $data = $data->whereNull('filepath');
            }
        }

        //filter location
        $value = $request->get('location');
        if ($request->has('location')) {
            if (in_array('-1', $value)) {
                $data = $data->whereNull('location_id');
            } else {
                $data = $data->where('location_id', $value);
            }
        }

        //filter warehouse
        $value = $request->category;
        if ($request->has('category')) {
            $data = $data->where('category_id', $value);
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter date
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {

            $dtMax = $dtMin;

            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $dateType = $request->get('date_unity');

                if ($dateType == 'creation') {
                    $data->whereBetween('created_at', [$dtMin, $dtMax]);
                } else if ($dateType == 'reception') {
                    $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $dateType) {
                        $q->where('action', $dateType);
                        $q->whereBetween('updated_at', [$dtMin, $dtMax]);
                    });
                } else if ($dateType == 'transfer') {
                    $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $dateType) {
                        $q->where('action', $dateType);
                        $q->whereBetween('updated_at', [$dtMin, $dtMax]);
                    });
                } else if ($dateType == 'out') {
                    $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $dateType) {
                        $q->where('action', $dateType);
                        $q->whereBetween('updated_at', [$dtMin, $dtMax]);
                    });
                }
            } else { //filter by shipment date

                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';

                $data = $data->whereBetween('last_update', [$dtMin, $dtMax]);
            }
        }


        return Datatables::of($data)
            ->edit_column('sku', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.sku', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.name', compact('row'))->render();
            })
            ->edit_column('location_id', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.location', compact('row'))->render();
            })
            ->edit_column('category_id', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.category', compact('row'))->render();
            })
            ->edit_column('stock_total', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.stock', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.status', compact('row'))->render();
            })
            ->edit_column('last_update', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.last_update', compact('row'))->render();
            })
            ->add_column('photo', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.photo', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.equipments.equipments.datatables.equipments.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableLocations(Request $request)
    {

        $data = Location::with(['equipments' => function ($q) {
            $q->where('status', '<>', 'outstock');
        }])
            ->filterSource()
            ->select();

        //filter warehouse
        $value = $request->warehouse;
        if ($request->has('warehouse')) {
            $data = $data->where('warehouse_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('warehouse_id', function ($row) {
                return view('admin.equipments.equipments.datatables.locations.warehouse', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.equipments.equipments.datatables.locations.name', compact('row'))->render();
            })
            ->edit_column('equipments', function ($row) {
                return view('admin.equipments.equipments.datatables.locations.equipments', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.equipments.equipments.datatables.locations.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {
            $customers = Customer::filterAgencies()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->get(['name', 'code', 'id']);

            if (!$customers->isEmpty()) {
                $results = array();
                foreach ($customers as $customer) {
                    $results[] = array('id' => $customer->id, 'text' => $customer->code . ' - ' . str_limit($customer->name, 40));
                }
            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }
        } catch (\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Store picking process
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function pickingStore(Request $request)
    {

        $barcode    = $request->get('code');
        $action     = $request->get('action');
        $customerId = $request->get('customer_id');
        $locationId = $request->get('location_id');
        $categoryId = $request->get('category_id');
        $otCode     = $request->get('ot_code');
        $name       = $request->get('name');
        $name       = $name ? $name : $barcode;
        //$autocreate = $request->get('autocreate', false);
        //$autocreate = $autocreate == 'true' ? true : false;


        $location = Location::filterSource()->find($locationId);

        if (!$location && $action != 'out') {
            return response()->json([
                'result'   => false,
                'feedback' => 'Localização inválida ou desconhecida.',
                'html'     => null
            ]);
        }

        if ($action == 'reception' && empty($categoryId)) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Cliente e Categoria são campos obrigatórios',
                'html'     => null
            ]);
        }

        $equipment = Equipment::filterSource()
            ->where(function ($q) use ($barcode) {
                $q->where('sku', $barcode);
                $q->orWhere('lote', $barcode);
                $q->orWhere('serial_no', $barcode);
            })
            ->first();

        $oldLocationId = @$equipment->location_id;

        // if(@$equipment->status == 'outstock') {
        //     return response()->json([
        //         'result'   => false,
        //         'feedback' => 'Este equipamento já não se encontra disponível.',
        //         'html'     => null
        //     ]);
        // }

        if ($action == 'reception') {
            if (empty($equipment)) {
                $equipment = new Equipment();
            } elseif ($equipment->status == 'reception') {
                $action = 'transfer'; //muda estado se o estado anterior for recepção e o produto agora já existe (ex: picagem 2ª vez ou mudança diretamente da acao)
            }
            $equipment->sku           = $barcode;
            $equipment->stock_total   = 1;
            $equipment->status        = 'available';
            $equipment->source        = config('app.source');
            $equipment->customer_id   = $customerId;
            $equipment->category_id   = $categoryId;
            $equipment->name          = $name;
            $equipment->ot_code       = $otCode ? $otCode : $equipment->ot_code;
        }

        if ($equipment) {

            $equipment->location_id   = $locationId ? @$location->id : $equipment->location_id;
            $equipment->warehouse_id  = $locationId ? @$location->warehouse_id : $equipment->warehouse_id;
            $equipment->last_update   = date('Y-m-d H:i:s');

            if ($action == 'out') {
                $equipment->status       = 'outstock';
                $equipment->location_id  = null;
                $equipment->warehouse_id = null;
                $equipment->stock_total  = 0;
                $equipment->ot_code      = $otCode ? $otCode : $equipment->ot_code;
            }

            $equipment->ot_code = $otCode ? $otCode : $equipment->ot_code;
            $equipment->save();

            //add history
            $equipmentHistory = new History();
            $equipmentHistory->equipment_id = $equipment->id;
            $equipmentHistory->ot_code      = $otCode ? $otCode : $equipment->ot_code;
            $equipmentHistory->action       = $action;

            if ($action == 'out') { //coloca na localização
                $equipmentHistory->location_id = $oldLocationId;
            } else {
                $equipmentHistory->location_id = $equipment->location_id;
            }

            $equipmentHistory->operator_id = Auth::user()->id;
            $equipmentHistory->save();

            $response = [
                'result'   => true,
                'feedback' => 'Localizado com sucesso.',
                'html'     => view('admin.equipments.equipments.partials.list_item', compact('equipment'))->render(),
                'id'       => $equipment->id
            ];
        } else {
            $response = [
                'result'   => false,
                'feedback' => 'Equipamento não encontrado.',
                'html'     => null,
            ];
        }


        return response()->json($response);
    }

    /**
     * Return equipments stats
     */
    public function getStats($dtMin = null, $dtMax = null, $locationId = null)
    {

        $defaultDtMin = new Date();
        $defaultDtMin = $defaultDtMin->subDays(30)->format('Y-m-d');

        $dtMax = $dtMax ? $dtMax : date('Y-m-d');
        $dtMin = $dtMin ? $dtMin : $defaultDtMin;

        $dtMin = $dtMin . ' 00:00:00';
        $dtMax = $dtMax . ' 23:59:59';

        $equipments = Equipment::with('category', 'location')
            ->filterSource();
        if ($locationId) {
            $equipments = $equipments->where('location_id', $locationId);
        } else {
            $equipments = $equipments->whereNotNull('location_id');
        }
        $equipments = $equipments->get();

        $categories = $equipments->groupBy('category.name')->transform(function ($item) {
            return $item->groupBy('status')->transform(function ($item) {
                return $item->sum('stock_total');
            });
        });

        $categoryLocation = $equipments->groupBy('location.id')->transform(function ($item) {
            return $item->groupBy('category.id')->transform(function ($item) {
                return $item->sum('stock_total');
            });
        });

        $equipmentHistories = History::with('equipment')
            ->whereHas('equipment', function ($q) use ($locationId) {
                $q->filterSource();
            })
            ->whereBetween('created_at', [$dtMin, $dtMax]);

        if ($locationId) {
            $equipmentHistories = $equipmentHistories->where('location_id', $locationId);
        }

        $equipmentHistories = $equipmentHistories->get();

        $equipmentHistories = $equipmentHistories->groupBy('equipment.category_id')->transform(function ($item) {
            return $item->groupBy('action')->transform(function ($item) {
                return $item->count();
            });
        });

        return [
            'categories'       => $categories,
            'categoryLocation' => $categoryLocation,
            'history'          => $equipmentHistories
        ];
    }

    public function importConferenceFile(Request  $request)
    {
        $file       = $request->file('file');
        $input      = $request->all();

        $importerController = new ImporterController();
        $mappingCols = $importerController->getColumnMapping();

        try {

            $excel = Excel::load($file, function ($reader) use ($mappingCols, $input) {

                $test = $reader->toArray();
                if (is_array(@$test[0][0])) { //multiple sheets
                    $reader = $reader->first();
                }


                $reader->each(function ($row) use ($mappingCols, $input) {

                    if (!empty($row)) {

                        $row = array_values($row->toArray());

                        $sku        = @$row[@$mappingCols[strtolower($input['column_sku'])]];
                        $date       = @$row[@$mappingCols[strtolower($input['column_date'])]];
                        $ot         = @$row[@$mappingCols[strtolower($input['column_ot'])]];
                        $location   = @$row[@$mappingCols[strtolower($input['column_operator'])]];

                        $equipment = Equipment::filterSource()
                            ->where('sku', $sku)
                            ->first();

                        $location_id = Location::filterSource()
                            ->where('code', $location)
                            ->first();


                        if ($equipment) {

                            $stock_total = $equipment->stock_total;

                            $equipment->status      = @$input['action'] == 'out' ? 'outstock' : 'available';
                            $equipment->ot_code     = $ot;
                            $equipment->last_update = $date;
                            $location               = $equipment->location_id;

                            if (@$input['action'] == 'out') {
                                $equipment->stock_total  = '0';
                                $location                = null;
                                $equipment->warehouse_id = null;
                            }
                            if (@$input['action'] == 'transfer') {
                                $equipment->location_id = $location_id->id;
                            }

                            $equipment->save();

                            $equipmentHistory = new History();
                            $equipmentHistory->equipment_id = $equipment->id;
                            $equipmentHistory->ot_code      = $ot;
                            $equipmentHistory->location_id  = $location;
                            $equipmentHistory->action       = @$input['action'];
                            $equipmentHistory->stock        = $stock_total;
                            $equipmentHistory->stock_low    = $stock_total;
                            $equipmentHistory->created_at   = $date;
                            $equipmentHistory->obs          = 'Ficheiro - ' . @$input['type_file'];
                            $equipmentHistory->operator_id  = Auth::user()->id;
                            $equipmentHistory->save();
                        }
                    }
                });
            });
        } catch (\Exception $e) {
            return Redirect::back()->with('success', 'Falha na leitura do ficheiro. ' . $e->getMessage());
        }

        return Redirect::back()->with('success', 'Ficheiro processado com sucesso.');
    }

    public function filterExportFile($group = null)
    {

        $action = 'Filtrar num Período';
        $dateStart = date('Y-m-d');
        $dateEnd   = date('Y-m-d', strtotime('+1 day'));

        $data = compact('action', 'dateStart', 'dateEnd', 'group');


        return view('admin.equipments.equipments.modals.export_equipments', $data)->render();
    }
}
