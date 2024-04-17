<?php

namespace App\Http\Controllers\Admin\AirWaybills;

use App\Models\AirWaybill\Agent;
use App\Models\AirWaybill\Expense;
use App\Models\AirWaybill\GoodType;
use App\Models\AirWaybill\IataAirport;
use App\Models\AirWaybill\Model;
use App\Models\AirWaybill\Provider;
use App\Models\AirWaybill\Waybill;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use phpDocumentor\Reflection\Types\Resource_;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Response, File, DB;

class WaybillsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'air-waybills';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',air-waybills']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('name', 'id')->toArray();

        $operators = User::remember(5)
            ->filterAgencies()
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        return $this->setContent('admin.awb.air_waybills.index', compact('operators', 'providers', 'agents', 'goodsTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $modelId = $request->get('prefill');

        $action = 'Nova Carta de Porte Aéreo';

        $waybill = new Waybill();

        if($modelId) {
            $model = Model::findOrFail($modelId);

            $scaleIds = [];
            if(!empty($waybill->flight_scales)) {
                foreach ($waybill->flight_scales as $flyscale) {
                    $scaleIds[] = $flyscale->airport;
                }
            }

            $scaleAirports = IataAirport::whereIn('code', $scaleIds)->pluck('airport', 'code')->toArray();

            $selectedExpenses = [];
            foreach ($model->expenses as $item) {
                $selectedExpenses[] = [
                    'expense' => $item->expense,
                    'price'   => $item->price
                ];
            }

            $selectedOtherExpenses = [];
            foreach ($model->other_expenses as $item) {
                $selectedOtherExpenses[] = [
                    'expense' => $item->expense,
                    'price'   => $item->price
                ];
            }

            $model = $model->toArray();
            $waybill->fill($model);
            $waybill->expenses = $selectedExpenses;
            $waybill->other_expenses = $selectedOtherExpenses;
            $waybill->prefill  = true;

        } else {
            $scaleAirports = ['' => ''];
        }

        $formOptions = array('route' => array('admin.air-waybills.store'), 'method' => 'POST', 'class' => 'form-horizontal form-waybill');

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('display_name', 'id')->toArray();

        $allExpenses = Expense::filterSource()->get();

        $otherExpenses = $allExpenses->filter(function($item){
            return $item->type == 'other';
        })->pluck('name', 'id')->toArray();

        $expenses = $allExpenses->filter(function($item){
            return $item->type != 'other';
        })->pluck('name', 'id')->toArray();

        $preFillModels = Model::filterSource()->orderBy('name')->pluck('name', 'id')->toArray();

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        return view('admin.awb.air_waybills.edit', compact('waybill', 'action', 'formOptions', 'providers', 'agents', 'scaleAirports', 'expenses', 'otherExpenses', 'preFillModels', 'goodsTypes'))->render();
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
    public function edit($id) {

        $action = 'Editar Carta de Porte Aéreo';

        $waybill = Waybill::with('sourceAirport', 'expenses')
                        ->filterSource()
                        ->findOrfail($id);

        $selectedExpenses = $selectedOtherExpenses = [];
        foreach ($waybill->expenses as $item) {

            $arr = [
                'expense' => $item->id,
                'price'   => $item->pivot->price
            ];

            if($item->type == 'other') {
                $selectedOtherExpenses[] = $arr;
            } else {
                $selectedExpenses[] = $arr;
            }
        }

        $waybill->expenses       = $selectedExpenses;
        $waybill->other_expenses = $selectedOtherExpenses;

        $formOptions = array('route' => array('admin.air-waybills.update', $waybill->id), 'method' => 'PUT', 'class' => 'form-horizontal form-waybill');

        $hawbs = Waybill::where('main_waybill_id', $waybill->id)->get();

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('display_name', 'id')->toArray();

        $allExpenses = Expense::filterSource()->get();

        $otherExpenses = $allExpenses->filter(function($item){
            return $item->type == 'other';
        })->pluck('name', 'id')->toArray();

        $expenses = $allExpenses->filter(function($item){
            return $item->type != 'other';
        })->pluck('name', 'id')->toArray();

        $providerCode = explode('-', $waybill->awb_no);
        $awbNo = explode(' ', @$providerCode[1]);
        $waybill->awb = [
            '1' => @$providerCode[0],
            '2' => @$awbNo[0],
            '3' => @$awbNo[1],
        ];

        $scaleIds = [];
        if(!empty($waybill->flight_scales)) {
            foreach ($waybill->flight_scales as $flyscale) {
                $scaleIds[] = $flyscale->airport;
            }
        }

        $scaleAirports = IataAirport::whereIn('code', $scaleIds)->pluck('airport', 'code')->toArray();

        $preFillModels = Model::filterSource()->orderBy('name')->pluck('name', 'id')->toArray();

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        return view('admin.awb.air_waybills.edit', compact('waybill', 'action', 'formOptions', 'providers', 'agents', 'scaleAirports', 'expenses', 'otherExpenses', 'preFillModels', 'hawbs', 'goodsTypes'))->render();
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

        $input['awb_no'] = @trim($input['awb'][1]) . '-' . trim(@$input['awb'][2]) . ' ' . trim(@$input['awb'][3]);

        $flightScales = [];
        $i = 0;
        foreach ($input['flight_scales'] as $scale) {
            if(!empty($scale['airport'])) {
                $flightScales[$i] = [
                    'airport'  => $scale['airport'],
                    'provider' => $scale['provider']
                ];

                $i++;
            }
        }

        $goods = [];
        $i = 0;
        $totalGoodsPrice = 0;
        $totalChargableWeight = 0;
        $totalWeight  = 0;
        $totalVolumes = 0;
        foreach ($input['goods'] as $good) {
            if(!empty($good['volumes'])) {
                $goods[$i] = [
                    'volumes'           => @$good['volumes'],
                    'unity'             => @$good['unity'],
                    'weight'            => @$good['weight'],
                    'chargeable_weight' => @$good['chargeable_weight'],
                    'rate_charge'       => @$good['rate_charge'],
                    'rate_class'        => @$good['rate_class'],
                    'rate_no'           => @$good['rate_no'],
                    'total'             => @$good['total'],
                ];

                $totalVolumes+= (int) @$good['volumes'];
                $totalWeight+= (float) @$good['weight'];
                $totalChargableWeight+= (float) @$good['chargeable_weight'];
                $totalGoodsPrice+= (float) @$good['total'];
                $i++;
            }
        }

        $input['volumes']           = $totalVolumes;
        $input['weight']            = $totalWeight;
        $input['chargable_weight']  = $totalChargableWeight;
        $input['total_goods_price'] = $totalGoodsPrice;
        $input['flight_scales']     = $flightScales;
        $input['goods']             = $goods;

        $waybill = Waybill::filterSource()->findOrNew($id);

        if ($waybill->validate($input)) {
            $waybill->fill($input);
            $waybill->source    = config('app.source');
            $waybill->user_id   = Auth::user()->id;
            $waybill->has_hawb  = false;
            $waybill->hawb_hash = null;
            $waybill->save();

            $waybill->expenses()->detach();
            $totalPrice = 0;
            $allExpenses = array_merge($input['other_expenses'], $input['expenses']);

            foreach ($allExpenses as $expense) {
                if(!empty(trim($expense['expense']))) {
                    $totalPrice+= (float) $expense['price'];
                    $waybill->expenses()->attach($expense['expense'], ['price' => $expense['price']]);
                }
            }

            $waybill->total_price = $totalPrice;

            /*
             * Store new HAWB
             */
            if(isset($input['hawb_hash']) && !empty($input['hawb_hash'])) {

                Waybill::where('hawb_hash', $input['hawb_hash'])
                    ->where('id', '<>', $waybill->id)
                    ->update([
                    'main_waybill_id'   => $waybill->id,
                    'source_airport'    => $waybill->source_airport,
                    'recipient_airport' => $waybill->source_airport,
                    'flight_no_1'       => $waybill->flight_no_1,
                    'flight_no_2'       => $waybill->flight_no_2,
                    'flight_no_3'       => $waybill->flight_no_3,
                    'flight_no_4'       => $waybill->flight_no_4,
                    'flight_scales'     => json_encode($waybill->flight_scales),
                    'agent_id'          => $waybill->agent_id,
                    'currency'          => $waybill->currency
                ]);
            }


            /*
             * Update house waybills if exists
             */
            $hawbs = Waybill::where('main_waybill_id', $waybill->id)->get();
            if(!$hawbs->isEmpty()) {
                $goods = [];
                $totalVolumes = $totalWeight = $totalGoodsPrice = $i = 0;
                foreach ($hawbs as $hawb) {

                    if($hawb->goods) {
                       foreach ($hawb->goods as $item) {
                           $goods[$i] = [
                               'volumes'           => $item->volumes,
                               'unity'             => $item->unity,
                               'weight'            => $item->weight,
                               'chargeable_weight' => $item->chargeable_weight,
                               'rate_charge'       => $item->rate_charge,
                               'rate_class'        => $item->rate_class,
                               'rate_no'           => $item->rate_no,
                               'total'             => $item->total,
                           ];

                           $totalVolumes+= (int) $item->volumes;
                           $totalWeight+= (float) $item->weight;
                           $totalGoodsPrice+= (float) $item->total;
                           $i++;
                       }
                    }

                    $hawb->save();
                }

                $waybill->goods     = $goods;
                $waybill->volumes   = $totalVolumes;
                $waybill->weight    = $totalWeight;
                $waybill->total_goods_price = $totalGoodsPrice;
                $waybill->has_hawb  = true;
                $waybill->save();
            }

            $waybill->save();


            // Print labels or transportation guide
            $print = $html = false;
            if($request->has('print_awb')) {
                $html  = view('admin.shipments.shipments.modals.popup_denied')->render();
                $print = route('admin.air-waybills.print.pdf', $waybill->id);
            }

            if(!isset($feedback) && empty($feedback)) {
                $feedback = 'Carta de porte aéreo gravado com sucesso.';
            }

            $result = [
                'result'   => true,
                'feedback' => $feedback,
                'print'    => $print,
                'html'     => $html
            ];

        } else {
            $result = [
                'result'    => false,
                'feedback'  => $waybill->errors()->first()
            ];
        }

        return Response::json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = Waybill::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a Carta de Porte.');
        }

        return Redirect::route('admin.air-waybills.index')->with('success', 'Carta de Porte. removida com successo.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $ids = explode(',', $request->ids);

        $result = Waybill::filterSource()->whereIn('id', $ids)->delete();

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

        $data = Waybill::filterSource()
                    ->isHAWB(false)
                    ->with('sourceAirport', 'recipientAirport', 'provider', 'customer', 'goodType')
                    ->select();

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter customer
        $value = $request->get('customer');
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter operator
        $value = $request->get('operator');
        if($request->has('operator')) {
            $data = $data->where('user_id', $value);
        }

        //filter agent
        $value = $request->get('agent');
        if($request->has('agent')) {
            $data = $data->where('agent_id', $value);
        }

        //filter goods type
        $value = $request->get('type');
        if($request->has('type')) {
            $data = $data->where('goods_type_id', $value);
        }

        //filter has hawb
        $value = $request->get('has_hawb');
        if($request->has('has_hawb')) {
            $data = $data->where('has_hawb', $value);
        }

        //filter source airport
        $value = $request->get('source_airport');
        if($request->has('source_airport')) {
            $data = $data->where('source_airport', $value);
        }

        //filter recipient airport
        $value = $request->get('recipient_airport');
        if($request->has('recipient_airport')) {
            $data = $data->where('recipient_airport', $value);
        }

        //filter date
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter billed
        $value = $request->billed;
        if($request->has('billed')) {
            if($value == 1) {
                $data = $data->whereNotNull('invoice_id');
            } else {
                $data = $data->whereNull('invoice_id');
            }
        }

        return Datatables::of($data)
            ->edit_column('date', function($row) {
                return view('admin.awb.air_waybills.datatables.awb', compact('row'))->render();
            })
            ->edit_column('title', function($row) {
                return view('admin.awb.air_waybills.datatables.title', compact('row'))->render();
            })
            ->edit_column('agent_id', function($row) {
                return view('admin.awb.air_waybills.datatables.agent', compact('row'))->render();
            })
            ->edit_column('sender_name', function($row) {
                return view('admin.awb.air_waybills.datatables.sender', compact('row'))->render();
            })
            ->edit_column('consignee_name', function($row) {
                return view('admin.awb.air_waybills.datatables.consignee', compact('row'))->render();
            })
            ->add_column('airport', function($row) {
                return view('admin.awb.air_waybills.datatables.airport', compact('row'))->render();
            })
            ->add_column('flight_no', function($row) {
                return view('admin.awb.air_waybills.datatables.flight', compact('row'))->render();
            })
            ->add_column('volumes', function($row) {
                return view('admin.awb.air_waybills.datatables.volumes', compact('row'))->render();
            })
            ->edit_column('total_price', function($row) {
                return view('admin.awb.air_waybills.datatables.total', compact('row'))->render();
            })
            ->edit_column('invoice_id', function($row) {
                return view('admin.awb.air_waybills.datatables.invoice', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.awb.air_waybills.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchAirport(Request $request) {

        $originalSearch = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $originalSearch) . '%';

        try {

            $airports = IataAirport::where(function($q) use($search, $originalSearch){
                    $q->where('code', $originalSearch)
                      ->orWhere('airport', 'LIKE', $search);
                })
                ->get(['code', 'airport', 'country']);

            if($airports) {

                $results = array();
                foreach($airports as $airport) {
                    $results[]=array('id'=> $airport->code, 'text' => $airport->code. ' - '.$airport->airport . ' (' . $airport->country . ')');
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum aeroporto encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Search senders on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {
        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $customers = Customer::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->take(10)
                ->get(['name', 'code', 'id']);

            if($customers) {

                $results = array();
                foreach($customers as $customer) {
                    $results[] = ['data' => $customer->id, 'value' => $customer->name];
                }

            } else {
                $results = ['Nenhum cliente encontrado.'];
            }

        } catch(\Exception $e) {
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * Return customer details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProvider(Request $request) {

        $bindings = [
            'id',
            'iata_no',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
        ];

        $provider = Provider::filterSource()
            ->select($bindings)
            ->findOrFail($request->id);


        $provider->address = $provider->address;

        if($provider->zip_code || $provider->city) {
            $provider->address.= '<br/>'.$provider->zip_code.' '.$provider->city. '<br/>' . $provider->country;
        }

        if($provider->phone) {
            $provider->address.= '<br/>PH:'.$provider->phone;
        }

        $provider->address = br2nl(strtoupper($provider->address));

        return Response::json($provider);
    }

    /**
     * Return customer details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCustomer(Request $request) {

        $bindings = [
            'id',
            'vat',
            'name',
            'agency_id',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'mobile'
        ];

        $customer = Customer::filterAgencies()
                ->select($bindings)
                ->findOrFail($request->id);

        $customer->phone = empty($customer->mobile) ? $customer->phone : $customer->mobile;
        $customer->phone = str_replace(' ', '', $customer->phone);

        $customer->address;

        if($customer->zip_code || $customer->city) {
            $customer->address.= '<br/>' . $customer->zip_code . ' ' . $customer->city;
            $customer->address.= '<br/>' . trans('country.' . $customer->country);
        }

        if($customer->phone || $customer->city) {
            $customer->address.= '<br/>PH:' . $customer->phone;
        }

        $customer->address = br2nl(strtoupper($customer->address));

        return Response::json($customer);
    }

    /**
     * Print Air Waybill document
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printAwb($id) {
        return Waybill::printAwb([$id]);
    }

    /**
     * Print House Air Waybill document
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printHawbs($id) {
        return Waybill::printHawbs([$id]);
    }

    /**
     * Print Air Waybill document
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printManifest($id) {
        return Waybill::printManifest([$id]);
    }

    /**
     * Print Air Waybill labels
     *
     * @param type $shipmentId
     * @return type
     */
    public function printLabels($id){
        return Waybill::printLabels([$id]);
    }

    /**
     * Print Air Waybill document
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printSummary($id) {
        return Waybill::printSummary([$id]);
    }

    /**
     * Get price
     *
     * @param type $shipmentId
     * @return type
     */
    public function getPrice(Request $request){

        $input = $request->all();

        $provider         = @$input['provider'];
        $airport          = @$input['recipientAirport'];
        //$airport          = @$input['sourceAirport'];
        //$recipientAirport = @$input['recipientAirport'];
        $weight           = (float) @$input['weight'];
        $taxableWeight    = (float) @$input['weightTaxable'];
        $volumes          = (int) @$input['volumes'];
        $expenses         = @$input['expenses'];
        $expenses         = empty($expenses) ? [] : $expenses;
        $result           = null;

        $expenses = Expense::whereIn('id', $expenses)->get();

        if(!$expenses->isEmpty()) {
            foreach ($expenses as $expense) {

                $pricesRoles = $expense->prices;

                foreach ($pricesRoles as $role) {
                    $totalPrice = 0;
                    $providerValid = $airportValid = true;

                    if(!empty($role->airport)) {
                        $airportValid = false;
                        if($role->airport == $airport) {
                            $airportValid = true;
                        }
                    }

                    if(!empty($role->provider)) {
                        $providerValid = false;
                        if($role->provider == $provider) {
                            $providerValid = true;
                        }
                    }

                    if($airportValid && $providerValid) {
                        $price      = $role->price;
                        $priceMin   = $role->price_min;
                        $totalPrice = $role->price;

                        //dd($totalPrice);
                        if($role->unity == 'volumes') {
                            $totalPrice = $volumes * $price;
                        }

                        if($role->unity == 'weight') {
                            $totalPrice = $weight * $price;
                        }

                        if($role->unity == 'taxable_weight') {
                            $totalPrice = $taxableWeight * $price;
                        }

                        $totalPrice = $totalPrice > $priceMin ? $totalPrice : $priceMin;

                        $result[] = [
                            'expense'    => $expense->id,
                            'totalPrice' => number_format(@$totalPrice, 2, '.', ''),
                        ];
                    }
                }
            }
        }

        if(empty($result)) {
            return [
                'expense'    => null,
                'totalPrice' => null,
            ];
        }

        return $result;
    }

    /**
     * Replicate Air Waybill document
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function replicate($id) {

        $waybill =  Waybill::findOrFail($id);

        $newWaybill = $waybill->replicate();
        $newWaybill->awb_no = '';
        $newWaybill->date   = date('Y-m-d');
        $newWaybill->title  = '';
        $newWaybill->save();

        foreach ($waybill->expenses as $expense) {
            $newWaybill->expenses()->attach($expense->pivot->waybill_expense_id, ['price' => $expense->pivot->price]);
        }

        return Redirect::route('admin.air-waybills.index')->with('success', 'Carta de porte duplicada com successo.');
    }
}
