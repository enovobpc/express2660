<p>
    {{ transLocale('admin/email.cargo-instructions.presentation', $locale) }}<br/>

    @if($shipment->is_collection) {
        {{ transLocale('admin/email.cargo-instructions.title-pickup', $locale) }}
    @else
        {{ transLocale('admin/email.cargo-instructions.title-expedition', $locale) }}
    @endif
</p>

@foreach($shipments as $key => $shipment)
<p style="margin-bottom: 0; font-size: 16px; line-height: 12px"><b>ETAPA {{ $key + 1 }}: {{ strtoupper($shipment->sender_city) }} para {{ strtoupper($shipment->recipient_city) }}</b></p>
<table style="width: 100%">
    <tr>
        <td style="width: 50%"><b>{{ transLocale('admin/email.cargo-instructions.sender', $locale) }}</b></td>
        <td><b>{{ transLocale('admin/email.cargo-instructions.recipient', $locale) }}</b></td>
    </tr>
    <tr>
        <td>
            {{ strtoupper($shipment->sender_name) }}<br/>
            {{ strtoupper($shipment->sender_address) }}<br/>
            {{ strtoupper($shipment->sender_zip_code) }} {{ strtoupper($shipment->sender_city) }}<br/>
            {{ strtoupper(trans('country.' . $shipment->sender_country)) }}<br/>
            {{ strtoupper($shipment->sender_phone) }}
        </td>
        <td>
            {{ strtoupper($shipment->recipient_name) }}<br/>
            {{ strtoupper($shipment->recipient_address) }}<br/>
            {{ strtoupper($shipment->recipient_zip_code) }} {{ strtoupper($shipment->recipient_city) }}<br/>
            {{ strtoupper(trans('country.' . $shipment->recipient_country)) }}<br/>
            {{ strtoupper($shipment->recipient_phone) }}
        </td>
    </tr>
</table>
@endforeach

<p><b>{{  transLocale('admin/email.cargo-instructions.goods', $locale) }}</b></p>
<table style="margin: 0; width: 100%">
    <tr>
        <th style="background: #f2f2f2;">{{ transLocale('admin/global.word.description', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 5px;">{{ transLocale('admin/global.word.qty', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 60px;">{{ transLocale('admin/global.word.type', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.width_abrv', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.length_abrv', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.height_abrv', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 50px">{{ transLocale('admin/global.word.weight', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 20px">{{ transLocale('admin/global.word.class', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 20px">{{ transLocale('admin/global.word.letter', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2; width: 30px">Nº</th>
        <th class="text-center" style="background: #f2f2f2;">{{ transLocale('admin/global.word.from', $locale) }}</th>
        <th class="text-center" style="background: #f2f2f2;">{{ transLocale('admin/global.word.to', $locale) }}</th>
    </tr>

    @forelse($shipment->pack_dimensions as $pack)
        <tr>
            <td>{{ $pack->description }}</td>
            <td class="text-center">{{ money($pack->qty ? $pack->qty : 1) }}</td>
            <td class="text-center text-uppercase">{{ !empty($pack->type) ? trans('admin/global.packages_types.' . $pack->type) : (!empty(array_keys($shipment->packaging_type)[0]) ? trans('admin/global.packages_types.' . array_keys($shipment->packaging_type)[0]) : "VOL") }}</td>
            <td class="text-center" style="padding: 5px 0">{{ money($pack->length) }}</td>
            <td class="text-center">{{ money($pack->width) }}</td>
            <td class="text-center">{{ money($pack->height) }}</td>
            <td class="text-center">{{ money($pack->weight) }}</td>
            <td class="text-center">{{ $pack->adr_class }}</td>
            <td class="text-center">{{ $pack->adr_letter }}</td>
            <td class="text-center">{{ $pack->adr_number }}</td>
            <td class="text-center">{{ $shipment->sender_city }}</td>
            <td class="text-center">{{ $shipment->recipient_city }}</td>
        </tr>
    @empty
        <tr>
        <td></td>
        <td class="text-center">{{ money($shipment->volumes) }}</td>
        <td class="text-center">VOL</td>
        <td class="text-center" style="padding: 5px 0">0,00</td>
        <td class="text-center">0,00</td>
        <td class="text-center">0,00</td>
        <td class="text-center">{{ money($shipment->weight) }}</td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-center">{{ $shipment->sender_city }}</td>
        <td class="text-center">{{ $shipment->recipient_city }}</td>
    </tr>
    @endforelse

    @if($shipment->addresses)
        @foreach($shipment->addresses as $address)
            @if(!$address->pack_dimensions->isEmpty())
                @foreach($address->pack_dimensions as $pack)
                    <tr>
                        <td>{{ $pack->description }}</td>
                        <td class="text-center">{{ money($pack->qty ? $pack->qty : 1) }}</td>
                        <td class="text-center text-uppercase">{{ $pack->type ? trans('admin/global.packages_types.' . $pack->type)  : trans('admin/global.packages_types.' . $address->packaging_type) }}</td>
                        <td class="text-center" style="padding: 5px 0">{{ money($pack->length) }}</td>
                        <td class="text-center">{{ money($pack->width) }}</td>
                        <td class="text-center">{{ money($pack->height) }}</td>
                        <td class="text-center">{{ money($pack->weight) }}</td>
                        <td class="text-center">{{ $pack->adr_class }}</td>
                        <td class="text-center">{{ $pack->adr_letter }}</td>
                        <td class="text-center">{{ $pack->adr_number }}</td>
                        <td class="text-center">{{ $address->sender_city }}</td>
                        <td class="text-center">{{ $address->recipient_city }}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    @endif
</table>
{{--
    $message.= transLocale('admin/email.cargo-instructions.volumes', $locale).': ' .$shipment->volumes . '<br/>';
    $message.= transLocale('admin/email.cargo-instructions.weight', $locale).': '. $shipment->weight . ' KG<br/>';

    $message.= transLocale('admin/email.cargo-instructions.sender-ref', $locale).': ' . $shipment->reference . '<br/>';
    $message.= transLocale('admin/email.cargo-instructions.our-ref', $locale).': TRK' . $shipment->tracking_code . '</p>';

if(!$shipment->pack_dimensions->isEmpty()) {
$message.= '<p><b>'.transLocale('admin/email.cargo-instructions.dimensions', $locale).' (cm)</b><br/>';
    foreach ($shipment->pack_dimensions as $dimension) {
    $message.= $dimension->length.' x ' . $dimension->width.' x '.$dimension->height.'<br/>';
    }
    }

    $message.= '<p>'..'</p>';
--}}
<p >
    @if($shipment->charge_price)
        {{ transLocale('admin/email.cargo-instructions.charge', $locale).': '. money($shipment->charge_price, 'EUR')  }}<br/>
    @endif
</p>
<p>{{ transLocale('admin/email.cargo-instructions.text-after', $locale) }}</p>
{!! App\Models\Email\Email::getSignature() !!}
