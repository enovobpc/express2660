@if(config('app.source') == 'activos24')
<a href="#" style="cursor: not-allowed">
    {{ @$row->customer->name }}
</a>
@else
    <a href="{{ route('admin.logistic.shipping-orders.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
        {{ @$row->customer->name }}
    </a>
@endif
<br/>
<small class="text-muted">
    @trans('NIF:')' {{ @$row->customer->vat }}
</small>