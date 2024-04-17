<?php

namespace App\Models\InvoiceGateway\SageX3;

use App\Models\PaymentCondition;
use Date, Response;

class Document extends \App\Models\InvoiceGateway\SageX3\Base {

    /**
     * Sage X3 webservice Method
     * @var string
     */
    public $method = 'YWSSIH';


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsInvoices()
    {
        return $this->get($this->method);
    }

    /**
     * Obtem uma fatura de compra pelo seu ID de documento
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getInvoice($docId)
    {
        $data = [
            'NUM' => $docId
        ];

        return $this->get($this->method, $data);
    }

    /**
     * Get SAFT file
     * @param $year
     * @param null $month
     * @return mixed
     * @throws \Exception
     */
    public function getSaftFile($year, $month = null)
    {
       throw new \Exception('Funcionalidade não disponível.');
    }

    /**
     * Lista as taxas de IVA
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTaxes()
    {
        throw new \Exception('Funcionalidade não disponível.');
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
        throw new \Exception('Funcionalidade não disponível.');
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
        throw new \Exception('Funcionalidade não disponível.');
    }

    /**
     * Verifica se um documento já existe. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $idDoc identificador do documento
     * @param $docType tipo de documento. Ver tabela de Tipos de documentos possíveis.
     * @return mixed
     */
    public function documentExists($idDoc, $docType) { return false; }

    /**
     * Conta o número total de documentos do tipo indicado gravados na base de dados.
     *
     * @param $docType
     * @return mixed
     */
    public function countDocuments($docType) { return false; }

    /**
     * Devolve a lista de documentos.
     *
     * @param $docType
     * @param int $offset
     * @param string $orderBy
     * @param string $sortBy [asc|desc]
     * @return mixed
     */
    public function getDocumentslist($docType = null, $offset = 0)
    {
        return $this->get($this->method);
    }

    /**
     * Obtém uma lista de documentos filtrados pelo NIF de um cliente específico.
     *
     * @param $docType
     * @param $nif
     * @param int $offset
     * @return mixed
     */
    public function getDocumentslistByCustomer($docType, $nif, $offset = 0,  $orderBy = 'date', $sortBy = 'desc') { return false; }

    /**
     * Obtém os dados de um documento. Se o campo «docType» contiver um valor diferente dos aceites, será considerado 13:encomenda.
     *
     * @param $idDoc
     * @param $docType
     * @return mixed
     */
    public function getDocument($docId, $docType = null, $docSeries = null)
    {
        $data = [
            'NUM' => $docId
        ];

        return $this->get($this->method, $data);
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
        return false;
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
        return false;
    }

    /**
     * Devolve o conteúdo do ficheiro da fatura em base 64.
     *
     * @param $idDoc
     * @param $docType
     * @param bool $acceptStorage [true = get from storage, false = get from API]
     * @return mixed
     */
    public function getDocumentPdf($idDoc, $docType = null, $docSeries = null, $acceptStorage = true)
    {
        $idDoc = str_replace("\\", "/", $idDoc);

        $xml = '<PARAM>
                    <GRP ID="GRP1" >
                        <FLD NAME="SIHNUM">'.$idDoc.'</FLD>           
                    </GRP>
                </PARAM>';

        $xml = '<![CDATA[' . $xml . ']]>';
        $result = $this->run('YPDF', $xml);

        return @$result['RESULT']['GRP']['FLD'][2]['value'];
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
    public function createInvoice($data, $customer)
    {

        $docdate = str_replace('-', '', @$data['docdate']);
        $duedate = @$data['duedate'];
        $duedate = str_replace('-', '', $duedate);

        $paymentCondition = PaymentCondition::where('code', @$data['payment_condition'])->first();
        $paymentCondition = @$paymentCondition->software_code;

        $docType = 'FCN'; //config('webservices_mapping.keyinvoice.doc_type.' . $type);

       /* $data = [
            'nif'            => $data['nif'],
            'obs'            => utf8_encode(@$data['obs']),
            'opt_name'       => utf8_encode(@$data['name']),
            'opt_nif'        => $data['vat'],
            'opt_address'    => utf8_encode(@$data['address']),
            'opt_locality'   => utf8_encode(@$data['city']),
            'opt_postalCode' => @$data['zip_code'],
            'docRef'         => @$data['docref']
        ];*/

        $customerCode = strtoupper(@$customer->billing_country).@$customer->vat;


        $lines = '';
        foreach ($data['line'] as $key => $item) {
            if(!empty(@$item['reference'])) {

                $item['reference'] = 'MO00SZ27EX';


                $lines .= '<LIN NUM="1">
                            <FLD NAME="ITMREF" TYPE="Char">' . @$item['reference'] . '</FLD>
                            <FLD NAME="SAU" TYPE="Char">UN</FLD>
                            <FLD NAME="QTY" TYPE="Decimal">' . $item['qty'] . '</FLD>
                            <FLD NAME="GROPRI" TYPE="Decimal">' . $item['total_price'] . '</FLD>
                            <FLD NAME="DISCRGVAL1" TYPE="Decimal">0</FLD>
                            <FLD NAME="DISCRGVAL2" TYPE="Decimal">0</FLD>
                            <FLD NAME="DISCRGVAL3" TYPE="Decimal">0</FLD>
                        </LIN>';
            }
        }
        

        $xml = '<PARAM>
                    <GRP ID="SIH0_1">
                        <FLD NAME="SALFCY" TYPE="Char">M1</FLD>
                        <FLD NAME="SIVTYP" TYPE="Char">'.$docType.'</FLD>
                        <FLD NAME="NUM" TYPE="Char"></FLD>
                        <FLD NAME="INVREF" TYPE="Char">'.$data['docref'].'</FLD>
                        <FLD NAME="INVDAT" TYPE="Date">'.$docdate.'</FLD>
                        <FLD NAME="BPCINV" TYPE="Char">'.strtoupper($customer->billing_country).$customer->vat.'</FLD>
                    </GRP>
                    <GRP ID="SIH2_2">
                        <FLD NAME="STRDUDDAT" TYPE="Date">'.$duedate.'</FLD>
                        <FLD NAME="PTE" TYPE="Char">'.$paymentCondition.'</FLD>
                    </GRP>
                    <GRP ID="SIH2_3">
                        <LST NAME="DES" SIZE="3" TYPE="Char">
                            <ITM>'.@$data['obs'].'</ITM>
                        </LST>
                    </GRP>
                    <TAB DIM="300" ID="SIH4_1" SIZE="2">
                        '.$lines.'
                    </TAB>
                </PARAM>';

        $xml = '<![CDATA[' . $xml . ']]>';
        $result = $this->save('YWSSIH', $xml);
        $result = @$result['value'];

        $result = str_replace("/", "\\", $result);
        return $result;
    }

}