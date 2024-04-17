@extends(app_email_layout())

@section('content')
<div style="min-width: 650px">
    <h5>Notificação de validades a expirar</h5>
    <p>
        Existem viaturas da sua frota que requerem atenção.
        Se já procedeu a alguma manutenção listada abaixo, por favor, atualize o seu estado no sistema.
    </p>
    <hr/>
    @foreach($vehicles as $vehicle)
        <h3 style="margin-bottom: 0">{{ $vehicle->license_plate }} - {{ $vehicle->name }}</h3>
        <p style="margin-top: 0; margin-bottom: 5px">
            {{ trans('admin/fleet.vehicles.types.'. $vehicle->type) }}
            @if($vehicle->km)
            &bull; {{ money($vehicle->km, '', 0) }}Km
            @endif
        </p>
        <table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="4">
            <tr>
                <th style="background: #dddddd; text-align: left;">Notificação</th>
                <th style="background: #dddddd; text-align: left; width: 100px;">Data Limite</th>
                <th style="background: #dddddd; text-align: left; width: 80px">Km Limite</th>
                <th style="background: #dddddd; text-align: left; width: 100px">Dias Rest.</th>
                <th style="background: #dddddd; text-align: left; width: 100px">Km Rest.</th>
            </tr>
            @foreach($vehicle->notifications as $notification)
                <?php
                $date = @$notification['date'] ? $notification['date']->format('Y-m-d') : '';
                $km = @$notification['km'] ? money($notification['km'], '', 0) : '';

                $color = '#222';
                if($notification['status'] == 'expired') {
                    $color = 'red';
                }
                ?>
                <tr>
                    <td style="border-bottom: 1px solid #dddddd; color: {{ $color }}">{{ $notification['title'] }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: {{ $color }}">{{ $date }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: {{ $color }}">{{ $km }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: {{ $color }}">{{ @$notification['days_left'] ? $notification['days_left'].' dias' : '' }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: {{ $color }}">{{ @$notification['km_left'] ? money($notification['km_left'], '', 0).'km' : '' }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
</div>
@stop