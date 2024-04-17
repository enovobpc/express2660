{{ Form::open(array('route' => array('admin.cpanel.emails.autoresponders.store', $emailAccount->id), 'method' => 'POST', 'class' => 'form-emails')) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Gerir mensagem automática')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        @if(@$autoresponder['subject'])
        <div class="col-sm-12">
            <div class="alert alert-info"><i class="fas fa-info-circle"></i> @trans('Para anular o envio automático, apague o assunto e o texto da mensagem.')</div>
        </div>
        @endif
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('subject', __('Assunto da mensagem')) }}
                {{ Form::text('subject', @$autoresponder['subject'], ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('body', __('Texto da mensagem')) }}
                {{ Form::textarea('body', @$autoresponder['body'], ['class' => 'form-control', 'rows' => 6]) }}
            </div>
            <label style="margin-bottom: 10px; font-weight: normal">
                {{ Form::checkbox('is_html', 1, @$autoresponder['is_html']) }} @trans('A mensagem contém HTML.')'
            </label>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('interval', __('Intervalo entre Msgs.')) }}
                <div class="input-group">
                    {{ Form::text('interval', @$autoresponder['interval'] ? @$autoresponder['interval'] : 1, ['class' => 'form-control number']) }}
                    <div class="input-group-addon">@trans('Horas')</div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('start', __('Iniciar em')) }}
                <div class="input-group">
                    {{ Form::text('start', @$autoresponder['start'] ? @$autoresponder['start'] : date('Y-m-d'), ['class' => 'form-control datetime', 'placeholder' => 'Imeadiato', 'required']) }}
                    <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-1" style="width: 100px">
            <div class="form-group is-required">
                <label>@trans('Hora')</label>
                {{ Form::time('start_hour', @$autoresponder['start_hour'] ? @$autoresponder['start_hour'] : date('H:i'), ['class' => 'form-control timepicker', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('stop', __('Terminar em')) }}
                <div class="input-group">
                    {{ Form::text('stop', @$autoresponder['stop'], ['class' => 'form-control datetime', 'placeholder' => 'Nunca']) }}
                    <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-1" style="width: 100px">
            <div class="form-group is-required">
                <label>@trans('Hora')</label>
                {{ Form::time('stop_hour', @$autoresponder['stop_hour'], ['class' => 'form-control timepicker']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::hidden('remove') }}
    {{ Form::hidden('add') }}
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary btn-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('[data-toggle="tooltip"]').tooltip();
    $('.datetime').datepicker(Init.datepicker());


    $('.modal .btn-add').on('click', function () {

        $('.modal [name="email"]').trigger('change');

        if($('.modal [name="email"]').closest('.form-group').hasClass('has-error')) {
            Growl.error('E-mail inválido. Corrija antes de gravar.')
        } else {

            $('.modal .empty-forwarder').hide();

            var email = $('.modal [name="email"]').val();
            $('.modal [name="email"]').val('');

            html = '<tr><td>';
            html += '<i class="fas fa-arrow-right"></i> ' + email;
            html += '</td><td>';
            html += '<span class="text-red btn-remove-forwarder" data-email="'+email+'">';
            html += '<i class="fas fa-trash"></i>';
            html += '</span>';
            html += '</td><tr>';

            $('.table-emails').append(html)

            var addEmails = $('.modal [name="add"]').val();
            addEmails = addEmails + ',' + email;
            $('.modal [name="add"]').val(addEmails);
        }
    })

    $(document).on('click', '.modal .btn-remove-forwarder', function () {
        var email = $(this).data('email');

        $(this).closest('tr').remove();

        var removeEmails = $('.modal [name="remove"]').val();
        removeEmails = removeEmails + ',' + email;
        $('.modal [name="remove"]').val(removeEmails);

    })



    $('.form-emails').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn  = $(this).find('[type="submit"]')

        $btn.button('loading')
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $('#modal-remote-lg').modal('hide');
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
