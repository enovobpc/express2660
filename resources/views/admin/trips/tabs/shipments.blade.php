{{--Quando adiciona agrupado nao adiciona todos os pedidos--}}
<div class="pull-left">
    <div class="btn-group pull-left">
        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <i class="fas fa-plus"></i> @trans('Adicionar Serviços') <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="{{ route('admin.trips.shipments.map.show', @$trip->id) }}"
                    data-toggle="modal"
                    data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-map"></i> @trans('Adicionar do Mapa')
                </a>
            </li>
            <li>
                <a href="#"
                   data-empty="1"
                   data-toggle="modal"
                   data-target="#modal-select-shipments">
                    <i class="fas fa-fw fa-list"></i> @trans('Adicionar da Lista')
                </a>
            </li>
        </ul>
    </div>
</div>

@if($shipments->count() > 25)
    <a href="#" class="btn btn-sm btn-default pull-right m-r-5"
       style="opacity: 0.5"
       data-toggle="tooltip"
       title="Não é possível gerar rotas otimizadas para mais de 25 registos.">
        <i class="fas fa-route"></i>
        @if($cargoAppMode)
        @trans('Otimizar Viagem')
        @else
        @trans('Otimizar Entregas')
        @endif
    </a>
@else
    <a href="{{ route('admin.trips.shipments.optimize.edit', $trip->id) }}"
       data-toggle="modal"
       data-target="#modal-remote-xs"
       class="btn btn-sm btn-default pull-right m-r-5">
        <i class="fas fa-route"></i>
        @if($cargoAppMode)
        @trans('Otimizar Viagem')
        @else
        @trans('Otimizar Entregas')
        @endif
    </a>
@endif

@if(!$cargoAppMode)
<a href="{{ route('admin.trips.shipments.notify', $trip->id) }}"
   data-method="post"
   data-confirm-title="@trans('Notificar Destinatários')"
   data-confirm-label="@trans('Notificar')"
   data-confirm-class="btn-success"
   data-confirm="@trans('Pretende notificar por SMS/E-mail a hora prevista de entrega a todos os destinatários?')"
   class="btn btn-sm btn-default pull-right m-r-5">
    <i class="fas fa-mobile-alt"></i> @trans('Notificar SMS')
</a>
@endif

@if($cargoAppMode)
    <a href="{{ route('admin.trips.change-trailer.edit', $trip->id) }}"
       data-toggle="modal"
       data-target="#modal-remote"
       class="btn btn-sm btn-default pull-right m-r-5"
       class="btn btn-sm btn-default pull-right m-r-5 m-t-9 pull-right">
        <i class="fas fa-sync"></i> <i class="fas fa-truck-loading"></i> @trans('Troca')
    </a>
@endif

<a href="{{ route('admin.trips.change-status.edit', $trip->id) }}"
   data-toggle="modal"
   data-target="#modal-remote"
   class="btn btn-sm btn-default pull-right m-r-5"
   class="btn btn-sm btn-default pull-right m-r-5 m-t-9 pull-right">
    @if($cargoAppMode)
        <i class="fas fa-sync"></i> @trans('Alterar estados viagem')
    @else
        <i class="fas fa-sync"></i> @trans('Alterar estado envios')
    @endif
</a>



<div class="clearfix"></div>
<div class="shipments-table">
    @if($cargoAppMode)
    @include('admin.trips.partials.shipments_table_cargo')
    @else
    @include('admin.trips.partials.shipments_table')
    @endif
</div>

<div class="selected-rows-action hide">
    <div>
        <button class="btn btn-sm btn-primary" type="button" data-toggle="modal"
            data-target="#modal-edit-history">
            <i class="fas fa-tasks"></i> @trans('Alterar estado')
        </button>
    </div>
    <a href="{{ route('admin.trips.shipments.add-selected') }}"
        class="btn btn-default btn-sm m-r-0 m-l-5"
            data-toggle="modal"
            data-target="#modal-remote-lg"
            data-action-url="datatable-action-url">
            <i class="fas fa-fw fa-exchange-alt"></i> @trans('Transferir de mapa')
    </a>
    <a href="{{ route('admin.trips.shipments.confirm-docs-reception', [$trip->id]) }}"
       class="btn btn-default btn-sm m-r-2 m-l-2"
       data-method="post"
       data-confirm="@trans('Confirma a recepção dos documentos de transporte em escritório?')"
       data-confirm-title="@trans('Confirmar recepção documentos')"
       data-confirm-label="@trans('Confirmar')"
       data-confirm-class="btn-success"
       data-toggle="datatable-action-url">
        <i class="fas fa-check"></i>
        @if($cargoAppMode)
        @trans('CMR Recebido')
        @else
        @trans('Guia Recebida')
        @endif
    </a>
    <div class="btn-group btn-group-sm dropup">
        <button type="button" class="btn btn-sm btn-default  m-l-5 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-print"></i> @trans('Imprimir') <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="{{ route('admin.printer.shipments.labels') }}" data-toggle="datatable-action-url" target="_blank">
                    @trans('Etiquetas')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.shipments.transport-guide') }}" data-toggle="datatable-action-url" target="_blank">
                    @trans('Guia de Transporte')
                </a>
            </li>
        </ul>
    </div>
    @include('admin.shipments.history.mass_edit')
</div>

@if($trip->parent_code || $trip->children_code)
    {{--<div class="panel {{ $trip->parent_code ? 'panel-default' : 'panel-info' }} panel-collapse m-t-15 m-b-0">
        <div class="panel-heading" role="tab" id="return-map-details-header">
            <h4 class="panel-title"  data-toggle="collapse" data-parent="#return-map-details-header" href="#return-map-details">

                    <i class="fas fa-caret-down pull-right"></i>
                    @if($trip->parent_code)
                        <i class="fas fa-arrow-right"></i> Pré-visualização da Viagem Inicial <b>{{ $trip->parent_code }}</b>
                    @else
                        Retorno associado: <b>{{ $trip->children_code }}</b>
                    @endif
                <a href="{{ route('admin.trips.show', $trip->children_id) }}" class="btn btn-xs btn-default"><i class="fas fa-search"></i> Ver</a>
            </h4>
        </div>
        <div id="return-map-details" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body m-0 p-0">
                <div class="shipments-table" style="margin-top: -10px">
                    @include('admin.trips.partials.shipments_table')
                </div>
            </div>
        </div>
    </div>--}}

    @if($trip->parent_code)
    <a href="{{ route('admin.trips.show', $trip->parent_id) }}" style="text-decoration: none">
        <div class="alert btn-footer-toggle m-t-15 m-b-0">
            <i class="fas fa-fw fa-arrow-right"></i> @trans('Viagem Inicial') <b>{{ $trip->parent_code }}</b>
            <button class="btn btn-xs btn-default" style="margin-top: -4px; margin-bottom: -2px;">
                <i class="fas fa-search"></i> @trans('Ver')
            </button>
        </div>
    </a>
    @else
    <a href="{{ route('admin.trips.show', $trip->children_id) }}" style="text-decoration: none">
        <div class="alert btn-footer-toggle m-t-15 m-b-0">
            <i class="fas fa-fw fa-undo"></i> @trans('Retorno associado'): <b>{{ $trip->children_code }}</b>
            <button class="btn btn-xs btn-default" style="margin-top: -4px; margin-bottom: -2px;">
                <i class="fas fa-search"></i> @trans('Ver')
            </button>
        </div>
    </a>
    @endif

@endif


<style>
    .status-date {
        line-height: 12px;
        margin-top: 4px;
        font-size: 11px;
        display: inline-block;
    }

    .shipments-totals {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 3px;
        margin: 10px 10px 0 0;
        padding: 0 5px;
        float: right;
    }

    .shipments-totals h4 {
        margin: 0;
    }

    .shipments-totals li {
        padding-right: 10px;
    }

    .selected-rows-action {
        position: relative;
        margin-bottom: -55px;
        left: 0px;
        right: 16px;
        top: -60px;
    }

    .btn-footer-toggle {
        margin: 15px -10px -10px;
        border-radius: 0 0 3px 3px;
        color: #0A3354;
        background: #cadbec;
        border: 1px solid #c4d4e5;
        border-top: 1px solid #91a9ba;
    }

    .btn-footer-toggle:hover {
        background: #bbc4cd;
        border: 1px solid #aeb9c5;
        border-top: 1px solid #7b8f9d;

    }
</style>