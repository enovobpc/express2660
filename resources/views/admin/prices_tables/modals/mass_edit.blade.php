{{ Form::open(['route' => 'admin.prices-tables.mass.update']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Atualizar preços em massa')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            {{ Form::label('update_signal', __('Ação')) }}
            <div class="input-group w-100">
                {{ Form::select('update_signal', ['add' => __('Aumentar'), 'sub' => __('Diminuir')], null, ['class' => 'form-control input-sm select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="sp-20"></div>
            <div class="input-group">
                {{ Form::text('value', null, ['class' => 'form-control', 'maxlength' => 2]) }}
            </div>
        </div>
        <div class="col-sm-1">
            <div class="sp-20"></div>
            <div class="input-group" style="margin-left: -15px">
                {{ Form::select('update_target', ['percent' => '%', 'euro' => Setting::get('app_currency')], null, ['class' => 'form-control input-sm select2']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="input-group w-100">
                {{ Form::label('update_billing_zones', __('Atualizar para os serviços')) }}
                {{ Form::select('update_services[]', $services, null, ['class' => 'form-control input-sm select2', 'multiple', 'required']) }}
            </div>
        </div>
    </div>
    <br/>
    <div class="row row-5">
        <div class="col-sm-6">
            <div class="input-group w-100">
                {{ Form::label('update_billing_zones', __('Atualizar só para a zona')) }}
                {{ Form::select('update_billing_zones[]', $billingZones, null, ['class' => 'form-control input-sm select2', 'multiple', 'data-placeholder' => 'Todas']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-group w-100">
                {{ Form::label('update_price_table_id', __('Atualizar só para as tabelas')) }}
                {{ Form::select('update_price_table_id[]', ['-1' => __('Personalizada')] + $pricesTables, null, ['class' => 'form-control input-sm select2', 'multiple', 'data-placeholder' => 'Todas']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="input-group w-100 m-t-15">
                {{ Form::label('update_customer_id', __('Atualizar apenas para os clientes...')) }}
                {{ Form::select('update_customer_id[]', $customers, null, ['class' => 'form-control input-sm select2', 'multiple', 'data-placeholder' => 'Todas']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Atualizar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2())
</script>