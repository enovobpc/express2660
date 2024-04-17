@section('title')
    {{ config('app.name') }} |
@stop

@section('metatags')
    <meta name="description" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ trans('seo.og-image.url') }}">
    <meta property="og:image:width" content="{{ trans('seo.og-image.width') }}">
    <meta property="og:image:height" content="{{ trans('seo.og-image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
    <header>
        <div class="container">
            <div class="row">
                <div class="col-xs-7 col-sm-4">
                    <a href="{{ route('home.index') }}">
                        @if(File::exists(public_path(). '/assets/img/logo/logo.svg'))
                            <img src="{{ asset('assets/img/logo/logo.svg') }}" class="header-logo"/>
                        @elseif(File::exists(public_path(). '/assets/img/logo/logo_sm.png'))
                            <img src="{{ asset('assets/img/logo/logo_sm.png') }}" class="header-logo"/>
                        @else
                            <h4>{{ Setting::get('company_name') }}</h4>
                        @endif
                    </a>
                </div>
                <div class="col-xs-5 col-sm-8">
                    <ul class="list-inline pull-right">
                        <li>
                            <div class="btn-group btn-tracking pull-right" role="group">
                                <a href="{{ route('tracking.index') }}"class="btn">
                                    <img style="border: none;border-radius: 0;" class="h-60px" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00OTEuNzI5LDExMi45NzFMMjU5LjI2MSwwLjc0NWMtMi4wNjEtMC45OTQtNC40NjEtMC45OTQtNi41MjEsMEwyMC4yNzEsMTEyLjk3MWMtMi41OTIsMS4yNTEtNC4yMzksMy44NzYtNC4yMzksNi43NTQgICAgdjI3Mi41NDljMCwyLjg3OCwxLjY0Nyw1LjUwMyw0LjIzOSw2Ljc1NGwyMzIuNDY4LDExMi4yMjZjMS4wMywwLjQ5NywyLjE0NiwwLjc0NiwzLjI2MSwwLjc0NnMyLjIzLTAuMjQ5LDMuMjYxLTAuNzQ2ICAgIGwyMzIuNDY4LTExMi4yMjZjMi41OTItMS4yNTEsNC4yMzktMy44NzYsNC4yMzktNi43NTRWMTE5LjcyNkM0OTUuOTY4LDExNi44NDYsNDk0LjMyLDExNC4yMjMsNDkxLjcyOSwxMTIuOTcxeiBNMjU2LDE1LjgyOCAgICBsMjE1LjIxNywxMDMuODk3bC02Mi4zODcsMzAuMTE4Yy0wLjM5NS0wLjMwMS0wLjgxMi0wLjU3OS0xLjI3LTAuOEwxOTMuODA1LDQ1Ljg1M0wyNTYsMTUuODI4eiBNMTc2Ljg2Nyw1NC4zMzNsMjE0LjkwNCwxMDMuNzQ2ICAgIGwtNDQuMDE1LDIxLjI0OUwxMzIuOTQxLDc1LjYyNEwxNzYuODY3LDU0LjMzM3ogTTM5Ni43OTksMTcyLjMwN3Y3OC41NDZsLTQxLjExMywxOS44NDh2LTc4LjU0NkwzOTYuNzk5LDE3Mi4zMDd6ICAgICBNNDgwLjk2OCwzODcuNTY4TDI2My41LDQ5Mi41NVYyMzYuNjU4bDUxLjg3My0yNS4wNDJjMy43My0xLjgwMSw1LjI5NC02LjI4NCwzLjQ5My0xMC4wMTUgICAgYy0xLjgwMS0zLjcyOS02LjI4NC01LjI5NS0xMC4wMTUtMy40OTNMMjU2LDIyMy42MjNsLTIwLjc5Ni0xMC4wNGMtMy43MzEtMS44MDMtOC4yMTQtMC4yMzctMTAuMDE1LDMuNDkzICAgIGMtMS44MDEsMy43My0wLjIzNyw4LjIxNCwzLjQ5MywxMC4wMTVsMTkuODE4LDkuNTY3VjQ5Mi41NUwzMS4wMzIsMzg3LjU2NlYxMzEuNjc0bDE2NS42LDc5Ljk0NSAgICBjMS4wNTEsMC41MDgsMi4xNjIsMC43NDgsMy4yNTUsMC43NDhjMi43ODgsMCw1LjQ2Ni0xLjU2Miw2Ljc1OS00LjI0MWMxLjgwMS0zLjczLDAuMjM3LTguMjE0LTMuNDkzLTEwLjAxNWwtMTYyLjM3LTc4LjM4NiAgICBsNzQuNTA1LTM1Ljk2OEwzNDAuNTgyLDE5Mi41MmMwLjAzMywwLjA0NiwwLjA3LDAuMDg3LDAuMTA0LDAuMTMydjg5Ljk5OWMwLDIuNTgxLDEuMzI3LDQuOTgsMy41MTMsNi4zNTMgICAgYzEuMjE0LDAuNzYyLDIuNTk5LDEuMTQ3LDMuOTg4LDEuMTQ3YzEuMTEyLDAsMi4yMjctMC4yNDcsMy4yNi0wLjc0Nmw1Ni4xMTMtMjcuMDg5YzIuNTkyLTEuMjUxLDQuMjM5LTMuODc1LDQuMjM5LTYuNzU0di05MC40OTUgICAgbDY5LjE2OS0zMy4zOTJWMzg3LjU2OHoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik05Mi45MjYsMzU4LjQ3OUw1OC44MTEsMzQyLjAxYy0zLjczMi0xLjgwMy04LjIxNC0wLjIzNy0xMC4wMTUsMy40OTNjLTEuODAxLDMuNzMtMC4yMzcsOC4yMTQsMy40OTMsMTAuMDE1ICAgIGwzNC4xMTUsMTYuNDY5YzEuMDUxLDAuNTA4LDIuMTYyLDAuNzQ4LDMuMjU1LDAuNzQ4YzIuNzg4LDAsNS40NjYtMS41NjIsNi43NTktNC4yNDEgICAgQzk4LjIyLDM2NC43NjMsOTYuNjU2LDM2MC4yODEsOTIuOTI2LDM1OC40Nzl6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMTI0LjMyMywzMzguMDQybC02NS40NjUtMzEuNjA0Yy0zLjczMS0xLjgwMS04LjIxNC0wLjIzNy0xMC4wMTUsMy40OTRjLTEuOCwzLjczLTAuMjM2LDguMjE0LDMuNDk0LDEwLjAxNSAgICBsNjUuNDY1LDMxLjYwNGMxLjA1MSwwLjUwNywyLjE2MiwwLjc0OCwzLjI1NSwwLjc0OGMyLjc4OCwwLDUuNDY2LTEuNTYyLDYuNzU5LTQuMjQxICAgIEMxMjkuNjE3LDM0NC4zMjYsMTI4LjA1MywzMzkuODQyLDEyNC4zMjMsMzM4LjA0MnoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                                    <span class="title hidden-xs">Seguir Encomenda </span>
                                </a>
                            </div>
                        </li>
                        <li class="hidden-xs">
                            <div class="btn-group btn-tracking pull-right" role="group">
                                <a href="{{ route('account.register') }}"class="btn btn-sm btn-default">
                                    <img class="h-60px" style="opacity: 0.6" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNTUgNTUiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU1IDU1OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8cGF0aCBkPSJNNTUsMjcuNUM1NSwxMi4zMzcsNDIuNjYzLDAsMjcuNSwwUzAsMTIuMzM3LDAsMjcuNWMwLDguMDA5LDMuNDQ0LDE1LjIyOCw4LjkyNiwyMC4yNThsLTAuMDI2LDAuMDIzbDAuODkyLDAuNzUyDQoJYzAuMDU4LDAuMDQ5LDAuMTIxLDAuMDg5LDAuMTc5LDAuMTM3YzAuNDc0LDAuMzkzLDAuOTY1LDAuNzY2LDEuNDY1LDEuMTI3YzAuMTYyLDAuMTE3LDAuMzI0LDAuMjM0LDAuNDg5LDAuMzQ4DQoJYzAuNTM0LDAuMzY4LDEuMDgyLDAuNzE3LDEuNjQyLDEuMDQ4YzAuMTIyLDAuMDcyLDAuMjQ1LDAuMTQyLDAuMzY4LDAuMjEyYzAuNjEzLDAuMzQ5LDEuMjM5LDAuNjc4LDEuODgsMC45OA0KCWMwLjA0NywwLjAyMiwwLjA5NSwwLjA0MiwwLjE0MiwwLjA2NGMyLjA4OSwwLjk3MSw0LjMxOSwxLjY4NCw2LjY1MSwyLjEwNWMwLjA2MSwwLjAxMSwwLjEyMiwwLjAyMiwwLjE4NCwwLjAzMw0KCWMwLjcyNCwwLjEyNSwxLjQ1NiwwLjIyNSwyLjE5NywwLjI5MmMwLjA5LDAuMDA4LDAuMTgsMC4wMTMsMC4yNzEsMC4wMjFDMjUuOTk4LDU0Ljk2MSwyNi43NDQsNTUsMjcuNSw1NQ0KCWMwLjc0OSwwLDEuNDg4LTAuMDM5LDIuMjIyLTAuMDk4YzAuMDkzLTAuMDA4LDAuMTg2LTAuMDEzLDAuMjc5LTAuMDIxYzAuNzM1LTAuMDY3LDEuNDYxLTAuMTY0LDIuMTc4LTAuMjg3DQoJYzAuMDYyLTAuMDExLDAuMTI1LTAuMDIyLDAuMTg3LTAuMDM0YzIuMjk3LTAuNDEyLDQuNDk1LTEuMTA5LDYuNTU3LTIuMDU1YzAuMDc2LTAuMDM1LDAuMTUzLTAuMDY4LDAuMjI5LTAuMTA0DQoJYzAuNjE3LTAuMjksMS4yMi0wLjYwMywxLjgxMS0wLjkzNmMwLjE0Ny0wLjA4MywwLjI5My0wLjE2NywwLjQzOS0wLjI1M2MwLjUzOC0wLjMxNywxLjA2Ny0wLjY0OCwxLjU4MS0xDQoJYzAuMTg1LTAuMTI2LDAuMzY2LTAuMjU5LDAuNTQ5LTAuMzkxYzAuNDM5LTAuMzE2LDAuODctMC42NDIsMS4yODktMC45ODNjMC4wOTMtMC4wNzUsMC4xOTMtMC4xNCwwLjI4NC0wLjIxN2wwLjkxNS0wLjc2NA0KCWwtMC4wMjctMC4wMjNDNTEuNTIzLDQyLjgwMiw1NSwzNS41NSw1NSwyNy41eiBNMiwyNy41QzIsMTMuNDM5LDEzLjQzOSwyLDI3LjUsMlM1MywxMy40MzksNTMsMjcuNQ0KCWMwLDcuNTc3LTMuMzI1LDE0LjM4OS04LjU4OSwxOS4wNjNjLTAuMjk0LTAuMjAzLTAuNTktMC4zODUtMC44OTMtMC41MzdsLTguNDY3LTQuMjMzYy0wLjc2LTAuMzgtMS4yMzItMS4xNDQtMS4yMzItMS45OTN2LTIuOTU3DQoJYzAuMTk2LTAuMjQyLDAuNDAzLTAuNTE2LDAuNjE3LTAuODE3YzEuMDk2LTEuNTQ4LDEuOTc1LTMuMjcsMi42MTYtNS4xMjNjMS4yNjctMC42MDIsMi4wODUtMS44NjQsMi4wODUtMy4yODl2LTMuNTQ1DQoJYzAtMC44NjctMC4zMTgtMS43MDgtMC44ODctMi4zNjl2LTQuNjY3YzAuMDUyLTAuNTE5LDAuMjM2LTMuNDQ4LTEuODgzLTUuODY0QzM0LjUyNCw5LjA2NSwzMS41NDEsOCwyNy41LDgNCglzLTcuMDI0LDEuMDY1LTguODY3LDMuMTY4Yy0yLjExOSwyLjQxNi0xLjkzNSw1LjM0NS0xLjg4Myw1Ljg2NHY0LjY2N2MtMC41NjgsMC42NjEtMC44ODcsMS41MDItMC44ODcsMi4zNjl2My41NDUNCgljMCwxLjEwMSwwLjQ5NCwyLjEyOCwxLjM0LDIuODIxYzAuODEsMy4xNzMsMi40NzcsNS41NzUsMy4wOTMsNi4zODl2Mi44OTRjMCwwLjgxNi0wLjQ0NSwxLjU2Ni0xLjE2MiwxLjk1OGwtNy45MDcsNC4zMTMNCgljLTAuMjUyLDAuMTM3LTAuNTAyLDAuMjk3LTAuNzUyLDAuNDc2QzUuMjc2LDQxLjc5MiwyLDM1LjAyMiwyLDI3LjV6IE00Mi40NTksNDguMTMyYy0wLjM1LDAuMjU0LTAuNzA2LDAuNS0xLjA2NywwLjczNQ0KCWMtMC4xNjYsMC4xMDgtMC4zMzEsMC4yMTYtMC41LDAuMzIxYy0wLjQ3MiwwLjI5Mi0wLjk1MiwwLjU3LTEuNDQyLDAuODNjLTAuMTA4LDAuMDU3LTAuMjE3LDAuMTExLTAuMzI2LDAuMTY3DQoJYy0xLjEyNiwwLjU3Ny0yLjI5MSwxLjA3My0zLjQ4OCwxLjQ3NmMtMC4wNDIsMC4wMTQtMC4wODQsMC4wMjktMC4xMjcsMC4wNDNjLTAuNjI3LDAuMjA4LTEuMjYyLDAuMzkzLTEuOTA0LDAuNTUyDQoJYy0wLjAwMiwwLTAuMDA0LDAuMDAxLTAuMDA2LDAuMDAxYy0wLjY0OCwwLjE2LTEuMzA0LDAuMjkzLTEuOTY0LDAuNDAyYy0wLjAxOCwwLjAwMy0wLjAzNiwwLjAwNy0wLjA1NCwwLjAxDQoJYy0wLjYyMSwwLjEwMS0xLjI0NywwLjE3NC0xLjg3NSwwLjIyOWMtMC4xMTEsMC4wMS0wLjIyMiwwLjAxNy0wLjMzNCwwLjAyNUMyOC43NTEsNTIuOTcsMjguMTI3LDUzLDI3LjUsNTMNCgljLTAuNjM0LDAtMS4yNjYtMC4wMzEtMS44OTUtMC4wNzhjLTAuMTA5LTAuMDA4LTAuMjE4LTAuMDE1LTAuMzI2LTAuMDI1Yy0wLjYzNC0wLjA1Ni0xLjI2NS0wLjEzMS0xLjg5LTAuMjMzDQoJYy0wLjAyOC0wLjAwNS0wLjA1Ni0wLjAxLTAuMDg0LTAuMDE1Yy0xLjMyMi0wLjIyMS0yLjYyMy0wLjU0Ni0zLjg5LTAuOTcxYy0wLjAzOS0wLjAxMy0wLjA3OS0wLjAyNy0wLjExOC0wLjA0DQoJYy0wLjYyOS0wLjIxNC0xLjI1MS0wLjQ1MS0xLjg2Mi0wLjcxM2MtMC4wMDQtMC4wMDItMC4wMDktMC4wMDQtMC4wMTMtMC4wMDZjLTAuNTc4LTAuMjQ5LTEuMTQ1LTAuNTI1LTEuNzA1LTAuODE2DQoJYy0wLjA3My0wLjAzOC0wLjE0Ny0wLjA3NC0wLjIxOS0wLjExM2MtMC41MTEtMC4yNzMtMS4wMTEtMC41NjgtMS41MDQtMC44NzZjLTAuMTQ2LTAuMDkyLTAuMjkxLTAuMTg1LTAuNDM1LTAuMjc5DQoJYy0wLjQ1NC0wLjI5Ny0wLjkwMi0wLjYwNi0xLjMzOC0wLjkzM2MtMC4wNDUtMC4wMzQtMC4wODgtMC4wNy0wLjEzMy0wLjEwNGMwLjAzMi0wLjAxOCwwLjA2NC0wLjAzNiwwLjA5Ni0wLjA1NGw3LjkwNy00LjMxMw0KCWMxLjM2LTAuNzQyLDIuMjA1LTIuMTY1LDIuMjA1LTMuNzE0bC0wLjAwMS0zLjYwMmwtMC4yMy0wLjI3OGMtMC4wMjItMC4wMjUtMi4xODQtMi42NTUtMy4wMDEtNi4yMTZsLTAuMDkxLTAuMzk2bC0wLjM0MS0wLjIyMQ0KCWMtMC40ODEtMC4zMTEtMC43NjktMC44MzEtMC43NjktMS4zOTJ2LTMuNTQ1YzAtMC40NjUsMC4xOTctMC44OTgsMC41NTctMS4yMjNsMC4zMy0wLjI5OHYtNS41N2wtMC4wMDktMC4xMzENCgljLTAuMDAzLTAuMDI0LTAuMjk4LTIuNDI5LDEuMzk2LTQuMzZDMjEuNTgzLDEwLjgzNywyNC4wNjEsMTAsMjcuNSwxMGMzLjQyNiwwLDUuODk2LDAuODMsNy4zNDYsMi40NjYNCgljMS42OTIsMS45MTEsMS40MTUsNC4zNjEsMS40MTMsNC4zODFsLTAuMDA5LDUuNzAxbDAuMzMsMC4yOThjMC4zNTksMC4zMjQsMC41NTcsMC43NTgsMC41NTcsMS4yMjN2My41NDUNCgljMCwwLjcxMy0wLjQ4NSwxLjM2LTEuMTgxLDEuNTc1bC0wLjQ5NywwLjE1M2wtMC4xNiwwLjQ5NWMtMC41OSwxLjgzMy0xLjQzLDMuNTI2LTIuNDk2LDUuMDMyYy0wLjI2MiwwLjM3LTAuNTE3LDAuNjk4LTAuNzM2LDAuOTQ5DQoJbC0wLjI0OCwwLjI4M1YzOS44YzAsMS42MTIsMC44OTYsMy4wNjIsMi4zMzgsMy43ODJsOC40NjcsNC4yMzNjMC4wNTQsMC4wMjcsMC4xMDcsMC4wNTUsMC4xNiwwLjA4Mw0KCUM0Mi42NzcsNDcuOTc5LDQyLjU2Nyw0OC4wNTQsNDIuNDU5LDQ4LjEzMnoiLz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                                    <span class="title hidden-xs">Iniciar Sessão</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
<div class="container account-container">
    <div class="{{ Setting::get('account_signup_fast') ? 'col-xs-12 col-md-6 col-md-offset-3' : 'col-xs-12 col-md-10 col-md-offset-1' }}">
        <div class="card">
            <div class="card-body">
                <div class="content-right">
                    <h2 class="text-primary m-b-30 m-t-15 {{  Setting::get('account_signup_fast') ? 'text-center' : '' }}">{{ trans('account/global.word.create-new-account') }}</h2>
                    <div class="home-alerts">
                        @include('partials.alerts')
                    </div>
                    {{ Form::open(['route' => 'account.register.submit', 'method' => 'POST', 'autocomplete'=>'off']) }}

                    @if(Setting::get('account_signup_fast'))
                        <div class="row row-10">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('name', trans('account/global.word.name'), ['class' => 'control-label']) }}
                                    {!! tip('Este é o nome que vai ser apresentado no seu nome de login.') !!}
                                    {{ Form::text('name', null, ['class' => 'form-control input-sm', 'autofocus', 'required', 'maxlength' => 25]) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('type', trans('account/global.word.account-type'), ['class' => 'control-label']) }}
                                    {{ Form::select('type', trans('account/global.account-types'), null, ['class' => 'form-control select2', 'autofocus', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('country', trans('account/global.word.country'), ['class' => 'control-label']) }}
                                    {{ Form::select('country', trans('country'), null, ['class' => 'form-control select2', 'autofocus', 'required']) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('email', trans('account/global.word.email'), ['class' => 'control-label']) }}
                            {{ Form::text('email', null, ['class' => 'form-control email nospace lowercase', 'autofocus', 'required']) }}
                        </div>
                        <div class="form-group" style="position: relative">
                            {{ Form::label('password', trans('account/global.word.choose-password')) }}
                            <span class="btn-show-pass"><i class="far fa-eye"></i></span>
                            {{ Form::password('password', ['class' => 'form-control nospace', 'required', 'autofill' => 'false', 'autocomplete' => 'off']) }}
                        </div>
                    @else
                        <div class="row row-10">
                            <div class="col-sm-8" style="padding-right: 50px">
                                <h4>Dados de Expedição</h4>
                                <div class="row row-10">
                                    <div class="col-sm-9">
                                        <div class="form-group is-required">
                                            {{ Form::label('name', trans('account/global.word.sender-name'), ['class' => 'control-label']) }}
                                            {{ Form::text('name', null, ['class' => 'form-control input-sm', 'autofocus', 'required', 'maxlength' => 25]) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('vat', trans('account/global.word.tin-abrv.'.Setting::get('app_country')), ['class' => 'control-label']) }}
                                            {{ Form::text('vat', null, ['class' => 'form-control vat']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group is-required">
                                    {{ Form::label('address', trans('account/global.word.address'), ['class' => 'control-label']) }}
                                    {{ Form::text('address', null, ['class' => 'form-control input-sm', 'required']) }}
                                </div>
                                <div class="row row-10">
                                    <div class="col-sm-3">
                                        <div class="form-group is-required">
                                            {{ Form::label('zip_code', trans('account/global.word.zip_code'), ['class' => 'control-label']) }}
                                            {{ Form::text('zip_code', null, ['class' => 'form-control', 'required']) }}
                                        </div>
                                    </div>
                                    @if(in_array(Setting::get('app_country'), ['us', 'br']))
                                        <div class="col-sm-2" style="padding-left: 0">
                                            <div class="form-group">
                                                {{ Form::label('state', 'State') }}
                                                {{ Form::select('state', ['' => ''] + trans('districts_codes.districts.'.Setting::get('app_country')), null, ['class' => 'form-control select2']) }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="{{ in_array(Setting::get('app_country'), ['us', 'br']) ? 'col-sm-4' : 'col-sm-6' }}">
                                        <div class="form-group is-required">
                                            {{ Form::label('city', trans('account/global.word.city'), ['class' => 'control-label']) }}
                                            {{ Form::text('city', null, ['class' => 'form-control', 'required']) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group is-required">
                                            {{ Form::label('country', trans('account/global.word.country'), ['class' => 'control-label']) }}
                                            {{ Form::select('country', trans('country'), null, ['class' => 'form-control select2', 'autofocus', 'required']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>
                                    {{ Form::checkbox('billing_data', 1, true) }}
                                        Usar os mesmos dados para faturação
                                    </label>
                                </div>
                                <div class="billing-form" style="display: none">
                                    <h4>Dados para faturação</h4>
                                    <div class="form-group is-required">
                                        {{ Form::label('billing_name', trans('account/global.word.social-designation'), ['class' => 'control-label']) }}
                                        {{ Form::text('billing_name', null, ['class' => 'form-control input-sm']) }}
                                    </div>
                                    <div class="form-group is-required">
                                        {{ Form::label('billing_address', trans('account/global.word.address'), ['class' => 'control-label']) }}
                                        {{ Form::text('billing_address', null, ['class' => 'form-control input-sm']) }}
                                    </div>
                                    <div class="row row-10">
                                        <div class="col-sm-3">
                                            <div class="form-group is-required">
                                                {{ Form::label('billing_zip_code', trans('account/global.word.zip_code'), ['class' => 'control-label']) }}
                                                {{ Form::text('billing_zip_code', null, ['class' => 'form-control']) }}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group is-required">
                                                {{ Form::label('billing_city', trans('account/global.word.city'), ['class' => 'control-label']) }}
                                                {{ Form::text('billing_city', null, ['class' => 'form-control']) }}
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group is-required">
                                                {{ Form::label('billing_country', trans('account/global.word.country'), ['class' => 'control-label']) }}
                                                {{ Form::select('billing_country', trans('country'), null, ['class' => 'form-control select2', 'autofocus']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <h4>Dados de Contacto</h4>
                                {{--<div class="form-group">
                                    {{ Form::label('type', trans('account/global.word.account-type'), ['class' => 'control-label']) }}
                                    <br/>
                                    {{ Form::select('type', trans('account/global.account-types'), null, ['class' => 'form-control select2', 'autofocus', 'required']) }}
                                </div>--}}
                                <div class="form-group is-required">
                                    {{ Form::label('contact_email', trans('account/global.word.contact-email'), ['class' => 'control-label']) }}
                                    {{ Form::text('contact_email', null, ['class' => 'form-control email nospace lowercase', 'autofocus', 'required']) }}
                                </div>
                                <div class="form-group is-required">
                                    {{ Form::label('mobile', trans('account/global.word.mobile'), ['class' => 'control-label']) }}
                                    {{ Form::text('mobile', null, ['class' => 'form-control phone', 'required']) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('phone', trans('account/global.word.phone'), ['class' => 'control-label']) }}
                                    {{ Form::text('phone', null, ['class' => 'form-control mobile']) }}
                                </div>
                            </div>
                        </div>

                        <hr style="margin: 5px 0 15px"/>
                        <div class="row row-10">
                            <div class="col-sm-5 col-md-6">
                                <div class="form-group is-required">
                                    {{ Form::label('email', trans('account/global.word.login-email'), ['class' => 'control-label']) }}
                                    {{ Form::text('email', null, ['class' => 'form-control email nospace lowercase', 'autofocus', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group is-required" style="position: relative">
                                    {{ Form::label('password', trans('account/global.word.choose-password')) }}
                                    <span class="btn-show-pass"><i class="far fa-eye"></i></span>
                                    {{ Form::password('password', ['class' => 'form-control nospace', 'required', 'autofill' => 'false', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-2">
                                <div class="form-group is-required">
                                    {{ Form::label('type', trans('account/global.word.account-type'), ['class' => 'control-label']) }}
                                    {{ Form::select('type', trans('account/global.account-types'), null, ['class' => 'form-control select2', 'autofocus', 'required']) }}
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row row-0">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 0">
                                    <label style="padding: 0; margin-bottom: 5px;">
                                        <input type="checkbox" name="accept" required> Concordo com os <a href="{{ route('legal.show', 'condicoes-uso') }}" target="_blank">termos e condições</a>  e com a <a href="{{ route('legal.show', 'politica-privacidade') }}" target="_blank">política de privacidade</a>.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-lg btn-block btn-primary">{{ trans('account/global.word.signup') }}</button>
                        {{--<div class="spacer-15"></div>
                        <div class="text-center">
                            <p class="m-b-5">{{ trans('account/global.word.already-have-account') }}</p>
                            <a href="{{ route('account.login') }}" class="p-b-10 btn btn-xs btn-black">
                                {{ trans('account/global.word.login') }}
                            </a>
                        </div>--}}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        @if(!env('APP_HIDE_CREDITS'))
            <p class="credit text-center">
                {!! app_brand() !!}
            </p>
        @endif
    </div>
    @include('auth.passwords.email')
</div>
    <style>
        .select2-container .select2-selection--single {
            height: 39px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 37px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin-top: 0px;
        }

        @if(!Setting::get('account_signup_fast'))
        .login-simple .card {
            margin-top: 2vh;
        }
        @endif
    </style>
@stop

@section('scripts')
    <script>

        $(document).on('change', '[name="name"]', function(){
            if($(this).val().length < 3) {
                var html = '<small class="feedback text-red">O nome deve ter pelo menos 3 caracteres.</small>';
                $('[name="name"]').closest('.form-group').addClass('has-error');
                if(!$('[name="name"]').closest('.form-group').find('.feedback').length) {
                    $('[name="name"]').closest('.form-group').append(html)
                }
            } else {
                $('[name="name"]').closest('.form-group').removeClass('has-error')
                $('[name="name"]').closest('.form-group').find('.feedback').remove();
            }
        })

        $(document).on('change keyup', '[name="password"]', function(){
            var value    = $(this).val();
            var $target  = $('[name="password"]').closest('.form-group');
            var isSecure = validateSecurePassword(value);

            if(value.length < 6) {
                var html = '<small class="feedback text-red">A palavra-passe deve ter pelo menos 6 caracteres.</small>';
                $target.addClass('has-error');
                if($target.find('.feedback').length) {
                    $target.find('.feedback').html(html)
                } else {
                    $target.append(html)
                }
            } else if(!isSecure) {
                var html = '<small class="feedback text-red">A password não é segura. Deve usar letras maiusculas, minúsculas e números.</small>';
                $target.addClass('has-error');
                if($target.find('.feedback').length) {
                    $target.find('.feedback').html(html)
                } else {
                    $target.append(html)
                }
            } else {
                $target.removeClass('has-error')
                $target.find('.feedback').remove();
            }
        })


        function validateSecurePassword(inputtxt)
        {
            var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
            if(inputtxt.match(passw)) {
                return true;
            } else {
                return false;
            }
        }

        $('[name="billing_data"]').on('change', function () {

            if($(this).is(':checked')) {
                $('.billing-form').hide();
                $('.billing-form').find('input, select').val('').prop('required', false).trigger('change');
            } else {
                $('.billing-form').show();
                $('.billing-form').find('input, select').val('').prop('required', true).trigger('change');
            }

        })

    </script>
@stop