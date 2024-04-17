<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-incidences">
            <li>
                <a href="{{ route('admin.fleet.incidences.create', ['vehicle' => $vehicle->id]) }}"
                   class="btn btn-success btn-sm"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
            <li>
                <strong>@trans('Data')</strong>
                <div class="input-group input-group-sm w-200px">
                    {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início')]) }}
                    <span class="input-group-addon">@trans('até')</span>
                    {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim')]) }}
                </div>
            </li>
            <li class="fltr-primary w-200px">
                <strong>@trans('Operador')</strong><br class="visible-xs"/>
                <div class="pull-left form-group-sm w-125px">
                    {{ Form::select('incidences_operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'incidences_operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        </ul>
        <table id="datatable-incidences" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-70px">@trans('Data')</th>
                    <th>@trans('Ocorrência')</th>
                    <th class="w-200px">@trans('Operador')</th>
                    <th class="w-60px">Km</th>
                    <th class="w-1">@trans('Resolução')</th>
                    <th class="w-60px">@trans('Estado')</th>
                    <th class="w-80px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.incidences.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>