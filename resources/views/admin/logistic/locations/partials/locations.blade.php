<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <a href="{{ route('admin.logistic.locations.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <li>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> A sincronizar">
                <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.importer.index') }}" target="_blank">
                        <i class="fas fa-fw fa-file-excel"></i> @trans('Importar Localizações')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.export.logistic.locations', 'all') }}"  >
                        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.logistic.locations.print') }}"  >
                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir')
                    </a>
                </li>
            </ul>
        </div>
    </li>
            {{--<li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
        </button>
    </li>--}}
    @if(count($warehouses) > 1)
        <li class="fltr-primary w-200px">
            <strong>@trans('Armazém')</strong><br class="visible-xs"/>
            <div class="pull-left form-group-sm w-130px">
                {{ Form::select('warehouse', ['' => __('Todos')] + $warehouses, fltr_val(Request::all(), 'warehouse'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    @endif
    <li class="fltr-primary w-170px">
        <strong>@trans('Tipo')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::selectMultiple('type', $types, fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li class="fltr-primary w-200px">
        <strong>@trans('Ocupação')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::selectMultiple('status', trans('admin/logistic.locations.status'), fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
   

</ul>
{{--<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left">
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Altura Máx.</strong><br/>
            <div class="w-120px">
                <div class="input-group">
                    {{ Form::text('height', fltr_val(Request::all(), 'height'), array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                    <span class="input-group-addon">
                                        cm
                                    </span>
                </div>
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Largura Máx.</strong><br/>
            <div class="w-120px">
                <div class="input-group">
                    {{ Form::text('length', fltr_val(Request::all(), 'length'), array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                    <span class="input-group-addon">
                                        cm
                                    </span>
                </div>
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Comprimento Máx.</strong><br/>
            <div class="w-120px">
                <div class="input-group">
                    {{ Form::text('width', fltr_val(Request::all(), 'width'), array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                    <span class="input-group-addon">
                                        cm
                                    </span>
                </div>
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Peso Máx.</strong><br/>
            <div class="w-120px">
                <div class="input-group">
                    {{ Form::text('weight', fltr_val(Request::all(), 'weight'), array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                    <span class="input-group-addon">
                        kg
                    </span>
                </div>
            </div>
        </li>
    </ul>
</div>--}}
<div class="table-responsive">
    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-150px">@trans('Armazém')</th>
            <th class="w-90px">@trans('Localização')</th>
            <th class="w-90px">@trans('Código Barras')</th>
            <th>@trans('Tipologia')</th>
            <th class="w-140px">@trans('Dimensões')</th>
            <th class="w-70px">@trans('Paletes')</th>
            <th class="w-70px">@trans('Peso Max.')</th>
            <th class="w-1">@trans('Estado')</th>
            <th class="w-1">@trans('Ações')</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="selected-rows-action hide">
    <div class="pull-left">
        <div class="btn-group btn-group-sm dropup m-l-5">
            {{ Form::open(array('route' => 'admin.logistic.locations.selected.destroy')) }}
                <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>

        <a href="{{ route('admin.export.logistic.locations','selected') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
            <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')    
        </a>

        <div class="btn-group btn-group-sm dropup m-l-5">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-print"></i> @trans('Imprimir') <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.logistic.locations.print' ) }}"  data-toggle="export-selected" >
                        @trans('Localizações')           
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.logistic.locations.selected.labels.print' ) }}"  data-toggle="export-selected" >
                        @trans('Etiquetas')           
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
     //export selected
     window.addEventListener('load', function(){
        $(document).on('change', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            $('[data-toggle="export-selected"]').each(function(i, selected){
                var exportUrl = Url.removeQueryString($(selected).attr('href'));
                $(selected).attr('href', exportUrl + '?' + queryString);
            })
        });
     })
     
</script>