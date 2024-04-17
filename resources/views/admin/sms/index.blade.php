@section('title')
    Envio de SMS
@stop

@section('content-header')
    Envio de SMS
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Envio de SMS</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-history" data-toggle="tab">Histórico de Envios</a></li>
                    @if(hasPermission('sms_packs'))
                    <li><a href="#tab-packs" data-toggle="tab">Pacotes de SMS</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-history">
                        @include('admin.sms.tabs.history')
                    </div>
                    @if(hasPermission('sms_packs'))
                    <div class="tab-pane" id="tab-packs">
                        @include('admin.sms.tabs.packs')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTableHistory, oTablePacks;

        $(document).ready(function () {
            oTableHistory = $('#datatable-history').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'source', name: 'source', class: 'text-center'},
                    {data: 'to', name: 'to'},
                    {data: 'message', name: 'message'},
                    {data: 'status', name: 'status'},
                    {data: 'sms_parts', name: 'sms_parts', class:'text-center'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[7, "desc"]],
                ajax: {
                    url: "{{ route('admin.sms.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.date_min = $('[data-target="#datatable-history"] [name=date_min]').val();
                        d.date_max = $('[data-target="#datatable-history"] [name=date_max]').val();
                        d.customer = $('[data-target="#datatable-history"] select[name=customer]').val();
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

            @if(hasPermission('sms_packs'))
            oTablePacks = $('#datatable-packs').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'total_sms', name: 'total_sms', class: 'text-center'},
                    {data: 'remaining_sms', name: 'remaining_sms', class: 'text-center'},
                    {data: 'price_un', name: 'price_un', class: 'text-center'},
                    {data: 'total', name: 'total', class: 'text-center'},
                    {data: 'reference', name: 'reference'},
                    {data: 'is_active', name: 'is_active', class: 'text-center'},
                    {data: 'buy_by', name: 'buy_by'},
                    @if(Auth::user()->isAdmin())
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    @endif
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.sms.packs.datatable') }}",
                    type: "POST",
                    data: function (d) {
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTablePacks) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-packs"] .filter-datatable').on('change', function (e) {
                oTablePacks.draw();
                e.preventDefault();
            });
            @endif
        });

        @if(Request::has('action'))
        $(document).ready(function() {
            $('.btn-new-pack').trigger('click')
        })
        @endif
    </script>
@stop