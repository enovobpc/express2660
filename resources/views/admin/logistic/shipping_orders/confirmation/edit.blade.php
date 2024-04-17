<?php
    $modalToken = str_random(10);
?>
{{ Form::model($shippingOrder, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Confirmação pedido #'){{ $shippingOrder->code }}</h4>
</div>
<div class="modal-body {{ $modalToken }}">
    <div style="overflow: hidden; margin: -15px -15px 15px -15px">
    <div class="mtop-header">
    <div class="row row-5">
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('customer_id', __('Cliente')) }}
                <p>
                    {{ @$shippingOrder->customer->code . ' ' .  @$shippingOrder->customer->name }}
                </p>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('document', __('Referência')) }}
                <p>
                    {{ @$shippingOrder->document }}
                </p>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('', __('Data')) }}
                <p>
                    {{ @$shippingOrder->date }}
                </p>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('', __('Estado')) }}
                <p>
                    <span class="label" style="background: {{ @$shippingOrder->status->color  }}">
                        {{ @$shippingOrder->status->name }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="search-bar">
            <div class="form-group m-b-0" style="margin-bottom: -1px">
                <div class="input-group">
                    <div class="loading" style="display: none">
                        <i class="fas fa-spin fa-circle-notch"></i>
                    </div>
                    <div class="input-group-addon">
                        <img style="height: 22px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                    </div>
                    {{ Form::text('barcode', null, ['class' => 'form-control', 'placeholder' => __('Código de barras, SKU ou designação...')]) }}
                </div>
            </div>
            </div>
            <div class="confirmation-content">
                @include('admin.logistic.shipping_orders.partials.confirmation_content')
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="button" class="btn btn-default btn-save">@trans('Gravar')</button>
    <button type="button" class="btn btn-primary btn-conclude"><i class="fas fa-check"></i> @trans('Finalizar')</button>
</div>
{{ Form::hidden('conclude', 0) }}
{{ Form::close() }}

<div class="modal" id="modal-choose-location">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Selecionar Localização</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-t-0 bold">Localização esperada: <b class="target-location"></b></h4>
                <div class="form-group-lg">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <img style="height: 22px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                        </div>
                        {{ Form::text('location_barcode', null, ['class' => 'form-control', 'placeholder' => 'Código da localização...']) }}
                    </div>
                </div>
                <hr style="margin:  10px 0 5px 0"/>
                <h4>Ou selecione outra localização:</h4>
                <ul class="list-unstyled available-locations">
                </ul>
                <p class="feedback text-red m-t-10 m-b-0" style="display: none">
                    <i class="fas fa-exclamation-circle"></i> <span>Localização inválida. Deve retirar da localização <b>00-01-01</b></span>
                </p>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Cancelar</button>
                    <button type="button" class="btn btn-default" data-answer="1">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal-alert">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notificação</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0"><b></b></h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal-conclude">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Finalizar picking</h4>
            </div>
            <div class="modal-body">
                <div class="msg-dft-message">
                    <h4 class="m-t-0 m-b-10"><b>Confirma a finalização do picking?</b></h4>
                    <p>Após concluir o picking não será possível editar de novo.</p>
                </div>
                <div class="msg-qty-unsatisfied" style="display: none">
                    <h4 class="m-t-0 m-b-10"><b>O pedido não se encontra satisfeito.<br/>Pretende finalizar e expedir o pedido parcialmente?</b></h4>
                    <p>Existem ainda artigos em falta no pedido. Após concluir o picking não será possível editar de novo.</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Cancelar</button>
                    <button type="button" class="btn btn-default" data-answer="1">Concluir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .search-bar {
        padding: 3px 3px 4px;
        background: #adb0b5;
        border-radius: 3px 3px 0 0;
        margin-bottom: -1px;
        position: relative;
        z-index: 1;
    }

    .search-bar input {
        font-size: 15px;
        height: 36px;
        padding: 8px;
        border-left: 0;
        border: none;
    }

    .search-bar .input-group-addon {
        border: none;
    }

    .search-bar .loading {
        position: absolute;
        right: 0;
        z-index: 3;
        font-size: 18px;
        padding: 9px;
        color: #777;
    }

    .rw-red {
        background: rgba(255, 0, 0, 0.07);
        border-bottom-color: #FF0000;
    }

    .rw-green {
        background: #ceffc1;
    }

    .rw-red td {
        border-bottom-color: #FF0000;
    }

    .confirmation-content {
        border: 1px solid #ddd;
        min-height: 250px;
        overflow-y: auto;
        border-radius: 0 0 3px 3px;
    }

    .mtop-header {
        background: #f9f9f9;
        border-bottom: 1px solid #ccc;
        margin: 0 0 2px 0;
        padding: 10px 20px;
        box-shadow: 0 0px 3px #ccc;
    }

    .available-locations li {
        padding: 10px 10px;
        font-size: 18px;
        border: 1px solid #ccc;
        margin-bottom: 4px;
        border-radius: 4px;
        cursor: pointer;
    }

    .available-locations li:hover {
        border-color: #777;
        background: #eee;
    }

    .available-locations li.active {
        border-color: #738ac7;
        background: #ccecff;
    }

    .qty-fld {
        height: 23px;
        margin: -3px 0;
        padding: 0;
        text-align: center;
    }

    .rw-red .qty-fld {
        color: red;
        border-color: #ff0000;
    }

    .rw-green .qty-fld {
        color: #4bb200;
        border-color: #4bb200;
    }
</style>

<script>
    var modalToken = ".modal .{{ $modalToken }}";
    $('.form-confirmation .datepicker').datepicker(Init.datepicker());
    $('.form-confirmation .select2').select2(Init.select2());

    $(modalToken).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $(modalToken+' [name=barcode]').on('change', function(e) {
        e.preventDefault();
        searchBarcode($(this));
    })

    $(modalToken+' [name=barcode]').on('keyup', function (e) {
        e.preventDefault();
        if(e.keyCode == 13) {
            $(this).trigger('change');
        }
    });

    $(modalToken+' .qty-fld').on('change', function (e) {
        var $tr = $(this).closest('tr');
        var qty = $(this).closest('tr').data('qty');
        var sat = $(this).val();

        if(sat > qty) {
            $tr.removeClass('rw-green').addClass('rw-red');
            Growl.error('Quantidade máxima permitida: ' + qty);
            $(this).val($tr.data('satisfied'))
        } else if(sat <= 0) {
            $tr.removeClass('rw-green').addClass('rw-red');
            Growl.error('A quantidade tem de ser superior a 0');
            $(this).val($tr.data('satisfied'))
        }else if(sat == qty) {
            $tr.removeClass('rw-red').addClass('rw-green');
            $tr.data('satisfied', sat)
        } else {
            $tr.removeClass('rw-green').addClass('rw-red');
            $tr.data('satisfied', sat)
        }
        
        
        // /**
        //  * submete quando as linhas encontram-se a verde
        // */
        // if($('.rw-red').length == 0){
        //     $('.modal [name="conclude"]').val(1)
        //     $('.form-confirmation').submit()
        // } 
        
    });


    function searchBarcode(obj) {

        var barcode = obj.val();

        $('.search-bar .loading').show()
        $.post('{{ route('admin.logistic.shipping-orders.confirmation.search.barcode', $shippingOrder->id) }}', {barcode:barcode}, function (data) {
            if(data.result) {

                if(data.singleLocation) {
                    var exists = false;
                    $(modalToken+' .confirmation-content tr').each(function () {
                        if($(this).data('product') == data.product && $(this).data('location') == data.location) {
                            exists = true;
                            var totalQty  = parseInt($(this).data('qty'));
                            var satisfied = parseInt($(this).data('satisfied'))
                            satisfied     = isNaN(satisfied) ? 0 : satisfied;

                            if(satisfied < totalQty) {
                                satisfied = satisfied + 1;
                                $(this).data('satisfied', satisfied);
                                $(this).find('.qty-satisfied').html(satisfied)
                                $(this).find('.qty-fld').val(satisfied);

                                if (satisfied == totalQty) { //atingiu o máximo
                                    $(this).removeClass('rw-red').addClass('rw-green');
                                    $(this).find('.qty-satisfied').removeClass('text-red').addClass('text-green')
                                }

                                Notifier.soundOk();
                            } else {
                                $('#modal-alert').closest('.modal').addClass('in').show();
                                $('#modal-alert h4 b').html('O artigo já se encontra satisfeito na totalidade.')
                                Notifier.soundWarning();
                            }
                            return false;
                        }
                    })

                    if(!exists) {
                        $('#modal-alert').closest('.modal').addClass('in').show();
                        $('#modal-alert h4 b').html('Artigo não solicitado no pedido.');
                        Notifier.soundError();
                    }
                } else {

                    var locationCode = '';
                    var allSatisfied = true;
                    $(modalToken+' .confirmation-content tr').each(function () {
                        if($(this).data('product') == data.product) {
                            locationCode = $(this).data('location-code')
                            if(!$(this).hasClass('rw-green')){
                                allSatisfied = false;
                            }
                        }
                    });

                    if(allSatisfied) {
                        $('#modal-alert').closest('.modal').addClass('in').show();
                        $('#modal-alert h4 b').html('O artigo já se encontra satisfeito na totalidade.')
                    } else {
                        html = '';
                        $.each(data.locations, function(key, value) {
                            html+= '<li data-location="'+value.id+'" data-product="'+value.product+'" data-barcode="'+value.barcode+'" data-location-code="'+value.location_barcode+'">' +
                                '<b>'+ value.code+'</b> <small class="text-center text-muted fs-14">&nbsp;&bull; '+value.qty+' unidades</small>' +
                                '</li>';
                        });

                        $('#modal-choose-location ul').html(html)
                        $('#modal-choose-location .target-location').html(locationCode)
                        $('#modal-choose-location').closest('.modal').addClass('in').show();
                        $('#modal-choose-location [name="location_barcode"]').focus()
                    }

                    Notifier.soundWarning();
                }
                
                /**
                 * submete quando as linhas encontram-se a verde
                */
                    if($('.rw-red').length == 0){
                        $('.modal [name="conclude"]').val(1)
                        $('.form-confirmation').submit()
                    } 
                
            } else {
                Growl.error(data.feedback)
                Notifier.soundError();
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $('.search-bar .loading').hide()
            obj.val('').focus();
        })
    }

    $(document).on('click', '#modal-choose-location ul li', function(){
        $('#modal-choose-location ul li').removeClass('active');
        $(this).addClass('active')
    })

    $('[name="location_barcode"]').on('change', function() {
        var barcode = $(this).val();
        $(this).val('');
        $('#modal-choose-location ul li[data-location-code="'+barcode+'"]').trigger('click');
        $('#modal-choose-location [data-answer="1"]').trigger('click');
    })

    $('#modal-alert [data-answer]').on('click', function(){
        $(this).closest('.modal').removeClass('in').hide();
    });

    $('#modal-choose-location [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            var barcode = $(document).find('#modal-choose-location ul li.active').data('barcode');
            //PARA CASO SELECIONE UMA LOCALIZAÇÃO INVÁLIDA
            /*var product = $(document).find('#modal-choose-location ul li.active').data('product');
            var existsBarcode = false;

            $(modalToken+' .table-products [data-product]').each(function () {
                if($(this).data('barcode') == barcode) {
                    existsBarcode = true
                }
            })

            if(!existsBarcode) {
                //remove 1 unidade do artigo e adiciona uma nova linha para esta referência
                var trClone = $(modalToken+' .table-products [data-product]').eq(1).clone();
                trClone.
                $(modalToken+' .table-products tbody').append(trClone);
            }*/

            $(modalToken+' [name=barcode]').val(barcode).trigger('change');
        }
        $(this).closest('.modal').removeClass('in').hide();
    });

    $('#modal-conclude [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            $('.modal [name="conclude"]').val(1);
            $('.form-confirmation').submit()
        }
        $(this).closest('.modal').removeClass('in').hide();
    });

    $('.btn-save').on('click', function (e) {
        e.preventDefault();
        $('.modal [name="conclude"]').val(0);
        $('.form-confirmation').submit()
    })

    $('.btn-conclude').on('click', function (e) {
        e.preventDefault();
        $('#modal-conclude').addClass('in').show();

        if($('tr.rw-red').length > 0) {
            $('#modal-conclude .msg-qty-unsatisfied').show();
            $('#modal-conclude .msg-dft-message').hide();
        } else {
            $('#modal-conclude .msg-qty-unsatisfied').hide();
            $('#modal-conclude .msg-dft-message').show();
        }
    });
    
    $(document).ready(function () {
        $('[name=barcode]').focus();
    });

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-confirmation').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                Growl.success(data.feedback)
                $('#modal-remote-xl, #modal-remote-lg, #modal-remote').modal('hide');
            } else {
                Growl.error(data.feedback)
            }

        }).fail(function () {
            Growl.error500()
        }).always(function(){
            $button.button('reset');
        })
    });
</script>




