{{ Form::model($recipient, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <h4 class="m-t-0">{{ trans('account/global.word.shipping-info') }}</h4>
            <div class="row row-5">
                <div class="col-sm-4 col-md-2">
                    <div class="form-group">
                        {{ Form::label('code', trans('account/global.word.customer-code')) }}
                        {{ Form::text('code', null, ['class' => 'form-control nospace']) }}
                    </div>
                </div>
                <div class="col-sm-8 col-md-10">
                    <div class="form-group is-required">
                        {{ Form::label('name', trans('account/global.word.recipient-name')) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
            </div>
            <div class="form-group is-required">
                {{ Form::label('address', trans('account/global.word.address')) }}
                {{ Form::text('address', null, ['class' => 'form-control', 'required']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('zip_code', trans('account/global.word.zip_code')) }}
                        {{ Form::text('zip_code', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group is-required">
                        {{ Form::label('city', trans('account/global.word.city')) }}
                        {{ Form::text('city', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('country', trans('account/global.word.country')) }}
                        {{ Form::select('country', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('obs', trans('account/recipients.word.obs')) }}
                {!! tip(trans('account/recipients.tips.obs')) !!}
                {{ Form::text('obs', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <h4 class="m-t-0">{{ trans('account/global.word.contact-info') }}</h4>
            <div class="form-group">
                {{ Form::label('responsable', trans('account/global.word.recipient-attn')) }}
                {{ Form::text('responsable', null, ['class' => 'form-control']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('phone', trans('account/global.word.phone')) }}
                        {{ Form::text('phone', $recipient->exists ? str_replace(' ', '', $recipient->phone) : null, ['class' => 'form-control nospace phone', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('mobile', trans('account/global.word.mobile')) }}
                        {{ Form::text('mobile', $recipient->exists ? str_replace(' ', '', $recipient->mobile) : null, ['class' => 'form-control nospace phone']) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('email', trans('account/global.word.email')) }}
                {{ Form::text('email', null, ['class' => 'form-control nospace lowercase email']) }}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('vat', trans('account/global.word.tin')) }}
                {{ Form::text('vat', null, ['class' => 'form-control nospace vat', 'data-country' => 'country']) }}
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
    $('.phone').intlTelInput(Init.intlTelInput());
    $('.select2').select2(Init.select2());
</script>

