<div class="modal" id="modal-assign-customer">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.refunds.cod.selected.assign-customers']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Associar registos ao cliente</h4>
            </div>
            <div class="modal-body">
                <div class="form-group m-b-0">
                    {{ Form::label('assign_customer_id', 'Associar registos selecionados ao cliente:') }}
                    {{ Form::select('assign_customer_id', [], null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Associar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>