{{ Form::model($customerMessage, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('subject', __('Assunto')) }}
        {{ Form::text('subject', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="form-group is-required">
        {{ Form::label('message', __('Mensagem')) }}
        {{ Form::textarea('message', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="row row-5">
        <div class="col-sm-9 col-md-8">
            <div class="form-group">
                {{ Form::label('selected_customers[]', __('Escolher os clientes para quem enviar:')) }}
                <i class="fas fa-spin fa-circle-notch loading-filters" style="display: none"></i>
                <div class="input-group">
                    {{ Form::select('selected_customers[]', $customers, $selectedCustomers, ['class' => 'form-control select2 customers-box', 'multiple' => true]) }}
                    <div class="input-group-btn" style="vertical-align: top">
                        <button type="button" class="btn btn-sm btn-default btn-open-filter">
                            <i class="fas fa-search"></i> @trans('Filtrar')
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-md-2">
            <label class="m-t-25" style="font-weight: normal; padding: 0">
                {{ Form::checkbox('send_all', 1) }}
                @trans('Enviar para todos')
            </label>
        </div>
        <div class="col-sm-3 col-md-2">
            <label class="m-t-25" style="font-weight: normal; padding: 0">
                {{ Form::checkbox('is_static', 1) }}
                @trans('Mensagem fixa') {!! tip(__('Uma mensagem fixa é apresentada sempre na área de cliente mesmo para novos clientes. (Ex: mensagem de boas vindas)')) !!}
            </label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div style="float: left; margin-top: 5px;">
        <div class="form-group m-b-0">
            <label style="font-weight: normal;">
                {{ Form::checkbox('send_email', 1) }}
                <i class="fas fa-envelope"></i> @trans('Enviar mensagem também por e-mail')
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Enviar')</button>
</div>
{{ Form::close() }}

@include('admin.customers.messages.partials.modal_search_customers')
<script>
    $('.select2').select2(Init.select2());

    $('[name="send_all"]').on('change', function () {
        if($(this).is(':checked')) {
            $('.customers-box').val('').trigger('change').prop('disabled', true);
        } else {
            $('.customers-box').prop('disabled', false);
        }
    })

    $('[name="is_static"]').on('change', function () {
        if($(this).is(':checked')) {
            $('[name="send_all"]').prop('checked', false).prop('disabled', true);
            $('.customers-box').val('').trigger('change').prop('disabled', true);
        } else {
            $('[name="send_all"]').prop('disabled', false)
            $('.customers-box').prop('disabled', false);
        }
    })

    $('.btn-open-filter').on('click', function(){
        $('#modal-filter-customers').show().addClass('in')
    })

    $('.filter-cancel, .filter-customers').on('click', function(e){
        e.preventDefault()
        $('#modal-filter-customers').hide().removeClass('in')
    })

    $('.filter-customers').on('click', function(e){
        e.preventDefault()

        var $form = $('.form-filter-customers');

        $('.loading-filters').show();
        $.post($form.attr('action'), $form.serialize(), function(data){
            $('[name="selected_customers[]"]').val(data).trigger('change');
        }).always(function (){
            $('.loading-filters').hide();
        })
    })
</script>

