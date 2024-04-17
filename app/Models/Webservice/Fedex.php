<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Date, File, View, Setting;
use App\Models\ShipmentHistory;

use FedEx\RateService\ComplexType\ShipmentSpecialServicesRequested;
use FedEx\RateService\SimpleType\ShipmentSpecialServiceType;
use FedEx\ShipService;
use FedEx\ShipService\ComplexType;
use FedEx\ShipService\SimpleType;
use Mockery\Exception;

class Fedex extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    //https://developer.fedex.com/api/en-us/catalog/rate/v1/docs.html
    private $url = 'https://ws.fedex.com:443/web-services/ship';
    private $urlTest = 'https://wsbeta.fedex.com:443/web-services/ship';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/fedex/';

    /**
     * @var string
     */
    private $meterNumber;

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @var string
     */
    private $password;

    /**
     * @var null
     */
    private $session_id = null;

    /**
     * Tipsa constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($fedexMeterNumber, $fedexAccountNumber, $fedexPassword, $fedexKey, $department = null, $endpoint = null, $debug = false)
    {
        $this->session_id    = $fedexKey;
        $this->password      = $fedexPassword;
        $this->accountNumber = $fedexAccountNumber;
        $this->meterNumber   = $fedexMeterNumber;

        //test aveirofast
        /*  $this->session_id    = 'IO0TCGlTsqT8A8s7';
        $this->password      = '8rOXA3y7ULkE43HVVd3VBXGGF';
        $this->accountNumber = '510087500';
        $this->meterNumber   = '119014791';*/

        //production intercourier
        /* $this->session_id    = 'e1EeRa2Q04e7P9HY';
        $this->password      = 'TilIyODlLWrNvQN1h0xbggz4a';
        $this->accountNumber = '456787460';
        $this->meterNumber   = '112960641';*/

        //production aveirofast
        /* $this->session_id    = '1VK3NMwVcnf48cRK';
        $this->password      = 'KK67MfYwCzVWM44mFRqNXeIQ6';
        $this->accountNumber = '304411015';
        $this->meterNumber   = '112900048';*/
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($codAgeCargo = null, $codAgeOri = null, $trakingCode)
    {

        $trackingId = $trakingCode;

        $userCredential = new \FedEx\TrackService\ComplexType\WebAuthenticationCredential();
        $userCredential->setKey($this->session_id)
            ->setPassword($this->password);

        $webAuthenticationDetail = new \FedEx\TrackService\ComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $clientDetail = new \FedEx\TrackService\ComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber($this->accountNumber)
            ->setMeterNumber($this->meterNumber);

        $version = new \FedEx\TrackService\ComplexType\VersionId();
        $version->setMajor(5)
            ->setIntermediate(0)
            ->setMinor(0)
            ->setServiceId('trck');

        $identifier = new \FedEx\TrackService\ComplexType\TrackPackageIdentifier();
        $identifier
            ->setType(\FedEx\TrackService\SimpleType\TrackIdentifierType::_TRACKING_NUMBER_OR_DOORTAG)
            ->setValue($trackingId);

        $request = new \FedEx\TrackService\ComplexType\TrackRequest();
        $request->setWebAuthenticationDetail($webAuthenticationDetail)
            ->setClientDetail($clientDetail)
            ->setVersion($version)
            ->setPackageIdentifier($identifier)
            ->setIncludeDetailedScans(false);

        $trackService = new \FedEx\TrackService\Request();
        $trackService->getSoapClient()->__setLocation('https://ws.fedex.com:443/web-services/track');
        $response = $trackService->getTrackReply($request);
        $response = $response->toArray();

        $response  = @$response['TrackDetails'][0];

        if ($response && !empty($response['Events'])) {
            $weight    = @$response['ShipmentWeight']['Value'];
            $receiver  = @$response['DeliverySignatureName'];
            $events   = @$response['Events'][0];

            $eventCountry = strtoupper(@$events['Address']['CountryCode']);
            $eventCity    = @$events['Address']['City'];

            $location = '';
            if ($eventCity || $eventCountry) {
                $location = $eventCity . ' (' . $eventCountry . ')';
            }

            $data = [
                'status_code' => $events['EventType'],
                'description' => $events['EventDescription'],
                'weight'      => $weight,
                'receiver'    => $receiver,
                'obs'         => $location,
                'created_at'  => $events['Timestamp']
            ];

            return [$data];
        }

        return false;
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode)
    {
    }

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date)
    {
    }

    /**
     * Devolve o URL do comprovativo de entrega
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode)
    {
        return false;
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia)
    {
    }

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date)
    {
    }

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
    }


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {
    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getRecolhaByTrk($trakingCode)
    {
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeRecolha($data)
    {
        throw new Exception('Não é possível criar recolhas via FedEx.');
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $requestedShipment = new ComplexType\RequestedShipment();
        $requestedShipment->setShipTimestamp($data['date']);
        $requestedShipment->setDropoffType(new SimpleType\DropoffType(SimpleType\DropoffType::_REGULAR_PICKUP));
        //$requestedShipment->setServiceType(new SimpleType\ServiceType(SimpleType\ServiceType::_INTERNATIONAL_PRIORITY)); //service
        $requestedShipment->setServiceType($data['service']);
        $requestedShipment->setPackagingType(new SimpleType\PackagingType($data['packaging']));
        $requestedShipment->setShipper($this->addShipper($data)); //shipper data
        $requestedShipment->setRecipient($this->addRecipient($data)); //recipient data
        $requestedShipment->setLabelSpecification($this->addLabelSpecification()); //set label config
        $requestedShipment->setRateRequestTypes(array(new SimpleType\RateRequestType(SimpleType\RateRequestType::_PREFERRED)));
        $requestedShipment->setShippingChargesPayment($this->addChargesPayment($data)); //valores de pagamento
        $requestedShipment->setCustomsClearanceDetail($this->addCustomsClearanceDetail($data['weight'], $data['volumes'])); //customs details
        /*$requestedShipment->setSpecialServicesRequested(
            $this->addSpecialServicesRequested(['ELECTRONIC_TRADE_DOCUMENTS'])
        );*/
        $requestedShipment->setPackageCount($data['volumes']); //volumes
        $requestedShipment->setTotalWeight($this->addWeight($data['weight'])); //weight

        $masterDetails = [];
        $weightPerVolume = $data['weight'] / $data['volumes'];
        for ($volumeNum = 1; $volumeNum <= $data['volumes']; $volumeNum++) {

            $requestedShipment->setRequestedPackageLineItems([$this->addPackage($volumeNum, $weightPerVolume)]);

            if ($volumeNum > 1) { //set master trk to the package
                $trackingId = new ComplexType\TrackingId();
                $trackingId->setFormId($masterDetails['FormId']);
                $trackingId->setTrackingIdType($masterDetails['TrackingIdType']);
                $trackingId->setTrackingNumber($masterDetails['TrackingNumber']);
                $requestedShipment->setMasterTrackingId($trackingId);
            }

            $result = $this->shipmentRequest($requestedShipment); //1 request by each package

            if ($result['status'] == 'ERROR' || $result['status'] == 'FAILURE') { //webservice failure
                throw new Exception($result['message'] . ' Processados ' . $volumeNum . ' de ' . $data['volumes'] . ' volumes');
                exit;
            } else {
                $trk   = $result['trk'];
                $label = $result['label'];

                if ($volumeNum == 1) {
                    $masterDetails = $result['master'];
                }

                if (!empty($label)) {

                    if (!File::exists(public_path() . $this->upload_directory)) {
                        File::makeDirectory(public_path() . $this->upload_directory, 0777, true, true);
                    }

                    $storageLabel = File::put(public_path() . $this->upload_directory . $trk . '_' .  $volumeNum . '_labels.pdf', $label);

                    if ($storageLabel === false) {
                        throw new \Exception('Não foi possível gravar a etiqueta.');
                    }
                }
            }
        }

        $tempPdfs = [];
        for ($volumeNum = 1; $volumeNum <= $data['volumes']; $volumeNum++) {
            $tempPdfs[] = public_path() . $this->upload_directory . $trk . '_' .  $volumeNum . '_labels.pdf';
        }

        //Merge files
        $pdf = new \LynX39\LaraPdfMerger\PdfManage;
        foreach ($tempPdfs as $filepath) {
            $pdf->addPDF($filepath, 'all');
        }


        //Save merged file
        $filepath = public_path() . $this->upload_directory . $trk . '.pdf';
        $result = $pdf->merge('File', $filepath);

        File::delete($tempPdfs);

        return $trk;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiqueta($senderAgency, $trackingCode)
    {
        $file = File::get(public_path() . $this->upload_directory . $trackingCode . '.pdf');
        $file = base64_encode($file);
        return $file;
    }

    /**
     * Devolve as informações completas dos envios e respetivo POD de entrega dos envios numa data
     *
     * @param type $date
     * @param type $tracking Se indicado, devolve a informação apenas para o envio com o tkr indicado
     * @return type
     */
    public function InfEnvEstPOD($date, $tracking = null)
    {
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/



    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {

        if ($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk($shipment->provider_tracking_code);
        } else {
            $data = self::getEstadoEnvioByTrk(
                $shipment->provider_cargo_agency,
                $shipment->provider_sender_agency,
                $shipment->provider_tracking_code
            );
        }

        if ($data) {

            foreach ($data as $item) {

                $fedexStatus = config('shipments_import_mapping.fedex-status');
                $item['status_id'] = @$fedexStatus[$item['status_code']];
                $item['created_at'] = new Date($item['created_at']);

                if ($item['status_id'] == '9') {

                    $fedexIncidences = config('shipments_import_mapping.fedex-incidences');

                    $incidenceId = @$fedexIncidences[$item['status_id']];
                    if ($incidenceId) {
                        $item['incidence_id'] = $incidenceId;
                    }
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id' => $shipment->id,
                    'created_at'  => $item['created_at'],
                    'status_id'   => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;
            }

            try {
                $history->sendEmail(false, false, true);
            } catch (\Exception $e) {
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at;
            $shipment->save();

            if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                $shipment->addPickupFailedExpense();
            }

            return true;
        }
        return false;
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false)
    {

        $service = $this->getProviderService($shipment);

        $reference =  $shipment->reference ? ' - ' . $shipment->reference : '';

        $shipment->sender_zip_code = explode('-', $shipment->sender_zip_code);
        $shipment->sender_zip_code = $shipment->sender_zip_code[0];
        $shipment->sender_zip_code = str_replace('-', '', $shipment->sender_zip_code);

        $shipment->recipient_zip_code = explode('-', $shipment->recipient_zip_code);
        $shipment->recipient_zip_code = $shipment->recipient_zip_code[0];
        $shipment->recipient_zip_code = str_replace('-', '', $shipment->recipient_zip_code);

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        if ($shipment->packaging_type == 'box10') {
            $packaging = 'FEDEX_10KG_BOX';
        } elseif ($shipment->packaging_type == 'box25') {
            $packaging = 'FEDEX_25KG_BOX';
        } elseif ($shipment->packaging_type == 'envelope') {
            $packaging = 'FEDEX_ENVELOPE';
        } elseif ($shipment->packaging_type == 'box') {
            $packaging = 'FEDEX_BOX';
        } else {
            //$packaging = 'YOUR_PACKAGING';
            $packaging = 'FEDEX_PAK';
        }

        $shipmentWeight = $shipment->weight;
        if(config('app.source') == 'ship2u' && $shipment->service->is_internacional) {
               $shipmentWeight = '0.1';
        }

        $stateUS = '';
        if($shipment->recipient_country == 'us'){
            $stateUS = $shipment->recipient_state;
        }


        $data = [
            "date"               => date('c'), //new Date($shipment->date),
            "service"            => $service,
            "volumes"            => $shipment->volumes,
            "weight"             => $shipmentWeight,
            "charge_price"       => $shipment->charge_price ? forceDecimal($shipment->charge_price) : 0,
            "sender_name"        => $shipment->sender_name,
            "sender_address"     => $shipment->sender_address,
            "sender_city"        => $shipment->sender_city,
            "sender_country"     => strtoupper($shipment->sender_country),
            "sender_zip_code"    => $shipment->sender_zip_code,
            "sender_phone"       => $shipment->sender_phone,
            "recipient_attn"     => $shipment->recipient_attn,
            "recipient_name"     => $shipment->recipient_name,
            "recipient_address"  => $shipment->recipient_address,
            "recipient_city"     => $shipment->recipient_city,
            "recipient_country"  => strtoupper($shipment->recipient_country),
            "recipient_state"    => $stateUS,
            "recipient_zip_code" => $shipment->recipient_zip_code,
            "recipient_phone"    => $shipment->recipient_phone,
            "recipient_phone"    => $shipment->recipient_phone,
            "observations"       => $shipment->obs,
            "reference"          => 'TRK' . $shipment->tracking_code . $reference,
            "packaging"          => $packaging
        ];

        if ($isCollection) {
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data);
        }
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment)
    {

        $userCredential = new ComplexType\WebAuthenticationCredential();
        $userCredential
            ->setKey($this->session_id)
            ->setPassword($this->password);

        $webAuthenticationDetail = new ComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $clientDetail = new ComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber($this->accountNumber)
            ->setMeterNumber($this->meterNumber);

        $version = new ComplexType\VersionId();
        $version
            ->setServiceId('ship')
            ->setMajor(21)
            ->setIntermediate(0)
            ->setMinor(0);

        $trackingId = new ComplexType\TrackingId();
        $trackingId
            ->setTrackingNumber($shipment->provider_tracking_code)
            ->setTrackingIdType(SimpleType\TrackingIdType::_FEDEX);


        $deleteShipmentRequest = new ComplexType\DeleteShipmentRequest();
        $deleteShipmentRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        $deleteShipmentRequest->setClientDetail($clientDetail);
        $deleteShipmentRequest->setVersion($version);
        $deleteShipmentRequest->setTrackingId($trackingId);
        $deleteShipmentRequest->setDeletionControl(SimpleType\DeletionControlType::_DELETE_ALL_PACKAGES);


        $validateShipmentRequest = new ShipService\Request();
        $validateShipmentRequest->getSoapClient()->__setLocation('https://ws.fedex.com:443/web-services/ship');
        $result = $validateShipmentRequest->getDeleteShipmentReply($deleteShipmentRequest);
        $result = $result->toArray();

        $status = @$result['HighestSeverity'];
        $notification = '[' . $result['Notifications'][0]['Code'] . '] ' . $result['Notifications'][0]['Message'];

        if ($status == 'SUCCESS') {
            return true;
        } else {
            throw new Exception($notification);
        }
    }

    /**
     * Add Web Authentication
     *
     * @return ComplexType\WebAuthenticationDetail
     */
    public function addWebAuthentication()
    {
        $userCredential = new ComplexType\WebAuthenticationCredential();
        $userCredential
            ->setKey($this->session_id)
            ->setPassword($this->password);

        $webAuthenticationDetail = new ComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        return $webAuthenticationDetail;
    }

    /**
     * Add client detail
     *
     * @return ComplexType\ClientDetail
     */
    public function addClientDetail()
    {
        $clientDetail = new ComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber($this->accountNumber)
            ->setMeterNumber($this->meterNumber);

        return $clientDetail;
    }

    /**
     * Add shipment shipper
     *
     * @param $shipment
     * @return ComplexType\Party
     */
    public function addShipper($shipment)
    {
        $shipperAddress = new ComplexType\Address();
        $shipperAddress
            ->setStreetLines([$shipment['sender_address']])
            ->setCity($shipment['sender_city'])
            ->setStateOrProvinceCode(@$shipment['sender_state'])
            ->setPostalCode($shipment['sender_zip_code'])
            ->setCountryCode(strtoupper($shipment['sender_country']));

        $shipperContact = new ComplexType\Contact();
        $shipperContact
            ->setCompanyName($shipment['sender_name'])
            //->setEMailAddress('test@example.com')
            ->setPersonName($shipment['sender_name'])
            ->setPhoneNumber($shipment['sender_phone']);

        $shipper = new ComplexType\Party();
        $shipper
            ->setAccountNumber($this->accountNumber)
            ->setAddress($shipperAddress)
            ->setContact($shipperContact);

        return $shipper;
    }

    /**
     * Add shipment recipient
     *
     * @param $shipment
     * @return ComplexType\Party
     */
    public function addRecipient($shipment)
    {
        $recipientAddress = new ComplexType\Address();
        $recipientAddress
            ->setStreetLines([$shipment['recipient_address']])
            ->setCity($shipment['recipient_city'])
            ->setStateOrProvinceCode(@$shipment['recipient_state'])
            ->setPostalCode($shipment['recipient_zip_code'])
            ->setCountryCode(strtoupper($shipment['recipient_country']));

        $recipientContact = new ComplexType\Contact();
        $recipientContact
            ->setPersonName($shipment['recipient_name'])
            ->setPhoneNumber($shipment['recipient_phone']);

        $recipient = new ComplexType\Party();
        $recipient
            ->setAddress($recipientAddress)
            ->setContact($recipientContact);

        return $recipient;
    }

    /**
     * Add shipment package
     *
     * @param $packageCount
     * @param $weight
     * @return ComplexType\RequestedPackageLineItem
     */
    public function addPackage($packageCount, $weight)
    {

        $packageLineItem = new ComplexType\RequestedPackageLineItem();
        $packageLineItem
            ->setGroupPackageCount(1)
            ->setSequenceNumber($packageCount)
            ->setItemDescription('Product description ' . $packageCount)
            ->setWeight(new ComplexType\Weight(array(
                'Value' => $weight,
                'Units' => SimpleType\WeightUnits::_KG
            )));

        return $packageLineItem;
    }

    /**
     * Add shipment label details
     *
     * @return ComplexType\LabelSpecification
     */
    public function addLabelSpecification()
    {
        $labelSpecification = new ComplexType\LabelSpecification();
        $labelSpecification
            ->setLabelStockType(new SimpleType\LabelStockType(SimpleType\LabelStockType::_STOCK_4X6))
            ->setImageType(new SimpleType\ShippingDocumentImageType(SimpleType\ShippingDocumentImageType::_PDF))
            ->setLabelFormatType(new SimpleType\LabelFormatType(SimpleType\LabelFormatType::_COMMON2D));

        return $labelSpecification;
    }

    /**
     * Add Version
     *
     * @return ComplexType\VersionId
     */
    public function addVersion()
    {

        $major = 21;
        $intermediate = 0;

        if (config('app.source') == 'hunterex') {
            $major = 12;
            $intermediate = 1;
        }

        $version = new ComplexType\VersionId();
        $version
            ->setMajor($major)
            ->setIntermediate($intermediate)
            ->setMinor(0)
            ->setServiceId('ship');

        return $version;
    }

    /**
     * Add payment weight
     *
     * @param $weight
     * @return ComplexType\Weight
     */
    public function addWeight($weight)
    {

        $totalWeight = new ComplexType\Weight(array(
            'Units' => SimpleType\WeightUnits::_KG,
            'Value' => $weight,
        ));

        return $totalWeight;
    }

    /**
     * Add shipment charges payment info
     *
     * @param $shipment
     * @return ComplexType\Payment
     */
    public function addChargesPayment($shipment)
    {
        $shippingChargesPayor = new ComplexType\Payor();
        $shippingChargesPayor->setResponsibleParty($this->addShipper($shipment));

        $shippingChargesPayment = new ComplexType\Payment();
        $shippingChargesPayment
            ->setPaymentType(SimpleType\PaymentType::_RECIPIENT)
            ->setPayor($shippingChargesPayor);

        return $shippingChargesPayment;
    }

    /**
     * Add special services
     */
    public function addSpecialServicesRequested($serviceTypes)
    {

        $specialServices = new ComplexType\ShipmentSpecialServicesRequested();
        $specialServices->setSpecialServiceTypes($serviceTypes);
        return $specialServices;
    }

    /**
     * Add Customs Clearance Detail for internacional shipments
     *
     * @param $weight
     * @param $volumes
     * @return ComplexType\CustomsClearanceDetail
     */
    public function addCustomsClearanceDetail($weight, $volumes)
    {

        $commodity = new ComplexType\Commodity();
        $commodity->setName("BOOKS");
        $commodity->setDescription("Books Ready Artwork");
        $commodity->setCountryOfManufacture('US');
        $commodity->setNumberOfPieces(1);
        $commodity->setQuantity($volumes);
        $commodity->setQuantityUnits('UN');
        $commodity->setWeight($this->addWeight($weight));

        $commodity->setUnitPrice(new ComplexType\Money([
            'Currency' => 'EUR',
            'Amount'   => 1.00
        ]));

        $customsClearanceDetail = new ComplexType\CustomsClearanceDetail();
        $customsClearanceDetail->setCustomsValue(new ComplexType\Money([
            'Currency' => 'EUR',
            'Amount'   => 1.00
        ]));

        $dutiesPayment = new ComplexType\Payment();
        $dutiesPayment->setPaymentType('SENDER');
        $payor = new ComplexType\Payor();
        $party = new ComplexType\Party();
        $party->setAccountNumber($this->accountNumber);

        $contact = new ComplexType\Contact();
        $contact->setCompanyName('N/A');
        $contact->setPersonName('N/A');
        $contact->setPhoneNumber('+351910111222');
        $party->setContact($contact);
        $payor->setResponsibleParty($party);
        $dutiesPayment->setPayor($payor);

        $customsClearanceDetail->setCommodities([$commodity]);
        $customsClearanceDetail->setDutiesPayment($dutiesPayment);

        return $customsClearanceDetail;
    }

    /**
     * Submit shipment request to fedex webservice
     *
     * @param $requestedShipment
     * @return array|ComplexType\ProcessShipmentReply|ShipService\stdClass
     */
    public function shipmentRequest($requestedShipment)
    {

        $processShipmentRequest = new ComplexType\ProcessShipmentRequest();
        $processShipmentRequest->setWebAuthenticationDetail($this->addWebAuthentication());
        $processShipmentRequest->setClientDetail($this->addClientDetail());
        $processShipmentRequest->setVersion($this->addVersion());
        $processShipmentRequest->setRequestedShipment($requestedShipment);


        $shipService = new ShipService\Request();
        $shipService->getSoapClient()->__setLocation(ShipService\Request::PRODUCTION_URL);
        $result = $shipService->getProcessShipmentReply($processShipmentRequest);

        //dd($shipService->getSoapClient()->__getLastRequest());

        $result = $result->toArray();


        $status = @$result['HighestSeverity'];
        $trk    = @$result['CompletedShipmentDetail']['MasterTrackingId']['TrackingNumber'];
        $label  = @$result['CompletedShipmentDetail']['CompletedPackageDetails'][0]['Label']['Parts'][0]['Image'];
        $notification  = '[' . $result['Notifications'][0]['Code'] . '] ' . $result['Notifications'][0]['Message'];
        $masterDetails = @$result['CompletedShipmentDetail']['MasterTrackingId'];

        $result = [
            'status'  => $status,
            'message' => $notification,
            'trk'     => $trk,
            'label'   => $label,
            'master'  => $masterDetails
        ];

        return $result;
    }

    /**
     * Get Pickup Points
     * @param null $webservice
     * @param array $ids
     * @return array
     */
    public function getPontosRecolha($paramsArr = [])
    {
    }

    /**
     * Get provider service
     *
     * @param $shipment
     */
    public function getProviderService($shipment)
    {

        $providerService = null;

        $source = config('app.source');

        $webserviceConfigs = WebserviceConfig::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceConfig::CACHE_TAG)
            ->where('source', $source)
            ->where('method', $shipment->webservice_method)
            ->where('provider_id', @$shipment->provider_id)
            ->first();

        try {

            $serviceKey = $shipment->recipient_country;
            if ($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if (!$providerService) {
                $mapping = config('shipments_export_mapping.fedex-services');
                $providerService = $mapping[$shipment->service->code];
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Fedex.');
        }

        return $providerService;
    }
}
