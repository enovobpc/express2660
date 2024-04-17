@if($row->method == 'mb')
    <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 20px"/>
@elseif($row->method == 'mbway')
    <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 20px"/>
@elseif($row->method == 'visa' || $row->method == 'cc')
    <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 15px"/>
@elseif($row->method == 'visa' || $row->method == 'tb')
    <img src="{{ asset('assets/img/default/tb.svg') }}" style="height: 20px"/>
@elseif($row->method == 'wallet')
    <img src="{{ asset('assets/img/default/wallet.svg') }}" style="height: 30px"/>
@endif