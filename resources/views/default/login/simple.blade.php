@extends('layouts.default')

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
                                {{--<button type="button" class="btn btn-user dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ asset('assets/img/default/avatar.png') }}"/>
                                    <span class="username hidden-xs m-t-7">
                                    Iniciar Sessão<br/>
                                </span>
                                </button>--}}
                            </div>
                        </li>
                        @if(hasModule('account_signup') && Setting::get('account_signup'))
                        <li class="hidden-xs">
                            <div class="btn-group btn-tracking pull-right" role="group">
                                <a href="{{ route('account.register') }}"class="btn btn-sm btn-default">
                                    <img class="h-60px" style="opacity: 0.6" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNTUgNTUiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU1IDU1OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8cGF0aCBkPSJNNTUsMjcuNUM1NSwxMi4zMzcsNDIuNjYzLDAsMjcuNSwwUzAsMTIuMzM3LDAsMjcuNWMwLDguMDA5LDMuNDQ0LDE1LjIyOCw4LjkyNiwyMC4yNThsLTAuMDI2LDAuMDIzbDAuODkyLDAuNzUyDQoJYzAuMDU4LDAuMDQ5LDAuMTIxLDAuMDg5LDAuMTc5LDAuMTM3YzAuNDc0LDAuMzkzLDAuOTY1LDAuNzY2LDEuNDY1LDEuMTI3YzAuMTYyLDAuMTE3LDAuMzI0LDAuMjM0LDAuNDg5LDAuMzQ4DQoJYzAuNTM0LDAuMzY4LDEuMDgyLDAuNzE3LDEuNjQyLDEuMDQ4YzAuMTIyLDAuMDcyLDAuMjQ1LDAuMTQyLDAuMzY4LDAuMjEyYzAuNjEzLDAuMzQ5LDEuMjM5LDAuNjc4LDEuODgsMC45OA0KCWMwLjA0NywwLjAyMiwwLjA5NSwwLjA0MiwwLjE0MiwwLjA2NGMyLjA4OSwwLjk3MSw0LjMxOSwxLjY4NCw2LjY1MSwyLjEwNWMwLjA2MSwwLjAxMSwwLjEyMiwwLjAyMiwwLjE4NCwwLjAzMw0KCWMwLjcyNCwwLjEyNSwxLjQ1NiwwLjIyNSwyLjE5NywwLjI5MmMwLjA5LDAuMDA4LDAuMTgsMC4wMTMsMC4yNzEsMC4wMjFDMjUuOTk4LDU0Ljk2MSwyNi43NDQsNTUsMjcuNSw1NQ0KCWMwLjc0OSwwLDEuNDg4LTAuMDM5LDIuMjIyLTAuMDk4YzAuMDkzLTAuMDA4LDAuMTg2LTAuMDEzLDAuMjc5LTAuMDIxYzAuNzM1LTAuMDY3LDEuNDYxLTAuMTY0LDIuMTc4LTAuMjg3DQoJYzAuMDYyLTAuMDExLDAuMTI1LTAuMDIyLDAuMTg3LTAuMDM0YzIuMjk3LTAuNDEyLDQuNDk1LTEuMTA5LDYuNTU3LTIuMDU1YzAuMDc2LTAuMDM1LDAuMTUzLTAuMDY4LDAuMjI5LTAuMTA0DQoJYzAuNjE3LTAuMjksMS4yMi0wLjYwMywxLjgxMS0wLjkzNmMwLjE0Ny0wLjA4MywwLjI5My0wLjE2NywwLjQzOS0wLjI1M2MwLjUzOC0wLjMxNywxLjA2Ny0wLjY0OCwxLjU4MS0xDQoJYzAuMTg1LTAuMTI2LDAuMzY2LTAuMjU5LDAuNTQ5LTAuMzkxYzAuNDM5LTAuMzE2LDAuODctMC42NDIsMS4yODktMC45ODNjMC4wOTMtMC4wNzUsMC4xOTMtMC4xNCwwLjI4NC0wLjIxN2wwLjkxNS0wLjc2NA0KCWwtMC4wMjctMC4wMjNDNTEuNTIzLDQyLjgwMiw1NSwzNS41NSw1NSwyNy41eiBNMiwyNy41QzIsMTMuNDM5LDEzLjQzOSwyLDI3LjUsMlM1MywxMy40MzksNTMsMjcuNQ0KCWMwLDcuNTc3LTMuMzI1LDE0LjM4OS04LjU4OSwxOS4wNjNjLTAuMjk0LTAuMjAzLTAuNTktMC4zODUtMC44OTMtMC41MzdsLTguNDY3LTQuMjMzYy0wLjc2LTAuMzgtMS4yMzItMS4xNDQtMS4yMzItMS45OTN2LTIuOTU3DQoJYzAuMTk2LTAuMjQyLDAuNDAzLTAuNTE2LDAuNjE3LTAuODE3YzEuMDk2LTEuNTQ4LDEuOTc1LTMuMjcsMi42MTYtNS4xMjNjMS4yNjctMC42MDIsMi4wODUtMS44NjQsMi4wODUtMy4yODl2LTMuNTQ1DQoJYzAtMC44NjctMC4zMTgtMS43MDgtMC44ODctMi4zNjl2LTQuNjY3YzAuMDUyLTAuNTE5LDAuMjM2LTMuNDQ4LTEuODgzLTUuODY0QzM0LjUyNCw5LjA2NSwzMS41NDEsOCwyNy41LDgNCglzLTcuMDI0LDEuMDY1LTguODY3LDMuMTY4Yy0yLjExOSwyLjQxNi0xLjkzNSw1LjM0NS0xLjg4Myw1Ljg2NHY0LjY2N2MtMC41NjgsMC42NjEtMC44ODcsMS41MDItMC44ODcsMi4zNjl2My41NDUNCgljMCwxLjEwMSwwLjQ5NCwyLjEyOCwxLjM0LDIuODIxYzAuODEsMy4xNzMsMi40NzcsNS41NzUsMy4wOTMsNi4zODl2Mi44OTRjMCwwLjgxNi0wLjQ0NSwxLjU2Ni0xLjE2MiwxLjk1OGwtNy45MDcsNC4zMTMNCgljLTAuMjUyLDAuMTM3LTAuNTAyLDAuMjk3LTAuNzUyLDAuNDc2QzUuMjc2LDQxLjc5MiwyLDM1LjAyMiwyLDI3LjV6IE00Mi40NTksNDguMTMyYy0wLjM1LDAuMjU0LTAuNzA2LDAuNS0xLjA2NywwLjczNQ0KCWMtMC4xNjYsMC4xMDgtMC4zMzEsMC4yMTYtMC41LDAuMzIxYy0wLjQ3MiwwLjI5Mi0wLjk1MiwwLjU3LTEuNDQyLDAuODNjLTAuMTA4LDAuMDU3LTAuMjE3LDAuMTExLTAuMzI2LDAuMTY3DQoJYy0xLjEyNiwwLjU3Ny0yLjI5MSwxLjA3My0zLjQ4OCwxLjQ3NmMtMC4wNDIsMC4wMTQtMC4wODQsMC4wMjktMC4xMjcsMC4wNDNjLTAuNjI3LDAuMjA4LTEuMjYyLDAuMzkzLTEuOTA0LDAuNTUyDQoJYy0wLjAwMiwwLTAuMDA0LDAuMDAxLTAuMDA2LDAuMDAxYy0wLjY0OCwwLjE2LTEuMzA0LDAuMjkzLTEuOTY0LDAuNDAyYy0wLjAxOCwwLjAwMy0wLjAzNiwwLjAwNy0wLjA1NCwwLjAxDQoJYy0wLjYyMSwwLjEwMS0xLjI0NywwLjE3NC0xLjg3NSwwLjIyOWMtMC4xMTEsMC4wMS0wLjIyMiwwLjAxNy0wLjMzNCwwLjAyNUMyOC43NTEsNTIuOTcsMjguMTI3LDUzLDI3LjUsNTMNCgljLTAuNjM0LDAtMS4yNjYtMC4wMzEtMS44OTUtMC4wNzhjLTAuMTA5LTAuMDA4LTAuMjE4LTAuMDE1LTAuMzI2LTAuMDI1Yy0wLjYzNC0wLjA1Ni0xLjI2NS0wLjEzMS0xLjg5LTAuMjMzDQoJYy0wLjAyOC0wLjAwNS0wLjA1Ni0wLjAxLTAuMDg0LTAuMDE1Yy0xLjMyMi0wLjIyMS0yLjYyMy0wLjU0Ni0zLjg5LTAuOTcxYy0wLjAzOS0wLjAxMy0wLjA3OS0wLjAyNy0wLjExOC0wLjA0DQoJYy0wLjYyOS0wLjIxNC0xLjI1MS0wLjQ1MS0xLjg2Mi0wLjcxM2MtMC4wMDQtMC4wMDItMC4wMDktMC4wMDQtMC4wMTMtMC4wMDZjLTAuNTc4LTAuMjQ5LTEuMTQ1LTAuNTI1LTEuNzA1LTAuODE2DQoJYy0wLjA3My0wLjAzOC0wLjE0Ny0wLjA3NC0wLjIxOS0wLjExM2MtMC41MTEtMC4yNzMtMS4wMTEtMC41NjgtMS41MDQtMC44NzZjLTAuMTQ2LTAuMDkyLTAuMjkxLTAuMTg1LTAuNDM1LTAuMjc5DQoJYy0wLjQ1NC0wLjI5Ny0wLjkwMi0wLjYwNi0xLjMzOC0wLjkzM2MtMC4wNDUtMC4wMzQtMC4wODgtMC4wNy0wLjEzMy0wLjEwNGMwLjAzMi0wLjAxOCwwLjA2NC0wLjAzNiwwLjA5Ni0wLjA1NGw3LjkwNy00LjMxMw0KCWMxLjM2LTAuNzQyLDIuMjA1LTIuMTY1LDIuMjA1LTMuNzE0bC0wLjAwMS0zLjYwMmwtMC4yMy0wLjI3OGMtMC4wMjItMC4wMjUtMi4xODQtMi42NTUtMy4wMDEtNi4yMTZsLTAuMDkxLTAuMzk2bC0wLjM0MS0wLjIyMQ0KCWMtMC40ODEtMC4zMTEtMC43NjktMC44MzEtMC43NjktMS4zOTJ2LTMuNTQ1YzAtMC40NjUsMC4xOTctMC44OTgsMC41NTctMS4yMjNsMC4zMy0wLjI5OHYtNS41N2wtMC4wMDktMC4xMzENCgljLTAuMDAzLTAuMDI0LTAuMjk4LTIuNDI5LDEuMzk2LTQuMzZDMjEuNTgzLDEwLjgzNywyNC4wNjEsMTAsMjcuNSwxMGMzLjQyNiwwLDUuODk2LDAuODMsNy4zNDYsMi40NjYNCgljMS42OTIsMS45MTEsMS40MTUsNC4zNjEsMS40MTMsNC4zODFsLTAuMDA5LDUuNzAxbDAuMzMsMC4yOThjMC4zNTksMC4zMjQsMC41NTcsMC43NTgsMC41NTcsMS4yMjN2My41NDUNCgljMCwwLjcxMy0wLjQ4NSwxLjM2LTEuMTgxLDEuNTc1bC0wLjQ5NywwLjE1M2wtMC4xNiwwLjQ5NWMtMC41OSwxLjgzMy0xLjQzLDMuNTI2LTIuNDk2LDUuMDMyYy0wLjI2MiwwLjM3LTAuNTE3LDAuNjk4LTAuNzM2LDAuOTQ5DQoJbC0wLjI0OCwwLjI4M1YzOS44YzAsMS42MTIsMC44OTYsMy4wNjIsMi4zMzgsMy43ODJsOC40NjcsNC4yMzNjMC4wNTQsMC4wMjcsMC4xMDcsMC4wNTUsMC4xNiwwLjA4Mw0KCUM0Mi42NzcsNDcuOTc5LDQyLjU2Nyw0OC4wNTQsNDIuNDU5LDQ4LjEzMnoiLz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
                                    <span class="title hidden-xs">{{ trans('account/global.word.register') }}</span>
                                </a>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </header>
<div class="container account-container">
    @if(config('app.source') == 'lojadostransportes')
        <div class="col-xs-12 col-md-6 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="content-right">
                        <h2 class="text-primary m-b-30 m-t-15">Cálculo de Cotação</h2>
                        <div class="col-lg-9">
                            <div class="row row-5">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        {{ Form::label('budget_sender_zip_code',  trans('account/global.word.sender-zp'), ['class' => 'control-label']) }}
                                        {{ Form::text('budget_sender_zip_code', null, ['class' => 'form-control input-sm']) }}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group is-required">
                                        {{ Form::label('budget_sender_country', trans('account/global.word.sender-country'), ['class' => 'control-label']) }}
                                        {{ Form::select('budget_sender_country', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2', 'required']) }}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        {{ Form::label('budget_recipient_zip_code', trans('account/global.word.recipient-zp'), ['class' => 'control-label']) }}
                                        {{ Form::text('budget_recipient_zip_code', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group is-required">
                                        {{ Form::label('budget_recipient_country', trans('account/global.word.recipient-country'), ['class' => 'control-label']) }}
                                        {{ Form::select('budget_recipient_country', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2', 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row row-5">
                                <div class="col-sm-4">
                                    <div class="form-group is-required">
                                        {{ Form::label('budget_service', trans('account/global.word.service'), ['class' => 'control-label']) }}<br/>
                                        {!! Form::select('budget_service', $services, null, ['class' => 'form-control select2', 'required'])!!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group is-required"
                                         data-toggle="tooltip"
                                         title="{{ trans('account/shipments.budget.tips.volumes') }}">
                                        {{ Form::label('budget_volumes', trans('account/global.word.volumes'), ['class' => 'control-label']) }}
                                        {{ Form::text('budget_volumes', null, ['class' => 'form-control number', 'required']) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group is-required"
                                         data-toggle="tooltip"
                                         title="{{ trans('account/shipments.budget.tips.weight') }}">
                                        {{ Form::label('budget_weight', trans('account/global.word.weight'), ['class' => 'control-label']) }}
                                        <div class="input-group">
                                            {{ Form::text('budget_weight', null, ['class' => 'form-control decimal']) }}
                                            <span class="input-group-addon">kg</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="display: none">
                                    <div class="form-group is-required"
                                         data-toggle="tooltip"
                                         title="{{ trans('account/shipments.budget.tips.kms') }}">
                                        {{ Form::label('budget_kms', trans('account/global.word.distance').' (km)', ['class' => 'control-label']) }}
                                        <div class="input-group">
                                            {{ Form::text('budget_kms', null, ['class' => 'form-control decimal']) }}
                                            <span class="input-group-addon">km</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group"
                                         data-toggle="tooltip"
                                         title="{{ trans('account/shipments.budget.tips.weight-vol') }}">
                                        {{ Form::label('budget_volumetric_weight', trans('account/global.word.volumetric-weight'), ['class' => 'control-label']) }}
                                        <div class="input-group">
                                            {{ Form::text('budget_volumetric_weight', null, ['class' => 'form-control', 'required', 'readonly']) }}
                                            <div class="input-group-addon"  data-toggle="modal" data-target="#modal-budget-dimensions">
                                                kg <i class="fas fa-external-link-square-alt"></i>
                                            </div>
                                        </div>
                                        {{ Form::hidden('budget_fator_m3') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <h4 class="m-t-0">Serviços Adicionais</h4>
                            <div class="row row-5">
                                <div class="col-sm-12">
                                    <div class="form-group m-b-0">
                                        <div class="checkbox m-b-0">
                                            <label style="padding-left: 0">
                                                {{ Form::checkbox('budget_pickup', '1') }} {{ trans('account/shipments.budget.addicional-services.pickup') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group m-b-0">
                                        <div class="checkbox m-b-0">
                                            <label style="padding-left: 0">
                                                {{ Form::checkbox('budget_charge', '1') }} {{ trans('account/shipments.budget.addicional-services.charge') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group m-b-0">
                                        <div class="checkbox m-b-0">
                                            <label style="padding-left: 0">
                                                {{ Form::checkbox('budget_rguide', '1') }} {{ trans('account/shipments.budget.addicional-services.rguide') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 hide">
                                    <div class="form-group m-b-0">
                                        <div class="checkbox m-b-0">
                                            <label style="padding-left: 0">
                                                {{ Form::checkbox('budget_outstandard', '1') }} VFN
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <hr/>
                            <div class="row">
                                <div class="col-sm-8">
                                    <h3 class="m-0 m-b-5 service-name">{{ @$minBudget['serviceName'] }}</h3>
                                    <p class="service-description">{{ @$minBudget['serviceDescription'] }}&nbsp;</p>

                                    <p class="bold m-t-30 text-red">É empresa? Preços especiais para clientes contratuais. {{ Setting::get('support_phone_1') }}</p>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <h1 class="m-0 m-b-5" style="margin-top: -5px; line-height: 20px;">
                                        <small class="fs-15" style="display: block; margin-bottom: 10px">Preço desde</small>
                                        <span class="budget-loading" style="display: none"><small><i class="fas fa-spin fa-circle-notch"></i></small></span>
                                        <span class="budget-total">{{ @$minBudget['priceVat'] }}</span>
                                        <div>
                                            <small class="fs-15" >IVA incluído.</small>
                                        </div>
                                    </h1>
                                    <a href="{{ route('account.register') }}" class="btn btn-xs btn-black m-t-5">Solicitar transporte <i class="fas fa-angle-right"></i></a>
                                    {{--<hr class="m-t-15 m-b-5 hide table-budget-details"/>
                                    <table class="table-condensed w-100 fs-13 hide table-budget-details">
                                        <tr class="hide base">
                                            <td class="text-right">{{ trans('account/shipments.budget.price-overview.base') }}&nbsp;&nbsp;</td>
                                            <td class="text-left bold w-90px"></td>
                                        </tr>
                                        <tr class="hide pickup">
                                            <td class="text-right">{{ trans('account/shipments.budget.price-overview.pickup') }}&nbsp;&nbsp;</td>
                                            <td class="text-left bold w-90px"></td>
                                        </tr>
                                        <tr class="hide charge">
                                            <td class="text-right">{{ trans('account/shipments.budget.price-overview.charge') }}&nbsp;&nbsp;</td>
                                            <td class="text-left bold w-90px"></td>
                                        </tr>
                                        <tr class="hide rguide">
                                            <td class="text-right">{{ trans('account/shipments.budget.price-overview.rguide') }}&nbsp;&nbsp;</td>
                                            <td class="text-left bold w-90px"></td>
                                        </tr>
                                        <tr class="hide fueltax">
                                            <td class="text-right">{{ trans('account/shipments.budget.price-overview.fuel') }}&nbsp;&nbsp;</td>
                                            <td class="text-left bold w-90px"></td>
                                        </tr>
                                        <tr class="hide outstandard">
                                            <td class="text-right">{{ trans('account/shipments.budget.price-overview.outstandard') }}&nbsp;&nbsp;</td>
                                            <td class="text-left bold w-90px"></td>
                                        </tr>
                                    </table>--}}
                                    {{--<hr class="m-t-5 m-b-5"/>
                                    <span>
                                        <small class="fs-10 lh-1-1" style="display: block">
                                            *<span class="helper-vat">{{ trans('account/shipments.budget.plus-vat-info') }}</span>
                                            <span class="helper-empty-vat" style="display: none;">{{ trans('account/shipments.budget.empty-vat-info') }}</span>
                                            <span class="helper-with-vat" style="display: none;">Cliente Particular - IVA Incluído</span>
                                            <span>{{ trans('account/shipments.budget.exceptions-info') }}</span>
                                        </small>
                                    </span>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('account.shipments.modals.budget_dimensions')
        <div class="col-xs-12 col-md-6 col-lg-4">
    @else
        <div class="col-xs-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
    @endif

        <div class="card">
            <div class="card-body">
                <div class="content-right">
                    <h2 class="text-primary text-center m-b-30 m-t-15">{{ trans('account/global.word.customer-portal') }}</h2>
                    <div class="home-alerts">
                        @include('partials.alerts')
                    </div>
                    {{ Form::open(['route' => 'account.login.submit', 'method' => 'POST']) }}
                    <div class="form-group">
                        {{ Form::label('email', trans('account/global.word.email'), ['class' => 'control-label']) }}
                        {{ Form::text('email', null, ['class' => 'form-control email nospace lowercase', 'autofocus', 'required']) }}
                    </div>
                    <div class="form-group" style="position: relative">
                        {{ Form::label('password', trans('account/global.word.password')) }}
                        <span class="btn-show-pass">
                            <i class="far fa-eye"></i>
                        </span>
                        {{ Form::password('password', ['class' => 'form-control nospace', 'required']) }}
                    </div>
                    <div class="row row-0">
                        <div class="col-xs-5">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 0">
                                    <label style="padding: 0; margin-bottom: 5px;">
                                        <input type="checkbox" name="remember"> {{ trans('auth.remember') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-7 col-sm-7">
                            @if(hasModule('account_signup'))
                            <a href="#" data-toggle="modal" data-target="#reset-password" class="reset-password">
                                <span class="hidden-xs">
                                    <i class="fas fa-lock"></i> {{ trans('auth.forgot.title') }}
                                </span>
                                <span class="visible-xs">
                                    <i class="fas fa-lock"></i> Recuperar Password
                                </span>
                            </a>
                            <div class="sp-15"></div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-lg btn-block btn-primary">{{ trans('account/global.word.login') }}</button>
                        <div class="spacer-15"></div>
                        @if(hasModule('account_signup'))
                        <div class="text-center">
                            <p class="m-b-5">Ainda não é cliente?</p>
                            <a href="{{ route('account.register') }}" class="p-b-10 btn btn-xs btn-black">
                                {{ trans('account/global.word.signup') }}
                            </a>
                        </div>
                        @else
                            <a href="#" data-toggle="modal" data-target="#reset-password" class="reset-password">
                                <span class="hidden-xs">
                                    <i class="fas fa-lock"></i> {{ trans('auth.forgot.title') }}
                                </span>
                            </a>
                        @endif
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
            height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 37px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 6px;
            right: 3px;
        }
    </style>
@stop

@section('scripts')
    <script>
        $('[name="budget_service"], [name="budget_sender_country"],[name="budget_recipient_country"],[name="budget_volumes"],[name="budget_weight"],[name="budget_kms"],[name="budget_fator_m3"],[name="budget_sender_zip_code"],[name="budget_recipient_zip_code"]').on('change', function(e) {
            calcBudget();
        });

        $('[name="budget_pickup"],[name="budget_charge"],[name="budget_rguide"],[name="budget_outstandard"]').on('change', function(e) {
            calcBudget();
        });

        $(document).on('change', '[name="budget_recipient_country"], [name="budget_sender_country"]', function(){
            var sender = $('[name="budget_sender_country"]').val();
            var recipient = $('[name="budget_recipient_country"]').val();

            if(sender == recipient) {
                $('.helper-vat').show();
                $('.helper-empty-vat').hide();
            } else {
                $('.helper-vat').hide();
                $('.helper-empty-vat').show();
            }
        })

        function calcBudget() {
            $('.budget-loading').show()
            $.post("{{ route('account.guest.budget') }}",
                {
                    service          : $('[name="budget_service"]').val(),
                    sender_country   : $('[name="budget_sender_country"]').val(),
                    recipient_country: $('[name="budget_recipient_country"]').val(),
                    sender_zip_code  : $('[name="budget_sender_zip_code"]').val(),
                    recipient_zip_code: $('[name="budget_recipient_zip_code"]').val(),
                    volumes          : $('[name="budget_volumes"]').val(),
                    weight           : $('[name="budget_weight"]').val(),
                    kms              : $('[name="budget_kms"]').val(),
                    fatorM3          : $('[name="budget_fator_m3"]').val(),
                    charge           : $('[name="budget_charge"]').is(':checked'),
                    pickup           : $('[name="budget_pickup"]').is(':checked'),
                    rguide           : $('[name="budget_rguide"]').is(':checked'),
                    outstandard      : $('[name="budget_outstandard"]').is(':checked'),
                }, function (data) {
                    $('.budget-total').html(data.priceVat)
                    //$('.budget-total').html(data.price)
                    if(data.is_particular) {
                        $('.budget-total').html(data.priceVat)
                        $('.helper-with-vat').show();
                        $('.helper-vat, .helper-empty-vat').hide();
                    }

                    $('[name="budget_volumetric_weight"]').val(data.volumetricWeight)
                    $('.service-name').html(data.serviceName)
                    $('.service-description').html(data.serviceDescription)

                    if(data.hasAdicionalService) {
                        $('.table-budget-details tr').addClass('hide');
                        $('.table-budget-details').removeClass('hide');
                        $('.budget-result').css('padding-top', '0');
                        $('.table-budget-details .base').removeClass('hide');
                        $('.table-budget-details .base').find('td:last-child').html(data.basePrice)

                        if(data.hasCharge) {
                            $('.table-budget-details .charge')
                                .removeClass('hide')
                                .find('td:last-child')
                                .html(data.charge)
                        }

                        if(data.hasOutStandard) {
                            $('.table-budget-details .outstandard')
                                .removeClass('hide')
                                .find('td:last-child')
                                .html(data.outStandard)
                        }

                        if(data.hasPickup) {
                            $('.table-budget-details .pickup')
                                .removeClass('hide')
                                .find('td:last-child')
                                .html(data.pickup)
                        }

                        if(data.hasRguide) {
                            $('.table-budget-details .rguide')
                                .removeClass('hide')
                                .find('td:last-child')
                                .html(data.rguide)
                        }

                        if(data.hasFuelTax) {
                            $('.table-budget-details .fueltax')
                                .removeClass('hide')
                                .find('td:last-child')
                                .html(data.fuelTax)
                        }

                        if(data.isParticular) {
                            $('.helper-vat').show();
                            $('.helper-empty-vat').hide();
                        }
                    } else {
                        $('.table-budget-details').addClass('hide');
                        $('.budget-result').css('padding-top', '40px')
                        $('.table-budget-details .base').addClass('hide');
                    }


                }).always(function(){
                $('.budget-loading').hide();
            })
        }

        $('[name="budget_service"]').on('change', function(e) {
            var unity = $(this).find(':selected').data('unity');

            if(unity == 'km') {
                $('[name="budget_kms"]').val('').closest('.form-group').parent().show()
                $('[name="budget_weight"]').val('').closest('.form-group').parent().hide()
            } else if(unity == 'm3') {
                $('[name="budget_kms"]').val('').closest('.form-group').parent().hide()
                $('[name="budget_weight"]').val('').closest('.form-group').parent().show()
                $('[name="budget_weight"]').closest('.form-group').find('label').html('{{ trans('account/global.word.volume') }} M3')
            } else {
                $('[name="budget_kms"]').val('').closest('.form-group').parent().hide()
                $('[name="budget_weight"]').val('').closest('.form-group').parent().show()
                $('[name="budget_weight"]').closest('.form-group').find('label').html('{{ trans('account/global.word.weight') }}')
            }
        });

        /**
         * DIMENSIONS
         */
        //hide dimensions modal
        $('.confirm-budget-dimensions').on('click', function(){
            $('#modal-budget-dimensions').modal('hide');
            var val;
            var fatorM3 = 0;
            var palletOutstandard = 0;
            var totalWeight     = 0;
            var weight          = $('[name="budget_weight"]').val();
            var volumes         = $('[name="budget_volumes"]').val();
            var maxDimension    = "{{ Setting::get('shipments_dimension_out_off_standard') }}";
            var maxWeight       = "{{ Setting::get('shipments_weight_out_off_standard') }}";
            var serviceUnity    = $('[name="budget_service"]').find(':selected').data('unity');

            $('#modal-budget-dimensions [name="budget_fator_m3_row[]"]').each(function(){
                val = $(this).val() == "" ? 0 : $(this).val();
                fatorM3+= parseFloat(val);

                var rowWeight = parseFloat($(this).closest('tr').find('[name="budget_weight[]"]').val());
                rowWeight = rowWeight == '' || isNaN(rowWeight) ? 0 : rowWeight;

                palletOutstandard+= rowWeight > 600 ? 1 : 0;
                totalWeight+= rowWeight;
            })

            $('[name="budget_outstandard"]').prop('checked', false);
            $('#modal-budget-dimensions [name="budget_ml_row[]"]').each(function(){
                val = $(this).val() == "" ? 0 : $(this).val();

                if(parseFloat(val) > parseFloat(maxDimension)) {
                    $('[name="budget_outstandard"]').prop('checked', true);
                }
            })

            if(serviceUnity == 'pallet') {
                if(parseFloat((weight/volumes)) > 600 || palletOutstandard) {
                    $('[name="budget_outstandard"]').prop('checked', true);
                }
            } else if(parseFloat(weight) > parseFloat(maxWeight)) {
                $('[name="budget_outstandard"]').prop('checked', true);
            }

            $('[name="budget_fator_m3"]').val(fatorM3).trigger('change');
            $('[name="budget_size"]').val(fatorM3).trigger('change');
            $('[name="budget_weight"]').val(totalWeight);
        })

        //change weight
        $('[name="budget_volumes"], [name="budget_weight"]').on('change', function(){
            var weight       = parseInt($('[name="budget_weight"]').val());
            var volumes      = parseInt($('[name="budget_volumes"]').val());
            var maxDimension = "{{ Setting::get('shipments_dimension_out_off_standard') }}";
            var maxWeight    = "{{ Setting::get('shipments_weight_out_off_standard') }}";
            var serviceUnity = $('[name="budget_service"]').find(':selected').data('unity');

            $('[name="budget_outstandard"]').prop('checked', false);

            if(serviceUnity == 'pallet') {
                if(parseFloat((weight/volumes)) > 600) {
                    $('[name="budget_outstandard"]').prop('checked', true);
                }
            } else if((volumes == 1 && parseFloat(weight) > parseFloat(maxWeight)) || ((parseFloat(weight)/volumes) > parseFloat(maxWeight))){
                $('[name="budget_outstandard"]').prop('checked', true);
            }

            $('#modal-budget-dimensions [name="budget_ml_row[]"]').each(function(){
                val = $(this).val() == "" ? 0 : $(this).val();

                if(parseFloat(val) > parseFloat(maxDimension)) {
                    $('[name="budget_outstandard"]').prop('checked', true);
                }
            })
        })

        //Change volumes
        $('[name=budget_volumes]').on('change', function(){
            var volumes = $(this).val();

            $('[name="budget_fator_m3"], [name="budget_volumetric_weight"]').val('');

            if (volumes != $('[name="budget_width[]"]').length) {
                $('table.budget-dimensions tr:gt(0)').html('');
                $('[name=budget_length], [name=budget_width],[name=budget_height], [name=budget_weight], [name=budget_fator_m3_row]').val("")
            }

            var i;
            for (i = 1; i <= volumes; i++) {
                var html = '<tr>';
                html += '<td>' + i + '</td>';
                html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="budget_width[]" type="text"><div class="input-group-addon">cm</div></div></td>';
                html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="budget_length[]" type="text"><div class="input-group-addon">cm</div></div></td>';
                html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="budget_height[]" type="text"><div class="input-group-addon">cm</div></div></td>';
                html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="budget_weight[]" type="text"><div class="input-group-addon">kg</div></div></td>';
                html += '<td style="display: none"><input class="form-control input-sm m-0" name="budget_fator_m3_row[]" type="text" readonly></td>';
                html += '<td><input class="form-control input-sm m-0" name="budget_ml_row[]" type="text" readonly></td>';
                html += '</tr>';

                $('table.budget-dimensions').append(html);
            }
        })

        $(document).on('change', '[name="budget_width[]"], [name="budget_height[]"], [name="budget_length[]"], [name="budget_weight[]"]', function(){
            var $tr = $(this).closest('tr');

            var width   = $tr.find('[name="budget_width[]"]').val();
            var height  = $tr.find('[name="budget_height[]"]').val();
            var length  = $tr.find('[name="budget_length[]"]').val();

            width  = width == "" ? 0 : width;
            length = length == "" ? 0 : length;
            height = height == "" ? 0 : height;

            var ml = parseFloat(width) + parseFloat(height) + parseFloat(length);

            if("{{ config('app.source') }}" == "entregaki") {
                var ml = parseFloat(width) + (2*parseFloat(height)) + (2*parseFloat(length)); //formula dos CTT
            }

            $tr.find('[name="budget_fator_m3_row[]"]').val(calcVolume(width, height, length));
            $tr.find('[name="budget_ml_row[]"]').val(ml);
        })

        function calcVolume(width, height, length) {
            var width  = width == "" ? 0 : width;
            var length = length == "" ? 0 : length;
            var height = height == "" ? 0 : height;
            return (parseFloat(width) * parseFloat(height) * parseFloat(length)) / 1000000;
        }
    </script>
@stop