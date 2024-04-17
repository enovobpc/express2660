<?php

namespace App\Models\InvoiceGateway\KeyInvoice;

use App\Models\Invoice;
use Date, Response, File, Mail, Setting;
use Mockery\Exception;

class Document extends \App\Models\InvoiceGateway\KeyInvoice\Base
{


    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var null
     */
    public $session_id = null;

    /**
     * storage path
     */
    const STORAGE_PATH = 'invoices/';

    /**
     * Get SAFT file
     * @param $year
     * @param null $month
     * @return mixed
     * @throws \Exception
     */
    public function getSaftFile($year, $month = null)
    {
        $data = [
            'sid'   => $this->session_id,
            'year'  => $year,
            'month' => $month
        ];

        return $this->execute('getsaftfile', $data);
    }

    /**
     * Lista as taxas de IVA
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTaxes()
    {
        $data = [
            'sid'     => $this->session_id
        ];

        return $this->execute('getTaxes', $data);
    }

    /**
     * Lista todas as séries activas para o tipo de documento indicado.
     *
     * @param $idDoc identificador do documento
     * @param $docType tipo de documento. Ver tabela de Tipos de documentos possíveis.
     * @return mixed
     */
    public function getActiveSeries($docType, $apiKey = null)
    {

        $sessionId = $this->session_id;

        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $sessionId,
            'docType' => $docType
        ];

        return $this->execute('listdocumentseries', $data);
    }

    /**
     * List all series for a given doc type
     *
     * @param $idDoc ID da fatura
     * @param $docSerie ID da série da fatura
     * @return mixed
     */
    public function listAllSeries($docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'docType'   => $docType,
        ];

        $list = $this->execute('listAllDocumentSeries', $data);

        return $list;
    }

    /**
     * Verifica se um documento já existe. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $idDoc identificador do documento
     * @param $docType tipo de documento. Ver tabela de Tipos de documentos possíveis.
     * @return mixed
     */
    public function documentExists($idDoc, $docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'idDoc'   => $idDoc,
            'docType' => $docType
        ];

        return $this->execute('documentExists', $data);
    }

    /**
     * Conta o número total de documentos do tipo indicado gravados na base de dados.
     *
     * @param $docType
     * @return mixed
     */
    public function countDocuments($docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'docType' => $docType
        ];

        return $this->execute('countDocuments', $data);
    }

    /**
     * Devolve a lista de documentos. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $docType
     * @param int $offset
     * @param string $orderBy
     * @param string $sortBy [asc|desc]
     * @return mixed
     */
    public function getDocumentslist($docType, $offset = 0, $orderBy = 'date', $sortBy = 'desc')
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'docType' => $docType,
            'offset'  => $offset,
            'order_by' => $orderBy,
            'sort_by' => $sortBy
        ];

        //return $this->execute('documentsList', $data);
        return $this->execute('documentsList_custom', $data);
    }

    /**
     * Obtém uma lista de documentos filtrados pelo NIF de um cliente específico.
     *
     * @param $docType
     * @param $nif
     * @param int $offset
     * @return mixed
     */
    public function getDocumentslistByCustomer($docType, $nif, $offset = 0,  $orderBy = 'date', $sortBy = 'desc')
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'docType' => $docType,
            'nif'     => $nif,
            'offset'  => $offset,
            'order_by' => $orderBy,
            'sort_by' => $sortBy
        ];

        return $this->execute('documentsList_byClient_custom', $data);
    }

    /**
     * Obtém os dados de um documento. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $idDoc
     * @param $docType
     * @return mixed
     */
    public function getDocument($idDoc, $docType, $docSeries)
    {
        $originalDocType = $docType;
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDoc'     => $idDoc,
            'docType'   => $docType,
            'docSeries' => $docSeries
        ];
    
        //$data1 = $this->execute('getDocument_bySeries', $data, true); //metodo devolve a mais o nif e as observacoes
        $data2  = $this->execute('getDocumentDetails_bySeries', $data, true);
        $lines1 = $this->execute('getDocumentLines_bySeries', $data);
        $lines2 = $this->execute('getDocumentLinesDetails_bySeries', $data);

        $lines = [];
        $documentSubtotal = 0;
        $lineNum = 0;
        if(!empty($lines2)) {
            
            foreach($lines2 as $key => $line) {
                $lineSubtotal = $line->ValorMercadoria;
                $documentSubtotal+= $lineSubtotal;

                $discountPercent = null;
                if($line->ValorDesconto) {
                    $subtotalSemDesconto = $line->PrecoUnitario * $line->Qty;
                    $discountPercent = ($line->ValorDesconto * 100) / $subtotalSemDesconto;
                }

                $vatRateValue = @$lines1[$key]->TAX;
                $exemptionReason = null;
                if(empty($vatRateValue)) {
                    $exemptionReason = 'M05';
                }

                $lineNum++;
                $lines[] = [
                    'line'          => $lineNum,
                    'reference'     => $line->CodigoArtigo,
                    'description'   => utf8_decode($line->ProductName),
                    'qty'           => $line->Qty,
                    'total_price'   => $line->PrecoUnitario,
                    'subtotal'      => $line->ValorMercadoria,
                    'tax_rate'      => $vatRateValue,
                    'total'         => $line->ValorTotal,
                    'discount'      => $discountPercent,
                    'exemption_reason'      => $exemptionReason,
                    'exemption_reason_code' => ''
                ];
            
            }
        }

        if ($data2) {
            $response = [
                'doc_id'        => $data2->IdDoc,
                'doc_series_id' => $data2->DocSeries,
                'doc_type'      => $originalDocType,
                //'vat'           => $data1->NIF,
                'billing_code'  => $data2->CodigoCliente,
                'billing_name'  => $data2->ClientName,
                'doc_date'      => $data2->Date,
                'doc_subtotal'  => $data2->ValorSemIva,
                'doc_vat'       => $data2->Total - $data2->ValorSemIva,
                'doc_total'     => $data2->Total,
               // 'obs'           => $data1->Comments,
                'lines'         => $lines
            ];
        
            return $response;
        }

        return false;
    }

    /**
     * Obtém os dados de um documento. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $idDoc
     * @param $docType
     * @return mixed
     */
    public function getDocumentAdicionalInfo($idDoc, $docType, $fieldname = null)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'idDoc'   => $idDoc,
            'docType' => $docType,
            'fieldname' => $fieldname
        ];

        return $this->execute('getDocumentInfo', $data, true);
    }

    /**
     * Devolve os dados das linhas de um documento. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $idDoc
     * @param $docType
     * @return mixed
     */
    public function getDocumentLines($idDoc, $docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'idDoc'   => $idDoc,
            'docType' => $docType,
        ];

        return $this->execute('getDocumentLines', $data, true);
    }

    /**
     * Devolve o conteúdo do ficheiro da fatura em base 64.
     *
     * @param $idDoc
     * @param $docType
     * @param bool $acceptStorage [true = get from storage, false = get from API]
     * @return mixed
     */
    public function getDocumentPdf($idDoc, $docType, $docSeries = null, $acceptStorage = true)
    {
        $returnDocSigned = false; //retorna documento assinado
        $storeFiles      = Setting::get('billing_store_invoices');

        $method = $returnDocSigned ? 'getDocumentPDFSigned' : 'getDocumentPDFLink';

        $docTypeId = $docType;
        $docType   = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDoc'     => $idDoc,
            'docType'   => $docType,
            'docSeries' => $docSeries
        ];

        if ($docSeries) {
            //$acceptStorage = false; //quando se obtem a partir da lista de conta corrente
            //getDocumentPDFSigned_bySeries
            $method = $returnDocSigned ? 'getDocumentPDFSigned' : 'getdocumentpdflink_byseries';
            $data['docSeries'] = $docSeries;
        }

        //check if file exists on repository
        $filename   = 'doc_' . $docTypeId . '_' . $idDoc . '_' . $this->apiKey . '.txt';

        $folderpath = storage_path(self::STORAGE_PATH);
        $filepath   = storage_path(self::STORAGE_PATH . $filename);

        if ($acceptStorage && $storeFiles && File::exists($filepath)) { //get file from storage
            $result = file_get_contents($filepath);
            return $result;
        } else {
            //download file and store in storage
            $result = $this->execute($method, $data, true);

            if ($docSeries) {
                if(@$this->externalEndoint) {
                    $result = base64_encode(file_get_contents($this->externalUrl.'?download='.$result));
                } else {
                    if($method = 'getdocumentpdflink_byseries') {
                        $result = base64_encode(file_get_contents($result));
                    }
                }
            }

            if ($storeFiles) {
                if (!File::exists($folderpath)) {
                    File::makeDirectory($folderpath);
                }

                File::put($filepath, $result);
            }

            return $result;
        }
    }

    /**
     * Cria um novo documento, em modo rascunho. Se o campo «docType» contiver um valor diferente dos aceites, será
     * considerado 13:encomenda. Os campos servem para associar os dados de um cliente a um documento sem que este
     * exista na base de dados, e só são tidos em conta se o campo nif estiver vazio ('') e se o campo docType contiver
     * 5 (venda a dinheiro)
     *
     * @param null $nif vazio caso o cliente não exista no sistema
     * @param $docType
     * @param null $obs
     * @param null $name caso pretenda a criação do cliente de forma automática
     * @param null $opNif caso pretenda a criação do cliente de forma automática
     * @param null $address caso pretenda a criação do cliente de forma automática
     * @param null $zipCode caso pretenda a criação do cliente de forma automática
     * @param null $citycaso pretenda a criação do cliente de forma automática
     * @param null $docRef
     * @return mixed Código do Documento Temporário
     */
    public function createDraft($type, $data)
    {

        //memoriza a última data lançada
        $folder   = storage_path() . '/keyinvoice-logs/';
        $filename = $folder . 'session.json';

        $content = json_decode(File::get($filename), true);

        if (!empty(@$content[$this->apiKey])) {
            $content[$this->apiKey]['last_docdate'] = @$data['docdate'];
        }

        File::put($filename, json_encode($content));


        $duedate = @$data['duedate'];
        $docdate = @$data['docdate'];
        $paymentMethod = @$data['paymentMethod'];
        $taxRetention  = @$data['taxRetention'];
        $totalDiscount = @$data['totalDiscount'];
        $printComment  = @$data['printComment'];
        $transportDetails = @$data['transport'];

        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $type);

        if ($data['nif'] == '999999999' || $data['nif'] == '999999990') {
            $data['nif'] = '';
        }

        $data = [
            'sid'            => $this->session_id,
            'nif'            => $data['nif'],
            'docType'        => $docType,
            'obs'            => utf8_encode(@$data['obs']),
            'opt_name'       => utf8_encode(str_replace("'", "’", @$data['name'])),
            'opt_nif'        => $data['nif'],
            'opt_address'    => utf8_encode(str_replace("'", "’", @$data['address'])),
            'opt_locality'   => utf8_encode(str_replace("'", "’", @$data['city'])),
            'opt_postalCode' => @$data['zip_code'],
            'docRef'         => @$data['docref']
        ];

        $draftId = $this->execute('insertDocumentHeader', $data);


        if (!empty($docdate)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'docdate', $docdate);
        }

        if (!empty($docdate)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'docdate', $docdate);
        }

        if (!empty($duedate)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'duedate', $duedate);
        }

        if (!empty($printComment)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'printComment', utf8_encode($printComment));
        }

        if (!empty($totalDiscount)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'discount', $totalDiscount);
        }

        if (!empty($taxRetention)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'taxRetention', $taxRetention);
        }

        if (!empty($transportDetails)) {
            $serieId = null;
            $this->insertTransportDetails($draftId, $docType, $serieId, $transportDetails);
        }

        if (!empty($paymentMethod)) {

            if ($paymentMethod == 'tb') {
                $paymentMethodCode = 3; //transferencia bancaria
            } elseif ($paymentMethod == 'check') {
                $paymentMethodCode = 2; //cheque
            } elseif ($paymentMethod == 'mb') {
                $paymentMethodCode = 4; //multibanco
            } else {
                $paymentMethodCode = 1; //numerario
            }

            //insertDocumentPaymentMethod existe este metodo na api
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'paymentMethod', $paymentMethodCode);
        }

        return $draftId;
    }

    /**
     * Cria uma nova linha num documento rascunho.
     * Nos campos «qt», «price», «tax» e «prodDes», se forem passadas cadeias de caracteres vazias (''),
     * serão tidos em conta os valores que estão no artigo. Se o campo «docType» contiver um valor diferente dos
     * aceites, será considerado 13:encomenda.
     *
     * @param int $draftId ID do rascunho
     * @param string $docType Tipo de Documento
     * @param array $data array com os dados da linha [ref, qt, price, tax, prodDesc, discount]
     * @return mixed
     */
    public function insertDraftLine($draftId, $docType, $data)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $taxId = $data['tax_id'];
        
        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $draftId,
            'docType'   => $docType,
            'ref'       => @$data['ref'],
            'qt'        => @$data['qt'] ? @$data['qt'] : 1,
            'price'     => @$data['price'],
            'tax'       => @$data['tax'],
            'prodDesc'  => utf8_encode(str_replace("'", "’", @$data['prodDesc'])),
            'discount'  => @$data['discount'],
            'exemption' => @$data['exemption'],
            'obs'       => utf8_encode(@$data['obs'])
        ];

        $lineId =  $this->execute('insertDocumentLine', $data);

        if (@$data['obs'] || (($data['tax'] == '0' || $data['tax'] == '0.00') && !empty($data['exemption']))) {

            $taxId = $taxId ? $taxId : $data['tax']; //se tiver ID da taxa definido, usa o taxa ID, se nao usa o valor numerico do IVA como sendo ID da taxa (ou seja 23% => id 23)

            if (($data['tax'] == '0' || $data['tax'] == '0.00') && !empty($data['exemption'])) { //se taxa exenta, assume o ID da isencao
                $taxId = $data['exemption'];
            } elseif ($taxId == '6') {
                $taxId = '3'; //ID da taxa 6%
            } elseif ($taxId == '13') {
                $taxId = '2'; //ID da taxa 13%
            } elseif($taxId == '23') {
                $taxId = '1'; //ID da taxa 23%
            }
           
            if(!empty($taxId)) {
                $this->changeLine($draftId, $docType, $lineId, $data['qt'], $data['price'], $taxId, $data['discount'], $data['obs']);
                //$this->changeLineTax($draftId, $docType, $lineId, $data['exemption']); //antigo metodo só para mudar o IVA
            }
        }


        return $lineId;
    }

    /**
     * Insere informação específica no cabeçalho de um documento.
     *
     * @param $draftId
     * @param $docType
     * @param $fieldname O campo que se pretende preencher: 'obs', 'printComment', 'docRef', 'pickupDateTime', 'pickupLocation', 'deliveryDateTime', 'deliveryLocationTxt', 'licencePlate', 'opt_deliveryLocation_address', 'opt_deliveryLocation_postalCode', 'opt_deliveryLocation_city', 'opt_deliveryLocation_countryCode', 'docdate', 'duedate'
     * @param $value
     * @return mixed
     */
    public function insertDocumentHeaderAdicionalField($draftId, $docType, $fieldname, $value)
    {

        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $draftId,
            'docType'   => $docType,
            'fieldname' => $fieldname,
            'value'     => $value
        ];

        return $this->execute('setDocumentHeaderField', $data);
    }

    /**
     * Fecha o documento, tornando-o definitivo. Se o campo «docType» contiver um valor diferente dos aceites,
     * será considerado 13:encomenda.
     *
     * @param $idDocTemp
     * @param $docType
     * @return mixed
     */
    public function convertDraftToDoc($idDocTemp, $docType, $docSerie = null)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $idDocTemp,
            'docType'   => $docType,
        ];

        $response = $this->execute('closeDocument', $data, true);

        return $response;
    }

    /**
     * Devolve a lista de documentos em rascunho. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $docType
     * @param int $offset
     * @return mixed
     */
    public function getDraftsList($docType, $offset = 0)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'docType' => $docType,
            'offset'  => $offset
        ];

        return $this->execute('documentsDraftsList', $data);
    }

    /**
     * Elimina um documento ainda em rascunho.
     *
     * @param $idDocTemp
     * @param $docType
     * @return mixed
     */
    public function deleteDraft($idDocTemp, $docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $idDocTemp,
            'docType'   => $docType,
        ];

        if ($docType == 9) { //receipt
            return true;
        }

        return $this->execute('deleteDocumentDraft', $data, true);
    }

    /**
     * Anula/Estorna um documento emitido anteriormente.
     *
     * @param $docType
     * @param $docSeries
     * @param $idDoc
     * @param $docSeries
     * @return mixed
     */
    public function deleteDocument($docId, $docType, $data, $returnFullData = false)
    {

        if (@$data['doc_serie']) {
            $serieId = $data['doc_serie'];
        } else {
            //obtem a série ativa para o documento em questão
            $serie = $this->getCurrentSerie($docType); //obtem a serie em vigor do tipo de documento indicado
            $serieId = $serie['id'];
        }

        if (config('app.source') == 'ontimeservices') {
            $serieId = 62; //'T20.'
        }


        //delete receipt
        if ($docType == 'receipt') {
            return $this->deleteReceipt($docId, $serieId);
        }

        $docTypeId = $docType;
        $docType   = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'docType'   => $docType,
            'docSeries' => $serieId,
            'idDoc'     => $docId,
            'c_series'  => '', //$data['credit_serie'],
            'c_date'    => @$data['credit_date'],
            'c_reason'  => @$data['credit_reason'] ? utf8_encode(@$data['credit_reason']) : '',
        ];

        try {
            $result = $this->execute('setDocumentVoid', $data, true);

            //apaga ficheiro arquivado
            try {
                $filename = 'doc_' . $docTypeId . '_' . $docId . '_' . $this->apiKey . '.txt';
                $filepath = storage_path(self::STORAGE_PATH . $filename);
                File::delete($filepath);
            } catch (\Exception $e) {
            }

            return $result;
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage());
        }
    }

    public function deleteReceipt($docId, $serieId)
    {

        $data = [
            'sid'       => $this->session_id,
            'docSeries' => $serieId,
            'idDoc'     => $docId,
        ];

        try {
            return $this->execute('setReceiptVoid', $data, true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Altera os dados de uma linha do documento
     *
     * @param $draftId
     * @param $docType
     * @param $lineId
     * @param $qty
     * @param $price
     * @param $taxId
     * @param $discount
     * @param $obs
     * @return mixed
     * @throws \Exception
     */
    public function changeLine($draftId, $docType, $lineId, $qty, $price, $taxId, $discount, $obs)
    {

        $data = [
            'sid'           => $this->session_id,
            'idDocTemp'     => $draftId,
            'docType'       => $docType,
            'idDocLineTemp' => $lineId,
            'qt'            => $qty,
            'price'         => $price,
            'taxid'         => $taxId,
            'discount'      => $discount,
            'obs'           => $obs
        ];

        return $this->execute('changeDocumentLine', $data, true);
    }

    /**
     * Indica outra taxa de IVA, por ID, para a linha do documento indicada.
     * Pode ser útil para o caso em que indica uma taxa de IVA 0 mas é atribuída uma taxa 0 com o Motivo de Isenção errado.
     *
     * @param $draftId
     * @param $docType
     * @param $lineId
     * @param $taxId
     * @return mixed
     */
    public function changeLineTax($draftId, $docType, $lineId, $taxId)
    {

        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $draftId,
            'docType'   => $docType,
            'idDocLineTemp' => $lineId,
            'taxid'     => $taxId
        ];

        return $this->execute('changeDocumentLineTax', $data, true);
    }

    /**
     * Verifica se já existe um recibo para a factura indicada e caso exista devolve o ID do recibo criado.
     *
     * @param $docId
     * @param $docSeries
     * @return bool
     */
    public function checkIfPaid($docId, $docSeries)
    {

        $data = [
            'sid'       => $this->session_id,
            'docId'     => $docId,
            'docSeries' => $docSeries,
        ];


        try {
            $response = $this->execute('checkIfSettle', $data, true);
        } catch (\Exception $e) {
            $msg = 'DOC ID: ' . $docId . ' --> ' . $e->getMessage() . ' File ' . $e->getFile() . ' Line ' . $e->getLine();

            Mail::raw($msg, function ($message) {
                $message->to('paulo.costa@enovo.pt');
            });

            throw new \Exception($e->getMessage());
        }

        //return $response;

        if ($response == "1") {
            return false;
        }

        return true;
    }

    /**
     * Cria um novo documento, em modo rascunho. Se o campo «docType» contiver um valor diferente dos aceites, será
     * considerado 13:encomenda. Os campos servem para associar os dados de um cliente a um documento sem que este
     * exista na base de dados, e só são tidos em conta se o campo nif estiver vazio ('') e se o campo docType contiver
     * 5 (venda a dinheiro)
     *
     * @param null $nif vazio caso o cliente não exista no sistema
     * @param $docType
     * @param null $obs
     * @param null $name caso pretenda a criação do cliente de forma automática
     * @param null $opNif caso pretenda a criação do cliente de forma automática
     * @param null $address caso pretenda a criação do cliente de forma automática
     * @param null $zipCode caso pretenda a criação do cliente de forma automática
     * @param null $citycaso pretenda a criação do cliente de forma automática
     * @param null $docRef
     * @return mixed Código do Documento Temporário
     */
    public function createReceiptDraft($data, $docSerie = null)
    {
        $docdate = @$data['docdate'];
        $obs     = @$data['obs'];
        $docRef  = @$data['reference'];
        $paymentMethod = @$data['paymentMethod'];

        if (!$docSerie) {
            $docSerie = @$data['serie_id'];
        }

        if ($data['nif'] == '999999999' || $data['nif'] == '999999990') {
            $data['nif'] = '';
        }

        $data = [
            'sid'            => $this->session_id,
            'nif'            => @$data['nif'],
            'docSeries'      => $docSerie,
            'opt_name'       => utf8_encode(@$data['name']),
            'opt_address'    => utf8_encode(@$data['address']),
            'opt_locality'   => utf8_encode(@$data['city']),
            'opt_postalCode' => @$data['zip_code'],
        ];


        $draftId = $this->execute('insertReciptHeader', $data);

        if (!empty($docdate)) {
            $this->insertDocumentHeaderAdicionalField($draftId, 9, 'docdate', $docdate);
        }

        if (!empty($obs)) {
            $this->insertDocumentHeaderAdicionalField($draftId, 9, 'obs', utf8_encode($obs));
        }

        if (!empty($docRef)) {
            $this->insertDocumentHeaderAdicionalField($draftId, 9, 'docRef', utf8_encode($docRef));
        }

        /* if(!empty($paymentMethod)) {

            if($paymentMethod == 'tb') {
                $paymentMethodCode = 3; //transferencia bancaria
            } elseif($paymentMethod == 'check') {
                $paymentMethodCode = 2; //cheque
            } elseif($paymentMethod == 'mb') {
                $paymentMethodCode = 4; //multibanco
            } else {
                $paymentMethodCode = 1; //numerario
            }

            $this->insertDocumentHeaderAdicionalField($draftId, 9, 'paymentMethod', $paymentMethodCode);
        }*/

        return $draftId;
    }

    /**
     * Cria uma nova linha num documento rascunho.
     * Nos campos «qt», «price», «tax» e «prodDes», se forem passadas cadeias de caracteres vazias (''),
     * serão tidos em conta os valores que estão no artigo. Se o campo «docType» contiver um valor diferente dos
     * aceites, será considerado 13:encomenda.
     *
     * @param int $draftId ID do rascunho
     * @param string $docType Tipo de Documento
     * @param array $data array com os dados da linha [ref, qt, price, tax, prodDesc, discount]
     * @return mixed
     */
    public function insertReceiptLine($draftId, $data, $serieId)
    {
        $data = [
            'sid'         => $this->session_id,
            'idDocTemp'   => $draftId,
            'docSeries'   => $serieId,
            'reciptRef'   => @$data['invoice_ref'],
            'reciptValue' => @$data['value'],
        ];

        $lineId = $this->execute('insertReciptLine', $data);

        return $lineId;
    }

    /**
     * Fecha o recibo
     *
     * @param $idDocTemp
     * @param $docSeries
     * @return mixed
     */
    public function convertDraftToReceipt($idDocTemp, $docSeries)
    {
        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $idDocTemp,
            'docSeries' => $docSeries,
        ];

        return $this->execute('closeRecipt', $data, true);
    }

    /**
     * Gera um recibo para a factura indicada.
     *
     * @param $idDoc ID da fatura
     * @param $docType ID da série da fatura
     * @return mixed
     */
    public function settleInvoice($idDoc, $docType)
    {
        //$docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDoc'     => $idDoc,
            'docSeries' => $docType,
        ];

        try {
            return $this->execute('settleInvoice', $data);
        } catch (\Exception $e) {
            throw new Exception('Erro ao emitir recibo. A fatura já pode ter sido liquidada anteriormente.');
        }
    }

    /**
     * Get current serie for current api key
     * @param $docType
     */
    public function getCurrentSerie($docType, $apiKey = null)
    {

        $series = $this->getActiveSeries($docType, $apiKey);

        //obtem a série ativa correspondente à apiKey atual
        foreach ($series as $serie) {

            $key   = $serie->Key;
            $name  = $serie->Value;
            $code  = $serie->Info1;
            $info2 = $serie->Info2;

            $isApiSerie = false;
            if ($info2) {
                /*$info2 = explode('=', str_replace(';', '', $info2));
                $isApiSerie = trim(@$info2[1]);
                $isApiSerie == 'true' ? true : false;*/
                $isApiSerie = $info2 == 'current apikey series = true;' ? true : false;
            }

            if ($isApiSerie) {
                return [
                    'id'       => $key,
                    'code'     => $code,
                    'name'     => utf8_decode($name),
                    'doc_type' => $docType,
                    'api_key'  => $this->apiKey
                ];
            }
        }

        return false;
    }

    /**
     * Gera um recibo para a factura indicada.
     *
     * @param $idDoc ID da fatura
     * @param $docType ID da série da fatura
     * @return mixed
     */
    public function getCertificate($idDoc, $docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDoc'     => $idDoc,
            'docSeries' => $docType,
        ];

        try {
            return $this->execute('getDocumentCertificate', $data);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Devolve os documentos do mês e ano em formato zipado
     *
     * @param $draftId
     * @param $docType
     * @param $lineId
     * @param $taxId
     * @return mixed
     */
    public function getZipDocuments($docType, $month, $year, $mode = 'all_in_one_pdf', $startDate = null, $endDate = null)
    {

        /*
         * “count” -> contagem dos documentos
         * “pdf” -> devolve todos os pdfs dos documentos
         * “all_in_one_pdf” -> devolve 1 único pdf com todos os documentos
         */
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'        => $this->session_id,
            'docType'    => $docType,
            'month'      => $month,
            'year'       => $year,
            'mode'       => $mode,
            /*'start_date' => $startDate,
            'end_date'   => $endDate*/
        ];

        $url = $this->execute('zipDocs', $data, true);

        if (is_numeric($url)) {
            throw new \Exception('Ficheiro sem dados.');
        }

        return 'https://login.keyinvoice.com/' . $url;
    }

    /**
     * Lista as taxas de IVA
     *
     * @return mixed
     * @throws \Exception
     */
    public function getReportIVA($startDate, $endDate, $exportType)
    {
        $data = [
            'sid'           => $this->session_id,
            'type'          => 'pdf',
            'date_from'     => $startDate,
            'date_to'       => $endDate,
            'export_type'   => $exportType
        ];

        return $this->execute('getReportIVA', $data);
    }

    /**
     * Gera uma referência MB para uma fatura
     * Este método usa as configurações do KeyInvoice
     *
     * @return mixed
     * @throws \Exception
     */
    public function addPaymentMB($idDoc, $docType, $native = true)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'idDoc'   => $idDoc,
            'docType' => $docType
        ];

        $reference = (array) $this->execute('generatembref', $data);

        return $reference;
    }

    /**
     * @param $docSeries
     * @param $docRef
     * @param $target_idDoc
     * @param $target_docSeries
     * @return mixed
     * @throws \Exception
     */
    public function creditRegularization($docSeries, $docRef, $target_idDoc, $target_docSeries)
    {

        $data = [
            'sid'              => $this->session_id,
            'docSeries'        => $docSeries,
            'docRef'           => $docRef,
            'target_idDoc'     => $target_idDoc,
            'target_docSeries' => $target_docSeries,
        ];

        return $this->execute('creditRegularization', $data);
    }

    /**
     * Inserir dados de transporte a um documento.
     *
     * @param $draftId
     * @param $docType
     * @param $serieId
     * @param $data
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function insertTransportDetails($draftId, $docType, $serieId, $data)
    {
        $data = [
            'sid'                => $this->session_id,
            'idDocTemp'          => $draftId,
            'docType'            => $docType,
            'docSeries'          => $serieId,
            'HoraCarga'          => @$data['shipping_date'],
            'MoradaCarga'        => utf8_encode(str_replace("'", "’", @$data['sender_address'])),
            'LocalidadeCarga'    => utf8_encode(str_replace("'", "’", @$data['sender_city'])),
            'CodPostalCarga'     => @$data['sender_zip_code'],
            'CodigoPaisCarga'    => @$data['sender_country'],
            'DataHoraDescarga'   => @$data['delivery_date'],
            'MoradaDescarga'     => utf8_encode(str_replace("'", "’", @$data['recipient_address'])),
            'LocalidadeDescarga' => utf8_encode(str_replace("'", "’", @$data['recipient_city'])),
            'CodPostalDescarga'  => @$data['recipient_zip_code'],
            'CodigoPaisDescarga' => @$data['recipient_country'],
            'MatriculaViatura'   => @$data['vehicle'],
        ];

        $lineId = $this->execute('documentTransport', $data);

        return $lineId;
    }



    /**
     * Inserir dados de transporte a um documento.
     *
     * @param $draftId
     * @param $docType
     * @param $serieId
     * @param $data
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function communicateAT($docId, $docType, $serie = null)
    {

        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        if ($serie) {
            $data = [
                'sid'       => $this->session_id,
                'idDoc'     => $docId,
                'docType'   => $docType,
                'docSeries' => $serie
            ];

            $atCode = $this->execute('documentCommunication_byseries', $data);
        } else {
            $data = [
                'sid'     => $this->session_id,
                'idDoc'   => $docId,
                'docType' => $docType,
            ];

            $atCode = $this->execute('documentCommunication', $data);
        }

        return $atCode;
    }
}
