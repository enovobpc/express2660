<?php

namespace App\Models\Trip;

use App\Models\Agency;
use App\Models\Company;
use App\Models\PackType;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB, Setting, Mpdf\Mpdf, Auth, Excel;
use Jenssegers\Date\Date;
use Mockery\Exception;

class Trip extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_trips';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trips';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'code', 'period_id', 'start_date', 'start_hour', 'end_date', 'end_hour', 'pickup_date', 'delivery_date',
        'start_kms', 'end_kms', 'operator_id', 'assistants', 'provider_id', 'vehicle', 'trailer',
        'agency_id', 'pickup_route_id', 'delivery_route_id', 'created_by',
        'period',  'start_location', 'start_country', 'end_location', 'end_country', 'avg_delivery_time', 'obs',
        'is_nacional', 'is_spain', 'is_internacional', 'kms', 'kms_empty', 'keywords',
        'type', 'parent_id', 'parent_code', 'children_type', 'children_id', 'children_code', 'sort',

        'cost_price', 'cost_expenses_price', 'allowances_price', 'weekend_price', 'fuel_consumption',
        'is_route_optimized'
    ];

    /**
     * Date attributes 
     * 
     * @var type 
     */
    protected $dates = [
        'pickup_date',
        'delivery_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'pickup_date' => 'required'
    );

    /**
     * Set delivery manifest code
     * 
     * @return string
     */
    public function setCode($save = true, $originalTrip = null)
    {
        if (!$this->exists) {
            if ($save) {
                $this->save();
            }

            if ($originalTrip) {
                $code = 'R' . $originalTrip->code;
                $sort = substr($originalTrip->sort, 0, -1) . '1'; //o retorno termina com '1' para que ao ordenar fique junto com o mapa original
            } else {
                $totalTrips = Trip::filterSource()
                    ->withTrashed()
                    ->where(DB::raw('YEAR(pickup_date)'), date('Y'))
                    ->count();

                $docno = str_pad($totalTrips, 5, '0', STR_PAD_LEFT);
                $code  = $docno . '/' . date('y');
                $sort  = date('y') . $docno . '0'; //termina em zero para que ao ordenar com a recolha, fique o retorno a seguir ao original
            }

            if ($save) {
                $this->sort = $sort;
                $this->code = $code;
                $this->save();
            }

            return $code;
        }

        $this->save();
        return $this->code;
    }

    /**
     * Return manifest statistics
     * @return int[]
     */
    public function calcStats()
    {

        $finalStatus = ShippingStatus::isFinal()
            ->pluck('id')
            ->toArray();

        $totalShipments  = $this->shipments->count();
        $totalDelivery   = $this->shipments->filter(function ($item) use ($finalStatus) {
            return in_array($item->status_id, $finalStatus);
        })->count();

        $totalWeight      = $this->shipments->sum('weight');
        $totalLdm         = $this->shipments->sum('ldm');
        $vehicleMaxWeight = @$this->vehicleData->gross_weight ?? 0;
        $vehicleMaxLdm    = @$this->vehicleData->ldm ?? 13.60;

        $conclusionPercent  = $totalShipments > 0 ? ($totalDelivery * 100) / $totalShipments : 0;
        $weightPercent      = $vehicleMaxWeight > 0.00 ? ($totalWeight * 100) / $vehicleMaxWeight : 0;
        $ldmPercent         = $vehicleMaxLdm  > 0.00 ? ($totalLdm * 100) / $vehicleMaxLdm : 0;

        $stats = [
            'conclusion' => [
                'percent'   => $conclusionPercent,
                'delivered' => $totalDelivery,
                'total'     => $totalShipments,
                'color'     => getPercentColor($conclusionPercent)
            ],
            'weight' => [
                'percent' => $weightPercent,
                'max'     => $vehicleMaxWeight,
                'total'   => $totalWeight,
                'color'   => getPercentColor($weightPercent, true)
            ],
            'ldm' => [
                'percent' => $ldmPercent,
                'max'     => $vehicleMaxLdm,
                'total'   => $totalLdm,
                'color'   => getPercentColor($ldmPercent, true)
            ]
        ];


        return $stats;
    }

    /**
     * Add one or more shipments to manifest
     * If shipment exists on another manifest, delete it from old manifest
     * @param $shipmentId
     * @return mixed
     */
    public function addShipments($shipmentsIds)
    {

        $trip = $this;
        $trip->update(['is_route_optimized' => 0]);

        //adiciona os envios ao mapa
        foreach ($shipmentsIds as $shipmentId) {

            //remove os envios de outros mapas onde eles estejam
            $manifests = Trip::whereHas('shipments', function ($q) use ($shipmentId) {
                $q->where('shipment_id', $shipmentId);
            })
                ->get();

            foreach ($manifests as $manifest) {
                $manifest->deleteShipments([$shipmentId]);
            }

            //adiciona envio ao mapa atual selecionado
            $manifestShipment = TripShipment::firstOrNew([
                'trip_id'     => $this->id,
                'shipment_id' => $shipmentId
            ]);
            $manifestShipment->save();
        }

        $updateArr = [
            'trip_id'     => $trip->id,
            'trip_code'   => $trip->code,
            'vehicle'     => $trip->vehicle,
            'trailer'     => $trip->trailer,
            'operator_id' => $trip->operator_id
        ];

        //atribui data de entrega ao envio igual à data do manifesto
        if ($this->date) {
            $updateArr['delivery_date'] = $this->date;
        }

        Shipment::whereIn('id', $shipmentsIds)->update($updateArr);

        //atualiza informações geral dos envios do manifesto
        $trip->syncShipmentsData();

        return $trip;
    }

    /**
     * Remove multiple shipments to manifest
     * @param $shipmentId
     * @return mixed
     */
    public function deleteShipments($shipmentsIds)
    {

        $trip = $this;

        $shipments = Shipment::whereIn('id', $shipmentsIds)->get();

        foreach ($shipments as $shipment) {

            $result = TripShipment::where('trip_id', $trip->id)
                ->where('shipment_id', $shipment->id)
                ->delete();

            if ($result) {
                $shipment->update([
                    'trip_id'   => null,
                    'trip_code' => null
                ]);
            }
        }

        $trip->is_route_optimized = 0;
        $trip->save();
        $trip->syncShipmentsData();

        return true;
    }

    /**
     * Atualiza a informação dos envios do manifesto de acordo com os dados do manifesto
     * @return bool
     */
    public function syncShipmentsData()
    {

        $shipments = $this->shipments;
        $totalShipments = $shipments->count();

        $costPrice = null;
        $vatRate   = null;
        if ($this->cost_price > 0.00 && $totalShipments) {
            $costPrice = $this->cost_price / $totalShipments;
        } elseif ($this->cost_price == 0) {
            //apaga todos os preços de custo
            $costPrice = 0;
        }


        foreach ($shipments as $shipment) {

            $updateFields = [];
            $updateFields['trip_id']   = $this->id;
            $updateFields['trip_code'] = $this->code;

            if ($this->delivery_route_id) {
                $shipment->route_id = $this->delivery_route_id;
            }

            if ($this->operator_id) {
                $shipment->operator_id = $this->operator_id;
            }

            if ($this->vehicle) {
                $shipment->vehicle = $this->vehicle;
            }

            if ($this->trailer) {
                $shipment->trailer = $this->trailer;
            }

            if ($this->provider_id && !$shipment->submited_at) {
                $shipment->provider_id = $this->provider_id;
            }

            if (!is_null($costPrice)) {
                $shipment->cost_shipping_price = $costPrice;
            }

            $shipment->save();
        }

        return true;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function newVehicleHistory() {

        $trip = $this;

        $tripVehicle = new TripVehicle();
        $tripVehicle->fill($trip->toArray());
        $tripVehicle->trip_id  = $trip->id; 
        $tripVehicle->start_at = $trip->pickup_date;
        $tripVehicle->end_at   = $trip->delivery_date;
        $tripVehicle->save();

        return $tripVehicle;
    }

    /**
     * Update last vehicle history
     *
     * @return void
     */
    public function updateVehicleHistory() {

        $trip = $this;

        //atualiza a tripVehicle mais recente
        $tripVehicle = TripVehicle::where('trip_id', $trip->id)
            ->orderBy('start_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if(!$tripVehicle) {
            $tripVehicle = new TripVehicle();
        }

        $tripVehicle->fill($trip->toArray());
        $tripVehicle->trip_id  = $trip->id; 
        $tripVehicle->start_at = $trip->pickup_date;
        $tripVehicle->end_at   = $trip->delivery_date;
        $tripVehicle->save();

    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function createHistoryIfNotExists() {

        if($this->vehicles_history->isEmpty()) { //se tem viatura e não tem histórico, cria/atualiza o histórico

            $vehicleHistory = new TripVehicle();
            $vehicleHistory->fill($this->toArray());
            $vehicleHistory->start_at = $this->pickup_date;
            $vehicleHistory->end_at   = $this->delivery_date;
            $vehicleHistory->trip_id  = $this->id;
            $vehicleHistory->save();

            //grava histórico para cada envio
            if(!$this->shipments->isEmpty()) {
                $shipments = $this->shipments;
                foreach($shipments as $shipment) {

                    if($shipment->pickuped_date) {
                        $history = TripHistory::firstOrNew([
                            'trip_id'   => $this->id,
                            'action'    => 'pickup',
                            'target'    => 'Shipment',
                            'target_id' => $shipment->id
                        ]);
            
                        $history->fill($this->toArray());
                        $history->trip_id           = $this->id;
                        $history->trip_vehicle_id   = $vehicleHistory->id;
                        $history->action            = 'pickup';
                        $history->target            = 'Shipment';
                        $history->target_id         = $shipment->id;
                        $history->date              = $shipment->pickuped_date;
                        $history->save();

                    } else if($shipment->delivered_date) {
                        $history = TripHistory::firstOrNew([
                            'trip_id'   => $this->id,
                            'action'    => 'delivery',
                            'target'    => 'Shipment',
                            'target_id' => $shipment->id
                        ]);
            
                        $history->fill($this->toArray());
                        $history->trip_id           = $this->id;
                        $history->trip_vehicle_id   = $vehicleHistory->id;
                        $history->action            = 'delivery';
                        $history->target            = 'Shipment';
                        $history->target_id         = $shipment->id;
                        $history->date              = $shipment->delivered_date;
                        $history->save();
                    }
                }
            }

            if($vehicleHistory) {
                return $vehicleHistory;
            }
        }

        return false;
    }

    /**
     * Print shipments
     *
     * @param Request $request
     * @param null $shipments
     * @return \Illuminate\Http\Response|string
     */
    public static function printSummary($manifestsIds, $outputFormat = 'I')
    {

        $manifests = Trip::with('operator')
            ->with(['shipments' => function ($q) {
                $q->orderBy('pivot_sort');
            }])
            ->orderBy('code', 'asc')
            ->whereIn('id', $manifestsIds)
            ->get();

        $packTypes = PackType::pluck('name', 'code')->toArray();

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

        foreach ($manifests as $manifest) {

            $data = [
                'manifest'          => $manifest,
                'packTypes'         => $packTypes,
                'documentTitle'     => 'Folha de Viagem ' . $manifest->code,
                'documentSubtitle'  => $manifest->pickup_date->format('Y-m-d') . ' | ' . @$manifest->operator->name,
                'ref2name'          => Setting::get('shipments_reference2_name'),
                'view'              => 'admin.printer.trips.summary'
            ];

            $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render());
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Folha de Viagem.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Print shipments
     *
     * @param Request $request
     * @param null $shipments
     * @return \Illuminate\Http\Response|string
     */
    public static function printBillingSummary($manifestsIds, $outputFormat = 'I')
    {

        $manifests = Trip::with('operator')
            ->with(['shipments' => function ($q) {
                $q->with(['history' => function ($q) {
                    $q->where('status_id', ShippingStatus::DOCS_RECEIVED_ID);
                    $q->whereNull('deleted_at');
                }]);
                $q->orderBy('pivot_sort');
            }])
            ->orderBy('code', 'asc')
            ->whereIn('id', $manifestsIds)
            ->get();

        $packTypes = PackType::pluck('name', 'code')->toArray();

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


        foreach ($manifests as $manifest) {

            $returnManifest = null;
            if ($manifest->type == 'R') { //se é retorno, vai buscar o manifesto original
                $returnManifest = $manifest;

                //obtem viagem principal
                $manifest = Trip::with('operator')
                    ->with(['shipments' => function ($q) {
                        $q->with(['history' => function ($q) {
                            $q->where('status_id', ShippingStatus::DOCS_RECEIVED_ID);
                            $q->whereNull('deleted_at');
                        }]);
                        $q->orderBy('pivot_sort');
                    }])
                    ->orderBy('code', 'asc')
                    ->firstOrNew(['id' => $returnManifest->parent_id]);
            } else {

                //obtem viagem de retorno
                $returnManifest = Trip::with('operator')
                    ->with(['shipments' => function ($q) {
                        $q->with(['history' => function ($q) {
                            $q->where('status_id', ShippingStatus::DOCS_RECEIVED_ID);
                            $q->whereNull('deleted_at');
                        }]);
                        $q->orderBy('pivot_sort');
                    }])
                    ->orderBy('code', 'asc')
                    ->where('id', $manifest->children_id)
                    ->first();

                if (!$returnManifest) {
                    $returnManifest = new Trip();
                }
            }


            $subtitle = $manifest->pickup_date->format('Y-m-d') . ' ' . ($returnManifest && $returnManifest->delivery_date ? ' a ' . $returnManifest->delivery_date->format('Y-m-d') : ($manifest->delivery_date ? ' a ' . $manifest->delivery_date->format('Y-m-d') : ''));

            $data = [
                'manifest'          => $manifest,
                'returnManifest'    => $returnManifest,
                'packTypes'         => $packTypes,
                'documentTitle'     => 'Balancete Viagem ' . $manifest->code,
                'documentSubtitle'  => $subtitle,
                'ref2name'          => Setting::get('shipments_reference2_name'),
                'view'              => 'admin.printer.trips.billing'
            ];

            $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render());
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Mapa de Viagem.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Print operator activity declaration
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printActivityDeclaration($inputs, $returnMode = 'pdf')
    {

        //http://quickbox.test/admin/printer/trips/activity-declaration?last_date=2022-12-20&last_hour=00%3A25&next_date=2023-02-07&next_hour=14%3A05&responsable=1&inactivity_reasons%5B%5D=3&operator=376
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 24,
            'margin_bottom' => 0,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($inputs as $data) {

            $operator = User::filterSource()
                ->find($data['operator']);

            $manager = User::filterSource()
                ->find($data['responsable']);

            $agency = Agency::with('company_details')
                ->filterSource()
                ->where('id', @$operator->agency_id)
                ->first();
            $company = @$agency->company_details;

            if (!$company) {
                $company = Company::filterSource()->first();
            }


            if ($operator) {
                $lastName  = last_name($operator->fullname);
                $firstName = str_replace($lastName, '', $operator->fullname);
                $operator->name = trim($lastName . ', ' . $firstName);

                if ($operator->birthdate) {
                    $operator->birthdate = new Date($operator->birthdate);
                    $operator->birthdate = $operator->birthdate->format('d/m/Y');
                }

                if ($operator->admission_date) {
                    $operator->admission_date = new Date($operator->admission_date);
                    $operator->admission_date = $operator->admission_date->format('d/m/Y');
                }
            }

            if ($manager) {
                $lastName  = last_name($manager->fullname);
                $firstName = str_replace($lastName, '', $manager->fullname);
                $manager->name = trim($lastName . ', ' . $firstName);
            }

            if ($data['start_date']) {
                $dt = new Date($data['start_date']);
                $data['start_date'] = $data['start_hour'] . ' ' . $dt->format('d/m/Y');
            }

            if ($data['end_date']) {
                $dt = new Date($data['end_date']);
                $data['end_date'] = $data['end_hour'] . ' ' . $dt->format('d/m/Y');
            }

            $docDate = date('d/m/Y');

            $data = [
                'company'       => $company,
                'operator'      => $operator,
                'manager'       => $manager,
                'startDate'     => @$data['start_date'],
                'endDate'       => @$data['end_date'],
                'docDate'       => $docDate,
                'reasons'       => @$data['inactivity_reasons'] ? $data['inactivity_reasons'] : [],
                'documentTitle' => 'Declaração Atividade',
                'view'          => 'admin.printer.trips.activity_declaration'
            ];

            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write
        }

        if ($returnMode == 'string') {
            return $mpdf->Output('Declaração Atividade.pdf', 'S'); //string
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;

        return $mpdf->Output('Declaração Atividade.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function calcCostConsumption()
    {
        $pricePerLitter = Setting::get('guides_fuel_price'); //subsituir pelo ultimo registo de combustivel entre a data inicio e fim da viagem
        $liters = $this->fuel_consumption;
        return $liters * $pricePerLitter;
    }

    public function calcCostSalary()
    {

        $pricePerHour = @$this->operator->salary_value_hour;

        if ($this->pickup_date && $this->delivery_date) {
            $hours = $this->duration_hours;
        } else {
            $startDate    = new Date($this->start_date);
            $now          = Date::now();
            $hours = $startDate->diffInHours($now);
        }

        return $hours * $pricePerHour;
    }

        /**
     * Download excel file
     *
     * @param $data
     * @param null $filename
     * @param bool $exportString [true|false] Return excel as base64 string
     * @return bool|string
     */
    public static function exportExcel($data, $fileName = null)
    {
        ini_set("memory_limit", "-1");

        $maxRows = 5000;

        if ($data->count() > 5000) {
            throw new Exception('Só é permitido exportar um máximo de ' . $maxRows . ' registos de cada vez.');
        }

        $filename = $fileName ? $fileName : 'Listagem Mapas Viagem';

        $appMode = Setting::get('app_mode');

        $header = [];
        $header[] = 'Código';
        $header[] = 'Tipo';
        $header[] = 'Viagem Principal';
        $header[] = 'Viagem Retorno';

        $header[] = 'Motorista';
        $header[] = 'Acompanhantes';
        $header[] = 'Viatura';
        $header[] = 'Reboque';

        $header[] = 'Data/Hora Início';
        $header[] = 'Kms Início';
        $header[] = 'Localidade Início';
        $header[] = 'País Início';

        $header[] = 'Data/Hora Fim';
        $header[] = 'Kms Fim';
        $header[] = 'Localidade Fim';
        $header[] = 'País Fim';

        if ($appMode != 'cargo' && $appMode != 'freight') {
            $header[] = 'Período Dia';
            $header[] = 'Média Tempo Entrega';
        }

        if ($appMode == 'cargo' || $appMode == 'freight') {
            $header[] = 'KM Vazio';
        }

        $header[] = 'Nº Serviços';
        $header[] = 'Nº Volumes';
        $header[] = 'Peso';
        if ($appMode != 'cargo' && $appMode != 'freight') {
            $header[] = 'COD';
        }
        $header[] = 'Preço';


        $header[] = 'Fornecedor Subcontratado';
        $header[] = 'Preço Custo';

        $header[] = 'Nacional';
        $header[] = 'Espanha';
        $header[] = 'Internacional';

        if ($appMode != 'cargo' && $appMode != 'freight') {
            $header[] = 'Rota Recolha';
            $header[] = 'Rota Entrega';
        }

        $header[] = 'Custos - Consumo Combustível';

        if ($appMode == 'cargo' || $appMode == 'freight') {
            $header[] = 'Custos - Ajudas Custo';
            $header[] = 'Custos - Fim de semana';
        }

        $header[] = 'Observações';
        $rowCounter = 1;
        $excel = Excel::create($filename, function ($file) use (&$rowCounter, $data, $header, $appMode) {
            $file->sheet('Listagem', function ($sheet) use (&$rowCounter, $data, $header, $appMode) {
                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                foreach ($data as $trip) {
                    $rowData = [];
                    $rowData[] = $trip->code;
                    $rowData[] = ($trip->type == 'R') ? 'Retorno' : '';
                    $rowData[] = $trip->parent_code;
                    $rowData[] = $trip->children_code;
                    $rowData[] = @$trip->operator->name;
                    $rowData[] = $trip->getAssistantsNamesAttribute($trip->assistants);
                    $rowData[] = @$trip->vehicle;
                    $rowData[] = @$trip->trailer;

                    $rowData[] = @$trip->pickup_date;
                    $rowData[] = @$trip->start_kms;
                    $rowData[] = @$trip->start_location;
                    $rowData[] = trans('country.' . @$trip->start_country ?? 'pt');


                    $rowData[] = @$trip->delivery_date;
                    $rowData[] = @$trip->end_kms;
                    $rowData[] = @$trip->end_location;
                    $rowData[] = trans('country.' . @$trip->end_country ?? 'pt');

                    if ($appMode != 'cargo' && $appMode != 'freight') {
                        $rowData[] = @$trip->period->name;
                        $rowData[] = @$trip->avg_delivery_time;
                    }

                    if ($appMode == 'cargo' || $appMode == 'freight') {
                        $rowData[] = @$trip->kms_empty;
                    }

                    $rowData[] = @$trip->shipments->count();
                    $rowData[] = $trip->shipments->sum(function ($shipment) {
                        return $shipment->volumes;
                    });
                    $rowData[] = $trip->shipments->sum(function ($shipment) {
                        return $shipment->weight;
                    });
                    if ($appMode != 'cargo' && $appMode != 'freight') {
                        $rowData[] = $trip->shipments->sum(function ($shipment) {
                            return $shipment->charge_price;
                        });
                    }
                    $rowData[] = $trip->shipments->sum(function ($shipment) {
                        return $shipment->shipping_price;
                    });

                    $rowData[] = @$trip->provider->name;
                    $rowData[] = @$trip->cost_price;

                    $rowData[] = @$trip->is_nacional;
                    $rowData[] = @$trip->is_spain;
                    $rowData[] = @$trip->is_internacional;

                    if ($appMode != 'cargo' && $appMode != 'freight') {
                        $rowData[] = @$trip->delivery_route->name;
                        $rowData[] = @$trip->pickup_route->name;
                    }

                    $rowData[] = @$trip->fuel_consumption;

                    if ($appMode == 'cargo' || $appMode == 'freight') {
                        $rowData[] = @$trip->allowances_price;
                        $rowData[] = @$trip->weekend_price;
                    }

                    $rowData[] = @$trip->obs;

                    $sheet->appendRow($rowData);
                    $rowCounter++;
                }
            });
        });

        $excel->export('xlsx');
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function period()
    {
        return $this->belongsTo('App\Models\Trip\TripPeriod', 'period_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function auxiliar()
    {
        return $this->belongsTo('App\Models\User', 'auxiliar_id');
    }

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function vehicleData()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle', 'license_plate');
    }

    public function pickup_route()
    {
        return $this->belongsTo('App\Models\Route', 'pickup_route_id');
    }

    public function delivery_route()
    {
        return $this->belongsTo('App\Models\Route', 'delivery_route_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function vehicle_data()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle', 'license_plate');
    }

    public function shipments()
    {
        return $this->belongsToMany('App\Models\Shipment', 'trips_shipments', 'trip_id')->withPivot('sort');
    }

    public function assistants()
    {
        $assistants = $this->assistants;
        return User::whereIn('id', $assistants)->get();
    }

    public function parent_manifest()
    {
        return $this->belongsTo('App\Models\Trip\Trip', 'parent_id');
    }

    public function child_manifest()
    {
        return $this->belongsTo('App\Models\Trip\Trip', 'children_id');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Trip\TripExpense', 'trip_id');
    }

    public function vehicles_history()
    {
        return $this->hasMany('App\Models\Trip\TripVehicle', 'trip_id');
    }

    public function history()
    {
        return $this->hasMany('App\Models\Trip\TripHistory', 'trip_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when 
    | getting or setting them. Simply define a 'getFooAttribute' method on your model 
    | to declare an accessor. Keep in mind that the methods should follow camel-casing, 
    | even though your database columns are snake-case.
    |
    */
    public function setPeriodIdAttribute($value)
    {
        $this->attributes['period_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setAuxiliarIdAttribute($value)
    {
        $this->attributes['auxiliar_id'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setCreatedByAttribute($value)
    {
        $this->attributes['created_by'] = empty($value) ? null : $value;
    }

    public function setDeliveryRouteIdAttribute($value)
    {
        $this->attributes['delivery_route_id'] = empty($value) ? null : $value;
    }

    public function setPickupRouteIdAttribute($value)
    {
        $this->attributes['pickup_route_id'] = empty($value) ? null : $value;
    }

    public function setAgencyIdAttribute($value)
    {
        $this->attributes['agency_id'] = empty($value) ? null : $value;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = empty($value) ? null : $value;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }

    public function setStartHourAttribute($value)
    {
        $this->attributes['start_hour'] = empty($value) ? null : $value;
    }

    public function setEndHourAttribute($value)
    {
        $this->attributes['end_hour'] = empty($value) ? null : $value;
    }

    public function setStartKmsAttribute($value)
    {
        $this->attributes['start_kms'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setEndKmsAttribute($value)
    {
        $this->attributes['end_kms'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setAssistantsAttribute($value)
    {
        $this->attributes['assistants'] = empty($value) ? null : json_encode($value);
    }

    public function setVehiclesAttribute($value)
    {
        $this->attributes['vehicles'] = empty($value) ? null : json_encode($value);
    }

    public function getAvgDeliveryTimeAttribute($value)
    {
        return empty($value) ? '00:05' : $value;
    }

    public function getAssistantsAttribute($value)
    {
        return empty($value) ? [] : array_map('intval', json_decode($value, true));
    }

    public function getVehiclesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }

    public function getAssistantsNamesAttribute($value)
    {
        $values = @$this->assistants()->pluck('name')->toArray();
        if (!empty($values)) {
            return implode(',', $values);
        }
        return '';
    }

    public function getCostPriceAttribute($value)
    {
        return $value > 0.00 ? $value : null;
    }

    public function getTotalPriceAttribute($value)
    {
        return @$this->shipments->sum('billing_subtotal');
    }

    public function getBillingSubtotalAttribute($value)
    {
        return $this->shipments->sum('billing_subtotal');
    }

    public function getFuelConsumptionAttribute($value)
    {
        return $value > 0.00 ? $value : @$this->vehicle_data->average_consumption;
    }

    public function getCostBillingSubtotalAttribute($value)
    {
        $total = $this->cost_price ? $this->cost_price : $this->shipments->sum('cost_billing_subtotal');
        $total += $this->cost_expenses_price;
        $total += $this->allowances_price;
        $total += $this->calcCostSalary();
        $total += $this->calcCostConsumption();
        $total += $this->expenses->sum('total');
        return $total;
    }

    public function getBalanceAttribute($value)
    {
        return $this->billing_subtotal - $this->cost_billing_subtotal;
    }

    public function getDurationHoursAttribute($value)
    {

        $start = new Date($this->pickup_date);
        $end   = new Date($this->delivery_date);

        return $end->diffInHours($start);
    }

    public function getDurationDaysAttribute($value)
    {

        $start = new Date($this->pickup_date);
        $end   = new Date($this->delivery_date);

        return $end->diffInDays($start);
    }

    public function getGainKmAttribute($value)
    {
        return $this->kms > 0.00 ? $this->billing_subtotal / $this->kms : 0;
    }

    public function getCostKmAttribute($value)
    {
        return $this->kms > 0.00 ? $this->cost_billing_subtotal / $this->kms : 0;
    }

    public function getCo2EmissionsAttribute($value)
    {
        return $this->kms > 0.00 ? @$this->vehicle->co2 * $this->kms : 0;
    }
}
