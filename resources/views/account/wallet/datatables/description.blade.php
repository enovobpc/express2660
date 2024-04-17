<div>
    <a href="{{ route('account.wallet.show', $row->id) }}"
       data-target="#modal-remote-xs"
       data-toggle="modal">
        {{ $row->description }}
    </a>
</div>
<small class="text-muted">
    Pagamento {{ $row->code }}
</small>