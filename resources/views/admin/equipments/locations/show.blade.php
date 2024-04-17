<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Equipamentos na Localização</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-md-12">
            <table class="table table-condensed m-b-0">
                <tr>
                    <th class="bg-gray-light w-120px">SKU</th>
                    <th class="bg-gray-light">Equipamento</th>
                    <th class="bg-gray-light">Categoria</th>
                    <th class="bg-gray-light w-1">Qtd</th>
                    <th class="bg-gray-light w-1">Estado</th>
                    <th class="bg-gray-light w-140px">Ultimo Movimento</th>
                </tr>
                @if($location->equipments->isEmpty())
                    <tr>
                        <td colspan="4">Não existem equipamentos nesta localização.</td>
                    </tr>
                @else
                    @foreach($location->equipments as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ @$product->category->name }}</td>
                            <td>{{ $product->stock_total }}</td>
                            <td>
                                <span class="label" style="background: {{ trans('admin/equipments.equipments.status-color.'.$product->status) }}">
                                    {{ trans('admin/equipments.equipments.status.'.$product->status) }}
                                </span>
                            </td>
                            <td>{{ $product->last_update }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
