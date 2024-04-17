<div class="modal fade" id="modal-grouped-guide">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="float: right">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">{{ trans('account/global.word.close') }}</span>
                </button>
                <h4 class="modal-title">{{ trans('account/shipments.modal-grouped-guide.title') }}</h4>
            </div>
            <div class="modal-body">
                <h4>{{ trans('account/shipments.modal-grouped-guide.message') }}</h4>
                <hr/>
                <div class="row row-5">
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            {{ Form::label('packing_type', trans('account/shipments.modal-grouped-guide.pack-type'), ['class' => 'control-label']) }}
                            {{ Form::select('packing_type', ['VOLUME' => 'Volume', 'CAIXA' => 'Caixa', 'PALETE' => 'Palete'], null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-0">
                            {{ Form::label('description', trans('account/shipments.modal-grouped-guide.goods-description'), ['class' => 'control-label']) }}
                            {{ Form::text('description', null, ['class' => 'form-control', 'placeholder' => 'Volume']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            {{ Form::label('vehicle', trans('account/shipments.modal-grouped-guide.license-plate'), ['class' => 'control-label']) }}
                            {{ Form::text('vehicle', null, ['class' => 'form-control uppercase']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('account.shipments.selected.print.guide') }}" id="url-grouped-transport-guide" data-toggle="datatable-action-url" target="_blank"></a>
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
                    <button type="submit" class="btn btn-black btn-print-grouped">{{ trans('account/global.word.print') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>