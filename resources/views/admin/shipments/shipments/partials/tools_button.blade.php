<div class="btn-group btn-group-sm" role="group">
    <div class="btn-group btn-group-sm" role="group">
        @if(!app_mode_transfers())
        <a href="{{ route('admin.operator.tasks.index') }}" class="btn btn-default"  data-toggle="modal" data-target="#modal-remote-xl">
            <i class="fas fa-dolly"></i> @trans('Recolhas')
        </a>
        @endif
        <a href="{{ route('admin.maps.operators') }}" class="btn btn-default"  data-toggle="modal" data-target="#modal-remote-xl">
            <i class="fas fa-map-marker-alt"></i> @trans('Localizar')
        </a>
        <button type="button" class="btn btn-default dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> @trans('A sincronizar')"
        >
            <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="#" data-empty="1" data-toggle="modal" data-target="#modal-shipments-scheduled">
                    <i class="fas fa-fw fa-clock"></i> @trans('Agendamentos Periódicos')
                </a>
            </li>
            <li role="separator" class="divider"></li>
            @if(Auth::user()->hasRole(config('permissions.role.admin')) || Auth::user()->can('importer'))
            <li>
                <a href="{{ route('admin.importer.index') }}">
                    <i class="fas fa-fw fa-upload"></i> @trans('Importador de Ficheiros Excel')
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('admin.shipments.expenses.import.modal') }}" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-fw fa-upload"></i> @trans('Importar encargos via excel')
                </a>
            </li>
            @if(hasPermission('export_shipments'))
                <li role="separator" class="divider"></li>
                <li>
                    <a href="{{ route('admin.export.shipments', Request::all()) }}" data-toggle="export-url">
                        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar listagem detalhada')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.export.shipments.alternative', Request::all()) }}" data-toggle="export-url">
                        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar listagem simples')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.export.shipments.dimensions', Request::all()) }}" data-toggle="export-url">
                        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar detalhe mercadoria')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.printer.shipments.selected', Request::all()) }}" data-toggle="export-url">
                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir listagem atual')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.printer.shipments.selected', ['customer'] + Request::all()) }}" target="_blank" data-toggle="export-url">
                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir listagem (por cliente)')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.printer.shipments.cargo-manifest', ['grouped'], Request::all()) }}" data-toggle="export-url">
                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir mapa carga atual')
                    </a>
                </li>
            @endif
            <li role="separator" class="divider"></li>
            <li>
                <a href="{{ route('admin.traceability.get.manifest') }}" data-toggle="modal" data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-users"></i> @trans('Manifesto Entrega por Motorista')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.shipments.generic-transportation-guide.edit') }}" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-fw fa-file-alt"></i> @trans('Guia Genérica por Viatura')
                </a>
            </li>
            @if(hasModule('webservices') && (Auth::user()->hasRole(config('permissions.role.admin')) || Auth::user()->can('webservices')))
                <li role="separator" class="divider"></li>
                <li>
                    <a href="{{ route('admin.webservices.sync.shipments') }}" data-toggle="modal" data-target="#modal-remote">
                        <i class="fas fa-fw fa-sync-alt"></i> @trans('Sincronizar Envios numa data')
                    </a>
                </li>
                <li>
                    <a href="#" class="btn-webservice-sync">
                        <i class="fas fa-fw fa-sync-alt"></i> @trans('Sincronizar Estados Agora')
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="{{ route('admin.traceability.assign.ctt-correios') }}" data-toggle="modal" data-target="#modal-remote">
                        <i class="fas fa-fw fa-barcode"></i> @trans('Atribuir código CTT correios')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.shipments.acceptance-certificates') }}" data-toggle="modal" data-target="#modal-remote">
                        <i class="fas fa-fw fa-check"></i> @trans('Certificados de Aceitação CTT')
                    </a>
                </li>

            @elseif(Auth::user()->isGuest())
                <li>
                    <a href="#" style="color: #ddd">
                        <i class="fas fa-fw fa-sync-alt"></i> @trans('Sincronizar Envios numa data')
                    </a>
                </li>
                <li>
                    <a href="#" style="color: #ddd">
                        <i class="fas fa-fw fa-sync-alt"></i> @trans('Sincronizar Todos os Estados')
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('admin.shipments.generate-shipments-from-pickups') }}"
                   data-method="post"
                   data-confirm="@trans('Pretende gerar ou associar automaticamente as recolhas ao respetivo envio?')"
                   data-confirm-title="@trans('Gerar envios automático')"
                   data-confirm-label="@trans('Gerar Envios')"
                   data-confirm-class="btn-success">
                    <i class="fas fa-fw fa-sync-alt"></i> @trans('Gerar envios das recolhas')
                </a>
            </li>
        </ul>
    </div>
    <button type="button" class="btn btn-filter-datatable btn-default">
        <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
    </button>
</div>