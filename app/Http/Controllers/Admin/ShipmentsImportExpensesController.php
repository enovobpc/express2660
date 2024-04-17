<?php
namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\CustomerWebservice;
use App\Models\ShipmentExpense;
use App\Models\ShippingExpense;
use App\Models\Webservice\Envialia;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Html, Auth, Response;
use Excel;
use Cache;
use Date;
use View;
use App\Models\Shipment;
use App\Models\WebserviceMethod;

class ShipmentsImportExpensesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

    /**
     * Store last row of each iteration
     * 
     * @var type 
     */
    protected $lastRow = null;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }

    /**
     * Show import modal
     *
     * @return string
     */
    public function importModal() {
        return view('admin.shipments.shipments.modals.expenses_import')->render();
    }

    /**
     * Show import modal
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request) {

      /*  $method = WebserviceMethod::find($request->import_method);

        if(empty($method)) {
            return Redirect::back()->with('error', 'Nenhum método de importação detectado.');
        }

        $request->import_method = $method->method;
        */

        return $this->{$request->import_method}($request);
    }


    
    /**
     * Import Envialia file
     *
     * @return \Illuminate\Http\Response
     */
    public function envialia(Request $request) {

        $mapAttrs = config('shipments_expenses_mapping.'.$request->import_method.'.fields');

        $excel = Excel::load($request->file->getRealPath());

        if(!$excel) {
            return Redirect::back()->with('error', 'O ficheiro carregado não é suportado.');
        }

        /*if(!$excel->first()->has('agencia_que_soporta') || !$excel->first()->has('age_soporta')) {
            if($request->ajax()) {
                return Response::json([
                    'result'      => false,
                    'feedback'    => 'O ficheiro carregado não é um ficheiro da Enviália.',
                    'totalErrors' => 0,
                    'errors'      => null
                ]);
            }
            return Redirect::back()->with('error', 'O ficheiro carregado não é um ficheiro da Enviália.');
        }*/

        $errors = [];
        $totalSuccess = 0;

        $allExpenses = ShippingExpense::filterSource()->select(['id', 'code', 'price'])->get();

        Excel::load($request->file->getRealPath(), function($reader)  use($mapAttrs, $request, &$allExpenses, &$errors, &$totalSuccess){

            $reader->each(function($row) use($mapAttrs, $request, &$allExpenses, &$errors, &$totalSuccess) {

                $row = mapArrayKeys($row->toArray(), $mapAttrs);

                $row['provider_tracking_code'] = trim($row['provider_tracking_code']);
                $row['provider_collection_tracking_code'] = trim($row['provider_collection_tracking_code']);

                $row['provider_tracking_code'] = empty($row['provider_collection_tracking_code']) ? $row['provider_tracking_code'] : $row['provider_collection_tracking_code'];

                $row['provider_tracking_code'] = strlen($row['provider_tracking_code']) == 14 ? '00' . $row['provider_tracking_code'] : $row['provider_tracking_code'];
                $row['provider_tracking_code'] = strlen($row['provider_tracking_code']) == 16 ? substr($row['provider_tracking_code'], 6) : substr($row['provider_tracking_code'], 12);

                if(empty($row['canceled'])) {
                    $row['canceled'] = false;
                } else {
                    $row['canceled'] = (strtolower($row['canceled']) == 'false')  ? false : true;
                }
                $row['qty'] = intval($row['qty']);

                if(!$row['canceled']) {
                    $recolhaFalhada = false;
                    if(empty($row['provider_tracking_code']) || $row['provider_tracking_code'] == false) { //RECOLHAS FALHADAS, O CÓDIGO DO ENVIO VEM VAZIO
                        $row['provider_tracking_code'] = $row['provider_collection_tracking_code']; //ATRIBUI O CODIGO DA RECOLHA AO PROVIDER TRACKING CODE
                        $recolhaFalhada = true;
                    }

                    $shipment = Shipment::with('customer')
                                    ->where('provider_tracking_code', $row['provider_tracking_code'])
                                    ->first();

                    if($recolhaFalhada && !$shipment) {
                        //VAI SINCRONIZAR COM A ENVIÁLIA E IMPORTAR A RECOLHA FALHADA UMA VEZ QUE NÃO ESTÁ NO PROGRAMA
                        $user   = $row['customer_code'];
                        $agency = $row['agency_id'];
                        try {
                            $agencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();
                            $loginDetails = CustomerWebservice::whereHas('customer', function($q) use($user, $agencies){
                                $q->where('code', $user);
                                $q->whereIn('agency_id', $agencies);
                            })
                            ->where('method', 'envialia')
                            ->where('user', $user)
                            ->where('agency', $agency)
                            ->first();

                            $envialia = new Envialia('', '', '');
                            $shipment = $envialia->importCollection($loginDetails, $row['provider_tracking_code']);

                            if($shipment != 1 || $shipment != true || !is_object($shipment) || !is_array($shipment)) {
                                $errors[] = [
                                    'code'      => $row['provider_tracking_code'],
                                    'message'   => $shipment
                                ];

                                $shipment = false;
                            }
                        } catch (\Exception $e) {
                            $errors[] = [
                                'code'      => $row['provider_tracking_code'],
                                'message'   => 'Envio não encontrado na plataforma. Não foi possível obter pelo webservice'
                            ];
                        }
                    }

                    if(empty($shipment)) {
                        $errors[] = [
                            'code'      => $row['provider_tracking_code'],
                            'message'   => 'Envio não encontrado na plataforma'
                        ];
                    } else {
                        $expense = $allExpenses->filter(function($item) use($row){
                                                return $item->code == $row['expense_code'];
                                        })->first();

                        if(empty($expense)) {
                            $errors[] = [
                                'code'      => $row['provider_tracking_code'],
                                'message'   => 'A plataforma não reconhece o encargo com o código '.$row['expense_code'].'. Verificar mapeamento.'
                            ];

                        } else {

                            if(!empty($row['qty'])) {

                                $zone = Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country);

                                $shipmentExpenses = ShipmentExpense::firstOrNew([
                                    'provider_code' => $row['provider_code']
                                ]);

                                //$price = $expense->getPrice($shipment, $shipment->customer, $row['qty']);

                                $shipmentExpenses->shipment_id   = $shipment->id;
                                $shipmentExpenses->expense_id    = $expense->id;
                                $shipmentExpenses->provider_code = $row['provider_code'];
                                $shipmentExpenses->cost_price    = $row['cost_price'];
                               /* $shipmentExpenses->qty           = $price['qty'];
                                $shipmentExpenses->price         = $price['price'];
                                $shipmentExpenses->subtotal      = $price['subtotal'];*/
                                $shipmentExpenses->date          = $row['date'];

                                $shipmentExpenses->save();

                                //update shipment total_expenses field
                                ShipmentExpense::updateShipmentTotal($shipment->id);
                            }

                            $totalSuccess++;
                        }
                    }

                }
            });
        });

        if($request->ajax()) {

            $totalErrors = count($errors);

            $result = empty($totalErrors) ? true : false;

            return Response::json([
                'result'      => true,
                'feedback'    => 'Ficheiro importado com sucesso.',
                'html'        => view('admin.shipments.shipments.partials.import_expenses_errors', compact('errors', 'totalSuccess', 'totalErrors'))->render(),
                'totalErrors' => $totalErrors
            ]);
        }

        return Redirect::back()->with('success', 'Ficheiro importado com sucesso.');

    }
}