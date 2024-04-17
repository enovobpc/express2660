<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use Illuminate\Console\Command;
use File, Setting;

class FtpPowerBi extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:power-bi {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Communication ftp with PowerBi';


    /**
     * @var null
     */
    private $ftpHost = 'logisimple.pt';
    private $ftpUser = 'enovo@logisimple.pt';
    private $ftpPass = '2vNt#E$8!yoT';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Sync data with PowerBi by ftp");

        if ($this->argument('action') == 'weblog') {
            $this->exportWeblog();
        } else if ($this->argument('action') == 'wms') {
            $this->exportWMS();
        } else {
            $this->exportTMS();
        }

        $this->info("Sync completed");
        return;
    }

    /**
     * Export WMS 
     */
    public function exportWMS()
    {
        $filename    = 'WMS.csv';
        $localfile   = public_path('uploads/ftp_importer/powerbi/' . $filename);

        $header = [];
        $header[] = 'Tipo';
        $header[] = 'TRK';
        $header[] = 'TRK Secundário';
        $header[] = 'Data Carga';
        $header[] = 'Data Descarga';
        $header[] = 'Cod. Serviço';
        $header[] = 'Serviço';
        $header[] = 'Referência';
        $header[] = 'Referência 2';
        $header[] = 'Referência 3';
        $header[] = 'Agência Origem';
        $header[] = 'Agência Destino';
        $header[] = 'Fornecedor';
        $header[] = 'Cod. Cliente';
        $header[] = 'Nome Cliente';
        $header[] = 'Departamento';
        $header[] = 'A/C remetente';
        $header[] = 'Remetente';
        $header[] = 'Morada Remetente';
        $header[] = 'CP Remetente';
        $header[] = 'Localidade Remetente';
        $header[] = 'Pais Remetente';
        $header[] = 'Contacto Remetente';
        $header[] = 'A/C Destinatário';
        $header[] = 'Destinatário';
        $header[] = 'Morada Destinatário';
        $header[] = 'CP Destinatário';
        $header[] = 'Localidade Destinatário';
        $header[] = 'País Destinatário';
        $header[] = 'Contacto Destinatário';
        $header[] = 'E-mail Destinatário';
        $header[] = 'Volumes';
        $header[] = 'Volume (M3)';
        $header[] = 'Peso (Kg)';
        $header[] = 'Peso Vol. (Kg)';
        $header[] = 'Kms';
        $header[] = 'Cobrança';
        $header[] = 'Retorno';
        $header[] = 'Último Estado';
        $header[] = 'Data Último Estado';
        $header[] = 'Motivo Incidência';
        $header[] = 'Observações Estado';
        $header[] = 'Data Entrega';
        $header[] = 'Nome Receptor';
        $header[] = 'Cód. Motorista';
        $header[] = 'Nome Motorista';
        $header[] = 'Solicitado por';
        $header[] = 'Observações Carga';
        $header[] = 'Observações Descarga';
        $header[] = 'Viatura';
        $header[] = 'Reboque';
        $header[] = 'Obs. Internas';
        $header[] = 'Preço Custo';
        $header[] = 'Preço';
        $header[] = 'Encargos';
        $header[] = 'Total';
        $header[] = 'Taxa IVA';
        $header[] = 'Data de Registo em Sistema';
        $header[] = 'Data de Entrada Armazém';

        //apaga o ficheiro local caso exista
        File::delete($localfile);

        //abre para escrita um novo ficheiro vazio.
        $tmpFile = fopen($localfile, 'a');

        //escreve cabeçalho do ficheiro csv
        fputcsv($tmpFile, $header, ';', '"', "\\");

        Shipment::where('status_id', '!=', 5)->chunk(1000, function ($shipments) use (&$tmpFile) {

            foreach ($shipments as $shipment) {
                $rowData = [];
                $customer = Customer::where('id', $shipment->id)->first();
                $shippingDate = new Carbon($shipment->shipping_date);
                $shippingDate = $shippingDate->format('Y-m-d H:i');

                $deliveryDate = '';
                if ($shipment->delivery_date != null && $shipment->delivery_date != "") {
                    $deliveryDate = new Carbon($shipment->delivery_date);
                    $deliveryDate = $deliveryDate->format('Y-m-d H:i');
                }

                $totalPrice = '';
                $totalPrice = @$shipment->total_price + @$shipment->total_expenses;

                $shipmentPrice = '';
                if ($shipment->total_price > 0.00) {
                    $shipmentPrice = $shipment->total_price;
                }

                $expensesPrice = '';
                if ($shipment->total_expenses > 0.00) {
                    $expensesPrice = $shipment->total_expenses;
                }

                $costPrice = '';
                if ($shipment->cost_price > 0.00) {
                    $costPrice = $shipment->cost_price;
                }

                $vatRate = Setting::get('vat_rate_normal');
                if (($shipment->isExport() && @$customer->is_particular) || @$shipment->service->is_mail) {
                    $vatRate = 0;
                }

                if ($shipment->payment_at_recipient) {
                    $totalPrice = '';
                }

                $totalPrice = $totalPrice > 0.00 ? $totalPrice : '';
                $vatRate    = $totalPrice > 0.00 ? $vatRate : '';

                //start here
                $rowData[] = @$shipmentTypes[$shipment->type] ? $shipmentTypes[$shipment->type] : 'ENVIO';
                $rowData[] = $shipment->tracking_code . ' ';
                $rowData[] = $shipment->provider_tracking_code . ' ';

                $rowData[] = $shippingDate;
                $rowData[] = $deliveryDate;
                $rowData[] = @$shipment->service->display_code;
                $rowData[] = @$shipment->service->name;

                $rowData[] = $shipment->reference;
                $rowData[] = $shipment->reference2;
                $rowData[] = $shipment->reference3;

                $rowData[] = @$shipment->senderAgency->name;
                $rowData[] = @$shipment->recipientAgency->name;
                $rowData[] = @$shipment->provider->name;

                $rowData[] = $shipment->customer ? @$shipment->customer->code : '';
                $rowData[] = $shipment->customer ? @$shipment->customer->name : '';
                $rowData[] = $shipment->department_id ? @$shipment->department->name : '';

                $rowData[] = $shipment->sender_attn;
                $rowData[] = $shipment->sender_name;
                $rowData[] = $shipment->sender_address;
                $rowData[] = $shipment->sender_zip_code;
                $rowData[] = $shipment->sender_city;
                $rowData[] = $shipment->sender_country;
                $rowData[] = $shipment->sender_phone;
                $rowData[] = $shipment->recipient_attn;
                $rowData[] = $shipment->recipient_name;
                $rowData[] = $shipment->recipient_address;
                $rowData[] = $shipment->recipient_zip_code;
                $rowData[] = $shipment->recipient_city;
                $rowData[] = $shipment->recipient_country;
                $rowData[] = $shipment->recipient_phone;
                $rowData[] = $shipment->recipient_email;
                $rowData[] = $shipment->volumes;
                $rowData[] = $shipment->volume_m3;
                $rowData[] = $shipment->weight;
                $rowData[] = $shipment->volumetric_weight;
                $rowData[] = $shipment->kms;
                $rowData[] = $shipment->charge_price ? money($shipment->charge_price) : '';
                $rowData[] = $shipment->return_type ? trans('admin/shipments.return.' . $shipment->return_type) : '';
                $rowData[] = @$shipment->status->name;
                $rowData[] = @$shipment->lastHistory->created_at;
                $rowData[] = @$shipment->lastIncidence->incidence->name;
                $rowData[] = @$shipment->lastHistory->obs;
                $rowData[] = @$shipment->lastHistory->status_id == ShippingStatus::DELIVERED_ID ? @$shipment->lastHistory->created_at : '';
                $rowData[] = @$shipment->lastHistory->receiver;

                $rowData[] = @$shipment->operator->code;
                $rowData[] = @$shipment->operator->name;


                $rowData[] = $shipment->requester_name;
                $rowData[] = $shipment->obs . ' ' . ($shipment->status_id == ShippingStatus::PICKUP_FAILED_ID ? '#### RECOLHA FALHADA ####' : '');
                $rowData[] = $shipment->obs_delivery;
                $rowData[] = $shipment->vehicle;
                $rowData[] = $shipment->trailer;

                $rowData[] = $shipment->obs_internal;

                $rowData[] = $costPrice;

                $rowData[] = $shipmentPrice;
                $rowData[] = $expensesPrice;
                $rowData[] = $totalPrice;

                $rowData[] = $vatRate;

                $registerSystem = "";
                $enterInArmazem = "";
                $registerSystem = $shipment->created_at;
                foreach ($shipment->history as $row) {
                    // if($row->status_id == 5){
                    //     $registerSystem = $row->created_at;
                    // }
                    if ($row->status_id == 17) {
                        $enterInArmazem = $row->created_at;
                    }
                }

                $rowData[] = $registerSystem;
                $rowData[] = $enterInArmazem;

                //escreve linha no ficheiro csv
                fputcsv($tmpFile, $rowData, ';', '"', "\\");
            }
        });

        fclose($tmpFile);

        if ($this->storeFTP($filename)) {
            echo 'WMS - Sincronização com sucesso.';
        } else {
            echo 'WMS - Sincronização falhada.';
        }
    }


    /**
     * Comunicate shipments trackings
     */
    public function exportTMS()
    {

        $filename    = 'TMS.csv';
        $localfile   = public_path('uploads/ftp_importer/powerbi/' . $filename);

        $header = [];
        $header[] = 'Tipo';
        $header[] = 'TRK';
        $header[] = 'TRK Secundário';
        $header[] = 'Data Carga';
        $header[] = 'Data Descarga';
        $header[] = 'Cod. Serviço';
        $header[] = 'Serviço';
        $header[] = 'Referência';
        $header[] = 'Referência 2';
        $header[] = 'Referência 3';
        $header[] = 'Agência Origem';
        $header[] = 'Agência Destino';
        $header[] = 'Fornecedor';
        $header[] = 'Cod. Cliente';
        $header[] = 'Nome Cliente';
        $header[] = 'Departamento';
        $header[] = 'A/C remetente';
        $header[] = 'Remetente';
        $header[] = 'Morada Remetente';
        $header[] = 'CP Remetente';
        $header[] = 'Localidade Remetente';
        $header[] = 'Pais Remetente';
        $header[] = 'Contacto Remetente';
        $header[] = 'A/C Destinatário';
        $header[] = 'Destinatário';
        $header[] = 'Morada Destinatário';
        $header[] = 'CP Destinatário';
        $header[] = 'Localidade Destinatário';
        $header[] = 'País Destinatário';
        $header[] = 'Contacto Destinatário';
        $header[] = 'E-mail Destinatário';
        $header[] = 'Volumes';
        $header[] = 'Volume (M3)';
        $header[] = 'Peso (Kg)';
        $header[] = 'Peso Vol. (Kg)';
        $header[] = 'Kms';
        $header[] = 'Cobrança';
        $header[] = 'Retorno';
        $header[] = 'Último Estado';
        $header[] = 'Data Último Estado';
        $header[] = 'Motivo Incidência';
        $header[] = 'Observações Estado';
        $header[] = 'Data Entrega';
        $header[] = 'Nome Receptor';
        $header[] = 'Cód. Motorista';
        $header[] = 'Nome Motorista';
        $header[] = 'Solicitado por';
        $header[] = 'Observações Carga';
        $header[] = 'Observações Descarga';
        $header[] = 'Viatura';
        $header[] = 'Reboque';
        $header[] = 'Obs. Internas';
        $header[] = 'Preço Custo';
        $header[] = 'Preço';
        $header[] = 'Encargos';
        $header[] = 'Total';
        $header[] = 'Taxa IVA';
        $header[] = 'Data de Registo em Sistema';
        $header[] = 'Data de Entrada Armazém';


        //apaga o ficheiro local caso exista
        File::delete($localfile);

        //abre para escrita um novo ficheiro vazio.
        $tmpFile = fopen($localfile, 'a');

        //escreve cabeçalho do ficheiro csv
        fputcsv($tmpFile, $header, ';', '"', "\\");

        //SELECT * FROM `shipments` where date>='2022-09-28' and deleted_at is null
        $date = date("Y-m-d");
        $dateToCompare = date('Y-m-d', strtotime($date . ' - 1 months'));

        Shipment::where('date', '>=', $dateToCompare)->chunk(1000, function ($shipments) use (&$tmpFile) {
            foreach ($shipments as $shipment) {
                $rowData = [];
                $customer = Customer::where('id', $shipment->id)->first();
                $shippingDate = new Carbon($shipment->shipping_date);
                $shippingDate = $shippingDate->format('Y-m-d H:i');


                $deliveryDate = '';
                if ($shipment->delivery_date != null && $shipment->delivery_date != "") {
                    $deliveryDate = new Carbon($shipment->delivery_date);
                    $deliveryDate = $deliveryDate->format('Y-m-d H:i');
                }
                // $deliveryDate = new Carbon($shipment->delivery_date);
                // $deliveryDate = $deliveryDate->format('Y-m-d H:i');

                $totalPrice = '';
                $totalPrice = @$shipment->total_price + @$shipment->total_expenses;

                $shipmentPrice = '';
                if ($shipment->total_price > 0.00) {
                    $shipmentPrice = $shipment->total_price;
                }

                $expensesPrice = '';
                if ($shipment->total_expenses > 0.00) {
                    $expensesPrice = $shipment->total_expenses;
                }

                $costPrice = '';
                if ($shipment->cost_price > 0.00) {
                    $costPrice = $shipment->cost_price;
                }

                $vatRate = Setting::get('vat_rate_normal');
                if (($shipment->isExport() && @$customer->is_particular) || @$shipment->service->is_mail) {
                    $vatRate = 0;
                }

                if ($shipment->payment_at_recipient) {
                    $totalPrice = '';
                }

                $totalPrice = $totalPrice > 0.00 ? $totalPrice : '';
                $vatRate    = $totalPrice > 0.00 ? $vatRate : '';

                //start here
                $rowData[] = @$shipmentTypes[$shipment->type] ? $shipmentTypes[$shipment->type] : 'ENVIO';
                $rowData[] = $shipment->tracking_code . ' ';
                $rowData[] = $shipment->provider_tracking_code . ' ';

                $rowData[] = $shippingDate;
                $rowData[] = $deliveryDate;
                $rowData[] = @$shipment->service->display_code;
                $rowData[] = @$shipment->service->name;

                $rowData[] = $shipment->reference;
                $rowData[] = $shipment->reference2;
                $rowData[] = $shipment->reference3;

                $rowData[] = @$shipment->agency->name;
                $rowData[] = @$shipment->recipientAgency->name;
                $rowData[] = @$shipment->provider->name;

                $rowData[] = $shipment->customer ? @$shipment->customer->code : '';
                $rowData[] = $shipment->customer ? @$shipment->customer->name : '';
                $rowData[] = $shipment->department_id ? @$shipment->department->name : '';

                $rowData[] = $shipment->sender_attn;
                $rowData[] = $shipment->sender_name;
                $rowData[] = $shipment->sender_address;
                $rowData[] = $shipment->sender_zip_code;
                $rowData[] = $shipment->sender_city;
                $rowData[] = $shipment->sender_country;
                $rowData[] = $shipment->sender_phone;
                $rowData[] = $shipment->recipient_attn;
                $rowData[] = $shipment->recipient_name;
                $rowData[] = $shipment->recipient_address;
                $rowData[] = $shipment->recipient_zip_code;
                $rowData[] = $shipment->recipient_city;
                $rowData[] = $shipment->recipient_country;
                $rowData[] = $shipment->recipient_phone;
                $rowData[] = $shipment->recipient_email;
                $rowData[] = $shipment->volumes;
                $rowData[] = $shipment->volume_m3;
                $rowData[] = $shipment->weight;
                $rowData[] = $shipment->volumetric_weight;
                $rowData[] = $shipment->kms;
                $rowData[] = $shipment->charge_price ? money($shipment->charge_price) : '';
                $rowData[] = $shipment->return_type ? trans('admin/shipments.return.' . $shipment->return_type) : '';
                $rowData[] = @$shipment->status->name;
                $rowData[] = @$shipment->lastHistory->created_at;
                $rowData[] = @$shipment->lastIncidence->incidence->name;
                $rowData[] = @$shipment->lastHistory->obs;
                $rowData[] = @$shipment->lastHistory->status_id == ShippingStatus::DELIVERED_ID ? @$shipment->lastHistory->created_at : '';
                $rowData[] = @$shipment->lastHistory->receiver;

                $rowData[] = @$shipment->operator->code;
                $rowData[] = @$shipment->operator->name;


                $rowData[] = $shipment->requester_name;
                $rowData[] = $shipment->obs . ' ' . ($shipment->status_id == ShippingStatus::PICKUP_FAILED_ID ? '#### RECOLHA FALHADA ####' : '');
                $rowData[] = $shipment->obs_delivery;
                $rowData[] = $shipment->vehicle;
                $rowData[] = $shipment->trailer;

                $rowData[] = $shipment->obs_internal;

                $rowData[] = $costPrice;

                $rowData[] = $shipmentPrice;
                $rowData[] = $expensesPrice;
                $rowData[] = $totalPrice;

                $rowData[] = $vatRate;

                $registerSystem = "";
                $enterInArmazem = "";
                $registerSystem = $shipment->created_at;
                foreach ($shipment->history as $row) {
                    // if($row->status_id == 5){
                    //     $registerSystem = $row->created_at;
                    // }
                    if ($row->status_id == 17) {
                        $enterInArmazem = $row->created_at;
                    }
                }
                $rowData[] = $registerSystem;
                $rowData[] = $enterInArmazem;

                fputcsv($tmpFile, $rowData, ';', '"', "\\");
            }
        });

        fclose($tmpFile);

        if ($this->storeFTP($filename)) {
            echo 'TMS - Sincronização com sucesso.';
        } else {
            echo 'TMS - Sincronização falhada.';
        }
    }

    /**
     * Export Weblog (3months)
     */
    public function exportWeblog()
    {

        $filename    = 'WEBLOG.csv';
        $localfile   = public_path('uploads/ftp_importer/powerbi/' . $filename);

        $header = [];
        $header[] = 'Tipo';
        $header[] = 'TRK';
        $header[] = 'TRK Secundário';
        $header[] = 'Data Carga';
        $header[] = 'Data Descarga';
        $header[] = 'Cod. Serviço';
        $header[] = 'Serviço';
        $header[] = 'Referência';
        $header[] = 'Referência 2';
        $header[] = 'Referência 3';
        $header[] = 'Agência Origem';
        $header[] = 'Agência Destino';
        $header[] = 'Fornecedor';
        $header[] = 'Cod. Cliente';
        $header[] = 'Nome Cliente';
        $header[] = 'Departamento';
        $header[] = 'A/C remetente';
        $header[] = 'Remetente';
        $header[] = 'Morada Remetente';
        $header[] = 'CP Remetente';
        $header[] = 'Localidade Remetente';
        $header[] = 'Pais Remetente';
        $header[] = 'Contacto Remetente';
        $header[] = 'A/C Destinatário';
        $header[] = 'Destinatário';
        $header[] = 'Morada Destinatário';
        $header[] = 'CP Destinatário';
        $header[] = 'Localidade Destinatário';
        $header[] = 'País Destinatário';
        $header[] = 'Contacto Destinatário';
        $header[] = 'E-mail Destinatário';
        $header[] = 'Volumes';
        $header[] = 'Volume (M3)';
        $header[] = 'Peso (Kg)';
        $header[] = 'Peso Vol. (Kg)';
        $header[] = 'Montagem';
        $header[] = 'Kms';
        $header[] = 'Cobrança';
        $header[] = 'Retorno';
        $header[] = 'Último Estado';
        $header[] = 'Data Último Estado';
        $header[] = 'Motivo Incidência';
        $header[] = 'Observações Estado';
        $header[] = 'Data Entrega';
        $header[] = 'Nome Receptor';
        $header[] = 'Cód. Motorista';
        $header[] = 'Nome Motorista';
        $header[] = 'Solicitado por';
        $header[] = 'Observações Carga';
        $header[] = 'Observações Descarga';
        $header[] = 'Viatura';
        $header[] = 'Reboque';
        $header[] = 'Obs. Internas';
        $header[] = 'Preço Custo';
        $header[] = 'Preço';
        $header[] = 'Encargos';
        $header[] = 'Total';
        $header[] = 'Taxa IVA';
        $header[] = 'Data de Registo em Sistema';
        $header[] = 'Data de Entrada Armazém';


        //apaga o ficheiro local caso exista
        File::delete($localfile);

        //abre para escrita um novo ficheiro vazio.
        $tmpFile = fopen($localfile, 'a');

        //escreve cabeçalho do ficheiro csv
        fputcsv($tmpFile, $header, ';', '"', "\\");

        //SELECT * FROM `shipments` where date>='2022-09-28' and deleted_at is null
        $date = date("Y-m-d");
        $dateToCompare = date('Y-m-d', strtotime($date . ' - 3 months'));

        Shipment::where('date', '>=', $dateToCompare)->chunk(1000, function ($shipments) use (&$tmpFile) {
            foreach ($shipments as $shipment) {
                $rowData = [];
                $customer = Customer::where('id', $shipment->id)->first();
                $shippingDate = new Carbon($shipment->shipping_date);
                $shippingDate = $shippingDate->format('Y-m-d H:i');


                $deliveryDate = '';
                if ($shipment->delivery_date != null && $shipment->delivery_date != "") {
                    $deliveryDate = new Carbon($shipment->delivery_date);
                    $deliveryDate = $deliveryDate->format('Y-m-d H:i');
                }
                // $deliveryDate = new Carbon($shipment->delivery_date);
                // $deliveryDate = $deliveryDate->format('Y-m-d H:i');

                $totalPrice = '';
                $totalPrice = @$shipment->total_price + @$shipment->total_expenses;

                $shipmentPrice = '';
                if ($shipment->total_price > 0.00) {
                    $shipmentPrice = $shipment->total_price;
                }

                $expensesPrice = '';
                if ($shipment->total_expenses > 0.00) {
                    $expensesPrice = $shipment->total_expenses;
                }

                $costPrice = '';
                if ($shipment->cost_price > 0.00) {
                    $costPrice = $shipment->cost_price;
                }

                $vatRate = Setting::get('vat_rate_normal');
                if (($shipment->isExport() && @$customer->is_particular) || @$shipment->service->is_mail) {
                    $vatRate = 0;
                }

                if ($shipment->payment_at_recipient) {
                    $totalPrice = '';
                }

                $totalPrice = $totalPrice > 0.00 ? $totalPrice : '';
                $vatRate    = $totalPrice > 0.00 ? $vatRate : '';

                //start here
                $rowData[] = @$shipmentTypes[$shipment->type] ? $shipmentTypes[$shipment->type] : 'ENVIO';
                $rowData[] = $shipment->tracking_code . ' ';
                $rowData[] = $shipment->provider_tracking_code . ' ';

                $rowData[] = $shippingDate;
                $rowData[] = $deliveryDate;
                $rowData[] = @$shipment->service->display_code;
                $rowData[] = @$shipment->service->name;

                $rowData[] = $shipment->reference;
                $rowData[] = $shipment->reference2;
                $rowData[] = $shipment->reference3;

                $rowData[] = @$shipment->senderAgency->name;
                $rowData[] = @$shipment->recipientAgency->name;
                $rowData[] = @$shipment->provider->name;

                $rowData[] = $shipment->customer ? @$shipment->customer->code : '';
                $rowData[] = $shipment->customer ? @$shipment->customer->name : '';
                $rowData[] = $shipment->department_id ? @$shipment->department->name : '';

                $rowData[] = $shipment->sender_attn;
                $rowData[] = $shipment->sender_name;
                $rowData[] = $shipment->sender_address;
                $rowData[] = $shipment->sender_zip_code;
                $rowData[] = $shipment->sender_city;
                $rowData[] = $shipment->sender_country;
                $rowData[] = $shipment->sender_phone;
                $rowData[] = $shipment->recipient_attn;
                $rowData[] = $shipment->recipient_name;
                $rowData[] = $shipment->recipient_address;
                $rowData[] = $shipment->recipient_zip_code;
                $rowData[] = $shipment->recipient_city;
                $rowData[] = $shipment->recipient_country;
                $rowData[] = $shipment->recipient_phone;
                $rowData[] = $shipment->recipient_email;
                $rowData[] = $shipment->volumes;
                $rowData[] = $shipment->volume_m3;
                $rowData[] = $shipment->weight;
                $rowData[] = $shipment->volumetric_weight;
                $rowData[] = $shipment->has_assembly == 1 ? 'S' : '';
                $rowData[] = $shipment->kms;
                $rowData[] = $shipment->charge_price ? money($shipment->charge_price) : '';
                $rowData[] = $shipment->return_type ? trans('admin/shipments.return.' . $shipment->return_type) : '';
                $rowData[] = @$shipment->status->name;
                $rowData[] = @$shipment->lastHistory->created_at;
                $rowData[] = @$shipment->lastIncidence->incidence->name;
                $rowData[] = @$shipment->lastHistory->obs;
                $rowData[] = @$shipment->lastHistory->status_id == ShippingStatus::DELIVERED_ID ? @$shipment->lastHistory->created_at : '';
                $rowData[] = @$shipment->lastHistory->receiver;

                $rowData[] = @$shipment->operator->code;
                $rowData[] = @$shipment->operator->name;


                $rowData[] = $shipment->requester_name;
                $rowData[] = $shipment->obs . ' ' . ($shipment->status_id == ShippingStatus::PICKUP_FAILED_ID ? '#### RECOLHA FALHADA ####' : '');
                $rowData[] = $shipment->obs_delivery;
                $rowData[] = $shipment->vehicle;
                $rowData[] = $shipment->trailer;

                $rowData[] = $shipment->obs_internal;

                $rowData[] = $costPrice;

                $rowData[] = $shipmentPrice;
                $rowData[] = $expensesPrice;
                $rowData[] = $totalPrice;

                $rowData[] = $vatRate;

                $registerSystem = "";
                $enterInArmazem = "";
                $registerSystem = $shipment->created_at;
                foreach ($shipment->history as $row) {
                    // if($row->status_id == 5){
                    //     $registerSystem = $row->created_at;
                    // }
                    if ($row->status_id == 17) {
                        $enterInArmazem = $row->created_at;
                    }
                }
                $rowData[] = $registerSystem;
                $rowData[] = $enterInArmazem;

                fputcsv($tmpFile, $rowData, ';', '"', "\\");
            }
        });

        fclose($tmpFile);

        if ($this->storeFTP($filename)) {
            echo 'TMS - Sincronização com sucesso.';
        } else {
            echo 'TMS - Sincronização falhada.';
        }
    }

    /**
     * Submit file by FTP
     *
     * @param [type] $filename
     * @return void
     */
    public function storeFTP($filename)
    {

        // connect to FTP server
        try {
            $connectionId = ftp_connect($this->ftpHost, 21, 120);
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot connect to host.');
        }

        //login into FTP
        try {
            $login = ftp_login($connectionId, $this->ftpUser, $this->ftpPass);

            if (!$login) {
                throw new \Exception('Cannot login via FTP');
            }
            ftp_pasv($connectionId, true);
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot login');
        }


        $localfile  = public_path('uploads/ftp_importer/powerbi/' . $filename);
        $remoteFile = 'OUT/' . $filename;

        $result = false;
        if (File::exists($localfile)) {

            $upload = ftp_put($connectionId, $remoteFile, $localfile, FTP_BINARY);

            if ($upload) {
                $result = true;
                File::delete($localfile);
            }
        }

        ftp_close($connectionId);

        return $result;
    }
}
