<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <a href="{{ route('admin.repository.create') }}" class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-upload"></i> @trans('Carregar Ficheiro')
        </a>
    </li>
    {{--<li>
        <a href="{{ route('admin.repository.sort') }}"
           class="btn btn-default btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>--}}
    <li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
        </button>
    </li>
    <li class="fltr-primary w-240px">
        <strong>@trans('Tipo')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-170px">
            {{ Form::selectMultiple('type', $types, fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left">
        <li class="col-xs-12">
            <strong>@trans('Data')</strong><br/>
            <div class="input-group input-group-sm w-220px">
                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">@trans('até')</span>
                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>@trans('Extensão')</strong><br/>
            <div class="w-160px">
                {{ Form::selectMultiple('extension', $extensions, fltr_val(Request::all(), 'extension'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th>@trans('Documento')</th>
            <th>@trans('Origem')</th>
            <th class="w-1">@trans('Tamanho')</th>
            <th class="w-1">@trans('Extensão')</th>
            <th class="w-65px">@trans('Criado Em')</th>
            <th class="w-65px">@trans('Ações')</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.repository.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar</button>
    {{ Form::close() }}
</div>
<div class="clearfix"></div>