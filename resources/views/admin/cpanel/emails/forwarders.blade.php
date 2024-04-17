{{ Form::open(array('route' => array('admin.cpanel.emails.forwarders.store', $emailAccount->id), 'method' => 'POST', 'class' => 'form-emails')) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Gerir redirecionamentos')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('email', __('E-mail para redirecionar')) }}
                {{ Form::email('email', null, ['class' => 'form-control email']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="sp-20"></div>
            <button type="button" class="btn btn-sm btn-block btn-success btn-add"><i class="fas fa-plus"></i> Adicionar</button>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            <h4 class="bold">@trans('Redirecionamentos ativos')</h4>
            <table class="table table-condensed table-emails m-0">
                <tr>
                    <th class="bg-gray-light">@trans('E-mail')</th>
                    <th class="bg-gray-light w-1"></th>
                </tr>
                @if($listForwarders)
                @foreach($listForwarders as $forwarder)
                <tr>
                    <td><i class="fas fa-arrow-right"></i> {{ $forwarder }}</td>
                    <td>
                        <span class="text-red btn-remove-forwarder" data-email="{{ $forwarder }}">
                            <i class="fas fa-trash"></i>
                        </span>
                    </td>
                </tr>
                @endforeach
                @endif
            </table>
            <p class="m-t-5 m-l-5 empty-forwarder" style="{{ !$listForwarders ? '' : 'display:none' }}">@trans('Nenhum redirecionamento ativo.')</p>
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
