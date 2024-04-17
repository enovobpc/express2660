<div class="modal" id="modal-mass-replicate">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.services.selected.replicate']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Duplicar serviços</h4>
            </div>
            <div class="modal-body">
                <div class="row row-10">
                    @if(Auth::user()->isAdmin())
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ Form::label('source', 'Plataforma') }}
                                {{ Form::text('source', null, ['class' => 'form-control nospace lowercase']) }}
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            {{ Form::label('code', 'Novo código') }}
                            {{ Form::text('code', null, ['class' => 'form-control nospace uppercase', 'maxlength' => 5, Auth::user()->isAdmin() ? '' : 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group is-required">
                            {{ Form::label('name', 'Designação novo serviço') }}
                            {{ Form::text('name', null, ['class' => 'form-control ucwords', Auth::user()->isAdmin() ? '' : 'required', 'maxlength' => 25]) }}
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