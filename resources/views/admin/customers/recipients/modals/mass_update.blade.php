<div class="modal" id="modal-mass-update">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.recipients.selected.update'], 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Editar destinatários em massa')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {{ Form::label('assign_assigned_customer_id', __('Cliente associado ao destinatário')) }}
                            {{ Form::select('assign_assigned_customer_id', ['-1' => __('Sem cliente associado')], null, ['class' => 'form-control select2', 'data-placeholder' => '- Não alterar -']) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {{ Form::label('assign_country', __('País')) }}
                            {{ Form::select('assign_country', ['' => __('- Não alterar -')] + trans('country'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">@trans('Gravar')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>