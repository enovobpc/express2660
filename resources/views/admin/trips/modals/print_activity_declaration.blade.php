{{ Form::open(['route' => 'admin.printer.trips.activity-declaration', 'method' => 'GET', 'target' => '_blank', 'class' => 'form-print-declaration']) }}
<div class="modal-header">
    <h4 class="modal-title">@trans('Imprimir Declaração de Atividade')</h4>
</div>
<div class="modal-body">
    @if(!@$trip->operator_id)
        <div class="text-center m-t-15  m-b-15">
            <h4 class="text-red">
                <i class="fas fa-exclamation-triangle"></i> @trans('Não é possível imprimir a declaração.')
            </h4>
            <p class="m-0">
                @trans('A viagem não tem um motorista atribuido.')
            </p>
        </div>
    @else
        @if(!$trip->edit_modal || @$trip->last_manifest_hours > 72)
            <h4>
                Passaram <span class="days">{{ @$trip->last_manifest_days }}</span> dias desde a última viagem de <span class="operator-name">{{ @$trip->operator->name }}</span>.
                @if(@$trip->last_manifest_code)
                    <br/>
                    <small class="italic">@trans('É obrigatória a impressão de declaração de atividade.')</small>
                @else
                    <br/><small class="italic">
                        @trans('É obrigatória a impressão de declaração de atividade.')'
                    </small>
                @endif
            </h4>
            <hr style="margin: 25px 0 15px 0"/>
        @endif

        <div class="row">
            <div class="col-sm-5">
                <div class="form-group">
                    {{ Form::label('last_activity_date', __('Última viagem')) }}
                    <small class="italic">(@trans('Viagem') <span class="last-manifest"><a href="{{ route('admin.trips.show', @$trip->id) }}" target="_blank">{{ @$trip->last_manifest_code }}</a></span>)</small>
                    <div class="row row-0 m-t-10">
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                {{ Form::text('last_date', @$trip->last_date, ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-left: -1px">
                            {{ Form::select('last_hour', [''=>''] + listHours(5), @$trip->last_hour, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('next_date', __('Próxima viagem')) }}
                    <div class="row row-0">
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                {{ Form::text('next_date', $trip->pickup_date ? $trip->pickup_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-left: -1px">
                            {{ Form::select('next_hour', [''=>''] + listHours(5), $trip->start_hour, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="form-group input-xs">
                    {{ Form::label('responsable', __('Gestor Tráfego')) }}
                    {{ Form::select('responsable', [Auth::user()->id => Auth::user()->name] + ($managers ?? []), null, ['class' => 'form-control select2']) }}
                </div>

                <div class="form-group input-xs">
                    {{ Form::label('operator', __('Motorista')) }}
                    {{ Form::select('operator', $operators, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-7">
                {{ Form::label('', __('Justificação de atividade')) }}
                @foreach(trans('admin/shipments.inactivity-reasons') as $key => $reason)
                    <div class="checkbox">
                        <label>
                            {{ Form::checkbox('inactivity_reasons[]', $key, $key == 3, ['style' => 'margin-left: -21px !important;']) }}
                            {{ $reason }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
<div class="modal-footer">
    {{-- {{ Form::hidden('operator', $trip->operator_id, ['class' => 'form-control']) }} --}}
    @if($trip->edit_modal)
        <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
        @if(@$trip->operator_id)
        <button type="button" class="btn btn-sm btn-primary btn-submit" target="_blank"><i class="fas fa-print"></i> @trans('Imprimir')</button>
        @endif
    @else
        <button type="button" class="btn btn-sm btn-default btn-cancel">@trans('Cancelar')</button>
        <button type="button" class="btn btn-sm btn-default btn-confirm-yes"><i class="fas fa-clock"></i> @trans('Gravar e Imprimir mais tarde')</button>
        <button type="submit" class="btn btn-sm btn-primary btn-print" target="_blank"><i class="fas fa-print"></i> @trans('Gravar e Imprimir')</button>
    @endif
</div>
{{ Form::close() }}

@if($trip->edit_modal)
<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $('.modal .form-print-declaration .btn-submit').on('click', function(){

        var requiredEmpty = false;
        $('.modal .form-print-declaration [required]').each(function() {
            if($(this).val() == '') {
                requiredEmpty = true;
            }
        })

        if(!$('[name="inactivity_reasons[]"]:checked').length) {
            requiredEmpty = true;
        }

        if(requiredEmpty) {
            Growl.warning('Existem campos não preenchidos.')
        } else {
            $(this).closest('form').submit()
            $('#modal-remote').modal('hide')
        }
    });
</script>
@endif