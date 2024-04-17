{{ Form::open(['route' => 'admin.gateway.payments.store', 'method' => 'POST', 'class' => 'payment-form']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gerar pagamento automático</h4>
</div>
<div class="modal-body">
    @if($shipments)
    <div style="margin: -15px -15px 15px -15px;
    padding: 15px;
    background: #ddd;
    border-bottom: 1px solid #ccc;">
        <h4 class="m-0 bold">
            Pagamento envio #{{ $shipments->first()->tracking_code }}
        </h4>
    </div>
    @endif
    <div class="row">
        <div class="col-sm-12">
            <label style="font-weight: normal">Escolha uma forma de pagamento.</label>
            <div style="border: 1px solid #999;border-radius: 4px;">
            <ul class="list-inline m-t-10">
                <li style="margin-right: 28px; margin-left: 25px;">
                    <label>
                        {{ Form::radio('payment_method', 'mb', true, [hasModule('gateway_payments') ? : 'disabled']) }}
                        <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 33px"/>
                    </label>
                </li>
                <li style="margin-right: 15px">
                    <label>
                        {{ Form::radio('payment_method', 'mbway', null, [hasModule('gateway_payments') ? : 'disabled']) }}
                        <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 29px"/>
                    </label>
                </li>
                <li>
                    <label>
                        {{ Form::radio('payment_method', 'visa', null, [hasModule('gateway_payments') ? : 'disabled']) }}
                        <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 18px"/>
                        <img src="{{ asset('assets/img/default/mastercard.svg') }}" style="height: 23px; margin-left: 5px"/>
                    </label>
                </li>
            </ul>
            </div>
        </div>
    </div>

    @if(!hasModule('gateway_payments'))
        <div class="row row-10 m-t-20">
            <div class="col-sm-3">
                <img class="w-100 m-t-20" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ4Ny4zMTYgNDg3LjMxNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDg3LjMxNiA0ODcuMzE2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzQ3LjY1OCwwaC0yODhjLTMwLjkxMSwwLjA0LTU1Ljk2LDI1LjA4OS01Niw1NnYyODcuMzM2YzAuMDQsMzAuOTExLDI1LjA4OSw1NS45Niw1Niw1Nmg4OHYtMTZoLTg4ICAgIGMtMjIuMDgtMC4wMjYtMzkuOTc0LTE3LjkyLTQwLTQwVjIwOGg1MS4zNzZjNC43NDEtMC4zNDcsOC4zMDQtNC40NzEsNy45NTctOS4yMTNjLTAuMDY3LTAuOTE0LTAuMjc5LTEuODEyLTAuNjI5LTIuNjU5ICAgIGMtNy4xMS0xNi4xOCwwLjI0Mi0zNS4wNiwxNi40MjItNDIuMTdjMTYuMTgtNy4xMSwzNS4wNiwwLjI0Miw0Mi4xNywxNi40MjJjMy42MDUsOC4yMDQsMy42MDUsMTcuNTQ0LDAsMjUuNzQ4ICAgIGMtMS45NzYsNC4xMzQtMC4yMjYsOS4wODcsMy45MDgsMTEuMDYzYzEuMDY5LDAuNTExLDIuMjM2LDAuNzg3LDMuNDIsMC44MWg1MS4zNzZ2NDhjLTIuNjQzLTAuNDUtNS4zMTktMC42NzItOC0wLjY2NCAgICBjLTI2LjUxLTAuMDA5LTQ4LjAwNywyMS40NzQtNDguMDE3LDQ3Ljk4M2MtMC4wMDcsMTkuMDk5LDExLjMxLDM2LjM4NCwyOC44MTcsNDQuMDE3bDYuNC0xNC42NTYgICAgYy0xNi4yMDYtNy4wNS0yMy42MjktMjUuOTAyLTE2LjU4LTQyLjEwOGM1LjA4Ni0xMS42OTMsMTYuNjI5LTE5LjI1LDI5LjM4LTE5LjIzNmM0LjQwOS0wLjAwMSw4Ljc2OSwwLjkyLDEyLjgsMi43MDQgICAgYzQuMDQ5LDEuNzY3LDguNzY1LTAuMDgzLDEwLjUzMi00LjEzMmMwLjQ0LTEuMDA4LDAuNjY3LTIuMDk2LDAuNjY4LTMuMTk2VjIwOGg0MC42OGMtMC40NTUsMi42NDItMC42ODMsNS4zMTktMC42OCw4ICAgIGMwLjA0OCwyMi43OTQsMTYuMDc3LDQyLjQzLDM4LjQsNDcuMDRsMy4yLTE1LjY3MmMtMTQuODgyLTMuMDc5LTI1LjU2Ny0xNi4xNzEtMjUuNi0zMS4zNjhjLTAuMDAxLTQuNDA5LDAuOTItOC43NjksMi43MDQtMTIuOCAgICBjMS43NjctNC4wNDktMC4wODMtOC43NjUtNC4xMzItMTAuNTMyYy0xLjAwOC0wLjQ0LTIuMDk2LTAuNjY3LTMuMTk2LTAuNjY4aC01MS4zNzZ2LTQwLjY4YzIuNjQyLDAuNDU1LDUuMzE5LDAuNjgzLDgsMC42OCAgICBjMjYuNTEsMCw0OC0yMS40OSw0OC00OHMtMjEuNDktNDgtNDgtNDhjLTIuNjgxLTAuMDAzLTUuMzU4LDAuMjI1LTgsMC42OFYxNmgxMzZjMjIuMDgsMC4wMjYsMzkuOTc0LDE3LjkyLDQwLDQwdjEzNmgtNTEuMzc2ICAgIGMtNC40MTgsMC4wMDItNy45OTgsMy41ODYtNy45OTYsOC4wMDRjMC4wMDEsMS4xLDAuMjI4LDIuMTg4LDAuNjY4LDMuMTk2YzEuNzg0LDQuMDMxLDIuNzA1LDguMzkxLDIuNzA0LDEyLjhoMTYgICAgYzAuMDAzLTIuNjgxLTAuMjI1LTUuMzU4LTAuNjgtOGg0OC42OGM0LjQxOCwwLDgtMy41ODIsOC04VjU2QzQwMy42MTgsMjUuMDg5LDM3OC41NjksMC4wNCwzNDcuNjU4LDB6IE0yMTkuNjU4LDcyICAgIGMxNy42NzMsMCwzMiwxNC4zMjcsMzIsMzJzLTE0LjMyNywzMi0zMiwzMmMtNC40MDksMC4wMDEtOC43NjktMC45Mi0xMi44LTIuNzA0Yy00LjA0OS0xLjc2Ny04Ljc2NSwwLjA4My0xMC41MzIsNC4xMzIgICAgYy0wLjQ0LDEuMDA4LTAuNjY3LDIuMDk2LTAuNjY4LDMuMTk2djUwLjcxMmgtNDAuNjI0YzAuNDAxLTIuNDI1LDAuNjA5LTQuODc4LDAuNjI0LTcuMzM2YzAtMC4xNDQsMC0wLjI3MiwwLTAuNDE2ICAgIHMwLTAuMTY4LDAtMC4yNDhjMC0yNi41MS0yMS40OS00OC00OC00OGMtMjYuNTEsMC00OCwyMS40OS00OCw0OGMwLDAuMDgsMCwwLjE2OCwwLDAuMjQ4czAsMC4yNzIsMCwwLjQxNiAgICBjMC4wMTUsMi40NTgsMC4yMjMsNC45MTEsMC42MjQsNy4zMzZIMTkuNjU4VjU2YzAuMDI2LTIyLjA4LDE3LjkyLTM5Ljk3NCw0MC00MGgxMzZ2NTEuMzc2YzAuMDAyLDQuNDE4LDMuNTg2LDcuOTk4LDguMDA0LDcuOTk2ICAgIGMxLjEtMC4wMDEsMi4xODgtMC4yMjgsMy4xOTYtMC42NjhDMjEwLjg4OSw3Mi45MiwyMTUuMjQ5LDcxLjk5OSwyMTkuNjU4LDcyeiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ2Ni41NDYsMzQzLjg1NmwtNjcuMjU2LTQ2LjMxMmwtMjUuODMyLTY5LjAxNmMtMS41NDEtNC4xNDEtNi4xNDctNi4yNDgtMTAuMjg4LTQuNzA3ICAgIGMtMC4xNDgsMC4wNTUtMC4yOTUsMC4xMTUtMC40NCwwLjE3OWwtNTIsMjNjLTMuODk4LDEuNzIxLTUuNzYyLDYuMTk2LTQuMjQsMTAuMTc2YzAuODY0LDIuMjQsMS42NzIsMy45MzYsMi41MjgsNS42OTYgICAgczEuNjQ4LDMuNDA4LDIuNjE2LDYuMDA4YzYuMjEsMTYuNTkzLTIuMTUxLDM1LjA4Ny0xOC43MTIsNDEuMzg0Yy0xNi41NTMsNi4xOTEtMzQuOTkxLTIuMjA4LTQxLjE4My0xOC43NjIgICAgYy0xLjU1My00LjE1Mi0yLjIyNi04LjU4MS0xLjk3Ny0xMy4wMDZjMC4yMzItNC40MTItMy4xNTctOC4xNzctNy41NjktOC40MDljLTEuMTA2LTAuMDU4LTIuMjExLDAuMTE0LTMuMjQ3LDAuNTA1ICAgIGwtNTUuNTg0LDIwLjkzNmMtNC4xMjcsMS41NTUtNi4yMTcsNi4xNTctNC42NzIsMTAuMjg4bDE5Ljk1Miw1My4zMDRjLTIuNjQ1LDAuNDgyLTUuMjQyLDEuMTk5LTcuNzYsMi4xNDQgICAgYy0yNC44OTUsOS4yNzctMzcuNTU1LDM2Ljk3OS0yOC4yNzgsNjEuODc0YzkuMjc3LDI0Ljg5NSwzNi45NzksMzcuNTU1LDYxLjg3NCwyOC4yNzhjMi41NjMtMC45NTUsNS4wNC0yLjEyNyw3LjQwNC0zLjUwNCAgICBsMTQuMzA0LDM4LjIwOGMxLjU1LDQuMTM4LDYuMTYsNi4yMzUsMTAuMjk4LDQuNjg1YzAuMDA1LTAuMDAyLDAuMDA5LTAuMDA0LDAuMDE0LTAuMDA1bDc5LjI2NC0yOS44NjQgICAgYzQuOTc5LDEwLjcwMiwxNC4xNDEsMTguODgzLDI1LjMzNiwyMi42MjRsNS4wNTYtMTUuMmMtNi41OTQtMi4yMjItMTIuMDc0LTYuOTA1LTE1LjI5Ni0xMy4wNzJsMjQuMTItOS4wOCAgICBjNy45MjMsMjIuMjg1LDI5LjAyOSwzNy4xNTksNTIuNjgsMzcuMTI4di0xNmMtMjIuMDM1LTAuMTM1LTM5Ljg2NS0xNy45NjUtNDAtNDBjMC0yLjEyMi0wLjg0NC00LjE1Ni0yLjM0NC01LjY1NmwtMjcuODU2LTI3Ljg1NiAgICBjLTQuMTMtNC4xNjYtNC41OTYtMTAuNzI0LTEuMDk2LTE1LjQzMmMyLjAzOC0yLjc1MSw1LjE4NC00LjQ2NSw4LjYtNC42ODhjMy40MjEtMC4yODgsNi43OSwwLjk3Miw5LjE4NCwzLjQzMmw1NS44NTYsNTUuODU2ICAgIGwxMS4zMTItMTEuMzEybC0xNi41MjgtMTYuNTI4YzQuMDcyLTEyLjE3NywzLjgyMS0yNS4zODYtMC43MTItMzcuNGwtMTQuOTkyLTQwLjA0TDQ1Ny40MjYsMzU3ICAgIGM2LjQwOCw0LjQ5OCwxMC4yMjYsMTEuODM1LDEwLjIzMiwxOS42NjRWNDgwaDE2VjM3Ni42NjRDNDgzLjY0NywzNjMuNTkxLDQ3Ny4yNiwzNTEuMzQ2LDQ2Ni41NDYsMzQzLjg1NnogTTQwOS4wOSwzNjkuMzYgICAgYzIuMjEsNS44NzQsMy4wMSwxMi4xODQsMi4zMzYsMTguNDI0bC0yNS45NjgtMjUuOTZjLTEwLjcxOS0xMC45OTMtMjguMzE5LTExLjIxNC0zOS4zMTItMC40OTYgICAgYy0xMC45OTMsMTAuNzE5LTExLjIxNCwyOC4zMTktMC40OTYsMzkuMzEyYzAuMTYzLDAuMTY3LDAuMzI4LDAuMzMzLDAuNDk2LDAuNDk2bDI1LjMwNCwyNS4zMDRsLTExMy4wNTYsNDIuNTg0bC0xNS4yNDgtNDAuNzQ0ICAgIGMtMC45NDktMi41MzItMy4xMTMtNC40MTItNS43NTItNWMtMC41Ny0wLjEyNi0xLjE1Mi0wLjE5MS0xLjczNi0wLjE5MmMtMi4wOTMtMC4wMDItNC4xMDMsMC44MTctNS42LDIuMjggICAgYy0xMi44NDYsMTIuMTM3LTMzLjEsMTEuNTYyLTQ1LjIzNy0xLjI4NWMtMTIuMTM3LTEyLjg0Ni0xMS41NjItMzMuMSwxLjI4NS00NS4yMzdjMy4wMjItMi44NTUsNi41Ny01LjA5MywxMC40NDgtNi41OTEgICAgYzIuNTQ3LTAuOTQ0LDUuMjA3LTEuNTQ2LDcuOTEyLTEuNzkyYzEuNjY0LTAuMjA0LDMuMzQyLTAuMjYsNS4wMTYtMC4xNjhjNC40MTIsMC4yMzEsOC4xNzctMy4xNTgsOC40MDgtNy41NyAgICBjMC4wNTgtMS4wOTctMC4xMTEtMi4xOTQtMC40OTYtMy4yMjJsLTIwLjg5Ni01NS43OTJsMzguMDk2LTE0LjM0NGM1LDI2LjAzNCwzMC4xNTgsNDMuMDg1LDU2LjE5MiwzOC4wODYgICAgYzIuNjYtMC41MTEsNS4yNzItMS4yNDYsNy44MDgtMi4xOThjMjQuODA3LTkuNDEyLDM3LjM0Ni0zNy4wOTksMjguMDU2LTYxLjk1MmMtMC43MzYtMS45NjgtMS40LTMuNTI4LTIuMDQtNC45MmwzNi44LTE2LjI5NiAgICBMNDA5LjA5LDM2OS4zNnoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
            </div>
            <div class="col-sm-9">
                <h4>Módulo não incluído na licença.</h4>
                <p class="text-muted">
                    Esta ferramenta permite-lhe a emissão de automáticos para os seus envios.
                    <br/>
                    A fatura será emitida automáticamente após o pagamento.
                </p>
                <a href="mailto:info@quickbox.pt?subject=Pedido de informação sobre módulo&body=Pretendia conhecer mais detalhes e valor para o módulo pagamentos automáticos."
                   class="btn btn-xs btn-primary">
                    <i class="fas fa-envelope"></i> Pedir informações
                </a>
            </div>
        </div>
    @else
    <div class="row row-5">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="amount-value m-t-15">
                <h2 class="m-0 text-center">
                    <span>{{ money(@$total) }}</span>{{ Setting::get('app_currency') }}
                    <button type="button" class="btn btn-xs btn-default btn-edit-amount">
                        <i class="fas fa-pencil-alt" style="font-size: 14px"></i>
                    </button>
                </h2>
                <div class="input-group input-group-lg input-edit-amount" style="display: none">
                    {{ Form::text('total', @$total ? $total : 0.00, ['class' => 'form-control input-lg text-center decimal', 'style' => 'border-right:none']) }}
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default btn-save-amount" style="background: transparent; border-left-color: transparent;"><i class="fas fa-check"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row row-5 payment-content" id="mb">
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('total', 'Enviar por SMS') }}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-mobile-alt"></i></div>
                    {{ Form::text('mb_phone', $phone, ['class' => 'form-control decimal']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group">
                {{ Form::label('email', 'Enviar referência por e-mail') }}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-envelope"></i></div>
                    {{ Form::email('mb_email', $email, ['class' => 'form-control email']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="row row-5 payment-content" id="mbway" style="display: none">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="form-group form-group-lg">
                {{ Form::label('total', 'Número de telemóvel para pagamento') }}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-mobile-alt"></i> +351</div>
                    {{ Form::text('mbw_phone', $phone, ['class' => 'form-control phone']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <p class="text-center">
                Vamos enviar uma notificação através da aplicação MB WAY para o telemóvel que indicou para confirmação do pagamento.
            </p>
        </div>
    </div>
    <div class="row row-5 payment-content" id="visa" style="display: none">
        <div class="col-sm-12">
            <div class="form-group form-group-lg">
                {{ Form::label('email', 'Indique o e-mail para envio dos dados de pagamento') }}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-envelope"></i></div>
                    {{ Form::text('visa_email', $phone, ['class' => 'form-control decimal']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <p class="text-center">
                Vamos enviar uma mensagem para o e-mail que indicou com as informações necessárias para concluír o pagamento.
            </p>
        </div>
    </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if(hasModule('gateway_payments'))
    <button type="button" class="btn btn-primary btn-submit" data-loading-text="A gerar pagamento...">
        <i class="fas fa-check"></i> Gerar Pagamento
    </button>
    @endif
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());

    $('.btn-edit-amount').on('click', function () {
        $(this).closest('.amount-value').find('h2').hide();
        $(this).closest('.amount-value').find('.input-edit-amount').show();
    })

    $('.btn-save-amount').on('click', function () {
        var value = parseFloat($(this).closest('.amount-value').find('input').val());
        value = value.toFixed(2);

        $(this).closest('.amount-value').find('h2 span').html(value)
        $(this).closest('.amount-value').find('h2').show();
        $(this).closest('.amount-value').find('.input-edit-amount').hide();
    })

    $('[name="payment_method"]').on('change', function(){
        var opt = $(this).val();
        $('.payment-content').hide()
        $('#' + opt).show();
    })

    $('.payment-form .btn-submit').on('click', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('.btn-submit');
        $submitBtn.button('loading');
        $submitBtn.button('reset');

        Growl.error('Não tem configurado nenhum gateway de pagamentos.');
        /*
        $btn.button('loading');
        $.post($(this).attr('action'), $form.serialize(), function (data) {
            if(data.result) {
                $('').html(data.html)
            } else {
                Growl.error(data.feedback)
            }
        }).error(function () {
            Growl.error500();
        }).always(function(){
            $btn.button('reset');
        })*/


    })
</script>
