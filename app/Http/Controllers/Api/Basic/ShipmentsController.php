<?php

namespace App\Http\Controllers\Api\Basic;

use App\Models\Agency;
use App\Models\FileRepository;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Log, DB, Date, File;

class ShipmentsController extends \App\Http\Controllers\Admin\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.api_docs';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function checkAuth(Request $request)
    {

        $allowedTokens = [
            'beiTrIeLb75vlm92v470M79Cw68ebrv11ZTin1CXxUOiq5LFRYSi31r7dsbfOz6', //moove logistica
            'beiTrIeLb75vlm92v470M79Cw68ebrv11ZTin1CXxUOiq5LFRYSi31r7dsbfOz6eLb75vZTin1CXxUn1CXxUOiq5LFRYSi31vxK82'  //atrans - invictacargo
        ];

        if (in_array($request->get('token'), $allowedTokens)) {
            return true;
        }

        return false;
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request)
    {

        if (!$this->checkAuth($request)) {
            return $this->responseError('shipments', '-1', 'Token inválido');
        }

        $date  = $request->get('date');
        $cdate = $request->get('cdate');
        $trk   = $request->get('trackings');
        $limit = $request->get('limit', 1000);

        if (empty($date) && empty($trk) && empty($cdate)) {
            return $this->responseError('shipments', '-1', 'Obrigatório indicar a data ou codigo do envio');
        }

        $shipments = Shipment::with('pack_dimensions')
            ->with(['service' => function ($q) {
                $q->select([
                    'id',
                    DB::raw('display_code as code'),
                    'name'
                ]);
            }]);

        if (config('app.source') != 'invictacargo') {
            $shipments = $shipments->with(['service' => function ($q) {
                $q->select('id', 'display_code', 'name');
            }])
            ->with(['status' => function ($q) {
                $q->select('id', 'name');
            }])
            ->with(['provider' => function ($q) {
                $q->select('id', 'name');
            }])
            ->with(['customer' => function ($q) {
                $q->select('id','code','vat','name');
            }]);
        }


        if (!empty($date)) {
            $shipments = $shipments->where('date', $date);
        }

        if (!empty($cdate)) {
            $shipments = $shipments->where('created_at', '>=', $cdate);
        }

        if (!empty($trk)) {
            $trks = explode(',', $trk);
            $shipments = $shipments->whereIn('tracking_code', $trks);
        }

        if ($limit) {
            $shipments = $shipments->take($limit);
        }


        $bindings = [
            'tracking_code',
            'reference',
            'reference2',
            'reference3',
            'sender_name',
            'sender_address',
            'sender_zip_code',
            'sender_state',
            'sender_city',
            'sender_country',
            'recipient_name',
            'recipient_address',
            'recipient_zip_code',
            'recipient_state',
            'recipient_city',
            'recipient_country',
            'date',
            'volumes',
            'weight',
            'volumetric_weight',
            'fator_m3',
            'kms',
            'shipping_date',
            'delivery_date',
            'payment_at_recipient',
            'charge_price',
            'complementar_services',
            DB::raw('total_price as shipping_price'),
            DB::raw('total_expenses as expenses_price'),
            DB::raw('0.00 as total_fuel'),
            'provider_id',
            'status_id',
            'service_id',
            'is_collection',
            'obs',
            'created_at'
        ];

        if (config('app.source') == 'invictacargo') {
            $bindings = [
                'customer_id',
                'tracking_code',
                DB::raw('is_collection as is_pickup'),
                'reference',
                'sender_name',
                'sender_address',
                'sender_zip_code',
                'sender_city',
                'sender_country',
                'recipient_name',
                'recipient_address',
                'recipient_zip_code',
                'recipient_city',
                'recipient_country',
                'volumes',
                'weight',
                DB::raw('fator_m3 as m3'),
                'kms',
                'service_id',
                DB::raw('created_at as creation_date'),
                'shipping_date',
                'delivery_date'
            ];
        }

        $shipments = $shipments->get($bindings);


        return response($shipments, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Get all history
     *
     * @param Request $request
     * @return mixed
     */
    public function history(Request $request)
    {


        if (!$this->checkAuth($request)) {
            return $this->responseError('shipments', '-1', 'Token inválido');
        }

        $date = $request->get('trackings');
        if (empty($date)) {
            return $this->responseError('shipments', '-1', 'Obrigatório indicar aos trackings');
        }

        $tracking = explode(',', $request->get('trackings'));

        $searchField = 'tracking_code';
        /*if(in_array($customer->id, $this->delnextCustomers)) {
            $searchField = 'reference';
        }*/

        $bindings = [
            'created_at',
            'status_id',
            'incidence_id',
            'operator_id',
            'agency_id',
            'city',
            'obs',
            'receiver',
            'signature',
            'filepath',
            'latitude',
            'longitude',
        ];

        $sourceAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

        $shipments = Shipment::with(['history' => function ($q) use ($bindings) {
            $q->with(['status' => function ($q) {
                $q->get(['id', 'name']);
            }]);
            $q->with(['incidence' => function ($q) {
                $q->get(['id', 'name']);
            }]);
            $q->with(['operator' => function ($q) {

                $fields = ['id', 'code'];

                if (Setting::get('tracking_show_operator_name')) {
                    $fields[] = 'fullname';
                }

                if (Setting::get('tracking_show_operator_phone')) {
                    $fields[] = 'professional_mobile';
                }

                $q->select($fields);
            }]);
            $q->orderBy('created_at');
            $q->get($bindings);
        }])
            //->where('customer_id', $customer->id)
            ->whereIn('agency_id', $sourceAgencies)
            ->whereIn($searchField, $tracking)
            ->get(['id', $searchField, 'operator_id']);

        if ($shipments->isEmpty()) {
            return $this->responseError('update', '-001', 'Envio não encontrado. Verifique os códigos dos envios.');
        }

        $histories = [];
        foreach ($shipments as $shipment) {
            $shipmentHistory = $shipment->history;
            /*if (!$shipmentHistory) {
                return $this->responseError('history', '-001');
            }*/

            $history = [];
            foreach ($shipmentHistory as $item) {
                $history[] = [
                    'date'          => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                    'status'        => @$item->status->name,
                    'status_id'     => $item->status_id,
                    'incidence'     => @$item->incidence->name,
                    'incidence_id'  => $item->incidence_id,
                    //'agency_id'     => $item->agency_id,
                    'receiver'      => $item->receiver,
                    'signature'     => $item->signature,
                    'attachment'    => $item->filepath ? asset($item->filepath) : null,
                    'obs'           => trim($item->obs),
                    'city'          => $item->city,
                    'latitude'      => $item->latitude,
                    'longitude'     => $item->longitude,
                    'tracking_code' => null,
                    'full_name_operator'   => @$item->operator->fullname ?? null,
                    'phone_operator'       => @$item->operator->professional_mobile ?? null,
                ];
            }

            $histories[$shipment->{$searchField}] = $history;
        }

        return response($histories, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {

        $headers     = $request->header();
        $inputFields = $request->toArray();


        if (config('app.source') == 'ttmb') {

            //input fields tem os dados da chamada
            return $this->storeDecathlon($inputFields);
        }

        //if($headers['api-authorization-key'][0] == '4CBCA3DF-FD21-40C0-A917-AEB1BC205692') { //pesamatrans
        //api-authorization-key : 4CBCA3DF-FD21-40C0-A917-AEB1BC205692
        //api-client-authorization-key :544F320B-DF3A-49DB-81F8-3F21F74E6D43
        //api-user-authorization-key :A03BF2ED-C2E2-4293-B4FF-C3667F102A52

        //api-key invicta = 44BCDFA47-8AD3-4FEC-BBAC-5EA75AA50291

        if ($request->has('id')) {
            /*Mail::raw($request->fullUrl().' -> '.print_r($inputFields, true), function($message) {
                    $message->to('paulo.costa@enovo.pt')
                        ->subject('RESPONSE');
                });*/

            return $this->getSjosePesamatrans($request->get('id'));
        } else {

            if(config('app.source') == 'pesamatrans') {
                return $this->storeSjosePesamatrans($inputFields);
            } elseif(config('app.source') == 'invictacargo') {
                return $this->storeSjoseInvictacargo($inputFields);
            }
        }

        //}

        return response()->json([
            'result'   => false,
            'feedback' => 'Chave API inválida'
        ]);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDecathlon($inputData)
    {


        $shipment = new Shipment();
        $shipment->xxxx = $inputData['xxxx'];
        //continuar....


        $requestData = new Request($shipment->toArray());
        $controller  = new \App\Http\Controllers\Api\Partners\ShipmentsController();
        $response    = $controller->store($requestData);
        $response    = json_decode($response->getContent(), true);

        //tratar a response
        $results[] = [
            'eesult'   => true,
            'feedback' => ''
        ];

        return response()->json($results);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSjosePesamatrans($inputData)
    {

        try {

            $results = [];
            foreach ($inputData as $key => $shipmentData) {

                $volumes = 0;
                $weight  = 0;
                $packTypes = [];
                $packDims  = [];
                foreach ($shipmentData['linhas'] as $item) {
                    $volumes += @$item['quantidade'];
                    $weight = @$item['peso'];
                    $packTypes[] = @$item['embalagem_Codigo'];

                    $packDims[] = [
                        'pack_type' => 'box',
                        'qty'       => (int) @$item['quantidade'],
                        'weight'    => (float) @$item['peso'],
                        'length'    => 0.01,
                        'height'    => 0.01,
                        'width'     => 0.01,
                    ];
                }

                $volumes > 0.00 ? $volumes : 1;
                $weight > 0.00 ? $weight : 1;


                $date = new Date($shipmentData['dataCarga']);

                //$shipmentData['tipoTransporte_Codigo'];
                //$shipmentData['dataDescarga'];
                //$shipmentData['cliente_Codigo'];
                //$shipmentData['expedidor_Codigo'];

                $shipment = new Shipment();
                $shipment->date             = $date->format('Y-m-d');
                $shipment->start_hour       = $date->format('H:i');
                $shipment->customer         = '342';
                $shipment->service          = 'BID';


                $shipment->sender_vat       = $shipmentData['expedidor_NIF'];
                $shipment->sender_name      = $shipmentData['expedidor_Nome'];
                $shipment->sender_address   = $shipmentData['moradaCarga_Morada'];
                $shipment->sender_zip_code  = $shipmentData['moradaCarga_CodPostal'];
                $shipment->sender_city      = $shipmentData['moradaCarga_Localidade'];
                $shipment->sender_country   = 'pt';
                $shipment->sender_phone     = '910000000';

                $shipment->recipient_vat        = $shipmentData['destinatario_NIF'];
                $shipment->recipient_name       = $shipmentData['destinatario_Nome'];
                $shipment->recipient_address    = $shipmentData['moradaDescarga_Morada'];
                $shipment->recipient_zip_code   = $shipmentData['moradaDescarga_CodPostal'];
                $shipment->recipient_city       = $shipmentData['moradaDescarga_Localidade'];
                $shipment->recipient_country    = 'pt';
                $shipment->recipient_phone      = '910000000';
                $shipment->reference            = $shipmentData['servico_RefPedido_Cliente'];
                $shipment->reference2           = $shipmentData['codigoAT_GuiaRemessa'];
                $shipment->reference3           = $shipmentData['idMensagem'];
                $shipment->obs                  = 'GR' . $shipmentData['codigoAT_GuiaRemessa'] . ' ' . $shipmentData['observacoes'];
                $shipment->charge_price         = $shipmentData['valorReembolso'] > 0.00 ? $shipmentData['valorReembolso'] : null;
                $shipment->volumes              = $volumes;
                $shipment->weight               = $weight;

                $shipment->dimensions = $packDims;
                // dd($shipment->toArray());

                $requestData = new Request($shipment->toArray());
                $controller = new \App\Http\Controllers\Api\Partners\ShipmentsController();
                $response = $controller->store($requestData);
                $response = json_decode($response->getContent(), true);

                $results[] = [
                    'Result'    => (int) substr(@$response['tracking_code'], 3),
                    'MessageId' => $shipment->reference3
                ];
            }

            $response = [
                'Success' => true,
                'Message' => @$results[0]['Result'],
                'Data' => $results
            ];

            return response()->json($response);
        } catch (\Exception $e) {

            return response()->json([
                'Success' => false,
                'Message' => $e->getMessage() . ' ' . $e->getLine(),
                'Data' => []
            ]);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSjoseInvictacargo($inputData)
    {

        try {

            $results = [];
            foreach ($inputData as $key => $shipmentData) {

                $volumes = 0;
                $weight  = 0;
                $packTypes = [];
                $packDims  = [];
                foreach ($shipmentData['linhas'] as $item) {
                    $volumes += @$item['quantidade'];
                    $weight = @$item['peso'];
                    $packTypes[] = @$item['embalagem_Codigo'];

                    $packDims[] = [
                        'pack_type' => 'box',
                        'qty'       => (int) @$item['quantidade'],
                        'weight'    => (float) @$item['peso'],
                        'length'    => 0.01,
                        'height'    => 0.01,
                        'width'     => 0.01,
                    ];
                }

                $volumes > 0.00 ? $volumes : 1;
                $weight > 0.00 ? $weight : 1;


                $date = new Date($shipmentData['dataCarga']);

                //$shipmentData['tipoTransporte_Codigo'];
                //$shipmentData['dataDescarga'];
                //$shipmentData['cliente_Codigo'];
                //$shipmentData['expedidor_Codigo'];

                $shipment = new Shipment();
                $shipment->date             = $date->format('Y-m-d');
                $shipment->start_hour       = $date->format('H:i');
                $shipment->customer         = '25';
                $shipment->service          = 'BID';


                $shipment->sender_vat       = $shipmentData['expedidor_NIF'];
                $shipment->sender_name      = $shipmentData['expedidor_Nome'];
                $shipment->sender_address   = $shipmentData['moradaCarga_Morada'];
                $shipment->sender_zip_code  = $shipmentData['moradaCarga_CodPostal'];
                $shipment->sender_city      = $shipmentData['moradaCarga_Localidade'];
                $shipment->sender_country   = 'pt';
                $shipment->sender_phone     = '910000000';

                $shipment->recipient_vat        = $shipmentData['destinatario_NIF'];
                $shipment->recipient_name       = $shipmentData['destinatario_Nome'];
                $shipment->recipient_address    = $shipmentData['moradaDescarga_Morada'];
                $shipment->recipient_zip_code   = $shipmentData['moradaDescarga_CodPostal'];
                $shipment->recipient_city       = $shipmentData['moradaDescarga_Localidade'];
                $shipment->recipient_country    = 'pt';
                $shipment->recipient_phone      = '910000000';
                $shipment->reference            = $shipmentData['servico_RefPedido_Cliente'];
                $shipment->reference2           = $shipmentData['codigoAT_GuiaRemessa'];
                $shipment->reference3           = $shipmentData['idMensagem'];
                $shipment->obs                  = 'GR' . $shipmentData['codigoAT_GuiaRemessa'] . ' ' . $shipmentData['observacoes'];
                $shipment->charge_price         = $shipmentData['valorReembolso'] > 0.00 ? $shipmentData['valorReembolso'] : null;
                $shipment->volumes              = $volumes;
                $shipment->weight               = $weight;

                $shipment->dimensions = $packDims;
                // dd($shipment->toArray());

                $requestData = new Request($shipment->toArray());
                $controller = new \App\Http\Controllers\Api\Partners\ShipmentsController();
                $response = $controller->store($requestData);
                $response = json_decode($response->getContent(), true);

                $success = false;
                if(@$response['tracking_code']) {
                    $success = true;
                }

                $results[] = [
                    'Result'    => (int) substr(@$response['tracking_code'], 3),
                    'MessageId' => $shipment->reference3
                ];
            }

            $response = [
                'Success' => $success,
                'Message' => @$results[0]['Result'],
                'Data' => $results
            ];

            return response()->json($response);
        } catch (\Exception $e) {

            return response()->json([
                'Success' => false,
                'Message' => $e->getMessage() . ' ' . $e->getLine(),
                'Data' => []
            ]);
        }
    }

    /**
     * @param Request $request
     */
    public function get(Request $request)
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSjosePesamatrans($trk)
    {


        $bindings = [
            'id',
            'service_id',
            'status_id',
            'route_id',
            DB::raw('tracking_code as ID'),
            DB::raw('tracking_code as Codigo'),
            DB::raw('sender_vat as Expedidor_NIF'),
            DB::raw('sender_name as Expedidor_Nome'),
            DB::raw('sender_address as MoradaCarga_Morada'),
            DB::raw('sender_zip_code as MoradaCarga_CodPostal'),
            DB::raw('sender_city as MoradaCarga_Localidade'),

            DB::raw('recipient_name as Destinatario_Nome'),
            DB::raw('recipient_vat as Destinatario_NIF'),
            DB::raw('recipient_address as MoradaDescarga_Morada'),
            DB::raw('recipient_zip_code as MoradaDescarga_CodPostal'),
            DB::raw('recipient_city as MoradaDescarga_Localidade'),
            DB::raw('reference as Servico_RefPedido_Cliente'),
            DB::raw('reference2 as CodigoAT_GuiaRemessa'),
            DB::raw('reference3 as IdMensagem'),
            DB::raw('charge_price as ValorReembolso'),
            DB::raw('obs as Observacoes'),
            DB::raw('vehicle as Veiculo_Matricula'),
            DB::raw('shipping_date as DataCarga'),
            DB::raw('delivery_date as DataDescarga'),
        ];

        $shipment = Shipment::where('tracking_code', 'like', '%' . $trk)
            ->first($bindings);


        /*
        Mail::raw($request->fullUrl().' -> '.print_r($inputFields, true), function($message) {
            $message->to('paulo.costa@enovo.pt')
                ->subject('RESPONSE');
        });*/

        /*
        Mail::raw(print_r($shipment->toArray(), true), function($message) {
            $message->to('paulo.costa@enovo.pt')
                ->subject('RESPONSE');
        });*/

        if (empty($shipment)) {
            return response()->json([]);
        }

        $packDimensions = [];
        foreach ($shipment->pack_dimensions as $dimension) {
            $packDimensions[] = [
                "Quantidade" => (int) $dimension->qty,
                "Embalagem_Codigo" => "VOL",
                "TipoMercadoria_Nome" => "",
                "TipoMercadoria_Codigo" => "",
                "TipoMercadoria_Lote" => "",
                "Peso" => (float) $dimension->weight
            ];
        }

        $shipmentArr = $shipment->toArray();
        unset($shipmentArr['id'], $shipmentArr['pack_dimensions'], $shipmentArr['service_id'], $shipmentArr['status_id'], $shipmentArr['route_id']);
        $shipmentArr['ID'] = (int) substr($shipmentArr['ID'], 3);
        $shipmentArr['TipoTransporte_Codigo'] = "BID"; //"001"
        $shipmentArr['TipoServico_Codigo']    = "";
        $shipmentArr['Cliente_Codigo']        = "JAI";
        $shipmentArr['Expedidor_Codigo']      = "JAI";
        $shipmentArr['Destinatario_Codigo']   = "";
        $shipmentArr['Motorista_Nome']    = @$shipment->operator->name;
        $shipmentArr['RotaCarga_Nome']    = @$shipment->pickup_route->name ?? 'COIMBRA';
        $shipmentArr['RotaDescarga_Nome'] = @$shipment->delivery_route->name ?? 'DISTRITAL';
        $shipmentArr['Estado'] = @$shipment->status->name;
        $shipmentArr['Documentos'] = [];
        $shipmentArr['Linhas'] = $packDimensions;
        $shipmentArr['Cliente'] = null;
        $shipmentArr['DataPrevistaDescarga'] = null;
        $shipmentArr['NumPedidoEmbalamento'] = "";
        $shipmentArr['AvisoSMSEnviado'] = false;
        $shipmentArr['Veiculo_Matricula'] = empty($shipmentArr['Veiculo_Matricula']) ? "" : $shipmentArr['Veiculo_Matricula'];
        $shipmentArr['Motorista_Nome'] = empty($shipmentArr['Motorista_Nome']) ? "" : $shipmentArr['Motorista_Nome'];
        $shipmentArr['ValorReembolso'] = empty($shipmentArr['ValorReembolso']) ? 0 : (float)$shipmentArr['ValorReembolso'];
        $shipmentArr['CodigoAT_GuiaRemessa'] = empty($shipmentArr['CodigoAT_GuiaRemessa']) ? "" : $shipmentArr['CodigoAT_GuiaRemessa'];


        $shipmentArr['DataCarga'] = explode(' ', $shipmentArr['DataCarga']);
        $shipmentArr['DataCarga'] = $shipmentArr['DataCarga'][0] . 'T' . $shipmentArr['DataCarga'][1] . '.000Z';
        $shipmentArr['DataDescarga'] = explode(' ', $shipmentArr['DataDescarga']);
        $shipmentArr['DataDescarga'] = $shipmentArr['DataDescarga'][0] . 'T' . $shipmentArr['DataDescarga'][1] . '.000Z';

        return response()->json($shipmentArr);
    }


    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function storeAttachment(Request $request)
    {

        if (!$this->checkAuth($request)) {
            return $this->responseError('shipments', '-1', 'Invalid token');
        }

        $trk   = $request->get('tracking_code');
        $files = $request->get('files');

        $shipment = Shipment::where('tracking_code', $trk)->first();

        if (empty($shipment)) {
            return $this->responseError('shipments', '-2', 'Tracking Code ' . $trk . ' não encontrado.');
        }

        if (empty($files) || !is_array($files)) {
            return $this->responseError('shipments', '-3', 'É obrigatório indicar os ficheiros');
        }

        try {

            foreach ($files as $file) {

                if ($file['filecontent']) {

                    $filename  = strtolower(trim(str_replace(' ', '_', removeAccents($file['filename']))));
                    $filenameParts = explode('.', $filename);
                    $extension = @$filenameParts[1];
                    $filename  = @$filenameParts[0];

                    $filepath = '/uploads/shipments_attachments/' . $shipment->tracking_code . '_' . $filename . '.' . $extension;

                    //store file
                    File::put(public_path() . $filepath, base64_decode($file['filecontent']));

                    $size = filesize(public_path() . $filepath);

                    if (in_array($extension, ['pdf', 'png'])) {
                        $attachment = new FileRepository();
                        $attachment->name         = @$file['title'];
                        $attachment->filepath     = $filepath;
                        $attachment->filename     = $filename;
                        $attachment->extension    = $extension;
                        $attachment->filesize     = $size;
                        $attachment->parent_id    = FileRepository::FOLDER_SHIPMENTS;
                        $attachment->source_class = 'Shipment';
                        $attachment->source_id    = $shipment->id;
                        $attachment->is_folder    = 0;
                        $attachment->save();
                    }
                }
            }

            $result = [
                'result'   => true,
                'feedback' => 'Anexos adicionados com sucesso'
            ];
        } catch (\Exception $e) {
            $result = [
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }



    /**
     * Store shipment custom attributes
     * @return array
     */
    public function customAttributes()
    {
        return trans('api.logistic.attributes');
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null)
    {

        $errors = trans('api.shipments.errors');

        $data = [
            'error'   => $code,
            'message' => $message ? $message : $errors[$method][$code]
        ];

        return response($data, 404)->header('Content-Type', 'application/json');
    }
}
