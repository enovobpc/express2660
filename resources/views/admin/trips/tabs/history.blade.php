@foreach ($tripVehicles as $tripVehicle)
<div class="manifest-history-group">
    <table class="table table-condensed m-b-0">
        <tr>
            <th class="bg-gray w-90px">@trans('Viatura')</th>
            <th class="bg-gray w-90px">@trans('Reboque')</th>
            <th class="bg-gray">@trans('Motorista')</th>
            <th class="bg-gray w-90px brdlft">@trans('Data Início')</th>
            <th class="bg-gray w-50px">@trans('Hora')</th>
            <th class="bg-gray w-70px">@trans('Kms')</th>
            <th class="bg-gray w-90px brdlft">@trans('Data Fim')</th>
            <th class="bg-gray w-50px">@trans('Hora')</th>
            <th class="bg-gray w-70px">@trans('Kms')</th>
            <th class="bg-gray w-80px brdlft">@trans('Kms')</th>
        </tr>
        <tr style="font-weight: bold">
            <td>{{ @$tripVehicle->vehicle }}</td>
            <td>{{ $tripVehicle->trailer }}</td>
            <td>{{ @$tripVehicle->operator->name }}</td>
            <td class="brdlft">{{ $tripVehicle->start_at ? $tripVehicle->start_at->format('Y-m-d') : '' }}</td>
            <td>{{ $tripVehicle->start_at ? $tripVehicle->start_at->format('H:i') : '' }}</td>
            <td>{{ $tripVehicle->start_kms }}</td>
            <td class="brdlft">{{ $tripVehicle->end_at ? $tripVehicle->end_at->format('Y-m-d') : '' }}</td>
            <td>{{ $tripVehicle->end_at ? $tripVehicle->end_at->format('H:i') : '' }}</td>
            <td>{{ $tripVehicle->end_kms }}</td>
            <td class="brdlft">{{ $tripVehicle->end_kms - $tripVehicle->start_kms }}</td>
        </tr>
    </table>
    <div class="event-timeline">
        @if($tripVehicle->histories->isEmpty())
        <p class="p-t-10 p-b-10 p-l-15 italic text-muted"><i class="fas fa-info-circle"></i> @trans('Não há histórico nesta viagem.')</p>
        @else
        <table class="table table-condensed w-100">
            {{-- <tr>
                <th class="bg-gray w-1"></th>
                <th class="bg-gray w-95px">@trans('Data')</th>
                <th class="bg-gray w-60px">@trans('Hora')</th>
                <th class="bg-gray w-60px">@trans('Ação')</th>
                <th class="bg-gray">@trans('Observações')</th>
            </tr> --}}
            <?php $histories = $tripVehicle->histories->sortBy('date'); ?>
            @foreach ($histories as $history)
            <tr>
                <td class="w-90px">{{ $history->date ? $history->date->format('Y-m-d') : '' }}</td>
                <td class="w-90px">{{ $history->date ? $history->date->format('H:i') : '' }}</td>
                <td class="w-90px" style="color: {{ trans('admin/trips.history-actions.'.$history->action.'.color') }}">
                    <i class="fas {{ trans('admin/trips.history-actions.'.$history->action.'.icon') }}"></i> 
                    {{ trans('admin/trips.history-actions.'.$history->action.'.title') }}
                </td>
                <td>
                    {{ $history->obs}}
                    @if($history->target == 'Shipment')
                    <a href="{{ route('admin.shipments.show', $history->target_id) }}" 
                        data-toggle="modal" 
                        data-target="#modal-remote-xl" 
                        class="btn btn-xs btn-default">
                        @trans('Ver')
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>
@endforeach

<style>

    .brdlft {
        border-left: 2px solid #333
    }

    .manifest-history-group table {
        border: 1px solid #ddd !important;
        background: #fff;
    }

    .manifest-history-group table th {
        background: #ddd;
    }

    .event-timeline {
        /* margin-left: 30px; */
    border-left: 3px solid #999;
    }
</style>