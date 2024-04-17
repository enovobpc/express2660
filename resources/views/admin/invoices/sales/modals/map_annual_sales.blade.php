<?php
$dt = \Jenssegers\Date\Date::today();
$startDate = $dt->startOfYear()->format('Y-m-d');
$endDate   = $dt->endOfYear()->format('Y-m-d');
?>
<div class="modal modal-filter-dates" id="modal-map-annual-sales">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.printer.invoices.customers.maps', 'yearly'], 'method' => 'GET', 'target' => '_blank']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-file-refresh"></i> Mapa Anual de Vendas</h4>
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
    height: 100px;
    position: absolute;
    z-index: 10;"></div>
                @endif--}}
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('year', 'Ano', ['class' => 'control-label']) }}
                            {{ Form::select('year', yearsArr(2018, null, true), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group is-required">
                            {{ Form::label('group', 'Agrupar resultados por', ['class' => 'control-label']) }}
                            {{ Form::select('group', ['' => 'Apenas por Mês', 'customer' => 'Por Cliente e Mês'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group is-required m-0">
                            <div class="checkbox m-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('nodoc', 1, false) }}
                                    Incluir valores de faturação "Sem documento"
                                </label>
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
            {{ Form::hidden('mode', 'monthly') }}
            {{ Form::close() }}
        </div>
    </div>
</div>
</script>