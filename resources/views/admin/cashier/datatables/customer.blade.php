@if($row->customer_id)
    {{--<a href="{{ route('admin.customers.edit', $row->customer_id) }}" target="_blank">
        {{ @$row->customer->code }} - {{ @$row->customer->name }}
    </a>--}}
    {{ @$row->customer->code }} - {{ @$row->customer->name }}
@endif
@if($row->provider_id)
    @if($row->customer_id)
        <br/>
    @endif
    {{--<a href="{{ route('admin.provider.edit', $row->provider_id) }}" target="_blank">
        {{ @$row->provider->code }} - {{ @$row->provider->name }}
    </a>--}}
    {{ @$row->provider->code }} - {{ @$row->provider->name }}
@endif