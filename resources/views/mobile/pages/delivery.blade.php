<div class="window shipment-delivery" id="window-delivery" style="display: none;">
    <header>
        <div class="action-buttons-right">
            <ul>
                <li>
                    <a href="{{ route('mobile.scanner') }}">
                       {{-- <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ4MCA0ODAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ4MCA0ODA7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNODAsNDhIMTZDNy4xNjgsNDgsMCw1NS4xNjgsMCw2NHY2NGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWODBoNDhjOC44MzIsMCwxNi03LjE2OCwxNi0xNiAgICBDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00NjQsMzM2Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djQ4aC00OGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDY0YzguODMyLDAsMTYtNy4xNjgsMTYtMTZ2LTY0ICAgIEM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ2NCw0OGgtNjRjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg0OHY0OGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWNjQgICAgQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNODAsNDAwSDMydi00OGMwLTguODMyLTcuMTY4LTE2LTE2LTE2Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNiAgICBDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik04MCwxMTJjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2MjI0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlYxMjhDOTYsMTE5LjE2OCw4OC44MzIsMTEyLDgwLDExMnoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0xNDQsMTEyYy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djE2MGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWMTI4ICAgIEMxNjAsMTE5LjE2OCwxNTIuODMyLDExMiwxNDQsMTEyeiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTIwOCwxMTJjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2MTYwYzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlYxMjggICAgQzIyNCwxMTkuMTY4LDIxNi44MzIsMTEyLDIwOCwxMTJ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMjcyLDExMmMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNnYyMjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2VjEyOCAgICBDMjg4LDExOS4xNjgsMjgwLjgzMiwxMTIsMjcyLDExMnoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0zMzYsMTEyYy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djE2MGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWMTI4ICAgIEMzNTIsMTE5LjE2OCwzNDQuODMyLDExMiwzMzYsMTEyeiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQwMCwxMTJjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2MjI0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlYxMjggICAgQzQxNiwxMTkuMTY4LDQwOC44MzIsMTEyLDQwMCwxMTJ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMTQ0LjY0LDMzNmgtMC4zMmMtOC44MzIsMC0xNS44NCw3LjE2OC0xNS44NCwxNmMwLDguODMyLDcuMzI4LDE2LDE2LjE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTYgICAgQzE2MC42NCwzNDMuMTY4LDE1My40NzIsMzM2LDE0NC42NCwzMzZ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMjA4LjY0LDMzNmgtMC4zMmMtOC44MzIsMC0xNS44NCw3LjE2OC0xNS44NCwxNmMwLDguODMyLDcuMzI4LDE2LDE2LjE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTYgICAgQzIyNC42NCwzNDMuMTY4LDIxNy40NzIsMzM2LDIwOC42NCwzMzZ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzM2LjY0LDMzNmgtMC4zMmMtOC44MzIsMC0xNS44NCw3LjE2OC0xNS44NCwxNmMwLDguODMyLDcuMzI4LDE2LDE2LjE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTYgICAgQzM1Mi42NCwzNDMuMTY4LDM0NS40NzIsMzM2LDMzNi42NCwzMzZ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" style="height: 28px;"/>--}}
                        <img style="height: 28px;" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgd2lkdGg9IjUxMiIgY2xhc3M9IiI+PGc+PHBhdGggZD0ibTMwIDMwaDkwdi0zMGgtMTIwdjEyMGgzMHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTM5MiAwdjMwaDkwdjkwaDMwdi0xMjB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im00ODIgNDgyaC05MHYzMGgxMjB2LTEyMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zMCAzOTJoLTMwdjEyMGgxMjB2LTMwaC05MHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTYxIDYwdjE1MGgxNTB2LTkwaDMwdi0zMGgtMzB2LTMwem0xMjAgMTIwaC05MHYtOTBoOTB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im00NTEgNDUwdi0xNTBoLTYwdi0zMGgtMzB2MzBoLTkwdjMwaDMwdjMwaC0zMHYzMGgzMHY2MHptLTEyMC0xMjBoOTB2OTBoLTkwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMTUxIDI3MGg2MHYtMzBoLTE1MHYzMGg2MHYzMGgtMzB2MzBoMzB2NjBoLTMwdjMwaDMwdjMwaDE1MHYtMzBoLTMwdi0zMGgtMzB2MzBoLTYwdi0zMGgzMHYtMzBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMTIxIDEyMGgzMHYzMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zNjEgMTIwaDMwdjMwaC0zMHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTM5MSAyMTBoNjB2LTE1MGgtMTUwdjE1MGg2MHYzMGgzMHptLTYwLTMwdi05MGg5MHY5MHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTQ1MSAyNzB2LTMwYy03LjI1NzgxMiAwLTUyLjY5MTQwNiAwLTYwIDB2MzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zNjEgMzYwaDMwdjMwaC0zMHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTI0MSAzMzBoMzB2MzBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMTgxIDM2MGgzMGMwLTcuMjU3ODEyIDAtNTIuNjkxNDA2IDAtNjBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMjExIDI3MGgzMHYzMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im05MSAzMzBoLTMwdjYwaDMwYzAtNy4yNTc4MTIgMC01Mi42OTE0MDYgMC02MHptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+PHBhdGggZD0ibTYxIDQyMGgzMHYzMGgtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0yNDEgNjBoMzB2MzBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMjQxIDE4MGgzMGMwLTcuMjU3ODEyIDAtNTIuNjkxNDA2IDAtNjBoLTMwem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6I0ZGRkZGRiIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtMjcxIDI0MHYtMzBoLTMwdjYwaDEyMHYtMzB6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojRkZGRkZGIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjwvZz4gPC9zdmc+" />
                    </a>
                </li>
            </ul>
        </div>
        <div class="action-buttons-left">
            <ul>
                <li>
                    @if(Route::currentRouteName() == 'mobile.shipments.find')
                        <a href="{{ route('mobile.shipments.index') }}">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDc5Mi4wODIgNzkyLjA4MiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNzkyLjA4MiA3OTIuMDgyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGcgaWQ9Il94MzdfXzM0XyI+CgkJPGc+CgkJCTxwYXRoIGQ9Ik0zMTcuODk2LDM5Ni4wMjRsMzA0Ljc0OS0yNzYuNDY3YzI3LjM2LTI3LjM2LDI3LjM2LTcxLjY3NywwLTk5LjAzN3MtNzEuNjc3LTI3LjM2LTk5LjAzNiwwTDE2OS4xMSwzNDIuMTYxICAgICBjLTE0Ljc4MywxNC43ODMtMjEuMzAyLDM0LjUzOC0yMC4wODQsNTMuODk3Yy0xLjIxOCwxOS4zNTksNS4zMDEsMzkuMTE0LDIwLjA4NCw1My44OTdsMzU0LjUzMSwzMjEuNjA2ICAgICBjMjcuMzYsMjcuMzYsNzEuNjc3LDI3LjM2LDk5LjAzNywwczI3LjM2LTcxLjY3NywwLTk5LjAzNkwzMTcuODk2LDM5Ni4wMjR6IiBmaWxsPSIjRkZGRkZGIi8+CgkJPC9nPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                        </a>
                    @elseif(Route::currentRouteName() != 'mobile.scanner')
                    <a href="#" data-toggle="back" data-target=".shipments-delivery-list">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDc5Mi4wODIgNzkyLjA4MiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNzkyLjA4MiA3OTIuMDgyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGcgaWQ9Il94MzdfXzM0XyI+CgkJPGc+CgkJCTxwYXRoIGQ9Ik0zMTcuODk2LDM5Ni4wMjRsMzA0Ljc0OS0yNzYuNDY3YzI3LjM2LTI3LjM2LDI3LjM2LTcxLjY3NywwLTk5LjAzN3MtNzEuNjc3LTI3LjM2LTk5LjAzNiwwTDE2OS4xMSwzNDIuMTYxICAgICBjLTE0Ljc4MywxNC43ODMtMjEuMzAyLDM0LjUzOC0yMC4wODQsNTMuODk3Yy0xLjIxOCwxOS4zNTksNS4zMDEsMzkuMTE0LDIwLjA4NCw1My44OTdsMzU0LjUzMSwzMjEuNjA2ICAgICBjMjcuMzYsMjcuMzYsNzEuNjc3LDI3LjM2LDk5LjAzNywwczI3LjM2LTcxLjY3NywwLTk5LjAzNkwzMTcuODk2LDM5Ni4wMjR6IiBmaWxsPSIjRkZGRkZGIi8+CgkJPC9nPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                    </a>
                    @else
                    <li>
                        <a href="{{ route('mobile.scanner') }}">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDc5Mi4wODIgNzkyLjA4MiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNzkyLjA4MiA3OTIuMDgyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGcgaWQ9Il94MzdfXzM0XyI+CgkJPGc+CgkJCTxwYXRoIGQ9Ik0zMTcuODk2LDM5Ni4wMjRsMzA0Ljc0OS0yNzYuNDY3YzI3LjM2LTI3LjM2LDI3LjM2LTcxLjY3NywwLTk5LjAzN3MtNzEuNjc3LTI3LjM2LTk5LjAzNiwwTDE2OS4xMSwzNDIuMTYxICAgICBjLTE0Ljc4MywxNC43ODMtMjEuMzAyLDM0LjUzOC0yMC4wODQsNTMuODk3Yy0xLjIxOCwxOS4zNTksNS4zMDEsMzkuMTE0LDIwLjA4NCw1My44OTdsMzU0LjUzMSwzMjEuNjA2ICAgICBjMjcuMzYsMjcuMzYsNzEuNjc3LDI3LjM2LDk5LjAzNywwczI3LjM2LTcxLjY3NywwLTk5LjAzNkwzMTcuODk2LDM5Ni4wMjR6IiBmaWxsPSIjRkZGRkZGIi8+CgkJPC9nPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                        </a>
                    </li>
                    @endif
                </li>
            </ul>
        </div>
        @include('mobile.partials.logo')
    </header>
    {{ Form::open(['route' => 'mobile.status.store', 'method' => 'POST', 'class' => 'ajax-form form-delivery', 'files' => true]) }}
    @if(Setting::get('app_mode') == 'courier')
        {{ Form::text('receiver', null, ['data-clear-btn' => 'true', 'placeholder' => 'Nome Receptor', Setting::get('mobile_app_receiver_required') ? 'required' : '', 'style' => 'float:left; width: 80%']) }}
        {{ Form::text('wainting_time', null, ['data-clear-btn' => 'true', 'placeholder' => 'T. Espera (min)', 'style' => 'width: 90px; position: absolute; top: 0; right: 0; border-left: 1px solid #999']) }}
    @else
        {{ Form::text('receiver', null, ['data-clear-btn' => 'true', 'placeholder' => 'Nome Receptor', Setting::get('mobile_app_receiver_required') ? 'required' : '']) }}
    @endif


    {{ Form::textarea('obs', null, ['data-clear-btn' => 'true','placeholder' => 'Observações...', 'rows' => 2]) }}

    {{--<button id="obs_barcode" style="display: none"><i class="fa fa-barcode"></i></button>--}}
    <div style="{{ Auth::user()->id == 1 ? '' : 'display: none;' }}">
        {{ Form::hidden('signature') }}
        {{ Form::hidden('status_id', null, ['required']) }}
        {{ Form::hidden('id') }}
        {{ Form::hidden('latitude') }}
        {{ Form::hidden('longitude') }}
        {{ Form::hidden('has_return', 0) }}
        {{ Form::file('attachment') }}
    </div>

    <div id="signatureparent">
        <div id="signature" class="signature"></div>
    </div>

    <div class="footer-buttons footer-four-btn">
        <ul class="list-unstyled">
            <li>
                <a href="#" class="upload-photo">
                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDEwMCAxMDA7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNNTAsNDBjLTguMjg1LDAtMTUsNi43MTgtMTUsMTVjMCw4LjI4NSw2LjcxNSwxNSwxNSwxNWM4LjI4MywwLDE1LTYuNzE1LDE1LTE1ICAgIEM2NSw0Ni43MTgsNTguMjgzLDQwLDUwLDQweiBNOTAsMjVINzhjLTEuNjUsMC0zLjQyOC0xLjI4LTMuOTQ5LTIuODQ2bC0zLjEwMi05LjMwOUM3MC40MjYsMTEuMjgsNjguNjUsMTAsNjcsMTBIMzMgICAgYy0xLjY1LDAtMy40MjgsMS4yOC0zLjk0OSwyLjg0NmwtMy4xMDIsOS4zMDlDMjUuNDI2LDIzLjcyLDIzLjY1LDI1LDIyLDI1SDEwQzQuNSwyNSwwLDI5LjUsMCwzNXY0NWMwLDUuNSw0LjUsMTAsMTAsMTBoODAgICAgYzUuNSwwLDEwLTQuNSwxMC0xMFYzNUMxMDAsMjkuNSw5NS41LDI1LDkwLDI1eiBNNTAsODBjLTEzLjgwNywwLTI1LTExLjE5My0yNS0yNWMwLTEzLjgwNiwxMS4xOTMtMjUsMjUtMjUgICAgYzEzLjgwNSwwLDI1LDExLjE5NCwyNSwyNUM3NSw2OC44MDcsNjMuODA1LDgwLDUwLDgweiBNODYuNSw0MS45OTNjLTEuOTMyLDAtMy41LTEuNTY2LTMuNS0zLjVjMC0xLjkzMiwxLjU2OC0zLjUsMy41LTMuNSAgICBjMS45MzQsMCwzLjUsMS41NjgsMy41LDMuNUM5MCw0MC40MjcsODguNDMzLDQxLjk5Myw4Ni41LDQxLjk5M3oiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                </a>
            </li>
            <li>
                <a href="#" class="update-status-btn" data-status-id="5" data-toggle="ajax-form">
                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNNDM3LjAxOSw3NC45OEMzODguNjY3LDI2LjYyOSwzMjQuMzgsMCwyNTYsMEMxODcuNjE5LDAsMTIzLjMzMiwyNi42MjksNzQuOTgsNzQuOThDMjYuNjI5LDEyMy4zMzIsMCwxODcuNjIsMCwyNTYgICAgczI2LjYyOSwxMzIuNjY3LDc0Ljk4LDE4MS4wMTlDMTIzLjMzMiw0ODUuMzcxLDE4Ny42Miw1MTIsMjU2LDUxMnMxMzIuNjY3LTI2LjYyOSwxODEuMDE5LTc0Ljk4ICAgIEM0ODUuMzcxLDM4OC42NjcsNTEyLDMyNC4zOCw1MTIsMjU2UzQ4NS4zNzEsMTIzLjMzMyw0MzcuMDE5LDc0Ljk4eiBNMzc4LjMwNiwxOTUuMDczTDIzNS4yNDEsMzM4LjEzOSAgICBjLTIuOTI5LDIuOTI5LTYuNzY4LDQuMzkzLTEwLjYwNiw0LjM5M2MtMy44MzksMC03LjY3OC0xLjQ2NC0xMC42MDctNC4zOTNsLTgwLjMzNC04MC4zMzNjLTUuODU4LTUuODU3LTUuODU4LTE1LjM1NCwwLTIxLjIxMyAgICBjNS44NTctNS44NTgsMTUuMzU1LTUuODU4LDIxLjIxMywwbDY5LjcyOCw2OS43MjdsMTMyLjQ1OC0xMzIuNDZjNS44NTctNS44NTgsMTUuMzU1LTUuODU4LDIxLjIxMywwICAgIEMzODQuMTY0LDE3OS43MTgsMzg0LjE2NCwxODkuMjE1LDM3OC4zMDYsMTk1LjA3M3oiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                </a>
            </li>
            <li>
                <a href="#" class="update-status-btn" data-status-id="12" data-toggle="ajax-form">
                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDMxNC4wMTUgMzE0LjAxNSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzE0LjAxNSAzMTQuMDE1OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGcgaWQ9Il94MzRfMzcuX1Byb2dyZXNzIj4KCQk8Zz4KCQkJPHBhdGggZD0iTTE1Ny4wMDcsMEM3MC4yOTEsMCwwLDcwLjI4OSwwLDE1Ny4wMDdjMCw4Ni43MTIsNzAuMjksMTU3LjAwNywxNTcuMDA3LDE1Ny4wMDcgICAgIGM4Ni43MDksMCwxNTcuMDA3LTcwLjI5NSwxNTcuMDA3LTE1Ny4wMDdDMzE0LjAxNCw3MC4yODksMjQzLjcxNiwwLDE1Ny4wMDcsMHogTTMxLjQwMywxNTcuMDE1ICAgICBjMC02OS4zNzMsNTYuMjI4LTEyNS42MTMsMTI1LjYwNC0xMjUuNjEzVjI4Mi42MkM4Ny42MzEsMjgyLjYyLDMxLjQwMywyMjYuMzgsMzEuNDAzLDE1Ny4wMTV6IiBmaWxsPSIjRkZGRkZGIi8+CgkJPC9nPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                </a>
            </li>
            <li>
                <a href="#" class="reset-signature">
                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDM2MCAzNjAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM2MCAzNjA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzQ4Ljk5NCwxMDIuOTQ2TDI1MC4wNCwzLjk5M2MtNS4zMjMtNS4zMjMtMTMuOTU0LTUuMzI0LTE5LjI3NywwbC0xNTMuNywxNTMuNzAxbDExOC4yMywxMTguMjNsMTUzLjcwMS0xNTMuNyAgICBDMzU0LjMxNywxMTYuOTAyLDM1NC4zMTcsMTA4LjI3MSwzNDguOTk0LDEwMi45NDZ6IiBmaWxsPSIjRkZGRkZGIi8+CgkJPHBhdGggZD0iTTUyLjY0NiwxODIuMTFsLTQxLjY0LDQxLjY0Yy01LjMyNCw1LjMyMi01LjMyNCwxMy45NTMsMCwxOS4yNzVsOTguOTU0LDk4Ljk1N2M1LjMyMyw1LjMyMiwxMy45NTQsNS4zMiwxOS4yNzcsMCAgICBsNDEuNjM5LTQxLjY0MUw1Mi42NDYsMTgyLjExeiIgZmlsbD0iI0ZGRkZGRiIvPgoJCTxwb2x5Z29uIHBvaW50cz0iMTUwLjEzMywzNjAgMzQxLjc2NywzNjAgMzQxLjc2NywzMzEuOTQ5IDE4Mi44MDYsMzMxLjk0OSAgICIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                </a>
            </li>
        </ul>
    </div>

    @if(Setting::get('mobile_app_autoreturn'))
    <div class="confirm-modal return-modal" style="display: none;">
        <h4 class="bold">Este envio tem retorno.</h4>
        <p>Pretende gerar o retorno do mesmo?</p>
        <div style="padding: 30px 50px 50px 50px;">
            <div style="width: 40%;float: left;margin-right: 40px;">
                <label>Nº Volumes</label>
                <input type="text" name="return_volumes" class="text-center" data-placeholder="Nº Volumes"/>
            </div>
            <div style="width: 40%;float: left;">
                <label>Peso Total</label>
                <input type="text" name="return_weight" class="text-center" data-placeholder="Peso"/>
            </div>
            <div class="clearfix"></div>
        </div>
        {{ Form::hidden('create_return', '-1') }}
        <button class="btn btn-default create-return-yes">Gerar Retorno</button>
        {{--<button class="btn btn-default create-return-no">Não Gerar</button>--}}
    </div>
    @endif

    {{ Form::close() }}
</div>

<div class="confirm-modal form-required-error" style="display: none;">
    <h4 style="color: #ff0000">Há campos não preenchidos</h4>
    <p>Preencha o campo "<span class="fieldname bold"></span>" antes de gravar.</p>
    <br/>
    <button class="btn btn-default error-close">OK</button>
</div>