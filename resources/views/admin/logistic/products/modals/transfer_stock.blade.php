{{ Form::open(['route' => array('admin.logistic.products.stock.transfer.save', $stock->product_id, $stock->location_id), 'method' => 'POST', 'class' => 'form-ajax']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Transferir Stock')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-10">
            <h4 class="m-0 bold lh-1-3">
                <small>@trans('Localização de Origem')</small><br/>
                {{ $stock->location->code }} ({{ $stock->location->warehouse->name }})
            </h4>
        </div>
        <div class="col-sm-2">
            <h4 class="logistic-stock-label">
                <small>@trans('Stock')</small><br/>
                {{ $stock->stock }} @trans('UN')
            </h4>
        </div>
    </div>
    <div class="sp-25"></div>
    <div class="row row-5">
        <div class="col-sm-12">
            <table class="table table-condensed m-b-0" id="transfer-locations">
                <tr class="bg-gray-light">
                    <th style="width: 20%">@trans('Qtd a Transferir')</th>
                    <th>@trans('Nova Localização')</th>
                </tr>
                @for($i=1 ; $i<=3 ; $i++)
                    <tr>
                        <td style="padding-left: 0;">{{ Form::text('qty[]', null, ['class' => 'form-control number', 'maxlength' => 4, $i == 1 ? 'required' : '']) }}</td>
                        <td>{{ Form::select('location[]', ['' => ''] + $locations ,null, ['class' => 'form-control select2', $i == 1 ? 'required' : '' ]) }}</td>
                    </tr>
                @endfor
            </table>
            <button type="button" class="btn btn-xs btn-default" data-toggle="add-location" data-target="#transfer-locations">
                <i class="fas fa-plus"></i> @trans('Outra Localização')
            </button>
        </div>
    </div>
</div>
<div class="modal-footer">
    <p class="modal-feedback text-red m-t-5 m-b-0 pull-left" style="display: none;"></p>
    {{ Form::hidden('max_stock', $stock->stock) }}
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary" disabled>@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());

    $(document).on('change', '[name="qty[]"]', function(){

        var total = 0;
        var totalMax = parseInt($('[name="max_stock"]').val());

        $('[name="qty[]"]').each(function(){
            total = total + (parseInt($(this).val()) || 0);
        });

        if(total > totalMax) {
            $('[type=submit]').prop('disabled', true);
            $('.modal-feedback').html('<i class="fas fa-exclamation-triangle"></i> Apenas pode transferir desta localização {{ $stock->stock }} unidades.').show();
        } else if(total == 0 || total == '') {
            $('[type=submit]').prop('disabled', true);
            $('.modal-feedback').hide();
        } else {
            $('[type=submit]').prop('disabled', false);
            $('.modal-feedback').hide();
        }
    })

    $('[data-toggle="add-location"]').click(function () {
        var $target = $($(this).data('target'));
        var $clone = $target.find('tr:last-child').clone();
        $clone.find('input').val('');
        $clone.find('.select2-container').remove();
        $clone.find('.select2').select2(Init.select2());

        $target.append($clone);
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-ajax').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote').modal('hide');
                $('#locations-table').replaceWith(data.html)
            } else {
                $('.modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).error(function () {
            $('.modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function(){
            $button.button('reset');
        })
    });
</script>

