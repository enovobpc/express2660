@if($row->customers)
    <?php $customers = \App\Models\Customer::whereIn('id', $row->customers)->pluck('name', 'code')->toArray(); ?>
    @foreach($customers as $code => $name)
        <span class="label label-default" data-toggle="tooltip" title="{{ $name }}">{{ $code }}</span>
    @endforeach
@else
    Todos
@endif