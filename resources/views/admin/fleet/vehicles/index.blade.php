@section('title')
Viaturas
@stop

@section('content-header')
    Viaturas
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão de Frota')</li>
<li class="active">@trans('Viaturas')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-vehicles" data-toggle="tab">@trans('Viaturas')</a></li>
                    <li><a href="#tab-trailers" data-toggle="tab">@trans('Reboques')</a></li>
                    <li><a href="#tab-forklifts" data-toggle="tab">@trans('Empilhadores e Outros')</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-vehicles">
                        @include('admin.fleet.vehicles.tabs.vehicles')
                    </div>
                    <div class="tab-pane" id="tab-trailers">
                        @include('admin.fleet.vehicles.tabs.trailers')
                    </div>
                    <div class="tab-pane" id="tab-forklifts">
                        @include('admin.fleet.vehicles.tabs.forklifts')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.fleet.vehicles.modals.validities')
    @include('admin.fleet.vehicles.modals.costs_balance')
    @include('admin.fleet.vehicles.modals.print_costs_balance')
@stop

@section('scripts')
<script type="text/javascript">
    var oTableVehicles;
    var oTableTrailers;
    var oTableForklifts;

    $(document).ready(function () {

        oTableVehicles = $('#datatable-vehicles').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'license_plate', name: 'license_plate', class: 'text-center'},
                {data: 'name', name: 'name'},
                {data: 'type', name: 'type', class:'text-center'},
                /*{data: 'trailer_id', name: 'trailer_id'},*/
                {data: 'operator_id', name: 'operator_id'},

                {data: 'insurance_date', name: 'insurance_date'},
                {data: 'iuc_date', name: 'iuc_date'},
                {data: 'ipo_date', name: 'ipo_date'},
                @if (app_mode_cargo())
                {data: 'tachograph_date', name: 'tachograph_date'},
                @endif
                /*{data: 'next_review_km', name: 'next_review_km'},*/
                {data: 'counter_km', name: 'counter_km'},
                {data: 'last_location', name: 'last_location', class: 'text-center'},
                {data: 'counter_consumption', name: 'counter_consumption', class: 'text-center'},
                /*{data: 'speed', name: 'speed', class: 'text-center'},*/
                {data: 'status', name: 'status', 'class': 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'license_plate', name: 'license_plate', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.fleet.vehicles.datatable', ['tab' => 'vehicles']) }}",
                type: "POST",
                data: function (d) {
                    d.type   = $('[data-target="#datatable-vehicles"] select[name="type"]').val(),
                    d.brand  = $('[data-target="#datatable-vehicles"] select[name="brand"]').val(),
                    d.status = $('[data-target="#datatable-vehicles"] select[name="vstatus"]').val(),
                    d.operator = $('[data-target="#datatable-vehicles"] select[name="operator"]').val(),
                    d.hide_inactive = $('input[name=hide_inactive]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableVehicles) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-vehicles"] .filter-datatable').on('change', function (e) {
            oTableVehicles.draw();
            e.preventDefault();
        });

        //show concluded shipments
        $(document).on('change', '[name="hide_inactive"]', function (e) {
            oTableVehicles.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);

        });


        /**
         * Trailers
         * @type {*|jQuery}
         */
        oTableTrailers = $('#datatable-trailers').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'license_plate', name: 'license_plate', class: 'text-center'},
                {data: 'name', name: 'name'},
                {data: 'trailer_type', name: 'trailer_type'},
                {data: 'increase_roof', name: 'increase_roof'},
                {data: 'operator_id', name: 'operator_id'},
                {data: 'status', name: 'status'},
                {data: 'insurance_date', name: 'insurance_date'},
                /*{data: 'iuc_date', name: 'iuc_date'},*/
                {data: 'ipo_date', name: 'ipo_date'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'license_plate', name: 'license_plate', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.fleet.vehicles.datatable', ['tab' => 'trailers']) }}",
                type: "POST",
                data: function (d) {
                    d.t_brand = $('[data-target="#datatable-trailers"] select[name="t_brand"]').val()
                    d.t_operator = $('[data-target="#datatable-trailers"] select[name="t_operator"]').val()
                    d.t_status = $('[data-target="#datatable-trailers"] select[name="t_status"]').val()
                    d.t_hide_inactive = $('input[name=t_hide_inactive]:checked').length;
                    
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableTrailers) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-trailers"] .filter-datatable').on('change', function (e) {
            oTableTrailers.draw();
            e.preventDefault();
        });


        oTableForklifts = $('#datatable-forklifts').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'license_plate', name: 'license_plate', class: 'text-center'},
                {data: 'name', name: 'name'},
                {data: 'insurance_date', name: 'insurance_date'},
                {data: 'iuc_date', name: 'iuc_date'},
                {data: 'ipo_date', name: 'ipo_date'},
                {data: 'status', name: 'status', 'class': 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'license_plate', name: 'license_plate', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.fleet.vehicles.datatable', ['tab' => 'forklifts']) }}",
                type: "POST",
                data: function (d) {
                    d.type   = 'forklift',
                    d.brand  = $('[data-target="#datatable-forklifts"] select[name="brand"]').val(),
                    d.status = $('[data-target="#datatable-forklifts"] select[name="vstatus"]').val(),
                    d.operator = $('[data-target="#datatable-forklifts"] select[name="operator"]').val(),
                    d.hide_inactive = $('input[name=hide_inactive]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableForklifts) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-forklifts"] .filter-datatable').on('change', function (e) {
            oTableVehicles.draw();
            e.preventDefault();
        });
    });

    //export selected
    $(document).on('change', '.row-select',function(){
        var queryString = '';
        $('input[name=row-select]:checked').each(function(i, selected){
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
        });

        var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
        $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
    });

     //show concluded shipments
     $(document).on('change', '[name="t_hide_inactive"]', function (e) {
            oTableTrailers.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);

        });

    $('#modal-print-validities [type=submit], #modal-print-costs-balance [type=submit]').on('click', function(e){
        e.preventDefault();
        $(this).closest('form').submit();
        $(this).closest('.modal').modal('hide');
        $(this).button('reset');
    })
</script>
@stop