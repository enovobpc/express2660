{{ Form::model($resolution, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('resolution_type_id', 'Tipo de Resolução') }}
        {{ Form::select('resolution_type_id', $resolutionsTypes, null, ['class' => 'form-control select2', 'required']) }}
    </div>
    <div class="form-group m-0">
        {{ Form::label('obs', 'Observações ou anotações') }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left modal-feedback text-red m-t-5"></div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar
        </button>
    </div>
</div>
{{ Form::close() }}
<script>

    $('.select2').select2(Init.select2());

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-incidence-resolution').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote').modal('hide');
                $('.table-incidence-resolutions').replaceWith(data.html);
            } else {
                $('.table-incidence-resolutions .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }
        }).error(function () {
            $('.table-incidence-resolutions .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno.');
        }).always(function(){
            $button.button('reset');
        })
    });


</script>