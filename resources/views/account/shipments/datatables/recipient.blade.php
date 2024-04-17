{{ str_limit($row->recipient_name, 35) }}<br/>
<small class="text-muted italic">
    {{ $row->recipient_zip_code }} {{ $row->recipient_city }}
</small>