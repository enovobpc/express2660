<div class="modal fade" id="modal-assign-customer">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.billing.shipments.selected.assign-customers']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Associar registos ao cliente</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {{ Form::label('assign_customer_id', 'Associar registos selecionados ao cliente:') }}
                    {{ Form::select('assign_customer_id', [], null, ['class' => 'form-control']) }}
                </div>
                <div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('calc_prices', 1, true) }}
                        Calcular de novo os preços de cada envio.
                    </label>
                </div>
                <div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::radio('update_name', 'sender', false) }}
                        Atualizar dados do <u>remetente</u> de acordo com os dados do cliente.
                    </label>
                </div>
                <div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::radio('update_name', 'recipient', false) }}
                        Atualizar dados do <u>destinatário</u> de acordo com os dados do cliente.
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>