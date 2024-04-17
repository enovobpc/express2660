<div class="box no-border">
    <div class="box-body p-t-0">
        @if(!$customer->count_cod)
            <p class="text-center text-muted padding-40 m-t-50 m-b-50">
                <i class="fas fa-info-circle"></i> Não há envios com pagamento no destino em {{ trans('datetime.month.'.$month) }} de {{ $year }}
            </p>
        @else
        <div class="table-responsive m-t-10">
            <table id="datatable-tab-cod" class="table table-condensed table-striped table-dashed table-hover">
                <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-80px">TRK</th>
                    <th>Remetente</th>
                    <th>Destinatário</th>
                    <th class="w-1">Serv.</th>
                    <th class="w-1">Remessa</th>
                    <th class="w-50px">Info</th>
                    <th>Estado</th>
                    <th>Valor</th>
                    <th class="w-1"></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="selected-rows-action hide">
            <div>
                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                    <button class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-assign-customer">
                        <i class="fas fa-user-plus"></i> Associar a Cliente
                    </button>
                    @include('admin.shipments.shipments.modals.assign.customer')
                @endif
            </div>
            <div>
                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                    <div class="btn-group btn-group-sm dropup m-l-5">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Alterar... <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                                    <a href="#" data-toggle="modal" data-target="#modal-assign-customer">
                                        <i class="fas fa-user-plus"></i> Associar envios a outro Cliente
                                    </a>
                                @endif
                            </li>
                            <li>
                                <a href="#" data-toggle="modal" data-target="#modal-mass-update">
                                    <i class="fas fa-fw fa-pencil-alt"></i> Editar/Corrigir Envios Selecionados
                                </a>
                            </li>
                        </ul>
                    </div>
                    @include('admin.billing.customers.modals.mass_update')
                    @include('admin.shipments.shipments.modals.assign.customer')
                @endif
            </div>
        </div>
        @endif
    </div>
</div>