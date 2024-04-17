{{ Form::model($sms, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="">
        <div class="payment-loading" style="display: none">
            <div class="text-center">
                <h4>
                    <i class="fas fa-spin fa-circle-notch fs-30"></i>
                    <br/>
                    A gerar dados de pagamento...
                    <br/>
                    <small>Poderá demorar alguns instantes.</small>
                </h4>
            </div>
        </div>
        <div class="packs-selection">
            <table class="table">
                <tr>
                    <td class="bold bg-gray-light"></td>
                    <td class="bold bg-gray-light">Pacote</td>
                    <td class="bold bg-gray-light">Preço/SMS</td>
                    <td class="bold bg-gray-light">Subtotal</td>
                    <td class="bold bg-gray-light">Total (IVA {{ money($packsOptions->first()->vat, '%') }})</td>
                </tr>
                @foreach($packsOptions as $pack)
                <?php
                    $subtotal = (float) $pack->price_un * (float) $pack->qty;
                    $vat = (float) $pack->vat;
                    $total = $subtotal * (1 + ($vat/100));
                ?>
                <tr class="fs-14 {{ $pack->qty == '1000' ? 'rw-selected' : '' }}">
                    <td>
                        {{ Form::radio('pack_id', $pack->id, $pack->qty == '1000' ? true : false) }}
                    </td>
                    <td class="vertical-align-middle">
                        Pacote <b>{{ number_format($pack->qty, 0, '','.') }}</b> SMS
                    </td>
                    <td class="vertical-align-middle text-center">{{ money($pack->price_un, '€', 3) }}</td>
                    <td class="vertical-align-middle text-center">{{ money($subtotal, '€') }}</td>
                    <td class="vertical-align-middle text-center bold fs-14">{{ money($total, '€') }}</td>
                </tr>
                @endforeach
            </table>
            <div class="row">
                <div class="col-sm-3">
                    <p class="m-0 m-l-5">
                        <img height="35" src="data:image/png;base64,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"/>
                    </p>
                </div>
                <div class="col-sm-9">
                    <p class="text-blue">
                        Após clicar em "comprar" será gerada a respetiva referência multibanco.
                        A fatura-recibo será emitida após pagamento.
                    </p>
                </div>
                </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button class="btn btn-primary btn-submit"><i class="fas fa-shopping-cart"></i> Comprar</button>
</div>
{{ Form::close() }}

<style>

    .modal tr {
        cursor: pointer !important;
    }

    .modal tr:hover {
        background-color: #f2f2f2 !important;
    }

    .rw-selected {
        background: #fffded !important;
        border-left: 1px solid #999;
        border-right: 1px solid #999;
    }

    .rw-selected td {
        border-top: 1px solid #999 !important;
        border-bottom: 1px solid #999;
    }
</style>
<script>
    $(document).on('change', '[name="buy_sms"]', function() {
        $('tr.rw-selected').removeClass('rw-selected');
        $(this).closest('tr').addClass('rw-selected')
    })

    $(document).on('click', 'tr', function() {
        $('tr.rw-selected').removeClass('rw-selected');
        $(this).addClass('rw-selected');
        $(this).find('[name="pack_id"]').prop('checked', true)
    })

    $('.modal form').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit],.btn-submit');

        $('.payment-loading').show();
        $('.packs-selection').hide();
        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {
            if(data.result) {
                $('.payment-loading').html(data.html);
                $('#modal-remote .modal-dialog').addClass('modal-xs')
                $('#modal-remote .modal-dialog .btn-submit').hide();
                oTablePacks.draw();
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $button.button('reset');
        })
    })

    $('#modal-remote').on('hidden.bs.modal', function () {
        $('#modal-remote').find('.modal-dialog').removeClass('modal-xs')
    })
</script>
