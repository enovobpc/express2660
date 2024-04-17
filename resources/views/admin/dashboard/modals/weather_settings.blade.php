<div class="modal" id="modal-weather-settings" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(array('route' => array('admin.weather.store'))) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Configurações de Meteorologia')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group m-0">
                    {{ Form::label('weather_setting_location', __('Localização')) }}
                    {{ Form::select('weather_setting_location', [@Auth::user()->settings['weather_city'] => @Auth::user()->settings['weather_city_name']], null, array('class' => 'form-control')) }}
                    {{ Form::hidden('weather_setting_city', @Auth::user()->settings['weather_city_name']) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>