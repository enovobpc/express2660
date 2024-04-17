@section('title')
    {{ trans('account/tracking.title') }} |
@stop

@section('metatags')
    <meta name="description" content="Seguimento de envio. Acompanhe o estado de entrega dos seus envios.">
    <meta property="og:title" content="{{ trans('account/tracking.title') }}">
    <meta property="og:description" content="Seguimento de envio. Acompanhe o estado de entrega dos seus envios.">
    <meta property="og:image" content="{{ trans('seo.og-image.url') }}">
    <meta property="og:image:width" content="{{ trans('seo.og-image.width') }}">
    <meta property="og:image:height" content="{{ trans('seo.og-image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
    <header>
        <div class="container">
            <div class="row">
                <div class="col-xs-9 col-sm-4">
                    <a href="{{ route('home.index') }}">
                        <img src="{{ asset('assets/img/logo/logo_sm.png') }}" class="header-logo"/>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-8">
                    <div class="btn-group btn-username pull-right" role="group">
                        <a href="{{ route('account.index') }}" class="btn btn-user">
                            <img src="{{ asset('assets/img/default/avatar.png') }}"/>
                            <span class="username hidden-xs m-t-7">
                                {{ trans('account/global.word.login') }}<br/>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section class="m-b-20">
        <div class="container account-container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="bold m-b-20">{{ trans('account/tracking.title') }}</h3>
                </div>
                <div class="col-xs-12">
                    <div class="card card-tracking m-b-0 p-20">
                        <div class="card-body">
                            <form method="GET" action="{{ route('tracking.index') }}" accept-charset="UTF-8">
                                @if(!empty($tracking))
                                <div class="row row-5">
                                    <div class="col-sm-3 col-md-2">
                                        <label class="m-t-10 pull-right fw-400 fs-15 hidden-xs">
                                            {{ trans('account/tracking.form.label') }}
                                            <i class="fas fa-question-circle text-blue"
                                               data-toggle="tooltip"
                                               title="{{ trans('account/tracking.form.tip') }}"></i>
                                        </label>
                                        <label class="visible-xs">
                                            {{ trans('account/tracking.form.label') }}
                                            <i class="fas fa-question-circle text-blue"
                                               data-toggle="tooltip"
                                               title="{{ trans('account/tracking.form.tip') }}"></i>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-8">
                                        <div class="form-group m-b-0">
                                            <input class="form-control nospace" value="{{ $tracking }}" placeholder="Ex. 001023900321, 005068105492, ..." name="tracking" type="text">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-md-2">
                                        <button type="submit" class="btn btn-block btn-primary">
                                            {{ trans('account/tracking.form.button') }}
                                        </button>
                                    </div>
                                </div>
                                @else
                                    <div class="row">
                                        <div class="col-xs-12 col-md-offset-3 col-md-6">
                                            <div class="spacer-30"></div>
                                            <div class="text-center">
                                                <img class="h-60px" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyIDUxMjsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIj48Zz48Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00OTEuNzI5LDExMi45NzFMMjU5LjI2MSwwLjc0NWMtMi4wNjEtMC45OTQtNC40NjEtMC45OTQtNi41MjEsMEwyMC4yNzEsMTEyLjk3MWMtMi41OTIsMS4yNTEtNC4yMzksMy44NzYtNC4yMzksNi43NTQgICAgdjI3Mi41NDljMCwyLjg3OCwxLjY0Nyw1LjUwMyw0LjIzOSw2Ljc1NGwyMzIuNDY4LDExMi4yMjZjMS4wMywwLjQ5NywyLjE0NiwwLjc0NiwzLjI2MSwwLjc0NnMyLjIzLTAuMjQ5LDMuMjYxLTAuNzQ2ICAgIGwyMzIuNDY4LTExMi4yMjZjMi41OTItMS4yNTEsNC4yMzktMy44NzYsNC4yMzktNi43NTRWMTE5LjcyNkM0OTUuOTY4LDExNi44NDYsNDk0LjMyLDExNC4yMjMsNDkxLjcyOSwxMTIuOTcxeiBNMjU2LDE1LjgyOCAgICBsMjE1LjIxNywxMDMuODk3bC02Mi4zODcsMzAuMTE4Yy0wLjM5NS0wLjMwMS0wLjgxMi0wLjU3OS0xLjI3LTAuOEwxOTMuODA1LDQ1Ljg1M0wyNTYsMTUuODI4eiBNMTc2Ljg2Nyw1NC4zMzNsMjE0LjkwNCwxMDMuNzQ2ICAgIGwtNDQuMDE1LDIxLjI0OUwxMzIuOTQxLDc1LjYyNEwxNzYuODY3LDU0LjMzM3ogTTM5Ni43OTksMTcyLjMwN3Y3OC41NDZsLTQxLjExMywxOS44NDh2LTc4LjU0NkwzOTYuNzk5LDE3Mi4zMDd6ICAgICBNNDgwLjk2OCwzODcuNTY4TDI2My41LDQ5Mi41NVYyMzYuNjU4bDUxLjg3My0yNS4wNDJjMy43My0xLjgwMSw1LjI5NC02LjI4NCwzLjQ5My0xMC4wMTUgICAgYy0xLjgwMS0zLjcyOS02LjI4NC01LjI5NS0xMC4wMTUtMy40OTNMMjU2LDIyMy42MjNsLTIwLjc5Ni0xMC4wNGMtMy43MzEtMS44MDMtOC4yMTQtMC4yMzctMTAuMDE1LDMuNDkzICAgIGMtMS44MDEsMy43My0wLjIzNyw4LjIxNCwzLjQ5MywxMC4wMTVsMTkuODE4LDkuNTY3VjQ5Mi41NUwzMS4wMzIsMzg3LjU2NlYxMzEuNjc0bDE2NS42LDc5Ljk0NSAgICBjMS4wNTEsMC41MDgsMi4xNjIsMC43NDgsMy4yNTUsMC43NDhjMi43ODgsMCw1LjQ2Ni0xLjU2Miw2Ljc1OS00LjI0MWMxLjgwMS0zLjczLDAuMjM3LTguMjE0LTMuNDkzLTEwLjAxNWwtMTYyLjM3LTc4LjM4NiAgICBsNzQuNTA1LTM1Ljk2OEwzNDAuNTgyLDE5Mi41MmMwLjAzMywwLjA0NiwwLjA3LDAuMDg3LDAuMTA0LDAuMTMydjg5Ljk5OWMwLDIuNTgxLDEuMzI3LDQuOTgsMy41MTMsNi4zNTMgICAgYzEuMjE0LDAuNzYyLDIuNTk5LDEuMTQ3LDMuOTg4LDEuMTQ3YzEuMTEyLDAsMi4yMjctMC4yNDcsMy4yNi0wLjc0Nmw1Ni4xMTMtMjcuMDg5YzIuNTkyLTEuMjUxLDQuMjM5LTMuODc1LDQuMjM5LTYuNzU0di05MC40OTUgICAgbDY5LjE2OS0zMy4zOTJWMzg3LjU2OHoiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6IzRFNEU0RSIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD4KCTwvZz4KPC9nPjxnPgoJPGc+CgkJPHBhdGggZD0iTTkyLjkyNiwzNTguNDc5TDU4LjgxMSwzNDIuMDFjLTMuNzMyLTEuODAzLTguMjE0LTAuMjM3LTEwLjAxNSwzLjQ5M2MtMS44MDEsMy43My0wLjIzNyw4LjIxNCwzLjQ5MywxMC4wMTUgICAgbDM0LjExNSwxNi40NjljMS4wNTEsMC41MDgsMi4xNjIsMC43NDgsMy4yNTUsMC43NDhjMi43ODgsMCw1LjQ2Ni0xLjU2Miw2Ljc1OS00LjI0MSAgICBDOTguMjIsMzY0Ljc2Myw5Ni42NTYsMzYwLjI4MSw5Mi45MjYsMzU4LjQ3OXoiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6IzRFNEU0RSIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD4KCTwvZz4KPC9nPjxnPgoJPGc+CgkJPHBhdGggZD0iTTEyNC4zMjMsMzM4LjA0MmwtNjUuNDY1LTMxLjYwNGMtMy43MzEtMS44MDEtOC4yMTQtMC4yMzctMTAuMDE1LDMuNDk0Yy0xLjgsMy43My0wLjIzNiw4LjIxNCwzLjQ5NCwxMC4wMTUgICAgbDY1LjQ2NSwzMS42MDRjMS4wNTEsMC41MDcsMi4xNjIsMC43NDgsMy4yNTUsMC43NDhjMi43ODgsMCw1LjQ2Ni0xLjU2Miw2Ljc1OS00LjI0MSAgICBDMTI5LjYxNywzNDQuMzI2LDEyOC4wNTMsMzM5Ljg0MiwxMjQuMzIzLDMzOC4wNDJ6IiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiM0RTRFNEUiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+Cgk8L2c+CjwvZz48L2c+IDwvc3ZnPg==" />
                                            </div>
                                            <h4 class="lh-1-4 m-b-0 text-center">
                                                {{ trans('account/tracking.index.title') }}
                                            </h4>
                                            <p class="text-center text-muted m-b-50">{{ trans('account/tracking.index.subtitle') }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <label class="m-t-10 pull-righ fw-400 fs-15">
                                                {{ trans('account/tracking.form.label') }}
                                            </label>
                                            <div class="form-group m-b-0">
                                                <input class="form-control input-lg nospace" value="{{ $tracking }}" placeholder="Ex. 001023900321, 005068105492, ..." name="tracking" type="text">
                                            </div>
                                            <div class="m-t-15 text-center">
                                                <button type="submit" class="btn btn-primary fs-16">
                                                    <i class="fas fa-search"></i> {{ trans('account/tracking.form.button') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="spacer-80 hidden-xs"></div>
                                    <div class="spacer-30 visible-xs"></div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(!empty($shipmentsResults))
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    @foreach($shipmentsResults as $shipment)
                        @include('default.partials.card_tracking')
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @elseif(!empty($tracking))
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="card card-tracking">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xs-12 col-md-offset-3 col-md-6">
                                        <div class="spacer-100"></div>
                                        <div class="text-center">
                                        <img class="h-60px" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNTEyLjAwMDM4IDUxMiIgd2lkdGg9IjUxMiI+PGc+PHBhdGggZD0ibTQ5OS42OTUzMTIgNDQwLjE3NTc4MS0xMTkuMjk2ODc0LTExOS4yOTY4NzVjLTEuODc4OTA3LTEuODc1LTQuNDE3OTY5LTIuOTI5Njg3LTcuMDcwMzEzLTIuOTI5Njg3cy01LjE5NTMxMyAxLjA1NDY4Ny03LjA3NDIxOSAyLjkyOTY4N2wtLjQ4ODI4MS40ODgyODItMTcuOTg4MjgxLTE3Ljk4ODI4MmMyMi41NjI1LTMxLjQ1NzAzMSAzNS44NjMyODEtNjkuOTgwNDY4IDM1Ljg2MzI4MS0xMTEuNTU4NTk0IDAtMTA1Ljc2OTUzMS04Ni4wNTA3ODEtMTkxLjgyMDMxMi0xOTEuODIwMzEzLTE5MS44MjAzMTItNDEuMDgyMDMxIDAtODEuODMyMDMxIDEzLjUzNTE1Ni0xMTQuNzUgMzguMTA5Mzc1LTQuNDI1NzgxIDMuMzA0Njg3LTUuMzM1OTM3IDkuNTcwMzEzLTIuMDMxMjUgMTMuOTk2MDk0IDMuMzA0Njg4IDQuNDI1NzgxIDkuNTY2NDA3IDUuMzMyMDMxIDEzLjk5NjA5NCAyLjAzMTI1IDI5LjQ4NDM3NS0yMi4wMTU2MjUgNjUuOTg4MjgyLTM0LjEzNjcxOSAxMDIuNzg1MTU2LTM0LjEzNjcxOSA5NC43NDIxODggMCAxNzEuODIwMzEzIDc3LjA3ODEyNSAxNzEuODIwMzEzIDE3MS44MjAzMTIgMCA5NC43NDIxODgtNzcuMDc4MTI1IDE3MS44MjAzMTMtMTcxLjgyMDMxMyAxNzEuODIwMzEzLTk0Ljc0MjE4NyAwLTE3MS44MjAzMTItNzcuMDc4MTI1LTE3MS44MjAzMTItMTcxLjgyMDMxMyAwLTMwLjExNzE4NyA3Ljk2MDkzOC01OS44MjgxMjQgMjMuMDE5NTMxLTg1LjkxMDE1NiAyLjc2MTcxOS00Ljc4NTE1NiAxLjEyMTA5NC0xMC44OTg0MzctMy42NjAxNTYtMTMuNjYwMTU2LTQuNzg1MTU2LTIuNzYxNzE5LTEwLjg5ODQzNy0xLjEyMTA5NC0xMy42NjAxNTYgMy42NjAxNTYtMTYuODEyNSAyOS4xMjEwOTQtMjUuNjk5MjE5IDYyLjI4NTE1Ni0yNS42OTkyMTkgOTUuOTEwMTU2IDAgMTA1Ljc2OTUzMiA4Ni4wNTA3ODEgMTkxLjgyMDMxMyAxOTEuODIwMzEyIDE5MS44MjAzMTMgNDEuNTc4MTI2IDAgODAuMTA1NDY5LTEzLjMwNDY4NyAxMTEuNTYyNS0zNS44NzEwOTRsMTcuOTg4MjgyIDE3Ljk5MjE4OC0uNDkyMTg4LjQ5MjE4N2MtMy45MDYyNSAzLjkwNjI1LTMuOTA2MjUgMTAuMjM0Mzc1IDAgMTQuMTQwNjI1bDExOS4yOTY4NzUgMTE5LjMwMDc4MWM4LjIwMzEyNSA4LjIwMzEyNiAxOC45ODA0NjkgMTIuMzA0Njg4IDI5Ljc1NzgxMyAxMi4zMDQ2ODhzMjEuNTU0Njg3LTQuMTAxNTYyIDI5Ljc2MTcxOC0xMi4zMDg1OTRjMTYuNDA2MjUtMTYuNDEwMTU2IDE2LjQwNjI1LTQzLjEwNTQ2OCAwLTU5LjUxNTYyNXptLTE4MC42MDE1NjItMTA0Ljk3NjU2MmM1LjY3OTY4OC01LjA0Njg3NSAxMS4wNjI1LTEwLjQyNTc4MSAxNi4xMDkzNzUtMTYuMTA5Mzc1bDE2LjQyMTg3NSAxNi40MjE4NzUtMTYuMTEzMjgxIDE2LjEwOTM3NXptMTY2LjQ1NzAzMSAxNTAuMzUxNTYyYy04LjYwOTM3NSA4LjYxMzI4MS0yMi42MjUgOC42MTMyODEtMzEuMjM0Mzc1IDBsLTExMi4yMjY1NjItMTEyLjIyNjU2Mi40NzY1NjItLjQ3MjY1N2MuMDA3ODEzLS4wMDc4MTIuMDExNzE5LS4wMTE3MTguMDE5NTMyLS4wMTk1MzFsMzAuMjUtMzAuMjVjLjAwMzkwNi0uMDAzOTA2LjAwMzkwNi0uMDAzOTA2LjAwNzgxMi0uMDA3ODEybC40ODQzNzUtLjQ4NDM3NSAxMTIuMjI2NTYzIDExMi4yMjY1NjJjOC42MDkzNzQgOC42MTMyODIgOC42MDkzNzQgMjIuNjI1LS4wMDM5MDcgMzEuMjM0Mzc1em0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6IzRFNEU0RSIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48cGF0aCBkPSJtNDQuNzMwNDY5IDg0LjUzNTE1NmMxLjg1NTQ2OSAxLjUwMzkwNiA0LjA4MjAzMSAyLjIzNDM3NSA2LjI5Mjk2OSAyLjIzNDM3NSAyLjkxMDE1NiAwIDUuNzk2ODc0LTEuMjY1NjI1IDcuNzczNDM3LTMuNjk5MjE5bC4xNjAxNTYtLjE5OTIxOGMzLjQ4ODI4MS00LjI4NTE1NiAyLjgxMjUtMTAuNTQ2ODc1LTEuNDcyNjU2LTE0LjAzMTI1LTQuMjgxMjUtMy40ODgyODItMTAuNjA5Mzc1LTIuODA0Njg4LTE0LjA5NzY1NiAxLjQ4MDQ2OGwtLjEyMTA5NC4xNDg0MzhjLTMuNDgwNDY5IDQuMjg5MDYyLTIuODI0MjE5IDEwLjU4NTkzOCAxLjQ2NDg0NCAxNC4wNjY0MDZ6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojNEU0RTRFIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zMzAuMjMwNDY5IDI3MS43MzA0NjljMTQuMDA3ODEyLTI0LjI2MTcxOSAyMS40MTAxNTYtNTEuODk0NTMxIDIxLjQxMDE1Ni03OS45MTAxNTcgMC04OC4xMjUtNzEuNjk1MzEzLTE1OS44MjAzMTItMTU5LjgyMDMxMy0xNTkuODIwMzEycy0xNTkuODIwMzEyIDcxLjY5NTMxMi0xNTkuODIwMzEyIDE1OS44MjAzMTIgNzEuNjk1MzEyIDE1OS44MjAzMTMgMTU5LjgyMDMxMiAxNTkuODIwMzEzYzI4LjAxNTYyNiAwIDU1LjY0ODQzOC03LjQwMjM0NCA3OS45MTAxNTctMjEuNDEwMTU2IDQuNzg1MTU2LTIuNzYxNzE5IDYuNDIxODc1LTguODc1IDMuNjYwMTU2LTEzLjY2MDE1Ny0yLjc2MTcxOS00Ljc4MTI1LTguODc1LTYuNDIxODc0LTEzLjY2MDE1Ni0zLjY2MDE1Ni0yMS4yMjY1NjMgMTIuMjUzOTA2LTQ1LjM5ODQzOCAxOC43MzQzNzUtNjkuOTEwMTU3IDE4LjczNDM3NS03Ny4wOTc2NTYgMC0xMzkuODIwMzEyLTYyLjcyNjU2Mi0xMzkuODIwMzEyLTEzOS44MjQyMTkgMC03Ny4wOTc2NTYgNjIuNzIyNjU2LTEzOS44MjAzMTIgMTM5LjgyMDMxMi0xMzkuODIwMzEyIDc3LjA5NzY1NyAwIDEzOS44MjAzMTMgNjIuNzIyNjU2IDEzOS44MjAzMTMgMTM5LjgyMDMxMiAwIDI0LjUxMTcxOS02LjQ3NjU2MyA0OC42ODc1LTE4LjczMDQ2OSA2OS45MTQwNjMtMi43NjE3MTggNC43ODEyNS0xLjEyMTA5NCAxMC44OTg0MzcgMy42NjAxNTYgMTMuNjYwMTU2IDQuNzg1MTU3IDIuNzU3ODEzIDEwLjg5ODQzOCAxLjEyMTA5NCAxMy42NjAxNTctMy42NjQwNjJ6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojNEU0RTRFIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im0zMTEuMTY0MDYyIDI4Mi44OTA2MjVjLTQuMTY3OTY4LTMuNjIxMDk0LTEwLjQ4NDM3NC0zLjE3NTc4MS0xNC4xMDU0NjguOTkyMTg3LTMuNjgzNTk0IDQuMTE3MTg4LTMuMzMyMDMyIDEwLjQzNzUuNzg1MTU2IDE0LjEyMTA5NCAxLjkwNjI1IDEuNzA3MDMyIDQuMjg5MDYyIDIuNTQ2ODc1IDYuNjY0MDYyIDIuNTQ2ODc1IDIuNzQ2MDk0IDAgNS40ODA0NjktMS4xMjUgNy40NTMxMjYtMy4zMzIwMzFsLjE4NzUtLjIwNzAzMS03LjUzOTA2My02LjU3MDMxMyA3LjU1MDc4MSA2LjU1NDY4OGMzLjYyMTA5NC00LjE3MTg3NSAzLjE3NTc4Mi0xMC40ODQzNzUtLjk5NjA5NC0xNC4xMDU0Njl6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBzdHlsZT0iZmlsbDojNEU0RTRFIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCI+PC9wYXRoPjxwYXRoIGQ9Im05NS4yMjI2NTYgMjQxLjgyMDMxMmMwIDMuNTcwMzEzIDEuOTA2MjUgNi44NzEwOTQgNSA4LjY2MDE1N2w4Ni41OTc2NTYgNDkuOTk2MDkzYy4wMjczNDQuMDE1NjI2LjA1NDY4OC4wMjM0MzguMDc4MTI2LjAzOTA2My4zNTU0NjguMTk5MjE5LjcxODc1LjM4MjgxMyAxLjEwMTU2Mi41MzkwNjMuMDIzNDM4LjAxMTcxOC4wNTA3ODEuMDE5NTMxLjA3NDIxOS4wMzEyNS4zNDM3NS4xMzY3MTguNjk1MzEyLjI1MzkwNiAxLjA1MDc4MS4zNTE1NjIuMTAxNTYyLjAyNzM0NC4yMDMxMjUuMDUwNzgxLjMwMDc4MS4wNzgxMjUuMjkyOTY5LjA3NDIxOS41OTM3NS4xMjg5MDYuODk4NDM4LjE3NTc4MS4xMDE1NjIuMDE1NjI1LjIwNzAzMS4wMzUxNTYuMzA4NTkzLjA0Njg3NS4zOTA2MjYuMDQ2ODc1Ljc4NTE1Ny4wNzgxMjUgMS4xOTE0MDcuMDc4MTI1LjQwMjM0MyAwIC43OTY4NzUtLjAzMTI1IDEuMTg3NS0uMDc4MTI1LjEwMTU2Mi0uMDExNzE5LjIwMzEyNS0uMDMxMjUuMzA4NTkzLS4wNDY4NzUuMzA0Njg4LS4wNDY4NzUuNjA1NDY5LS4xMDU0NjguOTAyMzQ0LS4xNzU3ODEuMDk3NjU2LS4wMjczNDQuMTk1MzEzLS4wNTA3ODEuMjkyOTY5LS4wNzgxMjUuMzYzMjgxLS4xMDE1NjIuNzE0ODQ0LS4yMTg3NSAxLjA1ODU5NC0uMzU1NDY5LjAyMzQzNy0uMDExNzE5LjA0Njg3NS0uMDE1NjI1LjA3MDMxMi0uMDI3MzQzLjM3ODkwNy0uMTU2MjUuNzQ2MDk0LS4zMzk4NDQgMS4xMDE1NjMtLjUzOTA2My4wMjM0MzctLjAxNTYyNS4wNTA3ODEtLjAyNzM0NC4wNzgxMjUtLjAzOTA2M2w4Ni41OTc2NTYtNTBjMy4wOTM3NS0xLjc4NTE1NiA1LTUuMDg1OTM3IDUtOC42NjAxNTZ2LTk5Ljk5NjA5NGMwLS4wMjczNDMtLjAwMzkwNi0uMDU0Njg3LS4wMDM5MDYtLjA4MjAzMS0uMDAzOTA3LS40MTAxNTYtLjAzMTI1LS44MjAzMTItLjA4MjAzMS0xLjIyNjU2Mi0uMDAzOTA3LS4wMjM0MzgtLjAwNzgxMy0uMDQ2ODc1LS4wMTE3MTktLjA3MDMxMy0uMDUwNzgxLS4zNjcxODctLjEyODkwNy0uNzM0Mzc1LS4yMjI2NTctMS4wOTc2NTYtLjAyMzQzNy0uMDk3NjU2LS4wNTA3ODEtLjE5NTMxMi0uMDgyMDMxLS4yOTI5NjktLjA4MjAzMS0uMjkyOTY5LS4xODM1OTMtLjU4NTkzNy0uMjk2ODc1LS44NzEwOTMtLjAzOTA2Mi0uMDk3NjU3LS4wNzAzMTItLjE5NTMxMy0uMTEzMjgxLS4yODkwNjMtLjE1MjM0NC0uMzU5Mzc1LS4zMjQyMTktLjcxODc1LS41MjczNDQtMS4wNjY0MDZ2LS4wMDM5MDdjLS4yMDMxMjUtLjM0NzY1Ni0uNDI1NzgxLS42NzU3ODEtLjY2MDE1Ni0uOTg4MjgxLS4wNjI1LS4wODU5MzctLjEzMjgxMy0uMTY0MDYyLS4xOTUzMTMtLjI0NjA5My0uMTkxNDA2LS4yNDIxODgtLjM5MDYyNC0uNDY4NzUtLjYwMTU2Mi0uNjg3NS0uMDc0MjE5LS4wNzQyMTktLjE0NDUzMS0uMTUyMzQ0LS4yMTg3NS0uMjIyNjU3LS4yNjE3MTktLjI1NzgxMi0uNTM1MTU2LS41MDM5MDYtLjgyNDIxOS0uNzI2NTYyLS4wMjM0MzctLjAxOTUzMS0uMDQ2ODc1LS4wNDI5NjktLjA3MDMxMi0uMDU4NTk0LS4zMjQyMTktLjI1LS42NjQwNjMtLjQ3MjY1Ni0xLjAxMTcxOS0uNjc5Njg3LS4wMjczNDQtLjAxNTYyNi0uMDUwNzgxLS4wMzUxNTctLjA3ODEyNS0uMDUwNzgybC04Ni41OTc2NTYtNDkuOTk2MDk0Yy0zLjA5Mzc1LTEuNzg5MDYyLTYuOTA2MjUtMS43ODkwNjItMTAgMGwtODYuNjAxNTYzIDQ5Ljk5NjA5NGMtLjAyMzQzNy4wMTE3MTktLjA0Mjk2OC4wMzEyNS0uMDY2NDA2LjA0Mjk2OS0uMzU1NDY5LjIxMDkzNy0uNjk5MjE5LjQzNzUtMS4wMjczNDQuNjkxNDA2LS4wMTE3MTguMDA3ODEzLS4wMjM0MzcuMDE5NTMxLS4wMzkwNjIuMDMxMjUtLjI5Njg3NS4yMzA0NjktLjU4MjAzMi40ODQzNzUtLjg1NTQ2OS43NTM5MDctLjA3MDMxMy4wNzAzMTItLjEzNjcxOS4xNDA2MjQtLjIwNzAzMS4yMTA5MzctLjIxMDkzOC4yMjI2NTYtLjQxNDA2My40NTMxMjUtLjYwOTM3NS42OTkyMTktLjA2NjQwNy4wNzgxMjUtLjEzMjgxMy4xNTYyNS0uMTkxNDA3LjIzODI4MS0uMjM0Mzc0LjMxNjQwNi0uNDYwOTM3LjY0MDYyNS0uNjYwMTU2Ljk5MjE4Ny0uMDAzOTA2IDAtLjAwMzkwNiAwLS4wMDM5MDYuMDAzOTA3LS4xOTkyMTkuMzQ3NjU2LS4zNzEwOTQuNzAzMTI1LS41MjczNDQgMS4wNjY0MDYtLjAzOTA2Mi4wOTM3NS0uMDc0MjE4LjE5MTQwNi0uMTEzMjgxLjI4NTE1Ni0uMTEzMjgxLjI4OTA2My0uMjEwOTM3LjU4MjAzMS0uMjk2ODc1Ljg3NS0uMDI3MzQ0LjA5NzY1Ny0uMDU0Njg4LjE5MTQwNy0uMDgyMDMxLjI4OTA2My0uMDkzNzUuMzY3MTg3LS4xNjc5NjkuNzMwNDY4LS4yMTg3NSAxLjEwMTU2Mi0uMDAzOTA3LjAyMzQzOC0uMDExNzE5LjA0Njg3NS0uMDExNzE5LjA3MDMxMy0uMDU0Njg4LjQwNjI1LS4wNzgxMjUuODE2NDA2LS4wODIwMzEgMS4yMjI2NTYgMCAuMDMxMjUtLjAwNzgxMy4wNTg1OTQtLjAwNzgxMy4wODU5Mzd6bTIwLTgyLjY3NTc4MSAyOC4zNDM3NSAxNi4zNjMyODFjLjAxNTYyNS4wMDc4MTMuMDMxMjUuMDE5NTMyLjA1MDc4Mi4wMjczNDRsMzguMjAzMTI0IDIyLjA1ODU5NHY3Ni45MDYyNWwtNjYuNTk3NjU2LTM4LjQ1MzEyNXptMTQzLjE5OTIxOS0xNy4zMjAzMTItNjYuNjAxNTYzIDM4LjQ0OTIxOS0yMy4zMDA3ODEtMTMuNDUzMTI2IDY2LjYwMTU2My0zOC40NDkyMTh6bS01Ni42MDE1NjMgMTMyLjY3NTc4MXYtNzYuOTA2MjVsNjYuNjAxNTYzLTM4LjQ0OTIxOXY3Ni45MDIzNDR6bS0xMC0xNzEuMTI4OTA2IDIzLjMwMDc4MiAxMy40NTMxMjUtNjYuNjAxNTYzIDM4LjQ0OTIxOS0yMy4yOTY4NzUtMTMuNDQ5MjE5em0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6IzRFNEU0RSIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD48L2c+IDwvc3ZnPg==" />
                                        </div>
                                        <h4 class="lh-1-4 m-b-100 text-center">
                                            Não encontrámos correspondências para o código de envio <b>"{{ $tracking }}"</b>.
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    @if(!env('APP_HIDE_CREDITS'))
                        <p class="credit text-center">
                            {!! app_brand() !!}
                        </p>
                        <div class="spacer-60"></div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@stop

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey('public') }}"></script>
    <script>
        $('[data-toggle="popover"]').popover();

        @if(Setting::get('tracking_auto_sync') && count($shipmentsResults) == 1 && @!$shipmentsResults[0]['shipment']->status->is_final && @$shipmentsResults[0]['shipment']->hasSync())
            $('.table-history tbody, .loading-error').remove();
            $('.table-history').after('<h4 class="text-center loading-history" style="margin: 80px 0;"><i class="fas fa-spin fa-circle-notch"></i> A obter informação de estados. Aguarde. </h4>');

            $.post("{{ route('tracking.sync', @$shipmentsResults[0]['shipment']->tracking_code) }}", function(data) {
                if(data.html) {
                    $('.card-tracking[data-tracking]').replaceWith(data.html);
                    $('[data-toggle="popover"]').popover();
                } else {
                    $('.table-history').after('<h4 class="text-center loading-error text-red" style="margin: 80px 0;"><i class="fas fa-exclamation-triangle"></i> Lamentamos, não conseguimos obter a informação do seu pedido.</h4>');
                }
            }).fail(function (){
                $('.table-history').after('<h4 class="text-center loading-error text-red" style="margin: 80px 0;"><i class="fas fa-exclamation-triangle"></i> Lamentamos, não conseguimos obter a informação do seu pedido.</h4>');
            }).always(function () {
                $('.loading-history').remove();
            })
        @endif

        @if(@$shipment->live_tracking)
            var myLatlng = new google.maps.LatLng({{ Setting::get('maps_latitude_1') }},{{ Setting::get('maps_longitude_1') }});
            var mapOptions = {
                zoom: 16,
                center: myLatlng,
                mapTypeControl: false,
                scaleControl: false,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE
                },
            }
            var map = new google.maps.Map(document.getElementById("map"), mapOptions);

            var marker = new google.maps.Marker({
                position: myLatlng,
                icon: "{{ asset('assets/website/img/map-marker.svg') }}",
                title:"ESTAMOS AQUI"
            });

            // To add the marker to the map, call setMap();
            marker.setMap(map);
        @endif
    </script>
@stop