<div class="modal fade" id="modal-destroy-event" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['method' => 'DELETE']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Eliminar evento')</h4>
            </div>
            <div class="modal-body">
                <h4>@trans('Confirma a remoção do evento?')</h4>
            </div>
            <div class="modal-footer">
                <div class="extra-options pull-left w-65">
                    <div class="checkbox">
                        <label>
                            {{ Form::checkbox('destroy_repetions', 1) }}
                            @trans('Apagar também repetições agendadas.')'
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                <button type="submit" class="btn btn-danger">@trans('Eliminar')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>