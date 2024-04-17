{{ Form::model($recipient, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4 col-md-2">
            <div class="form-group">
                {{ Form::label('code', __('Cód. Cliente')) }}
                {{ Form::text('code', null, ['class' => 'form-control uppercase']) }}
            </div>
        </div>
        <div class="col-sm-8 col-md-6">
            <div class="form-group is-required">
                {{ Form::label('name', __('Destinatário')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="form-group">
                {{ Form::label('responsable', __('Pessoa Contacto')) }}
                {{ Form::text('responsable', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="form-group is-required">
        {{ Form::label('address', __('Morada')) }}
        {{ Form::text('address', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('zip_code', __('Cód. Postal')) }}
                {{ Form::text('zip_code', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('city', __('Localidade')) }}
                {{ Form::text('city', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('country', __('País')) }}
                {{ Form::select('country', trans('country'), $recipient->country ?? Setting::get('app_country'), ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('phone', __('Telefone')) }}
                {{ Form::text('phone', null, ['class' => 'form-control nospace phone']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('email', __('E-mail')) }}
                {{ Form::text('email', null, ['class' => 'form-control nospace email']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('assigned_customer_id', __('Associar destinatário ao cliente')) }}
                {!! tip(__('Se este destinatário corresponder a um cliente, associe-o aqui.')) !!}
                {{ Form::select('assigned_customer_id', [@$recipient->assigned_customer->id => @$recipient->assigned_customer->name], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox m-b-0 m-t-25">
                    <label style="padding-left: 0 !important;">
                        {{ Form::checkbox('always_cod', 1, $recipient->always_cod) }}
                        @trans('Sempre Portes Destino')
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group m-0">
        {{ Form::label('obs', __('Observações a preencher no envio')) }}
        {!! tip(__('As observações que aqui colocar serão automáticamente apresentadas no campo das observações do envio quando este cliente for selecionado.')) !!}
        {{ Form::text('obs', null, ['class' => 'form-control']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());

    $(".modal select[name=assigned_customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customers.search') }}")
    });

    $('.modal [name="zip_code"], .modal [name="country"]').on('change', function() {
        var zipCode = $('.modal [name="zip_code"]').val();
        var country = $('.modal [name="country"]').val();
        ZipCode.validateInput(country, zipCode);
    })
</script>

