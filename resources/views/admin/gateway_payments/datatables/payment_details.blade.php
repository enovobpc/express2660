@if($row->method == 'mb' && $row->reference)
    Entidade: {{ $row->mb_entity }}<br/>
    Referência: {{ $row->mb_reference }}
@elseif($row->method == 'mbw')

@elseif($row->method == 'cc')

    @if($row->card_type)
        Cartão: {{ $row->card_type }}<br/>
        ****-****-****-{{ $row->card_last_digits }}
    @else
        @if($row->status == \App\Models\PaymentNotification::STATUS_PENDING ||
            $row->status == \App\Models\PaymentNotification::STATUS_FAILED)
            <a href="{{ $row->visa_url }}" target="_blank">Ir para página pagamento <i class="fa fa-external-link"></i></a>
        @endif
    @endif

@endif