<div class="modal" id="modal-assign-provider">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.shipments.selected.update', 'provider']]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="float: right">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Alterar fornecedor em massa</h4>
            </div>
            <div class="modal-body">
                <div class="form-group is-required">
                    {{ Form::label('assign_provider_id', 'Associar envios ao fornecedor', ['class' => 'control-label']) }}
                    {{ Form::select('assign_provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2', 'required']) }}
                </div>
                <div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('auto_submit', 1, true) }}
                        Submeter via webservice se disponível
                    </label>
                </div>
                <div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('calc_prices', 1, true) }}
                        Calcular de novo o preço de custo de cada envio.
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>