@include('mobile.pages.incidences')
<div class="window shipment-full-detail" style="z-index: 999999999;">
    <header>
        <div class="action-buttons-right">
            <ul>
                <li>
                    <a href="{{ route('mobile.scanner') }}">
                        <img style="height: 28px;" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgd2lkdGg9IjUxMiIgY2xhc3M9IiI+PGc+PHBhdGggZD0ibTMwIDMwaDkwdi0zMGgtMTIwdjEyMGgzMHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTM5MiAwdjMwaDkwdjkwaDMwdi0xMjB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im00ODIgNDgyaC05MHYzMGgxMjB2LTEyMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zMCAzOTJoLTMwdjEyMGgxMjB2LTMwaC05MHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTYxIDYwdjE1MGgxNTB2LTkwaDMwdi0zMGgtMzB2LTMwem0xMjAgMTIwaC05MHYtOTBoOTB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im00NTEgNDUwdi0xNTBoLTYwdi0zMGgtMzB2MzBoLTkwdjMwaDMwdjMwaC0zMHYzMGgzMHY2MHptLTEyMC0xMjBoOTB2OTBoLTkwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMTUxIDI3MGg2MHYtMzBoLTE1MHYzMGg2MHYzMGgtMzB2MzBoMzB2NjBoLTMwdjMwaDMwdjMwaDE1MHYtMzBoLTMwdi0zMGgtMzB2MzBoLTYwdi0zMGgzMHYtMzBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMTIxIDEyMGgzMHYzMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zNjEgMTIwaDMwdjMwaC0zMHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTM5MSAyMTBoNjB2LTE1MGgtMTUwdjE1MGg2MHYzMGgzMHptLTYwLTMwdi05MGg5MHY5MHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTQ1MSAyNzB2LTMwYy03LjI1NzgxMiAwLTUyLjY5MTQwNiAwLTYwIDB2MzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zNjEgMzYwaDMwdjMwaC0zMHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTI0MSAzMzBoMzB2MzBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMTgxIDM2MGgzMGMwLTcuMjU3ODEyIDAtNTIuNjkxNDA2IDAtNjBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMjExIDI3MGgzMHYzMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im05MSAzMzBoLTMwdjYwaDMwYzAtNy4yNTc4MTIgMC01Mi42OTE0MDYgMC02MHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTYxIDQyMGgzMHYzMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0yNDEgNjBoMzB2MzBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMjQxIDE4MGgzMGMwLTcuMjU3ODEyIDAtNTIuNjkxNDA2IDAtNjBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMjcxIDI0MHYtMzBoLTMwdjYwaDEyMHYtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjwvZz4gPC9zdmc+" />
                    </a>
                </li>
            </ul>
        </div>
        <div class="action-buttons-left">
            <ul>
                <li>
                    <a href="{{ route('mobile.index') }}" data-toggle="ajax" data-method="get" data-target="#main-window">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDQ2MC4yOTggNDYwLjI5NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDYwLjI5OCA0NjAuMjk3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTIzMC4xNDksMTIwLjkzOUw2NS45ODYsMjU2LjI3NGMwLDAuMTkxLTAuMDQ4LDAuNDcyLTAuMTQ0LDAuODU1Yy0wLjA5NCwwLjM4LTAuMTQ0LDAuNjU2LTAuMTQ0LDAuODUydjEzNy4wNDEgICAgYzAsNC45NDgsMS44MDksOS4yMzYsNS40MjYsMTIuODQ3YzMuNjE2LDMuNjEzLDcuODk4LDUuNDMxLDEyLjg0Nyw1LjQzMWgxMDkuNjNWMzAzLjY2NGg3My4wOTd2MTA5LjY0aDEwOS42MjkgICAgYzQuOTQ4LDAsOS4yMzYtMS44MTQsMTIuODQ3LTUuNDM1YzMuNjE3LTMuNjA3LDUuNDMyLTcuODk4LDUuNDMyLTEyLjg0N1YyNTcuOTgxYzAtMC43Ni0wLjEwNC0xLjMzNC0wLjI4OC0xLjcwN0wyMzAuMTQ5LDEyMC45MzkgICAgeiIgZmlsbD0iI0ZGRkZGRiIvPgoJCTxwYXRoIGQ9Ik00NTcuMTIyLDIyNS40MzhMMzk0LjYsMTczLjQ3NlY1Ni45ODljMC0yLjY2My0wLjg1Ni00Ljg1My0yLjU3NC02LjU2N2MtMS43MDQtMS43MTItMy44OTQtMi41NjgtNi41NjMtMi41NjhoLTU0LjgxNiAgICBjLTIuNjY2LDAtNC44NTUsMC44NTYtNi41NywyLjU2OGMtMS43MTEsMS43MTQtMi41NjYsMy45MDUtMi41NjYsNi41Njd2NTUuNjczbC02OS42NjItNTguMjQ1ICAgIGMtNi4wODQtNC45NDktMTMuMzE4LTcuNDIzLTIxLjY5NC03LjQyM2MtOC4zNzUsMC0xNS42MDgsMi40NzQtMjEuNjk4LDcuNDIzTDMuMTcyLDIyNS40MzhjLTEuOTAzLDEuNTItMi45NDYsMy41NjYtMy4xNCw2LjEzNiAgICBjLTAuMTkzLDIuNTY4LDAuNDcyLDQuODExLDEuOTk3LDYuNzEzbDE3LjcwMSwyMS4xMjhjMS41MjUsMS43MTIsMy41MjEsMi43NTksNS45OTYsMy4xNDJjMi4yODUsMC4xOTIsNC41Ny0wLjQ3Niw2Ljg1NS0xLjk5OCAgICBMMjMwLjE0OSw5NS44MTdsMTk3LjU3LDE2NC43NDFjMS41MjYsMS4zMjgsMy41MjEsMS45OTEsNS45OTYsMS45OTFoMC44NThjMi40NzEtMC4zNzYsNC40NjMtMS40Myw1Ljk5Ni0zLjEzOGwxNy43MDMtMjEuMTI1ICAgIGMxLjUyMi0xLjkwNiwyLjE4OS00LjE0NSwxLjk5MS02LjcxNkM0NjAuMDY4LDIyOS4wMDcsNDU5LjAyMSwyMjYuOTYxLDQ1Ny4xMjIsMjI1LjQzOHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                    </a>
                </li>
            </ul>
        </div>
        @include('mobile.partials.logo')
    </header>
    <div class="p-5">
        <div style="position: fixed;
    top: 57px;
    bottom: 50px;
    right: 0;
    left: 0;
    overflow: scroll;
    padding-top: 10px;
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 15px;
    margin-bottom: 0;
            ">
            <table class="w-100">
                <tr>
                    <td>
                        <h4 class="m-t-0 m-b-10 tracking-number">{{ $shipment->tracking_code }}</h4>
                        <dl class="ui-dl dl-status">
                            <dt>Estado</dt>
                            <dd>{{ @$shipment->status->name }}</dd>
                            <dt>Serviço</dt>
                            <dd>{{ @$shipment->service->display_code }}</dd>
                        </dl>
                    </td>
                    <td>
                        <h4 class="m-t-0 details-total-charge">
                            <small>Total a Cobrar</small><br/>
                            @if($shipment->payment_at_recipient)
                                {{ money($shipment->charge_price + $shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}
                            @else
                                {{ money($shipment->charge_price + $shipment->total_price_for_recipient, Setting::get('app_currency')) }}
                            @endif
                        </h4>
                    </td>
                </tr>
            </table>
            <div class="clearfix"></div>
            <hr class="m-t-5 m-b-5"/>
            @if($shipment->is_collection)
                <p style="margin-bottom: 5px">
                    <span class="text-primary">Local de Recolha</span>
                    <br/>
                    <b class="text-uppercase">{{ $shipment->sender_name }}</b>
                    <br/>
                    {{ $shipment->sender_address }}<br/>
                    {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}<br/>
                    <a href="tel:{{ $shipment->sender_phone }}">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU3OC4xMDYgNTc4LjEwNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTc4LjEwNiA1NzguMTA2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTU3Ny44Myw0NTYuMTI4YzEuMjI1LDkuMzg1LTEuNjM1LDE3LjU0NS04LjU2OCwyNC40OGwtODEuMzk2LDgwLjc4MSAgICBjLTMuNjcyLDQuMDgtOC40NjUsNy41NTEtMTQuMzgxLDEwLjQwNGMtNS45MTYsMi44NTctMTEuNzI5LDQuNjkzLTE3LjQzOSw1LjUwOGMtMC40MDgsMC0xLjYzNSwwLjEwNS0zLjY3NiwwLjMwOSAgICBjLTIuMDM3LDAuMjAzLTQuNjg5LDAuMzA3LTcuOTUzLDAuMzA3Yy03Ljc1NCwwLTIwLjMwMS0xLjMyNi0zNy42NDEtMy45NzlzLTM4LjU1NS05LjE4Mi02My42NDUtMTkuNTg0ICAgIGMtMjUuMDk2LTEwLjQwNC01My41NTMtMjYuMDEyLTg1LjM3Ni00Ni44MThjLTMxLjgyMy0yMC44MDUtNjUuNjg4LTQ5LjM2Ny0xMDEuNTkyLTg1LjY4ICAgIGMtMjguNTYtMjguMTUyLTUyLjIyNC01NS4wOC03MC45OTItODAuNzgzYy0xOC43NjgtMjUuNzA1LTMzLjg2NC00OS40NzEtNDUuMjg4LTcxLjI5OSAgICBjLTExLjQyNS0yMS44MjgtMTkuOTkzLTQxLjYxNi0yNS43MDUtNTkuMzY0UzQuNTksMTc3LjM2MiwyLjU1LDE2NC41MXMtMi44NTYtMjIuOTUtMi40NDgtMzAuMjk0ICAgIGMwLjQwOC03LjM0NCwwLjYxMi0xMS40MjQsMC42MTItMTIuMjRjMC44MTYtNS43MTIsMi42NTItMTEuNTI2LDUuNTA4LTE3LjQ0MnM2LjMyNC0xMC43MSwxMC40MDQtMTQuMzgyTDk4LjAyMiw4Ljc1NiAgICBjNS43MTItNS43MTIsMTIuMjQtOC41NjgsMTkuNTg0LTguNTY4YzUuMzA0LDAsOS45OTYsMS41MywxNC4wNzYsNC41OXM3LjU0OCw2LjgzNCwxMC40MDQsMTEuMzIybDY1LjQ4NCwxMjQuMjM2ICAgIGMzLjY3Miw2LjUyOCw0LjY5MiwxMy42NjgsMy4wNiwyMS40MmMtMS42MzIsNy43NTItNS4xLDE0LjI4LTEwLjQwNCwxOS41ODRsLTI5Ljk4OCwyOS45ODhjLTAuODE2LDAuODE2LTEuNTMsMi4xNDItMi4xNDIsMy45NzggICAgcy0wLjkxOCwzLjM2Ni0wLjkxOCw0LjU5YzEuNjMyLDguNTY4LDUuMzA0LDE4LjM2LDExLjAxNiwyOS4zNzZjNC44OTYsOS43OTIsMTIuNDQ0LDIxLjcyNiwyMi42NDQsMzUuODAyICAgIHMyNC42ODQsMzAuMjkzLDQzLjQ1Miw0OC42NTNjMTguMzYsMTguNzcsMzQuNjgsMzMuMzU0LDQ4Ljk2LDQzLjc2YzE0LjI3NywxMC40LDI2LjIxNSwxOC4wNTMsMzUuODAzLDIyLjk0OSAgICBjOS41ODgsNC44OTYsMTYuOTMyLDcuODU0LDIyLjAzMSw4Ljg3MWw3LjY0OCwxLjUzMWMwLjgxNiwwLDIuMTQ1LTAuMzA3LDMuOTc5LTAuOTE4YzEuODM2LTAuNjEzLDMuMTYyLTEuMzI2LDMuOTc5LTIuMTQzICAgIGwzNC44ODMtMzUuNDk2YzcuMzQ4LTYuNTI3LDE1LjkxMi05Ljc5MSwyNS43MDUtOS43OTFjNi45MzgsMCwxMi40NDMsMS4yMjMsMTYuNTIzLDMuNjcyaDAuNjExbDExOC4xMTUsNjkuNzY4ICAgIEM1NzEuMDk4LDQ0MS4yMzgsNTc2LjE5Nyw0NDcuOTY4LDU3Ny44Myw0NTYuMTI4eiIgZmlsbD0iIzMzMzMzMyIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /> {{ $shipment->sender_phone }}
                    </a>
                </p>
                <p>
                    <span class="text-primary">Destinatário</span>
                    <br/>
                    @if(Setting::get('mobile_app_details_full_sender'))
                        <br/>
                        <span class="text-uppercase">
                    {!! $shipment->recipient_attn ?  $shipment->recipient_attn.'<br/>' : '' !!}
                            <b>{{ $shipment->recipient_name }}</b>
                    <br/>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                    <a href="tel:{{ $shipment->recipient_phone }}">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU3OC4xMDYgNTc4LjEwNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTc4LjEwNiA1NzguMTA2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTU3Ny44Myw0NTYuMTI4YzEuMjI1LDkuMzg1LTEuNjM1LDE3LjU0NS04LjU2OCwyNC40OGwtODEuMzk2LDgwLjc4MSAgICBjLTMuNjcyLDQuMDgtOC40NjUsNy41NTEtMTQuMzgxLDEwLjQwNGMtNS45MTYsMi44NTctMTEuNzI5LDQuNjkzLTE3LjQzOSw1LjUwOGMtMC40MDgsMC0xLjYzNSwwLjEwNS0zLjY3NiwwLjMwOSAgICBjLTIuMDM3LDAuMjAzLTQuNjg5LDAuMzA3LTcuOTUzLDAuMzA3Yy03Ljc1NCwwLTIwLjMwMS0xLjMyNi0zNy42NDEtMy45NzlzLTM4LjU1NS05LjE4Mi02My42NDUtMTkuNTg0ICAgIGMtMjUuMDk2LTEwLjQwNC01My41NTMtMjYuMDEyLTg1LjM3Ni00Ni44MThjLTMxLjgyMy0yMC44MDUtNjUuNjg4LTQ5LjM2Ny0xMDEuNTkyLTg1LjY4ICAgIGMtMjguNTYtMjguMTUyLTUyLjIyNC01NS4wOC03MC45OTItODAuNzgzYy0xOC43NjgtMjUuNzA1LTMzLjg2NC00OS40NzEtNDUuMjg4LTcxLjI5OSAgICBjLTExLjQyNS0yMS44MjgtMTkuOTkzLTQxLjYxNi0yNS43MDUtNTkuMzY0UzQuNTksMTc3LjM2MiwyLjU1LDE2NC41MXMtMi44NTYtMjIuOTUtMi40NDgtMzAuMjk0ICAgIGMwLjQwOC03LjM0NCwwLjYxMi0xMS40MjQsMC42MTItMTIuMjRjMC44MTYtNS43MTIsMi42NTItMTEuNTI2LDUuNTA4LTE3LjQ0MnM2LjMyNC0xMC43MSwxMC40MDQtMTQuMzgyTDk4LjAyMiw4Ljc1NiAgICBjNS43MTItNS43MTIsMTIuMjQtOC41NjgsMTkuNTg0LTguNTY4YzUuMzA0LDAsOS45OTYsMS41MywxNC4wNzYsNC41OXM3LjU0OCw2LjgzNCwxMC40MDQsMTEuMzIybDY1LjQ4NCwxMjQuMjM2ICAgIGMzLjY3Miw2LjUyOCw0LjY5MiwxMy42NjgsMy4wNiwyMS40MmMtMS42MzIsNy43NTItNS4xLDE0LjI4LTEwLjQwNCwxOS41ODRsLTI5Ljk4OCwyOS45ODhjLTAuODE2LDAuODE2LTEuNTMsMi4xNDItMi4xNDIsMy45NzggICAgcy0wLjkxOCwzLjM2Ni0wLjkxOCw0LjU5YzEuNjMyLDguNTY4LDUuMzA0LDE4LjM2LDExLjAxNiwyOS4zNzZjNC44OTYsOS43OTIsMTIuNDQ0LDIxLjcyNiwyMi42NDQsMzUuODAyICAgIHMyNC42ODQsMzAuMjkzLDQzLjQ1Miw0OC42NTNjMTguMzYsMTguNzcsMzQuNjgsMzMuMzU0LDQ4Ljk2LDQzLjc2YzE0LjI3NywxMC40LDI2LjIxNSwxOC4wNTMsMzUuODAzLDIyLjk0OSAgICBjOS41ODgsNC44OTYsMTYuOTMyLDcuODU0LDIyLjAzMSw4Ljg3MWw3LjY0OCwxLjUzMWMwLjgxNiwwLDIuMTQ1LTAuMzA3LDMuOTc5LTAuOTE4YzEuODM2LTAuNjEzLDMuMTYyLTEuMzI2LDMuOTc5LTIuMTQzICAgIGwzNC44ODMtMzUuNDk2YzcuMzQ4LTYuNTI3LDE1LjkxMi05Ljc5MSwyNS43MDUtOS43OTFjNi45MzgsMCwxMi40NDMsMS4yMjMsMTYuNTIzLDMuNjcyaDAuNjExbDExOC4xMTUsNjkuNzY4ICAgIEM1NzEuMDk4LDQ0MS4yMzgsNTc2LjE5Nyw0NDcuOTY4LDU3Ny44Myw0NTYuMTI4eiIgZmlsbD0iIzMzMzMzMyIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /> {{ $shipment->recipient_phone }}
                    </a>
                            @else
                                <span class="text-uppercase">
                    <b>{{ $shipment->recipient_name }}</b>
                </span>
                                <br/>
                    @endif
                </p>
            @else
                <p style="margin-bottom: 5px">
                    <span class="text-primary">Remetente</span>
                    @if(Setting::get('mobile_app_details_full_sender'))
                        @if($shipment->sender_attn)
                            <br/>
                            {{ $shipment->sender_attn }}
                        @endif
                        <br/>
                        <span class="text-uppercase">
                    <b>{{ $shipment->sender_name }}</b>
                    <br/>
                            {{ $shipment->sender_address }}<br/>
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}<br/>
                    <a href="tel:{{ $shipment->sender_phone }}">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU3OC4xMDYgNTc4LjEwNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTc4LjEwNiA1NzguMTA2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTU3Ny44Myw0NTYuMTI4YzEuMjI1LDkuMzg1LTEuNjM1LDE3LjU0NS04LjU2OCwyNC40OGwtODEuMzk2LDgwLjc4MSAgICBjLTMuNjcyLDQuMDgtOC40NjUsNy41NTEtMTQuMzgxLDEwLjQwNGMtNS45MTYsMi44NTctMTEuNzI5LDQuNjkzLTE3LjQzOSw1LjUwOGMtMC40MDgsMC0xLjYzNSwwLjEwNS0zLjY3NiwwLjMwOSAgICBjLTIuMDM3LDAuMjAzLTQuNjg5LDAuMzA3LTcuOTUzLDAuMzA3Yy03Ljc1NCwwLTIwLjMwMS0xLjMyNi0zNy42NDEtMy45NzlzLTM4LjU1NS05LjE4Mi02My42NDUtMTkuNTg0ICAgIGMtMjUuMDk2LTEwLjQwNC01My41NTMtMjYuMDEyLTg1LjM3Ni00Ni44MThjLTMxLjgyMy0yMC44MDUtNjUuNjg4LTQ5LjM2Ny0xMDEuNTkyLTg1LjY4ICAgIGMtMjguNTYtMjguMTUyLTUyLjIyNC01NS4wOC03MC45OTItODAuNzgzYy0xOC43NjgtMjUuNzA1LTMzLjg2NC00OS40NzEtNDUuMjg4LTcxLjI5OSAgICBjLTExLjQyNS0yMS44MjgtMTkuOTkzLTQxLjYxNi0yNS43MDUtNTkuMzY0UzQuNTksMTc3LjM2MiwyLjU1LDE2NC41MXMtMi44NTYtMjIuOTUtMi40NDgtMzAuMjk0ICAgIGMwLjQwOC03LjM0NCwwLjYxMi0xMS40MjQsMC42MTItMTIuMjRjMC44MTYtNS43MTIsMi42NTItMTEuNTI2LDUuNTA4LTE3LjQ0MnM2LjMyNC0xMC43MSwxMC40MDQtMTQuMzgyTDk4LjAyMiw4Ljc1NiAgICBjNS43MTItNS43MTIsMTIuMjQtOC41NjgsMTkuNTg0LTguNTY4YzUuMzA0LDAsOS45OTYsMS41MywxNC4wNzYsNC41OXM3LjU0OCw2LjgzNCwxMC40MDQsMTEuMzIybDY1LjQ4NCwxMjQuMjM2ICAgIGMzLjY3Miw2LjUyOCw0LjY5MiwxMy42NjgsMy4wNiwyMS40MmMtMS42MzIsNy43NTItNS4xLDE0LjI4LTEwLjQwNCwxOS41ODRsLTI5Ljk4OCwyOS45ODhjLTAuODE2LDAuODE2LTEuNTMsMi4xNDItMi4xNDIsMy45NzggICAgcy0wLjkxOCwzLjM2Ni0wLjkxOCw0LjU5YzEuNjMyLDguNTY4LDUuMzA0LDE4LjM2LDExLjAxNiwyOS4zNzZjNC44OTYsOS43OTIsMTIuNDQ0LDIxLjcyNiwyMi42NDQsMzUuODAyICAgIHMyNC42ODQsMzAuMjkzLDQzLjQ1Miw0OC42NTNjMTguMzYsMTguNzcsMzQuNjgsMzMuMzU0LDQ4Ljk2LDQzLjc2YzE0LjI3NywxMC40LDI2LjIxNSwxOC4wNTMsMzUuODAzLDIyLjk0OSAgICBjOS41ODgsNC44OTYsMTYuOTMyLDcuODU0LDIyLjAzMSw4Ljg3MWw3LjY0OCwxLjUzMWMwLjgxNiwwLDIuMTQ1LTAuMzA3LDMuOTc5LTAuOTE4YzEuODM2LTAuNjEzLDMuMTYyLTEuMzI2LDMuOTc5LTIuMTQzICAgIGwzNC44ODMtMzUuNDk2YzcuMzQ4LTYuNTI3LDE1LjkxMi05Ljc5MSwyNS43MDUtOS43OTFjNi45MzgsMCwxMi40NDMsMS4yMjMsMTYuNTIzLDMuNjcyaDAuNjExbDExOC4xMTUsNjkuNzY4ICAgIEM1NzEuMDk4LDQ0MS4yMzgsNTc2LjE5Nyw0NDcuOTY4LDU3Ny44Myw0NTYuMTI4eiIgZmlsbD0iIzMzMzMzMyIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /> {{ $shipment->sender_phone }}
                    </a>
                            @else
                                <br/>
                                <b class="text-uppercase">{{ $shipment->sender_name }}</b>
                    @endif
                </p>
                <p>
                    <span class="text-primary">Destinatário</span>
                    <br/>
                    <span class="text-uppercase">
                {!! $shipment->recipient_attn ?  $shipment->recipient_attn.'<br/>' : '' !!}
                        <b>{{ $shipment->recipient_name }}</b>
                <br/>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                <a href="tel:{{ $shipment->recipient_phone }}">
                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU3OC4xMDYgNTc4LjEwNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTc4LjEwNiA1NzguMTA2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTU3Ny44Myw0NTYuMTI4YzEuMjI1LDkuMzg1LTEuNjM1LDE3LjU0NS04LjU2OCwyNC40OGwtODEuMzk2LDgwLjc4MSAgICBjLTMuNjcyLDQuMDgtOC40NjUsNy41NTEtMTQuMzgxLDEwLjQwNGMtNS45MTYsMi44NTctMTEuNzI5LDQuNjkzLTE3LjQzOSw1LjUwOGMtMC40MDgsMC0xLjYzNSwwLjEwNS0zLjY3NiwwLjMwOSAgICBjLTIuMDM3LDAuMjAzLTQuNjg5LDAuMzA3LTcuOTUzLDAuMzA3Yy03Ljc1NCwwLTIwLjMwMS0xLjMyNi0zNy42NDEtMy45NzlzLTM4LjU1NS05LjE4Mi02My42NDUtMTkuNTg0ICAgIGMtMjUuMDk2LTEwLjQwNC01My41NTMtMjYuMDEyLTg1LjM3Ni00Ni44MThjLTMxLjgyMy0yMC44MDUtNjUuNjg4LTQ5LjM2Ny0xMDEuNTkyLTg1LjY4ICAgIGMtMjguNTYtMjguMTUyLTUyLjIyNC01NS4wOC03MC45OTItODAuNzgzYy0xOC43NjgtMjUuNzA1LTMzLjg2NC00OS40NzEtNDUuMjg4LTcxLjI5OSAgICBjLTExLjQyNS0yMS44MjgtMTkuOTkzLTQxLjYxNi0yNS43MDUtNTkuMzY0UzQuNTksMTc3LjM2MiwyLjU1LDE2NC41MXMtMi44NTYtMjIuOTUtMi40NDgtMzAuMjk0ICAgIGMwLjQwOC03LjM0NCwwLjYxMi0xMS40MjQsMC42MTItMTIuMjRjMC44MTYtNS43MTIsMi42NTItMTEuNTI2LDUuNTA4LTE3LjQ0MnM2LjMyNC0xMC43MSwxMC40MDQtMTQuMzgyTDk4LjAyMiw4Ljc1NiAgICBjNS43MTItNS43MTIsMTIuMjQtOC41NjgsMTkuNTg0LTguNTY4YzUuMzA0LDAsOS45OTYsMS41MywxNC4wNzYsNC41OXM3LjU0OCw2LjgzNCwxMC40MDQsMTEuMzIybDY1LjQ4NCwxMjQuMjM2ICAgIGMzLjY3Miw2LjUyOCw0LjY5MiwxMy42NjgsMy4wNiwyMS40MmMtMS42MzIsNy43NTItNS4xLDE0LjI4LTEwLjQwNCwxOS41ODRsLTI5Ljk4OCwyOS45ODhjLTAuODE2LDAuODE2LTEuNTMsMi4xNDItMi4xNDIsMy45NzggICAgcy0wLjkxOCwzLjM2Ni0wLjkxOCw0LjU5YzEuNjMyLDguNTY4LDUuMzA0LDE4LjM2LDExLjAxNiwyOS4zNzZjNC44OTYsOS43OTIsMTIuNDQ0LDIxLjcyNiwyMi42NDQsMzUuODAyICAgIHMyNC42ODQsMzAuMjkzLDQzLjQ1Miw0OC42NTNjMTguMzYsMTguNzcsMzQuNjgsMzMuMzU0LDQ4Ljk2LDQzLjc2YzE0LjI3NywxMC40LDI2LjIxNSwxOC4wNTMsMzUuODAzLDIyLjk0OSAgICBjOS41ODgsNC44OTYsMTYuOTMyLDcuODU0LDIyLjAzMSw4Ljg3MWw3LjY0OCwxLjUzMWMwLjgxNiwwLDIuMTQ1LTAuMzA3LDMuOTc5LTAuOTE4YzEuODM2LTAuNjEzLDMuMTYyLTEuMzI2LDMuOTc5LTIuMTQzICAgIGwzNC44ODMtMzUuNDk2YzcuMzQ4LTYuNTI3LDE1LjkxMi05Ljc5MSwyNS43MDUtOS43OTFjNi45MzgsMCwxMi40NDMsMS4yMjMsMTYuNTIzLDMuNjcyaDAuNjExbDExOC4xMTUsNjkuNzY4ICAgIEM1NzEuMDk4LDQ0MS4yMzgsNTc2LjE5Nyw0NDcuOTY4LDU3Ny44Myw0NTYuMTI4eiIgZmlsbD0iIzMzMzMzMyIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /> {{ $shipment->recipient_phone }}
                </a>
            </span>
                </p>
            @endif

            <hr class="m-t-5 m-b-5"/>
            <table class="w-100">
                <tr>
                    <td>
                        <dl class="ui-dl">
                            <dt>Reembolso</dt>
                            <dd>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</dd>
                            @if($shipment->payment_at_recipient)
                                <dt>Portes</dt>
                                <dd>{{ money($shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}</dd>
                            @else
                                <dt>Portes</dt>
                                <dd>{{ money(0, Setting::get('app_currency')) }}</dd>
                            @endif
                        </dl>
                    </td>
                    <td>
                        <dl class="ui-dl">
                            <dt>Volumes</dt>
                            <dd>{{ $shipment->volumes }}</dd>
                            <dt>Peso</dt>
                            <dd>{{ $shipment->weight }} Kg</dd>
                        </dl>
                    </td>
                </tr>
            </table>
            <hr class="m-t-5 m-b-5"/>
            <table class="w-100">
                <tr>
                    <td>
                        <dl class="ui-dl">
                            <dt>
                                @if(is_array($shipment->has_return) && in_array('rpack', $shipment->has_return))
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDQzOC41MzYgNDM4LjUzNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDM4LjUzNiA0MzguNTM2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTQxNC40MSwyNC4xMjNDMzk4LjMzMyw4LjA0MiwzNzguOTYzLDAsMzU2LjMxNSwwSDgyLjIyOEM1OS41OCwwLDQwLjIxLDguMDQyLDI0LjEyNiwyNC4xMjMgICBDOC4wNDUsNDAuMjA3LDAuMDAzLDU5LjU3NiwwLjAwMyw4Mi4yMjV2Mjc0LjA4NGMwLDIyLjY0Nyw4LjA0Miw0Mi4wMTgsMjQuMTIzLDU4LjEwMmMxNi4wODQsMTYuMDg0LDM1LjQ1NCwyNC4xMjYsNTguMTAyLDI0LjEyNiAgIGgyNzQuMDg0YzIyLjY0OCwwLDQyLjAxOC04LjA0Miw1OC4wOTUtMjQuMTI2YzE2LjA4NC0xNi4wODQsMjQuMTI2LTM1LjQ1NCwyNC4xMjYtNTguMTAyVjgyLjIyNSAgIEM0MzguNTMyLDU5LjU3Niw0MzAuNDksNDAuMjA0LDQxNC40MSwyNC4xMjN6IE0zNzAuODgsMTU5LjAyNGwtMTc1LjMwNywxNzUuM2MtMy42MTUsMy42MTQtNy44OTgsNS40MjgtMTIuODUsNS40MjggICBjLTQuOTUsMC05LjIzMy0xLjgwNy0xMi44NS01LjQyMUw2Ny42NjMsMjMyLjExOGMtMy42MTYtMy42Mi01LjQyNC03Ljg5OC01LjQyNC0xMi44NDhjMC00Ljk0OSwxLjgwOS05LjIzMyw1LjQyNC0xMi44NDcgICBsMjkuMTI0LTI5LjEyNGMzLjYxNy0zLjYxNiw3Ljg5NS01LjQyNCwxMi44NDctNS40MjRjNC45NTIsMCw5LjIzNSwxLjgwOSwxMi44NTEsNS40MjRsNjAuMjQyLDYwLjI0bDEzMy4zMzQtMTMzLjMzMyAgIGMzLjYwNi0zLjYxNyw3Ljg5OC01LjQyNCwxMi44NDctNS40MjRjNC45NDUsMCw5LjIyNywxLjgwNywxMi44NDcsNS40MjRsMjkuMTI2LDI5LjEyNWMzLjYxLDMuNjE1LDUuNDIxLDcuODk4LDUuNDIxLDEyLjg0NyAgIFMzNzQuNDksMTU1LjQxMSwzNzAuODgsMTU5LjAyNHoiIGZpbGw9IiMzMzMzMzMiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                                @else
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDQwMS45OTggNDAxLjk5OCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDAxLjk5OCA0MDEuOTk4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTM3Ny44NywyNC4xMjZDMzYxLjc4Niw4LjA0MiwzNDIuNDE3LDAsMzE5Ljc2OSwwSDgyLjIyN0M1OS41NzksMCw0MC4yMTEsOC4wNDIsMjQuMTI1LDI0LjEyNiAgIEM4LjA0NCw0MC4yMTIsMC4wMDIsNTkuNTc2LDAuMDAyLDgyLjIyOHYyMzcuNTQzYzAsMjIuNjQ3LDguMDQyLDQyLjAxNCwyNC4xMjMsNTguMTAxYzE2LjA4NiwxNi4wODUsMzUuNDU0LDI0LjEyNyw1OC4xMDIsMjQuMTI3ICAgaDIzNy41NDJjMjIuNjQ4LDAsNDIuMDExLTguMDQyLDU4LjEwMi0yNC4xMjdjMTYuMDg1LTE2LjA4NywyNC4xMjYtMzUuNDUzLDI0LjEyNi01OC4xMDFWODIuMjI4ICAgQzQwMS45OTMsNTkuNTgsMzkzLjk1MSw0MC4yMTIsMzc3Ljg3LDI0LjEyNnogTTM2NS40NDgsMzE5Ljc3MWMwLDEyLjU1OS00LjQ3LDIzLjMxNC0xMy40MTUsMzIuMjY0ICAgYy04Ljk0NSw4Ljk0NS0xOS42OTgsMTMuNDExLTMyLjI2NSwxMy40MTFIODIuMjI3Yy0xMi41NjMsMC0yMy4zMTctNC40NjYtMzIuMjY0LTEzLjQxMWMtOC45NDUtOC45NDktMTMuNDE4LTE5LjcwNS0xMy40MTgtMzIuMjY0ICAgVjgyLjIyOGMwLTEyLjU2Miw0LjQ3My0yMy4zMTYsMTMuNDE4LTMyLjI2NGM4Ljk0Ny04Ljk0NiwxOS43MDEtMTMuNDE4LDMyLjI2NC0xMy40MThoMjM3LjU0MiAgIGMxMi41NjYsMCwyMy4zMTksNC40NzMsMzIuMjY1LDEzLjQxOGM4Ljk0NSw4Ljk0NywxMy40MTUsMTkuNzAxLDEzLjQxNSwzMi4yNjRWMzE5Ljc3MUwzNjUuNDQ4LDMxOS43NzF6IiBmaWxsPSIjMzMzMzMzIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                                @endif
                            </dt>
                            <dd>Ret. Encomenda</dd>
                        </dl>
                    </td>
                    <td>
                        <dl class="ui-dl">
                            <dt>
                                @if(is_array($shipment->has_return) && in_array('rguide', $shipment->has_return))
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDQzOC41MzYgNDM4LjUzNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDM4LjUzNiA0MzguNTM2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTQxNC40MSwyNC4xMjNDMzk4LjMzMyw4LjA0MiwzNzguOTYzLDAsMzU2LjMxNSwwSDgyLjIyOEM1OS41OCwwLDQwLjIxLDguMDQyLDI0LjEyNiwyNC4xMjMgICBDOC4wNDUsNDAuMjA3LDAuMDAzLDU5LjU3NiwwLjAwMyw4Mi4yMjV2Mjc0LjA4NGMwLDIyLjY0Nyw4LjA0Miw0Mi4wMTgsMjQuMTIzLDU4LjEwMmMxNi4wODQsMTYuMDg0LDM1LjQ1NCwyNC4xMjYsNTguMTAyLDI0LjEyNiAgIGgyNzQuMDg0YzIyLjY0OCwwLDQyLjAxOC04LjA0Miw1OC4wOTUtMjQuMTI2YzE2LjA4NC0xNi4wODQsMjQuMTI2LTM1LjQ1NCwyNC4xMjYtNTguMTAyVjgyLjIyNSAgIEM0MzguNTMyLDU5LjU3Niw0MzAuNDksNDAuMjA0LDQxNC40MSwyNC4xMjN6IE0zNzAuODgsMTU5LjAyNGwtMTc1LjMwNywxNzUuM2MtMy42MTUsMy42MTQtNy44OTgsNS40MjgtMTIuODUsNS40MjggICBjLTQuOTUsMC05LjIzMy0xLjgwNy0xMi44NS01LjQyMUw2Ny42NjMsMjMyLjExOGMtMy42MTYtMy42Mi01LjQyNC03Ljg5OC01LjQyNC0xMi44NDhjMC00Ljk0OSwxLjgwOS05LjIzMyw1LjQyNC0xMi44NDcgICBsMjkuMTI0LTI5LjEyNGMzLjYxNy0zLjYxNiw3Ljg5NS01LjQyNCwxMi44NDctNS40MjRjNC45NTIsMCw5LjIzNSwxLjgwOSwxMi44NTEsNS40MjRsNjAuMjQyLDYwLjI0bDEzMy4zMzQtMTMzLjMzMyAgIGMzLjYwNi0zLjYxNyw3Ljg5OC01LjQyNCwxMi44NDctNS40MjRjNC45NDUsMCw5LjIyNywxLjgwNywxMi44NDcsNS40MjRsMjkuMTI2LDI5LjEyNWMzLjYxLDMuNjE1LDUuNDIxLDcuODk4LDUuNDIxLDEyLjg0NyAgIFMzNzQuNDksMTU1LjQxMSwzNzAuODgsMTU5LjAyNHoiIGZpbGw9IiMzMzMzMzMiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                                @else
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDQwMS45OTggNDAxLjk5OCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDAxLjk5OCA0MDEuOTk4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTM3Ny44NywyNC4xMjZDMzYxLjc4Niw4LjA0MiwzNDIuNDE3LDAsMzE5Ljc2OSwwSDgyLjIyN0M1OS41NzksMCw0MC4yMTEsOC4wNDIsMjQuMTI1LDI0LjEyNiAgIEM4LjA0NCw0MC4yMTIsMC4wMDIsNTkuNTc2LDAuMDAyLDgyLjIyOHYyMzcuNTQzYzAsMjIuNjQ3LDguMDQyLDQyLjAxNCwyNC4xMjMsNTguMTAxYzE2LjA4NiwxNi4wODUsMzUuNDU0LDI0LjEyNyw1OC4xMDIsMjQuMTI3ICAgaDIzNy41NDJjMjIuNjQ4LDAsNDIuMDExLTguMDQyLDU4LjEwMi0yNC4xMjdjMTYuMDg1LTE2LjA4NywyNC4xMjYtMzUuNDUzLDI0LjEyNi01OC4xMDFWODIuMjI4ICAgQzQwMS45OTMsNTkuNTgsMzkzLjk1MSw0MC4yMTIsMzc3Ljg3LDI0LjEyNnogTTM2NS40NDgsMzE5Ljc3MWMwLDEyLjU1OS00LjQ3LDIzLjMxNC0xMy40MTUsMzIuMjY0ICAgYy04Ljk0NSw4Ljk0NS0xOS42OTgsMTMuNDExLTMyLjI2NSwxMy40MTFIODIuMjI3Yy0xMi41NjMsMC0yMy4zMTctNC40NjYtMzIuMjY0LTEzLjQxMWMtOC45NDUtOC45NDktMTMuNDE4LTE5LjcwNS0xMy40MTgtMzIuMjY0ICAgVjgyLjIyOGMwLTEyLjU2Miw0LjQ3My0yMy4zMTYsMTMuNDE4LTMyLjI2NGM4Ljk0Ny04Ljk0NiwxOS43MDEtMTMuNDE4LDMyLjI2NC0xMy40MThoMjM3LjU0MiAgIGMxMi41NjYsMCwyMy4zMTksNC40NzMsMzIuMjY1LDEzLjQxOGM4Ljk0NSw4Ljk0NywxMy40MTUsMTkuNzAxLDEzLjQxNSwzMi4yNjRWMzE5Ljc3MUwzNjUuNDQ4LDMxOS43NzF6IiBmaWxsPSIjMzMzMzMzIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                                @endif
                            </dt>
                            <dd>Ret. Guia Assinada</dd>
                        </dl>
                    </td>
                </tr>
            </table>
            @if($shipment->obs)
                <hr class="m-t-5 m-b-5"/>
                <span class="text-primary">Observações:</span><br/>
                {!! textWithUrls(nl2br($shipment->obs)) !!}<br/>
            @endif
            @if($shipment->obs_internal)
                {!! textWithUrls(nl2br($shipment->obs_internal)) !!}
            @endif

            @if($shipment->reference || $shipment->reference2 || $shipment->reference3)
                <hr class="m-t-5 m-b-5"/>
            @endif

            @if($shipment->reference)
                <div>
                    <span class="text-primary">Referência:</span> {{ $shipment->reference }}
                </div>
            @endif
            @if($shipment->reference2)
                <div>
                    <span class="text-primary">{{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Referência 3' }}:</span> {{ $shipment->reference2 }}
                </div>
            @endif
            @if($shipment->reference3)
                <div>
                    <span class="text-primary">{{ Setting::get('shipments_reference3_name') ? Setting::get('shipments_reference3_name') : 'Referência 3' }}:</span> {{ $shipment->reference3 }}
                </div>
            @endif
            @if(!empty($shipment->custom_fields))
                <div>
                    <span class="text-primary">Linhas de Rede:</span><br/>
                    @foreach(@$shipment->custom_fields as $field)
                        @if(!empty($field))
                            {{ @$field }}<br/>
                        @endif
                    @endforeach
                </div>
            @endif

            <hr/>
            <p>
                <span class="text-primary">Cliente que Paga</span>
                <br/>
                <span class="text-uppercase">{{ @$shipment->customer->code }} - {{ @$shipment->customer->name }}</span>
            </p>
            <br/>
        </div>
        <div class="footer-buttons footer-four-btn" style="margin-bottom: 0 !important;">
            <ul class="list-unstyled">
                @if(!@$shipment->status->is_final)
                    <li>
                        <a href="#" data-target="#window-incidence" data-shipment-id="{{ $shipment->id }}">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTUwNy40OTQsNDI2LjA2NkwyODIuODY0LDUzLjUzN2MtNS42NzctOS40MTUtMTUuODctMTUuMTcyLTI2Ljg2NS0xNS4xNzJjLTEwLjk5NSwwLTIxLjE4OCw1Ljc1Ni0yNi44NjUsMTUuMTcyICAgIEw0LjUwNiw0MjYuMDY2Yy01Ljg0Miw5LjY4OS02LjAxNSwyMS43NzQtMC40NTEsMzEuNjI1YzUuNTY0LDkuODUyLDE2LjAwMSwxNS45NDQsMjcuMzE1LDE1Ljk0NGg0NDkuMjU5ICAgIGMxMS4zMTQsMCwyMS43NTEtNi4wOTMsMjcuMzE1LTE1Ljk0NEM1MTMuNTA4LDQ0Ny44MzksNTEzLjMzNiw0MzUuNzU1LDUwNy40OTQsNDI2LjA2NnogTTI1Ni4xNjcsMTY3LjIyNyAgICBjMTIuOTAxLDAsMjMuODE3LDcuMjc4LDIzLjgxNywyMC4xNzhjMCwzOS4zNjMtNC42MzEsOTUuOTI5LTQuNjMxLDEzNS4yOTJjMCwxMC4yNTUtMTEuMjQ3LDE0LjU1NC0xOS4xODYsMTQuNTU0ICAgIGMtMTAuNTg0LDAtMTkuNTE2LTQuMy0xOS41MTYtMTQuNTU0YzAtMzkuMzYzLTQuNjMtOTUuOTI5LTQuNjMtMTM1LjI5MkMyMzIuMDIxLDE3NC41MDUsMjQyLjYwNSwxNjcuMjI3LDI1Ni4xNjcsMTY3LjIyN3ogICAgIE0yNTYuNDk4LDQxMS4wMThjLTE0LjU1NCwwLTI1LjQ3MS0xMS45MDgtMjUuNDcxLTI1LjQ3YzAtMTMuODkzLDEwLjkxNi0yNS40NywyNS40NzEtMjUuNDdjMTMuNTYyLDAsMjUuMTQsMTEuNTc3LDI1LjE0LDI1LjQ3ICAgIEMyODEuNjM4LDM5OS4xMSwyNzAuMDYsNDExLjAxOCwyNTYuNDk4LDQxMS4wMTh6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                        </a>
                    </li>
                    <li>
                        <a href="#" data-target="#window-delivery" data-shipment-id="{{ $shipment->id }}">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUyIDUyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MiA1MjsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIyNHB4IiBoZWlnaHQ9IjI0cHgiPgo8Zz4KCTxwYXRoIGQ9Ik0yNiwwQzExLjY2NCwwLDAsMTEuNjYzLDAsMjZzMTEuNjY0LDI2LDI2LDI2czI2LTExLjY2MywyNi0yNlM0MC4zMzYsMCwyNiwweiBNNDAuNDk1LDE3LjMyOWwtMTYsMTggICBDMjQuMTAxLDM1Ljc3MiwyMy41NTIsMzYsMjIuOTk5LDM2Yy0wLjQzOSwwLTAuODgtMC4xNDQtMS4yNDktMC40MzhsLTEwLThjLTAuODYyLTAuNjg5LTEuMDAyLTEuOTQ4LTAuMzEyLTIuODExICAgYzAuNjg5LTAuODYzLDEuOTQ5LTEuMDAzLDIuODExLTAuMzEzbDguNTE3LDYuODEzbDE0LjczOS0xNi41ODFjMC43MzItMC44MjYsMS45OTgtMC45LDIuODIzLTAuMTY2ICAgQzQxLjE1NCwxNS4yMzksNDEuMjI5LDE2LjUwMyw0MC40OTUsMTcuMzI5eiIgZmlsbD0iI0ZGRkZGRiIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>