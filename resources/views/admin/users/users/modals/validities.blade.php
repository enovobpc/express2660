<div class="modal" id="modal-print-validities">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.printer.users.validities', 'method' => 'GET', 'target' => '_blank']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-file-refresh"></i> @trans('Resumo de documentos a expirar')</h4>
            </div>
            <div class="modal-body">
                <p class="m-t-0 bold">@trans('Listar documentos e certificados a expirar entre as datas:')</p>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group is-required m-b-0">
                            {{ Form::label('start_date', __('Data Inicial'), ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('start_date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group is-required m-b-0">
                            {{ Form::label('end_date', __('Data Final'), ['class' => 'control-label']) }}
                            <div class="input-group">
                                <?php $dt = Date::today(); $dt = $dt->addDays(30)->format('Y-m-d')?>
                                {{ Form::text('end_date', $dt, ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                <button type="submit" class="btn btn-primary">@trans('Imprimir')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
</script>