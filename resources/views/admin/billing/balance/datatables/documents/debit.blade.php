@if($row->sense == 'debit')
    <div class="bold" data-total="{{ $row->total }}">
        @if($row->doc_serie != 'SIND')
            <span class="text-blue">{{ money($row->total, Setting::get('app_currency')) }}</span>
        @else
            <span class="text-blue">{{ money($row->total, Setting::get('app_currency')) }}</span>
        @endif
    </div>
@endif