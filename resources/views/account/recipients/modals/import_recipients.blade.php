<div class="modal fade" id="modal-import-recipients">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['account.recipients.import'], 'files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">{{ trans('account/recipents.word.import') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row row-10">
                    <div class="{{ !empty($departments) ? 'col-sm-8' : 'col-sm-12' }}">
                        <div class="form-group m-b-5">
                            {{ Form::label('file', trans('account/global.word.file-to-import'), ['class' => 'control-label']) }}
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput">
                                    <i class="fas fa-file fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new">{{ trans('account/global.word.search') }}</span>
                                    <span class="fileinput-exists">{{ trans('account/global.word.change') }}</span>
                                    <input type="file" name="file" data-file-format="csv,xls,xlsx" required>
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">{{ trans('account/global.word.remove') }}</a>
                            </div>
                        </div>
                    </div>
                    @if(!empty($departments))
                        <div class="col-sm-4">
                            <div class="form-group m-b-5">
                                {{ Form::label('customer_id', trans('account/global.word.department'), ['class' => 'control-label']) }}
                                {{ Form::select('customer_id', ['' => 'Apenas Conta Principal'] + $departments, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
                    <button type="submit" class="btn btn-black" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> {{ trans('account/global.word.loading') }}...">{{ trans('account/global.word.import') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>