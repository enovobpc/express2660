@if($row->deleted_at)
    <div class="text-red">
        {{ str_limit($row->sender_name, 40) }}
        <br/>
        <small class="italic fw-300">
            @if(Setting::get('shipment_list_show_address'))
                {{ $row->sender_address }}<br/>
            @endif
            @if(@$showIconFlag)
                <i class="flag-icon flag-icon-{{ $row->sender_country }}"></i>
            @endif
            {{ $row->sender_zip_code }} {{ $row->sender_city }}, {{ strtoupper($row->sender_country) }}
            {{-- @if($row->sender_phone)
                - {{ $row->sender_phone }}
            @endif --}}
        </small>
    </div>
@else
    {{ str_limit($row->sender_name, 40) }}
    <br/>
    @if($row->without_pickup)
        <small class="text-orange italic fw-300 bold">
            Sem recolha - entrega armazém
        </small>
    @else
        <small class="text-muted italic fw-300">
            @if(Setting::get('shipment_list_show_address'))
                {{ $row->sender_address }}<br/>
            @endif
            @if(@$showIconFlag)
                <i class="flag-icon flag-icon-{{ $row->sender_country }}"></i>
            @endif
            {{ $row->sender_zip_code }} {{ $row->sender_city }}, {{ strtoupper($row->sender_country) }}
           {{--  @if($row->sender_phone)
            - {{ $row->sender_phone }}
            @endif --}}
        </small>
    @endif
@endif