{{ Form::model($emailAccount, array('route' => array('admin.cpanel.emails.update', $emailAccount->id), 'method' => 'PUT', 'class' => 'form-emails')) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Editar conta e-mail')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <h4 class="m-t-0">
                <b>{{ $emailAccount->email }}</b><br/>
                <small>@trans('Criado em:')' {{ $emailAccount->created_at }}</small>
            </h4>
            <hr/>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('login_suspended', __('Início de sessão na conta')) }}
                <div>
                    <label class="radio-inline">
                        {{ Form::radio('login_suspended', 0, null) }} @trans('Permitido')
                    </label>
                    <label class="radio-inline">
                        {{ Form::radio('login_suspended', 1, null) }} @trans('Bloqueado')
                    </label>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('incoming_suspended', __('Recepção de e-mails')) }}
                <div>
                    <label class="radio-inline">
                        {{ Form::radio('incoming_suspended', 0, null) }} @trans('Permitido')
                    </label>
                    <label class="radio-inline">
                        {{ Form::radio('incoming_suspended', 1, null) }} @trans('Bloqueado')
                    </label>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('outgoing_suspended', __('Envio de e-mails')) }}
                <div>
                    <label class="radio-inline">
                        {{ Form::radio('outgoing_suspended', 0, null) }} @trans('Permitido')
                    </label>
                    <label class="radio-inline">
                        {{ Form::radio('outgoing_suspended', 1, null) }} @trans('Bloqueado')
                    </label>
                </div>
            </div>
            <hr/>
        </div>
        <div class="col-sm-8">
            <div class="form-group m-0">
                {{ Form::label('password', __('Nova palavra-passe')) }}
                <div class="input-group">
                    {{ Form::text('password', '', ['class' => 'form-control nospace', 'maxlength' => '30']) }}
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default btn-random-password" id="random-password"><i class="fas fa-sync-alt"></i> @trans('Gerar')</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-groupm-0">
                {{ Form::label('quota', __('Espaço (MB)')) }}
                <div class="input-group">
                    {{ Form::text('quota', null, ['class' => 'form-control number', 'maxlength' => '5', 'placeholder' => __('Ilimitado')]) }}
                    <div class="input-group-addon">
                        Mb
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary btn-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A criar e-mail...">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('[data-toggle="tooltip"]').tooltip();

    $('.form-emails').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn  = $(this).find('[type="submit"]')

        $btn.button('loading')
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $('#modal-remote-xs').modal('hide');
                oTable.draw();
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $btn.button('reset')
        })
    })
</script>
