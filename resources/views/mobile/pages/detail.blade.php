<div class="window shipment-detail" id="window-detail-{{ $shipment->id }}" style="{{ Route::currentRouteName() == 'mobile.shipments.find' ? '' : 'display: none' }}; z-index: 999999997;">
    <header>
        <div class="action-buttons-right">
            <ul>
                <li>
                    <a href="{{ route('mobile.scanner') }}">
                        {{--<img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ4MCA0ODAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ4MCA0ODA7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNODAsNDhIMTZDNy4xNjgsNDgsMCw1NS4xNjgsMCw2NHY2NGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWODBoNDhjOC44MzIsMCwxNi03LjE2OCwxNi0xNiAgICBDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00NjQsMzM2Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djQ4aC00OGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDY0YzguODMyLDAsMTYtNy4xNjgsMTYtMTZ2LTY0ICAgIEM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ2NCw0OGgtNjRjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg0OHY0OGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWNjQgICAgQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNODAsNDAwSDMydi00OGMwLTguODMyLTcuMTY4LTE2LTE2LTE2Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNiAgICBDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik04MCwxMTJjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2MjI0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlYxMjhDOTYsMTE5LjE2OCw4OC44MzIsMTEyLDgwLDExMnoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0xNDQsMTEyYy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djE2MGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWMTI4ICAgIEMxNjAsMTE5LjE2OCwxNTIuODMyLDExMiwxNDQsMTEyeiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTIwOCwxMTJjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2MTYwYzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlYxMjggICAgQzIyNCwxMTkuMTY4LDIxNi44MzIsMTEyLDIwOCwxMTJ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMjcyLDExMmMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNnYyMjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2VjEyOCAgICBDMjg4LDExOS4xNjgsMjgwLjgzMiwxMTIsMjcyLDExMnoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0zMzYsMTEyYy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2djE2MGMwLDguODMyLDcuMTY4LDE2LDE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTZWMTI4ICAgIEMzNTIsMTE5LjE2OCwzNDQuODMyLDExMiwzMzYsMTEyeiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQwMCwxMTJjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2MjI0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlYxMjggICAgQzQxNiwxMTkuMTY4LDQwOC44MzIsMTEyLDQwMCwxMTJ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMTQ0LjY0LDMzNmgtMC4zMmMtOC44MzIsMC0xNS44NCw3LjE2OC0xNS44NCwxNmMwLDguODMyLDcuMzI4LDE2LDE2LjE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTYgICAgQzE2MC42NCwzNDMuMTY4LDE1My40NzIsMzM2LDE0NC42NCwzMzZ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMjA4LjY0LDMzNmgtMC4zMmMtOC44MzIsMC0xNS44NCw3LjE2OC0xNS44NCwxNmMwLDguODMyLDcuMzI4LDE2LDE2LjE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTYgICAgQzIyNC42NCwzNDMuMTY4LDIxNy40NzIsMzM2LDIwOC42NCwzMzZ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzM2LjY0LDMzNmgtMC4zMmMtOC44MzIsMC0xNS44NCw3LjE2OC0xNS44NCwxNmMwLDguODMyLDcuMzI4LDE2LDE2LjE2LDE2YzguODMyLDAsMTYtNy4xNjgsMTYtMTYgICAgQzM1Mi42NCwzNDMuMTY4LDM0NS40NzIsMzM2LDMzNi42NCwzMzZ6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" style="height: 28px;"/>--}}
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
                {{ $shipment->is_collection ? 'background: #fff8bf' : '' }}
            ">
            <div style="border-bottom: 1px solid #ccc;
    margin: -15px -15px 10px;
    padding: 10px 15px;
    background: #fff;
    box-shadow: 0 -2px 5px #333;">
                <table class="w-100">
                    <tr>
                        <td>
                            <h4 class="m-t-0 m-b-10 tracking-number">{{ $shipment->tracking_code }}</h4>
                            <dl class="ui-dl dl-status">
                                <dt>Serviço</dt>
                                <dd>{{ @$shipment->service->display_code }}</dd>
                                <dt>Data</dt>
                                <dd>
                                    {{ @$shipment->date }}

                                    @if($shipment->start_hour)
                                        {{ $shipment->start_hour }}

                                        @if($shipment->end_hour)
                                            -{{ $shipment->end_hour }}
                                        @endif
                                    @endif
                                </dd>
                            </dl>
                        </td>
                        <td>
                            <span class="badge-status" style="background: {{ @$shipment->status->color }};">
                                {{ @$shipment->status->name }}
                            </span>
                            <div class="clearfix"></div>
                            <h4 class="m-t-0 details-total-charge">
                                <small>Total a Cobrar</small><br/>
                                @if($shipment->cod == 'D')
                                    {{ money($shipment->charge_price + $shipment->billing_subtotal, Setting::get('app_currency')) }}
                                @else
                                    {{ money($shipment->charge_price, Setting::get('app_currency')) }}
                                @endif
                            </h4>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="clearfix"></div>

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
                <div style="border-bottom: 1px solid #ccc;
    margin: -15px -15px 10px;
    padding: 10px 15px;
">
                    <p>
                        <div class="detail-block-header">
                            <a href="{{ \App\Http\Controllers\Mobile\BaseController::getGoogleMapsUrl($shipment, false) }}" class="detail-marker pull-right" target="_blank">
                                <img style="width: 16px" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiI+PGc+PGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMjU2LDBDMTUzLjc1NSwwLDcwLjU3Myw4My4xODIsNzAuNTczLDE4NS40MjZjMCwxMjYuODg4LDE2NS45MzksMzEzLjE2NywxNzMuMDA0LDMyMS4wMzUgICAgYzYuNjM2LDcuMzkxLDE4LjIyMiw3LjM3OCwyNC44NDYsMGM3LjA2NS03Ljg2OCwxNzMuMDA0LTE5NC4xNDcsMTczLjAwNC0zMjEuMDM1QzQ0MS40MjUsODMuMTgyLDM1OC4yNDQsMCwyNTYsMHogTTI1NiwyNzguNzE5ICAgIGMtNTEuNDQyLDAtOTMuMjkyLTQxLjg1MS05My4yOTItOTMuMjkzUzIwNC41NTksOTIuMTM0LDI1Niw5Mi4xMzRzOTMuMjkxLDQxLjg1MSw5My4yOTEsOTMuMjkzUzMwNy40NDEsMjc4LjcxOSwyNTYsMjc4LjcxOXoiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiIHN0eWxlPSJmaWxsOiNGRkZGRkYiPjwvcGF0aD4KCTwvZz4KPC9nPjwvZz4gPC9zdmc+" />
                            </a>
                            <a href="tel:{{ $shipment->sender_phone }}" class="detail-phone pull-right">
                                <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU3OC4xMDYgNTc4LjEwNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTc4LjEwNiA1NzguMTA2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTU3Ny44Myw0NTYuMTI4YzEuMjI1LDkuMzg1LTEuNjM1LDE3LjU0NS04LjU2OCwyNC40OGwtODEuMzk2LDgwLjc4MSAgICBjLTMuNjcyLDQuMDgtOC40NjUsNy41NTEtMTQuMzgxLDEwLjQwNGMtNS45MTYsMi44NTctMTEuNzI5LDQuNjkzLTE3LjQzOSw1LjUwOGMtMC40MDgsMC0xLjYzNSwwLjEwNS0zLjY3NiwwLjMwOSAgICBjLTIuMDM3LDAuMjAzLTQuNjg5LDAuMzA3LTcuOTUzLDAuMzA3Yy03Ljc1NCwwLTIwLjMwMS0xLjMyNi0zNy42NDEtMy45NzlzLTM4LjU1NS05LjE4Mi02My42NDUtMTkuNTg0ICAgIGMtMjUuMDk2LTEwLjQwNC01My41NTMtMjYuMDEyLTg1LjM3Ni00Ni44MThjLTMxLjgyMy0yMC44MDUtNjUuNjg4LTQ5LjM2Ny0xMDEuNTkyLTg1LjY4ICAgIGMtMjguNTYtMjguMTUyLTUyLjIyNC01NS4wOC03MC45OTItODAuNzgzYy0xOC43NjgtMjUuNzA1LTMzLjg2NC00OS40NzEtNDUuMjg4LTcxLjI5OSAgICBjLTExLjQyNS0yMS44MjgtMTkuOTkzLTQxLjYxNi0yNS43MDUtNTkuMzY0UzQuNTksMTc3LjM2MiwyLjU1LDE2NC41MXMtMi44NTYtMjIuOTUtMi40NDgtMzAuMjk0ICAgIGMwLjQwOC03LjM0NCwwLjYxMi0xMS40MjQsMC42MTItMTIuMjRjMC44MTYtNS43MTIsMi42NTItMTEuNTI2LDUuNTA4LTE3LjQ0MnM2LjMyNC0xMC43MSwxMC40MDQtMTQuMzgyTDk4LjAyMiw4Ljc1NiAgICBjNS43MTItNS43MTIsMTIuMjQtOC41NjgsMTkuNTg0LTguNTY4YzUuMzA0LDAsOS45OTYsMS41MywxNC4wNzYsNC41OXM3LjU0OCw2LjgzNCwxMC40MDQsMTEuMzIybDY1LjQ4NCwxMjQuMjM2ICAgIGMzLjY3Miw2LjUyOCw0LjY5MiwxMy42NjgsMy4wNiwyMS40MmMtMS42MzIsNy43NTItNS4xLDE0LjI4LTEwLjQwNCwxOS41ODRsLTI5Ljk4OCwyOS45ODhjLTAuODE2LDAuODE2LTEuNTMsMi4xNDItMi4xNDIsMy45NzggICAgcy0wLjkxOCwzLjM2Ni0wLjkxOCw0LjU5YzEuNjMyLDguNTY4LDUuMzA0LDE4LjM2LDExLjAxNiwyOS4zNzZjNC44OTYsOS43OTIsMTIuNDQ0LDIxLjcyNiwyMi42NDQsMzUuODAyICAgIHMyNC42ODQsMzAuMjkzLDQzLjQ1Miw0OC42NTNjMTguMzYsMTguNzcsMzQuNjgsMzMuMzU0LDQ4Ljk2LDQzLjc2YzE0LjI3NywxMC40LDI2LjIxNSwxOC4wNTMsMzUuODAzLDIyLjk0OSAgICBjOS41ODgsNC44OTYsMTYuOTMyLDcuODU0LDIyLjAzMSw4Ljg3MWw3LjY0OCwxLjUzMWMwLjgxNiwwLDIuMTQ1LTAuMzA3LDMuOTc5LTAuOTE4YzEuODM2LTAuNjEzLDMuMTYyLTEuMzI2LDMuOTc5LTIuMTQzICAgIGwzNC44ODMtMzUuNDk2YzcuMzQ4LTYuNTI3LDE1LjkxMi05Ljc5MSwyNS43MDUtOS43OTFjNi45MzgsMCwxMi40NDMsMS4yMjMsMTYuNTIzLDMuNjcyaDAuNjExbDExOC4xMTUsNjkuNzY4ICAgIEM1NzEuMDk4LDQ0MS4yMzgsNTc2LjE5Nyw0NDcuOTY4LDU3Ny44Myw0NTYuMTI4eiIgZmlsbD0iIzMzMzMzMyIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /> {{ $shipment->sender_phone }}
                            </a>
                            <div class="text-primary pull-left">
                                @if(Setting::get('app_mode') == 'courier')
                                    RECOLHA
                                @else
                                    REMETENTE
                                @endif
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        @if(Setting::get('mobile_app_details_full_sender'))
                            @if($shipment->sender_attn)
                                A/C: {{ $shipment->sender_attn }}<br/>
                            @endif
                            <span class="text-uppercase">
                                <b>{{ $shipment->sender_name }}</b>
                                <br/>
                                {{ $shipment->sender_address }}<br/>
                                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                            </span>
                        @else
                            <b class="text-uppercase">{{ $shipment->sender_name }}</b>
                        @endif
                    </p>
                </div>
                <div style="border-bottom: 1px solid #ccc;margin: -15px -15px 10px;padding: 10px 15px;">
                    <div class="detail-block-header">
                        <a href="{{ \App\Http\Controllers\Mobile\BaseController::getGoogleMapsUrl($shipment) }}" class="detail-marker pull-right" target="_blank">
                            <img style="width: 16px" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiI+PGc+PGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMjU2LDBDMTUzLjc1NSwwLDcwLjU3Myw4My4xODIsNzAuNTczLDE4NS40MjZjMCwxMjYuODg4LDE2NS45MzksMzEzLjE2NywxNzMuMDA0LDMyMS4wMzUgICAgYzYuNjM2LDcuMzkxLDE4LjIyMiw3LjM3OCwyNC44NDYsMGM3LjA2NS03Ljg2OCwxNzMuMDA0LTE5NC4xNDcsMTczLjAwNC0zMjEuMDM1QzQ0MS40MjUsODMuMTgyLDM1OC4yNDQsMCwyNTYsMHogTTI1NiwyNzguNzE5ICAgIGMtNTEuNDQyLDAtOTMuMjkyLTQxLjg1MS05My4yOTItOTMuMjkzUzIwNC41NTksOTIuMTM0LDI1Niw5Mi4xMzRzOTMuMjkxLDQxLjg1MSw5My4yOTEsOTMuMjkzUzMwNy40NDEsMjc4LjcxOSwyNTYsMjc4LjcxOXoiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiIHN0eWxlPSJmaWxsOiNGRkZGRkYiPjwvcGF0aD4KCTwvZz4KPC9nPjwvZz4gPC9zdmc+" />
                        </a>
                        <a href="tel:{{ $shipment->recipient_phone }}" class="detail-phone pull-right">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU3OC4xMDYgNTc4LjEwNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTc4LjEwNiA1NzguMTA2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTU3Ny44Myw0NTYuMTI4YzEuMjI1LDkuMzg1LTEuNjM1LDE3LjU0NS04LjU2OCwyNC40OGwtODEuMzk2LDgwLjc4MSAgICBjLTMuNjcyLDQuMDgtOC40NjUsNy41NTEtMTQuMzgxLDEwLjQwNGMtNS45MTYsMi44NTctMTEuNzI5LDQuNjkzLTE3LjQzOSw1LjUwOGMtMC40MDgsMC0xLjYzNSwwLjEwNS0zLjY3NiwwLjMwOSAgICBjLTIuMDM3LDAuMjAzLTQuNjg5LDAuMzA3LTcuOTUzLDAuMzA3Yy03Ljc1NCwwLTIwLjMwMS0xLjMyNi0zNy42NDEtMy45NzlzLTM4LjU1NS05LjE4Mi02My42NDUtMTkuNTg0ICAgIGMtMjUuMDk2LTEwLjQwNC01My41NTMtMjYuMDEyLTg1LjM3Ni00Ni44MThjLTMxLjgyMy0yMC44MDUtNjUuNjg4LTQ5LjM2Ny0xMDEuNTkyLTg1LjY4ICAgIGMtMjguNTYtMjguMTUyLTUyLjIyNC01NS4wOC03MC45OTItODAuNzgzYy0xOC43NjgtMjUuNzA1LTMzLjg2NC00OS40NzEtNDUuMjg4LTcxLjI5OSAgICBjLTExLjQyNS0yMS44MjgtMTkuOTkzLTQxLjYxNi0yNS43MDUtNTkuMzY0UzQuNTksMTc3LjM2MiwyLjU1LDE2NC41MXMtMi44NTYtMjIuOTUtMi40NDgtMzAuMjk0ICAgIGMwLjQwOC03LjM0NCwwLjYxMi0xMS40MjQsMC42MTItMTIuMjRjMC44MTYtNS43MTIsMi42NTItMTEuNTI2LDUuNTA4LTE3LjQ0MnM2LjMyNC0xMC43MSwxMC40MDQtMTQuMzgyTDk4LjAyMiw4Ljc1NiAgICBjNS43MTItNS43MTIsMTIuMjQtOC41NjgsMTkuNTg0LTguNTY4YzUuMzA0LDAsOS45OTYsMS41MywxNC4wNzYsNC41OXM3LjU0OCw2LjgzNCwxMC40MDQsMTEuMzIybDY1LjQ4NCwxMjQuMjM2ICAgIGMzLjY3Miw2LjUyOCw0LjY5MiwxMy42NjgsMy4wNiwyMS40MmMtMS42MzIsNy43NTItNS4xLDE0LjI4LTEwLjQwNCwxOS41ODRsLTI5Ljk4OCwyOS45ODhjLTAuODE2LDAuODE2LTEuNTMsMi4xNDItMi4xNDIsMy45NzggICAgcy0wLjkxOCwzLjM2Ni0wLjkxOCw0LjU5YzEuNjMyLDguNTY4LDUuMzA0LDE4LjM2LDExLjAxNiwyOS4zNzZjNC44OTYsOS43OTIsMTIuNDQ0LDIxLjcyNiwyMi42NDQsMzUuODAyICAgIHMyNC42ODQsMzAuMjkzLDQzLjQ1Miw0OC42NTNjMTguMzYsMTguNzcsMzQuNjgsMzMuMzU0LDQ4Ljk2LDQzLjc2YzE0LjI3NywxMC40LDI2LjIxNSwxOC4wNTMsMzUuODAzLDIyLjk0OSAgICBjOS41ODgsNC44OTYsMTYuOTMyLDcuODU0LDIyLjAzMSw4Ljg3MWw3LjY0OCwxLjUzMWMwLjgxNiwwLDIuMTQ1LTAuMzA3LDMuOTc5LTAuOTE4YzEuODM2LTAuNjEzLDMuMTYyLTEuMzI2LDMuOTc5LTIuMTQzICAgIGwzNC44ODMtMzUuNDk2YzcuMzQ4LTYuNTI3LDE1LjkxMi05Ljc5MSwyNS43MDUtOS43OTFjNi45MzgsMCwxMi40NDMsMS4yMjMsMTYuNTIzLDMuNjcyaDAuNjExbDExOC4xMTUsNjkuNzY4ICAgIEM1NzEuMDk4LDQ0MS4yMzgsNTc2LjE5Nyw0NDcuOTY4LDU3Ny44Myw0NTYuMTI4eiIgZmlsbD0iIzMzMzMzMyIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /> {{ $shipment->recipient_phone }}
                        </a>

                        <div class="text-primary pull-left">DESTINATÁRIO</div>
                        <div class="clearfix"></div>
                    </div>
                    <p>
                        <span class="text-uppercase">
                            {!! $shipment->recipient_attn ?  'A/C: ' . $shipment->recipient_attn.'<br/>' : '' !!}
                            <b>{{ $shipment->recipient_name }}</b>
                            <br/>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                        </span>
                    </p>
                </div>
            @endif

            <div style="border-bottom: 1px solid #ccc; margin: -15px -15px 10px;padding: 10px 15px;">
                <table class="w-100 text-center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="w-25">
                            <div class="detail-box-square">
                            <p class="text-primary">Reembolso</p>
                            {{ money($shipment->charge_price, Setting::get('app_currency')) }}
                            </div>
                        </td>
                        @if($shipment->payment_at_recipient)
                            <td class="w-25">
                                <div class="detail-box-square">
                                <p class="text-primary">Portes</p>
                                {{ money($shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}
                                </div>
                            </td>
                        @else
                            <td class="w-25">
                                <div class="detail-box-square">
                                <p class="text-primary">Portes</p>
                                {{ money(0, Setting::get('app_currency')) }}
                                </div>
                            </td>
                        @endif
                        <td class="w-25">
                            <div class="detail-box-square">
                            <p class="text-primary">Volumes</p>
                            {{ $shipment->volumes }}
                            </div>
                        </td>
                        <td class="w-25">
                            <div class="detail-box-square">
                            <p class="text-primary">Peso (Kg)</p>
                            {{ $shipment->weight }}
                            </div>
                        </td>
                        {{--<td class="w-20">
                            <div class="detail-box-square">
                            <p class="text-primary">Dist. (Km)</p>
                            {{ $shipment->kms ? $shipment->kms : 0 }}
                            </div>
                        </td>--}}
                        {{--<td>
                        </td>
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
                        </td>--}}
                    </tr>
                </table>
            </div>

            @if(is_array($shipment->has_return) && (in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return)))
            <div style="border-bottom: 1px solid #ccc;margin: -15px -15px 10px;padding: 10px 15px;">
                <p class="text-primary">SERVIÇOS ESPECIAIS</p>
                <table class="w-100">
                    <tr>
                        @if(is_array($shipment->has_return) && in_array('rpack', $shipment->has_return))
                        <td>
                            <p><img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDQzOC41MzYgNDM4LjUzNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDM4LjUzNiA0MzguNTM2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTQxNC40MSwyNC4xMjNDMzk4LjMzMyw4LjA0MiwzNzguOTYzLDAsMzU2LjMxNSwwSDgyLjIyOEM1OS41OCwwLDQwLjIxLDguMDQyLDI0LjEyNiwyNC4xMjMgICBDOC4wNDUsNDAuMjA3LDAuMDAzLDU5LjU3NiwwLjAwMyw4Mi4yMjV2Mjc0LjA4NGMwLDIyLjY0Nyw4LjA0Miw0Mi4wMTgsMjQuMTIzLDU4LjEwMmMxNi4wODQsMTYuMDg0LDM1LjQ1NCwyNC4xMjYsNTguMTAyLDI0LjEyNiAgIGgyNzQuMDg0YzIyLjY0OCwwLDQyLjAxOC04LjA0Miw1OC4wOTUtMjQuMTI2YzE2LjA4NC0xNi4wODQsMjQuMTI2LTM1LjQ1NCwyNC4xMjYtNTguMTAyVjgyLjIyNSAgIEM0MzguNTMyLDU5LjU3Niw0MzAuNDksNDAuMjA0LDQxNC40MSwyNC4xMjN6IE0zNzAuODgsMTU5LjAyNGwtMTc1LjMwNywxNzUuM2MtMy42MTUsMy42MTQtNy44OTgsNS40MjgtMTIuODUsNS40MjggICBjLTQuOTUsMC05LjIzMy0xLjgwNy0xMi44NS01LjQyMUw2Ny42NjMsMjMyLjExOGMtMy42MTYtMy42Mi01LjQyNC03Ljg5OC01LjQyNC0xMi44NDhjMC00Ljk0OSwxLjgwOS05LjIzMyw1LjQyNC0xMi44NDcgICBsMjkuMTI0LTI5LjEyNGMzLjYxNy0zLjYxNiw3Ljg5NS01LjQyNCwxMi44NDctNS40MjRjNC45NTIsMCw5LjIzNSwxLjgwOSwxMi44NTEsNS40MjRsNjAuMjQyLDYwLjI0bDEzMy4zMzQtMTMzLjMzMyAgIGMzLjYwNi0zLjYxNyw3Ljg5OC01LjQyNCwxMi44NDctNS40MjRjNC45NDUsMCw5LjIyNywxLjgwNywxMi44NDcsNS40MjRsMjkuMTI2LDI5LjEyNWMzLjYxLDMuNjE1LDUuNDIxLDcuODk4LDUuNDIxLDEyLjg0NyAgIFMzNzQuNDksMTU1LjQxMSwzNzAuODgsMTU5LjAyNHoiIGZpbGw9IiMzMzMzMzMiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" /> Ret. Encomenda</p>
                        </td>
                        @endif
                        @if(is_array($shipment->has_return) && in_array('rguide', $shipment->has_return))
                            <td>
                                <p><img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDQzOC41MzYgNDM4LjUzNiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDM4LjUzNiA0MzguNTM2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTQxNC40MSwyNC4xMjNDMzk4LjMzMyw4LjA0MiwzNzguOTYzLDAsMzU2LjMxNSwwSDgyLjIyOEM1OS41OCwwLDQwLjIxLDguMDQyLDI0LjEyNiwyNC4xMjMgICBDOC4wNDUsNDAuMjA3LDAuMDAzLDU5LjU3NiwwLjAwMyw4Mi4yMjV2Mjc0LjA4NGMwLDIyLjY0Nyw4LjA0Miw0Mi4wMTgsMjQuMTIzLDU4LjEwMmMxNi4wODQsMTYuMDg0LDM1LjQ1NCwyNC4xMjYsNTguMTAyLDI0LjEyNiAgIGgyNzQuMDg0YzIyLjY0OCwwLDQyLjAxOC04LjA0Miw1OC4wOTUtMjQuMTI2YzE2LjA4NC0xNi4wODQsMjQuMTI2LTM1LjQ1NCwyNC4xMjYtNTguMTAyVjgyLjIyNSAgIEM0MzguNTMyLDU5LjU3Niw0MzAuNDksNDAuMjA0LDQxNC40MSwyNC4xMjN6IE0zNzAuODgsMTU5LjAyNGwtMTc1LjMwNywxNzUuM2MtMy42MTUsMy42MTQtNy44OTgsNS40MjgtMTIuODUsNS40MjggICBjLTQuOTUsMC05LjIzMy0xLjgwNy0xMi44NS01LjQyMUw2Ny42NjMsMjMyLjExOGMtMy42MTYtMy42Mi01LjQyNC03Ljg5OC01LjQyNC0xMi44NDhjMC00Ljk0OSwxLjgwOS05LjIzMyw1LjQyNC0xMi44NDcgICBsMjkuMTI0LTI5LjEyNGMzLjYxNy0zLjYxNiw3Ljg5NS01LjQyNCwxMi44NDctNS40MjRjNC45NTIsMCw5LjIzNSwxLjgwOSwxMi44NTEsNS40MjRsNjAuMjQyLDYwLjI0bDEzMy4zMzQtMTMzLjMzMyAgIGMzLjYwNi0zLjYxNyw3Ljg5OC01LjQyNCwxMi44NDctNS40MjRjNC45NDUsMCw5LjIyNywxLjgwNywxMi44NDcsNS40MjRsMjkuMTI2LDI5LjEyNWMzLjYxLDMuNjE1LDUuNDIxLDcuODk4LDUuNDIxLDEyLjg0NyAgIFMzNzQuNDksMTU1LjQxMSwzNzAuODgsMTU5LjAyNHoiIGZpbGw9IiMzMzMzMzMiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" /> Ret. Guia</p>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
            @endif

            @if($shipment->obs)
                <div style="border-bottom: 1px solid #ccc;margin: -15px -15px 10px;padding: 10px 15px;">
                    <span class="text-primary">OBSERVAÇÕES</span><br/>
                    {!! textWithUrls(nl2br($shipment->obs)) !!}
                    @if($shipment->obs_internal)
                        <br/>
                        {!! textWithUrls(nl2br($shipment->obs_internal)) !!}
                    @endif
                </div>
            @endif


            {{--@if($shipment->obs || $shipment->obs2)
            <div style="height: 30px"></div>
            @endif--}}

            @if($shipment->reference || $shipment->reference2 || $shipment->reference3)
                <div style="border-bottom: 1px solid #ccc;margin: -15px -15px 10px;padding: 10px 15px;">
            @endif

            @if($shipment->reference)
                <div>
                    <span class="text-primary">REFERÊNCIA</span> {{ $shipment->reference }}
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
                    <span class="text-primary">LINHAS DE REDE</span><br/>
                    @foreach(@$shipment->custom_fields as $field)
                        @if(!empty($field))
                            {{ @$field }}<br/>
                        @endif
                    @endforeach
                </div>
            @endif
            @if($shipment->reference || $shipment->reference2 || $shipment->reference3)
                </div>
            @endif
            <p>
                <span class="text-primary">CLIENTE</span>
                <br/>
                <span class="text-uppercase">{{ @$shipment->customer->code }} - {{ @$shipment->customer->name }}</span>
            </p>
            <p>
                <span class="text-primary m-t-10">ANEXOS</span>
                <ul class="list-unstyled attachments-list m-0">
                    <li>
                        <a href="{{ route('api.mobile.shipments.guide.download', [$shipment->tracking_code, 'user' => config('app.source').'-'.$shipment->operator_id]) }}" target="_blank">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIuMDAxIDUxMi4wMDEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMi4wMDEgNTEyLjAwMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ0NC42MTUsMTk2LjkyM0gzMTUuMjM5Yy0xNC4xNTMsMC0yNS42MjQtMTEuNDczLTI1LjYyNC0yNS42MjRWMTU2LjE4VjI1LjYyNEMyODkuNjEzLDExLjQ3MywyNzguMTQsMCwyNjMuOTg5LDBINzcuOTYgICAgQzU3Ljk2OCwwLDQxLjc2MiwxNi4yMDYsNDEuNzYyLDM2LjE5OXY0MzkuNjA0YzAsMTkuOTkxLDE2LjIwNiwzNi4xOTksMzYuMTk4LDM2LjE5OWgzNTYuMDgyICAgIGMxOS45OTEsMCwzNi4xOTctMTYuMjA2LDM2LjE5Ny0zNi4xOTdWMjIyLjU0N0M0NzAuMjM5LDIwOC4zOTYsNDU4Ljc2NiwxOTYuOTIzLDQ0NC42MTUsMTk2LjkyM3ogTTE0MC4yMjQsODAuMTI3aDY0LjU1ICAgIGMxMS4wMDUsMCwyMC41MDQsOC40NTUsMjAuOTg5LDE5LjQ0OWMwLjUxNSwxMS42Ny04Ljc5NSwyMS4yOTMtMjAuMzUxLDIxLjI5M2gtNjQuNTVjLTExLjAwNSwwLTIwLjUwNC04LjQ1NS0yMC45ODktMTkuNDQ5ICAgIEMxMTkuMzU4LDg5Ljc1MSwxMjguNjY3LDgwLjEyNywxNDAuMjI0LDgwLjEyN3ogTTM3MS4wOTksNDE4LjI5MkgxNDAuODYzYy0xMS4wMDUsMC0yMC41MDYtOC40NTUtMjAuOTkxLTE5LjQ0OSAgICBjLTAuNTE1LTExLjY3LDguNzk1LTIxLjI5MywyMC4zNTEtMjEuMjkzaDIzMC4yMzdjMTEuMDA1LDAsMjAuNTA0LDguNDU2LDIwLjk4OSwxOS40NDkgICAgQzM5MS45NjUsNDA4LjY2OCwzODIuNjU1LDQxOC4yOTIsMzcxLjA5OSw0MTguMjkyeiBNMzcxLjA5OSwzNDkuMDI5SDE0MC44NjNjLTExLjAwNSwwLTIwLjUwNi04LjQ1NS0yMC45OTEtMTkuNDQ5ICAgIGMtMC41MTUtMTEuNjcsOC43OTUtMjEuMjk0LDIwLjM1MS0yMS4yOTRoMjMwLjIzN2MxMS4wMDUsMCwyMC41MDQsOC40NTYsMjAuOTg5LDE5LjQ0OSAgICBDMzkxLjk2NSwzMzkuNDA2LDM4Mi42NTUsMzQ5LjAyOSwzNzEuMDk5LDM0OS4wMjl6IE0zNzEuMDk5LDI4Mi40ODNIMTQwLjg2M2MtMTEuMDA1LDAtMjAuNTA2LTguNDU1LTIwLjk5MS0xOS40NDkgICAgYy0wLjUxNS0xMS42Nyw4Ljc5NS0yMS4yOTQsMjAuMzUxLTIxLjI5NGgyMzAuMjM3YzExLjAwNSwwLDIwLjUwNCw4LjQ1NSwyMC45ODksMTkuNDQ5ICAgIEMzOTEuOTY1LDI3Mi44NTksMzgyLjY1NSwyODIuNDgzLDM3MS4wOTksMjgyLjQ4M3oiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00NTUuMDUzLDE0NC42ODhsLTkyLjU0LTEyOS41NTZDMzU1Ljc0Niw1LjY1NiwzNDQuNzU2LDAsMzMzLjExMiwwYy0xLjUyMiwwLTIuNzU2LDEuMjM0LTIuNzU2LDIuNzU2VjE0My4xNCAgICBjMCw3LjIwMiw1LjgzOCwxMy4wNCwxMy4wMzksMTMuMDRoMTA1Ljc0NEM0NTUuMDUxLDE1Ni4xOCw0NTguNDg5LDE0OS40OTksNDU1LjA1MywxNDQuNjg4eiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                            Guia de Transporte
                        </a>
                    </li>
                    @if(hasModule('shipment_attachments') && !$shipment->attachments->isEmpty())
                        @foreach($shipment->attachments as $attachment)
                            <li>
                                <a href="{{ asset($attachment->filepath) }}" target="_blank">
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIuMDAxIDUxMi4wMDEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMi4wMDEgNTEyLjAwMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ0NC42MTUsMTk2LjkyM0gzMTUuMjM5Yy0xNC4xNTMsMC0yNS42MjQtMTEuNDczLTI1LjYyNC0yNS42MjRWMTU2LjE4VjI1LjYyNEMyODkuNjEzLDExLjQ3MywyNzguMTQsMCwyNjMuOTg5LDBINzcuOTYgICAgQzU3Ljk2OCwwLDQxLjc2MiwxNi4yMDYsNDEuNzYyLDM2LjE5OXY0MzkuNjA0YzAsMTkuOTkxLDE2LjIwNiwzNi4xOTksMzYuMTk4LDM2LjE5OWgzNTYuMDgyICAgIGMxOS45OTEsMCwzNi4xOTctMTYuMjA2LDM2LjE5Ny0zNi4xOTdWMjIyLjU0N0M0NzAuMjM5LDIwOC4zOTYsNDU4Ljc2NiwxOTYuOTIzLDQ0NC42MTUsMTk2LjkyM3ogTTE0MC4yMjQsODAuMTI3aDY0LjU1ICAgIGMxMS4wMDUsMCwyMC41MDQsOC40NTUsMjAuOTg5LDE5LjQ0OWMwLjUxNSwxMS42Ny04Ljc5NSwyMS4yOTMtMjAuMzUxLDIxLjI5M2gtNjQuNTVjLTExLjAwNSwwLTIwLjUwNC04LjQ1NS0yMC45ODktMTkuNDQ5ICAgIEMxMTkuMzU4LDg5Ljc1MSwxMjguNjY3LDgwLjEyNywxNDAuMjI0LDgwLjEyN3ogTTM3MS4wOTksNDE4LjI5MkgxNDAuODYzYy0xMS4wMDUsMC0yMC41MDYtOC40NTUtMjAuOTkxLTE5LjQ0OSAgICBjLTAuNTE1LTExLjY3LDguNzk1LTIxLjI5MywyMC4zNTEtMjEuMjkzaDIzMC4yMzdjMTEuMDA1LDAsMjAuNTA0LDguNDU2LDIwLjk4OSwxOS40NDkgICAgQzM5MS45NjUsNDA4LjY2OCwzODIuNjU1LDQxOC4yOTIsMzcxLjA5OSw0MTguMjkyeiBNMzcxLjA5OSwzNDkuMDI5SDE0MC44NjNjLTExLjAwNSwwLTIwLjUwNi04LjQ1NS0yMC45OTEtMTkuNDQ5ICAgIGMtMC41MTUtMTEuNjcsOC43OTUtMjEuMjk0LDIwLjM1MS0yMS4yOTRoMjMwLjIzN2MxMS4wMDUsMCwyMC41MDQsOC40NTYsMjAuOTg5LDE5LjQ0OSAgICBDMzkxLjk2NSwzMzkuNDA2LDM4Mi42NTUsMzQ5LjAyOSwzNzEuMDk5LDM0OS4wMjl6IE0zNzEuMDk5LDI4Mi40ODNIMTQwLjg2M2MtMTEuMDA1LDAtMjAuNTA2LTguNDU1LTIwLjk5MS0xOS40NDkgICAgYy0wLjUxNS0xMS42Nyw4Ljc5NS0yMS4yOTQsMjAuMzUxLTIxLjI5NGgyMzAuMjM3YzExLjAwNSwwLDIwLjUwNCw4LjQ1NSwyMC45ODksMTkuNDQ5ICAgIEMzOTEuOTY1LDI3Mi44NTksMzgyLjY1NSwyODIuNDgzLDM3MS4wOTksMjgyLjQ4M3oiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00NTUuMDUzLDE0NC42ODhsLTkyLjU0LTEyOS41NTZDMzU1Ljc0Niw1LjY1NiwzNDQuNzU2LDAsMzMzLjExMiwwYy0xLjUyMiwwLTIuNzU2LDEuMjM0LTIuNzU2LDIuNzU2VjE0My4xNCAgICBjMCw3LjIwMiw1LjgzOCwxMy4wNCwxMy4wMzksMTMuMDRoMTA1Ljc0NEM0NTUuMDUxLDE1Ni4xOCw0NTguNDg5LDE0OS40OTksNDU1LjA1MywxNDQuNjg4eiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                                    {{ @$attachment->name }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </p>
            <br/>
            <br/>
        </div>
        <div class="footer-buttons footer-four-btn">
            <ul class="list-unstyled">
                @if(!in_array($shipment->status_id, [5, 9]) && (Setting::get('mobile_app_opt_mark_as_collected') || in_array($shipment->status_id, ['37', '38']))) {{-- Estado lido pelo motorista --}}
                    <li>
                        <a href="#" data-toggle="confirm" data-target="#confirm-modal-{{ $shipment->id }}">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMi4wMDEgNTEyLjAwMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyLjAwMSA1MTIuMDAxOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ1LDMwMkgxNWMtOC4yOTEsMC0xNSw2LjcwOS0xNSwxNXYxODBjMCw4LjI5MSw2LjcwOSwxNSwxNSwxNWgzMGM1LjI4NCwwLDEwLjI4NS0xLjA4MiwxNS0yLjc2M1YzMDQuOTM0ICAgIEM1NS4yOCwzMDMuMjE0LDUwLjMyMSwzMDIsNDUsMzAyeiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ5NC41OTksMzIxLjJjLTEwLjgtNC4yLTIyLjgtMS41MDEtMzAuNTk5LDYuODk5bC04LjEwMSw4LjdsLTY2LDcwLjc5OWMtOC4zOTksOS4zLTIwLjQsMTQuNDAxLTMyLjk5OSwxNC40MDFIMjM5LjUgICAgYy01LjcsMC0xMS4xLDMuMy0xMy41LDguMzk5Yy00LjIsOC4xMDEtMTQuMDk5LDkuOTAxLTIwLjA5OSw2LjYwMWMtNy41LTMuNi0xMC41MDEtMTIuNjAyLTYuOTAxLTIwLjEwMiAgICBDMjA2LjgsNDAxLjYsMjIyLjEsMzkyLDIzOS41LDM5MmM2MC4zLDAsMzMuNjk5LDAsOTIuNSwwYzE2LjUsMCwzMC0xMy41LDMwLTMwcy0xMy41LTMwLTMwLTMwaC01OS4yICAgIGMtNi44OTksMC0xMy44MDEtMS41LTE5Ljc5OS00LjUwMUwyMDAuMiwzMDEuN2MtMzUuODQ3LTE3LjY1Ny03Ny45My0xMi43ODgtMTA5LjIsMTAuOTY4YzAsMTY1LjkwOCwwLDExMi4yOTUsMCwxODYuNzMgICAgQzEyMy40MDEsNTA3LjgsMTU3LDUxMiwxOTAuMyw1MTJIMzQ3YzMyLjk5OSwwLDY0LjItMTUuNjAxLDg0LTQyLjAwMWw3Mi4wMDEtOTZjNi03Ljc5OSw5LTE3LjEwMSw5LTI2Ljk5OSAgICBDNTEyLDMzNS42LDUwNS4xMDEsMzI1LjM5OSw0OTQuNTk5LDMyMS4yeiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTMyMi45NzMsMS43MTRjLTQuMzY1LTIuMjg1LTkuNTgtMi4yODUtMTMuOTQ1LDBMMTk2LjQ4Nyw2MC44TDMxNiwxMzIuNTFsMTE5LjUwOS03MS43MTJMMzIyLjk3MywxLjcxNHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0zMzEsMTU4LjQ5MnYxMzQuODE5bDExMi45NTQtNzAuNTk2YzQuMzgtMi43MzksNy4wNDYtNy41NDQsNy4wNDYtMTIuNzE1Vjg2LjQ5TDMzMSwxNTguNDkyeiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTE4MSw4Ni40OTRWMjEwYzAsNS4xNzEsMi42NjYsOS45NzYsNy4wNDYsMTIuNzE1TDMwMSwyOTMuMzExVjE1OC40OTJMMTgxLDg2LjQ5NHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                        </a>
                    </li>
                @endif

                @if(!@$shipment->status->is_final)
                <li>
                    <a href="#" data-target="#window-incidence" data-shipment-id="{{ $shipment->id }}">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTUwNy40OTQsNDI2LjA2NkwyODIuODY0LDUzLjUzN2MtNS42NzctOS40MTUtMTUuODctMTUuMTcyLTI2Ljg2NS0xNS4xNzJjLTEwLjk5NSwwLTIxLjE4OCw1Ljc1Ni0yNi44NjUsMTUuMTcyICAgIEw0LjUwNiw0MjYuMDY2Yy01Ljg0Miw5LjY4OS02LjAxNSwyMS43NzQtMC40NTEsMzEuNjI1YzUuNTY0LDkuODUyLDE2LjAwMSwxNS45NDQsMjcuMzE1LDE1Ljk0NGg0NDkuMjU5ICAgIGMxMS4zMTQsMCwyMS43NTEtNi4wOTMsMjcuMzE1LTE1Ljk0NEM1MTMuNTA4LDQ0Ny44MzksNTEzLjMzNiw0MzUuNzU1LDUwNy40OTQsNDI2LjA2NnogTTI1Ni4xNjcsMTY3LjIyNyAgICBjMTIuOTAxLDAsMjMuODE3LDcuMjc4LDIzLjgxNywyMC4xNzhjMCwzOS4zNjMtNC42MzEsOTUuOTI5LTQuNjMxLDEzNS4yOTJjMCwxMC4yNTUtMTEuMjQ3LDE0LjU1NC0xOS4xODYsMTQuNTU0ICAgIGMtMTAuNTg0LDAtMTkuNTE2LTQuMy0xOS41MTYtMTQuNTU0YzAtMzkuMzYzLTQuNjMtOTUuOTI5LTQuNjMtMTM1LjI5MkMyMzIuMDIxLDE3NC41MDUsMjQyLjYwNSwxNjcuMjI3LDI1Ni4xNjcsMTY3LjIyN3ogICAgIE0yNTYuNDk4LDQxMS4wMThjLTE0LjU1NCwwLTI1LjQ3MS0xMS45MDgtMjUuNDcxLTI1LjQ3YzAtMTMuODkzLDEwLjkxNi0yNS40NywyNS40NzEtMjUuNDdjMTMuNTYyLDAsMjUuMTQsMTEuNTc3LDI1LjE0LDI1LjQ3ICAgIEMyODEuNjM4LDM5OS4xMSwyNzAuMDYsNDExLjAxOCwyNTYuNDk4LDQxMS4wMTh6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                    </a>
                </li>
                @endif

                @if(in_array($shipment->status_id, [5, 9]) && (config('app.source') == 'giroflex' || Setting::get('mobile_app_opt_edit_shipment')))
                    <li>
                        <a href="#" data-toggle="confirm" data-target="#add-details-{{ $shipment->id }}">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBoZWlnaHQ9IjI0cHgiIHZpZXdCb3g9IjAgMCA0NDggNDQ4IiB3aWR0aD0iMjRweCI+PHBhdGggZD0ibTEzMy40ODQzNzUgMjY5LjI1IDE0Ny4wOTM3NS0xNDcuMDcwMzEyIDQ1LjI1MzkwNiA0NS4yNTc4MTItMTQ3LjA5Mzc1IDE0Ny4wNzAzMTJ6bTAgMCIgZmlsbD0iI0ZGRkZGRiIvPjxwYXRoIGQ9Im0zMjUuODI0MjE5IDkwLjE3NTc4MWMtOC40ODgyODEtLjAxOTUzMS0xNi42MzI4MTMgMy4zNTE1NjMtMjIuNjI1IDkuMzY3MTg4bC0xMS4zMTI1IDExLjMyMDMxMiA0NS4yNjU2MjUgNDUuMjU3ODEzIDExLjMwNDY4Ny0xMS4zMjAzMTNjOS4xNTYyNS05LjE1MjM0MyAxMS44OTQ1MzEtMjIuOTE3OTY5IDYuOTQxNDA3LTM0Ljg3ODkwNi00Ljk1NzAzMi0xMS45NjA5MzctMTYuNjI4OTA3LTE5Ljc1NzgxMy0yOS41NzQyMTktMTkuNzUzOTA2em0wIDAiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJtMTA2LjUzNTE1NiAzNDEuNDY0ODQ0IDU3LjQwMjM0NC0xOS4xMzY3MTktMzguMjY1NjI1LTM4LjI2NTYyNXptMCAwIiBmaWxsPSIjRkZGRkZGIi8+PHBhdGggZD0ibTIyNCAwYy0xMjMuNzEwOTM4IDAtMjI0IDEwMC4yODkwNjItMjI0IDIyNHMxMDAuMjg5MDYyIDIyNCAyMjQgMjI0IDIyNC0xMDAuMjg5MDYyIDIyNC0yMjRjLS4xNDA2MjUtMTIzLjY1MjM0NC0xMDAuMzQ3NjU2LTIyMy44NTkzNzUtMjI0LTIyNHptMTM1Ljc2OTUzMSAxNTYuMTIxMDk0LTE3Ni43MDcwMzEgMTc2LjY3OTY4Ny0xMDEuODI0MjE5IDMzLjk2MDkzOCAzMy45NjA5MzgtMTAxLjgyNDIxOSAxNzYuNjc5Njg3LTE3Ni43MDcwMzFjMTguNzQ2MDk0LTE4Ljc0NjA5NCA0OS4xNDA2MjUtMTguNzQ2MDk0IDY3Ljg5MDYyNSAwIDE4Ljc0NjA5NCAxOC43NSAxOC43NDYwOTQgNDkuMTQ0NTMxIDAgNjcuODkwNjI1em0wIDAiIGZpbGw9IiNGRkZGRkYiLz48L3N2Zz4K" />
                        </a>
                    </li>
                @endif

                @if(in_array($shipment->status_id, [5, 9]))
                <li>
                    <a href="#" data-toggle="confirm" data-target="#delivery-details-{{ $shipment->id }}">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDUxMCA1MTAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMCA1MTA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8ZyBpZD0iaW5mbyI+CgkJPHBhdGggZD0iTTI1NSwwQzExNC43NSwwLDAsMTE0Ljc1LDAsMjU1czExNC43NSwyNTUsMjU1LDI1NXMyNTUtMTE0Ljc1LDI1NS0yNTVTMzk1LjI1LDAsMjU1LDB6IE0yODAuNSwzODIuNWgtNTF2LTE1M2g1MVYzODIuNXogICAgIE0yODAuNSwxNzguNWgtNTF2LTUxaDUxVjE3OC41eiIgZmlsbD0iI0ZGRkZGRiIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                    </a>
                </li>
                @endif

                <li>
                    <a href="#" data-target="#window-delivery" data-shipment-id="{{ $shipment->id }}" data-shipment-return="{{ is_array($shipment->has_return) && in_array('rpack', $shipment->has_return) ? 1 : 0  }}">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUyIDUyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MiA1MjsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIyNHB4IiBoZWlnaHQ9IjI0cHgiPgo8Zz4KCTxwYXRoIGQ9Ik0yNiwwQzExLjY2NCwwLDAsMTEuNjYzLDAsMjZzMTEuNjY0LDI2LDI2LDI2czI2LTExLjY2MywyNi0yNlM0MC4zMzYsMCwyNiwweiBNNDAuNDk1LDE3LjMyOWwtMTYsMTggICBDMjQuMTAxLDM1Ljc3MiwyMy41NTIsMzYsMjIuOTk5LDM2Yy0wLjQzOSwwLTAuODgtMC4xNDQtMS4yNDktMC40MzhsLTEwLThjLTAuODYyLTAuNjg5LTEuMDAyLTEuOTQ4LTAuMzEyLTIuODExICAgYzAuNjg5LTAuODYzLDEuOTQ5LTEuMDAzLDIuODExLTAuMzEzbDguNTE3LDYuODEzbDE0LjczOS0xNi41ODFjMC43MzItMC44MjYsMS45OTgtMC45LDIuODIzLTAuMTY2ICAgQzQxLjE1NCwxNS4yMzksNDEuMjI5LDE2LjUwMyw0MC40OTUsMTcuMzI5eiIgZmlsbD0iI0ZGRkZGRiIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                    </a>
                </li>

                @if(!in_array($shipment->status_id, [5, 9]) && Setting::get('mobile_app_transfer_shipments'))
                <li>
                    <a href="{{ route('mobile.shipments.transfer', $shipment->id) }}" data-toggle="ajax" data-method="get" data-target="#main-window">
                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ5MC4wNjcgNDkwLjA2NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDkwLjA2NyA0OTAuMDY3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCI+CjxnPgoJPHBhdGggZD0iTTE3OC40MTQsNDY3LjAzOGM0Ljg4OS0yNy4xMjIsMTYuNDE2LTUyLjMxOCwzMy4xMjctNzMuNzk1Yy0zLjI0Ni0zLjIyOS02LjYwOS02LjMzNC0xMC4yMTEtOS4xNjQgICBjLTQuOTA4LTMuODg0LTkuMTY0LTguNDMxLTEyLjc5NS0xMy40MjFjLTE3LjAyNSwxNS4wNjktMzcuOTEyLDI0LjA4MS02MC41NDUsMjQuMDgxYy0yMi42MzEsMC00My41MTgtOS4wMTItNjAuNTM5LTI0LjA4MSAgIGMtMy42MzEsNC45ODItNy44NzUsOS41MjItMTIuNzg5LDEzLjQyMWMtMjIsMTcuMzI0LTM3Ljg3Nyw0Mi4zMzctNDMuMTU0LDcxLjYwM2wtMi4yMzEsMTIuNDU2ICAgYy0wLjk3Myw1LjQyNywwLjUwOCwxMS4wMTcsNC4wNTEsMTUuMjQ3YzMuNTQxLDQuMjQsOC43NzksNi42ODIsMTQuMzAxLDYuNjgyaDE1Mi41MjUgICBDMTc3Ljc4NSw0ODIuNzE1LDE3Ny4wMTgsNDc0Ljg3OCwxNzguNDE0LDQ2Ny4wMzh6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8cGF0aCBkPSJNNDMwLjkwNiwzODkuOTVjLTQuMDYxLTMuMTk1LTcuNjExLTYuODc0LTEwLjkyLTEwLjc1Yy0yMC42OTEsMTkuMDczLTQ2LjI3MSwzMC41MTItNzQuMDMxLDMwLjUxMiAgIGMtMjcuNzcxLDAtNTMuMzQ4LTExLjQzOS03NC4wNDktMzAuNDk0Yy0zLjI5NywzLjg1OC02Ljg0LDcuNTMtMTAuOSwxMC43MzJjLTI1LjUsMjAuMDc4LTQzLjg3Nyw0OS4wNDMtNDkuOTgyLDgyLjk2MiAgIGMtMC43Niw0LjI1MywwLjQwNCw4LjYyMywzLjE3OCwxMS45MzFjMi43NjgsMy4zMSw2Ljg2OSw1LjIyNSwxMS4xODksNS4yMjVoNy44MDdoMjMzLjI5MWM0LjMyLDAsOC40MjItMS45MTUsMTEuMTg0LTUuMjI1ICAgYzIuNzc5LTMuMzA4LDMuOTQxLTcuNjc4LDMuMTg0LTExLjkzMUM0NzQuNzI1LDQzOC45OTMsNDU2LjM3Nyw0MTAuMDI4LDQzMC45MDYsMzg5Ljk1eiIgZmlsbD0iI0ZGRkZGRiIvPgoJPHBhdGggZD0iTTI1NS4wMzUsMjU2Ljg5NWMwLDY2LjExMSw0MC43MzYsMTE5LjY4NSw5MC45MiwxMTkuNjg1YzUwLjE3OCwwLDkwLjg3My01My41NzMsOTAuODczLTExOS42ODUgICBjMC02Ni4wNzktNDAuNjk1LTExOS42NTItOTAuODczLTExOS42NTJDMjk1Ljc3MiwxMzcuMjQyLDI1NS4wMzUsMTkwLjgxNSwyNTUuMDM1LDI1Ni44OTV6IiBmaWxsPSIjRkZGRkZGIi8+Cgk8cGF0aCBkPSJNMTI3Ljk5LDM2MS42MDZjMzguNjgsMCw3MC4wMTgtNDEuMjcsNzAuMDE4LTkyLjE0OGMwLTUwLjkwNC0zMS4zMzgtOTIuMTY2LTcwLjAxOC05Mi4xNjYgICBjLTM4LjYyNSwwLTY5Ljk5Niw0MS4yNjItNjkuOTk2LDkyLjE2NkM1Ny45OTQsMzIwLjMzNyw4OS4zNjUsMzYxLjYwNiwxMjcuOTksMzYxLjYwNnoiIGZpbGw9IiNGRkZGRkYiLz4KCTxwYXRoIGQ9Ik0xMDkuODQ4LDExMS44NzdsNzYuMjEzLDAuMDQ4YzYuOTk0LDAuMDEsMTMuMjY2LTQuMjIxLDE1LjkzNC0xMC42MzdjMi42NjgtNi40NDYsMS4xOTEtMTMuODY0LTMuNzUyLTE4Ljc5OCAgIGwtMTYuNDI2LTE2LjQzYzE5LjgxMy0xMC42NzcsNDIuMDgyLTE2LjM2NCw2NS4yMzItMTYuMzY0YzM2LjgxNCwwLDcxLjQzLDE0LjMzNCw5Ny40NTksNDAuMzY0ICAgYzQuODUzLDQuODU0LDExLjIwOSw3LjI4LDE3LjU2OCw3LjI4YzYuMzU1LDAsMTIuNzE3LTIuNDI3LDE3LjU3LTcuMjhjOS43MDctOS42OTgsOS43MDctMjUuNDMxLDAtMzUuMTM5ICAgQzM0NC4yMzMsMTkuNTAxLDI5Ny4xMzksMCwyNDcuMDQ5LDBjLTM2LjU0NSwwLTcxLjQzOSwxMC40OS0xMDEuNDM0LDI5Ljg2MkwxMjIuMDIsNi4yNjhjLTQuOTI4LTQuOTM0LTEyLjMyNi02LjQwOC0xOC44MTYtMy43NDcgICBjLTYuNDIsMi42NjItMTAuNjA5LDguOTI1LTEwLjYwOSwxNS45Mzd2NzYuMTlDOTIuNTk0LDEwNC4xNDUsMTAwLjMyNCwxMTEuODc3LDEwOS44NDgsMTExLjg3N3oiIGZpbGw9IiNGRkZGRkYiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                    </a>
                </li>
                @endif

                {{--<li>
                    <a href="#" data-toggle="confirm" data-target="#confirm-gps-{{ $shipment->id }}">
                        <img height="24" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDQ1LjM1NCA0NS4zNTQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ1LjM1NCA0NS4zNTQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIiBjbGFzcz0iIj48Zz48Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0yMi42NzcsMEMxMi41MDksMCw0LjI2Niw4LjI0Myw0LjI2NiwxOC40MTFjMCw5LjIyNCwxMS40NzEsMjEuMzYsMTYuMzA1LDI2LjA2NWMxLjE4NCwxLjE1LDMuMDU2LDEuMTc0LDQuMjYzLDAuMDQ3ICAgIGM0Ljg2My00LjUzMywxNi4yNTQtMTYuMjExLDE2LjI1NC0yNi4xMTNDNDEuMDg3LDguMjQzLDMyLjg0NSwwLDIyLjY3NywweiBNMjIuNjc3LDI0LjM5M2MtNC4yMDQsMC03LjYxLTMuNDA2LTcuNjEtNy42MDkgICAgczMuNDA2LTcuNjEsNy42MS03LjYxYzQuMjAzLDAsNy42MDgsMy40MDYsNy42MDgsNy42MVMyNi44OCwyNC4zOTMsMjIuNjc3LDI0LjM5M3oiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSIiIHN0eWxlPSJmaWxsOiNGRkZGRkYiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+Cgk8L2c+CjwvZz48L2c+IDwvc3ZnPg==" />
                    </a>
                </li>--}}
            </ul>
        </div>
    </div>
</div>

@if(in_array($shipment->status_id, [5, 9]))
<div class="confirm-modal" id="delivery-details-{{ $shipment->id }}" style="display: none;">
    @if($shipment->status_id == 5)
        <h4>Informação de Entrega</h4>
        <div style="margin-left: 10px; margin-right: 20px;">
            <p>Entregue em: <b>{{ @$shipment->lastHistory->created_at }}</b></p>

            @if($shipment->lastHistory->latitude && $shipment->lastHistory->longitude)
                <p>
                    <a href="http://maps.google.com/maps?q={{ $shipment->lastHistory->latitude }},{{ $shipment->lastHistory->longitude }}" target="_blank">Ver localização de entrega</a>
                </p>
            @endif

            @if($shipment->lastHistory->receiver)
            <p>Recebido Por: <b>{{ @$shipment->lastHistory->receiver }}</b></p>
            @endif

            @if(@$shipment->lastHistory->obs)
            <p>Obs: <b>{{ @$shipment->lastHistory->obs }}</b></p>
            @endif

            @if(@$shipment->lastHistory->signature)
            <img src="{{ @$shipment->lastHistory->signature }}" style="max-width: 160px; max-height: 160px; border: 1px solid #ddd;">
            @endif
        </div>
    @else
        <h4>Informação de Incidência</h4>
        <div style="margin-left: 5px; margin-right: 10px;">
            <p>Incidência em: <b>{{ @$shipment->lastHistory->created_at }}</b></p>

            @if(@$shipment->lastHistory->incidence->name)
                <p>Motivo: <b>{{ @$shipment->lastHistory->incidence->name }}</b></p>
            @endif

            @if(@$shipment->lastHistory->obs)
                <p>Obs: <b>{{ @$shipment->lastHistory->obs }}</b></p>
            @endif
        </div>
    @endif
    <div class="spacer-30"></div>
    <button class="btn btn-default error-close" type="button">Fechar</button>
</div>
@endif

@if(Setting::get('mobile_app_opt_mark_as_collected') || in_array($shipment->status_id, ['37', '38']))
    <div class="confirm-modal" id="confirm-modal-{{ $shipment->id }}" style="display: none;">
        <h4>Confirma a recolha do serviço?</h4>
        <p>Ao confirmar a recolha irá colocar o como recolhido e passará para o estado em transporte.</p>
        <button href="{{ route('mobile.shipments.set.read', $shipment->id) }}" data-toggle="ajax" data-method="get" data-target="#main-window" class="btn btn-default">
            Marcar como Recolhido
        </button>
        <button class="btn btn-default error-close">Cancelar</button>
    </div>
@endif

@if(config('app.source') == 'giroflex' || Setting::get('mobile_app_opt_edit_shipment'))
    {{ Form::open(['route' => ['mobile.shipments.update', $shipment->id], 'method' => 'POST', 'class' => 'ajax-form']) }}
    <div class="confirm-modal" id="add-details-{{ $shipment->id }}" style="display: none;">
            <h4>Adicionar Informação Envio</h4>
            <div style="margin-left: 20px; margin-right: 40px;">
                <input type="text" name="reference" value="{{ $shipment->reference }}" placeholder="Referência" />

                @if(Setting::get('shipments_reference2'))
                    <input type="text" name="reference2" value="{{ $shipment->reference2 }}" placeholder="{{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Referência 2' }}" />
                @endif

                @if(Setting::get('shipments_reference3'))
                    <input type="text" name="reference3" value="{{ $shipment->reference3 }}" placeholder="{{ Setting::get('shipments_reference3_name') ? Setting::get('shipments_reference3_name') : 'Referência 3' }}" />
                @endif
            </div>
            <div class="spacer-30"></div>
            <button class="btn btn-default error-close" type="button">Cancelar</button>
            <button class="btn btn-default" type="submit">Gravar</button>
    </div>
    {{ Form::close() }}
@endif

{{--
<div class="confirm-modal" id="confirm-gps-{{ $shipment->id }}" style="display: none;">
    <h4>Para onde pretende iniciar a navegação?</h4>

    <a href="{{ \App\Http\Controllers\Mobile\BaseController::getGoogleMapsUrl($shipment, false) }}" target="_blank" class="btn btn-default">
        Morada Carga
    </a>
    <br/>
    <a href="{{ \App\Http\Controllers\Mobile\BaseController::getGoogleMapsUrl($shipment) }}" target="_blank" class="btn btn-default">
        Morada Descarga
    </a>
    <br/>
    <button class="btn btn-default error-close">Cancelar</button>
</div>--}}
