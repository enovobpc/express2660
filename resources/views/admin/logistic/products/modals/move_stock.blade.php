<?php
$modalToken = str_random(10);
?>
{{ Form::open(['route' => 'admin.logistic.products.move.store', 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Transferir stock')</h4>
</div>
<div class="modal-body {{ $modalToken }}">
    <div style="background: #f2f2f2;
    margin: -15px -15px -16px;
    padding: 15px;
    border-bottom: 1px solid #ddd;">
        <div class="row row-5">
            <div class="col-sm-12">
                <div class="form-group-lg">
                    {{ Form::label('', __('Procure o artigo a transferir')) }}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <img style="height: 22px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                        </div>
                        {{ Form::text('product_search', null, ['class' => 'form-control', 'placeholder' => __('SKU ou Código Barras do artigo...')]) }}
                    </div>
                </div>
                <div class="text-center p-t-20 loading" style="display: none">
                    <i class="fas fa-spin fa-circle-notch"></i> @trans('A carregar artigo...')'
                </div>
            </div>
        </div>
    </div>

    @if(@$product->exists)
    <div class="barcode-result-area">
        <div class="sp-25"></div>
        <div class="row">
            <div class="col-sm-12">
                <h4 class="m-t-0 m-b-20 bold lh-1-1">
                    {{ @$product->name }}<br/>
                    @if(@$product->lote || @$product->serial_no)
                        <small>{{ @$product->sku }} &nbsp;&bull;&nbsp;{{ @$product->lote }}{{ @$product->serial_no }}</small>
                    @else
                        <small>{{ @$product->sku }}</small>
                    @endif
                </h4>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group-lg">
                            <label>@trans('Localização Origem') <i class="fas fa-spin fa-circle-notch" style="display: none"></i></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <img style="height: 22px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                                </div>
                                {{ Form::text('move_source', @$selectedSourceLocation->code, ['class' => 'form-control']) }}
                                {{ Form::hidden('move_source_id', @$selectedSourceLocation->id, ['class' => 'fld-id']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group-lg">
                            <label>@trans('Localização Destino') <i class="fas fa-spin fa-circle-notch" style="display: none"></i></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <img style="height: 22px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                                </div>
                                {{ Form::text('move_destination', null, ['class' => 'form-control']) }}
                                {{ Form::hidden('move_destination_id', null, ['class' => 'fld-id']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group-lg">
                    {{ Form::label('', __('Quantidade')) }}
                    <div class="input-group">
                        {{ Form::text('qty', null, ['class' => 'form-control number', 'required']) }}
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif
    {{ Form::hidden('product_id', @$product->id) }}
    {{ Form::hidden('allowed_locations', $sourceLocations) }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    var modalToken = ".modal .{{ $modalToken }}";

    $(modalToken).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $(document).on('change', modalToken + ' [name="product_search"]', function (e) {
        e.preventDefault();
        getProduct()
    })

    $(document).on('keyup',  modalToken + ' [name="product_search"]', function (e) {
        e.preventDefault();
        if(e.keyCode == 13) {
            $(this).trigger('change');
        }
    })

    $(document).ready(function () {
        $(modalToken + ' [name="move_source"]').focus();
        if($(modalToken + ' [name="move_source"]').val() != '') {
            $(modalToken + ' [name="move_source"]').trigger('change');
        }
    })


    $(modalToken + ' [name="move_source"]').on('change', function (e) {
        e.preventDefault();
        $(modalToken + ' [name="move_destination"]').focus();
        getLocation($(this));
    })

    $(modalToken + ' [name="move_source"]').on('keyup', function (e) {
        e.preventDefault();
        if(e.keyCode == 13) {
            $(this).trigger('change');
        }
    })

    $(modalToken + ' [name="move_destination"]').on('change', function (e) {
        e.preventDefault();
        $(modalToken + ' [name="qty"]').focus();
        getLocation($(this));
    })

    $(modalToken + ' [name="move_destination"]').on('keyup', function (e) {
        e.preventDefault();
        if(e.keyCode == 13) {
            $(this).trigger('change');
        }
    })

    function getProduct() {
        var barcode = $('.modal [name="product_search"]').val();

        $('.modal .loading').show();
        $.get('{{ route('admin.logistic.products.move.edit') }}', {barcode:barcode}, function (data) {
            if (data.result) {
                $('#modal-remote .modal-content').html(data.html)
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $('.modal .loading').hide();
        })
    }

    function getLocation(obj) {
        var barcode = obj.val();
        var groupDiv = obj.closest('.form-group-lg');

        groupDiv.find('.fa-spin').show();
        $.post('{{ route('admin.logistic.products.get.location') }}', {barcode:barcode}, function (data) {
            if (data.result) {
                obj.val(data.code)
                groupDiv.find('.fld-id').val(data.id)
            } else {
                obj.val('')
                obj.focus();
                groupDiv.find('.fld-id').val('')
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            groupDiv.find('.fa-spin').hide();

            //verifica localização de origem
            var sourceValue      = $(modalToken + ' [name="move_source"]').val();
            var allowedLocations = $(modalToken + ' [name="allowed_locations"]').val();
            allowedLocations = allowedLocations.split(',')

            if(!allowedLocations.includes(sourceValue)) {
                Growl.error('Localização de origem inválida.');
                $(modalToken + ' [name="move_source"]').val('')
                $(modalToken + ' [name="move_source_id"]').val('')
            }
        })
    }

    $('button[type="submit"]').on('click', function (e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var $btn  = $(this);
        var qty   = $(modalToken + ' [name="qty"]').val();

        if(qty == 0 || qty == '') {
            Growl.error('A quantidade deve ser igual ou superior a 0')
        } else {

            $btn.button('loading');
            $.post($form.attr('action'), $form.serialize(), function (data) {
                if (data.result) {
                    $('#modal-remote').modal('hide');
                    Growl.success(data.feedback);
                } else {
                    Growl.error(data.feedback);
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $btn.button('reset');
            })
        }
    })

</script>

