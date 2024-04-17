{{ Form::model($incidence, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="row row-5">
                <div class="col-sm-6">
                    @if(!empty($incidence->vehicle))
                        <div class="form-group is-required">
                            {{ Form::label('vehicle_id', __('Viatura')) }}
                            {{ Form::text('vehicle_name', $incidence->vehicle->name, ['class' => 'form-control', 'disabled'] ) }}
                            {{ Form::hidden('vehicle_id', $incidence->vehicle->id) }}
                        </div>
                    @else
                        <div class="form-group is-required">
                            {{ Form::label('vehicle_id', __('Viatura')) }}
                            {{ Form::select('vehicle_id', ['' => ''] + $vehicles, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    @endif
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('km', __('Km atuais')) }}
                        <div class="input-group">
                            {{ Form::text('km', null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">km</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('date', __('Data Ocorrência')) }}
                        <div class="input-group">
                            {{ Form::text('date', $incidence->exists ? $incidence->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                            <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group is-required">
                        {{ Form::label('title', __('Título')) }}
                        {{ Form::text('title', null, ['class' => 'form-control', 'required', 'placeholder' => __('Ex.: Vidro traseiro partido.')]) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('operator_id', __('Motorista')) }}
                        {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('description', __('Descrição da ocorrência')) }}
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 4]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group" style="display: {{ ($incidence->exists && $incidence->filepath) ?  'none' : 'block' }};" }}>
                {{ Form::label('name', __('Ficheiros em anexo')) }}
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput">
                        <i class="fas fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                    </div>
                    <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">@trans('Procurar...')</span>
                            <span class="fileinput-exists">@trans('Alterar')</span>
                            <input type="file" name="file">
                        </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@trans('Remover')</a>
                </div>
            </div>
            <hr/>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('delete_file') }}
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());

    $(document).on('click', '.btn-delete', function(e){
        e.preventDefault();
        $(this).closest('.form-group').hide();
        $(this).closest('.form-group').prev().show();
        $('[name="delete_file"]').val(1);
    })

</script>

