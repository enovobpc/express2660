<table class="table table-condensed">
    <tr class="bg-gray-light">
        <th class="w-200px">Estado</th>
        <th class="w-70px text-center">Total</th>
        <th>Histórico por Operador</th>
    </tr>
    @foreach($status as $key => $items)
        <tr>
            <td>
                    <span class="label label-{{ trans('admin/budgets.status-labels.' . $key) }}">
                        {{ trans('admin/budgets.status.' . $key) }}
                    </span>
            </td>
            <td class="text-center">
                <span class="badge">{{ count($items) }}</span>
            </td>
            <td>
                <?php
                $items = $items->groupBy('operator.name');
                ?>
                @foreach($items as $operator => $item)
                    {{ $operator ? $operator : 'Sem operador' }} ({{ count($item) }})<br/>
                @endforeach
            </td>
        </tr>
    @endforeach
</table>