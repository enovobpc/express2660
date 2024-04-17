<tr>
    <td class="vertical-align-middle text-uppercase card-row">
        <span class="lbl-name">{{ @$card->name }} <i class="fas fa-pencil-alt"></i></span>
        {{ Form::text('name[]', @$card->name, ['class' => 'form-control inp-name', 'style' => 'display:none', 'required']) }}
    </td>
    <td>
        {{ Form::text('card_no[]', @$card->card_no, ['class' => 'form-control', 'required']) }}
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
        {{ Form::text('obs[]', @$card->obs, ['class' => 'form-control']) }}
    </td>
    <td class="vertical-align-middle">
        <a href="#" class="text-red btn-delete-card">
            <i class="fas fa-trash-alt"></i>
        </a>
    </td>
</tr>