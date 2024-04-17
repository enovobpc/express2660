<?php

namespace App\Models\Webservice;

use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Carbon\Carbon;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use LynX39\LaraPdfMerger\PdfManage;
use Date, File, Setting;

class Ups extends \App\Models\Webservice\Base
{

    /**
     * @var string
     *
     * URL CRIAR API KEYS
     * https://www.ups.com/upsdeveloperkit?loc=pt_PT
     */
    private $url     = 'https://onlinetools.ups.com/';
    private $testUrl = 'https://wwwcie.ups.com/';


    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/ups/';

    /**
     * @var null
     */
    private $username;

    /**
     * @var null
     */
    private $password;

    /**
     * @var null
     */
    private $accessKey;

    /**
     * @var null
     */
    private $accountId;

    /**
     * @var null
     */
    private $debug;

    /**
     * Gls constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = false)
    {
        if (config('app.env') == 'local') { //asfaltolargo
            $this->username      = 'PT-513868836';
            $this->password      = 'Gu123163';
            $this->licenseNumber = '7D86EAEE7E91D732';
            $this->accountId     = '7276RA';
        } else {
            $this->username      = $user;
            $this->password      = $password;
            $this->licenseNumber = $sessionId;
            $this->accountId     = $agencia;
        }

        $this->debug = $debug;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($trakingCode)
    {
        $trakingCode = explode(',', $trakingCode);
        $trakingCode = $trakingCode[0];

        try {
            $xml = '<?xml version="1.0"?>
                <AccessRequest xml:lang="en-US">
                    <AccessLicenseNumber>' . $this->licenseNumber . '</AccessLicenseNumber>
                    <UserId>' . $this->username . '</UserId>
                    <Password>' . $this->password . '</Password>
                </AccessRequest>
                <?xml version="1.0"?>
                <TrackRequest xml:lang="en-US">
                    <Request>
                       <!-- <TransactionReference>
                            <CustomerContext></CustomerContext>
                            <XpciVersion>1.0</XpciVersion>
                        </TransactionReference>-->
                        <RequestAction>Track</RequestAction>
                        <RequestOption>1</RequestOption>
                    </Request>
                    <TrackingNumber>' . $trakingCode . '</TrackingNumber>
                </TrackRequest>';

            $response = $this->execute($this->getUrl('ups.app/xml/Track'), $xml);

            if ($response == false) {
                throw new \Exception("Bad data.");
            } else {
                $response = xmlstr_to_array($response);

                if (@$response['Response']['Error']) {
                    $code = @$response['Response']['Error']['ErrorCode'];
                    $message = @$response['Response']['Error']['ErrorDescription'];
                    throw new \Exception($code . ' - ' . $message);
                } else {

                    $history = @$response['Shipment']['Package']['Activity'];
                    $weight  = @$response['Shipment']['Package']['PackageWeight']['Weight'];

                    if (empty($history)) {
                        throw new \Exception('Sem informação de estados.');
                    }

                    $mappedStatus = $this->mappingResult($history, 'status');

                    return [
                        'history' => $mappedStatus,
                        'weight' => (float)$weight
                    ];
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * Devolve a imagem do POD
     *
     * @param $codAgeCargo
     * @param $codAgeOri
     * @param $trakingCode
     * @return string
     * @throws \Exception
     */
    public function getPod($codAgeCargo, $codAgeOri, $trakingCode)
    {
        return false;
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
        return false;
    }

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
        return false;
    }


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {
        return false;
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
        return false;
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
        $data['service'] = str_pad($data['service'], 3, '0', STR_PAD_LEFT);
        $packType = '01'; //01=package - 03 = palete
        $startHour = str_replace(':', '', $data['start_hour']);
        $startHour = empty($startHour) ? '1000' : $startHour;
        $endHour   = str_replace(':', '', $data['end_hour']);
        $endHour   = empty($endHour) ? '1900' : $endHour;

        $requestXML = '<envr:Envelope xmlns:envr="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">
               <envr:Header>
                  <upss:UPSSecurity>
                     <upss:UsernameToken>
                        <upss:Username>' . $this->username . '</upss:Username>
                        <upss:Password>' . $this->password . '</upss:Password>
                     </upss:UsernameToken>
                     <upss:ServiceAccessToken>
                        <upss:AccessLicenseNumber>' . $this->licenseNumber . '</upss:AccessLicenseNumber>
                     </upss:ServiceAccessToken>
                  </upss:UPSSecurity>
               </envr:Header>
               <envr:Body>
                  <PickupCreationRequest xmlns="http://www.ups.com/XMLSchema/XOLTWS/Pickup/v1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                     <common:Request>
                        <common:RequestOption/>
                        <common:TransactionReference>
                           <common:CustomerContext>TRK' . $data['tracking_code'] . '</common:CustomerContext>
                        </common:TransactionReference>
                     </common:Request>
                     <RatePickupIndicator>N</RatePickupIndicator>
                     <!-- CARTAO PAGAMENTO RECOLHA -->
                     <!--<Shipper>
                        <ChargeCard>
                           <CardHolderName>Test user</CardHolderName>
                           <CardType>06</CardType>
                           <CardNumber>4023602222222125</CardNumber>
                           <ExpirationDate>201808</ExpirationDate>
                           <SecurityCode>737</SecurityCode>
                           <CardAddress>
                              <AddressLine>2311 York Rd</AddressLine>
                              <City>Rome</City>
                              <PostalCode>21093</PostalCode>
                              <CountryCode>IT</CountryCode>
                           </CardAddress>
                        </ChargeCard>
                     </Shipper>-->
                     <PickupDateInfo>
                        <CloseTime>' . $endHour . '</CloseTime>
                        <ReadyTime>' . $startHour . '</ReadyTime>
                        <PickupDate>' . str_replace('-', '', $data['date']) . '</PickupDate>
                     </PickupDateInfo>
                     <PickupAddress>
                        <CompanyName>' . substr($data['sender_name'], 0, 35) . '</CompanyName>
                        <ContactName>' . $data['sender_attn'] . '</ContactName>
                        <AddressLine>' . substr($data['sender_address'], 0, 34) . '</AddressLine>
                        <AddressLine2>' . substr($data['sender_address'], 36, 60) . '</AddressLine2>
                        <AddressLine3>' . substr($data['sender_address'], 61, 90) . '</AddressLine3>
                        <City>' . $data['sender_city'] . '</City>
                        <PostalCode>' . $data['sender_zip_code'] . '</PostalCode>
                        <CountryCode>' . strtoupper($data['sender_country']) . '</CountryCode>
                        <ResidentialIndicator>Y</ResidentialIndicator>
                        <Phone>
                           <Number>' . $data['recipient_phone'] . '</Number>
                           <Extension/>
                        </Phone>
                     </PickupAddress>
                     <AlternateAddressIndicator>Y</AlternateAddressIndicator>
                     <PickupPiece>
                        <ServiceCode>' . $data['service'] . '</ServiceCode>
                        <Quantity>' . $data['volumes'] . '</Quantity>
                        <DestinationCountryCode>' . strtoupper($data['recipient_country']) . '</DestinationCountryCode>
                        <ContainerCode>' . $packType . '</ContainerCode>
                     </PickupPiece>
                     <TotalWeight>
                        <Weight>' . $data['weight'] . '</Weight>
                        <UnitOfMeasurement>KGS</UnitOfMeasurement>
                     </TotalWeight>
                     <!--<TrackingData>
                        <TrackingNumber>Y</TrackingNumber>
                     </TrackingData>-->
                     <OverweightIndicator>N</OverweightIndicator>
                     <PaymentMethod>00</PaymentMethod>
                     <ReferenceNumber>ENOVO_TRK' . $data['tracking_code'] . '</ReferenceNumber>
                     <SpecialInstruction>' . substr($data['obs'], 0, 57) . '</SpecialInstruction>
                  </PickupCreationRequest>
               </envr:Body>
            </envr:Envelope>';

        try {

            //dd($requestXML);

            $response = $this->execute($this->getUrl('webservices/Pickup'), $requestXML);

            if ($this->debug) {
                if (!File::exists(public_path() . '/dumper/')) {
                    File::makeDirectory(public_path() . '/dumper/');
                }

                $responseXml = print_r($response, true);
                file_put_contents(public_path() . '/dumper/request.txt', $responseXml);
                file_put_contents(public_path() . '/dumper/response.txt', $responseXml);
            }

            if ($response == false) {
                throw new \Exception("Bad data.");
            } else {
                $response = xmlstr_to_array($response);

                $response = @$response['soapenv:Body'];
                if (@$response['soapenv:Fault']) {
                    $errorCode = @$response['soapenv:Fault']['detail']['err:Errors']['err:ErrorDetail']['err:PrimaryErrorCode']['err:Code'];
                    $errorMsg  = @$response['soapenv:Fault']['detail']['err:Errors']['err:ErrorDetail']['err:PrimaryErrorCode']['err:Description'];
                    throw new \Exception($errorCode . ' - ' . $errorMsg);
                } else {
                    $trk = @$response['pkup:PickupCreationResponse']['pkup:PRN'];
                    return $trk;
                }
            }
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data, $packages)
    {
        $requestXML = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
            <soap:Header>
                <UPSSecurity xmlns="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">
                    <UsernameToken>
                        <Username>' . $this->username . '</Username>
                        <Password>' . $this->password . '</Password>
                    </UsernameToken>
                    <ServiceAccessToken>
                        <AccessLicenseNumber>' . $this->licenseNumber . '</AccessLicenseNumber>
                    </ServiceAccessToken>
                </UPSSecurity>
            </soap:Header>
            <soap:Body>
                <ShipmentRequest xmlns="http://www.ups.com/XMLSchema/XOLTWS/Ship/v1.0">
                    <Request xmlns="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0">
                        <RequestOption>nonvalidate</RequestOption><!-- nao validar morada -->
                        <SubVersion>2007</SubVersion>
                    </Request>
                    <Shipment>
                        <Description>N/A</Description>
                        <Shipper>
                            <Name>' . substr($data['sender_name'], 0, 35) . '</Name>
                            <AttentionName>' . $data['sender_attn'] . '</AttentionName>
                            <TaxIdentificationNumber />
                            <EMailAddress></EMailAddress>
                            <Phone>
                                <Number>' . $data['sender_phone'] . '</Number>
                                <Extension />
                            </Phone>
                            <ShipperNumber>' . $this->accountId . '</ShipperNumber>
                            <FaxNumber />
                            <Address>
                                <AddressLine>' . substr($data['sender_address'], 0, 34) . '</AddressLine>
                                <AddressLine2>' . substr($data['sender_address'], 36, 60) . '</AddressLine2>
                                <AddressLine3>' . substr($data['sender_address'], 61, 90) . '</AddressLine3>
                                <City>' . $data['sender_city'] . '</City>
                                <PostalCode>' . $data['sender_zip_code'] . '</PostalCode>
                                <CountryCode>' . strtoupper($data['sender_country']) . '</CountryCode>
                            </Address>
                        </Shipper>
                        <ShipTo>
                            <Name>' . substr($data['recipient_name'], 0, 35) . '</Name>
                            <AttentionName>' . $data['recipient_attn'] . '</AttentionName>
                            <Phone>
                                <Number>' . $data['recipient_phone'] . '</Number>
                                <Extension />
                            </Phone>
                            <EMailAddress></EMailAddress>
                            <Address>
                                <AddressLine>' . substr($data['recipient_address'], 0, 34) . '</AddressLine>
                                <AddressLine2>' . substr($data['recipient_address'], 36, 60) . '</AddressLine2>
                                <AddressLine3>' . substr($data['recipient_address'], 61, 90) . '</AddressLine3>
                                <City>' . $data['recipient_city'] . '</City>
                                <PostalCode>' . $data['recipient_zip_code'] . '</PostalCode>
                                <CountryCode>' . strtoupper($data['recipient_country']) . '</CountryCode>
                            </Address>
                        </ShipTo>
                        <PaymentInformation>
                            <ShipmentCharge>
                                <Type>01</Type>
                                <BillShipper>
                                    <AccountNumber>' . $this->accountId . '</AccountNumber>
                                </BillShipper>
                            </ShipmentCharge>
                        </PaymentInformation>
                        <ReferenceNumber>
                            <Value>TRK' . $data['tracking_code'] . '</Value>
                        </ReferenceNumber>
                        <Service>
                            <Code>' . $data['service'] . '</Code>
                        </Service>
                        <ShipmentServiceOptions>
                            <!--<Notification>
                                <NotificationCode>6</NotificationCode>
                                <EMail>

                                <EMailAddress>qrb7snd@ups.com</EMailAddress>
                                &lt;!&ndash;Optional:&ndash;&gt;
                                <UndeliverableEMailAddress></UndeliverableEMailAddress>
                                &lt;!&ndash;Optional:&ndash;&gt;
                                <FromEMailAddress></FromEMailAddress>
                                &lt;!&ndash;Optional:&ndash;&gt;
                                <FromName>FROM Name</FromName>
                                &lt;!&ndash;Optional:&ndash;&gt;
                                <Memo>Memo</Memo>
                                &lt;!&ndash;Optional:&ndash;&gt;
                                <Subject>memo</Subject>
                                &lt;!&ndash;Optional:&ndash;&gt;
                                </EMail>
                            </Notification>-->
                        </ShipmentServiceOptions>';

        foreach ($packages as $package) {
            $requestXML .= '<Package>
                            <Description />
                            <Packaging>
                                <Code>02</Code>
                                <Description></Description>
                            </Packaging>
                            <PackageWeight>
                                <UnitOfMeasurement>
                                    <Code>KGS</Code>
                                    <Description>Kilo</Description>
                                </UnitOfMeasurement>
                                <Weight>' . $package['weight'] . '</Weight>
                            </PackageWeight>
                        </Package>';
        }

        $requestXML .= '</Shipment>
                    <LabelSpecification>
                        <LabelStockSize>
                            <Height>8</Height>
                            <Width>4</Width>
                        </LabelStockSize>
                        <LabelImageFormat>
                            <Code>PNG</Code>
                        </LabelImageFormat>
                    </LabelSpecification>
                </ShipmentRequest>
            </soap:Body>
        </soap:Envelope>';

        try {
            $response = $this->execute($this->getUrl('webservices/Ship'), $requestXML);

            if ($this->debug) {
                if (!File::exists(public_path() . '/dumper/')) {
                    File::makeDirectory(public_path() . '/dumper/');
                }

                $responseXml = print_r($response, true);
                file_put_contents(public_path() . '/dumper/request.txt', $requestXML);
                file_put_contents(public_path() . '/dumper/response.txt', $response);
            }

            if ($response == false) {
                throw new \Exception("Bad data.");
            } else {
                $response = xmlstr_to_array($response);

                $response = @$response['soapenv:Body'];
                if (@$response['soapenv:Fault']) {
                    $errorCode = @$response['soapenv:Fault']['detail']['err:Errors']['err:ErrorDetail']['err:PrimaryErrorCode']['err:Code'];
                    $errorMsg  = @$response['soapenv:Fault']['detail']['err:Errors']['err:ErrorDetail']['err:PrimaryErrorCode']['err:Description'];
                    throw new \Exception($errorCode . ' - ' . $errorMsg);
                } else {

                    $response  = $response['ship:ShipmentResponse']['ship:ShipmentResults'];
                    $packages  = $response['ship:PackageResults'];
                    $masterTrk = $response['ship:ShipmentIdentificationNumber'];
                    //$costPrice = $response['ship:ShipmentCharges']['ship:TotalCharges']['ship:MonetaryValue'];

                    $labels = [];
                    $parcelsTrk = [];

                    if ($data['volumes'] == 1) {
                        $packages = [$packages];
                    }

                    foreach ($packages as $package) {
                        $parcelsTrk[] = $package['ship:TrackingNumber'];
                        $labels[]     = $package['ship:ShippingLabel']['ship:GraphicImage'];
                    }

                    $folder = public_path() . $this->upload_directory;
                    if (!File::exists($folder)) {
                        File::makeDirectory($folder);
                    }

                    $labelsPdf = [];
                    foreach ($labels as $key => $label) {
                        $image = new \Imagick();
                        $image->readimageblob(base64_decode($label));
                        $image->rotateImage('#ffffff', 90);
                        $image->setImageFormat('pdf');

                        $filepath = public_path() . $this->upload_directory . $masterTrk . '_' . $key . '.pdf';
                        File::put($filepath, $image->getImageBlob());
                        $labelsPdf[] = $filepath;
                    }

                    $pdf = new PdfManage();
                    foreach ($labelsPdf as $labelPdf) {
                        $pdf->addPDF($labelPdf);
                    }

                    $outputFilepath = public_path() . $this->upload_directory . $masterTrk . '_labels.pdf';
                    $fileContent = $pdf->merge('string');
                    File::put($outputFilepath, $fileContent);

                    foreach ($labelsPdf as $labelPdf) {
                        File::delete($labelPdf);
                    }

                    $trk = implode(',', $parcelsTrk);
                    return $trk;
                }
            }
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiqueta($agency = null, $trackingCode = null)
    {
        $trackingCode = explode(',', $trackingCode);
        $trackingCode = $trackingCode[0];

        $file = File::get(public_path() . $this->upload_directory . $trackingCode . '_labels.pdf');
        $file = base64_encode($file);
        return $file;
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
     * Permite eliminar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function destroyShipment($shipment)
    {
        $trackingCode = explode(',', $shipment->provider_tracking_code);
        $trackingCode = $trackingCode[0];

        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
            <AccessRequest xml:lang="en-US">
                <AccessLicenseNumber>' . $this->licenseNumber . '</AccessLicenseNumber>
                <UserId>' . $this->username . '</UserId>
                <Password>' . $this->password . '</Password>
            </AccessRequest>
            <?xml version="1.0" encoding="UTF-8"?>
            <VoidShipmentRequest>
                <Request>
                    <TransactionReference>
                        <!--<CustomerContext>Bench Generated Void of</CustomerContext>
                        <XpciVersion>1.0</XpciVersion>-->
                    </TransactionReference>
                    <RequestAction>1</RequestAction>
                    <RequestOption>1</RequestOption>
                </Request>
                <ShipmentIdentificationNumber>' . $trackingCode . '</ShipmentIdentificationNumber>
            </VoidShipmentRequest>';

        $response = $this->execute($this->getUrl('ups.app/xml/Void'), $xmlRequest);

        if ($response == false) {
            throw new \Exception("Bad data.");
        } else {
            $response = xmlstr_to_array($response);

            $response = @$response['Response'];

            if (@$response['Error']) {
                $errorCode = @$response['Error']['ErrorCode'];
                $errorMsg  = @$response['Error']['ErrorDescription'];
                throw new \Exception($errorCode . ' - ' . $errorMsg);
            }
        }

        return true;
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * @param $url
     * @param (array) $data
     * @return mixed
     */
    private function execute($url, $xml)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {

        $data = self::getEstadoEnvioByTrk($shipment->provider_tracking_code);

        $webserviceWeight  = null;
        $receiverSignature = null;

        if ($data) {

            if (isset($data['weight'])) {
                $webserviceWeight  = $data['weight'];
            }

            $data = @$data['history'];

            aasort($data, 'created_at');
            foreach ($data as $key => $item) {

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'incidence_id' => @$item['incidence_id'],
                    'created_at'   => @$item['created_at'],
                    'status_id'    => @$item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            /**
             * Update shipment weight
             */
            if ($webserviceWeight > $shipment->weight) {
                $shipment->weight   = $webserviceWeight;

                $tmpShipment = $shipment;
                $prices = Shipment::calcPrices($tmpShipment);

                $shipment->volumetric_weight  = @$prices['volumetricWeight'];
                $shipment->total_price        = @$prices['total'];
                $shipment->fuel_tax           = @$prices['fuelTax'];
                $shipment->extra_weight       = @$prices['extraKg'];
                $shipment->cost_price         = @$prices['cost'];
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at;
            $shipment->save();
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

        $data = $shipment->toArray();

        $data['sender_attn']    = $data['sender_attn'] ? $data['sender_attn'] : $data['sender_name'];
        $data['recipient_attn'] = $data['recipient_attn'] ? $data['recipient_attn'] : $data['recipient_name'];

        $data['service'] = $this->getProviderService($shipment);

        if (!@$shipment->pack_dimensions->isEmpty()) {
            $packages = $shipment->pack_dimensions->toArray();
        } else {

            $avgWeight = $shipment->weight / $shipment->volumes;

            $packages = [];
            for ($i = 0; $i < $shipment->volumes; $i++) {
                $packages[] = [
                    'weight' => $avgWeight
                ];
            }
        }

        if ($isCollection) {
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data, $packages);
        }
    }

    /**
     * Map array of results
     *
     * @param type $data Array of data
     * @param type $mappingArray
     * @return type
     */
    private function mappingResult($data, $mappingArray)
    {

        $arr = [];

        foreach ($data as $row) {

            //mapping and process status
            if ($mappingArray == 'status' || $mappingArray == 'collection-status') {

                $hour = @$row['Time'];
                $hour = str_split($hour, 2);
                $hour = implode(':', $hour);

                $date = @$row['Date'];
                $date = new Date($date);
                $date = $date->format('Y-m-d');
                $datetime = $date . ' ' . $hour;

                $row = [
                    'status'     => @$row['Status']['StatusCode']['Code'],
                    'city'       => @$row['ActivityLocation']['Address']['CountryCode'] . (@$row['ActivityLocation']['Address']['City'] ? ', ' . @$row['ActivityLocation']['Address']['City'] : ''),
                    'receiver'   => @$row['ActivityLocation']['SignedForByName'],
                    'signature'  => @$row['ActivityLocation']['SignatureImage']['GraphicImage'] ? 'data:image/gif;base64,' . @$row['ActivityLocation']['SignatureImage']['GraphicImage'] : null,
                    'created_at' => $datetime,
                ];

                $statusCode  = trim(@$row['status']);

                $status = config('shipments_import_mapping.ups-status');
                $row['status_id'] = @$status[$statusCode];
                $row['obs']       = trim(@$row['city']);


                if (isset($row)) {
                    $arr[] = $row;
                }
            } else {
                $arr = $row;
            }
        }

        return $arr;
    }

    /**
     * Return base url
     * @return string
     */
    public function getUrl($method)
    {

        if (config('app.env') == 'local') {
            return $this->testUrl . $method;
        }

        return $this->url . $method;
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
                $mapping = config('shipments_export_mapping.ups-services');
                $providerService = $mapping[$shipment->service->code];
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço UPS.');
        }

        return $providerService;
    }

    /**
     * Retorna tempos transito
     */
    public function calcTransitTime($params = null) {


        $params = '{
              "RateRequest": {
                "Request": {
                  "SubVersion": "1703",
                  "TransactionReference": {
                    "CustomerContext": " "
                  }
                },
                "Shipment": {
                    "DeliveryTimeInformation":{
                        "PackageBillType": "03",
                        "Pickup":{
                            "Date": "'.str_replace(' ', '', $params['pickup_date']).'"
                        }
                    },
                  "ShipmentRatingOptions": {
                    "UserLevelDiscountIndicator": "TRUE"
                  },
                  "Shipper": {
                    "Name": "Billy Blanks",
                    "ShipperNumber": " ",
                    "Address": {
                      "AddressLine": "ADDRESS 1",
                      "City": "CITY ",
                      "StateProvinceCode": "",
                      "PostalCode": "'.$params['sender_zip_code'].'",
                      "CountryCode": "'.$params['sender_country'].'"
                    }
                  },
                  "ShipTo": {
                    "Name": "Sarita Lynn",
                    "Address": {
                      "AddressLine": "ADDRESS 1",
                      "City": "XPTO",
                      "StateProvinceCode": "",
                      "PostalCode": "'.$params['recipient_zip_code'].'",
                      "CountryCode": "'.$params['recipient_country'].'"
                    }
                  },
                  "ShipFrom": {
                    "Name": "Billy Blanks",
                    "Address": {
                      "AddressLine": "ADDRESS 1",
                      "City": "XPTO",
                      "StateProvinceCode": "",
                      "PostalCode": "'.$params['sender_zip_code'].'",
                      "CountryCode": "'.$params['sender_country'].'"
                    }
                  },
                  "Service": {
                    "Code": "'.($params['service'] ? $params['service'] : '65').'",
                    "Description": "Ground"
                  },
                  "ShipmentTotalWeight": {
                    "UnitOfMeasurement": {
                      "Code": "LBS",
                      "Description": "Pounds"
                    },
                    "Weight": "10"
                  },
                  "Package": {
                    "PackagingType": {
                      "Code": "02",
                      "Description": "Package"
                    },
                    "Dimensions": {
                      "UnitOfMeasurement": {
                        "Code": "CM"
                      },
                      "Length": "10",
                      "Width": "7",
                      "Height": "5"
                    },
                    "PackageWeight": {
                      "UnitOfMeasurement": {
                        "Code": "KGS"
                      },
                      "Weight": "7"
                    }
                  }
                }
              }
            }';

        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://onlinetools.ups.com/ship/v1/rating/Rate?additionalinfo=timeintransit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => array(
                'AccessLicenseNumber: '.$this->licenseNumber,
                'Password: '. $this->password,
                'Content-Type:  application/json',
                'transId:  Tran123',
                'transactionSrc:  XOLT',
                'Username: '.$this->username,
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        if(@$response['response']['errors']) {
            throw new \Exception($response['response']['errors'][0]['message']);
        }

        $date = @$response['RateResponse']['RatedShipment']['TimeInTransit']['ServiceSummary']['EstimatedArrival']['Arrival']['Date'];
        $date = substr($date, 0, 4). '-' .substr($date, 4, 2).'-'.substr($date, 6, 2);

        return [
            'delivery_date' => @$date,
            'transit_days'  => 0
        ];

    }
}
