{{ str_limit($row->recipient_name, 60) }}<br/>
<i class="text-muted">
    {{ $row->recipient_zip_code }} {{ $row->recipient_city }}
    @if($row->recipient_phone)
    - {{ str_replace(' ', '', $row->recipient_phone) }}
    @endif
</i>