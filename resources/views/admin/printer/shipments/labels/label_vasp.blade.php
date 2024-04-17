<div class="adhesive-label">
    <div>
        <div style="width: 2.5cm; float: left; height: 2.6cm">

        </div>
        <div style="width: 7cm; float: left;">
            <div style="background: #000; color: #fff; font-weight: bold; font-size: 30px; line-height: 35px; margin-bottom: 0">
                &nbsp;&nbsp;&nbsp;VASP <span style="font-size: 20px; line-height: 20px; margin-top: -15px">EXPRESSO</span>
            </div>
            <div style="font-size: 30px; line-height: 30px; font-weight: bold;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $label['servico'] }}
            </div>

            <div style="font-size: 20px; margin-top: 10px">
                {{ @$label['trk'] }}
            </div>
            <div style="font-size: 22px; margin-top: 10px; margin-bottom: 3px">
                {{ @$label['nomeRota'] }}
            </div>
        </div>
    </div>
    <div>
        <div style="width: 2.5cm; float: left; height: 6.3cm">
            <div  style="position: fixed; right: 0mm; bottom: 0mm; rotate: -90;">
                <img src="{{ $barcode }}" style="background: #fff; height: 220px;"/>
            </div>
        </div>
        <div style="width: 7cm; float: left;">
            <div style=" border-top: 2px solid #000; border-bottom: 2px solid #000;">
                <div style="width: 3.5cm; float: left; line-height: 14px;">
                    <div style=" margin-top: 5px; line-height: 14px; font-size: 13px">
                        Recolha: {{ @$label['dataRecolha'] }}<br/>
                        Entrega: {{ @$label['dataEntrega'] }}
                    </div>
                </div>
                <div style="width: 3.5cm; float: left; font-weight: bold; font-size: 45px; line-height: 40px; text-align: right">
                    {{ @$label['codigoRota'] }}</span>
                </div>
            </div>
            <div style="margin-top: 5px;font-size: 13px;line-height: 12px;font-weight: bold">
                Expedidor: <br/>
                {{ @$label['sender_name'] }}<br/>
                <div style="height: 25px;">
                    {{ @$label['sender_address'] }}
                </div>
                {{ @$label['sender_city'] }}<BR/>
                {{ @$label['sender_zip_code'] }}
            </div>
            <div style="border-top: 2px solid #000; margin-top: 5px; font-weight: bold; font-size: 13px; margin-top: 5px;line-height: 12px;">
                Destinatário: <br/>
                {{ @$label['recipient_name'] }}<br/>
                <div style="height: 25px;">
                    {{ @$label['recipient_address'] }}
                </div>
                {{ @$label['recipient_city'] }}<BR/>
                {{ @$label['recipient_zip_code'] }}
                <BR/>
                <BR/>
                Contacto destinatario: <br/>{{ @$label['recipient_attn'] }}<br/>{{ @$label['recipient_phone'] }}
            </div>
        </div>
    </div>
    <div style="border-top: 2px solid #000">
        <div style="line-height: 17px; font-size: 12px">
            Entrega<br/>
            Volume&nbsp;&nbsp;{{ $label['volume'] }}&nbsp;&nbsp;de&nbsp;&nbsp;{{ $totalVolumes }}<br/>
            Não entrega em Kios &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            @if(@$label['charge_price'] > 0.00)
                <span style="font-size: 18px; font-weight: bold;">{{ money(@$label['charge_price'], '€') }}</span>
            @endif
            <br/>
            Instruções de entrega: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ @$label['weight'] }}<br/>
        </div>
        {{ @$label['obs'] }}<br/>
        <div style="line-height: 17px; font-size: 12px; margin-top: 10px;">
            Instruçoes fixas:
        </div>
    </div>
</div>