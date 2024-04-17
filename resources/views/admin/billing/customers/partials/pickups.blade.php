<div class="box no-border">
    <div class="box-body p-t-0">
        @include('admin.billing.customers.partials.pickups_filters')
        <div class="table-responsive m-t-10">
            <table id="datatable-pickups" class="table table-striped table-dashed table-hover table-condensed">
                <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-95px">Nº Pedido</th>
                    <th>Local Recolha</th>
                    <th>Destinatário</th>
                    <th class="w-1">Serv.</th>
                    <th class="w-60px">Info</th>
                    <th>Estado</th>
                    <th class="w-85px">Envio Gerado</th>
                    <th class="w-5px">Taxa</th>
                    <th style="width: 45px"></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="selected-rows-action hide">
            {{--<button class="btn btn-sm btn-danger m-r-5" data-toggle="modal" data-target="#modal-mass-destroy">
                <i class="fas fa-trash-alt"></i> Apagar
            </button>--}}
            {{ Form::open(array('route' => 'admin.shipments.selected.destroy')) }}
            <button class="btn btn-sm btn-danger m-r-5" data-action="confirm" data-title="Apagar selecionados">
                <i class="fas fa-trash-alt"></i> Apagar
            </button>
            {{ Form::close() }}
            <div class="pull-left">
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
                            <a href="#" data-toggle="modal" data-target="#modal-mass-update-pickups">
                                <i class="fas fa-fw fa-pencil-alt"></i> Editar/Corrigir Envios Selecionados
                            </a>
                        </li>
                    </ul>
                </div>
                @include('admin.billing.customers.modals.mass_update_pickups')
                @include('admin.shipments.shipments.modals.assign.customer')
                @endif
            </div>
            <div>
                <a href="{{ route('admin.billing.customers.shipments.selected.update-billing-date', [$customer->id]) }}" class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-update-billing-date-pickup">
                    <i class="fas fa-calendar-alt"></i> Alterar Data Faturação
                </a>
            </div>
            <?php $pickupTab = 1?>
            @include('admin.billing.customers.modals.update_billing_date')
        </div>
    </div>
</div>