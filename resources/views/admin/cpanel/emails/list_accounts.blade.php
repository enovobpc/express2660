<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Listagem de e-mails')</h4>
</div>
<div class="modal-body">
    @include('admin.cpanel.emails.partials.list_accounts')
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>

<script>
    $('.select2').select2(Init.select2());

    $('[name="email"]').on('change', function(){
        var password = $(this).find('option:selected').html();
        $('.config-email').html(password)
        $('.config-password').html($(this).val())
    })
</script>