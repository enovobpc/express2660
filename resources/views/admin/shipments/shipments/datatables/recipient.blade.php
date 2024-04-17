@if($row->deleted_at)
    <div class="text-red">
        {{ str_limit($row->recipient_name, 60) }}<br/>
        <i>
            @if(Setting::get('shipment_list_show_address'))
                {{ $row->recipient_address }}<br/>
            @endif

            @if(@$showIconFlag)
                <i class="flag-icon flag-icon-{{ $row->recipient_country }}"></i>
            @endif
            {{ $row->recipient_zip_code }} {{ $row->recipient_city }}, {{ strtoupper($row->recipient_country) }}
            {{-- @if($row->recipient_phone)
                - {{ str_replace(' ', '', $row->recipient_phone) }}
            @endif --}}
        </i>
    </div>
@else
    {{ str_limit($row->recipient_name, 60) }}<br/>
    <small class="text-muted italic fw-300">
        @if(Setting::get('shipment_list_show_address'))
            {{ $row->recipient_address }}<br/>
        @endif
        @if(@$showIconFlag)
            <i class="flag-icon flag-icon-{{ $row->recipient_country }}"></i>
        @endif
        {{ $row->recipient_zip_code }} {{ $row->recipient_city }}, {{ strtoupper($row->recipient_country) }}
        {{-- @if($row->recipient_phone)
        - {{ str_replace(' ', '', $row->recipient_phone) }}
        @endif --}}
    </small>
    @if(!Setting::get('shipment_list_detail_master') && $row->children_type == \App\Models\Shipment::TYPE_MASTER && !$row->type)
        <div class="details-control text-orange fs-12" style="cursor: pointer"><i class="fas fa-plus-square"></i> Ver todos os destinos</div>
    @endif
@endif