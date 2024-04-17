@if(@$row->received_date)
    {{ @$row->received_date->format('Y-m-d') }}
@endif