<ul class="list-inline pull-left">
    <li style="width: 360px">
        <strong style="display: inline-block; float: left; padding: 5px;">Data Envio</strong>
        <div class="input-group input-group-sm" style="float: left; width: 240px;">
            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : date('Y-m-d'), ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
            <span class="input-group-addon">até</span>
            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : date('Y-m-d'), ['class' => 'form-control datepicker filter-datatable', 'autocomplete' => 'field-1']) }}
        </div>
    </li>
</ul>
<table class="table table-condensed pending-shipments">
    <tr class="bg-gray-light">
        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
        <th class="w-120px">Envio</th>
        <th>Remetente</th>
        <th>Destinatário</th>
        <th>Serviço</th>
        <th>Data</th>
        <th class="w-1">Volumes</th>
    </tr>
    @foreach($shipments as $shipment)
    <tr data-shipment-trk="{{ $shipment->tracking_code }}">
        <td>{{ Form::checkbox('row-select', $shipment->id, null, array('class' => 'row-select')) }}</td>
        <td class="w-120px">
            <b>{{ $shipment->tracking_code }}</b>
            <br/>
            <span class="label" style="background: {{ @$shipment->senderAgency->color }}" data-toggle="tooltip" title="{{ @$shipment->senderAgency->name }}">
                {{ @$shipment->senderAgency->code }}
            </span>
            &nbsp;<i class="fas fa-angle-right"></i>
            <span class="label" style="background: {{ @$shipment->recipientAgency->color }}" data-toggle="tooltip" title="{{ @$shipment->recipientAgency->name }}">{{ @$shipment->recipientAgency->code }}</span>
        </td>
        <td>
            {{ str_limit($shipment->sender_name, 40) }}
            <br/>
            <i class="text-muted">
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                @if($shipment->sender_phone)
                    - {{ $shipment->sender_phone }}
                @endif
            </i>
        </td>
        <td>
            {{ str_limit($shipment->recipient_name, 60) }}<br/>
            <i class="text-muted">
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                @if($shipment->recipient_phone)
                    - {{ str_replace(' ', '', $shipment->recipient_phone) }}
                @endif
            </i>
        </td>
        <td>{{ @$shipment->service->code }}</td>
        <td>{{ @$shipment->date }}</td>
        <td class="w-1 text-center">
            <span class="count-readed">0</span>/<b>{{ $shipment->volumes }}</b>
        </td>
    </tr>
    @endforeach
</table>
<div class="selected-rows-action hide">
    <div>
        <a href="#" data-toggle="modal" data-target="#modal-assign-status" class="btn btn-sm btn-default">
            <i class="fas fa-sync-alt"></i> Alterar Estado dos Envios
        </a>
        @include('admin.traceability.modals.assign_status')
    </div>
</div>