<div class="modal" id="modal-shipment-price-details" style="
    z-index: 10000;
    margin-top: 0;
    padding-top: 80px;
    background: rgb(255 255 255 / 53%);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalhe de preços</h4>
            </div>
            <div class="modal-body p-0">
                <div class="text-center fs-18 m-t-30 m-b-30"><i class="fas fa-spin fa-circle-notch"></i> A calcular preço...</div>
            </div>
            <div class="modal-footer">
                {{--<button type="button" class="btn btn-default .btn-refresh-prices"><i class="fas fa-sync"></i> Atualizar</button>--}}
                <button type="button" class="btn btn-default btn-close pull-right">Fechar</button>
                <div class="pull-right m-r-15" style="margin-top: -2px">
                    <h5 class="m-0 pull-right m-l-15 bold fs-15">
                        <small>{{ trans('account/global.word.total') }}</small><br/>
                        <span class="billing-total"></span>
                    </h5>
                    <h5 class="m-0 pull-right m-l-15 bold fs-15">
                        <small>{{ trans('account/global.word.vat') }}</small><br/>
                        <span class="billing-vat"></span>
                    </h5>
                    <h5 class="m-0 pull-right m-l-15 bold fs-15">
                        <small>{{ trans('account/global.word.subtotal') }}</small><br/>
                        <span class="billing-subtotal"></span>
                    </h5>
                </div>

            </div>
        </div>
    </div>
</div>