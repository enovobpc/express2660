{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $title }}</h4>
</div>

<div class="modal-body">

    <div class="row row-5">
        <div class="form-group is-required">
            {{ Form::label('price', __('Valor facultado ao motorista')) }}
            <div class="input-group">
                {{ Form::text('price', Setting::get('price_equipment') ?? null, ['class' => 'form-control', 'required']) }}
                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
            </div>
        </div>
    </div>
    
    <div class="row row-5" style="margin-bottom: 10px;">
        {{ Form::label('equipment_info', __('Características (uma por linha)')) }}
        {{ Form::textarea('equipment_info', Setting::get('info_equipment') ?? null, ['class' => 'form-control', 'rows' => 5]) }}
    </div>

    <div class="row row-5" >
        <div class="col-md-9">
            <div class="form-group flex-w">
                {{ Form::checkbox('save_info', 1, null, array('class' => 'form-control')) }}
                <label style="margin-left: 5px">
                    @trans('Guardar informação para ao futuro.')'
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <button type="submit" tar class="btn btn-primary btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gerar PDF..."
                    formtarget="_blank" ><i class="fas fa-fw fa-file-pdf"></i> @trans('Gerar PDF')</button>

            </div>
        </div>
    </div>
</div>
{{ Form::hidden('user_id', $userId) }}

{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $('form.modal-ajax-form').on('submit', function(event) {
        event.preventDefault();
        $(this).attr('target', '_blank');
        
        var form = this;
        form.submit();
        $('.modal').modal('hide');

    });
</script>