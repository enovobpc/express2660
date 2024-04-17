@section('title')
Zonas e Códigos Postais
@stop

@section('content-header')
Zonas e Códigos Postais
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Zonas e Códigos Postais</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs tabs-filter">
                    <li class="active"><a href="#tab-zipcodes-agency" data-toggle="tab"><i class="fas fa-warehouse"></i> Zonas Atuação</a></li>
                    <li><a href="#tab-zipcodes-blocked" data-toggle="tab"><i class="fas fa-ban"></i> Zonas Bloqueadas</a></li>
                    <li><a href="#tab-zipcodes-remote" data-toggle="tab"><i class="fas fa-road"></i> Zonas Remotas</a></li>
                    <li><a href="#tab-zipcodes" data-toggle="tab"><i class="fas fa-globe"></i> Todos Códigos</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-zipcodes-agency">
                        @include('admin.zip_codes.agencies.tabs.agency')
                    </div>
                    <div class="tab-pane" id="tab-zipcodes-remote">
                        @include('admin.zip_codes.agencies.tabs.remote')
                    </div>
                    <div class="tab-pane" id="tab-zipcodes-blocked">
                        @include('admin.zip_codes.agencies.tabs.blocked')
                    </div>
                    <div class="tab-pane" id="tab-zipcodes">
                        @include('admin.zip_codes.agencies.tabs.zip_codes')
                    </div>
                </div>
            </div>
        </div>
    </div>
   @include('admin.zip_codes.agencies.modals.import')  
   @include('admin.zip_codes.agencies.modals.import_from_agency') 
@stop

@section('scripts')
<script type="text/javascript">
    var oTableZipCodes, oTableZipCodesAgency, oTableRemoteZones, oTableBlockedZones;

    $(document).ready(function () {

        oTableZipCodesAgency = $('#datatable-zipcodes-agency').DataTable({
            columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'zip_code', name: 'zip_code', class: 'text-center'},
                    {data: 'country', name: 'country', class: 'text-center', orderable: false, searchable: false},
                    {data: 'city', name: 'city'},
                    {data: 'agency', name: 'agency', orderable: false, searchable: false},
                    {data: 'kms', name: 'kms', class: 'text-center'},
                    {data: 'services', name: 'services', orderable: false, searchable: false},
                    {data: 'is_regional', name: 'is_regional', class: 'text-center'},
                    {data: 'provider_id', name: 'provider_id', class: 'text-center'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.zip-codes.agencies.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.agency    = $('#tab-zipcodes-agency select[name=agency]').val()
                        d.provider  = $('#tab-zipcodes-agency select[name=provider]').val()
                        d.country   = $('#tab-zipcodes-agency select[name=country]').val()
                        d.district  = $('#tab-zipcodes-agency select[name=district]').val()
                        d.county    = $('#tab-zipcodes-agency select[name=county]').val()
                        d.regional  = $('#tab-zipcodes-agency select[name=regional]').val()
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableZipCodesAgency) },
                    complete: function () { Datatables.complete(); }
                }
        });

        $('#tab-zipcodes-agency .filter-datatable').on('change', function (e) {
            oTableZipCodesAgency.draw();
            e.preventDefault();
        });

        /**
         * Remote Zones
         */
         oTableRemoteZones = $('#datatable-zipcodes-remote').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'country', name: 'country', class: 'text-center', orderable: false, searchable: false},
                {data: 'zip_codes', name: 'zip_codes'},
                {data: 'services', name: 'services'},
                {data: 'provider', name: 'provider', orderable: false, searchable: false},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[8, "desc"]],
            ajax: {
                url: "{{ route('admin.zip-codes.zones.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.type = 'remote'
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableRemoteZones) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-zipcodes-remote .filter-datatable').on('change', function (e) {
            oTableRemoteZones.draw();
            e.preventDefault();
        });

        /**
         * Blocked zones
         */
         oTableBlockedZones = $('#datatable-zipcodes-blocked').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'country', name: 'country', class: 'text-center', orderable: false, searchable: false},
                {data: 'zip_codes', name: 'zip_codes'},
                {data: 'services', name: 'services'},
                {data: 'provider', name: 'provider', orderable: false},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[8, "desc"]],
            ajax: {
                url: "{{ route('admin.zip-codes.zones.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.type = 'blocked'
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableBlockedZones) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-zipcodes-blocked .filter-datatable').on('change', function (e) {
            oTableBlockedZones.draw();
            e.preventDefault();
        });

        /**
         * All Zip Codes
         */
        oTableZipCodes = $('#datatable-zipcodes').DataTable({
            columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'country', name: 'country', class: 'text-center', orderable: false, searchable: false},
                    {data: 'zip_code', name: 'zip_code', class: 'text-center'},
                    {data: 'city', name: 'city'},
                    {data: 'postal_designation', name: 'postal_designation'},
                    {data: 'state', name: 'state'},
                    {data: 'district_code', name: 'district_code'},
                    {data: 'county_code', name: 'county_code'},
                    {data: 'address', name: 'address'},
                    @if(Auth::user()->isAdmin())
                    {data: 'source', name: 'source'},
                    @endif
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.zip-codes.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.country   = $('#tab-zipcodes select[name=zp_country]').val(),
                        d.district  = $('#tab-zipcodes select[name=zp_district]').val(),
                        d.county    = $('#tab-zipcodes select[name=zp_county]').val()
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableZipCodes) },
                    complete: function () { Datatables.complete(); }
                }
        });

        
    });

    $('#tab-zipcodes .filter-datatable').on('change', function (e) {
            oTableZipCodes.draw();
            e.preventDefault();
        });
</script>
@stop