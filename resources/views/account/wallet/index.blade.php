@section('title')
    Histórico de pagamentos -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        <li>
            <a href="{{ route('account.wallet.create') }}" class="btn btn-sm btn-black pull-right m-0"
               data-toggle="modal"
               data-target="#modal-remote-xs">
                <i class="fas fa-plus"></i> Adicionar Saldo
            </a>
        </li>
    </ul>
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th class="w-40px">Método</th>
                    <th>Descrição</th>
                    <th class="w-80px">Valor</th>
                    <th class="w-1">Estado</th>
                    <th class="w-80px">Data</th>
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
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'method', name: 'method', class:'text-center'},
                {data: 'description', name: 'description'},
                {data: 'value', name: 'value'},
                {data: 'status', name: 'status', class:'text-center'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('account.wallet.datatable') }}",
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