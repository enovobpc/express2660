@if($row->sense == 'credit')
    @if($row->doc_type == 'credit-note')
    <b class="text-blue">{{ money($row->total, Setting::get('app_currency')) }}</b>
    @else
    <b class="text-blue">{{ money($row->total, Setting::get('app_currency')) }}</b>
    @endif
@endif