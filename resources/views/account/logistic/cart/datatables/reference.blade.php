
<a href="{{ route('account.logistic.cart.order.show', $row->id) }}"
    data-toggle="modal"
    data-target="#modal-remote-lg"
    class="text-blue">  {{ @$row->reference }} 
 </a>

@if($row->description)
    <br/>
    <small class="italic">{{ str_limit($row->description) }}</small>
@endif