{{ Form::open(['route' => array('admin.logistic.products.labels.print', $product->id), 'method' => 'GET', 'target' => '_blank', 'class' => 'form-print-label']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Imprimir Etiquetas')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <h4 class="m-0 bold lh-1-3">
                <small>@trans('Artigo')</small><br/>
                {{ $product->sku }} - {{ $product->name }}
            </h4>
        </div>
    </div>
    <div class="sp-25"></div>
    <div class="row row-5">
        <div class="col-sm-12">
            <table class="table table-condensed m-b-0">
                <tr class="bg-gray-light">
                    <th class="w-140px">@trans('Código de Barras')</th>
                    <th>@trans('Localização')</th>
                    <th style="width: 20%">@trans('Qtd a Imprimir')</th>
                </tr>
                @foreach($locations as $location)
                    <tr>
                        <td class="vertical-align-middle">{{ $location->barcode }}</td>
                        <td class="vertical-align-middle">{{ @$location->location->code }} <small>({{ @$location->location->warehouse->name }})</small></td>
                        <td style="padding-left: 0;">{{ Form::text('qty['.$location->barcode.']', null, ['class' => 'form-control print-qty text-center', 'maxlength' => 4, 'autocomplete' => 'off']) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary" disabled><i class="fas fa-print"></i> @trans('Imprimir')</button>
</div>
{{ Form::close() }}
<script>
    $(document).on('keyup', '.print-qty', function(){
        var hasQty = false;

        $('.print-qty').each(function(){
            if ($(this).val() != '') {
                hasQty = true;
            }
        })

        if(hasQty) {
            $('button[type="submit"]').prop('disabled', false);
        } else {
            $('button[type="submit"]').prop('disabled', true);
        }
    })


    $('button[type="submit"]').on('click', function () {
        $(this).closest('form').submit();
        $('#modal-remote').modal('hide');
    })

</script>

