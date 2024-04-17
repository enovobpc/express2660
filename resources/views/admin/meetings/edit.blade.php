{{ Form::model($meeting, $formOptions) }}
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
                <div class="col-sm-7">
                    <div class="form-group is-required">
                        {{ Form::label('customer_id', __('Cliente ou Prospect')) }}
                        @if($meeting->exists)
                            {{ Form::select('customer_id', [@$meeting->customer->id => @$meeting->customer->name], null, ['class' => 'form-control', 'required']) }}
                        @else
                            {{ Form::select('customer_id', $customer ? [@$customer->id => @$customer->name] : [], null, ['class' => 'form-control', 'required']) }}
                        @endif
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('interlocutor', __('Pessoa de Contacto')) }}
                        {{ Form::text('interlocutor', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('date', __('Data')) }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                            {{ Form::text('date', $meeting->exists ? $meeting->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group is-required">
                        {{ Form::label('hour', __('Hora')) }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="far fa-clock"></i></span>
                            {{ Form::select('hour', ['' => ''] + $hours, $meeting->exists ? $meeting->date->format('H:i') : null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('duration', __('Duração')) }}
                        {{ Form::select('duration', ['' => ''] + trans('admin/meetings.durations'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('local', __('Local da Reunião')) }}
                        {{ Form::select('local', ['' => ''] + trans('admin/meetings.places'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('objectives', __('Objetivos da Reunião')) }}
                        @foreach(trans('admin/meetings.objectives') as $id => $name)
                            <div class="checkbox m-t-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('objectives[]', $id) }}
                                    {{ $name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('occurrences', __('Acontecimentos')) }}
                        @foreach(trans('admin/meetings.occurrences') as $id => $name)
                            <div class="checkbox m-t-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('occurrences[]', $id) }}
                                    {{ $name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('charges', __('Cobranças')) }}
                        @foreach(trans('admin/meetings.charges') as $id => $name)
                            <div class="checkbox m-t-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('charges[]', $id) }}
                                    {{ $name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="p-l-30">
                <div class="row row-5">
                    <div class="col-sm-7">
                        @if(Auth::user()->hasRole([config('permissions.role.seller')]) && $sellers)
                            <div class="form-group">
                                {{ Form::label('seller_id', __('Comercial')) }}
                                {{ Form::select('seller_id', [Auth::user()->id => Auth::user()->name], null, ['class' => 'form-control select2']) }}
                            </div>
                        @elseif(!Auth::user()->hasRole([config('permissions.role.seller')]) && $sellers)
                            <div class="form-group">
                                {{ Form::label('seller_id', __('Colaborador')) }}
                                {{ Form::select('seller_id', ['' => ''] + $sellers, null, ['class' => 'form-control select2']) }}
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group is-required">
                            {{ Form::label('status', __('Estado')) }}
                            {{ Form::select('status', ['' => ''] + trans('admin/meetings.status'), null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('obs', __('Observações')) }}
                    {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 14]) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options pull-left w-65">
        <div class="checkbox">
            <label>
                {{ Form::checkbox('save_on_calendar', 1, $meeting->exists ? null : true) }}
                @if($meeting->exists)
                    @trans('Atualizar visita na agenda')
                @else
                    @trans('Registar visita na agenda')
                @endif
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $("select[name=customer_id]").select2({
        ajax: {
            url: "{{ route('admin.meetings.search.customer') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-meetings').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote-xl').modal('hide');
            } else {
                $.bootstrapGrowl(data.feedback, {type: 'error', align: 'center', width: 'auto', delay: 8000});
            }

            oTable.draw(); //update datatable
            oTableMeetings.draw();
        }).error(function () {
            $.bootstrapGrowl('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.', {type: 'error', align: 'center', width: 'auto', delay: 8000});
        }).always(function(){
            $button.button('reset');
        })
    });
</script>
