<div class="modal fade" id="modal-close-shipments">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => 'account.refunds.selected.confirm', 'class' => 'confirm-refunds', 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">{{ trans('account/refunds.confirm.selected.title') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-12">
                        <h4 class="message">{{ trans('account/refunds.confirm.selected.message') }}</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
                    <button type="submit" class="btn btn-success" data-loading-text="{{ trans('account/global.word.loading') }}...">{{ trans('account/refunds.confirm.selected.label') }}</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>