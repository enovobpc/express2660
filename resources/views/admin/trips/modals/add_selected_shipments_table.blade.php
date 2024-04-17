@if($trips->isEmpty())
    <div class="text-center m-t-140 text-muted">
        @if(app_mode_cargo())
            <i class="fas fa-info-circle"></i> @trans('Não existem viagens disponíveis.')
        @else
            <i class="fas fa-info-circle"></i> @trans('Não existem mapas distribuição disponíveis.')
        @endif
    </div>
@else
    <table class="table table-condensed table-hover table-maps m-0">
        <tr>
            <th class="bg-gray w-1"></th>
            <th class="bg-gray w-1">
                @if(app_mode_cargo())
                @trans('Viagem')
                @else
                @trans('Folha')
                @endif
            </th>
            <th class="bg-gray">@trans('Início')</th>
            <th class="bg-gray">@trans('Termo')</th>
            <th class="bg-gray">@trans('Motorista')</th>
            <th class="bg-gray w-85px">@trans('Viatura')</th>
            <th class="bg-gray w-1 text-center" style="border-left: 2px solid #333"><i class="fas fa-fw fa-truck"></i></th>
            <th class="bg-gray w-1 text-center"><i class="fas fa-fw fa-boxes"></i></th>
            <th class="bg-gray w-1 text-center"><i class="fas fa-weight-hanging"></i></th>
            @if(app_mode_cargo())
            <th class="bg-gray w-1">LDM</th>
            @endif
        </tr>

        @foreach($trips as $trip)
            <tr>
                <td>
                    {{ Form::radio('trip_id', $trip->id, null, ['required']) }}
                </td>
                <td>
                    <a href="{{ route('admin.trips.show', $trip->id) }}" target="_blank">
                        {{ $trip->code }}
                    </a>
                </td>
                <td>
                    @if(app_mode_cargo())
                        <i class="flag-icon flag-icon-{{ $trip->start_country }}"></i>
                    @endif
                    {{ $trip->start_location }}
                    <br/>
                    <small>{{ $trip->start_date }} {{ $trip->start_hour }}</small>
                </td>
                <td>
                    @if(app_mode_cargo())
                        <i class="flag-icon flag-icon-{{ $trip->end_country }}"></i>
                    @endif
                    {{ $trip->end_location }}
                    <br/>
                    <small>{{ $trip->end_date }} {{ $trip->end_hour }}</small>
                </td>
                <td>
                    {{ @$trip->operator->name ? @$trip->operator->name : 'N/A' }}
                </td>
                <td>
                    {{ @$trip->vehicle }}<br/>
                    <small>{{ @$trip->trailer }}</small>
                </td>
                <td style="border-left: 2px solid #333" class="text-center">
                    {{ @$trip->shipments->count() }}
                </td>
                <td class="text-center">
                    {{ @$trip->shipments->sum('volumes') }}
                </td>
                <td class="text-center">
                    {{ @$trip->shipments->sum('weight') }}
                </td>
                @if(app_mode_cargo())
                <td class="text-center">
                    {{ @$trip->shipments->sum('ldm') }}
                </td>
                @endif
            </tr>
        @endforeach
    </table>
@endif