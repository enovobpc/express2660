<div class="modal in" id="modal-export-yearly-grouped-type" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.export.invoices.purchase.anual.grouped.type'], 'method' => 'get']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Exportar Listagem Anual</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {{ Form::label('year', 'Ano') }}
                    {{ Form::number('year', date('Y'), ['class' => 'form-control date', 'required', 'maxlength' => 4]) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-submit">
                    Exportar
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div> 
</div>