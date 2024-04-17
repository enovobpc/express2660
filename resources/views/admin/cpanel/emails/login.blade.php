@section('title')
    Espaço e Contas E-mail
@stop

@section('content-header')
    Espaço e Contas E-mail
@stop

@section('breadcrumb')
    <li class="active">@trans('Servidor')</li>
    <li class="active">@trans('Espaço e Contas E-mail')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <form action="{{ $loginDetails['url'] }}" method="POST" class="webmail-login">
                        <input type="hidden" name="session" value="{{ $loginDetails['session'] }}">
                    </form>
                    <div class="text-center">
                        <div style="height: 200px"></div>
                        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                        <h3 class="bold">@trans('A iniciar sessão...')</h3>
                        <p>@trans('Por favor, aguarde.')</p>
                        <div style="height: 200px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .lds-ellipsis {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 40px;
        }
        .lds-ellipsis div {
            position: absolute;
            top: 33px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #000;
            animation-timing-function: cubic-bezier(0, 1, 1, 0);
        }
        .lds-ellipsis div:nth-child(1) {
            left: 8px;
            animation: lds-ellipsis1 0.6s infinite;
        }
        .lds-ellipsis div:nth-child(2) {
            left: 8px;
            animation: lds-ellipsis2 0.6s infinite;
        }
        .lds-ellipsis div:nth-child(3) {
            left: 32px;
            animation: lds-ellipsis2 0.6s infinite;
        }
        .lds-ellipsis div:nth-child(4) {
            left: 56px;
            animation: lds-ellipsis3 0.6s infinite;
        }
        @keyframes lds-ellipsis1 {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }
        @keyframes lds-ellipsis3 {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(0);
            }
        }
        @keyframes lds-ellipsis2 {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(24px, 0);
            }
        }

    </style>
@stop

@section('scripts')
    <script type="text/javascript">
       $('.webmail-login').submit();
    </script>
@stop