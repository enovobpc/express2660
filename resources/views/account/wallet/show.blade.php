<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">Detalhe do pagamento</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12 text-center">
            @if($payment->method == 'mb')
                <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 40px"/>
            @elseif($payment->method == 'mbway')
                <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 40px"/>
            @elseif($payment->method == 'visa' || $payment->method == 'cc')
                <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 40px"/>
            @elseif($payment->method == 'visa' || $payment->method == 'tb')
                <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 40px"/>
            @else
                <img src="" style="height: 20px"/>
            @endif
        </div>
        <div class="col-sm-12 text-center m-t-20">
            <h4 class="m-t-0">
                {{ $payment->description }}
            </h4>
            <p class="m-t-0 fs-16">
                Valor: {{ money($payment->value, $payment->currency) }}
            </p>
            <div>
                @if($payment->status == App\Models\GatewayPayment\Base::STATUS_SUCCESS)
                    <span class="label label-success">Aceite</span>
                @elseif($payment->status == App\Models\GatewayPayment\Base::STATUS_WAINTING)
                    <span class="label label-warning">Aguarda</span>
                @elseif($payment->status == App\Models\GatewayPayment\Base::STATUS_REJECTED)
                    <span class="label label-danger">Rejeitado</span>
                @else
                    <span class="label" style="background-color: #777">Pendente</span>
                @endif
            </div>
        </div>
        <div class="col-sm-12">
            <hr/>
            <ul class="list-unstyled lh-1-7">
                @if($payment->method == 'visa')
                <li><b>Cartão:</b> **** **** **** {{ substr($payment->cc_number, -4) }}</li>
                @elseif($payment->method == 'mbway')
                <li><b>Telemóvel:</b> {{ $payment->mbway_phone }}</li>
                @else
                <li><b>Entidade:</b> {{ $payment->mb_entity }}</li>
                <li><b>Referência:</b> {{ chunk_split($payment->mb_reference, 3, ' ') }}</li>
                @endif
                <li><b>Registo:</b> {{ $payment->created_at }}</li>
                <li><b>Pagamento:</b> {{ $payment->paid_at }}</li>
            </ul>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
</div>

