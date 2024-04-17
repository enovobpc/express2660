<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <style>
            .mail-template h1 {
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 22px !important;
                margin: 0px 0px 15px 0px !important;
                text-align: center;
            }

            .mail-template h3 {
                font-family: Arial, Helvetica, sans-serif;
            }

            .mail-template h5 {
                margin: 0;
                font-size: 16px;
            }

            .mail-template a {
                color: {{ env('APP_MAIL_COLOR_PRIMARY') }};
                text-decoration: none;
            }
            .mail-template a:hover {
                color: {{ env('APP_MAIL_COLOR_PRIMARY') }};
            }

            .button-link {
                background: {{ env('APP_MAIL_COLOR_PRIMARY') }} !important;
                color: #fff !important;
                text-decoration: none;
                padding: 10px;
                margin: 10px;
            }

            .button-link:hover {
                background: {{ env('APP_MAIL_COLOR_PRIMARY') }} !important;
                color: #fff !important;
                text-decoration: none;
            }
        </style>
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;
                    font-size: 14px;
                    margin: 0;
                    padding: 0;
                    background-color: #eee;
                    color: #333333;">

        <table class="mail-template"
               border="0"
               cellpadding="0"
               cellspacing="0" width="100%"
               style="font-family: Arial, Helvetica, sans-serif;
                        font-size: 14px;
                        margin: 20px 0 20px 0;
                        padding: 0;
                        background-color: #fff;
                        color: #333333;">
            <tr>
                <td style="background: #eeeeee">
                    <table align="center" cellpadding="0" cellspacing="0" class="email-layout" style="
                                    font-family: Arial, Helvetica, sans-serif;
                                    font-size: 14px;
                                    line-height: 1.428571429;
                                    background-color: #fff;
                                    padding: 0px;
                                    border: 1px solid #dadbdc;
                                    max-width: 700px
                                    ">
                        <tr>
                            <td bgcolor="{{ env('APP_MAIL_COLOR_PRIMARY') }}" class="header" style="color: #ffffff; padding: 15px 20px;">
                                <img src="{{ asset(env('APP_LOGO_MAIL') ? env('APP_LOGO_MAIL') : 'assets/img/logo/logo_white_sm.png') }}" alt="{{ Setting::get('company_name') }}" height="40" style="max-height: 40px"/>
                            </td>
                            <td bgcolor="{{ env('APP_MAIL_COLOR_PRIMARY') }}" style="text-align: right;">
                                <h4 style="
                                        font-family: Arial, Helvetica, sans-serif;
                                        margin: 0;
                                        margin-right: 10px;
                                        padding-right: 15px;
                                        font-size: 14px;
                                        text-transform: uppercase;
                                        font-weight: normal;
                                        color: #ffffff;
                                        opacity: 0.8;">
                                    Notificação
                                </h4>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" bgcolor="#fff" class="content" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; padding: 20px">
                                @yield('content')
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" bgcolor="#fff" class="content" style="font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    padding: 20px;
    border-top: 1px solid #ddd;
    background: #45474e;
    color: #fff;">
                                <h4 style="margin: 0;">Apoio ao Cliente</h4>
                                <table align="center" cellpadding="0" cellspacing="0" style="font-size: 12px; width: 100%; color: #eee">
                                    <tr>
                                        <td>
                                            {{ Setting::get('company_name') }}<br/>
                                            {{ Setting::get('company_website') ? Setting::get('company_website') : env('APP_URL') }}
                                        </td>
                                        <td style="text-align: right; color: #eee;">
                                            Telef: {{ Setting::get('support_phone_1') }}<br/>
                                            @if(Setting::get('support_phone_2'))
                                            Tefem:{{ Setting::get('support_phone_2') }}<br/>
                                            @endif
                                            E-mail: {{ Setting::get('support_email_1') }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        @if(!env('APP_HIDE_CREDITS'))
                        <tr>
                            <td colspan="2" bgcolor="#f9f9f9" class="footer" style="border: 1px solid #f1f2f2; padding: 10px; font-size: 11px; color: #898a8b; text-align: center;">
                                {!! app_brand(null, '', 'height: 18px; margin-bottom: 5px;') !!}
                                <br/>
                                {!! app_brand('docsignature') !!}
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>