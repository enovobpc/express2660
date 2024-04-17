<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Ordem Saída {{ $shippingOrder->code }}</h4>
</div>
<div class="modal-body">
    <ul class="list-inline m-b-20">
        <li>
            <small class="text-muted">Data Registo</small><br/>
            {{ $shippingOrder->created_at }}
        </li>
        <li>
            <small class="text-muted">Envio</small><br/>
            {{ @$shippingOrder->shipment->tracking_code }}
        </li>
        <li>
            <small class="text-muted">Observações</small><br/>
            {{ @$shippingOrder->obs }}
        </li>
    </ul>
    <table id="datatable-history" class="table table-condensed table-hover">
        <thead>
            <tr>
                <th class="w-1 bg-gray-light">Qty</th>
                <th class="w-50px bg-gray-light">SKU</th>
                <th class="bg-gray-light">Designação</th>
                <th class="bg-gray-light">NºSérie/Lote</th>
                <th class="w-70px bg-gray-light">Comprim.</th>
                <th class="w-70px bg-gray-light">Largura</th>
                <th class="w-70px bg-gray-light">Altura</th>
                <th class="w-70px bg-gray-light">Peso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shippingOrder->lines as $line)
                <tr>
                    <td>{{ $line->qty }}x</td>
                    <td>{{ @$line->product->sku }}</td>
                    <td>{{ @$line->product->name }}</td>
                    <td>{{ @$line->product->serial_no }}{{ @$line->product->lote }}</td>
                    <td>{{ money($line->product->width, 'cm') }}</td>
                    <td>{{ money($line->product->length, 'cm') }}</td>
                    <td>{{ money($line->product->height, 'cm') }}</td>
                    <td>{{ money($line->product->weight, 'kg') }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
