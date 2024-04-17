{{ Form::model($faq, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('faq_category_id', 'Adicionar pergunta na categoria') }}
                {{ Form::select('faq_category_id', ['' => ''] + $categories, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group m-t-25">
                <label style="font-weight: normal !important">
                    {!! Form::checkboxTrans('is_visible', 1, $faq->exists ? null : true) !!}
                    Pergunta Visivel (<span class="text-uppercase locale-key">PT</span>)
                </label>
            </div>
        </div>
    </div>
    <div class="form-group is-required">
        {{ Form::label('question', 'Pergunta') }}
        {!! Form::textTrans('question', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group is-required">
        {{ Form::label('answer', 'Resposta') }}
        {!! Form::textareaTrans('answer', null, ['class' => 'form-control']) !!}
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
</script>

