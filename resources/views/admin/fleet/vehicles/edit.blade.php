@section('title')
Viaturas
@stop

@section('content-header')
    Viaturas
    <small>
        @trans('Ficha de viatura')
    </small>
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li>
        <a href="{{ route('admin.fleet.vehicles.index') }}">
            @trans('Viaturas')
        </a>
    </li>
    <li class="active">
        @trans('Ficha de viatura')
    </li>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box no-border m-b-15">
            <div class="box-body p-5">
                <div class="row">
                    <div class="col-sm-5">
                        @if($vehicle->filepath)
                        <img src="{{ asset($vehicle->getThumb()) }}" class="pull-left w-60px m-r-10" style="border:none" />
                        @else
                            <?php $url = '/uploads/fleet_brands/' . str_slug($vehicle->brand->name) . '.jpg'; ?>
                            <img src="{{ asset($url) }}" class="pull-left w-60px m-r-10" style="border:none" onerror="this.onerror=null;this.src='{{ asset('assets/img/default/default.thumb.png') }}';"/>
                        @endif
                        <div class="pull-left">
                            <h4 class="m-t-5 pull-left"><b>{{ $vehicle->license_plate }}</b> <small>{{ $vehicle->name }}</small></h4>
                            <div class="clearfix"></div>
                            <ul class="list-inline m-b-0">
                                <li>
                                    {{ $vehicle->brand->name }}
                                    {{ @$vehicle->model->name }}
                                </li>
                                <li>
                                    |&nbsp;&nbsp;{{ money($vehicle->counter_km, 'km', 0) }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-7">
                        <ul class="list-inline m-t-8 m-b-0 pull-right hidden-xs">
                            {{--<li class="w-120px">
                                <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;">
                                    <small class="text-black">Operacionalidade</small><br/>
                                    <b data-toggle="tooltip" title="Ainda não existem dados suficientes para calcular o nível de operacionalidade"><i class="fas fa-wrench"></i>  N/A</b>
                                </h4>
                            </li>--}}
                            <li class="w-150px" style="position: relative">
                                <h4 class="m-0 pull-right" data-popover-graph="#popover-services" style="margin-top: -45px; position: absolute;">
                                    <small class="text-black">@trans('Rentabilidade')</small><br/>

                                    @if($balance['balance'] >= 0.00)
                                        <b class="text-green" style="display: block">
                                            <i class="fas fa-euro"></i> {{ money($balance['balance'], Setting::get('app_currency')) }}
                                            <i class="fas fa-chart-pie"></i>
                                            <i class="fas fa-angle-down"></i>
                                        </b>
                                    @else
                                        <b class="text-red" style="display: block">
                                            <i class="fas fa-euro"></i>
                                            {{ money($balance['balance'], Setting::get('app_currency')) }}
                                            <i class="fas fa-chart-pie"></i>
                                            <i class="fas fa-angle-down"></i>
                                        </b>
                                    @endif
                                    <small style="margin-top: 0; display: block;"><small>@trans('Ultimos 30 dias')</small></small>

                                </h4>
                                <div class="popover-graph" id="popover-services">
                                    <div class="popover-title text-uppercase">@trans('Histórico de rentabilidade') <small><i class="fas fa-times close"></i></small></div>
                                    <div class="p-15">
                                        <a href="{{ route('admin.fleet.stats.index', ['vehicle' => $vehicle->id]) }}" class="btn btn-xs btn-default pull-right"><i class="fas fa-chart-bar"></i> @trans('Todas Estatísticas')</a>
                                        <p class="pull-left">
                                            @trans('Histórico últimos 6 meses') {!! tip(__('Baseado no valor de envios/serviços e despesas no periodo indicado.')) !!}
                                        </p>
                                        <div class="clearfix"></div>
                                        <hr style="margin: 10px 0;"/>
                                        <div class="clearfix"></div>
                                        <div class="chart-loading" style="text-align: center; margin-bottom: -300px; margin-top: 100px;">
                                            <i class="fas fa-spin fa-circle-notch"></i>
                                            @trans('Aguarde...')'
                                        </div>
                                        {{--<h5 class="m-0 b-t-1 p-t-10">Histórico dos ultimos 30 dias</h5>--}}
                                        <canvas id="graphic-rentability" style='width: 500px; height: 250px'>
                                        </canvas>
                                    </div>
                                </div>
                            </li>
                            @if($vehicle->type != 'trailer')
                            <li class="w-80px">
                                <h4 class="m-0 pull-right" style="margin-top: -45px; position: absolute;">
                                    <small class="text-black">@trans('Consumo')</small><br/>
                                    <b class="{{ $vehicle->consumption_color }}"><i class="fas fa-gas-pump"></i> {{ $vehicle->counter_consumption }}</b>
                                    <small style="margin-top: 0; display: block;"><small>@trans('Ultima média')</small></small>
                                </h4>
                            </li>
                            @endif
                            <li class="divider"></li>
                            <li>
                                <div class="btn-group btn-group-sm" role="group" style="margin-top: -30px">
                                    @if($nextId = $vehicle->nextId())
                                        <a href="{{ route('admin.fleet.vehicles.edit', [$nextId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Anterior">
                                            <i class="fa fa-fw fa-angle-left"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-default" disabled>
                                            <i class="fa fa-fw fa-angle-left"></i>
                                        </button>
                                    @endif

                                    @if($prevId = $vehicle->previousId())
                                        <a href="{{ route('admin.fleet.vehicles.edit', ['id' => $prevId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Próximo">
                                            <i class="fa fa-fw fa-angle-right"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-default" disabled>
                                            <i class="fa fa-fw fa-angle-right"></i>
                                        </button>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-5">
    @if($notifications)
        <div class="col-xs-12">
            <div class="alert {{ @$notificationsExpired ? 'alert-danger' : 'alert-warning' }}">
                <h4 class="m-b-0">
                    @if(@$notificationsWarning)
                        @trans('Tem') {{ $notificationsWarning }} @trans('lembretes prestes a expirar.')
                    @endif
                    @if(@$notificationsExpired)
                        @trans('Tem') {{ $notificationsExpired }} @trans('lembretes já ultrapassados.')
                    @endif
                    <small>
                        <a href="{{ route('admin.fleet.reminders.reset.edit', ['vehicle' => $vehicle->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg"
                           class="btn btn-xs btn-default" style="color: #333; text-decoration: none; margin-top: -10px; margin-bottom: -5px;">
                           @trans('Reiniciar ou Concluir')
                        </a>
                    </small>
                </h4>
            </div>
        </div>
    @endif
    <div class="col-md-3 col-lg-2">
        <div class="box box-solid">
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active">
                        <a href="#tab-info" data-toggle="tab"><i class="fas fa-fw fa-info-circle"></i> @trans('Dados Gerais')</a>
                    </li>
                    @if(hasPermission('fleet_accessories'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-accessories" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-folder-plus"></i> @trans('Acessórios')</a>
                    </li>
                    @endif
                    @if($vehicle->type != 'trailer')
                        @if(hasPermission('fleet_usages'))
                            <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                                <a href="#tab-usage" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-user"></i> @trans('Histórico de Utilização')</a>
                            </li>
                        @endif
                        @if(hasPermission('fleet_fuel_logs'))
                        <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                            <a href="#tab-fuel" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-gas-pump"></i> @trans('Abastecimentos')</a>
                        </li>
                        @endif
                    @endif
                    @if(hasPermission('fleet_maintenances'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-maintenances" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-wrench"></i> @trans('Manutenções')</a>
                    </li>
                    @endif
                    @if(hasPermission('fleet_expenses') || hasPermission('fleet_tolls_log') || hasPermission('fleet_fixed_costs'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-costs" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-euro-sign"></i> @trans('Despesas e Custos Fixos')</a>
                    </li>
                    @endif
                    @if(hasPermission('fleet_tyres'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-tyres" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}">
                        <img style="height: 16px;
                        margin-top: -3px;" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOnN2Z2pzPSJodHRwOi8vc3ZnanMuY29tL3N2Z2pzIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTIiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnPjxwYXRoIGQ9Ik0yNTYgODUuMzMzYy05NC4xMDQgMC0xNzAuNjY3IDc2LjU2My0xNzAuNjY3IDE3MC42NjdTMTYxLjg5NiA0MjYuNjY3IDI1NiA0MjYuNjY3IDQyNi42NjcgMzUwLjEwNCA0MjYuNjY3IDI1NiAzNTAuMTA0IDg1LjMzMyAyNTYgODUuMzMzem04Ny4zMTMgNjMuNzVhMTAuNjM5IDEwLjYzOSAwIDAgMSA3LjUtMy41MWMyLjQ3OS0uMjgxIDUuNjY3LjkyNyA3Ljc1IDIuOTA2IDIyLjEyNSAyMC45NDggMzcuMTY3IDQ3LjYxNSA0My40NTggNzcuMTE1IDEgNC43MDgtMS4yNzEgOS41LTUuNTQyIDExLjcwOC0xMC40MzggNS4zOTYtMjIuODMzIDguMDEtMzQuNzA4IDguMDEtMTUuNDc5IDAtMzAuMTQ2LTQuNDM4LTM4LjY0Ni0xMi45MzgtMTguMDgzLTE4LjA3Mi0xMC41NDItNDkuMjA3IDIwLjE4OC04My4yOTF6bS0xNzQuNjI1IDIxNS41YTEwLjY1NCAxMC42NTQgMCAwIDEtNy41IDMuNTIxYy0uMTI1LjAxLS4yNzEuMDEtLjQxNy4wMWExMC42NTkgMTAuNjU5IDAgMCAxLTcuMzMzLTIuOTE3Yy0yMi4xMjUtMjAuOTQ4LTM3LjE2Ny00Ny42MTUtNDMuNDU4LTc3LjEyNS0xLTQuNzA4IDEuMjcxLTkuNSA1LjU0Mi0xMS43MDggMjQuMDQyLTEyLjQwNiA1OC4zMzMtMTAuMDk0IDczLjM1NCA0LjkyNyAxOC4wNjIgMTguMDc0IDEwLjUyIDQ5LjIwOS0yMC4xODggODMuMjkyem0yMC4xODctMTMyLjIwOGMtOC41IDguNS0yMy4xNjcgMTIuOTM4LTM4LjY0NiAxMi45MzgtMTEuODc1IDAtMjQuMjUtMi42MTUtMzQuNzA4LTguMDFhMTAuNjgxIDEwLjY4MSAwIDAgMS01LjU0Mi0xMS43MDhjNi4yOTItMjkuNSAyMS4zMzMtNTYuMTY3IDQzLjQ1OC03Ny4xMTUgMi4wODMtMS45NzkgNS4xNDYtMy4yMDggNy43NS0yLjkwNmExMC42NDMgMTAuNjQzIDAgMCAxIDcuNSAzLjUxYzMwLjczIDM0LjA4MyAzOC4yNzEgNjUuMjE4IDIwLjE4OCA4My4yOTF6TTMwMS40NTggMzk4LjI0Yy0xNC43NzEgNC43MDgtMzAuMDYzIDcuMDk0LTQ1LjQ1OCA3LjA5NHMtMzAuNjg4LTIuMzg1LTQ1LjQ1OC03LjA5NGMtNC42ODgtMS41LTcuNzUtNi03LjQxNy0xMC45MDZDMjA1Ljc5MiAzNDguOTQ4IDIyOC41MjEgMzIwIDI1NiAzMjBzNTAuMjA4IDI4Ljk0OCA1Mi44NzUgNjcuMzMzYTEwLjY5NSAxMC42OTUgMCAwIDEtNy40MTcgMTAuOTA3ek0yMTMuMzMzIDI1NmMwLTIzLjUzMSAxOS4xNDYtNDIuNjY3IDQyLjY2Ny00Mi42NjdzNDIuNjY3IDE5LjEzNSA0Mi42NjcgNDIuNjY3LTE5LjE0NiA0Mi42NjctNDIuNjY3IDQyLjY2Ny00Mi42NjctMTkuMTM2LTQyLjY2Ny00Mi42Njd6bTk1LjU0Mi0xMzAuNDljLTIuNjY3IDM4LjM3NS0yNS4zOTYgNjcuMzIzLTUyLjg3NSA2Ny4zMjNzLTUwLjIwOC0yOC45NDgtNTIuODc1LTY3LjMyM2MtLjMzMy00LjkwNiAyLjcwOC05LjQwNiA3LjM5Ni0xMC44OTZhMTQ5LjY0IDE0OS42NCAwIDAgMSA5MC45NTggMGM0LjY4OCAxLjQ5IDcuNzI5IDUuOTkgNy4zOTYgMTAuODk2em05My4xNDYgMTYwLjg2NWMtNi4yOTIgMjkuNTEtMjEuMzMzIDU2LjE3Ny00My40NTggNzcuMTI1YTEwLjY1OSAxMC42NTkgMCAwIDEtNy4zMzMgMi45MTdjLS4xNDYgMC0uMjkyIDAtLjQxNy0uMDFhMTAuNjQzIDEwLjY0MyAwIDAgMS03LjUtMy41MWMtMzAuNzI5LTM0LjA4My0zOC4yNzEtNjUuMjI5LTIwLjE4OC04My4zMDIgMTUuMDIxLTE1LjAzMSA0OS4yOTItMTcuMzEzIDczLjM1NC00LjkyN2ExMC42OCAxMC42OCAwIDAgMSA1LjU0MiAxMS43MDd6IiBmaWxsPSIjNDQ0NDQ0IiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBvcGFjaXR5PSIxIj48L3BhdGg+PHBhdGggZD0iTTI1NiAwQzExNC44MzMgMCAwIDExNC44NDQgMCAyNTZzMTE0LjgzMyAyNTYgMjU2IDI1NiAyNTYtMTE0Ljg0NCAyNTYtMjU2UzM5Ny4xNjcgMCAyNTYgMHptMCA0NDhjLTEwNS44NzUgMC0xOTItODYuMTM1LTE5Mi0xOTJTMTUwLjEyNSA2NCAyNTYgNjRzMTkyIDg2LjEzNSAxOTIgMTkyLTg2LjEyNSAxOTItMTkyIDE5MnoiIGZpbGw9IiM0NDQ0NDQiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIG9wYWNpdHk9IjEiPjwvcGF0aD48L2c+PC9zdmc+" /> Gestão Pneus
                        </a>   
                    </li>
                    @endif
                    @if(hasPermission('fleet_incidences'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-incidences" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-car-crash"></i> @trans('Sinistros')</a>
                    </li>
                    @endif
                    @if(hasPermission('fleet_reminders'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-reminders" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-bell"></i> @trans('Lembretes')</a>
                    </li>
                    @endif
                    @if(hasPermission('fleet_checklists'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-checklists" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-tasks"></i> @trans('Fichas de Controlo')</a>
                    </li>
                    @endif
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-attachments" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-file"></i> @trans('Documentos')</a>
                    </li>
                    @if(hasPermission('fleet_costs'))
                    <li class="{{ $vehicle->exists ? '' : 'disabled' }}">
                        <a href="#tab-history" data-toggle="{{ $vehicle->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-history"></i> @trans('Histórico Geral')</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9 col-lg-10">
        <div class="tab-content">
            <div class="active tab-pane" id="tab-info">
                @include('admin.fleet.vehicles.partials.info')
            </div>
            @if(hasPermission('fleet_accessories'))
            <div class="tab-pane" id="tab-accessories" data-empty="1">
                @include('admin.fleet.vehicles.partials.accessories')
            </div>
            @endif
            @if(hasPermission('fleet_reminders'))
            <div class="tab-pane" id="tab-reminders" data-empty="1">
                @include('admin.fleet.vehicles.partials.reminders')
            </div>
            @endif
            @if(hasPermission('fleet_usages'))
            <div class="tab-pane" id="tab-usage" data-empty="1">
                @include('admin.fleet.vehicles.partials.usage')
            </div>
            @endif
            @if(hasPermission('fleet_fuel_logs'))
            <div class="tab-pane" id="tab-fuel" data-empty="1">
                @include('admin.fleet.vehicles.partials.fuel')
            </div>
            @endif
            @if(hasPermission('fleet_maintenances'))
            <div class="tab-pane" id="tab-maintenances" data-empty="1">
                @include('admin.fleet.vehicles.partials.maintenances')
            </div>
            @endif
            @if(hasPermission('fleet_incidences'))
            <div class="tab-pane" id="tab-incidences" data-empty="1">
                @include('admin.fleet.vehicles.partials.incidences')
            </div>
            @endif
            @if(hasPermission('fleet_tyres'))
            <div class="tab-pane" id="tab-tyres" data-empty="1">
                @include('admin.fleet.vehicles.partials.tyres')
            </div>
            @endif
            @if(hasPermission('fleet_expenses') || hasPermission('fleet_tolls_log') || hasPermission('fleet_fixed_costs'))
            <div class="tab-pane" id="tab-costs">
                @include('admin.fleet.vehicles.partials.costs')
            </div>
            @endif
            @if(hasPermission('fleet_checklists'))
            <div class="tab-pane" id="tab-checklists" data-empty="1">
                @include('admin.fleet.vehicles.partials.checklists')
            </div>
            @endif
            <div class="tab-pane" id="tab-attachments" data-empty="1">
                @include('admin.fleet.vehicles.partials.attachments')
            </div>
            @if(hasPermission('fleet_costs'))
            <div class="tab-pane" id="tab-history" data-empty="1">
                @include('admin.fleet.vehicles.partials.history')
            </div>
            @endif
        </div>
    </div>
</div>
@include('admin.fleet.tolls.modals.import')
    <style>
        .dtbr-2 {
            border-right: 3px solid #888 !important;
        }
    </style>
@stop

@section('scripts')
    {{ Html::script('vendor/chart.js/dist/Chart.min.js') }}
<script type="text/javascript">
    $('[data-toggle="tooltip"]').tooltip();

    var oTableAttachments;
    var oTableFuel;
    var oTableTolls;
    var oTableAccessories;
    var oTableTyres;
    var oTableMaintenances;
    var oTableExpenses;
    var oTableIncidences;
    var oTableReminders;
    var oTableUsage;
    var oTableFixedCosts;
    var oTableHistory;
    var oTableChecklist;


    var tab = "{{ Request::get('tab') }}";
    if(tab.includes("costs")) {
        if(tab == "costs") {
            tab = 'costs-expenses';
        }
        $('[href="#tab-costs"]').closest('ul').find('li').removeClass('active');
        $('[href="#tab-costs"]').closest('li').addClass('active');
        $('.tab-pane').removeClass('active');
        $('#tab-' + tab).addClass('active');
        $('#tab-costs').addClass('active')
    }

    /**
     * Tab fuel
     */
    $(document).on('click', 'a[href="#tab-fuel"]', function(){
        $tab = $('#tab-fuel');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableFuel = $('#datatable-fuel').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'date', name: 'date'},
                    {data: 'provider', name: 'provider', orderable: false, searchable: false},
                    {data: 'operator', name: 'operator', orderable: false, searchable: false},
                    {data: 'product', name: 'product'},
                    {data: 'km', name: 'km', class: 'text-right'},
                    {data: 'liters', name: 'liters', class: 'text-right'},
                    {data: 'price_per_liter', name: 'price_per_liter', class: 'text-right'},
                    {data: 'total', name: 'total', class: 'text-right dtbr-2'},
                    {data: 'balance_km', name: 'balance_km', class: 'text-center'},
                    {data: 'balance_liter_km', name: 'balance_liter_km', class: 'text-center'},
                    {{--@if(hasPermission('purchase_invoices'))
                    {data: 'assigned_invoice_id', name: 'assigned_invoice_id'},
                    @endif--}}
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.fuel.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.provider = $('[data-target="#datatable-fuel"] select[name=fuel_provider]').val();
                        d.product  = $('[data-target="#datatable-fuel"] select[name=fuel_product]').val();
                        d.operator = $('[data-target="#datatable-fuel"] select[name=fuel_operator]').val();
                        d.date_min = $('[data-target="#datatable-fuel"] input[name=fuel_date_min]').val();
                        d.date_max = $('[data-target="#datatable-fuel"] input[name=fuel_date_max]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableFuel) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-fuel"] .filter-datatable').on('change', function (e) {
                oTableFuel.draw();
                e.preventDefault();
            });
        }
    });

    /**
     * Tab attachments
     */
    $(document).on('click', 'a[href="#tab-attachments"]', function(){
        $tab = $('#tab-attachments');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

             oTableAttachments = $('#datatable-attachments').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'name', name: 'name'},
                    {data: 'sort', name: 'sort'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[3, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.vehicles.attachments.datatable', $vehicle->id) }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle = "{{ $vehicle->id }}";
                        d.type_id = $('[data-target="#datatable-attachments"] select[name=type]').val();
                        d.active  = $('[data-target="#datatable-attachments"] select[name=active]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableAttachments) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-attachments"] .filter-datatable').on('change', function (e) {
                oTableAttachments.draw();
                e.preventDefault();
            });
        }
    })

    /**
     * Tab tolls
     */
    $(document).on('click', 'a[href="#tab-costs-tolls"]', function(){
        $tab = $('#tab-costs-tolls');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableTolls = $('#datatable-tolls').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'entry_date', name: 'entry_date'},
                    {data: 'provider_id', name: 'provider_id', orderable: false, searchable: false},
                    {data: 'toll_provider', name: 'toll_provider', orderable: false, searchable: false},
                    {data: 'count', name: 'count', class: 'text-center', orderable: false, searchable: false},
                    {data: 'total', name: 'total', orderable: false, searchable: false},
                    {data: 'class', name: 'class', class: 'text-center', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[3, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.tolls.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.date_min = $('[data-target="#datatable-tolls"] input[name=tolls_date_min]').val();
                        d.date_max = $('[data-target="#datatable-tolls"] input[name=tolls_date_max]').val();
                        d.provider = $('[data-target="#datatable-tolls"] select[name=tolls_provider]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableTolls) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-tolls"] .filter-datatable').on('change', function (e) {
                oTableTolls.draw();
                e.preventDefault();
            });
        }
    })


    /**
     * Tab expenses
     */
    $(document).on('click', 'a[href="#tab-costs-expenses"], a[href="#tab-costs"]', function(){
        $tab = $('#tab-costs-expenses');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableExpenses = $('#datatable-expenses').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'date', name: 'date'},
                    {data: 'title', name: 'title'},
                    {data: 'provider_id', name: 'provider_id'},
                    {data: 'operator_id', name: 'operator_id'},
                    {data: 'km', name: 'km'},
                    {data: 'total', name: 'total'},
                    {data: 'assigned_invoice_id', name: 'assigned_invoice_id'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.expenses.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.date_min = $('[data-target="#datatable-expenses"] input[name=expenses_date_min]').val();
                        d.date_max = $('[data-target="#datatable-expenses"] input[name=expenses_date_max]').val();
                        d.provider = $('[data-target="#datatable-expenses"] select[name=expenses_provider]').val();
                        d.operator = $('[data-target="#datatable-expenses"] select[name=expenses_operator]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableExpenses) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-expenses"] .filter-datatable').on('change', function (e) {
                oTableExpenses.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-target="#datatable-expenses"] [data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-target="#datatable-expenses"] [data-toggle="export-url"]').attr('href', exportUrl);
            });
        }
    })

    /**
     * Tab incidences
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-incidences"]', function(){
        $tab = $('#tab-incidences');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableIncidences = $('#datatable-incidences').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'date', name: 'date'},
                    {data: 'title', name: 'title'},
                    {data: 'operator_id', name: 'operator_id'},
                    {data: 'km', name: 'km'},
                    {data: 'total', name: 'total'},
                    {data: 'is_fixed', name: 'is_fixed'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.incidences.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.operator = $('[data-target="#datatable-incidences"] select[name=operator]').val();
                        d.date_min = $('[data-target="#datatable-incidences"] input[name=date_min]').val();
                        d.date_max = $('[data-target="#datatable-incidences"] input[name=date_max]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableIncidences) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-incidences"] .filter-datatable').on('change', function (e) {
                oTableIncidences.draw();
                e.preventDefault();
            });
        }
    })

    /**
     * Tab maintenances
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-maintenances"]', function(){
        $tab = $('#tab-maintenances');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableMaintenances = $('#datatable-maintenances').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'date', name: 'date'},
                    {data: 'title', name: 'title'},
                    {data: 'provider_id', name: 'provider_id'},
                    {data: 'km', name: 'km'},
                    {data: 'total', name: 'total'},
                    {{--@if(hasPermission('purchase_invoices'))
                    {data: 'assigned_invoice_id', name: 'assigned_invoice_id'},
                    @endif--}}
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.maintenances.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.operator = $('[data-target="#datatable-maintenances"] select[name=maintenance_operator]').val();
                        d.provider = $('[data-target="#datatable-maintenances"] select[name=maintenance_provider]').val();
                        d.parts    = $('[data-target="#datatable-maintenances"] select[name=maintenance_parts]').val();
                        d.date_min = $('[data-target="#datatable-maintenances"] input[name=maintenance_date_min]').val();
                        d.date_max = $('[data-target="#datatable-maintenances"] input[name=maintenance_date_max]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableMaintenances) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-maintenances"] .filter-datatable').on('change', function (e) {
                oTableMaintenances.draw();
                e.preventDefault();
            });
        }
    })

    /**
     * Tab maintenances
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-reminders"]', function(){
        $tab = $('#tab-reminders');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableReminders = $('#datatable-reminders').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'title', name: 'title'},
                    {data: 'date', name: 'date', class: 'text-center'},
                    {data: 'km', name: 'km', class: 'text-center'},
                    {data: 'days_alert', name: 'days_alert', class: 'text-center'},
                    {data: 'km_alert', name: 'km_alert', class: 'text-center'},
                    {data: 'is_active', name: 'is_active', class: 'text-center'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'km', name: 'km', visible: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.reminders.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.active   = $('[data-target="#datatable-reminders"] select[name=reminder_active]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableReminders) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-reminders"] .filter-datatable').on('change', function (e) {
                oTableReminders.draw();
                e.preventDefault();
            });
        }
    })

    /**
     * Tab accessories
     */
    $(document).on('click', 'a[href="#tab-accessories"]', function(){
        $tab = $('#tab-accessories');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableAccessories = $('#datatable-accessories').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'brand', name: 'brand'},
                    {data: 'model', name: 'model'},
                    {data: 'buy_date', name: 'buy_date'},
                    {data: 'validity_date', name: 'validity_date'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.accessories.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle = "{{ $vehicle->id }}";
                        d.type    = $('[data-target="#datatable-accessories"] select[name=type]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableAccessories) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-accessories"] .filter-datatable').on('change', function (e) {
                oTableaccessories.draw();
                e.preventDefault();
            });
        }
    });

    /**
     * Tab Usage Logs
     */
    $(document).on('click', 'a[href="#tab-usage"]', function(){
        $tab = $('#tab-usage');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableUsage = $('#datatable-usage').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'operator', name: 'operator', orderable: false, searchable: false},
                    {data: 'type', name: 'type', orderable: false, searchable: false, class: 'text-center'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'start_km', name: 'start_km', class: 'text-center'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'end_km', name: 'end_km', class: 'text-center'},
                    {data: 'duration', name: 'duration', class: 'text-center bold', orderable: false, searchable: false},
                    {data: 'total_km', name: 'total_km', class: 'text-center bold'},
                    {data: 'services', name: 'services', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.usages.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.date_min = $('[data-target="#datatable-usage"] input[name=usage_date_min]').val();
                        d.date_max = $('[data-target="#datatable-usage"] input[name=usage_date_max]').val();
                        d.operator = $('[data-target="#datatable-usage"] select[name=usage_operator]').val();
                        d.type     = $('[data-target="#datatable-usage"] select[name=type]').val();

                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableUsage) },
                    complete: function () { Datatables.complete(); },
                    error: function () {}
                }
            });

            $('[data-target="#datatable-usage"] .filter-datatable').on('change', function (e) {
                oTableUsage.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-target="#datatable-usage"] [data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-target="#datatable-usage"] [data-toggle="export-url"]').attr('href', exportUrl);
            });
        }
    });

    /**
     * Tab tyres
     */
     $(document).on('click', 'a[href="#tab-tyres"]', function(){
        $tab = $('#tab-tyres');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableTyres = $('#datatable-tyres').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'depth', name: 'depth'},
                    {data: 'date', name: 'date'},
                    {data: 'position_id', name: 'position_id'},
                    {data: 'kms', name: 'kms'},
                    {data: 'reference', name: 'reference'},
                    {data: 'brand', name: 'brand'},
                    {data: 'model', name: 'model'},
                    {data: 'size', name: 'size'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'end_kms', name: 'end_kms'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.tyres.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle = "{{ $vehicle->id }}";
                        d.type    = $('[data-target="#datatable-tyres"] select[name=type]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableTyres) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-tyres"] .filter-datatable').on('change', function (e) {
                oTableTyres.draw();
                e.preventDefault();
            });
        }
    });

    /**
     * Tab Fixed Costs
     */
    $(document).on('click', 'a[href="#tab-costs-fixed"]', function(){
        $tab = $('#tab-costs-fixed');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableFixedCosts = $('#datatable-fixed-costs').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'description', name: 'description'},
                    {data: 'type', name: 'type'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'total', name: 'total'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[4, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.fixed-costs.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.provider = $('[data-target="#datatable-fixed-costs"] select[name=provider]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableFixedCosts) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-fixed-costs"] .filter-datatable').on('change', function (e) {
                oTableFixedCosts.draw();
                e.preventDefault();
            });
        }
    });

    /**
     * Tab history
     */
    $(document).on('click', 'a[href="#tab-history"]', function(){
        $tab = $('#tab-history');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableHistory = $('#datatable-history').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'date', name: 'date'},
                    {data: 'km', name: 'km', class:'text-center'},
                    {data: 'type', name: 'type', orderable: false, searchable: false},
                    {data: 'description', name: 'description'},
                    {data: 'provider_id', name: 'provider_id', orderable: false, searchable: false},
                    {data: 'total', name: 'total'},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.vehicles.history.datatable', $vehicle->id) }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = "{{ $vehicle->id }}";
                        d.type     = $('[data-target="#datatable-history"] select[name=history_type]').val();
                        d.provider = $('[data-target="#datatable-history"] select[name=history_provider]').val();
                        d.operator = $('[data-target="#datatable-history"] select[name=history_operator]').val();
                        d.date_min = $('[data-target="#datatable-history"] input[name=history_date_min]').val();
                        d.date_max = $('[data-target="#datatable-history"] input[name=history_date_max]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableHistory) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-history"] .filter-datatable').on('change', function (e) {
                oTableHistory.draw();
                e.preventDefault();
            });
        }
    });

    /**
     * Tab checklists
     */
    $(document).on('click', 'a[href="#tab-checklists"]', function(){
        $tab = $('#tab-checklists');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableChecklist = $('#datatable-checklists').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'checklist', name: 'checklist', orderable: false, searchable: false},
                    {data: 'operator', name: 'operator'},
                    {data: 'km', name: 'km'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.vehicles.datatable.checklists') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle   = "{{ $vehicle->id }}";
                        d.checklist = $('[data-target="#datatable-checklists"] select[name=checklist_checklist]').val();
                        d.operator  = $('[data-target="#datatable-checklists"] select[name=checklist_operator]').val();
                        d.date_min  = $('[data-target="#datatable-checklists"] input[name=checklist_date_min]').val();
                        d.date_max  = $('[data-target="#datatable-checklists"] input[name=checklist_date_max]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableChecklist) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-checklists"] .filter-datatable').on('change', function (e) {
                oTableChecklist.draw();
                e.preventDefault();
            });
        }
    });

    //export selected
    $(document).on('change', '.row-select',function(){
        var $targetTable = $(this).closest('.dataTables_wrapper');

        var queryString = '';
        $($targetTable).find('input[name=row-select]:checked').each(function(i, selected){
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
        });

        var exportUrl = $targetTable.next().find('[data-toggle="export-selected"]').attr('href');
        if(typeof exportUrl !== 'undefined') {
            exportUrl = Url.removeQueryString(exportUrl);
            $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
        }
    });

    $('form.import-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type=submit]');
        $submitBtn.button('loading');

        var form = $(this)[0];
        var formData = new FormData(form);

        $('.import-inputs-area').hide();
        $('.import-results-area').html('<div class="text-center"><i class="fas fa-spin fa-circle-notch fs-30 m-b-5"></i><br/>A importar ficheiro. Aguarde...</div>');
        $.ajax({
            url: $form.attr('action'),
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function(data) {

                if (data.totalErrors == 0) {
                    Growl.success(data.feedback)
                    $('#modal-import-tolls').modal('hide');
                    oTableTolls.draw();
                } else {
                    if (data.totalErrors > 0) {
                        $('.import-results-area').html(data.html)
                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }

        }).fail(function () {
            $('.import-inputs-area').show();
            $('.import-results-area').html('');
            Growl.error500();
        }).always(function () {
            $submitBtn.button('reset');
        });
    });

    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })

     $(document).ready(function(){
         $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');
    })

    var parentTab = $('a[href="#tab-{{ Request::get("tab") }}"]').data('parent-tab');
    $('a[href="' + parentTab + '"]').trigger('click');

    $(document).on('hidden.bs.modal','#modal-remote', function(event, data){
        oTableAttachments.draw();
    })

    $('[name="brand_id"]').on('change', function(){
        getBrandModels($(this).val())
    })

    $('.btn-add-model').on('click', function(){
        getBrandModels('0')
    })

    function getBrandModels(brandId) {
        $('.brand-loading').show();

        $.post('{{ route('admin.fleet.vehicles.get.brand-models') }}', {'brand' : brandId}, function(data){
            $('#model-id').html(data);
        }).fail(function(){
            $('[name="model_id"]').html([]);
        }).always(function(){
            $('.brand-loading').hide();
            $('.select2').select2(Init.select2());
        });
    }

    var over = 0
    $('[data-popover-graph="#popover-services"]').on('mouseover', function(q){
        if(!over) {
            GetChartData();
        }
        over = 1;
    })

    var loadedAjaxData = {};
    var chartRentability;

    function respondCanvas() {
        chartRentability = {
            type: 'line',
            data: loadedAjaxData,
            options: {
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero:true}
                    }]
                },
                legend: {
                    display: true,
                    labels: {
                        boxWidth: 12,
                        padding: 5
                    }
                },
            }
        };

        var gBill = document.getElementById('graphic-rentability').getContext('2d');
        new Chart(gBill, chartRentability);
    }

    var GetChartData = function () {
        $.ajax({
            url: "{{ route('admin.fleet.stats.resume-chart', $vehicle->id) }}",
            method: 'GET',
            dataType: 'json',
            success: function (d) {
                $('.chart-loading').hide();

                loadedAjaxData = {
                    labels: d.labels,
                    datasets: [{
                        label: 'Faturação',
                        data: d.billing,
                        backgroundColor: "#19ac2b",
                        borderWidth: 1
                    },
                    {
                        label: 'Manutenções',
                        data: d.maintenance,
                        backgroundColor: "#F57C00",
                        borderWidth: 1
                    },
                    {
                        label: 'Abastecimentos',
                        data: d.fuel,
                        backgroundColor: "#f5c350",
                        borderWidth: 1
                    },
                    {
                        label: 'Outras Despesas',
                        data: d.others,
                        backgroundColor: "#8543de",
                        borderWidth: 1
                    }]
                },

                respondCanvas();
            }
        });
    };


</script>
@stop