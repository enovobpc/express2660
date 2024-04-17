<?php

namespace App\Models\InvoiceGateway\Moloni;

use Date, Response, File, Mail, Setting;
use Mockery\Exception;

class Document extends \App\Models\InvoiceGateway\Moloni\Base {


    /**
     * @var string
     */
    public $apiKey;

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
     * Lista todas as séries activas para o tipo de documento indicado.
     *
     * @param $idDoc identificador do documento
     * @param $docType tipo de documento. Ver tabela de Tipos de documentos possíveis.
     * @return mixed
     */
    public function getActiveSeries($docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
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
            'order_by'=> $orderBy,
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
            'order_by'=> $orderBy,
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
    public function getDocument($idDoc, $docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'     => $this->session_id,
            'idDoc'   => $idDoc,
            'docType' => $docType,
        ];

        return $this->execute('getDocument_bySeries', $data, true);
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

        $storeFiles = Setting::get('billing_store_invoices');

        $method = 'getDocumentPDF';
        //$method = 'getDocumentPDFLink';

        $docTypeId = $docType;
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDoc'     => $idDoc,
            'docType'   => $docType,
            'docSeries' => $docSeries
        ];


        if($docSeries) {
            $acceptStorage = false; //quando se obtem a partir da lista de conta corrente
            $method = 'getdocumentpdflink_byseries';
            $data['docSeries'] = $docSeries;
        }

        //check if file exists on repository
        $filename   = 'doc_' . $docTypeId . '_' . $idDoc.'_' . $this->apiKey.'.txt';
        $folderpath = storage_path(self::STORAGE_PATH);
        $filepath   = storage_path(self::STORAGE_PATH . $filename);

        if($acceptStorage && $storeFiles && File::exists($filepath)) { //get file from storage
            $result = file_get_contents($filepath);
            return $result;
        } else { //download file and store in storage
            $result = $this->execute($method, $data, true);

            if($docSeries) {
                $result = base64_encode(file_get_contents($result));
            }

            if($storeFiles) {
                if(!File::exists($folderpath)) {
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
        $duedate = @$data['duedate'];
        $docdate = @$data['docdate'];
        $taxRetention = @$data['taxRetention'];
        $printComment = @$data['printComment'];

        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $type);

        if($data['nif'] == '999999999' || $data['nif'] == '999999990') {
            $data['nif'] = '';
        }

        $data = [
            'sid'            => $this->session_id,
            'nif'            => $data['nif'],
            'docType'        => $docType,
            'obs'            => utf8_encode(@$data['obs']),
            'opt_name'       => utf8_encode(@$data['name']),
            'opt_nif'        => $data['nif'],
            'opt_address'    => utf8_encode(@$data['address']),
            'opt_locality'   => utf8_encode(@$data['city']),
            'opt_postalCode' => @$data['zip_code'],
            'docRef'         => @$data['docref']
        ];

        $draftId = $this->execute('insertDocumentHeader', $data);

        if(!empty($docdate)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'docdate', $docdate);
        }

        if(!empty($duedate)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'duedate', $duedate);
        }

        if(!empty($printComment)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'printComment', utf8_encode($printComment));
        }

        if(!empty($taxRetention)) {
            $this->insertDocumentHeaderAdicionalField($draftId, $docType, 'taxRetention', $taxRetention);
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

        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $draftId,
            'docType'   => $docType,
            'ref'       => @$data['ref'],
            'qt'        => @$data['qt'] ? @$data['qt'] : 1,
            'price'     => @$data['price'],
            'tax'       => @$data['tax'],
            'prodDesc'  => utf8_encode(@$data['prodDesc']),
            'discount'  => @$data['discount'],
            'exemption' => @$data['exemption'],
        ];

        $lineId =  $this->execute('insertDocumentLine', $data);

        if(($data['tax'] == '0' || $data['tax'] == '0.00') && !empty($data['exemption'])) {
            $this->changeLineTax($draftId, $docType, $lineId, $data['exemption']);
        }

        /*if($data['tax'] == '0' && config('app.source') == 'asfaltolargo') {
            $this->changeLineTax($draftId, $docType, $lineId, 5);
        }*/

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
    public function insertDocumentHeaderAdicionalField($draftId, $docType, $fieldname, $value){

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
    public function convertDraftToDoc($idDocTemp, $docType)
    {
        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'idDocTemp' => $idDocTemp,
            'docType'   => $docType,
        ];

        return $this->execute('closeDocument', $data, true);
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
    public function deleteDocument($docId, $docType, $data)
    {
        //obtem a série ativa para o documento em questão

        $serie = $this->getCurrentSerie($docType); //obtem a serie em vigor do tipo de documento indicado

        if(empty($serie)) {
            $serieId = $data['doc_serie'];
        } else {
            $serieId = $serie['id'];
        }

        if(config('app.source') == 'ontimeservices') {
            $serieId = 62; //'T20.'
        }

        $docType = config('webservices_mapping.keyinvoice.doc_type.' . $docType);

        $data = [
            'sid'       => $this->session_id,
            'docType'   => $docType,
            'docSeries' => $serieId,
            'idDoc'     => $docId,
            'c_series'  => $data['credit_serie'],
            'c_date'    => $data['credit_date'],
            'c_reason'  => utf8_encode($data['credit_reason']),
        ];

        try {
            return $this->execute('setDocumentVoid', $data, true);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

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
    public function changeLineTax($draftId, $docType, $lineId, $taxId) {

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
    public function checkIfPaid($docId, $docSeries) {

        $data = [
            'sid'       => $this->session_id,
            'docId'     => $docId,
            'docSeries' => $docSeries,
        ];

        try {
            $response = $this->execute('checkIfSettle', $data, true);
        } catch (\Exception $e) {
            $msg = 'DOC ID: ' . $docId . ' --> ' . $e->getMessage(). ' File ' . $e->getFile() . ' Line ' . $e->getLine();

            Mail::raw($msg, function ($message){
                $message->to('paulo.costa@enovo.pt');
            });

            throw new \Exception($e->getMessage());
        }

        if($response == "1") {
            return false;
        }

        return true;
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
    public function getCurrentSerie($docType) {

        $series = $this->getActiveSeries($docType);

        //obtem a série ativa correspondente à apiKey atual
        foreach ($series as $serie) {

            $key   = $serie->Key;
            $name  = $serie->Value;
            $code  = $serie->Info1;
            $info2 = $serie->Info2;

            $isApiSerie = false;
            if($info2) {
                $info2 = explode('=', str_replace(';', '', $info2));
                $isApiSerie = trim(@$info2[1]);
                $isApiSerie == 'true' ? true : false;
            }

            if($isApiSerie) {
                return [
                    'id'   => $key,
                    'code' => $code,
                    'name' => $name,
                ];
            }
        }

        return false;
    }
}