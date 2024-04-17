<div>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
        <tr>
            <th>Referência</th>
            <th>Designação</th>
            <th>Fornecedor</th>
            <th>Preço</th>
            <th>IVA</th>
            <th>Serviço</th>
            <th>Stock</th>
            <th>Ativo</th>
        </tr>

        @foreach ($items as $item)
            <tr>
                <td class="w-100px">{{ $item->reference }}</td>
                <td>@include('admin.billing.items.datatables.name', ['row' => $item, 'withoutLink' => true])</td>
                <td class="w-100px">@include('admin.billing.items.datatables.provider', ['row' => $item])</td>
                <td class="w-70px">@include('admin.billing.items.datatables.price', ['row' => $item])</td>
                <td class="w-40px">{{ money($item->tax_rate, '%', 0) }}</td>
                <td class="w-30px">{{ $item->is_service ? 'Sim' : 'Não' }}</td>
                <td class="w-70px">@include('admin.billing.items.datatables.stock_total', ['row' => $item])</td>
                <td class="w-30px">{{ $item->is_active ? 'Sim' : 'Não' }}</td>
            </tr>
        @endforeach
    </table>
</div>
