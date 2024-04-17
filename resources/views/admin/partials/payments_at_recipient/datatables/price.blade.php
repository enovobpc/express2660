@if($row->ignore_billing)
    <strike>
@endif

    <a href="{{ route('admin.refunds.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote" class="bold">
        {{ money($row->total_price_for_recipient, Setting::get('app_currency')) }}
    </a>

@if($row->ignore_billing)
    </strike>
@endif