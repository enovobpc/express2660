<div class="scanner-result-block" style="display: none">
    <a href="" class="btn btn-sm bg-blue print-manifest pull-right m-t-minus-5" target="_blank">
        <i class="fas fa-print"></i> Imprimir Todas Etiquetas
    </a>
    {{--<a href="{{ route('admin.shipments.create') }}"
        class="btn btn-sm bg-green pull-right m-t-minus-10 m-r-5"
        data-toggle="modal"
        data-target="#modal-remote-xl">
        <i class="fas fa-plus"></i> Novo
    </a>--}}
    <h4 style="margin: 6px 0 18px;"><i class="fas fa-barcode"></i> Serviços Processados</h4>
    <div class="table-responsive">
        <table class="table table-condensed selected-codes m-b-10">
            <thead>
                <tr class="bg-gray-light">
                    <td class="w-1">
                        {{ Form::checkbox('select-all[]', 1, false, ['class' => 'select-all']) }}
                    </td>
                    <th class="w-120px">Envio</th>
                    <th>Destino</th>
                    <th class="w-80px">Data</th>
                    <th class="w-120px">Fornecedor</th>
                    <th class="w-40px">Vol</th>
                    <th class="w-60px">Peso</th>
                    @if(Setting::get('shipments_custom_provider_weight'))
                    <th class="w-65px">Peso Etq</th>
                    @endif
                    <th class="w-40px text-center"><i class="fas fa-print"></i></th>
                    <th class="w-1">Ações</th>
                    <th class="w-1">Save</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <a href="" class="btn btn-sm btn-default print-selected-labels" target="_blank" style="display: none">
            <i class="fas fa-print"></i> Imprimir etiquetas
        </a>
        {{--<hr class="m-t-0 m-b-5"/>
        <ul class="list-inline">
            <li>
                <h4 class="m-0">
                    <small>Volumes</small><br/>
                    <span class="ttl-vol">0</span>
                </h4>
            </li>
            <li>
                <h4 class="m-0 m-l-10">
                    <small>Peso Total</small><br/>
                    <span class="ttl-kg">{{ money(0) }}</span>kg
                </h4>
            </li>
            <li>
                <h4 class="m-0 m-l-10">
                    <small>Portes</small><br/>
                    <span class="ttl-cod">{{ money(0) }}</span>{{ Setting::get('app_currency') }}
                </h4>
            </li>
            <li>
                <h4 class="m-0 m-l-10">
                    <small>Cobranças</small><br/>
                    <span class="ttl-charge">{{ money(0) }}</span>{{ Setting::get('app_currency') }}
                </h4>
            </li>
            <li>
                <h4 class="m-0 m-l-10">
                    <small>Total</small><br/>
                    <span class="ttl-total">{{ money(0) }}</span>{{ Setting::get('app_currency') }}
                </h4>
            </li>
        </ul>--}}
    </div>
</div>
<div class="wellcome-image w-100 m-t-120 text-center">
    <img class="w-120px" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzMDNDNDI7IiBkPSJNNDY5LjMzMyw2NGgtMzJjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42NjdjMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2N2gzMiAgIGMxMS43NzEsMCwyMS4zMzMsOS41NzMsMjEuMzMzLDIxLjMzM3YzMmMwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3UzUxMiwxNDQuNTYzLDUxMiwxMzguNjY3di0zMiAgIEM1MTIsODMuMTM1LDQ5Mi44NTQsNjQsNDY5LjMzMyw2NHoiLz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzMDNDNDI7IiBkPSJNMTAuNjY3LDE0OS4zMzNjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N3YtMzJjMC0xMS43Niw5LjU2My0yMS4zMzMsMjEuMzMzLTIxLjMzM2gzMiAgIGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3Qzg1LjMzMyw2OC43NzEsODAuNTYzLDY0LDc0LjY2Nyw2NGgtMzJDMTkuMTQ2LDY0LDAsODMuMTM1LDAsMTA2LjY2N3YzMiAgIEMwLDE0NC41NjMsNC43NzEsMTQ5LjMzMywxMC42NjcsMTQ5LjMzM3oiLz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzMDNDNDI7IiBkPSJNNTAxLjMzMywzNjIuNjY3Yy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djMyYzAsMTEuNzYtOS41NjMsMjEuMzMzLTIxLjMzMywyMS4zMzNoLTMyICAgYy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3UzQzMS40MzgsNDQ4LDQzNy4zMzMsNDQ4aDMyQzQ5Mi44NTQsNDQ4LDUxMiw0MjguODY1LDUxMiw0MDUuMzMzdi0zMiAgIEM1MTIsMzY3LjQzOCw1MDcuMjI5LDM2Mi42NjcsNTAxLjMzMywzNjIuNjY3eiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik03NC42NjcsNDI2LjY2N2gtMzJjLTExLjc3MSwwLTIxLjMzMy05LjU3My0yMS4zMzMtMjEuMzMzdi0zMmMwLTUuODk2LTQuNzcxLTEwLjY2Ny0xMC42NjctMTAuNjY3ICAgUzAsMzY3LjQzOCwwLDM3My4zMzN2MzJDMCw0MjguODY1LDE5LjE0Niw0NDgsNDIuNjY3LDQ0OGgzMmM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3UzgwLjU2Myw0MjYuNjY3LDc0LjY2Nyw0MjYuNjY3eiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik03NC42NjcsMTI4QzY4Ljc3MSwxMjgsNjQsMTMyLjc3MSw2NCwxMzguNjY3djIzNC42NjdDNjQsMzc5LjIyOSw2OC43NzEsMzg0LDc0LjY2NywzODQgICBjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YxMzguNjY3Qzg1LjMzMywxMzIuNzcxLDgwLjU2MywxMjgsNzQuNjY3LDEyOHoiLz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzMDNDNDI7IiBkPSJNMjg4LDEyOGMtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3YyMzQuNjY3YzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjcgICBjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YxMzguNjY3QzI5OC42NjcsMTMyLjc3MSwyOTMuODk2LDEyOCwyODgsMTI4eiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik00MzcuMzMzLDEyOGMtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3YyMzQuNjY3YzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjcgICBTNDQ4LDM3OS4yMjksNDQ4LDM3My4zMzNWMTM4LjY2N0M0NDgsMTMyLjc3MSw0NDMuMjI5LDEyOCw0MzcuMzMzLDEyOHoiLz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzMDNDNDI7IiBkPSJNMTM4LjY2NywxMjhjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2MTcwLjY2N2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3ICAgczEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjEzOC42NjdDMTQ5LjMzMywxMzIuNzcxLDE0NC41NjMsMTI4LDEzOC42NjcsMTI4eiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik0xODEuMzMzLDEyOGMtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3YxNzAuNjY3YzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjcgICBjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YxMzguNjY3QzE5MiwxMzIuNzcxLDE4Ny4yMjksMTI4LDE4MS4zMzMsMTI4eiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik0yMjQsMTI4Yy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djE3MC42NjdjMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2NyAgIGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjEzOC42NjdDMjM0LjY2NywxMzIuNzcxLDIyOS44OTYsMTI4LDIyNCwxMjh6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMzAzQzQyOyIgZD0iTTMzMC42NjcsMTI4Yy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djE3MC42NjdjMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2NyAgIGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjEzOC42NjdDMzQxLjMzMywxMzIuNzcxLDMzNi41NjMsMTI4LDMzMC42NjcsMTI4eiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik0zOTQuNjY3LDEyOGMtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3YxNzAuNjY3YzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjcgICBjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YxMzguNjY3QzQwNS4zMzMsMTMyLjc3MSw0MDAuNTYzLDEyOCwzOTQuNjY3LDEyOHoiLz4KCTxwYXRoIHN0eWxlPSJmaWxsOiMzMDNDNDI7IiBkPSJNMzk0LjY2NywzNDEuMzMzYy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djIxLjMzM2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3ICAgYzUuODk2LDAsMTAuNjY3LTQuNzcxLDEwLjY2Ny0xMC42NjdWMzUyQzQwNS4zMzMsMzQ2LjEwNCw0MDAuNTYzLDM0MS4zMzMsMzk0LjY2NywzNDEuMzMzeiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik0zMzAuNjY3LDM0MS4zMzNjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2MjEuMzMzYzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjcgICBjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YzNTJDMzQxLjMzMywzNDYuMTA0LDMzNi41NjMsMzQxLjMzMywzMzAuNjY3LDM0MS4zMzN6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMzAzQzQyOyIgZD0iTTIyNCwzNDEuMzMzYy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djIxLjMzM2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3ICAgYzUuODk2LDAsMTAuNjY3LTQuNzcxLDEwLjY2Ny0xMC42NjdWMzUyQzIzNC42NjcsMzQ2LjEwNCwyMjkuODk2LDM0MS4zMzMsMjI0LDM0MS4zMzN6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojMzAzQzQyOyIgZD0iTTE4MS4zMzMsMzQxLjMzM2MtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3YyMS4zMzNjMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2NyAgIGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjM1MkMxOTIsMzQ2LjEwNCwxODcuMjI5LDM0MS4zMzMsMTgxLjMzMywzNDEuMzMzeiIvPgoJPHBhdGggc3R5bGU9ImZpbGw6IzMwM0M0MjsiIGQ9Ik0xMzguNjY3LDM0MS4zMzNjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2MjEuMzMzYzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjcgICBzMTAuNjY3LTQuNzcxLDEwLjY2Ny0xMC42NjdWMzUyQzE0OS4zMzMsMzQ2LjEwNCwxNDQuNTYzLDM0MS4zMzMsMTM4LjY2NywzNDEuMzMzeiIvPgo8L2c+CjxwYXRoIHN0eWxlPSJmaWxsOiNEMzJGMkY7IiBkPSJNNTAxLjMzMywyMzQuNjY3SDEwLjY2N0M0Ljc3MSwyMzQuNjY3LDAsMjM5LjQzOCwwLDI0NS4zMzNDMCwyNTEuMjI5LDQuNzcxLDI1NiwxMC42NjcsMjU2aDQ5MC42NjcgIGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3QzUxMiwyMzkuNDM4LDUwNy4yMjksMjM0LjY2Nyw1MDEuMzMzLDIzNC42Njd6Ii8+CjxsaW5lYXJHcmFkaWVudCBpZD0iU1ZHSURfMV8iIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iLTQ3LjQxMDciIHkxPSI2NDAuNDEwMiIgeDI9Ii0yMS45OTU2IiB5Mj0iNjI4LjU1ODYiIGdyYWRpZW50VHJhbnNmb3JtPSJtYXRyaXgoMjEuMzMzMyAwIDAgLTIxLjMzMzMgOTk2LjMzMzQgMTM3OTEuNjY3KSI+Cgk8c3RvcCBvZmZzZXQ9IjAiIHN0eWxlPSJzdG9wLWNvbG9yOiNGRkZGRkY7c3RvcC1vcGFjaXR5OjAuMiIvPgoJPHN0b3Agb2Zmc2V0PSIxIiBzdHlsZT0ic3RvcC1jb2xvcjojRkZGRkZGO3N0b3Atb3BhY2l0eTowIi8+CjwvbGluZWFyR3JhZGllbnQ+CjxwYXRoIHN0eWxlPSJmaWxsOnVybCgjU1ZHSURfMV8pOyIgZD0iTTEzOC42NjcsMzQxLjMzM2MtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3YyMS4zMzMgIGMwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3czEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjM1MkMxNDkuMzMzLDM0Ni4xMDQsMTQ0LjU2MywzNDEuMzMzLDEzOC42NjcsMzQxLjMzM3ogICBNMTgxLjMzMywzNDEuMzMzYy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djIxLjMzM2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3ICBjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YzNTJDMTkyLDM0Ni4xMDQsMTg3LjIyOSwzNDEuMzMzLDE4MS4zMzMsMzQxLjMzM3ogTTIyNCwzNDEuMzMzICBjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2MjEuMzMzYzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjdjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YzNTIgIEMyMzQuNjY3LDM0Ni4xMDQsMjI5Ljg5NiwzNDEuMzMzLDIyNCwzNDEuMzMzeiBNNzQuNjY3LDQyNi42NjdoLTMyYy0xMS43NzEsMC0yMS4zMzMtOS41NzMtMjEuMzMzLTIxLjMzM3YtMzIgIGMwLTUuODk2LTQuNzcxLTEwLjY2Ny0xMC42NjctMTAuNjY3UzAsMzY3LjQzOCwwLDM3My4zMzN2MzJDMCw0MjguODY1LDE5LjE0Niw0NDgsNDIuNjY3LDQ0OGgzMiAgYzUuODk2LDAsMTAuNjY3LTQuNzcxLDEwLjY2Ny0xMC42NjdTODAuNTYzLDQyNi42NjcsNzQuNjY3LDQyNi42Njd6IE0xMC42NjcsMTQ5LjMzM2M1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3di0zMiAgYzAtMTEuNzYsOS41NjMtMjEuMzMzLDIxLjMzMy0yMS4zMzNoMzJjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N0M4NS4zMzMsNjguNzcxLDgwLjU2Myw2NCw3NC42NjcsNjRoLTMyICBDMTkuMTQ2LDY0LDAsODMuMTM1LDAsMTA2LjY2N3YzMkMwLDE0NC41NjMsNC43NzEsMTQ5LjMzMywxMC42NjcsMTQ5LjMzM3ogTTUwMS4zMzMsMjM0LjY2N0g0NDh2LTk2ICBjMC01Ljg5Ni00Ljc3MS0xMC42NjctMTAuNjY3LTEwLjY2N3MtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2OTZoLTIxLjMzM3YtOTZjMC01Ljg5Ni00Ljc3MS0xMC42NjctMTAuNjY3LTEwLjY2NyAgYy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djk2aC00Mi42Njd2LTk2YzAtNS44OTYtNC43NzEtMTAuNjY3LTEwLjY2Ny0xMC42NjdjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2OTYgIGgtMjEuMzMzdi05NmMwLTUuODk2LTQuNzcxLTEwLjY2Ny0xMC42NjctMTAuNjY3Yy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djk2aC00Mi42Njd2LTk2ICBjMC01Ljg5Ni00Ljc3MS0xMC42NjctMTAuNjY3LTEwLjY2N2MtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3Y5NkgxOTJ2LTk2YzAtNS44OTYtNC43NzEtMTAuNjY3LTEwLjY2Ny0xMC42NjcgIGMtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N3Y5NmgtMjEuMzMzdi05NmMwLTUuODk2LTQuNzcxLTEwLjY2Ny0xMC42NjctMTAuNjY3UzEyOCwxMzIuNzcxLDEyOCwxMzguNjY3djk2SDg1LjMzM3YtOTYgIGMwLTUuODk2LTQuNzcxLTEwLjY2Ny0xMC42NjctMTAuNjY3QzY4Ljc3MSwxMjgsNjQsMTMyLjc3MSw2NCwxMzguNjY3djk2SDEwLjY2N0M0Ljc3MSwyMzQuNjY3LDAsMjM5LjQzOCwwLDI0NS4zMzMgIEMwLDI1MS4yMjksNC43NzEsMjU2LDEwLjY2NywyNTZINjR2MTE3LjMzM0M2NCwzNzkuMjI5LDY4Ljc3MSwzODQsNzQuNjY3LDM4NGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjI1NkgxMjh2NTMuMzMzICBjMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2N3MxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YyNTZoMjEuMzMzdjUzLjMzM2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3ICBjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YyNTZoMjEuMzMzdjUzLjMzM2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3YzUuODk2LDAsMTAuNjY3LTQuNzcxLDEwLjY2Ny0xMC42NjdWMjU2ICBoNDIuNjY3djExNy4zMzNjMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2N2M1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjI1NkgzMjB2NTMuMzMzICBjMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2N2M1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjI1NkgzODR2NTMuMzMzYzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjcgIGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3VjI1NmgyMS4zMzN2MTE3LjMzM2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3UzQ0OCwzNzkuMjI5LDQ0OCwzNzMuMzMzVjI1Nmg1My4zMzMgIGM1Ljg5NiwwLDEwLjY2Ny00Ljc3MSwxMC42NjctMTAuNjY3QzUxMiwyMzkuNDM4LDUwNy4yMjksMjM0LjY2Nyw1MDEuMzMzLDIzNC42Njd6IE00NjkuMzMzLDY0aC0zMiAgYy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3YzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjdoMzJjMTEuNzcxLDAsMjEuMzMzLDkuNTczLDIxLjMzMywyMS4zMzN2MzIgIGMwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3UzUxMiwxNDQuNTYzLDUxMiwxMzguNjY3di0zMkM1MTIsODMuMTM1LDQ5Mi44NTQsNjQsNDY5LjMzMyw2NHogTTMzMC42NjcsMzQxLjMzMyAgYy01Ljg5NiwwLTEwLjY2Nyw0Ljc3MS0xMC42NjcsMTAuNjY3djIxLjMzM2MwLDUuODk2LDQuNzcxLDEwLjY2NywxMC42NjcsMTAuNjY3YzUuODk2LDAsMTAuNjY3LTQuNzcxLDEwLjY2Ny0xMC42NjdWMzUyICBDMzQxLjMzMywzNDYuMTA0LDMzNi41NjMsMzQxLjMzMywzMzAuNjY3LDM0MS4zMzN6IE01MDEuMzMzLDM2Mi42NjdjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2MzIgIGMwLDExLjc2LTkuNTYzLDIxLjMzMy0yMS4zMzMsMjEuMzMzaC0zMmMtNS44OTYsMC0xMC42NjcsNC43NzEtMTAuNjY3LDEwLjY2N1M0MzEuNDM4LDQ0OCw0MzcuMzMzLDQ0OGgzMiAgQzQ5Mi44NTQsNDQ4LDUxMiw0MjguODY1LDUxMiw0MDUuMzMzdi0zMkM1MTIsMzY3LjQzOCw1MDcuMjI5LDM2Mi42NjcsNTAxLjMzMywzNjIuNjY3eiBNMzk0LjY2NywzNDEuMzMzICBjLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42Njd2MjEuMzMzYzAsNS44OTYsNC43NzEsMTAuNjY3LDEwLjY2NywxMC42NjdjNS44OTYsMCwxMC42NjctNC43NzEsMTAuNjY3LTEwLjY2N1YzNTIgIEM0MDUuMzMzLDM0Ni4xMDQsNDAwLjU2MywzNDEuMzMzLDM5NC42NjcsMzQxLjMzM3oiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
    <h4 class="text-muted lh-1-4">
        Para começar, configure a leitura no configurador lateral.
        <br/>
        Utilize um leitor de códigos de barras para ler os envios ou escreva manualmente o código do envio.
    </h4>
</div>