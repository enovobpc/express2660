{{ Form::model($budget, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                Cliente
            </a>
        </li>
        @if($budget->type == 'animals')
        <li>
            <a href="#tab-goods" data-toggle="tab">
                Dados Animais
            </a>
        </li>
        @else
        <li>
            <a href="#tab-goods" data-toggle="tab">
                Mercadoria
            </a>
        </li>
        @endif
        <li>
            <a href="#tab-transport" data-toggle="tab">
                Transporte
            </a>
        </li>
        <li>
            <a href="#tab-services" data-toggle="tab">
                Serviços
            </a>
        </li>
        <li>
            <a href="#tab-observations" data-toggle="tab">
                Anotações
            </a>
        </li>
        <li>
            <a href="#tab-history" data-toggle="tab">
                Histórico
            </a>
        </li>
        <li>
            <a href="#tab-request" data-toggle="tab">
                Pedido Cliente
            </a>
        </li>
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0">
    <div class="tab-content m-b-0" style="padding-bottom: 10px;">
        <div class="tab-pane active" id="tab-info">
            @include('admin.budgets.budgets_courier.partials.info')
        </div>
        @if($budget->type == 'animals')
            <div class="tab-pane" id="tab-goods">
                @include('admin.budgets.budgets_courier.partials.animals')
            </div>
        @else
            <div class="tab-pane" id="tab-goods">
                @include('admin.budgets.budgets_courier.partials.goods')
            </div>
        @endif
        <div class="tab-pane" id="tab-transport">
            @include('admin.budgets.budgets_courier.partials.transport')
        </div>
        <div class="tab-pane" id="tab-services">
            @include('admin.budgets.budgets_courier.partials.services')
        </div>
        <div class="tab-pane" id="tab-observations">
            @include('admin.budgets.budgets_courier.partials.observations')
        </div>
        @if($budget->exists)
        <div class="tab-pane" id="tab-history">
            @include('admin.budgets.budgets_courier.partials.history')
        </div>
        @endif
        <div class="tab-pane" id="tab-request">
            @include('admin.budgets.budgets_courier.partials.request')
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::hidden('type') }}
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary btn-submit-waybill">Gravar</button>
</div>
{{ Form::close() }}

{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    CKEDITOR.config.toolbar = [
        ['Bold','Italic','Underline', 'StrikeThrough', 'NumberedList','BulletedList'],
    ] ;

    CKEDITOR.replace('ckeditor1', { height: 100 });
    CKEDITOR.replace('ckeditor2', { height: 300 });
    CKEDITOR.replace('ckeditor3', { height: 200 });
    CKEDITOR.replace('ckeditor4', { height: 200 });

    $('[name="model_id"]').on('change', function(){

        var id = $('[name="model_id"]').val();

        $.post("{{ route('admin.budgets.courier.model.get') }}", {id:id}, function(data){
            CKEDITOR.instances['ckeditor1'].setData(data.intro);
            CKEDITOR.instances['ckeditor2'].setData(data.transport_info);
            CKEDITOR.instances['ckeditor3'].setData(data.payment_conditions);
            CKEDITOR.instances['ckeditor4'].setData(data.geral_conditions);
        })
    });

    $('.modal .select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker())

    $('.service-price, .service-qty').on('change', function() {
        var $tr   = $(this).closest('tr');
        var qt    = $tr.find('.service-qty').val();
        var price = $tr.find('.service-price').val();

        $tr.find('.service-subtotal').val((price * qt).toFixed(2));
    })

    /**
     * Add goods
     */
    $('.btn-add-goods').on('click', function(){
        $('.table-goods').find('tr:hidden:first').show();

        if($('.table-goods').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-goods').on('click', function(){

        if($('.table-goods').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-goods').append($tr);

            if ($('.table-goods').find("tr:hidden").length == 0) {
                $('.btn-add-goods').hide();
            } else {
                $('.btn-add-goods').show();
            }
        }
    });

    /**
     * Add animals
     */
    $('.btn-add-animals').on('click', function(){
        $('.table-animals').find('tr:hidden:first').show();

        if($('.table-animals').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-animals').on('click', function(){

        if($('.table-animals').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-animals').append($tr);

            if ($('.table-animals').find("tr:hidden").length == 0) {
                $('.btn-add-animals').hide();
            } else {
                $('.btn-add-animals').show();
            }
        }
    });

    /**
     * Add transport
     */
    $('.btn-add-transport').on('click', function(){
        $('.table-transport').find('tr:hidden:first').show();

        if($('.table-transport').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-transport').on('click', function(){

        if($('.table-transport').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-transport').append($tr);

            if ($('.table-transport').find("tr:hidden").length == 0) {
                $('.btn-add-transport').hide();
            } else {
                $('.btn-add-transport').show();
            }
        }
    });

    /**
     * Add Services
     */
    $('.btn-add-services').on('click', function(){
        $('.table-services').find('tr:hidden:first').show();

        if($('.table-services').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-services').on('click', function(){

        if($('.table-services').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-services').append($tr);

            if ($('.table-services').find("tr:hidden").length == 0) {
                $('.btn-add-services').hide();
            } else {
                $('.btn-add-services').show();
            }
        }
    });

    $('.form-courier-budget button[type=submit]').on('click', function(e){
        e.preventDefault();
        //$('#ckeditor1').val(CKEDITOR.instances['ckeditor1'].getData());
        $('form.form-courier-budget').submit();
    })


    $("select[name=customer_id]").select2({
        ajax: {
            url: "{{ route('admin.budgets.courier.search.customer') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('.btn-search').on('click', function () {
        $('.input-customer').show()
        $('.input-name').hide()
    })

    $('.btn-name').on('click', function () {
        $('.input-customer').hide()
        $('.input-name').show()
    })
</script>

