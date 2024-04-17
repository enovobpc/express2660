@if(!hasModule('human_resources'))
    @include('admin.partials.denied_message')
@else
    <div class="box no-border">
        <div class="box-body">
            <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-absences">
                <li>
                    <a href="{{ route('admin.users.absences.create', $user->id) }}"
                       class="btn btn-success btn-sm"
                       data-toggle="modal"
                       data-target="#modal-remote-xs">
                        <i class="fas fa-plus"></i> @trans('Novo')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.absences.adjust', $user->id) }}"
                       class="btn btn-primary btn-sm"
                       data-toggle="modal"
                       data-target="#modal-remote-xs">
                        @trans('Ajustar')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.export.operators.absences', ['user' => $user->id]) }}"
                       class="btn btn-default btn-sm" target="_blank">
                        <i class="fas fa-file-excel"></i> @trans('Exportar')
                    </a>
                </li>
            </ul>
            <table id="datatable-absences" class="table table-striped table-dashed table-hover table-condensed">
                <thead>
                    <tr>
                        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                        <th></th>
                        <th>@trans('Tipo')</th>
                        <th class="w-80px">@trans('Estado')</th>
                        <th class="w-80px">@trans('Data Início')</th>
                        <th class="w-80px">@trans('Data Fim')</th>
                        <th class="w-1">@trans('Duração')</th>
                        <th>@trans('Observações')</th>
                        <th class="w-1"><span data-toggle="tooltip" title="Ausência Remunerada">@trans('Remun.')</span></th>
                        <th class="w-1"><span data-toggle="tooltip" title="Inclui Subsidio de Alimentação">@trans('Aliment.')</span></th>
                        <th class="w-65px">@trans('Ações')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <div class="selected-rows-action hide">
                {{ Form::open(['route' => ['admin.users.absences.selected.destroy', $user->id]]) }}
                <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endif