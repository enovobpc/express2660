<?php
$modalToken = str_random(10);
?>
{{ Form::open(['route' => 'admin.logistic.products.stock.store', 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Adicionar stock')</h4>
</div>
<div class="modal-body {{ $modalToken }}">

    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('', __('Adicionar stock ao produto...')) }}
                <div class="input-group">
                    <div class="input-group-addon">
                        <img style="height: 16px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                    </div>
                    {{ Form::select('product_id', @$product->exists ? [$product->id => $product->name] : [], null, ['class' => 'form-control select2', 'data-placeholder' => __('Procurar um artigo...')]) }}
                </div>
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            <div style="overflow-y: auto; max-height: 250px;">
                <table class="table table-condensed">
                    <tr>
                        <th>@trans('Localização')</th>
                        <th class="w-80px">@trans('Stock')</th>
                        {{--<th class="w-80px">Alocado</th>--}}
                    </tr>
                    @for($i=0; $i<10 ; $i++)
                    <tr>
                        <td>
                            {{ Form::select('location[]', ['' => ''] + $locations, null, ['class' => 'form-control location-field select2']) }}
                        </td>
                        <td>
                            {{ Form::text('qty[]', null, ['class' => 'form-control number text-center']) }}
                        </td>
                        {{--<td>
                            {{ Form::text('allocated[]', null, ['class' => 'form-control number']) }}
                        </td>--}}
                    </tr>
                    @endfor
                </table>
            </div>
        </div>
    </div>
    {{--<div style="background: #f2f2f2;
    margin: -15px -15px -16px;
    padding: 15px;
    border-bottom: 1px solid #ddd;">
        <div class="row row-5">
            <div class="col-sm-12">
                <div class="form-group-lg">
                    {{ Form::label('', 'Procure o artigo a editar') }}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <img style="height: 22px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                        </div>
                        {{ Form::text('product_search', null, ['class' => 'form-control', 'placeholder' => 'SKU ou Código Barras do artigo...']) }}
                    </div>
                </div>
                <div class="text-center p-t-20 loading" style="display: none">
                    <i class="fas fa-spin fa-circle-notch"></i> A carregar artigo...
                </div>
            </div>
        </div>
    </div>--}}
    {{--@if(!$products->isEmpty())
        <div class="sp-25"></div>
        @foreach($products as $product)
            <div class="row row-5">
                <div class="col-sm-12">
                    <h4 class="m-b-0 bold lh-1-1">
                        {{ @$product->name }}<br/>
                        @if(@$product->lote || @$product->serial_no)
                        <small>{{ @$product->sku }} &nbsp;&bull;&nbsp;{{ @$product->lote }}{{ @$product->serial_no }}</small>
                        @else
                        <small>{{ @$product->sku }}</small>
                        @endif
                    </h4>
                </div>
            </div>
            <div class="sp-15"></div>
            <div class="row row-5">
                <div class="col-sm-12">
                    <table class="table table-condensed m-b-0">
                        <tr class="bg-gray-light">
                            <th>Localização</th>
                            <th class="w-80px text-center">Qtd Atual</th>
                            <th class="w-80px text-center">Qtd Real</th>
                        </tr>
                        @foreach($product->locations as $location)
                            <tr>
                                <td class="vertical-align-middle" style="line-height: 14px">
                                    <b>{{ @$location->code }}</b>
                                    <br/><small class="text-muted">{{ @$location->warehouse->name }}</small>
                                </td>
                                <td class="vertical-align-middle text-center bold">{{ @$location->pivot->stock }}</td>
                                <td style="padding-left: 0;">{{ Form::text('qty['.@$location->pivot->barcode.']', null, ['class' => 'form-control print-qty text-center', 'maxlength' => 5, 'autocomplete' => 'off']) }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endforeach
    @endif--}}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>

    var modalToken = ".modal .{{ $modalToken }}";

    $(modalToken + " .select2").select2(Init.select2());
    $(modalToken + " select[name=product_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.product.select2') }}")
    });


    $(modalToken).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });



    $('button[type="submit"]').on('click', function () {
        $(this).closest('form').submit();
        $('#modal-remote').modal('hide');
    })

</script>

