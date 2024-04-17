@extends(app_email_layout())

@section('content')
<h5>Estimado comercial,</h5>
<p>Foi realizado pelo utilizador {{ @$customer->name }}, um pedido de encomenda.</p>
<table id="datatable-history" style="width: 650px">
    <tr>
        <th style="width: 1%; text-align: left"></th>
        <th style="width: 100px; text-align: left">Ref.</th>
        <th style="text-align: left">Artigo</th>
        <th style="width: 1%; text-align: left">Qtd</th>
    </tr>
    @foreach($products as $product)
        <tr>
            <td>
                @if(@$product->product->filepath)
                    <a href="{{ asset(@$product->product->filepath) }}"
                       data-src="{{ asset(@$product->product->filepath) }}"
                       data-thumb="{{ asset(@$product->product->getCroppa(200,200)) }}"
                       class="preview-img">
                        <img src="{{ asset(@$product->product->getCroppa(200,200)) }}" style="height: 40px; width: 40px" onerror="this.src='{{ asset('assets/img/default/broken.thumb.png') }}'"/>
                    </a>
                @else
                    <img src="{{ asset('assets/img/default/default.thumb.png') }}" style="height: 40px; width: 40px"/>
                @endif
            </td>
            <td>{{ @$product->product->sku }}</td>
            <td>{{ @$product->product->name }}</td>
            <td style="font-weight: bold">{{ @$product->qty }}</td>
        </tr>
    @endforeach
</table>

<div style="text-align: center">
    <p>
        Por favor, aprove ou rejeite este pedido na sua área de cliente.<br/>
            <br/>
            <a href="{{ route('account.index') }}" class="button-link">Entrar na Área de Cliente</a>
        </p>
</div>


@stop