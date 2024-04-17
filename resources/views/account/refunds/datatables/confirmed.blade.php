<div class="toggle-field">
    @if(@$row->refund_control->payment_method)
        @if(@$row->refund_control->confirmed)
            <a href="{{ route('account.refunds.update.field', [$row->id]) }}"
               data-method="PUT"
               data-remote="true"
               data-confirm-label="{{ trans('account/refunds.confirm.modal.confirm.label') }}"
               data-confirm-class="btn-success"
               data-title="{{ trans('account/refunds.confirm.modal.confirm.title') }}"
               data-params="confirmed=0"
               class="text-blue toogle-action"
               data-confirm="{{ trans('account/refunds.confirm.modal.confirm.message') }}">
                <i class="fas fa-check-circle text-green"></i>
            </a>
        @else
            <a href="{{ route('account.refunds.update.field', [$row->id]) }}"
               data-method="PUT"
               data-remote="true"
               data-confirm-label="{{ trans('account/refunds.confirm.modal.unconfirm.label') }}"
               data-confirm-class="btn-success"
               data-title="{{ trans('account/refunds.confirm.modal.unconfirm.title') }}"
               data-params="confirmed=1"
               class="text-blue toogle-action"
               data-confirm="{{ trans('account/refunds.confirm.modal.unconfirm.message') }}">
                <i class="fas fa-times-circle text-muted"></i>
            </a>
        @endif
    @endif
</div>