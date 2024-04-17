@if($equipment->exists)
    <?php $row = $equipment; ?>
    <tr data-id="{{ $equipment->id }}"
        data-sku="{{ $equipment->sku }}">
<!--        <td></td>-->
        <td>{{ $equipment->sku }}</td>
        <td>@include('admin.equipments.equipments.datatables.equipments.name')</td>
        <td>{{ $equipment->lote ? $equipment->lote : $equipment->serial_no }}</td>
        <td>{{ @$equipment->category->name }}</td>
        <td>@include('admin.equipments.equipments.datatables.equipments.location')</td>
        <td>{{ $equipment->stock_total }}</td>
        <td>@include('admin.equipments.equipments.datatables.equipments.status')</td>
    </tr>
@else
    <tr data-id="" data-sku="{{ $equipment->sku }}" class="text-red" style="background: #ff000036">
        <td></td>
        <td>{{ $equipment->sku }}</td>
        <td colspan="8">
            <i class="fas fa-exclamation-circle"></i> Não foi possível encontrar o equipamento.
        </td>
    </tr>
@endif