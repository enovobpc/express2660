<div class="modal" id="modal-assign-service">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.shipments.selected.update', 'service']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Associar registos ao serviço</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {{ Form::label('assign_service_id', 'Associar registos selecionados ao serviço:') }}
                    {{ Form::select('assign_service_id', ['' => ''] + $services, null, ['class' => 'form-control select2']) }}
                </div>
                <div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('calc_prices', 1, true) }}
                        Calcular de novo os preços de cada envio.
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spin fa-circle-o-notch'></i> Aguarde...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>