<div class="modal" id="modal-print-shipments">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-print"></i> {{ trans('account/global.printer.print') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('print_min_date', trans('account/global.printer.date-begin'), ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('print_min_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                                <span class="input-group-addon">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('print_max_date', trans('account/global.printer.date-end') , ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('print_max_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                                <span class="input-group-addon">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-0">
                            {{ Form::label('', trans('account/global.printer.list') , ['class' => 'control-label']) }}
                            @if($auth->show_billing)
                            <div class="checkbox">
                                <label style="padding-left: 0">
                                    {{ Form::radio('print_type', 'billing') }}
                                    <span style="text-transform: none">{{ trans('account/global.printer.price') }}</span>
                                </label>
                            </div>
                            @endif
                            <div class="checkbox">
                                <label style="padding-left: 0">
                                    {{ Form::radio('print_type', 'confirmation', true) }}
                                    <span style="text-transform: none">{{ trans('account/global.printer.weight') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.printer.close') }}</button>
                <a href="{{ route('account.shipments.print') }}" target="_blank" class="btn btn-black btn-print">{{ trans('account/global.printer.printer') }}</a>
            </div>
        </div>
    </div>
</div>