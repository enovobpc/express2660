<div style='font-family: Helvetica, Arial, "Lucida Grande", sans-serif; font-size: 12px'>

    <?php

    $html = Setting::get('budgets_mail_reminder_html');
    if($locale != 'pt') {
        $html = Setting::get('budgets_mail_reminder_html_' . $locale);
    }

    $html = str_replace(':nomeCliente', $budget->name, $html);
    $html = str_replace(':numOrcamento', $budget->budget_no, $html);
    $html = str_replace(':dataHora', $budget->created_at, $html);
    ?>

    {!! nl2br($html) !!}

    @if(Setting::get('budgets_mail_signature'))
        @if(Setting::get('budgets_mail_signature_html'))
            {!! Setting::get('budgets_mail_signature') !!}
        @else
            {!! nl2br(Setting::get('budgets_mail_signature')) !!}
        @endif
    @endif
</div>