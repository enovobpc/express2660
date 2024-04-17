<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-meetings">
            <li>
                <a href="{{ route('admin.meetings.create', ['customer' => $prospect->id]) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
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
            <li>
                <strong>@trans('Estado')</strong>
                {{ Form::select('status', array('' => __('Todos')) + trans('admin/meetings.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
            </li>
        </ul>
        <table id="datatable-meetings" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-60px">@trans('Data')</th>
                    <th class="w-150px">@trans('Detalhes')</th>
                    <th class="w-120px">@trans('Objetivos')</th>
                    <th class="w-120px">@trans('Acontecimentos')</th>
                    <th class="w-120px">@trans('Cobranças')</th>
                    <th class="w-1">@trans('Estado')</th>
                    <th class="w-65px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.meetings.selected.destroy', $prospect->id]]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>