<div class="modal" id="modal-mass-proforma">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.printer.billing.customers.shipments.summary.all', 'month' => $month, 'year' => $year, 'period' => $period], 'class' => 'mass-proforma-form']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Emitir tudo como fatura-proforma</h4>
            </div>
            <div class="modal-body">
                @if(hasModule('invoices-advanced'))
                @if($period == '30d')
                    <h4>Pretende emitir fatura-proforma do mês <b>{{ trans('datetime.month.' . $month) }} {{ $year }}</b>?</h4>
                @elseif($period == '1q')
                    <h4>Pretende emitir fatura-proforma da 1ª quinzena de {{ trans('datetime.month.' . $month) }} {{ $year }} automáticamente?</h4>
                @elseif($period == '2q')
                    <h4>Pretende emitir fatura-proforma da 2ª quinzena de {{ trans('datetime.month.' . $month) }} {{ $year }} automáticamente?</h4>
                @endif
                <p class="text-blue m-t-10 m-b-0">
                    <ul class="list-inline text-blue">
                        <li>
                            <i class="fas fa-info-circle"></i> Este processo vai demorar alguns minutos.
                        </li>
                        <li>
                            <i class="fas fa-info-circle"></i> Caso existam clientes com anomalias na faturação (ex: serviços sem preço ou outros) estes não serão faturados.
                        </li>
                    </ul>
                </p>
                @else
                    <div class="row">
                        <div class="col-sm-3">
                            <img class="w-120px pull-right" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ4Ny4zMTYgNDg3LjMxNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDg3LjMxNiA0ODcuMzE2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzQ3LjY1OCwwaC0yODhjLTMwLjkxMSwwLjA0LTU1Ljk2LDI1LjA4OS01Niw1NnYyODcuMzM2YzAuMDQsMzAuOTExLDI1LjA4OSw1NS45Niw1Niw1Nmg4OHYtMTZoLTg4ICAgIGMtMjIuMDgtMC4wMjYtMzkuOTc0LTE3LjkyLTQwLTQwVjIwOGg1MS4zNzZjNC43NDEtMC4zNDcsOC4zMDQtNC40NzEsNy45NTctOS4yMTNjLTAuMDY3LTAuOTE0LTAuMjc5LTEuODEyLTAuNjI5LTIuNjU5ICAgIGMtNy4xMS0xNi4xOCwwLjI0Mi0zNS4wNiwxNi40MjItNDIuMTdjMTYuMTgtNy4xMSwzNS4wNiwwLjI0Miw0Mi4xNywxNi40MjJjMy42MDUsOC4yMDQsMy42MDUsMTcuNTQ0LDAsMjUuNzQ4ICAgIGMtMS45NzYsNC4xMzQtMC4yMjYsOS4wODcsMy45MDgsMTEuMDYzYzEuMDY5LDAuNTExLDIuMjM2LDAuNzg3LDMuNDIsMC44MWg1MS4zNzZ2NDhjLTIuNjQzLTAuNDUtNS4zMTktMC42NzItOC0wLjY2NCAgICBjLTI2LjUxLTAuMDA5LTQ4LjAwNywyMS40NzQtNDguMDE3LDQ3Ljk4M2MtMC4wMDcsMTkuMDk5LDExLjMxLDM2LjM4NCwyOC44MTcsNDQuMDE3bDYuNC0xNC42NTYgICAgYy0xNi4yMDYtNy4wNS0yMy42MjktMjUuOTAyLTE2LjU4LTQyLjEwOGM1LjA4Ni0xMS42OTMsMTYuNjI5LTE5LjI1LDI5LjM4LTE5LjIzNmM0LjQwOS0wLjAwMSw4Ljc2OSwwLjkyLDEyLjgsMi43MDQgICAgYzQuMDQ5LDEuNzY3LDguNzY1LTAuMDgzLDEwLjUzMi00LjEzMmMwLjQ0LTEuMDA4LDAuNjY3LTIuMDk2LDAuNjY4LTMuMTk2VjIwOGg0MC42OGMtMC40NTUsMi42NDItMC42ODMsNS4zMTktMC42OCw4ICAgIGMwLjA0OCwyMi43OTQsMTYuMDc3LDQyLjQzLDM4LjQsNDcuMDRsMy4yLTE1LjY3MmMtMTQuODgyLTMuMDc5LTI1LjU2Ny0xNi4xNzEtMjUuNi0zMS4zNjhjLTAuMDAxLTQuNDA5LDAuOTItOC43NjksMi43MDQtMTIuOCAgICBjMS43NjctNC4wNDktMC4wODMtOC43NjUtNC4xMzItMTAuNTMyYy0xLjAwOC0wLjQ0LTIuMDk2LTAuNjY3LTMuMTk2LTAuNjY4aC01MS4zNzZ2LTQwLjY4YzIuNjQyLDAuNDU1LDUuMzE5LDAuNjgzLDgsMC42OCAgICBjMjYuNTEsMCw0OC0yMS40OSw0OC00OHMtMjEuNDktNDgtNDgtNDhjLTIuNjgxLTAuMDAzLTUuMzU4LDAuMjI1LTgsMC42OFYxNmgxMzZjMjIuMDgsMC4wMjYsMzkuOTc0LDE3LjkyLDQwLDQwdjEzNmgtNTEuMzc2ICAgIGMtNC40MTgsMC4wMDItNy45OTgsMy41ODYtNy45OTYsOC4wMDRjMC4wMDEsMS4xLDAuMjI4LDIuMTg4LDAuNjY4LDMuMTk2YzEuNzg0LDQuMDMxLDIuNzA1LDguMzkxLDIuNzA0LDEyLjhoMTYgICAgYzAuMDAzLTIuNjgxLTAuMjI1LTUuMzU4LTAuNjgtOGg0OC42OGM0LjQxOCwwLDgtMy41ODIsOC04VjU2QzQwMy42MTgsMjUuMDg5LDM3OC41NjksMC4wNCwzNDcuNjU4LDB6IE0yMTkuNjU4LDcyICAgIGMxNy42NzMsMCwzMiwxNC4zMjcsMzIsMzJzLTE0LjMyNywzMi0zMiwzMmMtNC40MDksMC4wMDEtOC43NjktMC45Mi0xMi44LTIuNzA0Yy00LjA0OS0xLjc2Ny04Ljc2NSwwLjA4My0xMC41MzIsNC4xMzIgICAgYy0wLjQ0LDEuMDA4LTAuNjY3LDIuMDk2LTAuNjY4LDMuMTk2djUwLjcxMmgtNDAuNjI0YzAuNDAxLTIuNDI1LDAuNjA5LTQuODc4LDAuNjI0LTcuMzM2YzAtMC4xNDQsMC0wLjI3MiwwLTAuNDE2ICAgIHMwLTAuMTY4LDAtMC4yNDhjMC0yNi41MS0yMS40OS00OC00OC00OGMtMjYuNTEsMC00OCwyMS40OS00OCw0OGMwLDAuMDgsMCwwLjE2OCwwLDAuMjQ4czAsMC4yNzIsMCwwLjQxNiAgICBjMC4wMTUsMi40NTgsMC4yMjMsNC45MTEsMC42MjQsNy4zMzZIMTkuNjU4VjU2YzAuMDI2LTIyLjA4LDE3LjkyLTM5Ljk3NCw0MC00MGgxMzZ2NTEuMzc2YzAuMDAyLDQuNDE4LDMuNTg2LDcuOTk4LDguMDA0LDcuOTk2ICAgIGMxLjEtMC4wMDEsMi4xODgtMC4yMjgsMy4xOTYtMC42NjhDMjEwLjg4OSw3Mi45MiwyMTUuMjQ5LDcxLjk5OSwyMTkuNjU4LDcyeiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ2Ni41NDYsMzQzLjg1NmwtNjcuMjU2LTQ2LjMxMmwtMjUuODMyLTY5LjAxNmMtMS41NDEtNC4xNDEtNi4xNDctNi4yNDgtMTAuMjg4LTQuNzA3ICAgIGMtMC4xNDgsMC4wNTUtMC4yOTUsMC4xMTUtMC40NCwwLjE3OWwtNTIsMjNjLTMuODk4LDEuNzIxLTUuNzYyLDYuMTk2LTQuMjQsMTAuMTc2YzAuODY0LDIuMjQsMS42NzIsMy45MzYsMi41MjgsNS42OTYgICAgczEuNjQ4LDMuNDA4LDIuNjE2LDYuMDA4YzYuMjEsMTYuNTkzLTIuMTUxLDM1LjA4Ny0xOC43MTIsNDEuMzg0Yy0xNi41NTMsNi4xOTEtMzQuOTkxLTIuMjA4LTQxLjE4My0xOC43NjIgICAgYy0xLjU1My00LjE1Mi0yLjIyNi04LjU4MS0xLjk3Ny0xMy4wMDZjMC4yMzItNC40MTItMy4xNTctOC4xNzctNy41NjktOC40MDljLTEuMTA2LTAuMDU4LTIuMjExLDAuMTE0LTMuMjQ3LDAuNTA1ICAgIGwtNTUuNTg0LDIwLjkzNmMtNC4xMjcsMS41NTUtNi4yMTcsNi4xNTctNC42NzIsMTAuMjg4bDE5Ljk1Miw1My4zMDRjLTIuNjQ1LDAuNDgyLTUuMjQyLDEuMTk5LTcuNzYsMi4xNDQgICAgYy0yNC44OTUsOS4yNzctMzcuNTU1LDM2Ljk3OS0yOC4yNzgsNjEuODc0YzkuMjc3LDI0Ljg5NSwzNi45NzksMzcuNTU1LDYxLjg3NCwyOC4yNzhjMi41NjMtMC45NTUsNS4wNC0yLjEyNyw3LjQwNC0zLjUwNCAgICBsMTQuMzA0LDM4LjIwOGMxLjU1LDQuMTM4LDYuMTYsNi4yMzUsMTAuMjk4LDQuNjg1YzAuMDA1LTAuMDAyLDAuMDA5LTAuMDA0LDAuMDE0LTAuMDA1bDc5LjI2NC0yOS44NjQgICAgYzQuOTc5LDEwLjcwMiwxNC4xNDEsMTguODgzLDI1LjMzNiwyMi42MjRsNS4wNTYtMTUuMmMtNi41OTQtMi4yMjItMTIuMDc0LTYuOTA1LTE1LjI5Ni0xMy4wNzJsMjQuMTItOS4wOCAgICBjNy45MjMsMjIuMjg1LDI5LjAyOSwzNy4xNTksNTIuNjgsMzcuMTI4di0xNmMtMjIuMDM1LTAuMTM1LTM5Ljg2NS0xNy45NjUtNDAtNDBjMC0yLjEyMi0wLjg0NC00LjE1Ni0yLjM0NC01LjY1NmwtMjcuODU2LTI3Ljg1NiAgICBjLTQuMTMtNC4xNjYtNC41OTYtMTAuNzI0LTEuMDk2LTE1LjQzMmMyLjAzOC0yLjc1MSw1LjE4NC00LjQ2NSw4LjYtNC42ODhjMy40MjEtMC4yODgsNi43OSwwLjk3Miw5LjE4NCwzLjQzMmw1NS44NTYsNTUuODU2ICAgIGwxMS4zMTItMTEuMzEybC0xNi41MjgtMTYuNTI4YzQuMDcyLTEyLjE3NywzLjgyMS0yNS4zODYtMC43MTItMzcuNGwtMTQuOTkyLTQwLjA0TDQ1Ny40MjYsMzU3ICAgIGM2LjQwOCw0LjQ5OCwxMC4yMjYsMTEuODM1LDEwLjIzMiwxOS42NjRWNDgwaDE2VjM3Ni42NjRDNDgzLjY0NywzNjMuNTkxLDQ3Ny4yNiwzNTEuMzQ2LDQ2Ni41NDYsMzQzLjg1NnogTTQwOS4wOSwzNjkuMzYgICAgYzIuMjEsNS44NzQsMy4wMSwxMi4xODQsMi4zMzYsMTguNDI0bC0yNS45NjgtMjUuOTZjLTEwLjcxOS0xMC45OTMtMjguMzE5LTExLjIxNC0zOS4zMTItMC40OTYgICAgYy0xMC45OTMsMTAuNzE5LTExLjIxNCwyOC4zMTktMC40OTYsMzkuMzEyYzAuMTYzLDAuMTY3LDAuMzI4LDAuMzMzLDAuNDk2LDAuNDk2bDI1LjMwNCwyNS4zMDRsLTExMy4wNTYsNDIuNTg0bC0xNS4yNDgtNDAuNzQ0ICAgIGMtMC45NDktMi41MzItMy4xMTMtNC40MTItNS43NTItNWMtMC41Ny0wLjEyNi0xLjE1Mi0wLjE5MS0xLjczNi0wLjE5MmMtMi4wOTMtMC4wMDItNC4xMDMsMC44MTctNS42LDIuMjggICAgYy0xMi44NDYsMTIuMTM3LTMzLjEsMTEuNTYyLTQ1LjIzNy0xLjI4NWMtMTIuMTM3LTEyLjg0Ni0xMS41NjItMzMuMSwxLjI4NS00NS4yMzdjMy4wMjItMi44NTUsNi41Ny01LjA5MywxMC40NDgtNi41OTEgICAgYzIuNTQ3LTAuOTQ0LDUuMjA3LTEuNTQ2LDcuOTEyLTEuNzkyYzEuNjY0LTAuMjA0LDMuMzQyLTAuMjYsNS4wMTYtMC4xNjhjNC40MTIsMC4yMzEsOC4xNzctMy4xNTgsOC40MDgtNy41NyAgICBjMC4wNTgtMS4wOTctMC4xMTEtMi4xOTQtMC40OTYtMy4yMjJsLTIwLjg5Ni01NS43OTJsMzguMDk2LTE0LjM0NGM1LDI2LjAzNCwzMC4xNTgsNDMuMDg1LDU2LjE5MiwzOC4wODYgICAgYzIuNjYtMC41MTEsNS4yNzItMS4yNDYsNy44MDgtMi4xOThjMjQuODA3LTkuNDEyLDM3LjM0Ni0zNy4wOTksMjguMDU2LTYxLjk1MmMtMC43MzYtMS45NjgtMS40LTMuNTI4LTIuMDQtNC45MmwzNi44LTE2LjI5NiAgICBMNDA5LjA5LDM2OS4zNnoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                        </div>
                        <div class="col-sm-9">
                            <h3 class="m-t-0">Módulo não incluído</h3>
                            <h4>O seu plano contratado não inclui esta funcionalidade.</h4>
                            <p class="text-muted">
                                <i class="fas fa-check"></i> Emissão automática de todas as faturas<br/>
                                <i class="fas fa-check"></i> Emissão automática de faturas-proforma + Ref. Multibanco<br/>
                                <i class="fas fa-check"></i> Emissão automática de faturas após bom pagamento<br/>
                                <i class="fas fa-check"></i> Emissão de documentos internos<br/>
                                <i class="fas fa-check"></i> Criação de faturas agendadas
                            </p>
                            <a href="mailto:info@quickbox.pt?subject=Pedido de informação sobre módulo&body=Pretendia conhecer mais detalhes e valor para o módulo de faturação avançada"
                               class="btn btn-sm btn-primary m-t-10">
                                <i class="fas fa-envelope"></i> Pedir informações
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    @if(hasModule('invoices-advanced'))
                    <button type="submit" class="btn btn-primary"
                            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A emitir faturas...">Emitir Faturas
                    </button>
                    @endif
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
