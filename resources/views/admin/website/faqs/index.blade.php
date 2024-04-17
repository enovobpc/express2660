@section('title')
    Perguntas Frequentes
@stop

@section('content-header')
    Perguntas Frequentes
@stop

@section('breadcrumb')
    <li class="active">Gestão do Website</li>
    <li class="active">Perguntas Frequentes</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-faqs" data-toggle="tab">Perguntas</a></li>
                <li><a href="#tab-categories" data-toggle="tab">Categorias</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-faqs">
                    @include('admin.website.faqs.partials.questions')
                </div>
                <div class="tab-pane" id="tab-categories">
                    @include('admin.website.faqs.partials.categories')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;
    var oTable2;

    $(document).ready(function () {

        oTable = $('#datatable-faqs').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'translation.question', name: 'translation.question'},
                {data: 'category', name: 'category', orderable: false, searchable: false},
                {data: 'is_visible', name: 'is_visible', orderable: false, searchable: false},
                {data: 'sort', name: 'sort'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.website.faqs.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.category = $('[name="category"]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        oTable2 = $('#datatable-categories').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'translations.name', name: 'translations.name'},
                {data: 'count_values', name: 'count_values', searchable: false},
                {data: 'is_visible', name: 'is_visible', orderable: false, searchable: false},
                {data: 'sort', name: 'sort'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[5, "asc"]],
            ajax: {
                url: "{{ route('admin.website.faqs.categories.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable2) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable2.draw();
            e.preventDefault();
        });
    });

</script>
@stop