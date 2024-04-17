<div class="modal" id="modal-mass-print">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.printer.billing.customers.shipments.summary.all', 'month' => $month, 'year' => $year], 'class' => 'mass-print-form']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Imprimir em massa</h4>
            </div>
            <div class="modal-body">
                <h4>Pretende imprimir todos os ficheiros de resumo de <span class="month">{{ trans('datetime.month.'.$month) }}</span> <span class="year">{{ $year }}</span>?</h4>
                <p class="text-red m-t-10 m-b-0">
                    <i class="fas fa-exclamation-triangle"></i>
                    Este processo vai demorar alguns minutos. Poderá continuar a utilizar a aplicação normalmente, será
                    notificado para <b>{{ Auth::user()->email }}</b> quando o ficheiro estiver disponível para download.
                </p>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary"
                            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gerar ficheiro...">Gerar ficheiro
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
