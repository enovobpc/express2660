@if($row->stock_total > 0.00)
    <a href="{{ route('account.logistic.cart.add', $row->id) }}" class="btn btn-xs btn-default btn-add-cart">
        <i class="fas fa-shopping-bag"></i>
    </a>
@else
    <button class="btn btn-xs btn-default" disabled>
        <i class="fas fa-shopping-bag"></i>
    </button>
@endif