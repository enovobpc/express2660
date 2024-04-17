<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Gerir Grupos de Trabalho')</h4>
</div>
<div class="modal-body p-b-0">
    {{ Form::model($workgroup, $formOptions) }}
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
            </div>
        </div>

        <div class="col-sm-7">
            <div class="form-group">
                {{ Form::label('services[]', __('Serviços')) }}
                {{ Form::select('services[]', $services, null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('status[]', __('Estados')) }}
                {{ Form::select('status[]', $status, null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('sender_countries[]', __('Países de Origem')) }}
                {{ Form::select('sender_countries[]', trans('country'), null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>
        <div class="col-sm-6">
            {{ Form::label('recipient_countries[]', __('Países de Destino')) }}
            {{ Form::select('recipient_countries[]', trans('country'), null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
        </div>

        <div class="col-sm-12"></div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('pickup_routes[]', __('Rotas Recolha')) }}
                {{ Form::select('pickup_routes[]', $pickupRoutes, null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todas']) }}
            </div>
        </div>
        <div class="col-sm-6">
            {{ Form::label('delivery_routes[]', __('Rotas Entrega')) }}
            {{ Form::select('delivery_routes[]', $deliveryRoutes, null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todas']) }}
        </div>

        <div class="col-sm-12">
            <button type="submit" class="btn btn-block btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i>">Adicionar</button>
        </div>
    </div>
    {{ Form::close() }}
    <hr>
    <table id="datatable-modal" class="table table-striped table-dashed table-hover table-condensed m-b-0">
        <thead>
            <tr class="bg-gray">
                <th></th>
                <th>@trans('Designação')</th>
                <th class="w-20px">@trans('Ações')</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
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
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.users.workgroups.datatable') }}",
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


