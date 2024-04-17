{{ str_limit($row->sender_name, 40) }}
<br/>
<small>
    <i class="text-muted">
        {{ $row->sender_zip_code }} {{ $row->sender_city }}
        @if($row->sender_country)
            <i class="flag-icon flag-icon-{{ $row->sender_country }}"></i>
        @else
            <i class="fas fa-exclamation-triangle text-red"></i>
        @endif
    </i>
</small>