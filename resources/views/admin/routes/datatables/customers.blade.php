@if($row->customers)
    {{ $row->customers }}
    @if(Auth::user()->isAdmin() || Auth::user()->can('customers'))
        <br/>
        <small>
            <a href="{{ route('admin.customers.index', ['route' => $row->id]) }}" target="_blank">(Ver Clientes)</a>
        </small>
    @endif
@else
    <span class="text-red">
        <i class="fas fa-exclamation-triangle"></i> {{ $row->customers }}
    </span>
@endif