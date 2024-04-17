<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\Agency;
use App\Models\Billing\Item;
use App\Models\FleetGest\Checklist;
use App\Models\FleetGest\ChecklistAnswer;
use App\Models\FleetGest\BrandModel;
use App\Models\FleetGest\Brand;
use App\Models\FleetGest\Vehicle;
use App\Models\FleetGest\VehicleHistory;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa, Form, Setting, Auth, DB, Date;

class VehiclesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_vehicles';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_vehicles']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $agencies = Auth::user()->listsAgencies();

        $brands = Brand::remember(config('cache.query_ttl'))
            ->cacheTags(Brand::CACHE_TAG)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), Auth::user()->isAdmin() ? true : false);

        $data = compact(
            'agencies',
            'brands',
            'operators'
        );

        return $this->setContent('admin.fleet.vehicles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $action = 'Adicionar Veículo';

        $vehicle = new Vehicle;

        if ($request->has('type') && $request->type == 'trailer') {
            $vehicle->type = 'trailer';
            $action = 'Adicionar Reboque';
        }

        $formOptions = array('route' => array('admin.fleet.vehicles.store'), 'method' => 'POST', 'files' => true);

        $agencies = Auth::user()->listsAgencies();

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $trailers = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->where('type', 'trailer')
            ->pluck('name', 'id')
            ->toArray();

        $brands = Brand::remember(config('cache.query_ttl'))
            ->cacheTags(Brand::CACHE_TAG);

        if ($request->has('type') && $request->type == 'trailer') {
            $brands = $brands->where('type', 'trailer');
        }

        $brands = $brands->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $models = BrandModel::remember(config('cache.query_ttl'))
            ->cacheTags(BrandModel::CACHE_TAG)
            ->where('brand_id', $vehicle->brand_id)
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), Auth::user()->isAdmin() ? true : false);


        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->where(function ($q) {
                $q->where('source', config('app.source'));
                $q->orWhereNull('source');
            })
            ->get();

        $subProviders = $allProviders->filter(function ($item) {
            return $item->type == 'carrier';
        })
            ->pluck('name', 'id')
            ->toArray();

        $fuelProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_GAS_STATION;
        })
            ->pluck('name', 'id')
            ->toArray();

        $mechanicProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_MECHANIC;
        })
            ->pluck('name', 'id')
            ->toArray();

        $inspectionProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_CAR_INSPECTION;
        })
            ->pluck('name', 'id')
            ->toArray();

        $insurerProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_INSURER;
        })
            ->pluck('name', 'id')
            ->toArray();

        $tollsProviders = $allProviders->filter(function ($item) {
            return $item->category_id == 5; //Provider::CATEGORY_TOLL;
        })
            ->pluck('name', 'id')
            ->toArray();

        $allProviders = $allProviders->pluck('name', 'id')->toArray();

        $data = compact(
            'action',
            'formOptions',
            'vehicle',
            'trailers',
            'agencies',
            'brands',
            'models',
            'operators',
            'allProviders',
            'fuelProviders',
            'mechanicProviders',
            'insurerProviders',
            'tollsProviders',
            'inspectionProviders',
            'subProviders'
        );

        return $this->setContent('admin.fleet.vehicles.create', $data);
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

        $action = 'Editar Veículo';

        $vehicle = Vehicle::filterSource()
            ->findOrfail($id);

        $formOptions = array('route' => array('admin.fleet.vehicles.update', $vehicle->id), 'method' => 'PUT', 'files' => true);

        $agencies = Auth::user()->listsAgencies();

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $trailers = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->where('type', 'trailer')
            ->pluck('name', 'id')
            ->toArray();

        $brands = Brand::remember(config('cache.query_ttl'))
            ->cacheTags(Brand::CACHE_TAG)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $models = BrandModel::remember(config('cache.query_ttl'))
            ->cacheTags(BrandModel::CACHE_TAG)
            ->where('brand_id', $vehicle->brand_id)
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), Auth::user()->isAdmin() ? true : false);

        $checklists = Checklist::remember(config('cache.query_ttl'))
            ->cacheTags(Checklist::CACHE_TAG)
            ->filterSource()
            ->orderBy('title', 'asc')
            ->pluck('title', 'id')
            ->toArray();

        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->where(function ($q) {
                $q->where('source', config('app.source'));
                $q->orWhereNull('source');
            })
            ->get();

        $subProviders = $allProviders->filter(function ($item) {
            return $item->type == 'carrier';
        })
            ->pluck('name', 'id')
            ->toArray();

        $fuelProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_GAS_STATION;
        })
            ->pluck('name', 'id')
            ->toArray();

        $mechanicProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_MECHANIC;
        })
            ->pluck('name', 'id')
            ->toArray();

        $inspectionProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_CAR_INSPECTION;
        })
            ->pluck('name', 'id')
            ->toArray();

        $insurerProviders = $allProviders->filter(function ($item) {
            return $item->category_slug == Provider::CATEGORY_INSURER;
        })
            ->pluck('name', 'id')
            ->toArray();

        $tollsProviders = $allProviders->filter(function ($item) {
            return $item->category_id == 5; //Provider::CATEGORY_TOLL;
        })
            ->pluck('name', 'id')
            ->toArray();

        $allProviders = $allProviders->pluck('name', 'id')->toArray();

        $parts = Item::remember(config('cache.query_ttl'))
            ->cacheTags(Item::CACHE_TAG)
            ->filterSource()
            ->isFleetPart()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $balance = $vehicle->getBalanceTotal();

        $params = ['expireds' => true];
        $notifications = Vehicle::getNotifications($vehicle->id, $params);
        $notificationsWarning = @$notifications->notifications_warning;
        $notificationsExpired = @$notifications->notifications_expired;
        $notifications        = @$notifications->notifications;

        $types = trans('admin/fleet.usages-logs.types');

        $data = compact(
            'vehicle',
            'trailers',
            'brands',
            'models',
            'agencies',
            'allProviders',
            'fuelProviders',
            'mechanicProviders',
            'insurerProviders',
            'inspectionProviders',
            'tollsProviders',
            'providers',
            'providersFuel',
            'operators',
            'balance',
            'parts',
            'action',
            'formOptions',
            'notifications',
            'notificationsWarning',
            'notificationsExpired',
            'graphData',
            'checklists',
            'subProviders',
            'types'
        );

        return $this->setContent('admin.fleet.vehicles.edit', $data);
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

        Vehicle::flushCache(Vehicle::CACHE_TAG);

        $input = $request->all();
        $input['is_default'] = $request->get('is_default', false);
        $input['assistants']  = $request->get('assistants', []);

        if (@$input['brand_id'] && !empty(@$input['model_name'])) {
            $model = new BrandModel();
            $model->brand_id = $input['brand_id'];
            $model->name     = strtoupper(trim($input['model_name']));
            $model->save();

            $input['model_id'] = $model->id;
        }

        $vehicle = Vehicle::filterSource()->findOrNew($id);

        //update iuc and ipo dates
        $today = Date::today();
        $registrationDate   = new Date($input['registration_date']); //data compra ===> dia + mes é o do IUC
        $ipoDateVehicle     = new Date($input['ipo_date']); //data da inspeção
        $ipoDate            = $ipoDateVehicle;
        //$ipoDate            = new Date($today->year . '-' . $ipoDateVehicle->format('m-d'));

        $iucDate = null;
        if ($input['type'] != 'trailer') {
            if (empty(@$input['iuc_date'])) {
                $iucDate = new Date($today->year . '-' . $registrationDate->format('m') . '-' . $registrationDate->endOfMonth()->day);
            } else {
                $iucDate = new Date($input['iuc_date']);
            }
        }

        /*if($iucDate->lt($today)) {
            $ipoDate = $ipoDate->addYear(1)->format('Y-m-d');
            $iucDate = $iucDate->addYear(1)->format('Y-m-d');
        }*/

        //if(!$vehicle->exists) {
        $input['iuc_date'] = $iucDate;
        $input['ipo_date'] = $ipoDate;
        //}

        //$input['iuc_date'] = $input['ipo_date'];

        /*if($input['is_default']) {
            Vehicle::filterSource()->where('is_default', 1)->update(['is_default' => 0]);
        }*/

        $input['tachograph_date'] = empty($input['tachograph_date']) ? null : $input['tachograph_date'];

        if ($vehicle->validate($input)) {
            $vehicle->fill($input);
            $vehicle->save();

            //delete image
            if ($request->delete_photo && !empty($vehicle->filepath)) {
                Croppa::delete($vehicle->filepath);
                $vehicle->filepath = null;
                $vehicle->filename = null;
            }

            //upload image
            if ($request->hasFile('image')) {

                if ($vehicle->exists && !empty($vehicle->filepath)) {
                    Croppa::delete($vehicle->filepath);
                }

                if (!$vehicle->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
            } else {
                $vehicle->save();
            }

            //assign vehicle to user
            if ($vehicle->operator_id) {
                User::whereVehicle($vehicle->license_plate)->update(['vehicle' => null]);
                User::whereId($vehicle->operator_id)->update(['vehicle' => $vehicle->license_plate]);
            } else {
                User::whereVehicle($vehicle->license_plate)->update(['vehicle' => null]);
            }

            return Redirect::route('admin.fleet.vehicles.edit', $vehicle->id)->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $vehicle->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $result = Vehicle::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a viatura.');
        }

        return Redirect::back()->with('success', 'Viatura removida com sucesso.');
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

        $result = Vehicle::filterSource()
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

        $data = Vehicle::with('trailer', 'operator', 'reminders', 'brand')
            ->filterSource()
            ->select();

        //filter tab
        $value = $request->get('tab');
        if ($request->has('tab') && $value == 'trailers') {
            $data = $data->where('type', 'trailer');

            //filter brand
            $value = $request->get('t_brand');
            if ($request->has('t_brand')) {
                $data = $data->where('brand_id', $value);
            }

            //filter operator
            $value = $request->get('t_operator');
            if ($request->has('t_operator')) {
                $data = $data->where('operator_id', $value);
            }


            //filter status
            $value = $request->get('t_status');
            if ($request->has('t_status')) {
                $data = $data->whereIn('status', $value);
            } else {
                $value = $request->t_hide_inactive;
                if ($request->has('t_hide_inactive') && !empty($value)) {
                    $data = $data->whereNotIn('status', ['inactive', 'sold', 'slaughter']);
                }
            }
        } elseif ($request->has('tab') && $value == 'forklifts') {
            $data = $data->where('type', 'forklift');
        } else {
            $data = $data->where(function($q){
                $q->where('type', '<>', 'trailer');
                $q->where('type', '<>', 'forklift');
            });

            //filter status
            $value = $request->get('status');
            if ($request->has('status')) {
                $data = $data->whereIn('status', $value);
            } else { //se há status ignora os check de inativos
                //show hidden
                $value = $request->hide_inactive;
                if ($request->has('hide_inactive') && !empty($value)) {
                    $data = $data->whereNotIn('status', ['inactive', 'sold', 'slaughter']);
                }
            }

            //filter brand
            $value = $request->get('brand');
            if ($request->has('brand')) {
                $data = $data->where('brand_id', $value);
            }

            //filter type
            $value = $request->get('type');
            if ($request->has('type')) {
                $data = $data->where('type', $value);
            }

            //filter operator
            $value = $request->get('operator');
            if ($request->has('operator')) {
                $data = $data->where('operator_id', $value);
            }
        }



        return Datatables::of($data)
            ->add_column('photo', function ($row) {
                return view('admin.fleet.vehicles.datatables.photo', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.fleet.vehicles.datatables.name', compact('row'))->render();
            })
            ->edit_column('type', function ($row) {
                return view('admin.fleet.vehicles.datatables.type', compact('row'))->render();
            })
            ->edit_column('trailer_id', function ($row) {
                return @$row->trailer->name;
            })
            ->edit_column('operator_id', function ($row) {
                return @$row->operator->first_last_name;
            })
            ->edit_column('status', function ($row) {
                return view('admin.fleet.vehicles.datatables.status', compact('row'))->render();
            })
            ->edit_column('insurance_date', function ($row) {
                $date = $row->insurance_date;
                return view('admin.fleet.vehicles.datatables.date', compact('date'))->render();
            })
            ->edit_column('ipo_date', function ($row) {
                $date = $row->ipo_date;
                return view('admin.fleet.vehicles.datatables.date', compact('date'))->render();
            })
            ->edit_column('iuc_date', function ($row) {
                $date = $row->iuc_date;
                return view('admin.fleet.vehicles.datatables.date', compact('date'))->render();
            })
            ->edit_column('tachograph_date', function ($row) {
                $date = $row->tachograph_date;
                return view('admin.fleet.vehicles.datatables.date', compact('date'))->render();
            })
            ->edit_column('counter_km', function ($row) {
                return view('admin.fleet.vehicles.datatables.counter_km', compact('row'))->render();
            })
            ->edit_column('last_location', function ($row) {
                return view('admin.fleet.vehicles.datatables.last_location', compact('row'))->render();
            })
            ->edit_column('counter_consumption', function ($row) {
                return view('admin.fleet.vehicles.datatables.consumption', compact('row'))->render();
            })
            ->edit_column('next_review_km', function ($row) {
                return view('admin.fleet.vehicles.datatables.next_review', compact('row'))->render();
            })
            ->edit_column('trailer_type', function ($row) {
                return view('admin.fleet.vehicles.datatables.trailer_type', compact('row'))->render();
            })
            ->edit_column('increase_roof', function ($row) {
                return view('admin.fleet.vehicles.datatables.increase_roof', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.fleet.vehicles.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    public function datatableChecklists(Request $request)
    {

        $data = ChecklistAnswer::with('checklist', 'operator')
            ->where('vehicle_id', $request->vehicle)
            ->groupBy('control_hash')
            ->select([
                '*',
                DB::raw('(MIN(answer)) as status')
            ]);

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('created_at', [$dtMin . ' 00:00:00', $dtMax . ' 23:59:59']);
        }

        //filter checklist
        $value = $request->get('checklist');
        if ($request->has('checklist')) {
            $data = $data->where('checklist_id', $value);
        }

        //filter operator
        $value = $request->get('operator');
        if ($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('operator', function ($row) {
                return @$row->operator->name;
            })
            ->add_column('checklist', function ($row) {
                return view('admin.fleet.checklists.datatables.checklist', compact('row'))->render();;
            })
            ->add_column('status', function ($row) {
                return view('admin.fleet.checklists.datatables.answers.status', compact('row'))->render();;
            })
            ->add_column('km', function ($row) {
                if ($row->km) {
                    return money($row->km, '', 0);
                }
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.fleet.checklists.datatables.answers.date', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.fleet.checklists.datatables.answers.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableHistory(Request $request, $vehicleId)
    {

        $data = VehicleHistory::with('provider')
            ->whereHas('vehicle', function ($q) {
                $q->filterSource();
            })
            ->where('vehicle_id', $vehicleId)
            ->select();

        //filter date min
        $dtMin = $request->get('history_date_min');
        if ($request->has('history_date_min')) {
            $dtMax = $dtMin;
            if ($request->has('history_date_max')) {
                $dtMax = $request->get('history_date_max');
            }

            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
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

        //filter type
        $value = $request->get('type');
        if ($request->has('type')) {
            $data = $data->where('type', $value);
        }

        return Datatables::of($data)
            ->edit_column('date', function ($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('type', function ($row) {
                return trans('admin/fleet.providers.types.' . $row->type);
            })
            ->edit_column('provider_id', function ($row) {
                return @$row->provider->name;
            })
            ->edit_column('km', function ($row) {
                return number_format($row->km, 0, '', '.');
            })
            ->edit_column('total', function ($row) {
                return '<b data-total="' . $row->total . '">' . money($row->total, Setting::get('app_currency')) . '</b>';
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function getBrandModels(Request $request)
    {

        $data = BrandModel::remember(config('cache.query_ttl'))
            ->cacheTags(BrandModel::CACHE_TAG)
            ->where('brand_id', $request->get('brand'))
            ->pluck('name', 'id')
            ->toArray();

        if (empty($data)) {
            return Form::text('model_name', null, ['class' => 'form-control uppercase']);
        }

        return Form::select('model_id', ['' => ''] + $data, null, ['class' => 'form-control select2']);
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setAutomaticName(Request $request)
    {

        Vehicle::flushCache(Vehicle::CACHE_TAG);

        $vehicles = Vehicle::with('brand')
            ->filterSource()
            ->where('type', '<>', 'trailer')
            ->get();

        foreach ($vehicles as $vehicle) {
            $vehicle->name = $vehicle->license_plate . ' | ' . @$vehicle->brand->name;
            $vehicle->save();
        }

        return Redirect::back()->with('success', 'Nomes atribuidos com sucesso.');
    }
}
