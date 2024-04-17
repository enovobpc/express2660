<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-12">
                {{--<h4 class="form-divider no-border" style="margin-top: -8px; margin-bottom: 20px;">
                    <i class="fas fa-fw fa-link"></i> Alocações
                </h4>--}}
                <table class="table" id="locations-table table-dashed">
                    <tr class="bg-gray-light">
                        <th class="w-90px">@trans('Data')</th>
                        <th class="w-90px">@trans('Pedido')</th>
                        <th class="w-1">@trans('Estado')</th>
                        <th>@trans('Referência')</th>
                        <th class="w-120px">@trans('Localização')</th>
                        <th class="w-70px text-center">@trans('Qtd')</th>
                        <th class="w-1"></th>
                    </tr>
                    <?php $totalQty = 0 ?>
                    @foreach($allocations as $row)
                        <?php $totalQty+= $row->qty ?>
                        <tr>
                            <td>{{ @$row->shipping_order->date }}</td>
                            <td>
                                <a href="{{ route('admin.logistic.shipping-orders.show', $row->shipping_order_id) }}"
                                   data-toggle="modal"
                                   data-target="#modal-remote-lg">
                                    {{ @$row->shipping_order->code }}
                                </a>
                            </td>
                            <td>
                                <span class="label" style="background: {{ @$row->shipping_order->status->color }}">{{ @$row->shipping_order->status->name }}</span>
                            </td>
                            <td>{{ @$row->shipping_order->document }}</td>
                            <td>{{ @$row->location->code }}</td>
                            <td class="text-center bold fs-15">{{ @$row->qty }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right bold fs-15">@trans('Total')</td>
                        <td class="text-center bold fs-15">{{ $totalQty }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>