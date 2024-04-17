<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Respostas ao formulário')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <th class="bg-gray-light w-1">@trans('Estado')</th>
                        <th class="bg-gray-light">@trans('Pergunta')</th>
                        <th class="bg-gray-light w-150px">@trans('Notas')</th>
                    </tr>
                    @foreach($answers as $answer)
                        <tr>
                            <td class="text-center">
                                @if($answer->answer)
                                    <i class="fas fa-check-circle text-green"></i>
                                @else
                                    <i class="fas fa-times-circle text-red"></i>
                                @endif
                            </td>
                            <td class="vertical-align-middle">
                                {{ $answer->item->name }}
                            </td>
                            <td>{{ $answer->obs }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>

