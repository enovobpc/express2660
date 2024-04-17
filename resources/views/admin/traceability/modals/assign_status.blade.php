<div class="modal" id="modal-assign-status">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.shipments.history.selected.update']]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="float: right">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Alterar estado dos envios</h4>
            </div>
            <div class="modal-body p-10">
                <div class="form-group is-required">
                    {{ Form::label('assign_status_id', 'Estado', ['class' => 'control-label']) }}
                    {{ Form::select('assign_status_id',  ['' => ''] + $status, null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar estado...">Gravar</button>
                </div>
            </div>
            {{ Form::hidden('ids') }}
            {{ Form::close() }}
        </div>
    </div>
</div>