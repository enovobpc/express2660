<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="far fa-clock"></i> @trans('Histórico de Acessos')</h4>
</div>
<div class="modal-body">
    <table class="table table-condensed" id="datatable-logs">
        <thead>
            <tr class="bg-gray-light">
                <th class="w-130px">@trans('Data')</th>
                <th>@trans('Utilizador')</th>
                <th class="w-100px">@trans('IP Acesso')</th>
                <th class="w-1">@trans('Remember')</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>

<style>
    #modal-remote-lg .table .table-condensed>tbody>tr>th,
    #modal-remote-lg .table .table-condensed>tbody>tr>td {
        padding: 0;
    }
</style>

<script>
    $(document).ready(function () {

        var oTable = $('#datatable-logs').DataTable({
            dom: "<'row row-0'<'col-md-6 col-sm-6 datatable-filters-area'><'col-sm-12 col-md-12'f><'col-sm-12 datatable-filters-area-extended'>>" +
                "<'row row-0'<'col-sm-12'tr>>" +
                "<'row row-0'<'col-sm-5'l><'col-sm-7'p>>",
            serverSide: false,
            order: [[0, "desc"]],
            data: {!! json_encode(@$logs) !!},
            columns: [
                { data: "created_at" },
                { data: "user" },
                { data: "ip"},
                { data: "remember", class: "text-center"},
            ]
        });

        $('[data-target="#datatable-providers"] .filter-datatable').on('change', function (e) {
            e.preventDefault();
            var providerType = $(this).val();
            oTable.search(providerType).draw();
        });
    });
</script>