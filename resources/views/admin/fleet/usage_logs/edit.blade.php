{{ Form::model($usageLog, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('vehicle_id', __('Viatura')) }}
                {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('operator_id', __('Motorista')) }}
                {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('type', __('Tipo')) }}
                {{ Form::select('type', ['' => ''] + trans('admin/fleet.usages-logs.types'), null, ['class' => 'form-control select2', 'required', 'id'  => 'type-usage-log' ]) }}
            </div>
        </div>
    </div>
    <hr class="m-t-0 m-b-10"/>
    <h4 class="m-b-5 m-t-0 bold text-primary" id="title1">@trans('Início')</h4>
    <div class="row row-5">
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('start_date', __('Data')) }}
                <div class="input-group">
                    {{ Form::text('start_date', $usageLog->exists ? $usageLog->start_date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('start_hour', __('Hora')) }}
                {{ Form::select('start_hour', ['' => ''] + $hours, $usageLog->exists ? $usageLog->start_date->format('H:i') : null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('start_km', __('Km Iniciais')) }}
                <div class="input-group">
                    {{ Form::text('start_km', null, ['class' => 'form-control number', 'required']) }}
                    <span class="input-group-addon">
                        km
                    </span>
                </div>
            </div>
        </div>
    </div>
    <hr class="m-t-0 m-b-10"/>
    <h4 class="m-b-5 m-t-0 bold text-primary" id="title2">@trans('Fim')</h4>
    <div class="row row-5">
        <div class="col-sm-5">
            <div class="form-group m-b-0 is-required">
                {{ Form::label('end_date', __('Data')) }}
                <div class="input-group">
                    {{ Form::text('end_date', $usageLog->exists ? $usageLog->start_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group m-b-0 is-required">
                {{ Form::label('end_hour', __('Hora')) }}
                {{ Form::select('end_hour', ['' => ''] + $hours, $usageLog->exists ? $usageLog->end_date->format('H:i') : null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-b-0 is-required">
                {{ Form::label('end_km', __('Km Finais')) }}
                <div class="input-group">
                    {{ Form::text('end_km', null, ['class' => 'form-control number', 'required']) }}
                    <span class="input-group-addon">
                        km
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>

    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker())

    
    function typeUsageLog(parament){

        console.log(parament);

        if('break' == parament || 'works' == parament || 'available' == parament){
            
            $('#start_km').prop( "disabled", true);
            $('#start_km').prop('required', false); 
            $('#start_km').parent().parent().removeClass('is-required'); 
            
            $('#end_km').prop( "disabled", true);
            $('#end_km').prop('required', false); 
            $('#end_km').parent().parent().removeClass('is-required'); 

            if('break' == parament){
                
                $('#title1').text("Início Descanso");
                $('#title2').text("Fim Descanso");

            }else if('works' == parament){

                $('#title1').text("Início Trabalho");
                $('#title2').text("Fim Trabalho");

            }else if('available' == parament){

                $('#title1').text("Início Tempo Livre");
                $('#title2').text("Fim Tempo Livre");

            }

        } else if('driving' == parament || 'outsourced' == parament) {

            $('#start_km').prop( "disabled", false);
            $('#start_km').prop('required', true); 
            $('#start_km').parent().parent().addClass('is-required'); 
            
            $('#end_km').prop( "disabled", false);
            $('#end_km').prop('required', true); 
            $('#end_km').parent().parent().addClass('is-required'); 

            if('driving' == parament){
                
                $('#title1').text("Início Condução");
                $('#title2').text("Fim Condução");

            }else if('outsourced' == parament){

                $('#title1').text("Início Subcontratado");
                $('#title2').text("Fim Subcontratado");

            }
        }
    }

    $(document).ready(function() {

        var type = $('#type-usage-log option:selected');

        typeUsageLog(type.val());

        $('#type-usage-log').change(function(){
            
            var type = $('#type-usage-log option:selected');
            
            typeUsageLog(type.val());
        })

    })
    

</script>

