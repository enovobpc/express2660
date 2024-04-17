<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Histórico do equipamento</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-md-12">
            <table class="table table-condensed m-b-0">
                <tr>
                    <th class="bg-gray-light w-90px">Data</th>
                    <th class="bg-gray-light w-1">Ação</th>
                    <th class="bg-gray-light">Localização</th>
                    <th class="bg-gray-light">N.º OT</th>
                    <th class="bg-gray-light">Stock</th>
                    <th class="bg-gray-light w-120px">Registo por</th>
                </tr>
                @if($equipment->history->isEmpty())
                    <tr>
                        <td colspan="4">Não existe histórico para este equipamento</td>
                    </tr>
                @else
                    <?php $histories = $equipment->history->sortByDesc('created_at')?>
                    @foreach($histories as $history)
                        <tr>
                            <td>
                                {{ $history->created_at->format('Y-m-d') }}<br/>
                                <small>{{ $history->created_at->format('H:i:s') }}</small>
                            </td>
                            <td class="text-center">
                                <span class="label" style="background: {{ trans('admin/equipments.equipments.actions-color.' . $history->action) }}">
                                    {{ trans('admin/equipments.equipments.actions.' . $history->action) }}
                                </span>
                            </td>
                            <td>
                                {{ @$history->location->name }}
                                @if(@$history->obs)
                                    <div><small>{{ @$history->obs }}</small></div>
                                @endif
                            </td>
                            <td>{{ @$history->ot_code }}</td>
                            <td>
                                @if(@$history->action == "transfer")
                                    {{ @$history->stock_low }}
                                @else
                                    <div><b>Baixa: </b>{{ @$history->stock_low }} </div>
                                @endif
                                
                                <div><small>Total: </small> {{@$history->stock}} </div>

                            </td>

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
