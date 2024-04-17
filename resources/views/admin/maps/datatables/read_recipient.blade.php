<button class="btn btn-xs btn-block bg-blue code-read m-b-5"
    data-address="{{ strtolower($row->recipient_address).', '.$row->recipient_zip_code .' ' . strtolower($row->recipient_city).', '. trans('country.'.$row->recipient_country) }}"
    data-name="{{ $row->recipient_name }}"
    data-id="{{ $row->id }}"
    data-trk="{{ $row->tracking_code }}">
    Ler Morada
</button>