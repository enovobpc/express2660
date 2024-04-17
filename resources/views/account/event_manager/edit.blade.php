@php
    $isDisable = $event->is_draft == '0' ? "disabled='true'" : ""; 
@endphp
{{ Form::model($event, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        {{-- Type | Event Name | User things Email, Phone, Mobile | Horary | Obs | is_active --}}
        <div class="col-sm-3"  style="border-right: 1px solid #ccc;">
            <div class="form-group is-required">
                {{ Form::label('name', 'Nome do Evento') }}
                {{ Form::text('name', $event->name ?? '', ['class' => 'form-control uppercase select2-container', 'required', $isDisable]) }}
            </div>
            <div class="form-group">
                {{ Form::label('type', 'Tipo') }}
                {{ Form::select('type', ['' => ''] + trans('admin/event-manager.types'), null, ['class' => 'form-control select2 max-w-full', $isDisable]) }}
            </div>
            <div class="form-group input-hours p-r-0">
                {{ Form::label('date', 'Data de duração', ['class' => 'control-label']) }}
                <div class="form-group form-group-sm m-b-5 col-sm-12">
                    <label class="col-sm-1 control-label p-r-5 p-l-0"  data-toggle="tooltip" title="Data de Expedição">
                        <i class="far fa-calendar-alt fs-15 m-t-2 hidden-xs"></i>
                        <span class="visible-xs"><i class="fas fa-calendar-alt"></i> Carga</span>
                    </label>
                    <div class="col-sm-7 p-l-0">
                        {{ Form::text('start_date', empty($event->date) ? date('Y-m-d') : null, ['class' => 'form-control datepicker', $isDisable]) }}
                    </div>
                    <div class="col-sm-4 p-r-0 p-l-5" style="margin-left: -27px;">
                        {{ Form::select('start_hour', ['' => '--:--'] + $hours, null, ['class' => 'form-control select2', $isDisable]) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-15 col-sm-12">
                    <label class="col-sm-1 control-label p-r-5 p-l-0" data-toggle="tooltip" title="Data de Entrega">
                        <i class="far fa-calendar-alt fs-15 m-t-2 hidden-xs"></i>
                        <span class="visible-xs"><i class="fas fa-calendar-alt"></i> Descarga</span>
                    </label>
                    <div class="col-sm-7 p-l-0">
                        {{ Form::text('end_date', empty($event->end_date) ? date('Y-m-d') : @$event->end_date->format('Y-m-d'), ['class' => 'form-control datepicker', $isDisable]) }}
                    </div>
                    <div class="col-sm-4 p-r-0 p-l-5" style="margin-left: -27px;">
                        {{ Form::select('end_hour', ['' => '--:--'] + $hours, empty($event->end_date) ? null : @$event->end_date->format('H:i'), ['class' => 'form-control select2', $isDisable]) }}
                    </div>
                </div>
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('observation', 'Observações') }}
                {{ Form::textArea('observation', $event->observation ?? '', ['class' => 'form-control select2-container', 'rows' => 2, $isDisable]) }}
            </div>  
        </div>
        <div class="col-sm-9">
            <div class="col-sm-12">
                <div class="row row-5">
                    <div class="col-sm-2">
                        <div class="form-group m-0">
                            {{ Form::label('sku_product', 'SKU') }}
                            {{ Form::text('sku_product', null, ['class' => 'form-control', 'disabled']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-0">
                            {{ Form::label('add_product', 'Artigo a adicionar') }}
                            {{ Form::text('add_product[]', null, ['class' => 'form-control m-0 ' . (hasModule('logistic') ? 'search-sku' : ''), $isDisable]) }}
                            {{ Form::hidden('product_id', null) }}
                        </div>
                    </div>
                    
                    <div class="col-sm-2">
                        <div class="form-group m-0">
                            {{ Form::label('add_qty', 'Qtd') }}
                            {{ Form::number('add_qty', null, ['class' => 'form-control number', 'min' => '0', $isDisable]) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label for=""><br></label>
                        <button class="btn btn-block btn-success btn-add-product" {{ $isDisable }}
                        data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">
                            <i class="fas fa-plus"></i> Adicionar
                        </button>
                    </div>
                </div>
            </div>
            <div id="table-products"  class="col-sm-12">
                @include('account.event_manager.partials.product_table')
            </div>
            <div class="clearfix"></div>
        </div>
        {{ Form::hidden('event_id', $event->id ?? null) }}
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left text-left w-30">
        {{-- <div class="checkbox m-b-0 m-t-4">
            <label style="padding-left: 10px !important;">
                {{ Form::checkbox('is_finish') }}
                Finalizar Evento
            </label>
            {!! tip('Caso pretenda finalizar o evento (Vai descontantar Stock). Irreversível!') !!}
        </div> --}}
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if (!$isDisable)
        <button class="btn btn-primary btn-submit">Gravar</button>
    @endif
</div>
{{ Form::close() }}

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.min.js')) }}
<script>
    $('.select2').select2(Init.select2());
    $('[data-toggle="tooltip"]').tooltip();
    $('.datepicker').datepicker(Init.datepicker());

    // Verifica se já existe, caso não manda criar
    function getEventId() {
        var eventId = $('.modal [name="event_id"]').val();
        // if not create then create with the current data
        if (typeof eventId === 'undefined' || eventId == null || eventId == '') {
            var data = {}; 
            data.name        = $('.modal [name="name"]').val();
            // data.customer_id = $('.modal [name="customer_id"]').val();
            data.type        = $('.modal [name="type"]').val();
            data.observation = $('.modal [name="observation"]').val();
            data.start_date  = $('.modal [name="start_date"]').val();
            data.start_hour  = $('.modal [name="start_hour"]').val();
            data.end_date    = $('.modal [name="end_date"]').val();
            data.end_hour    = $('.modal [name="end_hour"]').val();
            data.ajax        = true;

            $.ajax({
                type: "POST",
                url: '{{ route('account.event-manager.store') }}',
                data: data,
                async: false, // waits for ajax call
                success: function (data,status) {   // success callback function
                    if (isNaN(data)) { // Se der algum erro e não devolver um número
                        Growl.error("Preencha corretamente os dados do evento.")
                        return false;
                    } 
                    $('.modal [name="event_id"]').val(data);
                    eventId = data;
                },
                error: function () { // error callback 
                    Growl.error500()
                }
            })
        }

        return eventId;
    }

    $('.btn-add-product').on('click', function(e) {
        e.preventDefault();

        var eventId = getEventId();
        if (!eventId) { return false; } // validation
        var product_id = $('.modal [name="product_id"]').val();
        var product    = $('.modal [name="add_product[]"]').val();
        var sku        = $('.modal [name="sku_product"]').val();
        var qty        = parseInt($('.modal [name="add_qty"]').val());
        var maxQty     = parseInt($('.modal [name="add_qty"]').attr('max'));
        var $btn       = $(this);

        var data = {
            id: eventId,
            product_id: product_id,
            product: product,
            sku: sku,
            qty: qty
        };

        if(product == '') {
            Growl.error('Deve selecionar um artigo a adicionar.')
        } else if(qty == '' || qty == 0 || isNaN(qty)) {
            Growl.error('Deve indicar a quantidade a expedir')
        } else if(qty > maxQty) {
            Growl.error('A quantidade não pode ser superior a ' + maxQty)
        } else {
            $btn.button('loading');
            
            var url = '{{ route('admin.event-manager.line.update', ':eventId') }}';
            url = url.replace(':eventId', eventId);
            $.post(url, data, function(data){
                if(data.result) {
                    $('.modal [name="add_product[]"], .modal [name="product_id"], .modal [name="add_qty"], .modal [name="sku_product"]').val('');
                    $('.modal [name=add_qty]').prop('max', '');
                    $('.modal [name="add_product[]"] option').remove()
                    $('#table-products').html(data.html)
                } else {
                    Growl.error(data.feedback)
                }
            }).fail(function(){
                Growl.error500()
            }).always(function(){
                $btn.button('reset');
            })
        }
    });

    $(document).on('click', '.btn-delete-line', function (e) {
        e.preventDefault()
        var $btn = $(this).find('i');
        var $tr  = $(this).closest('tr');
        var url  = $(this).attr('href');

        $btn.removeClass('fa-times').addClass('fa-spin fa-circle-notch');

        $.ajax({
            type: 'DELETE',
            url: url,
        }).done(function (data){
            if(data.result) {
                $tr.remove();
                // Growl.success(data.feedback)
            } else {
                // Growl.error(data.feedback)
            }
        }).fail(function(){
            Growl.error500()
        }).always(function(){
            $btn.removeClass('fa-spin fa-circle-notch').addClass('fa-times');
        })
    });

    /**
     * UPDATE
     **/
     $(document).on('keyup', '[name="product_qty[]"]', function (e) {
        e.preventDefault()
        var $target = $(this).closest('tr').find('[name="product_qty[]"]');
        var line_id = $(this).closest('tr').find('[name="line_id"]').val();
        var url  = $target.data('url');
        var qty  = parseInt($target.val());
        var max  = parseInt($target.data('max'));
        // var price= $(this).closest('tr').find('[name="product_price[]"]').val();

        if(qty == '' || qty == 0 || isNaN(qty) || qty < 0) {
            Growl.error('A quantidade tem de ser superior a 0');
            $target.val($target.data('qty'));
        } else if(qty > max) {
            Growl.error('A quantidade não pode exceder a quantidade máxima de ' + max + ' un');
            $target.val($target.data('qty'));
        } else {
            var data = {
                line_id: line_id,
                qty: qty
            };

            $.post(url, data, function (data) {
                if (data.result) {
                    // Growl.success(data.feedback)
                    $target.data('qty', qty)
                } else {
                    $target.val(data.qty)
                    // Growl.error(data.feedback)
                }

            }).fail(function () {
                Growl.error500()
            }).always(function () {
                //$btn.removeClass('fa-spin fa-circle-notch').addClass('fa-times');
            })
        }
    });

    /**
     * SEARCH PRODUCT
     * ajax method
     */
    @if (hasModule('logistic'))        
     $('.search-sku').autocomplete({
        serviceUrl: "{{ route('admin.shipments.search.sku') }}",
        onSearchStart: function () {},
        beforeRender: function () {},
        onSelect: function (suggestion) {
            var $this = $(this);
            $('input[name=product_id]').val(suggestion.product);
            $('input[name=sku_product]').val(suggestion.sku);
            $('input[name=add_qty]').attr('max', suggestion.stock_total);
        },
    });
    @endif

</script>
