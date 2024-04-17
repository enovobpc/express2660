<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Ordem Saída #'){{ $shippingOrder->code }}</h4>
</div>
<div class="modal-body">
    <div style="overflow: hidden; margin: -15px -15px 15px -15px">
        <div class="mtop-header">
            <div class="row row-5">
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('customer_id', __('Cliente')) }}
                        <p>
                            {{ $shippingOrder->customer->name }}
                        </p>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('document', __('Documento ou Referência')) }}
                        <p>{{ $shippingOrder->document }}</p>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('status_id', __('Estado')) }}
                        <p>
                            <span class="label" style="background: {{ @$shippingOrder->status->color }}">
                                {{ @$shippingOrder->status->name }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('date', __('Data')) }}
                        <p>{{ @$shippingOrder->date }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            <table class="table table-condensed m-b-0 table-products">
                <tr class="bg-gray-light">
                    <th class="w-1">@trans('SKU')</th>
                    <th>@trans('Produto')</th>
                    <th class="w-120px">@trans('Localização')</th>
                    <th class="w-60px">@trans('Pedido')</th>
                    <th class="w-60px">@trans('Qtd Sat')</th>
                </tr>
                @foreach($shippingOrder->lines as $line)
                    <tr>
                        <td style="padding-left: 0;">
                            <a href="{{ route('admin.logistic.products.show', $line->product_id) }}" target="_blank">
                            {{ @$line->product->sku }}
                            </a>
                        </td>
                        <td>{{ @$line->product->name }}</td>
                        <td>{{ @$line->location->code }}</td>
                        <td class="text-center">{{ $line->qty }}</td>
                        <td class="text-center">
                            @if($line->qty_satisfied < $line->qty)
                                <span class="text-center text-red bold">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $line->qty_satisfied }}
                                </span>
                            @else
                                <span class="text-center text-green bold">{{ $line->qty_satisfied }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <hr style="margin-bottom: 0;"/>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group m-t-10 m-b-0">
                <label><i class="fas fa-box-open"></i> @trans('Expedição')</label>
                <p>
                    @if(@$shippingOrder->shipment->tracking_code)
                        <a href="{{ route('admin.shipments.show', $shippingOrder->shipment_id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
                            {{ @$shippingOrder->shipment->tracking_code }}
                        </a>

                        <br/>
                        <span class="label" style="background: {{@$shippingOrder->shipment->provider->color}}">
                            {{ @$shippingOrder->shipment->provider->name }}
                        </span>
                    @else
                    @trans('Não Criada')
                    @endif
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            @if($shippingOrder->obs)
                <div class="form-group m-t-10 m-b-0">
                    {{ Form::label('obs', __('Observações')) }}
                    <p>{{ $shippingOrder->obs }}</p>
                </div>
            @endif
        </div>
        <div class="col-sm-5">
            {{ Form::label('attachments', __('Anexos'), ['class' => 'm-t-10']) }}
            <p class="text-muted italic">@trans('Sem anexos')</p>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>
<style>
    .mtop-header {
        background: #f9f9f9;
        border-bottom: 1px solid #ccc;
        margin: 0 0 2px 0;
        padding: 10px 20px;
        box-shadow: 0 0px 3px #ccc;
    }
</style>



