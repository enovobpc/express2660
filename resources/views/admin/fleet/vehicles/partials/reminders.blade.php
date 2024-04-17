<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-reminders">
            <li>
                <a href="{{ route('admin.fleet.reminders.create', ['vehicle' => $vehicle->id]) }}"
                   class="btn btn-success btn-sm"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
            <li class="fltr-primary w-140px">
                <strong>@trans('Estado')</strong><br class="visible-xs"/>
                <div class="pull-left form-group-sm w-85px">
                    {{ Form::select('reminder_active', ['' => __('Todos'), '2' => __('Expirados'), '3' => __('Prestes Expirar'), '1'=> __('Ativo'), '0' => __('Concluídos')], fltr_val(Request::all(), 'reminder_active', 1), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        </ul>
        <table id="datatable-reminders" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th>@trans('Lembrete')</th>
                    <th class="w-80px">@trans('Data Limite')</th>
                    <th class="w-80px">@trans('Km Limite')</th>
                    <th class="w-70px">@trans('Aviso Dias')</th>
                    <th class="w-70px">@trans('Aviso Km')</th>
                    <th class="w-90px">@trans('Restante')</th>
                    <th class="w-1">@trans('Ativo')</th>
                    <th class="w-80px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.reminders.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>