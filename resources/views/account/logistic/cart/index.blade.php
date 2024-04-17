@section('title')
    Encomendas
@stop

@section('account-content')
<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <span class="caret"></span>
        </button>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : '' }}" data-target="#datatable">
    <ul class="list-inline pull-left">
        <li style="width: 120px" class="input-sm">
            <strong>{{ trans('account/global.word.status') }}</strong><br/>
            {{ Form::select('status', ['' => 'Todas'] + $status, Request::has('status') ? Request::get('status') : null, array('class' => 'form-control filter-datatable select2')) }}
        </li>
        @if(Auth::guard('customer')->user()->is_commercial)
            <li style="width: 150px" class="input-sm">
                        <strong>{{ trans('account/global.word.department') }}</strong><br/>
                        {{ Form::select('department', ['' => 'Todas'] + $departments, Request::has('department') ? Request::get('department') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
        @endif
       
    </ul>
    <div class="clearfix"></div>
</div>

    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th class="w-50px"></th>
                    <th class="w-100px">Referência</th>
                    <th class="w-200px">Estado</th>
                    <th class="w-100px">Envio Assoc.</th>
                    <th class="w-180px">Quantidade Produtos</th>
                    <th class="w-180px">Data de criação</th>
                    <th class="w-180px">Registado por</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

@stop

@section('styles')
    {{ Html::style('vendor/magnific-popup/dist/magnific-popup.css') }}
@stop

@section('scripts')
{{ Html::script('vendor/magnific-popup/dist/jquery.magnific-popup.js') }}
<script type="text/javascript">
    var oTableCartOrders;
    $(document).ready(function () {

        oTableCartOrders = $('#datatable').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'reference', name: 'reference'},
                {data: 'status', name: 'status'},
                {data: 'shipment', name: 'shipment'},
                {data: 'qty', name: 'qty'},
                {data: 'created_at', name: 'created_at'},
                {data: 'submitted_by', name: 'submitted_by'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[4, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.logistic.cart.datatable') }}",
                data: function (d) {
                    d.status    = $('select[name=status]').val();
                    d.department    = $('select[name=department]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableCartOrders) },
                complete: function () {
                    Datatables.complete();
                    $('.preview-img').magnificPopup({
                        type:'image'
                    });
                }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTableCartOrders.draw();
            e.preventDefault();

            /*var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);*/
        });
    });
</script>
@stop