{{ Form::model($pickupPoint, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('code', __('Código')) }}
                {{ Form::text('code', null, ['class' => 'form-control nospace uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group  is-required">
                {{ Form::label('provider_id', __('Fornecedor')) }}
                {{ Form::select('provider_id', ['' => ''] + $providers, @$provider, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4" style="">
            {{ Form::label('morning_start_hours', __('Horário Manhã')) }}
            <div class="input-group">
                {{ Form::select('start_morning_hour', ['' => '--:--'] + $hours, @$pickupPoint->horary['start_morning'], ['class' => 'form-control select2']) }}
                <div class="input-group-addon">@trans('Até')</div>
                {{ Form::select('end_morning_hour', ['' => '--:--'] + $hours, @$pickupPoint->horary['end_morning'], ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-4" style="">
            {{ Form::label('after_start_hours', __('Horário Tarde')) }}
            <div class="input-group">
                {{ Form::select('start_afternoon_hour', ['' => '--:--'] + $hours, @$pickupPoint->horary['start_afternoon'], ['class' => 'form-control select2']) }}
                <div class="input-group-addon">@trans('Até')</div>
                {{ Form::select('end_afternoon_hour', ['' => '--:--'] + $hours, @$pickupPoint->horary['end_afternoon'], ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('address', __('Morada')) }}
                {{ Form::text('address', null, ['class' => 'form-control uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('zip_code', __('Código Postal')) }}
                {{ Form::text('zip_code', null, ['class' => 'form-control uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('city', __('Localidade')) }} 
                {{ Form::text('city', null, ['class' => 'form-control uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('country', __('País')) }}
                {{ Form::select('country', trans('country'), $pickupPoint->exists ? null : Setting::get('app_country'), ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('email', __('E-mail')) }}
                {{ Form::text('email', null, ['class' => 'form-control nospace email']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('phone', __('Telefone')) }}
                {{ Form::text('phone', null, ['class' => 'form-control phone']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('mobile', __('Telemóvel')) }}
                {{ Form::text('mobile', null, ['class' => 'form-control phone']) }}
            </div>
        </div>
    </div>
    <div class="text-left">
        <ul class="list-inline">
            <li>
                <div class="checkbox m-b-0 m-t-4" style="margin-left: -10px">
                    <label style="padding-left: 10px !important;">
                        {{ Form::checkbox('delivery_saturday') }}
                        @trans('Aberto Sábado')
                    </label>
                    {!! tip(__('Ativar caso esteja disponivel aos Sábados.')) !!}
                </div>
            </li>
            <li>
                <div class="checkbox m-b-0 m-t-4">
                    <label style="padding-left: 10px !important;">
                        {{ Form::checkbox('delivery_sunday') }}
                        @trans('Aberto Domingo')
                    </label>
                    {!! tip(__('Ativar caso esteja disponivel aos Domingos.')) !!}
                </div>
            </li>
            <li>
                <div class="checkbox m-b-0 m-t-4 ">
                    <label style="padding-left: 10px !important;">
                        {{ Form::checkbox('is_active', 1, $pickupPoint->exists ? null : true) }}
                        @trans('Ativo')
                    </label>
                    {!! tip(__('Caso pretenda inativar o Pickup Point.')) !!}
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button class="btn btn-primary btn-submit">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal [data-toggle="tooltip"]').tooltip();
</script>
