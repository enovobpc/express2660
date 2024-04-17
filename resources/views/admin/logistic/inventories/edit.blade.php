<?php
$hash = str_random();
?>
{{ Form::model($inventory, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $inventory->allow_edit ? $action : __('Inventário #').$inventory->code }}</h4>
</div>
<div class="mtop-header modal-{{ $hash }}">
    <div class="row row-5">
        <div class="col-sm-1" style="width: 10%">
            <div class="form-group {{ $inventory->allow_edit ? 'is-required' : '' }}">
                {{ Form::label('code', __('Código')) }}
                {{ Form::text('code', null, ['class' => 'form-control', 'readonly']) }}
            </div>
        </div>
        <div class="col-sm-6" style="width: 48%">
            <div class="form-group {{ $inventory->allow_edit ? 'is-required' : '' }}">
                {{ Form::label('description', __('Descrição')) }}
                {{ Form::text('description', null, ['class' => 'form-control', 'required', $inventory->allow_edit ? '' : 'readonly']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('customer_id', __('Cliente')) }}
                @if($inventory->allow_edit)
                    {{ Form::select('customer_id', [$inventory->customer_id => @$inventory->customer->name], null, ['class' => 'form-control', 'data-placeholder' => __('Qualquer cliente')]) }}
                @else
                    {{ Form::text('customer_id', @$inventory->customer->name, ['class' => 'form-control', 'readonly']) }}
                @endif
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group {{ $inventory->allow_edit ? 'is-required' : '' }}">
                {{ Form::label('date', __('Data')) }}
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    {{ Form::text('date', $inventory->exists ? null : date('Y-m-d'), ['class' => 'form-control datepicker', 'required', $inventory->allow_edit ? '' : 'readonly']) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-body modal-{{ $hash }}">
    @if($inventory->allow_edit)
    <div class="row row-5">
        <div class="col-sm-12">
            <div style="padding: 5px;background: #ccc;border-radius: 3px 3px 0 0;margin-bottom: -10px;">
                <div class="row row-5">
                    <div class="col-sm-9">
                        <div class="input-group">
                            <div class="input-group-addon" style="padding: 5px 8px;border-right: 0;">
                                <img style="height: 22px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K">
                            </div>
                            {{ Form::text('add_sku', null, ['class' => 'form-control search-product', 'placeholder' => __('Procure um artigo...'), 'style' => 'border-left: none;margin-left: 0px;']) }}
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-block btn-success btn-add-product">@trans('Adicionar')</button>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-block btn-default btn-import">
                            <i class="fas fa-list-ul"></i> @trans('Importar Artigos')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="products-panel">
        @include('admin.logistic.inventories.partials.table')
    </div>
    {{ Form::hidden('id', $inventory->id) }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    @if($inventory->allow_edit)
    <button type="button" class="btn btn-default btn-save" data-loading-text="@trans('A guardar...')">@trans('Guardar')</button>
    <button type="button" class="btn btn-primary btn-conclude" data-loading-text="@trans('A guardar...')">@trans('Finalizar')</button>
    @endif
</div>
{{ Form::hidden('conclude', 0) }}
{{ Form::close() }}

@if($inventory->allow_edit)
<div class="modal" id="modal-conclude">
    <div class="modal-dialog modal-xs">

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Finalizar inventário</h4>
            </div>
            <div class="modal-body">
                <div class="msg-dft-message">
                    <h4 class="m-t-0 m-b-10"><b>Confirma a finalização do inventário?</b></h4>
                    <p>Após concluir o inventário não será possível editar de novo.</p>
                    <p class="text-yellow"><i class="fas fa-exclamation-triangle"></i> O stock de todos os artigos serão corrigidos para os indicados.</p>
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
<div class="modal modal-{{ $hash }}" id="modal-import">
    <div class="modal-dialog modal-lg">
        {{ Form::open(['route' => array('admin.logistic.inventories.items.import'), 'method' => 'POST', 'class' => 'form-preview-import']) }}
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Importar produtos</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-5">
                        <div class="row row-5">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {{ Form::label('import_customer', 'Cliente') }}
                                    {{ Form::select('import_customer', ['' => ''], null, ['class' => 'form-control select2', 'data-placeholder' => 'Todos']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('import_warehouse', 'Armazém') }}
                                    {{ Form::select('import_warehouse', array('' => 'Todos') + $warehouses, null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('import_status', 'Estado') }}
                                    {{ Form::select('import_status', array('' => 'Todos') + trans('admin/logistic.products.status'), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('import_serial_no', 'Nº Série') }}
                                    {{ Form::select('import_serial_no', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('import_lote', 'Lote') }}
                                    {{ Form::select('import_lote', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('import_date_unity', 'Tipo Data') }}
                                    {{ Form::select('date_unity', ['' => 'Últ. movimento', '3' => 'Data Validade', '4' => 'Data registo'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {{ Form::label('import_date', 'Data') }}
                                    <div class="input-group w-240px">
                                        {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                        <span class="input-group-addon">até</span>
                                        {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        {{ Form::label('', 'Produtos selecionados') }} (<span class="import-preview-counter">0</span>) <i class="fas fa-spin fa-circle-notch import-preview-loading" style="display: none"></i>
                        <div class="import-preview" style="border: 1px solid #999; border-radius: 3px; height: 170px; overflow: auto;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Cancelar</button>
                    <button type="button" class="btn btn-default" data-answer="1">Importar</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
@endif

<style>
    .mtop-header {
        background: #f9f9f9;
        border-bottom: 1px solid #ccc;
        margin: 0 0 2px 0;
        padding: 10px 20px;
        box-shadow: 0 0px 3px #ccc;
    }

    .products-panel {
        height: 370px;
        border: 1px solid #ccc;
        margin-top: 5px;
        overflow-y: auto;
    }

    .products-panel td {
        vertical-align: middle !important;
    }

    .products-panel tr.row-green {
        background: #33ff0052;
    }

    .products-panel tr.row-red {
        background: #ff00002e;
    }

    .products-panel tr.row-yellow {
        background: #ffeb0024;
    }

    .products-panel tr.border-bold td {
        border-top: 2px solid #000;
    }

    .bg-dark {
        color: #fff;
        background-color: #54565a !important;
    }
</style>

@if($inventory->allow_edit)
<script>
    var MODAL = '.modal-{{ $hash }}';

    $(MODAL+ ' .datepicker').datepicker(Init.datepicker());

    $(MODAL).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    var select2 = $(MODAL+ ' .select2').select2(Init.select2());

    var autocompleteOptions = {
        serviceUrl: '{{ route('admin.logistic.products.search.product') }}',
        onSearchStart: function () {
            $(this).closest('tr').find('[name="product_id[]"]').val('');
            $(this).closest('tr').find('[name="barcode[]"]').val('');
            $(this).closest('tr').find('[name="sku[]"]').val('');
        },
        onSelect: function (suggestion) {
            $(this).closest('tr').find('[name="product_id[]"]').val(suggestion.data);
            $(this).closest('tr').find('[name="barcode[]"]').val(suggestion.barcode);
            $(this).closest('tr').find('[name="sku[]"]').val(suggestion.customerRef);
        },
    };

    $(MODAL + '.search-product').autocomplete(autocompleteOptions);

    $(MODAL + " select[name=customer_id], " + MODAL + " select[name=import_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.customer') }}")
    });

    $(document).on('keypress', MODAL + ' .search-product', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            $(MODAL + ' .btn-add-product').trigger('click');
        }
    })

    //BUTTON IMPORT PRODUCTS
    $(document).on('click', MODAL + ' .btn-import', function (e) {
        e.preventDefault();
        $('#modal-import').addClass('in').show();
    });

    //IMPORT PRODUCTS
    $('#modal-import [data-answer]').on('click', function(){

        if($(this).data('answer') == '1') {
            var description = $(MODAL + " [name=description]").val();
            var date        = $(MODAL + " [name=date]").val();
            var id          = $(MODAL + " [name=id]").val();

            if (description == '') {
                Growl.error('Deve dar uma descrição ao inventário antes de adicionar artigos.')
            } else {
                var $form = $('.form-preview-import');
                var $btn = $(this);
                $btn.button('loading');

                $(MODAL + " .products-panel").html('<h4 class="text-center m-t-150"><i class="fas fa-spin fa-circle-notch"></i> A importar...</h4>')

                $.post($form.attr('action'), $form.serialize()+'&description='+description+'&date='+date+'&id='+id, function (data) {
                    if (data.result) {
                        $(MODAL + " .products-panel").html(data.html)
                        $(MODAL + ' .products-panel .select2').select2(Init.select2())
                        $(MODAL + " [name=id]").val(data.id)
                    } else {
                        Growl.error(data.feedback);
                    }
                }).fail(function () {
                    Growl.error500();
                }).always(function () {
                    $btn.button('reset');
                    $(MODAL + " .import-preview").html('')
                    $(MODAL + " .import-preview-counter").html('0')
                    $('#modal-import input, #modal-import select').val('').trigger('change.select2');
                })
            }
        }

        $(this).closest('.modal').removeClass('in').hide();
    });

    //PREVIEW IMPORT
    $('#modal-import input, #modal-import select').on('change', function(){

        var $loading = $('.import-preview-loading');
        var $form    = $('.form-preview-import');

        $loading.show();
        $.post("{{ route('admin.logistic.inventories.items.import.preview') }}", $form.serialize(), function (data) {
            if (data.result) {
                $(MODAL + " .import-preview").html(data.html)
                $(MODAL + " .import-preview-counter").html(data.counter)
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $loading.hide();
        })
    });


    //ADD NEW PRODUCT
    $(document).on('click', MODAL + ' .btn-add-product', function () {

        var description = $(MODAL + " [name=description]").val();


        if(description == '') {
            Growl.error('Deve dar uma descrição ao inventário antes de adicionar artigos.')
        } else if($(MODAL + " [name=add_sku]").val() != '') {
            var params = {
                sku: $(MODAL + " [name=add_sku]").val(),
                qty: $(MODAL + " [name=add_qty]").val(),
                id:  $(MODAL + " [name=id]").val(),
                desctiption: $(MODAL + " [name=description]").val(),
                date: $(MODAL + " [name=date]").val(),
            }

            var $btn = $(this);
            $btn.button('loading');

            $.post("{{ route('admin.logistic.inventories.items.store') }}", params, function (data) {
                if (data.result) {
                    $(MODAL + " .products-panel").html(data.html)
                    $(MODAL + ' .products-panel .select2').select2(Init.select2())
                    $(MODAL + " [name=id]").val(data.id)
                } else {
                    Growl.error(data.feedback);
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $btn.button('reset');
                $(MODAL + " [name=add_sku]").val('')
            })
        }
    })

    //UPDATE ROW
    $(document).on('change', MODAL + ' .qty-real, ' + MODAL + ' .qty-damaged, ' + MODAL + ' .p-location', function () {

        $tr = $(this).closest('tr');

        var id = $tr.data('id');
        var qtyExisting = $tr.data('qty-existing');
        var qtyDamaged  = $tr.find('.qty-damaged').val();
        var qtyReal     = $tr.find('.qty-real').val();
        var location    = $tr.find('.p-location').val();

        if(qtyExisting == qtyReal) {
            $tr.removeClass('row-yellow').removeClass('row-red').addClass('row-green');
        } else if(qtyReal == 0) {
            $tr.removeClass('row-green').removeClass('row-yellow').addClass('row-red');
        } else {
            $tr.removeClass('row-green').removeClass('row-red').addClass('row-yellow');
        }

        var params = {
            qty_damaged: qtyDamaged,
            qty_real: qtyReal,
            location: location,
            id: id,
        }

        $.post($tr.data('url'), params, function(data){
            if(data.result) {
                $(MODAL + " .products-panel").html(data.html)
            } else {
                Growl.error(data.feedback);
                $tr.find('.qty-real').val(data.qty_real)
            }
        }).fail(function (){
            Growl.error500();
        }).always(function (){})
    })

    $(document).on('click', MODAL + ' .btn-delete-product', function (e) {
        e.preventDefault()
        var $btn = $(this).find('i');
        var $tr  = $(this).closest('tr');
        var url  = $(this).attr('href');

        $btn.removeClass('fa-times').addClass('fa-spin fa-circle-notch');

        $.post(url, function(data){

            if(data.result) {
                $tr.remove();
                Growl.success(data.feedback)
            } else {
                Growl.error(data.feedback)
            }

        }).fail(function(){
            Growl.error500()
        }).always(function(){
            $btn.removeClass('fa-spin fa-circle-notch').addClass('fa-times');
        })
    })


    $('#modal-conclude [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            $('.modal [name="conclude"]').val(1);
            $('.form-inventory').submit()
        }
        $(this).closest('.modal').removeClass('in').hide();
    });

    $('.btn-save').on('click', function (e) {
        e.preventDefault();
        $('.modal [name="conclude"]').val(0);
        $('.form-inventory').submit()
    })

    $('.btn-conclude').on('click', function (e) {
        e.preventDefault();

        var emptyLocations = false
        if($(MODAL + ' .p-location.l-empty').each(function(){
            if($(this).val() == '') {
                emptyLocations = true;
            }
        }))



        if(emptyLocations) {
            Growl.error('Existem artigos sem localização definida.')
        } else {
            $('#modal-conclude').addClass('in').show();
        }
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-inventory').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                Growl.success(data.feedback);
                $('#modal-remote-xl, #modal-remote-lg, #modal-remote').modal('hide');
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function () {
            Growl.error500()
        }).always(function(){
            $button.button('reset');
        })
    });
</script>
@endif