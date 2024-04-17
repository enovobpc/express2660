{{ Form::model($trip, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="fas fa-sync"></i> <i class="fas fa-truck-loading"></i> @trans('Troca de reboque ou motorista')</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-5">
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group is-required">
                        {{ Form::label('date', __('Data troca'), ['class' => 'control-label']) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('date', null, ['class' => 'form-control datepicker', 'required']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group is-required">
                        {{ Form::label('hour', __('Hora'), ['class' => 'control-label']) }}
                        {{ Form::time('hour', null, ['class' => 'form-control hourpicker', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="row row-0">
                        <div class="col-sm-7">
                            <div class="form-group">
                                {{ Form::label('city', __('Localidade'), ['class' => 'control-label']) }}
                                {{ Form::text('city', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                {{ Form::label('country', __('País'), ['class' => 'control-label']) }}
                                {{ Form::select('country', ['' => '']+trans('country'), null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('operator_id', __('Motorista'), ['class' => 'control-label']) }}
                        {!! Form::select('operator_id', [''=>''] + $operators, null, ['class' => 'form-control select2', 'required']) !!}
                    </div>
                    <div class="form-group is-required">
                        {{ Form::label('vehicle', __('Viatura')) }}
                        {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('assistants[]', __('Acompanhantes'), ['class' => 'control-label']) }}
                        {!! Form::select('assistants[]', $operators, $trip->assistants, ['class' => 'form-control select2', 'multiple']) !!}
                    </div>

                    @if($trailers)
                        <div class="form-group">
                            {{ Form::label('trailer', __('Reboque')) }}
                            {{ Form::select('trailer', ['' => ''] + $trailers, null, ['class' => 'form-control select2', 'data-placeholder' => 'Manter']) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group m-0">
                {{ Form::label('obs', __('Observações')) }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .select2-multiple').select2(Init.select2Multiple())
    $('.modal .datepicker').datepicker(Init.datepicker());

    $('.modal [name="period_id"]').on('change', function() {
        var start = $(this).find('option:selected').data('start');
        var end   = $(this).find('option:selected').data('end');

        $('.modal [name="start_hour"]').val(start).trigger('change');
        $('.modal [name="end_hour"]').val(end).trigger('change');
    });
</script>
