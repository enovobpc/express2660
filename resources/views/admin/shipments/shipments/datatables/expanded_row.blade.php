@if(@$shipments)
    <div {{-- style="margin-top: -68px;position: relative;" --}}>
        <table class="table table-condensed m-0">
            <tr>
                <th class="bg-gray w-1"></th>
                <th class="bg-gray w-100px">TRK</th>
                <th class="bg-gray w-95px">Referência</th>
                <th class="bg-gray">Remetente</th>
                <th class="bg-gray">Destino</th>
                <th class="bg-gray w-80px">Remessa</th>
                <th class="bg-gray w-130px">Entrega</th>
                <th class="bg-gray w-40px">Viagem</th>
                <th class="bg-gray w-1">Estado</th>
                {{-- <th class="bg-gray w-1">Ações</th> --}}
            </tr>
            @foreach ($shipments as $row)
            <tr>
                <td>@include('admin.partials.datatables.select')</td>
                <td>@include('admin.shipments.shipments.datatables.tracking')</td>
                <td>@include('admin.shipments.shipments.datatables.reference')</td>
                <td>@include('admin.shipments.shipments.datatables.sender')</td>
                <td>@include('admin.shipments.shipments.datatables.recipient')</td>
                <td>@include('admin.shipments.shipments.datatables.volumes')</td>
                <td>@include('admin.shipments.shipments.datatables.delivery_date')</td>
                <td>@include('admin.shipments.shipments.datatables.vehicle')</td>
                <td>@include('admin.shipments.shipments.datatables.status')</td>
                {{-- <td>@include('admin.shipments.shipments.datatables.actions')</td> --}}
            </tr>
            @endforeach
        </table>
    </div>
@else
    <div>
    Sem serviços para expandir.
    </div>
@endif