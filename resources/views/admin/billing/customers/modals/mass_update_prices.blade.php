<div class="modal" id="modal-update-prices">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.billing.customers.mass.prices', 'month' => $month, 'year' => $year], 'class' => 'update-prices-form','files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Atualizar preços em massa</h4>
            </div>
            <div class="modal-body">
                <h4>Pretende atualizar o preço de todos os envios do mês de <span class="month">{{ trans('datetime.month.'.$month) }}</span> <span class="year">{{ $year }}</span>?</h4>
                <p class='text-info m-t-10px'>
                    <i class='fas fa-exclamation-circle'></i> Não serão calculados preços para envios com pagamento no destino.<br/>
                    <i class='fas fa-exclamation-circle'></i> Envios com preço bloqueado ou já faturados não serão considerados.<br/>
                    {{-- <i class='fas fa-exclamation-circle'></i> A atualização só afetará o preço de transporte e não as taxas adicionais. --}}
                </p>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary"
                            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A atualizar...">Atualizar
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
