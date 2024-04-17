<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Histórico de movimentos por categoria</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-md-12">
            <table class="table table-condensed m-b-0">
                <tr>
                    <th class="bg-gray-light w-150px">Data</th>
                    <th class="bg-gray-light w-1">Ação</th>
                    <th class="bg-gray-light">Localização</th>
                    <th class="bg-gray-light w-200px">Registo por</th>
                </tr>
                @if($histories->isEmpty())
                    <tr>
                        <td colspan="4">Não existe histórico para este equipamento</td>
                    </tr>
                @else
                    @foreach($histories as $history)
                        <tr>
                            <td>{{ $history->created_at }}</td>
                            <td>
                                <span class="label" style="background: {{ trans('admin/equipments.equipments.actions-color.'.$history->action) }}">
                                    {{ trans('admin/equipments.equipments.actions.'.$history->action) }}
                                </span>
                            </td>
                            <td>{{ @$history->location->name }}</td>
                            <td>{{ @$history->operator->name }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
