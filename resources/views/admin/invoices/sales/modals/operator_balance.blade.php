<div class="modal modal-filter-dates" id="modal-print-operator-balance">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.printer.invoices.operator-accountability', 'method' => 'GET', 'target' => '_blank']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-file-refresh"></i> Prestação de contas por colaborador</h4>
            </div>
            <div class="modal-body">
                <p class="m-t-0 bold">Listar prestação de contas entre:</p>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('start_date', 'Data Inicial', ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('start_date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('end_date', 'Data Final', ['class' => 'control-label']) }}
                            <div class="input-group">
                                <?php $dt = Date::today(); $dt = $dt->addDays(30)->format('Y-m-d')?>
                                {{ Form::text('end_date', $dt, ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {{ Form::label('operator', 'Colaborador', ['class' => 'control-label']) }}
                            {{ Form::select('operator', ['' => 'Todos'] + $operators, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-submit"><i class="fas fa-print"></i> Imprimir</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
</script>