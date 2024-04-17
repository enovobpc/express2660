@if($row->balance_last_update)
    <span>
        {{ human_date($row->balance_last_update->format('Y-m-d'), 'Y-m-d') }}
        <br/>
        <small>
            {{ $row->balance_last_update->format('H:i:s') }}
            <a href="{{ route('admin.billing.balance.sync.all', $row->id) }}"
               class="btn-update-balance"
               data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> Aguarde..."
               style="color: #777">
                <i class="fas fa-sync-alt"></i>
            </a>
        </small>
    </span>
@else
    <span class="text-red">Nunca</span>
    <a href="{{ route('admin.billing.balance.sync.all', $row->id) }}"
       class="btn-update-balance"
       data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> Aguarde..."
       style="color: #777">
        <i class="fas fa-sync-alt"></i>
    </a>
@endif