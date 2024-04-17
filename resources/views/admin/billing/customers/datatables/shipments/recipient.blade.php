{{ str_limit($row->recipient_name, 60) }}<br/>
<small>
    <i class="text-muted">
        {{ $row->recipient_zip_code }} {{ $row->recipient_city }}
        @if($row->recipient_country)
        <i class="flag-icon flag-icon-{{ $row->recipient_country }}"></i>
        @else
        <i class="fas fa-exclamation-triangle text-red"></i>
        @endif
    </i>
</small>