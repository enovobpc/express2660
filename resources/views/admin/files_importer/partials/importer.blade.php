<div class="row">
    {{ Form::open(['route' => 'admin.importer.import', 'files' => true, 'method' => 'POST']) }}
    <div class="col-sm-3">
        <div class="form-group m-b-5">
            {{ Form::label('file', __('Ficheiro a importar'), ['class' => 'control-label']) }}
            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                <div class="form-control" data-trigger="fileinput">
                    <i class="fas fa-file fileinput-exists"></i>
                    <span class="fileinput-filename"></span>
                </div>
                <span class="input-group-addon btn btn-default btn-file">
                    <span class="fileinput-new">@trans('Selecionar')</span>
                    <span class="fileinput-exists">@trans('Alterar')</span>
                    <input type="file" name="file" data-file-format="csv,xls,xlsx" {{ (@$hasErrors || @!$previewRows) ? 'required' : ''  }}>
                </span>
                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@trans('Anular')</a>
            </div>
        </div>
        <div class="form-group is-required">
            {{ Form::label('import_model', __('Modelo Importação'), ['class' => 'control-label']) }}
            {!! Form::selectWithData('import_model', $models, null, ['class' => 'form-control select2', 'required']) !!}
        </div>

        <div data-type="shipments" style="{{ in_array(@$importType, ['shipments', 'shipments_logistic']) || empty($importType) ? 'display:block' : 'display: none' }}">
            <div class="form-group is-required">
                {{ Form::label('agency_id', __('Agência Origem'), ['class' => 'control-label']) }}
                {{ Form::select('agency_id',  ['' => ''] + $agencies, @$agencyId,['class' => 'form-control select2']) }}
            </div>
            <div class="form-group hide">
                {{ Form::label('recipient_agency_id', __('Agência Destino'), ['class' => 'control-label']) }}
                {{ Form::select('recipient_agency_id',  ['' => __('Detetar Automáticamente')] + $agencies, null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group">
                {{ Form::label('provider_id', 'Associar envios ao fornecedor', ['class' => 'control-label']) }}
                {{ Form::select('provider_id',  ['' => __('Automático')] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group m-b-5">
                <div class="checkbox m-b-0">
                    <label style="padding-left: 0; margin-bottom: 0px;">
                        {{ Form::checkbox('direct_import', 1) }}
                        @trans('Importar sem pré-validar (não recomendado)')
                    </label>
                </div>
            </div>
            {{--<div class="form-group">
                <div class="checkbox">
                    <label style="padding-left: 0; margin-bottom: 10px;">
                        {{ Form::checkbox('print_labels', 1) }}
                        Imprimir etiquetas autocolantes
                    </label>
                </div>
            </div>--}}
            <div class="form-group">
                <div class="checkbox">
                    <label style="padding-left: 0; margin-bottom: 10px;">
                        {{ Form::checkbox('auto_submit', 1, false) }}
                        @trans('Submeter automático via webservice')
                    </label>
                </div>
            </div>
        </div>

        <div data-type="customers" style="{{ @$importType == 'customers' ? 'display:block' : 'display: none' }}">
            <div class="form-group">
                {{ Form::label('customers_agency_id', __('Agência'), ['class' => 'control-label']) }}
                {{ Form::select('customers_agency_id',  ['' => ''] + $agencies, null,['class' => 'form-control select2']) }}
            </div>
            <div class="form-group">
                {{ Form::label('type_id', __('Tipo de Cliente'), ['class' => 'control-label']) }}
                {{ Form::select('type_id',  ['' => ''] + $customerTypes, null, ['class' => 'form-control select2']) }}
            </div>
        </div>

        <div data-type="operators" style="{{ @$importType == 'operators' ? 'display:block' : 'display: none' }}">
            <div class="form-group">
                {{ Form::label('operators_agency_id', __('Agência'), ['class' => 'control-label']) }}
                {{ Form::select('operators_agency_id',  ['' => ''] + $agencies, null,['class' => 'form-control select2']) }}
            </div>
        </div>

        <div data-type="shipments_dimensions" style="{{ @$importType == 'shipments_dimensions' ? 'display:block' : 'display: none' }}">
            <div class="form-group">
                {{ Form::label('shipments_dimensions_agency_id', __('Agência'), ['class' => 'control-label']) }}
                {{ Form::select('shipments_dimensions_agency_id',  ['' => ''] + $agencies, null,['class' => 'form-control select2']) }}
            </div>
        </div>

        <div data-type="reception_orders" style="{{ @$importType == 'reception_orders' ? 'display:block' : 'display: none' }}">
            <div class="row row-5">
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('reception_order_date', __('Data Recepção'), ['class' => 'control-label']) }}
                        {{ Form::text('reception_order_date', date('Y-m-d'),['class' => 'form-control datepicker']) }}
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group">
                        {{ Form::label('reception_order_doc', __('Ref. Pedido'), ['class' => 'control-label']) }}
                        {{ Form::text('reception_order_doc', null,['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('reception_order_customer', __('N.º Cliente'), ['class' => 'control-label']) }}
                {{ Form::text('reception_order_customer', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div data-type="shipping_orders" style="{{ @$importType == 'shipping_orders' ? 'display:block' : 'display: none' }}">
            <div class="form-group is-required">
                {{ Form::label('shipping_order_customer', __('N.º Cliente'), ['class' => 'control-label']) }}
                {{ Form::text('shipping_order_customer', null, ['class' => 'form-control', 'required']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-5">
                    <div class="form-group is-required">
                        {{ Form::label('shipping_order_date', __('Data'), ['class' => 'control-label']) }}
                        {{ Form::text('shipping_order_date', date('Y-m-d'),['class' => 'form-control datepicker', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group is-required">
                        {{ Form::label('shipping_order_doc', __('Documento ou Referência'), ['class' => 'control-label']) }}
                        {{ Form::text('shipping_order_doc', null,['class' => 'form-control', 'required']) }}
                    </div>
                </div>
            </div>
        </div>

        @if(@$pricesTables)
        <div data-type="prices_table" style="{{ @$importType == 'prices_table' ? 'display:block' : 'display: none' }}">
            <div class="form-group">
                {{ Form::label('prices_table_id', __('Tabela de Preços'), ['class' => 'control-label']) }}
                {{ Form::select('prices_table_id',  ['' => ''] + @$pricesTables, null,['class' => 'form-control select2', @$importType == 'prices_table' ? 'required' : '' ]) }}
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <input type="hidden" name="filepath" value="{{ @$filepath }}">

                @if(@$hasErrors || empty(@$previewRows))
                    <input type="hidden" name="preview_mode" value="1">
                @else
                    <input type="hidden" name="preview_mode" value="0">
                @endif

                @if(@$hasErrors || @!$previewRows)
                    <button type="submit" class="btn btn-primary btn-validate" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A validar..."><i class="fas fa-upload"></i> @trans('Validar Ficheiro')</button>
                @else
                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar..."><i class="fas fa-check"></i> @trans('Importar Dados')</button>
                @endif
                @if(!empty(@$previewRows))
                <a href="{{ route('admin.importer.index') }}" class="btn btn-default">@trans('Cancelar')</a>
                @endif
            </div>
        </div>
    </div>
    {{ Form::hidden('rguide') }}
    {{ Form::hidden('rcheck') }}
    {{ Form::hidden('rpack') }}
    {{ Form::hidden('is_carrier') }}
    {{ Form::close() }}

    <div class="col-sm-9">
        @include('admin.files_importer.partials.preview_file')
    </div>
</div>
