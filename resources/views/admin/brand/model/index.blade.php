<div class="modal-header">
    <button class="close" data-dismiss="modal" type="button">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Modelos - {{ $brand->name }}</h4>
</div>
<div class="modal-body">
    {{ Form::open($formOptions) }}
    <div class="row row-5">
        <div class="col-sm-10">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="h-20px"></div>
            <button class="btn btn-block btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i>" type="submit">Adicionar</button>
        </div>
    </div>
    {{ Form::close() }}

    <table class="table table-striped table-dashed table-hover table-condensed m-b-0" id="datatable-modal">
        <thead>
            <tr class="bg-gray">
                <th>Designação</th>
                <th class="w-20px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" type="button">Fechar</button>
</div>

<script>
    var oTableModal;
    $(document).ready(function () {
        oTableModal = $('#datatable-modal').DataTable({
                dom: "<'row row-0'<'col-sm-12 push-bottom-11'f>>" +
            "<'row row-0'<'col-sm-12'tr>>" +
            "<'row row-0'<'col-sm-5'l><'col-sm-7'p>>",
            pageLength: 10,
            stateSave: true,
            columns: [
                {data: 'name', name: 'name'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[1, "asc"]],
            ajax: {
                url: "{{ route('admin.brands.models.datatable', $brand->id) }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableModal) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.modal-ajax-form').on('submit', function(e) {
            e.stopPropagation();
            e.preventDefault();

            var action = $(this).attr('action');
            var method = $(this).attr('method');
            var form = $(this)[0];
            var formData = new FormData(form);

            var $submitBtn = $(this).find('button[type=submit]');
            $submitBtn.button('loading');

            $.ajax({
                url: action,
                data: formData,
                type: method,
                contentType: false,
                processData: false,
                success: function(data){
                    if (data.result) {
                        $('.modal-ajax-form [name="name"]').val('')
                        oTableModal.draw();
                        Growl.success(data.feedback);
                    } else {
                        Growl.error500()
                    }

                    $submitBtn.button('reset');
                }
            }).fail(function () {
                Growl.error500()
            }).always(function () {
                $submitBtn.button('reset');
            });
        });

        $(document).on('click', '.trigger-edit-row', function(){
            $(this).closest('tr').find('.edit-datatable-field').trigger('click');
        });
    });
</script>