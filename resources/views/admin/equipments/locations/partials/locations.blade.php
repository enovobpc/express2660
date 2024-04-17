<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <a href="{{ route('admin.equipments.locations.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    {{--<li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
        </button>
    </li>--}}
    @if(count($warehouses) > 1)
        <li class="fltr-primary w-200px">
            <strong>Armazém</strong><br class="visible-xs"/>
            <div class="pull-left form-group-sm w-130px">
                {{ Form::select('warehouse', ['' => 'Todos'] + $warehouses, fltr_val(Request::all(), 'warehouse'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    @endif
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
            <th class="w-150px">Armazém</th>
            <th class="w-90px">Código</th>
            <th>Localização</th>
            <th class="w-250px">Operador</th>
            <th class="w-1">Artigos</th>
            <th class="w-1">Ações</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.equipments.locations.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>