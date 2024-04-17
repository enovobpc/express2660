<div class="modal" id="modal-mass-refund-destroy">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.refunds.customers.selected.destroy'], 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Cancelar reembolsos selecionados</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-t-0">
                    Confirma o cancelamento dos reembolsos selecionados?
                </h4>
                <hr class="m-t-10 m-b-10"/>
                <div class="form-group is-required m-b-0">
                    {{ Form::label('obs', 'Motivo de anulação ou cancelamento:') }} {!! tip('Indique o motivo pelo qual está a anular ou cancelar os reembolsos selecionados.') !!}
                    {{ Form::text('obs', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger">Cancelar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>