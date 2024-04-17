<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Histórico do Artigo</h4>
</div>
<div class="modal-body">
    {{--<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-history">
        <li>
            <strong>Ação</strong>
            {{ Form::select('action', array('' => 'Todos') + trans('admin/logistic.history.actions'), Request::has('action') ? Request::get('action') : null, array('class' => 'form-control input-sm filter-datatable')) }}
        </li>
    </ul>--}}
    <table id="datatable-history" class="table table-condensed table-hover">
        <thead>
        <tr>
            <th class="w-1">Ação</th>
            <th class="w-1">Qt</th>
            <th>Documento</th>
            <th>Observações</th>
            <th class="w-120px">Data</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable-history').DataTable({
            dom: "<'row row-0'<'col-sm-8 datatable-filters-area'><'col-sm-4'><'col-sm-12 datatable-filters-area-extended'>>" +
            "<'row row-0'<'col-sm-12'tr>>" +
            "<'row row-0'<'col-sm-7'li><'col-sm-5'p>>",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'qty', name: 'qty'},
                {data: 'document', name: 'document'},
                {data: 'obs', name: 'obs'},
                {data: 'created_at', name: 'created_at'},
            ],
            ajax: {
                url: "{{ route('account.logistic.products.datatable.history', $product->id) }}",
                type: "POST",
                data: function (d) {
                    d.action = $('.modal select[name=action]').val();
                },
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
