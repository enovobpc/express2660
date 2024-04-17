{{ Form::model($event, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('title', 'Designação do Evento') }}
                {{ Form::text('title', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('start', 'Data Início') }}
                <div class="input-group">
                    {{ Form::text('start', null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('start_hour', 'Hora') }}
                {{ Form::time('start_hour', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('end', 'Data Fim') }}
                <div class="input-group">
                    {{ Form::text('end', null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('end_hour', 'Hora') }}
                {{ Form::time('end_hour', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('color', 'Côr') }}
                {{ Form::select('color', $colors) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('obs', 'Observações') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="{{ route('admin.timeline.destroy', $event->id) }}"
       data-method="delete"
       data-confirm="Confirma a remoção do registo selecionado?"
       class="btn btn-danger pull-left">
        <i class="fas fa-fw fa-trash-alt"></i> Eliminar
    </a>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button class="btn btn-primary btn-submit">Gravar</button>
</div>
{{ Form::hidden('redirect', 'back')  }}
{{ Form::close() }}

{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
        $('#modal-remote .select2').select2(Init.select2());
    })
</script>
