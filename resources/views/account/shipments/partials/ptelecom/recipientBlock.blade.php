<div class="box-recipient-content">
    {{ Form::hidden('recipient_agency_id') }}
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient_id', 'Nome', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-10">
            {{ Form::text('recipient_name', null, ['class' => 'form-control input-sm search-recipient']) }}
            {{ Form::hidden('recipient_id') }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0" for="recipient_address">
            Morada <i class="fas fa-spin fa-circle-notch hide"></i>
        </label>
        <div class="col-sm-10">
            {{ Form::text('recipient_address', null, ['class' => 'form-control input-sm']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient__zip_code', 'C.Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::text('recipient_zip_code', null, ['class' => 'form-control input-sm', 'autocomplete'=> 'nofill']) }}
        </div>
        {{ Form::label('recipient_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('recipient_city', null, ['class' => 'form-control input-sm', 'autocomplete'=> 'nofill']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-0">
        {{ Form::label('recipient_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::select('recipient_country', ['' => ''] + trans('country'), $shipment->exists ? $shipment->recipient_country : Setting::get('app_country'), ['class' => 'form-control select2']) }}
        </div>
        {{ Form::label('recipient_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('recipient_phone', null, ['class' => 'form-control input-sm']) }}
        </div>
    </div>
</div>