<div class="box no-border">
    <div class="box-body">
        @if(empty($customer->covenants))
            <p class="text-center text-muted padding-40 m-t-50 m-b-50">
                <i class="fas fa-info-circle"></i>
                Este cliente não tem definidas avenças mensais em {{ trans('datetime.month.'.$month) }} de {{ $year }}
            </p>
        @else
            <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-covenants">
                <li>
                    {{--<a href="{{ route('admin.billing.customers.shipments.update-prices', [$customer->id, 'month' => $month, 'year' => $year]) }}"
                       class="btn btn-sm btn-success"
                       data-method="post" data-confirm="Confirma a atualização de preços?<br/><br/><small class='text-red m-t-10px'><i class='fas fa-exclamation-triangle'></i> Envios com preços superiores ao calculado, não serão alterados.<br/><i class='fas fa-exclamation-triangle'></i> Não serão calculados preços para envios com pagamento no destino.</small>" data-confirm-title="Confirmar atualização de preços" data-confirm-label="Atualizar" data-confirm-class="btn-success">
                        <i class="fas fa-sync-alt"></i> Faturar todas Avenças
                    </a>--}}
                </li>
            </ul>
            <table id="datatable-covenants" class="table table-condensed table-striped table-dashed table-hover">
                <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-1"></th>
                    <th class="w-90px">Tipo Avença</th>
                    <th>Descrição</th>
                    <th class="w-65px">Máx.Envios</th>
                    <th class="w-140px">Serviço</th>
                    <th class="w-65px">Valor</th>
                    <th class="w-65px">Início</th>
                    <th class="w-65px">Termo</th>
                    <th class="w-100px">Fatura</th>
                    <th class="w-65px">Ações</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="selected-rows-action hide">
                {{--{{ Form::open(array('route' => 'admin.shipments.selected.destroy')) }}
                <button class="btn btn-sm btn-danger m-r-5" data-action="confirm" data-title="Apagar selecionados">
                    <i class="fas fa-trash-alt"></i> Apagar
                </button>
                {{ Form::close() }}--}}
                <div class="pull-left">
                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                        <a href="{{ route('admin.billing.customers.shipments.selected.billing.edit', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period, 'select' => 'covenants']) }}" data-url-target="billing-selected" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-remote-xl">
                            <i class="fas fa-file-alt"></i> Faturar apenas selecionados
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>