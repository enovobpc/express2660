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
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('settings[carrier]', trans('account/global.word.carrier')) }}
                {{ Form::select('settings[carrier]', ['' => ''] + $carriers, @$webservice->settings['carrier'], ['class' => 'form-control select2 w-100']) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('settings[status][list][]', trans('account/ecommerce-gateway.mapping.list-status')) }}
                {{ Form::select('settings[status][list][]', ['' => ''] + $status, @array_map('intval', @$webservice->settings['status']['list']), ['class' => 'form-control select2', 'multiple']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('settings[status][in_distribution]', trans('account/ecommerce-gateway.mapping.in-distribution-status')) }}
                {{ Form::select('settings[status][in_distribution]', ['' => ''] + $status, @$webservice->settings['status']['in_distribution'], ['class' => 'form-control select2']) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('settings[status][delivered]', trans('account/ecommerce-gateway.mapping.delivered-status')) }}
                {{ Form::select('settings[status][delivered]', ['' => ''] + $status, @$webservice->settings['status']['delivered'], ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-md-12">
            <div class="checkbox">
                <label style="padding-left: 0px">
                    {{ Form::checkbox('settings[force_volumes_one]', 1, @$webservice->settings['force_volumes_one']) }}
                    {{ trans('account/ecommerce-gateway.mapping.force-volumes-one') }}
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    <button type="submit" class="btn btn-black">{{ trans('account/global.word.save') }}</button>
</div>
{{ Form::close() }}

<style>
    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }
</style>

<script>
    $('.select2').select2(Init.select2());
</script>