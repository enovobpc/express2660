<h5>Estimado {{ $shipment->provider->company }},</h5>
<p>
    Vimos por este meio pedir informações sobre o estado e localização GPS da ordem de carga <b>TRK{{ $shipment->tracking_code }}</b>.<br/>
    Aguardamos uma resposta.
</p>
<p>
    TRK: <b>{{ $shipment->tracking_code }}</b><br/>
    Nome: <b>{{ $shipment->recipient_name }}</b><br/>
    Morada: <b>{{ $shipment->recipient_address }}</b><br/>
    Código Postal: <b>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</b>
</p>
