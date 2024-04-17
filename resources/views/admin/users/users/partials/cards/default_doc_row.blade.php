@foreach(trans('admin/users.default-cards') as $cardCode => $cardName)
    <?php
    $card = $allCards->filter(function($item) use($cardCode) {
        return $item->type == $cardCode;
    })->first();
    ?>

    <tr>
        <td class="vertical-align-middle text-uppercase">
            {{ $cardName }}
            {{ Form::hidden('name[]') }}
        </td>
        <td class="hide">
            {{ Form::select('type[]', [$cardCode => $cardName], $cardCode, ['class' => 'form-control select2', 'required']) }}
        </td>
        <td>
            {{ Form::text('card_no[]', @$card->card_no, ['class' => 'form-control']) }}
        </td>
        <td>
            <div class="input-group">
                {{ Form::text('issue_date[]', @$card->issue_date ? @$card->issue_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                <div class="input-group-addon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </td>
        <td>
            <div class="input-group">
                {{ Form::text('validity_date[]', @$card->validity_date ? @$card->validity_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                <div class="input-group-addon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </td>
        <td>
            <div class="input-group">
                {{ Form::text('notification_days[]', @$card->notification_days, ['class' => 'form-control']) }}
                <div class="input-group-addon" style="border: none;">
                    @trans('dias antes')
                </div>
            </div>
        </td>
        <td>
            {{ Form::text('obs[]', @@$card->obs, ['class' => 'form-control']) }}
        </td>
        <td class="vertical-align-middle">
        </td>
    </tr>
@endforeach