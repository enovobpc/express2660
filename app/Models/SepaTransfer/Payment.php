<?php

namespace App\Models\SepaTransfer;

use App\Models\Bank;
use App\Models\BankInstitution;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Setting;

class Payment extends \App\Models\BaseModel
{

    //SEPA MANUAL
    //https://www.bportugal.pt/sites/default/files/sepa-manual-c2b-xml-112016-pt.pdf
    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_sepa_payments';
    const STATUS_EDITING           = 'editing';
    const STATUS_PENDING           = 'pending';
    const STATUS_REJECTED          = 'rejected';
    const STATUS_CONCLUDED         = 'concluded';
    const STATUS_CONCLUDED_PARTIAL = 'concluded-partial';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sepa_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'type', 'code', 'name', 'company_name', 'bank_id', 'bank_name', 'bank_iban', 'bank_swift', 'credor_code',
        'transactions_count', 'transactions_total', 'status', 'has_errors', 'errors_processed', 'error_code', 'error_msg',
        'bank_operation_code', 'company_vat'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'type'    => 'required',
        'code'    => 'required',
        'company_name' => 'required'
    );


    /**
     * Set payment code
     * @param false $save
     * @return int
     */
    public function setCode($save = false) {

        $sepaPayment = $this;
        $month = date('m');
        $year  = date('y');

        $lastCode = Payment::filterSource()
            ->whereRaw('YEAR(created_at) = 20' . $year)
            ->orderByRaw('CAST(code as unsigned) desc')
            ->first(['code']);

        @$lastCode->code = substr(@$lastCode->code, 4);
        $code = (int) @$lastCode->code + 1;

        $code = $year . $month . str_pad($code, 4, '0', STR_PAD_LEFT);

        if($save) {
            $sepaPayment->code = $code;
            $sepaPayment->save();
        }

        return $code;
    }

    /**
     * Create SEPA XML File
     * @return string
     */
    public function createXml($returnMode = 'string') {
        if($this->type == 'dd') {
            return $this->createDirectDebitXml($returnMode);
        }

        return $this->createBankTransferXml($returnMode);
    }

    /**
     * Create SEPA XML File for direct debits
     * @return string
     */
    public function createBankTransferXml($returnMode = 'string') {

        $payment = $this;
        $createdDate = $payment->created_at;
        $createdDate = $createdDate->format('Y-m-d').'T'.$createdDate->format('H:i:s');

        $groupXml = '';
        foreach ($payment->groups as $group) {


            $transactionLines = '';
            foreach ($payment->transactions as $transaction) {

                $transactionLines.=
                "<CdtTrfTxInf>
                    <PmtId>
                        <EndToEndId>".removeAccents($transaction->reference)."</EndToEndId>
                    </PmtId>
                    <Amt>
                        <InstdAmt Ccy=\"EUR\">".$transaction->amount."</InstdAmt>
                    </Amt>
                    <CdtrAgt>
                        <FinInstnId>
                            <BIC>".$transaction->bank_swift."</BIC>
                        </FinInstnId>
                    </CdtrAgt>
                    <Cdtr>
                        <Nm>".removeAccents($transaction->company_name)."</Nm>
                    </Cdtr>
                    <CdtrAcct>
                        <Id>
                            <IBAN>".$transaction->bank_iban."</IBAN>
                        </Id>
                    </CdtrAcct>
                    <Purp>
                        <Cd>".$transaction->transaction_code."</Cd>
                    </Purp>
                    <RmtInf>
                        <Ustrd>".removeAccents($transaction->obs)."</Ustrd>
                    </RmtInf>
                </CdtTrfTxInf>\n";
            }

            $groupXml.= '<PmtInf>
                    <PmtInfId>'.$group->code.'</PmtInfId>
                    <PmtMtd>TRF</PmtMtd>
                    <NbOfTxs>'.$group->transactions_count.'</NbOfTxs>
                    <CtrlSum>'.$group->transactions_total.'</CtrlSum>
                    <PmtTpInf>
                        <SvcLvl>
                            <Cd>SEPA</Cd>
                        </SvcLvl>
                        <CtgyPurp>
                            <Cd>'.$group->category.'</Cd>
                        </CtgyPurp>
                    </PmtTpInf>
                    <ReqdExctnDt>'.$group->processing_date->format('Y-m-d').'</ReqdExctnDt>
                    <Dbtr>
                        <Nm>'.$group->company.'</Nm>
                        <Id>
                            <OrgId>
                                <Othr>
                                    <Id>'.$group->credor_code.'</Id>
                                </Othr>
                            </OrgId>
                        </Id>
                    </Dbtr>
                    <DbtrAcct>
                        <Id>
                            <IBAN>'.$group->bank_iban.'</IBAN>
                        </Id>
                    </DbtrAcct>
                    <DbtrAgt>
                        <FinInstnId>
                            <BIC>'.$group->bank_swift.'</BIC>
                        </FinInstnId>
                    </DbtrAgt>
                    '.$transactionLines.'
                </PmtInf>';


        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.001.03">
        <CstmrCdtTrfInitn>
            <GrpHdr>
                <MsgId>'.removeAccents($payment->name).'</MsgId>
                <CreDtTm>'.$createdDate.'</CreDtTm>
                <NbOfTxs>'.$payment->transactions_count.'</NbOfTxs>
                <CtrlSum>'.$payment->transactions_total.'</CtrlSum>
                <InitgPty>
                    <Nm>'.removeAccents($payment->company).'</Nm>
                    <Id>
                        <PrvtId>
                            <Othr>
                                <Id>'.removeAccents($payment->code).'</Id>
                            </Othr>
                        </PrvtId>
                    </Id>
                </InitgPty>
            </GrpHdr>
            '.$groupXml.'
        </CstmrCdtTrfInitn>
    </Document>';


        if($returnMode == 'file') {
            header('Content-Disposition: attachment; filename="sepa.xml"');
            header('Content-Transfer-Encoding: binary');
            header("Content-type:application/xml");
            echo $xml;
            exit;
        }

        return $xml;
    }

    /**
     * Create SEPA XML File for direct debits
     * @return string
     */
    public function createDirectDebitXml($returnMode = 'string') {

        $payment = $this;
        $createdDate = $payment->created_at;
        $createdDate = $createdDate->format('Y-m-d').'T'.$createdDate->format('H:i:s');

        $groupXml = '';
        foreach ($payment->groups as $group) {


            $transactionLines = '';
            foreach ($payment->transactions as $transaction) {

                $transactionLines.=
                    "<DrctDbtTxInf>
                    <PmtId>
                        <EndToEndId>".removeAccents($transaction->reference)."</EndToEndId>
                    </PmtId>
                    <InstdAmt Ccy=\"EUR\">".$transaction->amount."</InstdAmt>
                    <DrctDbtTx>
                        <MndtRltdInf>
                            <MndtId>".$transaction->mandate_code."</MndtId>
                            <DtOfSgntr>".$transaction->mandate_date->format('Y-m-d')."</DtOfSgntr>
                            <AmdmntInd>false</AmdmntInd>
                        </MndtRltdInf>
                    </DrctDbtTx>
                    <DbtrAgt>
                        <FinInstnId>
                            <BIC>".$transaction->bank_swift."</BIC>
                        </FinInstnId>
                    </DbtrAgt>
                    <Dbtr>
                        <Nm>".removeAccents($transaction->company_name)."</Nm>
                    </Dbtr>
                    <DbtrAcct>
                        <Id>
                            <IBAN>".$transaction->bank_iban."</IBAN>
                        </Id>
                    </DbtrAcct>
                    <RmtInf>
                        <Ustrd>".removeAccents($transaction->obs)."</Ustrd>
                    </RmtInf>
                </DrctDbtTxInf>\n";
            }

             $groupXml.= '<PmtInf>
                    <PmtInfId>'.$group->code.'</PmtInfId>
                    <PmtMtd>DD</PmtMtd>
                    <NbOfTxs>'.$group->transactions_count.'</NbOfTxs>
                    <CtrlSum>'.$group->transactions_total.'</CtrlSum>
                    <PmtTpInf>
                        <SvcLvl>
                            <Cd>SEPA</Cd>
                        </SvcLvl>
                        <LclInstrm>
                            <Cd>'.$group->service_type.'</Cd>
                        </LclInstrm>
                        <SeqTp>'.$group->sequence_type.'</SeqTp>
                    </PmtTpInf>
                    <ReqdColltnDt>'.$group->processing_date->format('Y-m-d').'</ReqdColltnDt>
                    <Cdtr>
                        <Nm>'.$group->company.'</Nm>
                    </Cdtr>
                    <CdtrAcct>
                        <Id>
                            <IBAN>'.$group->bank_iban.'</IBAN>
                        </Id>
                    </CdtrAcct>
                    <CdtrAgt>
                        <FinInstnId>
                            <BIC>'.$group->bank_swift.'</BIC>
                        </FinInstnId>
                    </CdtrAgt>
                    <ChrgBr>SLEV</ChrgBr>
                    <CdtrSchmeId>
                        <Id>
                            <PrvtId>
                                <Othr>
                                    <Id>'.$group->credor_code.'</Id>
                                </Othr>
                            </PrvtId>
                        </Id>
                    </CdtrSchmeId>
                    '.$transactionLines.'
                </PmtInf>';


        }


        //$msgId = removeAccents($payment->name);
        //$paymentCodeId = $payment->code;
        //if(Bank::getBankCodeFromIban($payment->bank_iban) == 'PT0033') { //para o banco Millenium BCP o identificador tem de ser o NIF
        //    $paymentCodeId = $payment->company_vat;
        //}

        $msgId         = $payment->code; //codigo da transação
        $paymentCodeId = $payment->company_vat; //nif da empresa

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02">
            <CstmrDrctDbtInitn>
                <GrpHdr>
                    <MsgId>'.removeAccents($msgId).'</MsgId>
                    <CreDtTm>'.$createdDate.'</CreDtTm>
                    <NbOfTxs>'.$payment->transactions_count.'</NbOfTxs>
                    <CtrlSum>'.$payment->transactions_total.'</CtrlSum>
                    <InitgPty>
                        <Nm>'.removeAccents($payment->company_name).'</Nm>
                        <Id>
                            <OrgId>
                                <Othr>
                                    <Id>NOTPROVIDED</Id>
                                </Othr>
                            </OrgId>
                            <PrvtId>
                                <Othr>
                                    <Id>'.removeAccents($paymentCodeId).'</Id>
                                </Othr>
                            </PrvtId>
                        </Id>
                    </InitgPty>
                </GrpHdr>
                '.$groupXml.'
            </CstmrDrctDbtInitn>
        </Document>';


        if($returnMode == 'file') {
            header('Content-Disposition: attachment; filename="sepa.xml"');
            header('Content-Transfer-Encoding: binary');
            header("Content-type:application/xml");
            echo $xml;
            exit;
        }

        return $xml;
    }

    /**
     * Print payments summary
     *
     * @param $paymentIds
     */
    public static function printSummary($paymentIds, $returnMode = 'pdf') {

        $payments = Payment::filterSource()
            ->whereIn('id', $paymentIds)
            ->get();

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

        foreach ($payments as $payment) {
            $data = [
                'payment'           => $payment,
                'documentTitle'     => ($payment->type == 'dd' ? 'Débito Direto SEPA - ' : 'Transferência SEPA - ') . ' ' . $payment->code,
                'documentSubtitle'  => $payment->name,
                'view'              => 'admin.printer.sepa_transfers.summary'
            ];

            $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write
        }
        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;

        if ($returnMode == 'string') {
            return $mpdf->Output('Resumo Transferência SEPA.pdf', 'S'); //string
        }

        return $mpdf->Output('Resumo Transferência SEPA.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Return status code message
     * @param $statusCode
     * @return string
     */
    public function getReturnStatusMsg($statusCode) {

        $codes = [
            '0000' => 'Transação aceite',
            '0001' => 'Não existe Autorização de Débito',
            '0002' => 'Recusa de débito pelo Banco',
            '0003' => 'Saldo insuficiente.',
            '0004' => 'Dígitos de controlo do NIB inválidos',
            '0005' => 'Registo Inválido',
            '0006' => 'Cancelamento de instruções',
            '0007' => 'Já efectuado por outro meio de pagamento.',
            '0008' => 'Operação duplicada.',
            '0009' => 'Operação não diz respeito ao cliente.',
            '0010' => 'Nome do destinatário não corresponde.',
            '0014' => 'Balcão inexistente',
            '0015' => 'IBAN destinatário inexistente.',
            '0016' => 'Conta destinatária encerrada/bloqueada. (Não movimentável). ',
            '0017' => 'Importância inválida. Caracteres inválidos ou Zeros.',
            '0018' => 'Destinatário não identificado.',
            '0021' => 'Movimento não permitido para a conta destino em causa.',
            '0022' => 'Destinatário devolve directamente ao Ordenante. ',
            '0023' => 'Acerto de contas entre intervenientes. ',
            '0025' => 'Não recuperável.',
            'A259' => 'Morada do destinatário com caracteres inválidos.',
            'A262' => 'Código do país da morada do destinatário inválido.',
            'A263' => 'Morada do destinatário preenchida e código do país não preenchido. ',
            'A290' => 'Nome do ordenante original com caracteres inválidos',
            'A293' => 'Nome do último destinatário com caracteres inválidos.',
            'PY01' => 'Código BIC não pertence a um Participante',
            'R207' => 'Referência do Ordenante (EndToEndId) com caracteres inválidos.',
            'R216' => 'BIC do Banco destino inválido.',
            'R217' => 'Nome do destinatário com caracteres inválidos.',
            'R218' => 'IBAN do destinatário inválido.',
            'R219' => 'Informação estruturada inválida.',
            'R220' => 'Informação adicional com caracteres inválidos ou erro no Tipo ou Referência',
            'R296' => 'Cód. ISO do motivo da transferência inválido. Consultar Anexo 6.',
            'R242' => 'IBAN do devedor inválido.',
            'A359' => 'Cód. ISO da Categoria do motivo da transferência inválido. Consultar Anexo 5.',

            'M000' => 'Totalmente aceite',
            'M001' => 'Parcialmente aceite',
            'M002' => 'Totalmente rejeitada',
            'M003' => 'Identificação mensagem inválida (caracteres) ou não preenchida',
            'M004' => 'Quantidade de transacções mensagem inválidas',
            'M005' => 'Montante total (Control Sum) da mensagem inválido',
            'M006' => 'Identificação do Initiating Party inválido/desconhecido',
            'M007' => 'Group Reversal inválido (pain.007)',
            'M008' => 'Mensagem em duplicado',
            'M009' => 'Devolução/R-transaction para Ordenante/Credor',
            'M010' => 'Data/hora de criação da mensagem inválida ou não preenchida',
            'MO01' => 'Identificação da mensagem original inválida',
            'MO02' => 'Nome da mensagem original inválida',

            'L000' => 'Totalmente aceite',
            'L001' => 'Parcialmente aceite',
            'L002' => 'Devolução/R-transaction para Ordenante/Credor ',
            'LH03' => 'Tipo de serviço inválido. Deve ser “URG” (pain.001), “B2B” (pain.008 e pain.007) ou “SEPA”
(pain.008).',
            'LH06' => 'BIC Ordenante/ Credor inválido.',
            'LH07' => 'IBAN Ordenante/ Credor inválido. ',
            'LH08' => 'Código de Moeda inválido. Deve ser “EUR”.',
            'LH09' => 'Conta inexistente ou bloqueada. Implica a rejeição total do lote.',
            'LH11' => 'Data de lançamento inválida. ',
            'LH12' => 'Morada do Ordenante/Credor inválida.',
            'LH13' => 'Referência do Payment Information (lote) do Ordenante/Credor com caracteres inválidos.',
            'LH14' => 'Referência do Payment Information (lote) do Ordenante/Credor não preenchida.',
            'LH15' => 'Referência do Payment Information (lote) do Ordenante/Credor duplicada.',
            'LH16' => 'Todas as transacções de um lote foram rejeitadas',
            'LH17' => 'Identificação do Credor inválido ou inexistente. (Só para lotes de Débito)',
            'LH18' => 'Nome do Ordenante/Credor não preenchido ou com caracteres inválidos.',
            'LH20' => 'Código de País da Morada do Ordenante/Credor inválido. ',
            'LH22' => 'Código da Categoria do motivo da transferência/cobrança inválida. Consultar Anexo 5.',
            'LH23' => 'Cód. ISO do motivo da transferência inválido. Consultar Anexo 6.',
            'LH24' => 'Método de Pagamento inválido',
            'LH25' => 'Tipo de movimento inválido ',
            'LH26' => 'Original Payment Identification (Id lote original) inválido ou inexistente (pain.007)',
            'LH27' => 'Indicador Payment Information Reversal inválido (pain.007) ',
            'LH28' => 'Outra Identificação do Ordenante/Credor inválido',
            'LH29' => 'Tipo de movimento (Sequence Type) inválido ou não preenchido',
            'LT02' => 'Quantidade de transacções inválidas (Payment Information)',
            'LT03' => 'Montante total (Control Sum) do lote inválido (Payment Information)',
        ];

        return @$codes[$statusCode] ? $codes[$statusCode] : 'Erro '.$statusCode.' não documentado.';
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function groups()
    {
        return $this->hasMany('App\Models\SepaTransfer\PaymentGroup', 'payment_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\SepaTransfer\PaymentTransaction', 'payment_id');
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
    public function setNameAttribute($value) {
        $this->attributes['name'] = trim($value);
    }

    public function setCompanyAttribute($value) {
        $this->attributes['company'] = trim($value);
    }

    public function setErrorCodeAttribute($value) {
        $this->attributes['error_code'] = empty($value) || $value == 'M000' ? null : $value;
    }

    public function setErrorMsgAttribute($value) {
        $this->attributes['error_msg'] = empty($value) ? null : $value;
    }

    public function getEditModeAttribute() {
        $editMode = !@$this->attributes['status'] || @$this->attributes['status'] == 'editing' ? true : false;
        return $editMode;
    }

}
