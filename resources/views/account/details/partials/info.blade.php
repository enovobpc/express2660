{{ Form::model($auth, array('route' => 'account.details.update')) }}
<div class="row">
    <div class="col-sm-8">
        <h4>{{ trans('account/global.word.shipping-info') }}</h4>
        <div class="form-group">
            {{ Form::label($editMode ? 'name' : '', trans('account/global.word.sender-name'), array('class' => 'form-label')) }}
            {{ Form::text($editMode ? 'name' : '', $customer->name, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
        </div>
        <div class="form-group">
            {{ Form::label($editMode ? 'address' : '', trans('account/global.word.address'), array('class' => 'form-label')) }}
            {{ Form::text($editMode ? 'address' : '', $customer->address, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
        </div>
        <div class="row row-5">
            <div class="col-md-2">
                <div class="form-group m-b-0">
                    {{ Form::label($editMode ? 'zip_code' : '', trans('account/global.word.zip_code'), array('class' => 'form-label')) }}
                    {{ Form::text($editMode ? 'zip_code' : '', $customer->zip_code, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-b-0">
                    {{ Form::label($editMode ? 'city' : '', trans('account/global.word.city'), array('class' => 'form-label')) }}
                    {{ Form::text($editMode ? 'city' : '', $customer->city, array('class' => 'form-control', 'required', $editMode ? '' : 'readonly')) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group m-b-0">
                    {{ Form::label($editMode ? 'country' : '', trans('account/global.word.country'), array('class' => 'form-label')) }}
                    {{ Form::select($editMode ? 'country' : '', trans('country'), $customer->country, array('class' => 'form-control select2', 'required', $editMode ? '' : 'disabled')) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <h4>{{ trans('account/global.word.contact-info') }}</h4>
        <div class="row row-5">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('contact_email', trans('account/global.word.email'), array('class' => 'form-label')) }}
                    {{ Form::email('contact_email', null, array('class' => 'form-control nospace lowercase', 'required')) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('phone', trans('account/global.word.phone'), array('class' => 'form-label')) }}
                    {{ Form::text('phone', null, array('class' => 'form-control nospace phone', 'required')) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('mobile', trans('account/global.word.mobile'), array('class' => 'form-label')) }}
                    {{ Form::text('mobile', null, array('class' => 'form-control nospace phone', 'required')) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group m-b-0">
                    {{ Form::label('website', trans('account/global.word.website'), array('class' => 'form-label')) }}
                    {{ Form::url('website', null, array('class' => 'form-control nospace url')) }}
                </div>
            </div>
        </div>
    </div>
</div>


<hr/>
<button class="btn btn-black">{{ trans('account/global.word.save') }}</button>
{{ Form::close() }}