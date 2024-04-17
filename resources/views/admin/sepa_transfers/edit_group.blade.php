<?php
if($paymentGroup->exists) {
    $processingDate = $paymentGroup->processing_date->format('Y-m-d');
} else {
    $processingDate = Date::today()->addDays(2)->format('Y-m-d');
}

?>
{{ Form::model($paymentGroup, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('processing_date', 'Data Processamento') }}
                <div class="input-group">
                    {{ Form::text('processing_date', $processingDate, ['class' => 'form-control', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('bank_id', 'Nossa Conta') }}
                {{ Form::select('bank_id', ['' => ''] + $banks, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        @if(@$payment->type == 'dd')
            <div class="col-sm-2">
                <div class="form-group is-required">
                    {{ Form::label('service_type', 'Serviço') }}
                    {{ Form::select('service_type', ['' => '', 'CORE' => 'Core', 'B2B' => 'B2B'], null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('sequence_type', 'Sequencia') }}
                    {{ Form::select('sequence_type', ['' => ''] + trans('admin/billing.sepa-sequence-types'), null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
        @else
            <div class="col-sm-5">
                <div class="form-group is-required">
                    {{ Form::label('category', 'Categoria Transferência') }}
                    {{ Form::select('category', ['' => ''] + trans('admin/billing.sepa-category-types'), null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
    $('.modal input').iCheck(Init.iCheck())


    $('.modal [name="processing_date"]').datepicker({
        format: 'yyyy-mm-dd',
        language: 'pt',
        todayHighlight: true,
        startDate: '{{ $processingDate }}'
    });

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-sepa-group').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $(this);
        var $btn = $form.find('button[type=button]');

        $btn.button('loading');

        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: $form.attr('method'),
            success: function(data) {
                if(data.result) {
                    Growl.success(data.feedback);
                    $('.modal .sepa-groups-list').html(data.html)
                    $('#modal-remote').modal('hide');
                } else {
                    Growl.error(data.feedback);
                }
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $btn.button('reset');
        });

    });
</script>
