<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="far fa-clock"></i> @trans('Histórico de Edições')</h4>
</div>
<div class="modal-body">
    @if($changes->isEmpty())
        <h4 class="m-t-20 text-center text-muted"><i class="fas fa-info-circle"></i> @trans('Não há histórico de edições para apresentar.')</h4>
    @else
    <table class="table table-condensed">
        <tr class="bg-gray-light">
            <th class="w-80px">@trans('Ação')</th>
            <th class="w-90px">@trans('Data')</th>
            <th>@trans('Alterado Por')</th>
            <th class="w-60">
                @trans('Alterações')
                <a href="#" class="pull-right expand-all" style="font-weight: normal">@trans('Expandir/Recolher tudo')</a>
            </th>
        </tr>

        <?php $limit = 200 ?>
        @foreach($changes as $key => $change)
            @if($key <= $limit )
        <tr>
            <td>
                <span class="label label-info">{{ trans('admin/change_log.actions.' . $change->action) }}</span>
            </td>
            <td>{!! $change->created_at->format('Y-m-d').'<br/><small>'. $change->created_at->format('H:i:s') .'</small>' !!}</td>
            <td>
                @if(empty($change->user_id) && $change->customer_id)
                    <i class="fas fa-user" data-toggle="tooltip" title="Cliente"></i>
                @endif

                @if($change->is_api)
                    <i class="fas fa-plug text-blue" data-toggle="tooltip" title="Via API"></i>
                @endif

                {{ $change->user_id ? @$change->user->name : ($change->customer_id ? ($change->customer->customer_id ? ($change->customer->code .' - '. $change->customer->name) : $change->customer->name) : 'Sistema Automático') }}
            </td>
            <td>
                <div style="display: none">
                    <table class="table table-condensed">
                        <tr>
                            <th>@trans('Campo')</th>
                            <th class="w-30">@trans('Valor Anterior')</th>
                            <th class="w-30">@trans('Novo Valor')</th>
                        </tr>
                    <?php
                        $oldValues = (array) $change->old;
                        $totalChanged = 0;
                    ?>
                    @if(!empty($change->new))
                        @foreach($change->new as $key => $newValue)
                            <?php
                                $totalChanged++;

                                if($key == 'status_id' && !empty($oldValues[$key])) {
                                    $oldValues[$key] = @$status[$oldValues[$key]];
                                    $newValue        = @$status[$newValue];
                                }

                                if($key == 'service_id' && !empty($oldValues[$key])) {
                                    $oldValues[$key] = @$services[$oldValues[$key]];
                                    $newValue        = @$services[$newValue];
                                }

                                if($key == 'provider_id' && !empty($oldValues[$key])) {
                                    $oldValues[$key] = @$providers[$oldValues[$key]];
                                    $newValue        = @$providers[$newValue];
                                }
                            ?>
                            <tr>
                                <td>{{ trans('admin/change_log.keys.'.$change->source . '.' . $key) }}</td>
                                <td style="word-break: break-all;">{!! empty(@$oldValues[$key]) ? '<i class="text-muted">Vazio</i>' : @$oldValues[$key] !!}</td>
                                <td style="word-break: break-all;">
                                    {!! empty($newValue) ? '<i class="text-muted">Vazio</i>' : (is_object($newValue) ? var_dump($newValue) : $newValue) !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">{{ print_r($change->toArray()) }}</td>
                        </tr>
                    @endif
                    </table>
                </div>
                <a href="#" class="expand-changed-fields" data-toggle-text='Ocultar campos alterados <i class="fas fa-angle-up"></i>'>@trans('Ver <b>:total</b> campos alterados', ['total' => $totalChanged]) <i class="fas fa-angle-down"></i></a>
            </td>
        </tr>
            @endif
            <?php $limit++ ?>
        @endforeach
    </table>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>

<style>
    #modal-remote-lg .table .table-condensed>tbody>tr>th,
    #modal-remote-lg .table .table-condensed>tbody>tr>td {
        padding: 0;
    }
</style>

<script>
    $('.expand-changed-fields').on('click', function(e){
        e.preventDefault();
        var toggleText = $(this).data('toggle-text');
        var tmp = $(this).html();
        $(this).data('toggle-text', tmp);
        $(this).html(toggleText);

        $(this).prev().slideToggle();
    })

    $('.expand-all').on('click', function (e) {
        e.preventDefault();
        $('.expand-changed-fields').trigger('click');
    })
</script>