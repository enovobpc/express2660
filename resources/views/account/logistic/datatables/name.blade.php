@if(config('app.source') == 'activos24')
    <a href="#" style="cursor: not-allowed;">
        {{ @$row->name }}
    </a>
@else
    <a href="{{ route('account.logistic.products.show', $row->id) }}"
       data-toggle="modal"
       data-target="#modal-remote-lg">
        {{ @$row->name }}
    </a>
@endif

@if($row->description)
    <br/>
    <small class="italic">{{ str_limit($row->description) }}</small>
@endif