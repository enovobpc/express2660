<div class="panel panel-default panel-navigation has-breadcrumb m-b-10">
    <div class="panel-body">

        <a href="{{ route('admin.repository.index', ['folder' => @$curFolder->parent_id]) }}"
           class="back-button pull-left {{ $curFolder ? '' : 'hide' }}"
            style="padding: 15px 15px 14px;">
            <span class="fa fa-angle-left"></span>
        </a>

        <div class="pull-right">
            <ul class="list-inline hidden-xs" style="margin: -10px 0;">
                <li>
                    <h4>
                        <small>@trans('Espaço Ocupado (Max ') {{ Setting::get('server_size_gb') }} Gb)</small><br/>
                        {{ human_filesize($totalSize) }}
                    </h4>
                    <table class="w-100 m-t-5">
                        <tr>
                            <td style="background: {{ $ocupiedSizeColor }}; width: {{ $ocupiedSizePercent }}%; height: 3px"></td>
                            <td style="background: #ccc; width: {{ 100-$ocupiedSizePercent }}%"></td>
                        </tr>
                    </table>
                </li>
            </ul>
        </div>
        <div class="pull-left">
            <h4 style="margin: -5px 5px 10px;"><span class="fa fa-folder text-yellow"></span>
                @if(empty($breadcrumb))
                    {{ $curFolder ? $curFolder->name : 'Documentos' }}
                @else
                    @foreach($breadcrumb as $key => $item)
                        <span>
                            @if($key)
                                ›
                            @endif
                            <a href="{{ route('admin.repository.index', ['folder' => @$item['id']]) }}">
                            {{ @$item['title'] }}
                            </a>
                        </span>
                    @endforeach
                @endif
            </h4>
            <ol class="list-inline m-0" style="margin-bottom: -3px">
                @if(in_array(@$curFolder->id, $guardedFolders))
                    <li>
                        <a href="{{ route('admin.repository.create', ['parent' => Request::get('folder')]) }}" class="text-green"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-upload"></i> @trans('Carregar Ficheiro')
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-muted" style="cursor: not-allowed" disabled="true">
                            <i class="fas fa-folder"></i> @trans('Criar Pasta')
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('admin.repository.create', ['parent' => Request::get('folder')]) }}" class="text-green"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-upload"></i> @trans('Carregar Ficheiro')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.repository.create', ['source' => 'folder', 'parent' => Request::get('folder')]) }}" class="text-green"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-folder"></i> @trans('Criar Pasta')
                        </a>
                    </li>
                @endif
            </ol>
        </div>
    </div>
</div>

<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-nav">
    {{--<li>
        <a href="{{ route('admin.repository.create', ['parent' => Request::get('folder')]) }}" class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-upload"></i> Carregar Ficheiro
        </a>
    </li>
    <li>
        <a href="{{ route('admin.repository.create', ['source' => 'folder', 'parent' => Request::get('folder')]) }}" class="btn btn-default btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xs">
            <i class="fas fa-folder"></i> Criar Pasta
        </a>
    </li>--}}
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
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-nav">
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
    <table id="datatable-nav" class="table table-striped table-dashed table-hover table-condensed">
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
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar')</button>
    {{ Form::close() }}
</div>
<div class="clearfix"></div>