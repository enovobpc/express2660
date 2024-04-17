<div class="adhesive-label" style="text-align: center; height: 9.47cm; background: #ffeaeb">
    <div style="background: red; height: 20px; margin-bottom: 10px"></div>
    <img src="{{ asset('assets/img/default/error_256.png') }}" style="height: 60px;"/>
    <h2 style="color: red; margin-top: 10px; font-weight: bold">
        ERRO DE SINCRONIZAÇÃO<br/>
        <small style="font-weight: normal">TRK#{{ $shipment->tracking_code }}</small>
    </h2>
    <br/>
    <br/>
    @if($source == 'admin')
    <h4 style="margin-top: 0">Não foi possível sincronizar com o fornecedor {{ $shipment->provider->name }}.</h4>
    @else
    <h4 style="margin-top: 0">Não foi possível sincronizar o seu envio.</h4>
    @endif
    <p>Deve editar o envio, corrigir o erro abaixo e gravar de novo.</p>
    <hr style="margin: 15px 0 10px 0"/>
    <p style="line-height: 15px; margin: 0">
        <span style="color: red; font-weight: bold">{{ $shipment->webservice_error }}</span>
    </p>
    <hr style="margin: 15px 0 10px 0"/>
</div>
<div style="background: red; height: 20px;"></div>