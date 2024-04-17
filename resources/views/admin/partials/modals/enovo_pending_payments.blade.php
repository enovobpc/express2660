<div class="modal" id="enovo-pending-payments" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog {{ $enovoPayments->size }}">
        <div class="modal-content">
            {{ Form::open(array('route' => array('admin.account.payment.confirm'))) }}
           {{-- <div class="modal-header bg-blue">
                --}}{{--<button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>--}}{{--
                <h4 class="modal-title"><i class="fas fa-info-circle"></i> {!! $enovoPayments->title !!}</h4>
            </div>
            <div class="modal-body">
                {!! $enovoPayments->content !!}
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">{{ $enovoPayments->button }}</button>
            </div>--}}
            {!! $enovoPayments->content !!}
            {{ Form::close() }}
        </div>
    </div>
</div>