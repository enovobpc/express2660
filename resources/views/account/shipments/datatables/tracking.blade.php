<a href="{{ route('account.shipments.show', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl"
   class="fs-13">
    {{ $row->tracking_code }}
</a>

@if(Setting::get('customer_show_provider_trk') && $row->provider_tracking_code)
    <small data-toggle="tooltip" title="Nº de Envio {{ camel_case($row->webservice_method) }}">
        @if($row->webservice_method == 'ctt')
            <?php $trk = explode(',',  $row->provider_tracking_code)?>
            {{ @$trk[0] }}
        @else
            <span style="max-width: 115px;
                overflow-y: auto;
                display: inline-block;
                text-overflow: ellipsis;">
                {{ $row->provider_tracking_code }}
            </span>
        @endif
    </small>
@endif

@if($row->is_collection)
    <div>
    <small>
        <i class="far fa-calendar-alt"></i> {{ $row->date }}
    </small>
    </div>
@else
    @if(Setting::get('customers_show_delivery_date'))
    <small>
        <i class="far fa-calendar-alt"></i> {{ $row->date }}
        @if($row->start_hour)
        <br/>
        <i class="far fa-clock"></i> {{ $row->start_hour }}
        @endif
    </small>
    @endif

    @if($row->type == \App\Models\Shipment::TYPE_RETURN)
        <span class="label bg-green" data-toggle="tooltip" title="Envio associado">
            <i class="fas fa-undo"></i> {{ $row->parent_tracking_code }}
        </span>
    @endif

    <br/>
    <a href="{{ route('account.shipments.show', [$row->id, 'tab' => 'status']) }}"  data-toggle="modal" data-target="#modal-remote-xl">
        @if($row->hasSyncError() && Setting::get('customers_show_webservice_errors'))
            <span class="label bg-red" style="background-color: #cc0000">
                <i class="fas fa-exclamation-triangle"></i> FALHOU
            </span>
        @elseif(!$row->is_closed && ($row->status_id == 1 || $row->status_id == 2))
            <span class="label" style="background-color: #cc0000" data-toggle="tooltip" title="Envio Ainda não fechado.">
                Por Fechar
            </span>
        @else
            <span class="label" style="background-color: {{ @$row->status->color }}">
                {{ !empty(@$row->status->{$nameTrans}) ? @$row->status->{$nameTrans} : @$row->status->name }}
            </span>
        @endif
    </a>
@endif