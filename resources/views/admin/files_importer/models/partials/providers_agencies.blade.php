<div class="form-group">
    <label>@trans('Fornecedor')</label>
    {{ Form::select('provider_slug',  ['' => '', 'envialia' => 'Enviália', 'gls' => 'GLS', 'tipsa' => 'Tipsa'], null, ['class' => 'form-control select2', 'required']) }}
</div>