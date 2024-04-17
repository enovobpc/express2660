{{ Form::open(['route' => 'admin.shipments.expenses.import.store', 'class' => 'import-form','files' => true]) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Importar Encargos</h4>
</div>
<div class="modal-body">
    <div class="row row-10 import-inputs-area">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('import_method', 'Tipo Ficheiro', ['class' => 'control-label']) }}
                {{ Form::select('import_method',  ['' => '', 'envialia' => 'Enviália'], null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group m-b-0">
                {{ Form::label('file', 'Ficheiro a importar', ['class' => 'control-label']) }}
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">

                    <div class="form-control" data-trigger="fileinput">
                        <i class="fas fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                    </div>
                    <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new">Selecionar</span>
                        <span class="fileinput-exists">Alterar</span>
                        <input type="file" name="file" data-file-format="csv,xls,xlsx" required>
                    </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
            </div>
        </div>
    </div>
    <div class="import-results-area"></div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar...">Importar
        </button>
    </div>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());

    $('.import-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var url = $form.attr('action')


        var $submitBtn = $form.find('button[type=submit]');
        $submitBtn.button('loading');

        var form = $(this)[0];
        var formData = new FormData(form);

        $('.import-inputs-area').hide();
        $('.import-results-area').html('<div class="text-center"><i class="fas fa-spin fa-circle-notch fs-30 m-b-10"></i><br/>A importar ficheiro. Esta operação poderá demorar algum tempo...</div>');

        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function(data){

                if(data.success) {
                    Growl.success(data.feedback);
                    $('#modal-remote').modal('hide');
                } else {
                    if(data.totalErrors > 0) {
                        $('.import-results-area').html(data.html)
                    } else {
                        Growl.success(data.feedback);
                        $('#modal-remote').modal('hide');
                    }
                }

            }
        }).fail(function () {
            Growl.error500();
            $('#modal-remote').modal('hide');
        }).always(function () {
            $submitBtn.button('reset');
        });
    });
</script>