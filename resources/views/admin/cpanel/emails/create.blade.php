{{ Form::model($emailAccount, array('route' => array('admin.cpanel.emails.store'), 'method' => 'POST', 'class' => 'form-emails')) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Nova conta e-mail')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('email', __('Endereço e-mail')) }}
                <div class="input-group">
                    {{ Form::text('email', null, ['class' => 'form-control nospace lowercase', 'maxlength' => '30', 'required']) }}
                    <div class="input-group-addon">
                        {{ '@'.$domain }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('password', __('Palavra-passe')) }}
                <div class="input-group">
                    {{ Form::text('password', str_random(8), ['class' => 'form-control nospace', 'maxlength' => '30', 'required']) }}
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default btn-random-password" id="random-password"><i class="fas fa-sync-alt"></i> Gerar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('quota', __('Espaço (MB)')) }}
                <div class="input-group">
                    {{ Form::text('quota', 1024, ['class' => 'form-control number', 'maxlength' => '5', 'placeholder' => 'Ilimitado']) }}
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
