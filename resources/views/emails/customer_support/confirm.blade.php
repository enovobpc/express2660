<?php
    $html = Setting::get('tickets_mail_autoresponse');

    $html = str_replace(':nmCliente', $budget->name, $html);
    $html = str_replace(':numOrcamento', $budget->budget_no, $html);
    $html = str_replace(':dataHora', $budget->created_at, $html);
?>
@if(Setting::get('tickets_mail_autoresponse_html'))
    {!! $html !!}
@else
    {!! nl2br($html) !!}
@endif