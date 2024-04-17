<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-checklists">
            {{--<li>
                <a href="{{ route('admin.fleet.checklists.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> Novo
                </a>
            </li>--}}
            <li>
                <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                    <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                </button>
            </li>
            <li class="fltr-primary w-260px">
                <strong>@trans('Formulário')</strong><br class="visible-xs"/>
                <div class="pull-left form-group-sm w-185px">
                    {{ Form::select('checklist_checklist', ['' => __('Todos')] + $checklists , fltr_val(Request::all(), 'checklist_checklist'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        </ul>
        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-checklists">
            <ul class="list-inline pull-left">
                <li class="col-xs-12">
                    <strong>@trans('Data')</strong><br/>
                    <div class="input-group input-group-sm w-220px">
                        {{ Form::text('checklist_date_min', fltr_val(Request::all(), 'checklist_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                        <span class="input-group-addon">@trans('até')</span>
                        {{ Form::text('checklist_date_max', fltr_val(Request::all(), 'checklist_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>@trans('Motorista')</strong><br/>
                    <div class="w-140px">
                        {{ Form::select('checklist_operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'checklist_operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
            </ul>
        </div>
        <table id="datatable-checklists" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="w-110px">@trans('Data')</th>
                <th class="w-75px">@trans('Estado')</th>
                <th>@trans('Formulário')</th>
                <th>@trans('Motorista')</th>
                <th class="w-50px">Km</th>
                <th class="w-80px">@trans('Ações')</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
           {{-- {{ Form::open(['route' => ['admin.fleet.accessories.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
            {{ Form::close() }}--}}
        </div>
    </div>
</div>