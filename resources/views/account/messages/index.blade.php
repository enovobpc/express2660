@section('title')
    Caixa de Mensagens -
@stop

@section('account-content')
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th class="w-80px">{{ trans('account/global.word.date') }}</th>
                    <th>{{ trans('account/global.word.subject') }}</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            dom: "<'row row-0'<'col-sm-12'tr>>" +
                    "<'row row-0'<'col-sm-5'li><'col-sm-7'p>>",
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'subject', name: 'subject'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('account.messages.datatable') }}",
                type: "POST"
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop