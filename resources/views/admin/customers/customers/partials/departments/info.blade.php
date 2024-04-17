{{ Form::model($department, $formOptions) }}
@if(!$department->exists)
<div class="alert alert-info">
    <p class="m-0"><i class="fas fa-info-circle"></i> @trans('Poderá atribuir acesso à área de cliente depois de criar o departamento.')</p>
</div>
@endif
<div class="row row-5">
    <div class="{{ config('app.source') == 'packbox' ? 'col-sm-1' : 'col-sm-3 col-md-1' }}">
        <div class="form-group">
            {{ Form::label('code', __('Código')) }}
            {{ Form::text('code', null, ['class' => 'form-control nospace uppercase', 'maxlength' => 5]) }}
        </div>
    </div>
    <div class="{{ config('app.source') == 'packbox' ? 'col-sm-1' : 'col-sm-3 col-md-1' }}">
        <div class="form-group">
            {{ Form::label('code_abbrv', __('Abrev.')) }}
            {{ Form::text('code_abbrv', null, ['class' => 'form-control nospace uppercase', 'maxlength' => 5]) }}
        </div>
    </div>
    <div class="{{ config('app.source') == 'packbox' ? 'col-sm-7' : 'col-sm-9 col-md-10' }}">
        <div class="form-group is-required">
            {{ Form::label('name', __('Nome de Expedição')) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' =>  $department->exists ? null : @$department->customer->name]) }}
        </div>
    </div>
    @if(config('app.source') == 'packbox')
    <div class="col-sm-3">
        <div class="form-group is-required">
            {{ Form::label('agency_id', __('Agência')) }}
            {{ Form::select('agency_id', (count(@$agencies) == 1) ? @$agencies : ['' => ''] + @$agencies, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    </div>
    @endif
</div>
<div class="form-group is-required">
    {{ Form::label('address', __('Morada Expedição')) }}
    {{ Form::text('address', null, ['class' => 'form-control', 'required', 'placeholder' =>  $department->exists ? null : @$department->customer->address]) }}
</div>
<div class="row row-5">
    <div class="col-sm-3 col-md-2">
        <div class="form-group is-required">
            {{ Form::label('zip_code', __('Código Postal')) }}
            {{ Form::text('zip_code', null, ['class' => 'form-control', 'required', 'placeholder' =>  $department->exists ? null : @$department->customer->zip_code]) }}
        </div>
    </div>
    <div class="col-sm-5 col-md-6">
        <div class="form-group is-required">
            {{ Form::label('city', __('Localidade')) }}
            {{ Form::text('city', null, ['class' => 'form-control', 'required', 'placeholder' =>  $department->exists ? null : @$department->customer->city]) }}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {{ Form::label('country', __('País')) }}
            {{ Form::select('country', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2']) }}
        </div>
    </div>
</div>
<div class="row row-5">
    <div class="col-sm-6">
        <div class="form-group">
            {{ Form::label('contact_email', __('E-mail')) }}
            {{ Form::text('contact_email', null, ['class' => 'form-control email lowercase', 'placeholder' =>  $department->exists ? null : @$department->customer->contact_email]) }}
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            {{ Form::label('phone', __('Telefone')) }}
            {{ Form::text('phone', null, ['class' => 'form-control phone', 'placeholder' =>  $department->exists ? null : @$department->customer->phone]) }}
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            {{ Form::label('mobile', __('Telemóvel')) }}
            {{ Form::text('mobile', null, ['class' => 'form-control phone', 'placeholder' =>  $department->exists ? null : @$department->customer->mobile]) }}
        </div>
    </div>
</div>
<div class="row row-5">
    <div class="col-sm-3">
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_independent', 1) }}
                @trans('É Independente')
                {!! tip(__('Ao ativar esta opção, as tarefas dos operadores geradas ao criar envios, serão separadas do cliente principal.')) !!}
            </label>
        </div>
    </div>
</div>
<div class="modal-footer" style="margin-left: -15px; margin-right: -15px; padding-bottom: 0">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}