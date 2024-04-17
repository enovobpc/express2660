<div class="add-new" data-toggle="modal" data-target="#box-form">
    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjY0cHgiIGhlaWdodD0iNjRweCIgdmlld0JveD0iMCAwIDUxMCA1MTAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMCA1MTA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8ZyBpZD0iYWRkLWNpcmNsZSI+CgkJPHBhdGggZD0iTTI1NSwwQzExNC43NSwwLDAsMTE0Ljc1LDAsMjU1czExNC43NSwyNTUsMjU1LDI1NXMyNTUtMTE0Ljc1LDI1NS0yNTVTMzk1LjI1LDAsMjU1LDB6IE0zODIuNSwyODAuNWgtMTAydjEwMmgtNTF2LTEwMiAgICBoLTEwMnYtNTFoMTAydi0xMDJoNTF2MTAyaDEwMlYyODAuNXoiIGZpbGw9IiMwZWFiMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
</div>
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
<div class="box-form" id="box-form" style="display: none; z-index: 15;">
    {{ Form::open(['route' => 'mobile.pendings.store', 'class' => 'ajax-form']) }}
    <div class="form-group">
        {{ Form::label('name', 'Título da tarefa') }}
        {{ Form::text('name', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
    </div>
    <div class="form-group">
        {{ Form::label('description', 'Detalhes (opcional)') }}
        {{ Form::textarea('description', null, array('class' => 'form-control', 'rows' => 3)) }}
    </div>
    <div class="form-group">
        <div style="width: 30px; float: left">
            {{ Form::label('date', 'Data') }}
        </div>
        <div style="width: 225px;  float: right">
            <div style="width: 60px; float: left">
                {{ Form::select('date_y', yearsArr(2019, date('Y')), null, array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
            </div>
            <div class="pull-left" style="width: 100px; float: left">
                {{ Form::select('date_m', trans('datetime.list-month'), date('m'), array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
            </div>
            <div class="pull-left" style="width: 50px; float: left">
                {{ Form::select('date_d', listArr(1, 31), date('d'), array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <button class="btn btn-default">Gravar</button>
    <button class="btn btn-default btn-modal-close" type="button">Fechar</button>
    {{ Form::close() }}
</div>
<div class="window shipments-pending-list" style="margin-top: 40px;">
    <div class="alert-modal" style="display: none">
        <div class="box">
            <p></p>
            <button class="btn btn-default btn-close">Fechar</button>
        </div>
    </div>
    @include('mobile.partials.feedback')
    <div class="tab">
        <div class="tab-item {{ $tab == 'tab-pending' || empty($tab) ? 'active' : '' }}" data-toggle="tab" data-target="#tab-pending">Pendente{{ $tasksPending ? ' ('.count($tasksPending).')' : '' }}</div>
        <div class="tab-item {{ $tab == 'tab-accepted' ? 'active' : '' }}" data-toggle="tab" data-target="#tab-accepted">Aceite{{ $tasksAccepted ? ' ('.count($tasksAccepted).')' : '' }}</div>
        <div class="tab-item {{ $tab == 'tab-concluded' ? 'active' : '' }}" data-toggle="tab" data-target="#tab-concluded">Concluído</div>
        <div class="tab-item" data-toggle="tab" data-target="#tab-operators">Operadores</div>
    </div>

    <div class="tab-content" id="tab-pending" style="{{ $tab == 'tab-pending' || empty($tab) ? '' : 'display:none' }}">
        @if(@$totalPickups)
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="padding: 15px; background: #ea6d31; color: #fff; text-align: center;">
                Tem {{ $totalPickups }} recolhas para realizar.
                <a href="{{ route('mobile.shipments.index') }}" data-toggle="ajax" style="color: #fff;
    border: 1px solid #fff;
    border-radius: 100px;
    padding: 0 10px;
    margin-left: 12px;
    text-decoration: none;">VER</a>
            </li>
        </ul>
        @endif
        @if($tasksPending->isEmpty())
            <div class="filter-noresults" style="margin-top: 80px;">
                <h4>Nada há serviços pendentes.</h4>
            </div>
        @else
            <ul class="list w-100 m-0 p-0 pendings-list" id="pendingList">
                @foreach($tasksPending as $task)
                <li class="list-item" data-id="{{ $task->id }}">
                    <span class="arrow-right">
                        {{ Form::open(['route' => ['mobile.pendings.update', $task->id], 'method' => 'post',  'class' => 'ajax-form']) }}
                        <input type="hidden" name="tab_active" value="tab-pending"/>
                        <input type="hidden" name="concluded" value="0"/>
                        <input type="hidden" name="readed" value="1"/>
                        <button type="submit" class="btn-pending-action">Aceitar</button>
                        {{ Form::close() }}
                    </span>
                    <div class="recipient" data-toggle="alert" data-text="{{ $task->description . (($task->details && $task->description) ? '<hr/>' : '') . nl2br($task->details) }}<hr/><small>{{ $task->full_address }}</small>">
                        <b>{{ $task->name }}
                        @if($task->description)
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDUxMCA1MTAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMCA1MTA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8ZyBpZD0iaW5mbyI+CgkJPHBhdGggZD0iTTI1NSwwQzExNC43NSwwLDAsMTE0Ljc1LDAsMjU1czExNC43NSwyNTUsMjU1LDI1NXMyNTUtMTE0Ljc1LDI1NS0yNTVTMzk1LjI1LDAsMjU1LDB6IE0yODAuNSwzODIuNWgtNTF2LTE1M2g1MVYzODIuNXogICAgIE0yODAuNSwxNzguNWgtNTF2LTUxaDUxVjE3OC41eiIgZmlsbD0iIzAwNkRGMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                        @endif
                        </b>
                        <br/>
                        <ul class="attributes">
                            <li>
                                <img style="margin-bottom: -2px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDk3LjE2IDk3LjE2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA5Ny4xNiA5Ny4xNjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00OC41OCwwQzIxLjc5MywwLDAsMjEuNzkzLDAsNDguNThzMjEuNzkzLDQ4LjU4LDQ4LjU4LDQ4LjU4czQ4LjU4LTIxLjc5Myw0OC41OC00OC41OFM3NS4zNjcsMCw0OC41OCwweiBNNDguNTgsODYuODIzICAgIGMtMjEuMDg3LDAtMzguMjQ0LTE3LjE1NS0zOC4yNDQtMzguMjQzUzI3LjQ5MywxMC4zMzcsNDguNTgsMTAuMzM3Uzg2LjgyNCwyNy40OTIsODYuODI0LDQ4LjU4UzY5LjY2Nyw4Ni44MjMsNDguNTgsODYuODIzeiIgZmlsbD0iIzMzMzMzMyIvPgoJCTxwYXRoIGQ9Ik03My44OTgsNDcuMDhINTIuMDY2VjIwLjgzYzAtMi4yMDktMS43OTEtNC00LTRjLTIuMjA5LDAtNCwxLjc5MS00LDR2MzAuMjVjMCwyLjIwOSwxLjc5MSw0LDQsNGgyNS44MzIgICAgYzIuMjA5LDAsNC0xLjc5MSw0LTRTNzYuMTA3LDQ3LjA4LDczLjg5OCw0Ny4wOHoiIGZpbGw9IiMzMzMzMzMiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                                Há {{ timeElapsedString($task->last_update) }}
                            </li>
                            @if($task->volumes)
                            <li>
                                <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ3My44IDQ3My44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA0NzMuOCA0NzMuODsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiPgo8Zz4KCTxwYXRoIGQ9Ik00NTQuOCwxMTEuN2MwLTEuOC0wLjQtMy42LTEuMi01LjNjLTEuNi0zLjQtNC43LTUuNy04LjEtNi40TDI0MS44LDEuMmMtMy4zLTEuNi03LjItMS42LTEwLjUsMEwyNS42LDEwMC45ICAgYy00LDEuOS02LjYsNS45LTYuOCwxMC40djAuMWMwLDAuMSwwLDAuMiwwLDAuNFYzNjJjMCw0LjYsMi42LDguOCw2LjgsMTAuOGwyMDUuNyw5OS43YzAuMSwwLDAuMSwwLDAuMiwwLjEgICBjMC4zLDAuMSwwLjYsMC4yLDAuOSwwLjRjMC4xLDAsMC4yLDAuMSwwLjQsMC4xYzAuMywwLjEsMC42LDAuMiwwLjksMC4zYzAuMSwwLDAuMiwwLjEsMC4zLDAuMWMwLjMsMC4xLDAuNywwLjEsMSwwLjIgICBjMC4xLDAsMC4yLDAsMC4zLDBjMC40LDAsMC45LDAuMSwxLjMsMC4xYzAuNCwwLDAuOSwwLDEuMy0wLjFjMC4xLDAsMC4yLDAsMC4zLDBjMC4zLDAsMC43LTAuMSwxLTAuMmMwLjEsMCwwLjItMC4xLDAuMy0wLjEgICBjMC4zLTAuMSwwLjYtMC4yLDAuOS0wLjNjMC4xLDAsMC4yLTAuMSwwLjQtMC4xYzAuMy0wLjEsMC42LTAuMiwwLjktMC40YzAuMSwwLDAuMSwwLDAuMi0wLjFsMjA2LjMtMTAwYzQuMS0yLDYuOC02LjIsNi44LTEwLjggICBWMTEyQzQ1NC44LDExMS45LDQ1NC44LDExMS44LDQ1NC44LDExMS43eiBNMjM2LjUsMjUuM2wxNzguNCw4Ni41bC02NS43LDMxLjlMMTcwLjgsNTcuMkwyMzYuNSwyNS4zeiBNMjM2LjUsMTk4LjNMNTguMSwxMTEuOCAgIGw4NS4yLTQxLjNMMzIxLjcsMTU3TDIzNi41LDE5OC4zeiBNNDIuOCwxMzEuMWwxODEuNyw4OC4xdjIyMy4zTDQyLjgsMzU0LjRWMTMxLjF6IE0yNDguNSw0NDIuNVYyMTkuMmw4NS4zLTQxLjR2NTguNCAgIGMwLDYuNiw1LjQsMTIsMTIsMTJzMTItNS40LDEyLTEydi03MC4xbDczLTM1LjRWMzU0TDI0OC41LDQ0Mi41eiIgZmlsbD0iIzMzMzMzMyIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" style="margin-bottom: -3px;"/>
                                {{ $task->volumes }} Vol.
                            </li>
                            @endif
                            @if($task->weight)
                                <li>
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDYxMiA2MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDYxMiA2MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNNjEwLjQzNCw1MTIuNzE2bC05NS45ODgtMjk2LjY5MWMtNC4yNDQtMTMuMTE3LTE2LjQ1OS0yMi4wMDMtMzAuMjQ1LTIyLjAwM0gzODIuOTA0ICAgYzguMjExLTEzLjU2MywxMy4wMjgtMjkuMzk5LDEzLjAyOC00Ni4zNzljMC00OS41ODYtNDAuMzQ2LTg5LjkzMy04OS45MzMtODkuOTMzYy00OS41ODYsMC04OS45MzMsNDAuMzQ2LTg5LjkzMyw4OS45MzMgICBjMCwxNi45NzksNC44MTcsMzIuODE1LDEzLjAyOSw0Ni4zNzlIMTI3LjhjLTEzLjc4NiwwLTI2LjAwMSw4Ljg4Ni0zMC4yNDUsMjIuMDAzTDEuNTY3LDUxMi43MTYgICBjLTYuNjQzLDIwLjUzMSw4LjY2Niw0MS41NzMsMzAuMjQ1LDQxLjU3M2g1NDguMzc2QzYwMS43NjgsNTU0LjI5LDYxNy4wNzYsNTMzLjI0OCw2MTAuNDM0LDUxMi43MTZ6IE0yNTguNDUyLDE0Ny42NDMgICBjMC0yNi4yMjEsMjEuMzI3LTQ3LjU0OCw0Ny41NDgtNDcuNTQ4YzI2LjIyMSwwLDQ3LjU0OCwyMS4zMjcsNDcuNTQ4LDQ3LjU0OGMwLDIyLjcwNS0xNi4wMTUsNDEuNjgyLTM3LjMyNyw0Ni4zNzlIMjk1Ljc4ICAgQzI3NC40NjcsMTg5LjMyNiwyNTguNDUyLDE3MC4zNDgsMjU4LjQ1MiwxNDcuNjQzeiBNMjYzLjk2Miw0NTEuMDMybC0yNy40NzUtNTIuNzU2aC0xMy4wOTJ2NTIuNzU2aC0zMS44M1YzMjAuOTc4aDMxLjgzdjUwLjIwOCAgIGgxMy4wOTJsMjYuOTI3LTUwLjIwOGgzNC4xOThsLTM1LjExNyw2Mi4yMDV2MC4zN2wzNy4xMTYsNjcuNDc5SDI2My45NjJ6IE00MzEuMzY5LDQ1MS4wMzJoLTI2LjU1N3YtMy42NDQgICBjMC0yLjcyNiwwLjE3OC01LjQ1LDAuMTc4LTUuNDVoLTAuMzU1YzAsMC0xMi41NiwxMS4yNzEtMzQuMTk4LDExLjI3MWMtMzMuMjk0LDAtNjMuODUtMjQuOTEyLTYzLjg1LTY3LjQ3OSAgIGMwLTM3LjgyNywyOC41NTUtNjYuOTMxLDY4LjIxOS02Ni45MzFjMzMuMjgsMCw1MC4wMTcsMTcuMjcxLDUwLjAxNywxNy4yNzFsLTE1LjI4NSwyMy44MzFjMCwwLTEzLjI3MS0xMS45OTctMzEuNjUtMTEuOTk3ICAgYy0yNy4yODIsMC0zOC4zNzUsMTcuNDYyLTM4LjM3NSwzNi43M2MwLDI0Ljc1LDE3LjA5MiwzOS40NzIsMzcuMjk0LDM5LjQ3MmMxNS4yNywwLDI2LjM2Mi05LjQ0OSwyNi4zNjItOS40NDl2LTEwLjAxM2gtMTguMzY1ICAgdi0yNy4xMDRoNDYuNTY1TDQzMS4zNjksNDUxLjAzMkw0MzEuMzY5LDQ1MS4wMzJ6IiBmaWxsPSIjMzMzMzMzIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                                    {{ $task->weight }}kg
                                </li>
                            @endif
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="tab-content" id="tab-accepted" style="{{ $tab == 'tab-accepted' ? '' : 'display:none' }}">
        @if($tasksAccepted->isEmpty())
            <div class="filter-noresults" style="margin-top: 80px;">
                <h4>Nada há serviços aceites.</h4>
            </div>
        @else
            <ul class="list w-100 m-0 p-0 pendings-list" id="acceptedList">
                @foreach($tasksAccepted as $task)
                    <li class="list-item accepted" data-id="{{ $task->id }}" style="background: {{ $task->deleted ? '#f95656' : ''}}">
                        <span class="arrow-right">
                            @if($task->deleted)
                                {{ Form::open(['route' => ['mobile.pendings.update', $task->id], 'method' => 'post',  'class' => 'ajax-form']) }}
                                <input type="hidden" name="concluded" value="1"/>
                                <input type="hidden" name="readed" value="1"/>
                                <input type="hidden" name="tab_active" value="tab-accepted"/>
                                <button type="submit" class="btn-pending-action">Apagar</button>
                                {{ Form::close() }}
                            @else
                                {{ Form::open(['route' => ['mobile.pendings.update', $task->id], 'method' => 'post',  'class' => 'ajax-form']) }}
                                <input type="hidden" name="concluded" value="1"/>
                                <input type="hidden" name="readed" value="1"/>
                                <input type="hidden" name="tab_active" value="tab-accepted"/>
                                <button type="submit" class="btn-pending-action pending-assigned" style="padding-right: 50px;">Concluir</button>
                                {{ Form::close() }}

                                {{ Form::open(['route' => ['mobile.pendings.update', $task->id], 'method' => 'post',  'class' => 'ajax-form']) }}
                                <input type="hidden" name="concluded" value="0"/>
                                <input type="hidden" name="readed" value="0"/>
                                <input type="hidden" name="tab_active" value="tab-accepted"/>
                                <button type="submit" class="btn-pending-action" style="width: 40px;">✕</button>
                                {{ Form::close() }}
                            @endif
                        </span>
                        <div class="recipient" data-toggle="alert" data-text="{{ $task->description . (($task->details && $task->description) ? '<hr/>' : '') . nl2br($task->details) }}<hr/><small>{{ $task->full_address }}</small>">
                            <b>{{ $task->name }}
                                @if($task->description)
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDUxMCA1MTAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMCA1MTA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8ZyBpZD0iaW5mbyI+CgkJPHBhdGggZD0iTTI1NSwwQzExNC43NSwwLDAsMTE0Ljc1LDAsMjU1czExNC43NSwyNTUsMjU1LDI1NXMyNTUtMTE0Ljc1LDI1NS0yNTVTMzk1LjI1LDAsMjU1LDB6IE0yODAuNSwzODIuNWgtNTF2LTE1M2g1MVYzODIuNXogICAgIE0yODAuNSwxNzguNWgtNTF2LTUxaDUxVjE3OC41eiIgZmlsbD0iIzAwNkRGMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                                @endif
                            </b>
                            <br/>
                            <ul class="attributes">
                                @if($task->deleted)
                                    <li>
                                        Recolha Anulada
                                    </li>
                                @else
                                    <li>
                                        <img style="margin-bottom: -2px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDk3LjE2IDk3LjE2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA5Ny4xNiA5Ny4xNjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00OC41OCwwQzIxLjc5MywwLDAsMjEuNzkzLDAsNDguNThzMjEuNzkzLDQ4LjU4LDQ4LjU4LDQ4LjU4czQ4LjU4LTIxLjc5Myw0OC41OC00OC41OFM3NS4zNjcsMCw0OC41OCwweiBNNDguNTgsODYuODIzICAgIGMtMjEuMDg3LDAtMzguMjQ0LTE3LjE1NS0zOC4yNDQtMzguMjQzUzI3LjQ5MywxMC4zMzcsNDguNTgsMTAuMzM3Uzg2LjgyNCwyNy40OTIsODYuODI0LDQ4LjU4UzY5LjY2Nyw4Ni44MjMsNDguNTgsODYuODIzeiIgZmlsbD0iIzMzMzMzMyIvPgoJCTxwYXRoIGQ9Ik03My44OTgsNDcuMDhINTIuMDY2VjIwLjgzYzAtMi4yMDktMS43OTEtNC00LTRjLTIuMjA5LDAtNCwxLjc5MS00LDR2MzAuMjVjMCwyLjIwOSwxLjc5MSw0LDQsNGgyNS44MzIgICAgYzIuMjA5LDAsNC0xLjc5MSw0LTRTNzYuMTA3LDQ3LjA4LDczLjg5OCw0Ny4wOHoiIGZpbGw9IiMzMzMzMzMiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                                        Há {{ timeElapsedString($task->last_update) }}
                                    </li>
                                    @if($task->volumes)
                                        <li>
                                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ3My44IDQ3My44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA0NzMuOCA0NzMuODsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiPgo8Zz4KCTxwYXRoIGQ9Ik00NTQuOCwxMTEuN2MwLTEuOC0wLjQtMy42LTEuMi01LjNjLTEuNi0zLjQtNC43LTUuNy04LjEtNi40TDI0MS44LDEuMmMtMy4zLTEuNi03LjItMS42LTEwLjUsMEwyNS42LDEwMC45ICAgYy00LDEuOS02LjYsNS45LTYuOCwxMC40djAuMWMwLDAuMSwwLDAuMiwwLDAuNFYzNjJjMCw0LjYsMi42LDguOCw2LjgsMTAuOGwyMDUuNyw5OS43YzAuMSwwLDAuMSwwLDAuMiwwLjEgICBjMC4zLDAuMSwwLjYsMC4yLDAuOSwwLjRjMC4xLDAsMC4yLDAuMSwwLjQsMC4xYzAuMywwLjEsMC42LDAuMiwwLjksMC4zYzAuMSwwLDAuMiwwLjEsMC4zLDAuMWMwLjMsMC4xLDAuNywwLjEsMSwwLjIgICBjMC4xLDAsMC4yLDAsMC4zLDBjMC40LDAsMC45LDAuMSwxLjMsMC4xYzAuNCwwLDAuOSwwLDEuMy0wLjFjMC4xLDAsMC4yLDAsMC4zLDBjMC4zLDAsMC43LTAuMSwxLTAuMmMwLjEsMCwwLjItMC4xLDAuMy0wLjEgICBjMC4zLTAuMSwwLjYtMC4yLDAuOS0wLjNjMC4xLDAsMC4yLTAuMSwwLjQtMC4xYzAuMy0wLjEsMC42LTAuMiwwLjktMC40YzAuMSwwLDAuMSwwLDAuMi0wLjFsMjA2LjMtMTAwYzQuMS0yLDYuOC02LjIsNi44LTEwLjggICBWMTEyQzQ1NC44LDExMS45LDQ1NC44LDExMS44LDQ1NC44LDExMS43eiBNMjM2LjUsMjUuM2wxNzguNCw4Ni41bC02NS43LDMxLjlMMTcwLjgsNTcuMkwyMzYuNSwyNS4zeiBNMjM2LjUsMTk4LjNMNTguMSwxMTEuOCAgIGw4NS4yLTQxLjNMMzIxLjcsMTU3TDIzNi41LDE5OC4zeiBNNDIuOCwxMzEuMWwxODEuNyw4OC4xdjIyMy4zTDQyLjgsMzU0LjRWMTMxLjF6IE0yNDguNSw0NDIuNVYyMTkuMmw4NS4zLTQxLjR2NTguNCAgIGMwLDYuNiw1LjQsMTIsMTIsMTJzMTItNS40LDEyLTEydi03MC4xbDczLTM1LjRWMzU0TDI0OC41LDQ0Mi41eiIgZmlsbD0iIzMzMzMzMyIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" style="margin-bottom: -3px;"/>
                                            {{ $task->volumes }} vol.
                                        </li>
                                    @endif

                                    @if($task->weight)
                                        <li>
                                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDYxMiA2MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDYxMiA2MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNNjEwLjQzNCw1MTIuNzE2bC05NS45ODgtMjk2LjY5MWMtNC4yNDQtMTMuMTE3LTE2LjQ1OS0yMi4wMDMtMzAuMjQ1LTIyLjAwM0gzODIuOTA0ICAgYzguMjExLTEzLjU2MywxMy4wMjgtMjkuMzk5LDEzLjAyOC00Ni4zNzljMC00OS41ODYtNDAuMzQ2LTg5LjkzMy04OS45MzMtODkuOTMzYy00OS41ODYsMC04OS45MzMsNDAuMzQ2LTg5LjkzMyw4OS45MzMgICBjMCwxNi45NzksNC44MTcsMzIuODE1LDEzLjAyOSw0Ni4zNzlIMTI3LjhjLTEzLjc4NiwwLTI2LjAwMSw4Ljg4Ni0zMC4yNDUsMjIuMDAzTDEuNTY3LDUxMi43MTYgICBjLTYuNjQzLDIwLjUzMSw4LjY2Niw0MS41NzMsMzAuMjQ1LDQxLjU3M2g1NDguMzc2QzYwMS43NjgsNTU0LjI5LDYxNy4wNzYsNTMzLjI0OCw2MTAuNDM0LDUxMi43MTZ6IE0yNTguNDUyLDE0Ny42NDMgICBjMC0yNi4yMjEsMjEuMzI3LTQ3LjU0OCw0Ny41NDgtNDcuNTQ4YzI2LjIyMSwwLDQ3LjU0OCwyMS4zMjcsNDcuNTQ4LDQ3LjU0OGMwLDIyLjcwNS0xNi4wMTUsNDEuNjgyLTM3LjMyNyw0Ni4zNzlIMjk1Ljc4ICAgQzI3NC40NjcsMTg5LjMyNiwyNTguNDUyLDE3MC4zNDgsMjU4LjQ1MiwxNDcuNjQzeiBNMjYzLjk2Miw0NTEuMDMybC0yNy40NzUtNTIuNzU2aC0xMy4wOTJ2NTIuNzU2aC0zMS44M1YzMjAuOTc4aDMxLjgzdjUwLjIwOCAgIGgxMy4wOTJsMjYuOTI3LTUwLjIwOGgzNC4xOThsLTM1LjExNyw2Mi4yMDV2MC4zN2wzNy4xMTYsNjcuNDc5SDI2My45NjJ6IE00MzEuMzY5LDQ1MS4wMzJoLTI2LjU1N3YtMy42NDQgICBjMC0yLjcyNiwwLjE3OC01LjQ1LDAuMTc4LTUuNDVoLTAuMzU1YzAsMC0xMi41NiwxMS4yNzEtMzQuMTk4LDExLjI3MWMtMzMuMjk0LDAtNjMuODUtMjQuOTEyLTYzLjg1LTY3LjQ3OSAgIGMwLTM3LjgyNywyOC41NTUtNjYuOTMxLDY4LjIxOS02Ni45MzFjMzMuMjgsMCw1MC4wMTcsMTcuMjcxLDUwLjAxNywxNy4yNzFsLTE1LjI4NSwyMy44MzFjMCwwLTEzLjI3MS0xMS45OTctMzEuNjUtMTEuOTk3ICAgYy0yNy4yODIsMC0zOC4zNzUsMTcuNDYyLTM4LjM3NSwzNi43M2MwLDI0Ljc1LDE3LjA5MiwzOS40NzIsMzcuMjk0LDM5LjQ3MmMxNS4yNywwLDI2LjM2Mi05LjQ0OSwyNi4zNjItOS40NDl2LTEwLjAxM2gtMTguMzY1ICAgdi0yNy4xMDRoNDYuNTY1TDQzMS4zNjksNDUxLjAzMkw0MzEuMzY5LDQ1MS4wMzJ6IiBmaWxsPSIjMzMzMzMzIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                                            {{ $task->weight }}kg
                                        </li>
                                    @endif
                                @endif
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="tab-content" id="tab-concluded" style="{{ $tab == 'tab-concluded' ? '' : 'display:none' }}">
        @if($tasksConcluded->isEmpty())
            <div class="filter-noresults" style="margin-top: 80px;">
                <h4>Nada há serviços concluidos.</h4>
            </div>
        @else
            <ul class="list w-100 m-0 p-0 pendings-list">
                @foreach($tasksConcluded as $task)
                    <li class="list-item concluded">
                        <span class="arrow-right">
                            {{ Form::open(['route' => ['mobile.pendings.update', $task->id], 'method' => 'post',  'class' => 'ajax-form']) }}
                            <input type="hidden" name="concluded" value="0"/>
                            <input type="hidden" name="readed" value="1"/>
                            <input type="hidden" name="tab_active" value="tab-concluded"/>
                            <button type="submit" class="btn-pending-action">Anular</button>
                            {{ Form::close() }}
                        </span>
                        <div class="recipient" data-toggle="alert" data-text="{{ $task->description . (($task->details && $task->description) ? '<hr/>' : '') . nl2br($task->details) }}<hr/><small>{{ $task->full_address }}</small>">
                            <b>{{ $task->name }}
                                @if($task->description)
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDUxMCA1MTAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMCA1MTA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8ZyBpZD0iaW5mbyI+CgkJPHBhdGggZD0iTTI1NSwwQzExNC43NSwwLDAsMTE0Ljc1LDAsMjU1czExNC43NSwyNTUsMjU1LDI1NXMyNTUtMTE0Ljc1LDI1NS0yNTVTMzk1LjI1LDAsMjU1LDB6IE0yODAuNSwzODIuNWgtNTF2LTE1M2g1MVYzODIuNXogICAgIE0yODAuNSwxNzguNWgtNTF2LTUxaDUxVjE3OC41eiIgZmlsbD0iIzAwNkRGMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                                @endif
                            </b>
                            <br/>
                            <ul class="attributes">
                                <li>
                                    <img style="margin-bottom: -2px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDk3LjE2IDk3LjE2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA5Ny4xNiA5Ny4xNjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00OC41OCwwQzIxLjc5MywwLDAsMjEuNzkzLDAsNDguNThzMjEuNzkzLDQ4LjU4LDQ4LjU4LDQ4LjU4czQ4LjU4LTIxLjc5Myw0OC41OC00OC41OFM3NS4zNjcsMCw0OC41OCwweiBNNDguNTgsODYuODIzICAgIGMtMjEuMDg3LDAtMzguMjQ0LTE3LjE1NS0zOC4yNDQtMzguMjQzUzI3LjQ5MywxMC4zMzcsNDguNTgsMTAuMzM3Uzg2LjgyNCwyNy40OTIsODYuODI0LDQ4LjU4UzY5LjY2Nyw4Ni44MjMsNDguNTgsODYuODIzeiIgZmlsbD0iIzMzMzMzMyIvPgoJCTxwYXRoIGQ9Ik03My44OTgsNDcuMDhINTIuMDY2VjIwLjgzYzAtMi4yMDktMS43OTEtNC00LTRjLTIuMjA5LDAtNCwxLjc5MS00LDR2MzAuMjVjMCwyLjIwOSwxLjc5MSw0LDQsNGgyNS44MzIgICAgYzIuMjA5LDAsNC0xLjc5MSw0LTRTNzYuMTA3LDQ3LjA4LDczLjg5OCw0Ny4wOHoiIGZpbGw9IiMzMzMzMzMiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                                    Há {{ timeElapsedString($task->last_update) }}
                                </li>
                                @if($task->volumes)
                                    <li>
                                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ3My44IDQ3My44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA0NzMuOCA0NzMuODsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiPgo8Zz4KCTxwYXRoIGQ9Ik00NTQuOCwxMTEuN2MwLTEuOC0wLjQtMy42LTEuMi01LjNjLTEuNi0zLjQtNC43LTUuNy04LjEtNi40TDI0MS44LDEuMmMtMy4zLTEuNi03LjItMS42LTEwLjUsMEwyNS42LDEwMC45ICAgYy00LDEuOS02LjYsNS45LTYuOCwxMC40djAuMWMwLDAuMSwwLDAuMiwwLDAuNFYzNjJjMCw0LjYsMi42LDguOCw2LjgsMTAuOGwyMDUuNyw5OS43YzAuMSwwLDAuMSwwLDAuMiwwLjEgICBjMC4zLDAuMSwwLjYsMC4yLDAuOSwwLjRjMC4xLDAsMC4yLDAuMSwwLjQsMC4xYzAuMywwLjEsMC42LDAuMiwwLjksMC4zYzAuMSwwLDAuMiwwLjEsMC4zLDAuMWMwLjMsMC4xLDAuNywwLjEsMSwwLjIgICBjMC4xLDAsMC4yLDAsMC4zLDBjMC40LDAsMC45LDAuMSwxLjMsMC4xYzAuNCwwLDAuOSwwLDEuMy0wLjFjMC4xLDAsMC4yLDAsMC4zLDBjMC4zLDAsMC43LTAuMSwxLTAuMmMwLjEsMCwwLjItMC4xLDAuMy0wLjEgICBjMC4zLTAuMSwwLjYtMC4yLDAuOS0wLjNjMC4xLDAsMC4yLTAuMSwwLjQtMC4xYzAuMy0wLjEsMC42LTAuMiwwLjktMC40YzAuMSwwLDAuMSwwLDAuMi0wLjFsMjA2LjMtMTAwYzQuMS0yLDYuOC02LjIsNi44LTEwLjggICBWMTEyQzQ1NC44LDExMS45LDQ1NC44LDExMS44LDQ1NC44LDExMS43eiBNMjM2LjUsMjUuM2wxNzguNCw4Ni41bC02NS43LDMxLjlMMTcwLjgsNTcuMkwyMzYuNSwyNS4zeiBNMjM2LjUsMTk4LjNMNTguMSwxMTEuOCAgIGw4NS4yLTQxLjNMMzIxLjcsMTU3TDIzNi41LDE5OC4zeiBNNDIuOCwxMzEuMWwxODEuNyw4OC4xdjIyMy4zTDQyLjgsMzU0LjRWMTMxLjF6IE0yNDguNSw0NDIuNVYyMTkuMmw4NS4zLTQxLjR2NTguNCAgIGMwLDYuNiw1LjQsMTIsMTIsMTJzMTItNS40LDEyLTEydi03MC4xbDczLTM1LjRWMzU0TDI0OC41LDQ0Mi41eiIgZmlsbD0iIzMzMzMzMyIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" style="margin-bottom: -3px;"/>
                                        {{ $task->volumes }} vol.
                                    </li>
                                @endif

                                @if($task->weight)
                                    <li>
                                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDYxMiA2MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDYxMiA2MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNNjEwLjQzNCw1MTIuNzE2bC05NS45ODgtMjk2LjY5MWMtNC4yNDQtMTMuMTE3LTE2LjQ1OS0yMi4wMDMtMzAuMjQ1LTIyLjAwM0gzODIuOTA0ICAgYzguMjExLTEzLjU2MywxMy4wMjgtMjkuMzk5LDEzLjAyOC00Ni4zNzljMC00OS41ODYtNDAuMzQ2LTg5LjkzMy04OS45MzMtODkuOTMzYy00OS41ODYsMC04OS45MzMsNDAuMzQ2LTg5LjkzMyw4OS45MzMgICBjMCwxNi45NzksNC44MTcsMzIuODE1LDEzLjAyOSw0Ni4zNzlIMTI3LjhjLTEzLjc4NiwwLTI2LjAwMSw4Ljg4Ni0zMC4yNDUsMjIuMDAzTDEuNTY3LDUxMi43MTYgICBjLTYuNjQzLDIwLjUzMSw4LjY2Niw0MS41NzMsMzAuMjQ1LDQxLjU3M2g1NDguMzc2QzYwMS43NjgsNTU0LjI5LDYxNy4wNzYsNTMzLjI0OCw2MTAuNDM0LDUxMi43MTZ6IE0yNTguNDUyLDE0Ny42NDMgICBjMC0yNi4yMjEsMjEuMzI3LTQ3LjU0OCw0Ny41NDgtNDcuNTQ4YzI2LjIyMSwwLDQ3LjU0OCwyMS4zMjcsNDcuNTQ4LDQ3LjU0OGMwLDIyLjcwNS0xNi4wMTUsNDEuNjgyLTM3LjMyNyw0Ni4zNzlIMjk1Ljc4ICAgQzI3NC40NjcsMTg5LjMyNiwyNTguNDUyLDE3MC4zNDgsMjU4LjQ1MiwxNDcuNjQzeiBNMjYzLjk2Miw0NTEuMDMybC0yNy40NzUtNTIuNzU2aC0xMy4wOTJ2NTIuNzU2aC0zMS44M1YzMjAuOTc4aDMxLjgzdjUwLjIwOCAgIGgxMy4wOTJsMjYuOTI3LTUwLjIwOGgzNC4xOThsLTM1LjExNyw2Mi4yMDV2MC4zN2wzNy4xMTYsNjcuNDc5SDI2My45NjJ6IE00MzEuMzY5LDQ1MS4wMzJoLTI2LjU1N3YtMy42NDQgICBjMC0yLjcyNiwwLjE3OC01LjQ1LDAuMTc4LTUuNDVoLTAuMzU1YzAsMC0xMi41NiwxMS4yNzEtMzQuMTk4LDExLjI3MWMtMzMuMjk0LDAtNjMuODUtMjQuOTEyLTYzLjg1LTY3LjQ3OSAgIGMwLTM3LjgyNywyOC41NTUtNjYuOTMxLDY4LjIxOS02Ni45MzFjMzMuMjgsMCw1MC4wMTcsMTcuMjcxLDUwLjAxNywxNy4yNzFsLTE1LjI4NSwyMy44MzFjMCwwLTEzLjI3MS0xMS45OTctMzEuNjUtMTEuOTk3ICAgYy0yNy4yODIsMC0zOC4zNzUsMTcuNDYyLTM4LjM3NSwzNi43M2MwLDI0Ljc1LDE3LjA5MiwzOS40NzIsMzcuMjk0LDM5LjQ3MmMxNS4yNywwLDI2LjM2Mi05LjQ0OSwyNi4zNjItOS40NDl2LTEwLjAxM2gtMTguMzY1ICAgdi0yNy4xMDRoNDYuNTY1TDQzMS4zNjksNDUxLjAzMkw0MzEuMzY5LDQ1MS4wMzJ6IiBmaWxsPSIjMzMzMzMzIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                                        {{ $task->weight }}kg
                                    </li>
                                @endif
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="tab-content" id="tab-operators" style="display: none">
        <ul class="list w-100 m-0 p-0 pendings-list">
            @foreach($tasksOperators as $operator => $tasks)
            <li class="list-ite">
                <div class="accordion">
                    <div class="accordion-header" data-toggle="accordion" data-target="#accordion-{{ str_slug($operator) }}">
                        {{ $operator ? $operator : 'Eu' }}
                        <span class="arrow">&#9660;</span>
                        <span class="counter">{{ count($tasks) }}</span>
                    </div>
                    <div class="accordion-content" id="accordion-{{ str_slug($operator) }}" style="display: none">
                        <ul>
                            @foreach($tasks as $task)
                            <li class="text-uppercase">{{ $task->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
            @endforeach
        </ul>
    </div>

</div>