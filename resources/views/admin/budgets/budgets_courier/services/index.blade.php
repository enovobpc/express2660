<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gerir Tipos de Serviço</h4>
</div>
<div class="modal-body p-b-0">
    {{ Form::model($type, $formOptions) }}
    <div class="row row-5">
        <div class="{{ (hasModule('budgets_animals') && hasModule('budgets_courier')) ? 'col-sm-7' : 'col-sm-10' }}">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
            </div>
        </div>
        @if(hasModule('budgets_animals') && hasModule('budgets_courier'))
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('type', 'Tipo') }}
                {{ Form::select('type', trans('admin/budgets.types'), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        @endif
        <div class="col-sm-2">
            <div style="height: 20px;"></div>
            <button type="submit" class="btn btn-block btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Adicionar</button>
        </div>
    </div>
    {{ Form::close() }}
    <table id="datatable-modal" class="table table-striped table-dashed table-hover table-condensed m-b-0">
        <thead>
            <tr class="bg-gray">
                <th></th>
                <th>Designação</th>
                <th class="w-150px">Tipo</th>
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

<script type="text/javascript">

    $('.select2').select2(Init.select2());

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
                {data: 'name', name: 'name'},
                {data: 'type', name: 'type'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.budgets.courier.services.datatable') }}",
                type: "POST",
                data: function (d) {},
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
        }).done(function(data){

            if (data.result) {
                $('.modal-ajax-form [name="name"]').val('')
                oTableModal.draw();
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
            } else {
                $.bootstrapGrowl('Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.', {type: 'error', align: 'center', width: 'auto', delay: 8000});
            }

            $submitBtn.button('reset');
        }).error(function () {
            $.bootstrapGrowl('Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.', {type: 'error', align: 'center', width: 'auto', delay: 8000});
        }).always(function () {
            $submitBtn.button('reset');
        });
    });


    $(document).on('click', '.trigger-edit-row', function(){
       $(this).closest('tr').find('.edit-datatable-field').trigger('click');
    });
</script>


