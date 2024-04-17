<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gerir Grupos de Serviço</h4>
</div>
<div class="modal-body p-b-0">
    {{ Form::model($serviceGroup, $formOptions) }}
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('code', 'Código') }}
                {{ Form::text('code', null, ['class' => 'form-control lowercase nospace', 'maxlength' => 25, 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('name', 'Icone') }}
                <div class="input-group iconpicker-container">
                    {{ Form::text('icon', $serviceGroup->icon ? $serviceGroup->icon : 'fas fa-box', ['class' => 'form-control iconpicker']) }}
                    <span class="input-group-addon" style="padding: 9px 10px;">
                        <i class="{{ $serviceGroup->icon ? $serviceGroup->icon : 'fas fa-box' }}"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="h-20px"></div>
            <button type="submit" class="btn btn-block btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i>">Adicionar</button>
        </div>
    </div>
    {{ Form::close() }}
    <table id="datatable-modal" class="table table-striped table-dashed table-hover table-condensed m-b-0">
        <thead>
            <tr class="bg-gray">
                <th></th>
                <th class="w-1"><i class="fas fa-sort-amount-asc"></i></th>
                <th>Designação</th>
                <th class="w-20px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

{{ Html::style('/vendor/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.css') }}
{{ Html::script('/vendor/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.js') }}
<script>
    $('.iconpicker').iconpicker();

    var oTableModal;

    $(document).ready(function () {
        oTableModal = $('#datatable-modal').DataTable({
                dom: "<'row row-0'<'col-sm-12 push-bottom-11'f>>" +
            "<'row row-0'<'col-sm-12'tr>>" +
            "<'row row-0'<'col-sm-5'l><'col-sm-7'p>>",
            pageLength: 10,
            stateSave: true,
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'sort', name: 'sort'},
                {data: 'name', name: 'name'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[1, "asc"]],
            ajax: {
                url: "{{ route('admin.services.groups.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableModal) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });
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
</script>


