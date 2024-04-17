@if($row->subclass == 'nor')
    <div><i class="fas fa-square text-green"></i> {{ trans('admin/billing.vat-rates-subclasses.'.$row->subclass) }}</div>
@elseif($row->subclass == 'int')
    <div><i class="fas fa-square text-yellow"></i> {{ trans('admin/billing.vat-rates-subclasses.'.$row->subclass) }}</div>
@elseif($row->subclass == 'red')
    <div><i class="fas fa-square text-light-blue"></i> {{ trans('admin/billing.vat-rates-subclasses.'.$row->subclass) }}</div>
@else
    <div><i class="fas fa-square text-muted-light"></i> {{ trans('admin/billing.vat-rates-subclasses.'.$row->subclass) }}</div>
@endif