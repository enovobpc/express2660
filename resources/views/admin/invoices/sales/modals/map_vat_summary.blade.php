<?php
$dt = \Jenssegers\Date\Date::today();
$startDate = $dt->startOfMonth()->format('Y-m-d');
$endDate = $dt->endOfMonth()->format('Y-m-d');
?>
<div class="modal modal-filter-dates" id="modal-map-vat-summary">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.printer.invoices.customers.maps', 'vat'], 'method' => 'GET', 'target' => '_blank']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-file-refresh"></i> Mapa resumo de Taxas IVA</h4>
            </div>
            <div class="modal-body">
                {{--@if(!hasModule('invoices-advanced'))
                    <h4 class="text-center m-b-15">
                        <i class="fas fa-lock fs-20 m-b-10"></i>
                        <br/>
                        Esta funcionalidade não está incluída no valor da sua licença.
                    </h4>
                    <div class="overflow-div" style="opacity: 0.6;
    background: #fff;
    width: 95%;
    height: 80px;
    position: absolute;
    z-index: 10;"></div>
                @endif--}}
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('start_date', 'Data Inicial', ['class' => 'control-label']) }}
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
                            {{ Form::label('end_date', 'Data Final', ['class' => 'control-label']) }}
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