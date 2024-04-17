{{ Form::open(array('route' => array('admin.cpanel.emails.install.store'), 'method' => 'POST', 'class' => 'form-emails')) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Instalar múltiplos e-mails')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('emails', __('Insira os nomes de e-mail a criar (separado por vírgula)')) }}
                {{ Form::textarea('emails', null, ['class' => 'form-control nospace', 'rows' => 2]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('quota', __('Espaço (MB)')) }}
                <div class="input-group">
                    {{ Form::text('quota', 1024, ['class' => 'form-control number', 'maxlength' => '5', 'placeholder' => __('Ilimitado')]) }}
                    <div class="input-group-addon">
                        Mb
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('password', __('Formato Password')) }}
                <div style="margin-top: 8px">
                    <label class="radio-inline">
                        {{ Form::radio('password', 'format1', true) }} @trans('xxx#EMPRESA')
                    </label>
                    <label class="radio-inline">
                        {{ Form::radio('password', 'format2', null) }} xxxx#{{ date('Y') }}
                    </label>
                    <label class="radio-inline">
                        {{ Form::radio('password', 'random', null) }} @trans('Aleatório')
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary btn-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A criar e-mails...">@trans('Criar e-mails')</button>
</div>
{{ Form::close() }}

<script>
    $('.form-emails').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn  = $(this).find('[type="submit"]')

        $btn.button('loading')
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $('#modal-remote .modal-body').html(data.html);
                $btn.hide();
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