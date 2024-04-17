{{ Form::model($auth, array('route' => 'account.details.update')) }}
<div class="row">
    <div class="col-sm-8">
        <div class="form-group">
            {{ Form::label($editMode ? 'billing_name' : '',  trans('account/global.word.social-designation'), array('class' => 'form-label')) }}
            {{ Form::text($editMode ? 'billing_name' : '', $customer->billing_name, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
        </div>
        <div class="form-group">
            {{ Form::label($editMode ? 'billing_address' : '', trans('account/global.word.address'), array('class' => 'form-label')) }}
            {{ Form::text($editMode ? 'billing_address' : '', $customer->billing_address, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
        </div>

        <div class="row row-5">
            <div class="col-md-2">
                <div class="form-group m-b-0">
                    {{ Form::label($editMode ? 'billing_zip_code' : '', trans('account/global.word.zip_code'), array('class' => 'form-label')) }}
                    {{ Form::text($editMode ? 'billing_zip_code' : '', $customer->billing_zip_code, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-b-0">
                    {{ Form::label($editMode ? 'billing_city' : '', trans('account/global.word.city'), array('class' => 'form-label')) }}
                    {{ Form::text($editMode ? 'billing_city' : '', $customer->billing_city, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group m-b-0">
                    {{ Form::label($editMode ? 'billing_country' : '', trans('account/global.word.country'), array('class' => 'form-label')) }}
                    {{ Form::select($editMode ? 'billing_country' : '', trans('country'), $customer->billing_country, array('class' => 'form-control select2', 'required', $editMode ? '' : 'disabled')) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="row row-5">
            <div class="col-sm-5">
                <div class="form-group">
                    {{ Form::label('', trans('account/global.word.tin'), array('class' => 'form-label')) }}
                    {{ Form::text('', $customer->vat, array('class' => 'form-control', 'readonly')) }}
                </div>
            </div>
            <div class="col-sm-7">
                <div class="form-group">
                    {{ Form::label('', trans('account/global.word.payment-method'), array('class' => 'form-label')) }}
                    {{ Form::text('', @$customer->paymentCondition->name, array('class' => 'form-control', 'readonly', 'placeholder' => '30 Dias')) }}
                </div>
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('refunds_email', trans('account/global.word.refunds-email'), array('class' => 'form-label')) }}
            {{ Form::email('refunds_email', null, array('class' => 'form-control nospace email')) }}
        </div>
        <div class="form-group m-b-0">
            {{ Form::label('billing_email', trans('account/global.word.billing-email'), array('class' => 'form-label')) }}
            {{ Form::email('billing_email', null, array('class' => 'form-control nospace email')) }}
        </div>
    </div>
</div>
<hr/>
<button class="btn btn-black">{{ trans('account/global.word.save') }}</button>
{{ Form::close() }}