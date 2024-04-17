<div style='font-family: Helvetica, Arial, "Lucida Grande", sans-serif; font-size: 12px'>
    {!! $data['message'] !!}

    @if(Setting::get('budgets_mail_signature'))
        @if(Setting::get('budgets_mail_signature_html'))
            {!! Setting::get('budgets_mail_signature') !!}
        @else
            {!! nl2br(Setting::get('budgets_mail_signature')) !!}
        @endif
    @endif
</div>