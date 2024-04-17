{{ Form::model($cartProduct, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">

    <h4 class="box-title"><i class="fas fa-sign-in-alt"></i>
        Local Origem
    </h4>
    <div class="row row-5">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('origin_name', 'Remetente') }}
                {{ Form::text('origin_name', null, ['class' => 'form-control ']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-md-8">
            <div class="form-group is-required">
                {{ Form::label('origin_address', 'Morada') }}
                {{ Form::text('origin_address', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group is-required">
                {{ Form::label('origin_zip_code', trans('account/global.word.zip_code')) }}
                {{ Form::text('origin_zip_code', null, ['class' => 'form-control nospace']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                {{ Form::label('origin_city', trans('account/global.word.city')) }}
                {{ Form::text('origin_city', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group ">
                {{ Form::label('origin_country', trans('account/global.word.country')) }}
                {{ Form::text('origin_country', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                {{ Form::label('origin_phone_number', trans('account/global.word.phone')) }}
                {{ Form::text('origin_phone_number', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
  
    <h4 class="box-title"><i class="fas fa-sign-in-alt"></i>
        Local Entrega
    </h4>

    <div class="row row-5">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('destination_name', 'Destinatário') }}
                {{ Form::text('destination_name', null, ['class' => 'form-control ']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-md-8">
            <div class="form-group is-required">
                {{ Form::label('destination_address', 'Morada') }}
                {{ Form::text('destination_address', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                {{ Form::label('destination_zip_code', trans('account/global.word.zip_code')) }}
                {{ Form::text('destination_zip_code', null, ['class' => 'form-control nospace']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                {{ Form::label('destination_city', trans('account/global.word.city')) }}
                {{ Form::text('destination_city', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group ">
                {{ Form::label('destination_country', trans('account/global.word.country')) }}
                {{ Form::text('destination_country', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group">
                {{ Form::label('destination_phone_number', trans('account/global.word.phone')) }}
                {{ Form::text('destination_phone_number', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <hr>
    <div class="row row-5">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                {{ Form::label('obs', trans('account/global.word.obs')) }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 4]) }}
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
</script>