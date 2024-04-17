{{ Form::model($type, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        @if(hasModule('budgets_animals'))
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação do Modelo') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('type', 'Tipo') }}
                {{ Form::select('type', trans('admin/budgets.types'),null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        @else
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação do Modelo') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        @endif
    </div>
    <div class="form-group">
        {{ Form::label('intro', 'Introdução') }}
        {{ Form::textarea('intro', null, ['class' => 'form-control ckeditor', 'id' => 'ckeditor1', 'rows' => 3]) }}
    </div>
    <div class="form-group">
        {{ Form::label('transport_info', 'Informação de Transporte') }}
        {{ Form::textarea('transport_info', null, ['class' => 'form-control ckeditor', 'id' => 'ckeditor2', 'rows' => 4]) }}
    </div>
    <div class="form-group">
        {{ Form::label('payment_conditions', 'Informação de Pagamento') }}
        {{ Form::textarea('payment_conditions', null, ['class' => 'form-control ckeditor', 'id' => 'ckeditor3', 'rows' => 4]) }}
    </div>
    <div class="form-group">
        {{ Form::label('geral_conditions', 'Condições Gerais') }}
        {{ Form::textarea('geral_conditions', null, ['class' => 'form-control ckeditor', 'id' => 'ckeditor4', 'rows' => 4]) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    $('.select2').select2(Init.select2());

    CKEDITOR.config.toolbar = [
        ['Bold','Italic','Underline', 'StrikeThrough', 'NumberedList','BulletedList'],
    ] ;

    CKEDITOR.replace('ckeditor1', { height: 100 });
    CKEDITOR.replace('ckeditor2', { height: 300 });
    CKEDITOR.replace('ckeditor3', { height: 200 });
    CKEDITOR.replace('ckeditor4', { height: 200 });
</script>
