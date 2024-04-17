<div class="row">
    <div class="col-sm-8">
        <div class="row row-5">
            <div class="col-sm-12">
                <div class="form-group is-required input-customer"  style="{{ $budget->type == 'animals' ? 'display:none' : '' }}">
                    {{ Form::label('customer_id', 'Cliente') }}
                    <div class="input-group">
                        {{ Form::select('customer_id', [$budget->customer_id => $budget->name], null, ['class' => 'form-control']) }}
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default btn-name">Novo Cliente</button>
                        </div>
                    </div>
                </div>
                <div class="form-group is-required input-name" style="{{ $budget->type == 'animals' ? '' : 'display:none' }}">
                    {{ Form::label('name', 'Nome') }}
                    @if($budget->type != 'animals')
                    <div class="input-group">
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default btn-search"><i class="fas fa-search"></i> Procurar</button>
                        </div>
                    </div>
                    @else
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    @endif
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group is-required">
                    {{ Form::label('email', 'E-mail') }}
                    {{ Form::text('email', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group is-required">
                    {{ Form::label('phone', 'Contacto') }}
                    {{ Form::text('phone', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    {{ Form::label('address', 'Morada') }}
                    {{ Form::text('address', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('zip_code', 'Código Postal') }}
                    {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('city', 'Localidade') }}
                    {{ Form::text('city', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('country', 'País') }}
                    {{ Form::select('country', trans('country'), null, ['class' => 'form-control select2']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="row row-5">
            <div class="col-sm-7">
                <div class="form-group is-required">
                    {{ Form::label('budget_date', 'Data Proposta') }}
                    <div class="input-group">
                        {{ Form::text('budget_date', null, ['class' => 'form-control datepicker', 'required', 'autocomplete'=>'off']) }}
                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group is-required">
                    {{ Form::label('validity_days', 'Validade') }}
                    <div class="input-group">
                        {{ Form::text('validity_days', $budget->days ? null : 30, ['class' => 'form-control', 'required']) }}
                        <span class="input-group-addon">dias</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group is-required">
            {{ Form::label('status', 'Estado') }}
            {{ Form::select('status', ['' => ''] + trans('admin/budgets.status'), null, ['class' => 'form-control select2', 'required']) }}
        </div>

        <div class="row row-5">
            <div class="col-sm-9">
                <div class="form-group">
                    {{ Form::label('model_id', 'Modelo de Textos') }}
                    {{ Form::select('model_id', $models, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('locale', 'Língua') }}
                    {{ Form::select('locale', ['pt' => 'PT', 'en' => 'EN'], null, ['class' => 'form-control select2']) }}
                </div>
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('operator_id', 'Responsável') }}
            {{ Form::select('operator_id', ['' => '-- Sem responsável --'] + $operators, $budget->exists ? null : Auth::user()->id, ['class' => 'form-control select2']) }}
        </div>
    </div>
</div>