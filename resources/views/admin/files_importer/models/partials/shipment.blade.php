<div class="form-group">
    {{ Form::label('date_format', __('Formato de Data'), ['class' => 'control-label']) }}
    {{ Form::select('date_format',  trans('admin/importer.date_formats'), null, ['class' => 'form-control select2']) }}
</div>
<div class="row row-5">
    <div class="col-sm-6">
        <div class="form-group">
            <label>@trans('Fornecedor') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Todos os envios vão ser associados ao fornecedor indicado.')"></i></label>
            {{ Form::select('provider_id',  ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>@trans('Serviço') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Caso alguma linha a importar não possua serviço, será assumido o serviço aqui indicado.')"></i></label>
            {{ Form::select('service_id',  ['' => ''] + $services, null, ['class' => 'form-control select2']) }}
        </div>
    </div>
</div>
<div class="row row-5">
    <div class="col-sm-6">
        <div class="form-group">
            <label>@trans('Nº Cliente') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Todos os envios vão ser associados ao Nº de cliente indicado. Não é necessário preencher os dados do remetente.')"></i></label>
            {{ Form::text('customer_code', null, ['class' => 'form-control uppercase nospace']) }}
        </div>
    </div>
</div>
<div class="form-group">
    {{ Form::label('mapping_method', __('Mapeamento de Serviços e Estados'), ['class' => 'control-label']) }}
    {{ Form::select('mapping_method',  ['' => __('Nos serviços da plataforma'), 'envialia' => __('Com base na Enviália'), 'tipsa' => __('Com base na Tipsa'), 'gls' => __('Com base na GLS Gepard'), 'gls_zeta' => __('Com base na GLS Zeta'), 'ctt' => __('Com base nos CTT'), 'chronopost' => __('Com base na Chronopost')], null, ['class' => 'form-control select2']) }}
</div>
<div class="checkbox m-t-5 m-b-0">
    <label style="padding-left: 0">
        {{ Form::checkbox('available_customers') }}
        @trans('Disponível na área cliente')
    </label>
</div>