<div style='font-family: Helvetica, Arial, "Lucida Grande", sans-serif; font-size: 12px'>
    {!! $data['message'] !!}

    @if(Setting::get('tickets_mail_signature'))
        @if(Setting::get('tickets_mail_signature_html'))
            {!! Setting::get('tickets_mail_signature') !!}
        @else
            {!! nl2br(Setting::get('tickets_mail_signature')) !!}
        @endif
    @endif
</div>