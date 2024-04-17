<button class="btn btn-xs btn-block bg-info code-read" style="color: #fff"
        data-address="{{ strtolower($row->sender_address).', '.$row->sender_zip_code .' ' . strtolower($row->sender_city).', '. trans('country.'.$row->recipient_country) }}"
        data-name="{{ $row->sender_name }}"
        data-id="{{ $row->id }}"
        data-trk="{{ $row->tracking_code }}">
    Ler Morada
</button>