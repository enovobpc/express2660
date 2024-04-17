<div class="box-sender-content">
    {{ Form::hidden('sender_agency_id') }}
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('customer_id', 'Nome', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-10">
            {{ Form::text('sender_name', $shipment->exists ? null : '', ['class' => 'form-control input-sm search-sender', 'required']) }}
            {{ Form::hidden('sender_id') }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0" for="sender_address">
            Morada <i class="fas fa-spin fa-circle-notch hide"></i>
        </label>
        <div class="col-sm-10">
            {{ Form::text('sender_address', $shipment->exists ? null : '', ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>

    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('sender_zip_code', 'C.Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::text('sender_zip_code', $shipment->exists ? null : '', ['class' => 'form-control input-sm', 'required']) }}
        </div>
        {{ Form::label('sender_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('sender_city', $shipment->exists ? null : '', ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>

    <div class="form-group form-group-sm m-b-0">
        {{ Form::label('sender_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::select('sender_country', ['' => ''] + trans('country'), Setting::get('app_country'), ['class' => 'form-control select2', 'required']) }}
        </div>
        {{ Form::label('sender_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('sender_phone', $shipment->exists ? null : '', ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>
</div>