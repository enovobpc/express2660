<?php

namespace App\Console\Commands;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShipmentPackDimension;
use App\Models\ShippingStatus;
use App\Models\ZipCode\AgencyZipCode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;
use Excel, File, Setting, SimpleXMLElement;

class SyncDsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:dsv {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync DSV';

    protected $host       = '';
    protected $user       = '';
    protected $customer   = null;
    protected $service_id = null;

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
    public function handle() {
        
        $this->info("Sync data with DSV");

        if (config('app.env') == 'local') {
            $this->host       = 'test.b2b.dsv.net';
            $this->user       = 'cus.invicta';
            $this->customer   = Customer::first();
            $this->service_id = 2;
        } else if (config('app.source') == 'invictacargo') {
            $this->host       = 'test.b2b.dsv.net';
            $this->user       = 'cus.invicta';
            $this->customer   = Customer::filterSource()->find(173);
            $this->service_id = 2;
        }

        if($this->argument('action') == 'import') {
            $this->importServices();
        } else if ($this->argument('action') == 'status') {
            $this->comunicateStatus();
        }

        $this->info("Sync finalized");
        return;
    }

    public function importServices() {
        // FTP access parameters
        $remoteFolder = './outbox';
        $localFolder  = public_path('uploads/ftp_importer/dsv/import');

        if (!file_exists($localFolder)) {
            mkdir($localFolder);
        }

        $privateKey = new RSA();
        $privateKey->setPassword('dsv#EN0V0!');
        $privateKey->loadKey(Storage::disk('local')->get('dsv'));

        $sftp = new SFTP($this->host);
        $sftp->login($this->user, $privateKey);
        $files = $sftp->nlist($remoteFolder);

        // dd($files);

        // if (!$files) {
        //     return;
        // }

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $remoteFile = $remoteFolder . '/' . $file;
            $localFile = $localFolder . '/' . $file;
            $sftp->get($remoteFile, $localFile); //download from remote
        }

        $files = File::files($localFolder);
        if (!$files) {
            return;
        }

        foreach ($files as $file) {
            $xml = new SimpleXMLElement(File::get($file));

            // dd($xml->Header);

            if ($xml->Header->Function == 'Delete') {
                foreach ($xml->Transport->Shipments->Shipment as $shipment) {
                    $shipmentModel = Shipment::where('customer_id', $this->customer->id)
                        ->where('reference', $shipment->ShipmentId)
                        ->first();

                    if ($shipmentModel) {
                        $shipmentModel->status_id = ShippingStatus::CANCELED_ID;
                        $shipmentModel->save();

                        $history = new ShipmentHistory();
                        $history->status_id = $shipmentModel->status_id;
                        $history->agency_id = $shipmentModel->agency_id;
                        $history->shipment_id = $shipmentModel->id;
                        $history->api = 1;
                        $history->save();
                    }
                }
            } else if ($xml->Header->Function == 'New') {
                $errorsBag = [];
                foreach ($xml->Transport->Shipments->Shipment as $shipment) {
                    try {
                        $shipmentModel = Shipment::filterSource()
                            ->firstOrNew([
                                'customer_id'   => $this->customer->id,
                                'reference'     => (string)$shipment->ShipmentId
                            ]);
                        
                        if ($shipmentModel->exists) {
                            continue;
                        }

                        $shipmentModel->source = config('app.source');
    
                        foreach ($shipment->Dates as $date) {
                            $date = $date->Date;

                            if ($date['Type'] == 'Collection') {
                                // 20230301000000
                                $date = (string)$date->DateTime;
                                $date = sprintf("%s-%s-%s", substr($date, 0, 4), substr($date, 4, 2), substr($date, 6, 2));
                                $shipmentModel->date         = $date;
                                $shipmentModel->billing_date = $date;
                                break;
                            }
                        }

                        $shipmentModel->billing_date = $shipmentModel->date;

                        $shipmentModel->agency_id           = $this->customer->agency_id;
                        $shipmentModel->sender_agency_id    = $shipmentModel->agency_id;
                        $shipmentModel->provider_id         = Setting::get('shipment_default_provider');
                        $shipmentModel->service_id          = $this->service_id;
                        $shipmentModel->status_id           = ShippingStatus::PENDING_ID;
    
                        /** Sender data */
                        foreach ($shipment->Parties->Party as $party) {
                            $party = $party->PartyDetails;
                            if ($party['Role'] == 'Despatch') {
                                $shipmentModel->sender_name         = (string)$party->PartyName;
                                $shipmentModel->sender_address      = (string)$party->AddressLine1;
                                $shipmentModel->sender_city         = (string)$party->CityName;
                                $shipmentModel->sender_country      = (string)$party->CountryCode;
                                $shipmentModel->sender_zip_code     = (string)$party->PostCode;
                                $shipmentModel->sender_phone        = (string)$xml->Transport->TransportStages->TransportStage->Parties->Contact->Phone;
                                break;
                            }
                        }
                        /**-- */
    
                        /** Recipient data */
                        foreach ($shipment->Parties->Party as $party) {
                            $party = $party->PartyDetails;
                            if ($party['Role'] == 'Delivery') {
                                $shipmentModel->recipient_name      = (string)$party->PartyName;
                                $shipmentModel->recipient_address   = (string)$party->AddressLine1;
                                $shipmentModel->recipient_city      = (string)$party->CityName;
                                $shipmentModel->recipient_country   = (string)$party->CountryCode;
                                $shipmentModel->recipient_zip_code  = (string)$party->PostCode;
                                $shipmentModel->recipient_phone     = (string)$shipment->Parties->Contact->Phone;
                                break;
                            }
                        }
                        /**-- */
    
                        /** Cargo data */
                        foreach ($shipment->ControlTotals->ControlTotal as $controlTotal) {
                            if ($controlTotal['Type'] == 'GrossWeight') {
                                $shipmentModel->weight = (float)$controlTotal->Quantity;
                            } else if ($controlTotal['Type'] == 'Volume') {
                                $shipmentModel->volume_m3 = (float)$controlTotal->Quantity;
                            } else if ($controlTotal['Type'] == 'NumberOfPackages') {
                                $shipmentModel->volumes = (int)$controlTotal->Quantity;
                            }
                        }
    
                        if (!$shipmentModel->weight) { $shipmentModel->weight = 1.0; }
                        if (!$shipmentModel->volumes) { $shipmentModel->volumes = 1; }
                        /**-- */
    
                        // dd($shipmentModel->toArray());
    
                        /** Validate shipment */
                        $validator = Validator::make($shipmentModel->toArray(), $this->storeRules());
                        if ($validator->fails()) {
                            $errorsBag[$shipmentModel->reference] = $validator->errors();
                            continue;
                        }
                        /**-- */

                        // Detect recipient agency
                        $fullZipCode  = $shipmentModel->recipient_zip_code;
                        $zipCodeParts = explode('-', $fullZipCode);
                        $zipCode4     = $zipCodeParts[0];
                        $zipCode = AgencyZipCode::where(function ($q) use ($fullZipCode, $zipCode4) {
                            $q->where('zip_code', $zipCode4);
                            $q->orWhere('zip_code', $fullZipCode);
                        })
                            ->where('country', @$shipmentModel->recipient_country)
                            ->orderBy('zip_code', 'desc')
                            ->first();

                            $shipmentModel->recipient_agency_id = @$zipCode->agency_id ?? @$this->customer->agency_id;
                        //--
    
                        $shipmentModel->setTrackingCode();
                        $shipmentModel->save();

                        /**
                         * Pack Dimensions
                         */
                        foreach ($shipment->GoodsItems->GoodsItem as $goodsItem) {
                            $textLines   = $goodsItem->FreeTextData->FreeText;
                            $description = (string)$textLines->TextLine1 . ' '
                                . (string)$textLines->TextLine2 . ' '
                                . (string)$textLines->TextLine3 . ' '
                                . (string)$textLines->TextLine4;

                            for ($i = 0; $i < (int)$goodsItem->Item->PackageQty; $i++) {
                                $packDimension = new ShipmentPackDimension;
                                $packDimension->shipment_id = $shipmentModel->id;
                                $packDimension->qty = 1;
                                $packDimension->description = $description;
                                $packDimension->pack_no = $i + 1;
                                $packDimension->type = 'box';
                                $packDimension->barcode = '00' . (string)$goodsItem->PackageIds->GoodsIdentities->GoodsIdentifier[$i];
                                $packDimension->save();
                            }
                        }
                        /**-- */
    
                        $route = Route::getRouteFromZipCode($shipment->recipient_zip_code);
                        $shipmentModel->route_id = empty($route) ? '' : $route->id;
                        if (empty($shipmentModel->operator_id)) {
                            $shipmentModel->operator_id = empty($route) ? '' : $route->operator_id;
                        }

                        if (config('app.source') == 'invictacargo') {
                            $prices = Shipment::calcPrices($shipmentModel);
                            $shipmentModel->cost_price     = @$prices['cost'];
                            $shipmentModel->total_expenses = @$prices['totalExpenses'];
                            $shipmentModel->zone           = @$prices['zone'];
                            $shipmentModel->fuel_tax       = @$prices['fuelTax'];
                            $shipmentModel->extra_weight   = @$prices['extraKg'];

                            if ($shipmentModel->payment_at_recipient) {
                                $shipmentModel->total_price = 0;
                                $shipmentModel->total_price_for_recipient = $prices['total'];
                            } else {
                                $shipmentModel->total_price = $prices['total'];
                            }

                            $shipmentModel->save();
                        } else {
                            $prices = Shipment::calcPrices($shipmentModel) ?? [];
                            if (!empty($prices)) {
                                $shipmentModel->fill($prices['fillable']);
                                $shipmentModel->save();
                                $shipmentModel->storeExpenses($prices);
                            } else {
                                $shipmentModel->save();
                            }
                        }
    
                        $history = new ShipmentHistory();
                        $history->status_id   = $shipmentModel->status_id;
                        $history->agency_id   = $shipmentModel->agency_id;
                        $history->shipment_id = $shipmentModel->id;
                        $history->api         = 1;
                        $history->save();

                        /**
                         * Set notification
                         */
                        $shipmentModel->setNotification(BroadcastPusher::getGlobalChannel());
                    } catch (\Exception $e) {
                        \Log::error($e);
                    }
                }

                if ($errorsBag) {
                    \Log::error($errorsBag);
                }
            }

            $processedFolter = $localFolder . '/Processed';
            if (!file_exists($processedFolter)) {
                mkdir($processedFolter);
            }

            File::move($file, $processedFolter);
            // File::delete($file);
        }
    }

    public function comunicateStatus() {
        $shipments = Shipment::filterSource()
            ->with(['history'])
            ->where('customer_id', $this->customer->id)
            ->get();

        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <DSV_StatusMessage>
                <Header>
                    <MessageId>INVT' . date('YmdHi') . '</MessageId>
                    <SenderId>INVT</SenderId>
                    <ReceiverId>DSV</ReceiverId>
                    <DocumentDate Type="DocumentDate" Format="DateTime">
                        <DateTime>'. Carbon::now()->format('YmdHi') .'</DateTime>
                    </DocumentDate>
                    <DocumentCode>ConsignmentStatusReport</DocumentCode>
                    <Function>New</Function>
                    <Version>1.0</Version>
                </Header>
                <ShipmentStatus>
                    <Dates>
                        <Date Type="IssueDate" Format="DateTime">
                            <DateTime>'. Carbon::now()->format('YmdHi') .'</DateTime>
                        </Date>
                    </Dates>
                    <Locations>
                        <Location Type="BranchLocation">
                            <LocationIdentification Type="ISOCountry">
                                <LocationIdentifier>PT</LocationIdentifier>
                            </LocationIdentification>
                        </Location>
                    </Locations>
                    <Shipments>';

        foreach ($shipments as $shipment) {
            if (!$shipment->history) {
                continue;
            }

            $foundAnyStatus = false;
            $shipmentStatus  = '<Shipment><ShipmentId>'. $shipment->reference .'</ShipmentId>';
            foreach ($shipment->history as $history) {
                $status = $this->mapStatus($history);
                if (!$status) {
                    continue;
                }

                $foundAnyStatus = true;
                $shipmentStatus .= '<StatusDetails>
                    <Status Type="Consignment">
                        <StatusEventCode>'. $status .'</StatusEventCode>
                    </Status>
                    <Dates>
                        <Date Type="StatusDate" Format="DateTime">
                            <DateTime>'. $history->created_at->format('YmdHi') .'</DateTime>
                        </Date>
                    </Dates>
                    <Locations>
						<Location Type="PlaceOfRegistration">
							<LocationIdentification Type="SiteId">
								<LocationIdentifier>INVT</LocationIdentifier>
								<LocationName>INVT</LocationName>
							</LocationIdentification>
						</Location>
					</Locations>
					<TransportStages>
						<TransportStage>
							<Stage Type="MainCarriage">
								<ModeOfTransport>Road</ModeOfTransport>
							</Stage>
						</TransportStage>
					</TransportStages>
                </StatusDetails>';
            }
            $shipmentStatus .= '</Shipment>';

            if ($foundAnyStatus) {
                $xml .= $shipmentStatus;
            }
        }

        $xml .= '</Shipments>
            </ShipmentStatus>
        </DSV_StatusMessage>';

        $xml = str_replace(PHP_EOL, '', $xml);

        $localFolder  = public_path('uploads/ftp_importer/dsv/export');
        if (!file_exists($localFolder)) {
            mkdir($localFolder);
        }

        // Store locally the sent file
        $fileName = 'INVT'. date('YmdH') .'.xml';
        File::put($localFolder.'/'.$fileName, $xml);

        // SFTP access
        $remoteFolder = './inbox';
        $sftp = new SFTP($this->host);
        $privateKey = new RSA();
        $privateKey->setPassword('dsv#EN0V0!');
        $privateKey->loadKey(Storage::disk('local')->get('dsv'));

        $sftp->login($this->user, $privateKey);
        // Store remotely status file
        $sftp->put($remoteFolder.'/'.$fileName, $xml);
    }

    public function mapStatus($history) {
        $statusId    = $history->status_id;
        $incidenceId = $history->incidence_id;

        /**
         * Arrival              Arrival (1 - R)
         * ArriveCollection     Arrive Collection ( 153- R)
         * ArriveDelivery       Arrive Delivery (154 - R)
         * CancelCollection     Cancel Collection (157 - R)
         * CancelDelivery       Cancel Delivery (158 - R)
         * Collected            Collected (13 - R)
         * CollectionReloaded   Collection Reloaded (152 - R)
         * Delivered            Delivered (21 - R)
         * Delivery             Delivery (14 - R)
         * DeliveryReloaded     Delivery Reloaded (151 - R)
         * Departed             Departed / Loaded (48 - R)
         * Departure            Departure/ Loaded (27 - R)
         * KeptInStorage        Unknown/Kept in storage (90 - R)
         * Measured             Measured (127 - R)
         * NotCollected         Not Collected (54 - R)
         * NotDelivered         Not Delivered (55 - R)
         * StartCollection      Start Collection (155 - R)
         * StartDelivery        Start Delivery (156 - R)
         */

        if (in_array($statusId, [ShippingStatus::DELIVERED_ID])) {
            return 'Delivered';
        }

        if (in_array($statusId, [ShippingStatus::IN_DISTRIBUTION_ID])) {
            return 'Departed';
        }

        if (in_array($statusId, [ShippingStatus::SHIPMENT_PICKUPED])) {
            return 'Collected';
        }

        if (in_array($statusId, [ShippingStatus::IN_PICKUP_ID])) {
            return 'StartCollection';
        }

        if (in_array($statusId, [ShippingStatus::INCIDENCE_ID])) {
            if (in_array($incidenceId, [23])) { // Tentativa de Recolha Falhada
                return 'NotCollected';
            }
        }

        return null;
    }

    public function storeRules() {
        return [
            'sender_name'           => 'required',
            'sender_address'        => 'required',
            'sender_zip_code'       => 'required',
            'sender_city'           => 'required',
            'sender_country'        => 'required',
            // 'sender_phone'          => 'required',
            'recipient_name'        => 'required',
            'recipient_address'     => 'required',
            'recipient_zip_code'    => 'required',
            'recipient_city'        => 'required',
            'recipient_country'     => 'required',
            // 'recipient_phone'       => 'required',
            'volumes'               => 'numeric',
            'weight'                => 'numeric',
        ];
    }

}
