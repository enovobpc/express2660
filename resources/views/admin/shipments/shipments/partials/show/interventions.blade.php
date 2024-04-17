<div class="customer-support">
@if(@$shipment->interventions->isEmpty())
    <div class="text-center text-muted p-5 m-t-40">
        <h4><i class="fas fa-info-circle"></i> Não há registo de intervenções para este serviço.</h4>
        <a href="{{ route('admin.shipments.interventions.create', $shipment->id) }}"
           class="btn btn-default btn-sm m-t-20 m-b-40"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> Registar Intervenção
        </a>
    </div>
@else
    <table id="datatable" class="table m-b-0">
        <tr>
            <th class="bg-gray-light w-130px">Registo</th>
            <th class="bg-gray-light">Assunto</th>
            <th class="bg-gray-light">Ação Tomada</th>
            <th class="bg-gray-light w-1">Ações</th>
        </tr>
        @foreach($shipment->interventions as $item)
            <tr>
                <td>
                    {{ $item->created_at->format('Y-m-d H:i') }}<br/>
                    <small class="text-muted italic">{{ @$item->user->name}}</small>
                </td>
                <td>{{ $item->subject }}</td>
                <td>{!! nl2br($item->action_taken) !!}</td>
                <td class="text-center">
                    <a href="{{ route('admin.shipments.interventions.edit', [$item->shipment_id, $item->id]) }}"
                        data-toggle="modal"
                        data-target="#modal-remote"
                        class="text-green">
                        <i class="fas fa-pencil-alt"></i>
                     </a>
                </td>
            </tr>
        @endforeach
    </table>

    <a href="{{ route('admin.shipments.interventions.create', $shipment->id) }}"
        class="btn btn-xs btn-success"
        data-toggle="modal"
        data-target="#modal-remote">
            <i class="fas fa-plus"></i> Registar Intervenção
    </a>
@endif
</div>