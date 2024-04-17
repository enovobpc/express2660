<!DOCTYPE html>
<html class="error-page">
    <head>
        <title>Acesso negado</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        {{ Html::style('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}
        <link href="https://fonts.googleapis.com/css?family=Lato:400" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
           <script type="text/javascript" src="js/html5shiv.js"></script>
           <script type="text/javascript" src="js/respond.min.js"></script>
        <![endif]-->
        <style>
            body{
                font-family: 'Lato', sans-serif;
            }
            
            html.error-page{
                height: 100%;
                min-height: 100%;
                min-width: 100%;
                overflow: hidden;
                width: 100%;
            }
            html.error-page body{
                height: 100%;
                margin: 0;
                padding: 0;
                width: 100%;
            }
            html.error-page .container-fluid{
                display: table;
                height: 100%;
                padding: 0;
                width: 100%;
            }
            html.error-page .row-fluid{
                display: table-cell;
                height: 100%;
                vertical-align: middle;
            }
            html.error-page .centering{
                float: none;
                margin: 0 auto;
            }
            html.error-page h2.without-margin{
                margin-top: 0;
            }
            html.error-page .centering hr{
                width: 75%;
            }
            html .text-yellow{
                color: #f4d03f;
            }
        </style>
    </head>
    <body>
        <!-- content -->
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-lg-12">
                    <div class="centering text-center error-container">
                        <div class="text-center">
                            <h2 class="without-margin">
                                <i class="fas fa-lock" style="font-size: 200%"></i>
                                <br/>
                                Acesso negado
                            </h2>
                            <h4 class="text-muted">O acesso a esta página foi negado por falta de permissões.</h4>
                        </div>
                        <hr>
                        <ul class="pager">
                            <li><a href="{{ url()->previous() }}">Anterior</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>


