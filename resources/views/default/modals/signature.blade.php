<div class="modal" id="modal-signature">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account/global.word.delivery-proof') }}</h4>
            </div>
            <div class="modal-body">
                @if($item->receiver)
                <div class="receiver">
                    <p>{{ trans('account/global.word.received-by') }}: <b>{{ $item->receiver }}</b></p>
                </div>
                @endif
                @if($item->signature)
                <div class="delivery-proof-img">
                    <img src="{{ $item->signature }}"/>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default confirm-budget-dimensions" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
            </div>
        </div>
    </div>
</div>