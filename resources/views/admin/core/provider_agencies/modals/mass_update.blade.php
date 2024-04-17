<div class="modal" id="modal-mass-update">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['core.provider.agencies.selected.update']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Editar em massa</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('status', 'Estado') }}
                            {{ Form::select('status', [''=>''] + $status, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('mass_country', 'País') }}
                            {{ Form::select('mass_country', [''=>''] + trans('country'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('active', 'Ativo?') }}
                            {{ Form::select('active', [''=>'', '1'=> 'Sim', '0'=>'Não'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('hidden', 'Ocultar?') }}
                            {{ Form::select('hidden', [''=>'', '1'=> 'Ocultar', '0'=>'Desocultar'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>