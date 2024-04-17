{{ Form::model($vatRate, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('company_id', 'Taxa aplicável à empresa') }}
                {{ Form::select('company_id', [''=>'']+$companies, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('code', 'Código') }}
                {{ Form::text('code', null, ['class' => 'form-control uppercase nospace', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação Taxa') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('name_abrv', 'Designação Curta') }}{!! tip('Este será o nome apresentado nas listas de seleção do sistema') !!}
                {{ Form::text('name_abrv', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('Valor', 'Valor') }}
                <div class="input-group input-group-money">
                    {{ Form::text('value', null, ['class' => 'form-control decimal', 'required', $valueBlocked]) }}
                    <div class="input-group-addon">%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('billing_code', 'Código AT') }}
                {{ Form::text('billing_code', null, ['class' => 'form-control nospace', 'required', $valueBlocked]) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('class', 'Classe') }}
                {{ Form::select('class', trans('admin/billing.vat-rates-classes'), null, ['class' => 'form-control select2', 'required', $valueBlocked]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('subclass', 'Subclasse') }}
                {{ Form::select('subclass', [''=>''] + trans('admin/billing.vat-rates-subclasses'), null, ['class' => 'form-control select2', 'required', $valueBlocked]) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('zone', 'Zona Fiscal') }}
                {{ Form::select('zone', [''=>''] + trans('admin/billing.vat-rates-zones'), null, ['class' => 'form-control select2', 'required', $valueBlocked]) }}
            </div>
        </div>
    </div>
    <div class="row row-5 exemption-reason" style="{{ $vatRate->subclass == 'ise' ? 'display:block' : 'display:none' }}">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('exemption_reason', 'Motivo de Isenção de IVA') }}
                {{ Form::select('exemption_reason', [''=>'']+$exemptionReasons, null, ['class' => 'form-control select2', $valueBlocked]) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="checkbox">
                <label style="padding: 0">
                    {{ Form::checkbox('is_active') }}
                    Taxa Ativa
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox">
                <label style="padding: 0">
                    {{ Form::checkbox('is_default') }}
                    Por Defeito <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao ativar esta opção, esta taxa será a taxa principal a aplicar."></i>
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox">
                <label style="padding: 0">
                    {{ Form::checkbox('is_sales') }}
                    Visivel Vendas <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao ativar esta opção, esta taxa estará disponível no lançamento de vendas."></i>
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox">
                <label style="padding: 0">
                    {{ Form::checkbox('is_purchases') }}
                    Visivel Compras <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao ativar esta opção, esta taxa estará disponível no lançamento de compras."></i>
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())

    $('.modal [name="subclass"]').on('change', function(){

        if($(this).val() == 'ise') {
            $('.exemption-reason').show();
            $('.exemption-reason select').prop('required', true);
        } else {
            $('.exemption-reason').hide();
            $('.exemption-reason select').prop('required', false);
        }
    })
</script>
