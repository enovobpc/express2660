<div class="modal" id="modal-list-emails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Obter Lista de E-mails')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            {{ Form::label('agency', __('Clientes da Agência')) }}
                            {{ Form::select('agency', ['' => __('Todos')] + $agencies, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('type', __('Tipo de Cliente')) }}
                            {{ Form::select('type', ['' => __('Todos')] + $types, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            {{ Form::label('payment_method', __('Tipo Pagamento')) }}
                            {{ Form::select('payment_method', ['' => __('Todos')] + $paymentConditions, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="spacer-20"></div>
                        <div class="spacer-4"></div>
                        <button class="btn btn-block btn-success" id="get-emails-list">@trans('Obter Lista')</button>
                    </div>
                </div>

                <div class="form-group is-required">
                    <label>@trans('Copie e cole a lista abaixo no campo "Para" do seu e-mail.')</label>
                    {{ Form::textarea('emails', null, ['class' => 'form-control', 'rows' => 5]) }}
                </div>
                <p class="m-0 text-blue total-helper" style="display: none;"><i class="fas fa-info-circle"></i> @trans('Foram encontrados') <b></b> @trans('e-mails para os filtros selecionados.')</p>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                </div>
            </div>
        </div>
    </div>
</div>