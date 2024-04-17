<table class="table table-bordered table-dashed table-condensed m-0">
    <tr>
        <th class="bg-gray-light w-130px">@trans('Data')</th>
        <th class="bg-gray-light">@trans('Motorista')</th>
        <th class="bg-gray-light w-70px">@trans('Km')</th>
        <th class="bg-gray-light w-1">@trans('Estado')</th>
        <th class="bg-gray-light w-95px">@trans('Ações')</th>
    </tr>

    @foreach($answers as $key => $answer)
        <tr>
            <td class="vertical-align-middle">{{ $answer->created_at->format('Y-m-d H:i') }}</td>
            <td class="vertical-align-middle">{{ @$answer->operator->name }}</td>
            <td class="vertical-align-middle">{{ $answer->km }}</td>
            <td class="text-center">
                @if(empty($answer->status))
                    <i class="fas fa-times-circle text-red"></i>
                @else
                    <i class="fas fa-check-circle text-green"></i>
                @endif
            </th>
            <td>
                <a href="{{ route('admin.fleet.checklists.answer.details', [$answer->checklist_id, $answer->control_hash]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote"
                   class="btn btn-xs btn-default">
                   @trans('Detalhe')
                </a>
                <a href="{{ route('admin.fleet.checklists.answer.destroy', [$answer->checklist_id, $answer->control_hash]) }}"
                   data-method="delete"
                   data-confirm="@trans('Confirma a remoção do registo selecionado?')"
                   class="btn btn-xs btn-danger">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    @endforeach
</table>