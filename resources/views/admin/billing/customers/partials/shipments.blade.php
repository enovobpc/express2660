<div class="box no-border">
    <div class="box-body p-t-0">
        @include('admin.billing.customers.partials.shipments_filters')
        <div class="table-responsive m-t-10">
            <table id="datatable-shipments" class="table table-striped table-dashed table-hover table-condensed">
                <thead>
                    <tr>
                        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                        <th></th>
                        <th class="w-80px">TRK</th>
                        <th class="w-30px">Referência</th>
                        <th>Remetente</th>
                        <th>Destinatário</th>
                        <th class="w-1">Serv.</th>
                        <th class="w-1">Remessa</th>
                        @if(Setting::get('shipment_list_show_delivery_date'))
                        <th class="w-80px">Entrega</th>
                        @else
                        <th class="w-60px">Info</th>
                        @endif
                        @if(Setting::get('app_mode') == 'cargo')
                        <th>Viagem</th>
                        @endif
                        <th>Estado</th>
                        @if(config('app.source') == 'horasambulantes')
                        <th>Cobrança</th>
                        @endif
                        <th class="w-45px">Valor</th>
                        <th class="w-1">
                            <span>
                                <i class="fas fa-check-circle" data-toggle="tooltip" title="Conferir Envio"></i>
                            </span>
                        </th>
                        <th>Fatura</th>
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
                    <a href="{{ route('admin.billing.customers.shipments.selected.billing.edit', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period]) }}" data-url-target="billing-selected" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-remote-xl">
                        <i class="fas fa-file-alt"></i> Faturar selecionados
                    </a>
                @endif
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
            <div style="float: left;">
                <a href="{{ route('admin.billing.customers.shipments.selected.update-billing-date', [$customer->id]) }}" class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-update-billing-date">
                    <i class="fas fa-calendar-alt"></i> Alterar Data Faturação
                </a>

                <a href="#" class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-confirm-selected-shipments">
                    <i class="fas fa-check-circle"></i> Conferir
                </a>

                <a href="{{ route('admin.printer.shipments.selected', [$customer->id]) }}" class="btn btn-sm btn-default m-l-5" data-toggle="datatable-action-url" target="_blank">
                    <i class="fas fa-print"></i> Imprimir
                </a>

                <a href="{{ route('admin.export.billing.customers.shipments', Request::all() + [$customer->id]) }}" class="btn btn-sm btn-default m-l-5" data-toggle="datatable-action-url" target="_blank">
                    <i class="fas fa-file-excel"></i> Exportar
                </a>
            </div>
            <div class="selected-rows-totals">
                <div class="selected-rows-totals">
                    <h4>
                        <small>@trans('Total')</small><br/>
                        <span class="dt-sum-total bold"></span><b>€</b>
                    </h4>
                    <div class="clearfix"></div>
                </div>
            </div>
            @include('admin.billing.customers.modals.confirm_shipments')
            @include('admin.billing.customers.modals.update_billing_date')
        </div>
    </div>
</div>