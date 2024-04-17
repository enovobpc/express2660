{{ Form::model($webservice, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-md-3">
            <div class="form-group is-required">
                {{ Form::label('name', trans('account/global.word.name')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group is-required">
                {{ Form::label('method', trans('account/global.word.method')) }}
                {{ Form::select('method', ['' => ''] + trans('admin/ecommerce-gateway.methods'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('endpoint', 'Endpoint') }}
                {{ Form::text('endpoint', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-md-6 user-col">
            <div class="form-group">
                {{ Form::label('user', 'User') }}
                {{ Form::text('user', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6 password-col">
            <div class="form-group">
                {{ Form::label('password', 'Password') }}
                {{ Form::text('password', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6 key-col">
            <div class="form-group">
                {{ Form::label('key', 'Key') }}
                {{ Form::text('key', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6 secret-col">
            <div class="form-group">
                {{ Form::label('secret', 'Secret') }}
                {{ Form::text('secret', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    <button type="submit" class="btn btn-black">{{ trans('account/global.word.save') }}</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    methodChanged();
    $('[name="method"]').on('change', methodChanged);

    function methodChanged() {
        var $this = $('[name="method"]');
        var $user     = $('.user-col').find('input');
        var $password = $('.password-col').find('input');
        var $key      = $('.key-col').find('input');
        var $secret   = $('.secret-col').find('input');

        // Enable all the fields
        $user.attr('disabled', false);
        $password.attr('disabled', false);
        $key.attr('disabled', false);
        $secret.attr('disabled', false);

        if ($this.val() == 'PrestaShop') {
            $user.attr('disabled', true).val('');
            $password.attr('disabled', true).val('');
            $secret.attr('disabled', true).val('');
        } else if ($this.val() == 'WooCommerce') {
            $user.attr('disabled', true).val('');
            $password.attr('disabled', true).val('');
        } else if ($this.val() == 'Shopify') {
            $user.attr('disabled', true).val('');
            $password.attr('disabled', true).val('');
            $secret.attr('disabled', true).val('');
        }
    }
</script>

