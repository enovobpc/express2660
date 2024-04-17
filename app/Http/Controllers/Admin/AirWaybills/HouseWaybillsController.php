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
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Response, File;

class HouseWaybillsController extends \App\Http\Controllers\Admin\Controller {

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
/*    public function index() {
    }*/

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Nova HAWB';

        $waybill = new Waybill();

        $formOptions = array('route' => array('admin.air-waybills.hawb.store'), 'method' => 'POST', 'class' => 'form-horizontal form-hawb');

        $airports = IataAirport::pluck('airport', 'code')->toArray();

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('name', 'id')->toArray();

        $allExpenses = Expense::filterSource()->get();

        $otherExpenses = $allExpenses->filter(function($item){
            return $item->type == 'other';
        })->pluck('name', 'id')->toArray();

        $expenses = $allExpenses->filter(function($item){
            return $item->type != 'other';
        })->pluck('name', 'id')->toArray();

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        $selectedExpenses = [];

        $selectedOtherExpenses = [];

        $waybill->expenses = $selectedExpenses;
        $waybill->other_expenses = $selectedOtherExpenses;

        $hash     = $request->get('hash');
        $parentId = $request->get('parent');

        $parentAWB = Waybill::find($parentId);

        $data = compact(
            'waybill',
            'action',
            'formOptions',
            'hash',
            'parentId',
            'providers',
            'airports',
            'agents',
            'scaleAirports',
            'expenses',
            'otherExpenses',
            'goodsTypes',
            'parentAWB'
        );

        return view('admin.awb.air_waybills.edit_hawb', $data)->render();
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

        $selectedExpenses = [];
        foreach ($waybill->expenses as $item) {
            $selectedExpenses[] = [
                'expense' => $item->id,
                'price'   => $item->pivot->price
            ];
        }

        $waybill->expenses = $selectedExpenses;

        $formOptions = array('route' => array('admin.air-waybills.hawb.update', $waybill->id), 'method' => 'PUT', 'class' => 'form-horizontal form-hawb');

        $airports = IataAirport::pluck('airport', 'code')->toArray();

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('name', 'id')->toArray();

        $expenses = Expense::filterSource()->pluck('name', 'id')->toArray();

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

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        $parentId = $waybill->main_waybill_id;

        return view('admin.awb.air_waybills.edit_hawb', compact('waybill', 'action', 'formOptions', 'parentId', 'providers', 'airports', 'agents', 'scaleAirports', 'expenses', 'goodsTypes'))->render();
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

        if(empty($input['main_waybill_id'])) {
            unset($input['main_waybill_id']);
        }

        $goods = [];
        $i = 0;
        $totalGoodsPrice = 0;
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
                $totalGoodsPrice+= (float) @$good['total'];
                $i++;
            }
        }

        $input['volumes']           = $totalVolumes;
        $input['weight']            = $totalWeight;
        $input['total_goods_price'] = $totalGoodsPrice;
        $input['goods']             = $goods;

        $waybill = Waybill::filterSource()->findOrNew($id);

        if ($waybill->validate($input)) {
            $waybill->fill($input);
            $waybill->source = config('app.source');
            $waybill->user_id = Auth::user()->id;
            $waybill->save();

            if(isset($input['main_waybill_id']) && !empty($input['main_waybill_id'])) {
                $mainWaybill = $waybill::findOrFail($input['main_waybill_id']);
                $waybill->source_airport = $mainWaybill->source_airport;
                $waybill->recipient_airport = $mainWaybill->source_airport;
                $waybill->flight_no_1   = $mainWaybill->flight_no_1;
                $waybill->flight_no_2   = $mainWaybill->flight_no_2;
                $waybill->flight_no_3   = $mainWaybill->flight_no_3;
                $waybill->flight_no_4   = $mainWaybill->flight_no_4;
                $waybill->flight_scales = $mainWaybill->flight_scales;
                $waybill->agent_id      = $mainWaybill->agent_id;
                $waybill->currency      = $mainWaybill->currency;
                $waybill->save();

                $hawbs = Waybill::where('main_waybill_id', $waybill->main_waybill_id)->get();

                if($hawbs->count() > 0) {
                    $mainWaybill->has_hawb = true;
                } else {
                    $mainWaybill->has_hawb = false;
                }
                $mainWaybill->save();

            } else {
                $hawbs = Waybill::where('hawb_hash', $waybill->hawb_hash)->get();
            }


            $result = [
                'result'   => true,
                'feedback' => 'Carta de porte aéreo gravado com sucesso.',
                'html'     => view('admin.awb.air_waybills.partials.hawb', compact('hawbs'))->render()
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
                        ->first();

        if ($result) {
            $result = [
                'result'    => true,
                'feedback'  => 'HAWB removida com sucesso',
            ];
        } else {
            $result = [
                'result'    => false,
                'feedback'  => 'Não foi possível eliminar a HAWB.'
            ];
        }

        return Response::json($result);
    }
}
