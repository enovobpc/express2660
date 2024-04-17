{{ Form::model($expressService, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group is-required">
                        {{ Form::label('title', 'Descrição') }}
                        {{ Form::text('title', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('date', 'Data') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                            {{ Form::text('date', $expressService->exists ? null : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="form-group is-required">
                        {{ Form::label('customer_id', 'Cliente') }}
                        {{ Form::select('customer_id', [@$expressService->customer->id => @$expressService->customer->name], null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group m-b-0">
                        {{ Form::label('description', 'Observações') }}
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 7]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="p-l-30">
                <div class="form-group is-required">
                    {{ Form::label('status', 'Estado') }}
                    {{ Form::select('status', ['' => ''] + trans('admin/express_services.status'), null, ['class' => 'form-control select2', 'required']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('operator_id', 'Motorista') }}
                    {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                </div>

                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('km', 'Km') }}
                            {{ Form::text('km', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            {{ Form::label('vehicle', 'Matricula Viatura') }}
                            @if($vehicles)
                            {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2']) }}
                            @else
                            {{ Form::text('vehicle', null, ['class' => 'form-control']) }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('total_price', 'Preço Cliente') }}
                            <div class="input-group">
                                {{ Form::text('total_price', null, ['class' => 'form-control', 'required']) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('operator_price', 'Preço Motorista') }}
                            <div class="input-group">
                                {{ Form::text('operator_price', null, ['class' => 'form-control']) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="checkbox m-t-0 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_paid', 1) }}
                        Pago ao motorista
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options pull-left w-65">
        <div class="checkbox">
            <label>
                {{ Form::checkbox('save_on_calendar', 1, $expressService->exists ? null : true) }}
                @if($expressService->exists)
                    Atualizar expresso na agenda
                @else
                    Registar expresso na agenda
                @endif
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $("select[name=customer_id]").select2({
        ajax: {
            url: "{{ route('admin.express-services.search.customer') }}",
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
    $('.form-expressService').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote-lg').modal('hide');
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
