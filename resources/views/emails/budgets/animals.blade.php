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
                        <img src="data:image/png;base64,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" alt="{{ Setting::get('company_name') }}" style="max-height: 40px"/>
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
                            ORÇAMENTO
                        </h4>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" bgcolor="#fff" class="content" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; padding: 20px">
                        {!! $data['message'] !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" bgcolor="#f9f9f9" class="footer" style="border: 1px solid #f1f2f2; padding: 10px; font-size: 12px; color: #898a8b; text-align: center;">
                        ©{{ date('Y') }} {{ Setting::get('company_name') }}. | info@interpets.pt
                        <br/>
                        <a href="http://www.quickbox.pt">Processado por {{ app_brand('docsignature') }}</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>