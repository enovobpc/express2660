@if($row->payment_method == 'wallet')
    <div class="text-right">
    @if($row->wallet_balance > 0.00)
        {{ money($row->wallet_balance, Setting::get('app_currency')) }}
    @else
        <span class="text-muted">
            {{ money($row->wallet_balance, Setting::get('app_currency')) }}
        </span>
    @endif
    </div>
@endif