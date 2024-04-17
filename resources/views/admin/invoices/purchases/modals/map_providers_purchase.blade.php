<?php
$dt = \Jenssegers\Date\Date::today();
$startDate = $dt->startOfYear()->format('Y-m-d');
$endDate   = $dt->endOfYear()->format('Y-m-d');
?>
<div class="modal modal-filter-dates" id="modal-map-providers-purchase">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.printer.invoices.purchase.map', 'unpaid'], 'method' => 'GET', 'target' => '_blank']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-file-refresh"></i> Listagem pendentes por fornecedor</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('date_min', 'Data Inicial', ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('start_date', $startDate, ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('date_max', 'Data Final', ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('end_date', $endDate, ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>