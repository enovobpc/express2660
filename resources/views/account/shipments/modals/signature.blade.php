<div class="modal" id="modal-signature" style="z-index: 10000; margin-top: 20px;">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account/global.word.delivery-proof') }}</h4>
            </div>
            <div class="modal-body">
               <div class="receiver" style="display: none">
                    <p>{{ trans('account/global.word.received-by') }}: <b></b></p>
               </div>
                <div class="delivery-proof-img">
                    <img src=""/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default confirm-budget-dimensions">{{ trans('account/global.word.close') }}</button>
            </div>
        </div>
    </div>
</div>