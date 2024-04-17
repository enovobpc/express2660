{{ Form::model($location, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('code', 'Código') }}
                {{ Form::text('code', null, ['class' => 'form-control uppercase nospace', 'required', 'maxlength' => '5']) }}
            </div>
        </div>
        <div class="col-sm-10">
            <div class="form-group is-required">
                {{ Form::label('name', 'Descrição') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('warehouse_id', 'Armazém') }}
                {{ Form::select('warehouse_id', ['' => ''] + $warehouses, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('operator_id', 'Motorista associado') }}
                {{ Form::select('operator_id', ['' => ''] + $drivers, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('color', 'Idêntificador') }}<br/>
                {{ Form::select('color', trans('admin/global.colors')) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}


{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $('.modal .select2').select2(Init.select2());
    $(document).ready(function () {
        $('.modal select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-rack').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote').modal('hide');
            } else {
                $('.form-billing .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).error(function () {
            $('.form-billing .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function(){
            $button.button('reset');
        })
    });
</script>

