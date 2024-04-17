@extends(app_email_layout())

@section('content')
<div style="min-width: 650px">
    <h5>Notificação de validades</h5>
    <p>
        Existem colaboradores que requerem atenção.
        Se já procedeu a alguma renovação listada abaixo, por favor, atualize o seu estado no sistema.
    </p>
    <hr/>
    @foreach($users as $user)
        <h3 style="margin-bottom: 0">{{ $user->code }} - {{ $user->fullname }}</h3>
        <p style="margin-top: 0; margin-bottom: 5px">
            {{ $user->professional_role }}
        </p>
        <table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="4">
            <tr>
                <th style="background: #dddddd; text-align: left;">Notificação</th>
                <th style="background: #dddddd; text-align: left; width: 100px;">Data Limite</th>
                <th style="background: #dddddd; text-align: left; width: 100px">Dias Rest.</th>
                <th style="background: #dddddd; text-align: left; width: 1%"></th>
            </tr>
            @foreach($user->notifications as $notification)
                <?php
                $date = @$notification['date'] ? $notification['date']->format('Y-m-d') : '';

                $color = '#222';
                if($notification['status'] == 'expired') {
                    $color = 'red';
                }
                ?>
                <tr>
                    <td style="border-bottom: 1px solid #dddddd; color: {{ $color }}">{{ $notification['title'] }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: {{ $color }}">{{ $date }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: {{ $color }}">{{ @$notification['days_left'] ? $notification['days_left'].' dias' : '' }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: {{ $color }}">
                        @if($notification['status'] == 'expired')
                        <a href="{{ route('admin.users.edit', [$user->id, 'tab' => 'cards']) }}" style="padding: 3px 10px;background: #ec0000;color: #fff;border-radius: 3px;">Atualizar</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endforeach
</div>
@stop