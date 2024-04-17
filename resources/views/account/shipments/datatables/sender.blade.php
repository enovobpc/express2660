{{ str_limit($row->sender_name, 40) }}
<br/>
@if($row->without_pickup)
    <small class="italic fw-300 bold" style="color: #ff851b !important">
         Não recolher (levo à agência)
    </small>
@else
    <small class="text-muted italic">
        {{ $row->sender_zip_code }} {{ $row->sender_city }}
    </small>
@endif