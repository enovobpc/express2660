<?php

namespace App\Http\Controllers\Admin\Sms;

use App\Models\Core\SmsPrice;
use App\Models\Sms\Pack;
use App\Models\Sms\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Response, Auth, Setting;

class PacksController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'sms';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',sms']);
        validateModule('sms');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $sms = new Pack();

        $packsOptions = SmsPrice::orderBy('qty', 'asc')->get();

        $action = 'Adquirir pacote de SMS';

        $formOptions = array('route' => array('admin.sms.packs.store'), 'method' => 'POST', 'class' => 'form-sms');

        $data = compact(
            'sms',
            'action',
            'formOptions',
            'packsOptions'
        );

        return view('admin.sms.partials.packs.create', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        Sms::flushCache(Sms::CACHE_TAG);
        Pack::flushCache(Pack::CACHE_TAG);

        $input = $request->all();

        $pack = new Pack();

        $selectedPack = SmsPrice::find($input['pack_id']);
        $title        = $selectedPack->title;
        $priceUn      = $selectedPack->price_un;

        $subtotal     = $priceUn * $selectedPack->qty;
        $vat          = $selectedPack->vat;
        $total        = $subtotal * (1 + ($vat/100));

        if ($pack->validate($input)) {
            $pack->total_sms     = $selectedPack->qty;
            $pack->remaining_sms = $selectedPack->qty;
            $pack->buy_by        = Auth::user()->id;
            $pack->price_un      = $priceUn;
            $pack->is_active     = 0;
            $pack->source        = config('app.source');
            $pack->save();


            $url = "https://enovo.pt/api/testkey/proformas/store";

            $fields = [
                'source'       => config('app.source'),
                'total'        => $total,
                'invoice_type' => 'invoice-receipt',
                'add_mb'       => '1',
                'items' => [
                    ['title' => $title]
                ]
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($fields),
            ));

            $response = json_decode(curl_exec($curl), true);

            curl_close($curl);

            if(empty($response['error'])) {
                $pack->reference = @$response['proforma']['payment']['reference'];
                $pack->entity    = @$response['proforma']['payment']['entity'];
                $pack->save();
            } else {

                $pack->forceDelete();

                return response()->json([
                    'result'   => false,
                    'feedback' => $response['message']
                ]);
            }

            return response()->json([
                'result' => true,
                'html'   => view('admin.sms.partials.packs.reference_mb', compact('title', 'total', 'pack'))->render()
            ]);
        }

        return response()->json([
            'result' => true,
            'html'   => $pack->errors()->first()
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {

        $pack = Pack::filterSource()->findOrFail($id);

        $action = 'Editar pacote de SMS';

        $formOptions = array('route' => array('admin.sms.packs.update', $pack->id), 'method' => 'PUT', 'class' => 'form-sms');

        $data = compact(
            'sms',
            'action',
            'formOptions',
            'pack'
        );

        return view('admin.sms.partials.packs.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Sms::flushCache(Sms::CACHE_TAG);
        Pack::flushCache(Pack::CACHE_TAG);

        $input = $request->all();
        $input['is_active'] = $request->get('is_active', false);

        $pack = Pack::filterSource()->findOrNew($id);

        if ($pack->validate($input)) {
            $pack->fill($input);
            $pack->source = config('app.source');
            $pack->save();

            return Redirect::back()->withInput()->with('success', 'Pacote alterado com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $pack->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Pack::flushCache(Pack::CACHE_TAG);

        $result = Pack::filterSource()
                    ->whereId($id)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a viatura');
        }

        return Redirect::route('admin.sms.index')->with('success', 'SMS eliminado com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Pack::flushCache(Pack::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Pack::filterSource()
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

        $data = Pack::filterSource()
                    ->select();

        return Datatables::of($data)
            ->add_column('total', function($row) {
                return money(@$row->price_un * $row->total_sms * ( 1 + (Setting::get('vat_rate_normal')/100)), '€');
            })
            ->edit_column('buy_by', function($row) {
                return @$row->user->name;
            })
            ->edit_column('price_un', function($row) {
                return money($row->price_un, '€', 3);
            })
            ->edit_column('reference', function($row) {
                return view('admin.sms.datatables.packs.reference', compact('row'))->render();
            })
            ->edit_column('is_active', function($row) {
                return view('admin.sms.datatables.packs.is_active', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.sms.datatables.packs.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
