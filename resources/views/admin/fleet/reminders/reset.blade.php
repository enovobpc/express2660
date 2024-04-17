{{ Form::open(['route' => 'admin.fleet.reminders.reset.store', 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Reiniciar ou Concluir lembretes')</h4>
</div>
<div class="modal-body">
    @foreach($reminders as $status => $items)
        @if($status == 'warnings')
            <h4 class="text-yellow"><i class="fas fa-clock"></i> @trans('Prestes a expirar')</h4>
        @else
            <h4 class="text-red m-t-0"><i class="fas fa-exclamation-triangle"></i> @trans('Expirados')</h4>
        @endif
        <table class="table table-condensed m-b-0">
            <tr>
                <th class="bg-gray-light">@trans('Lembrete')</th>
                <th class="bg-gray-light w-120px">@trans('Ação')</th>
                <th class="bg-gray-light w-145px">@trans('Data Limite')</th>
                <th class="bg-gray-light w-80px">@trans('Aviso Dias')</th>
                <th class="bg-gray-light w-90px">@trans('Km Limite')</th>
                <th class="bg-gray-light w-80px">@trans('Aviso Km')</th>
            </tr>
            @foreach($items as $item)
                <?php
                    $date = '';
                    if($item['date']) {
                        $date = new Date($item['date']);
                        $date = $date->addYear()->format('Y-m-d');
                    }

                ?>
            <tr>
                <td class="vertical-align-middle lh-1-2">
                    {{ $item['title'] }}<br/>
                   <small class="text-muted italic">{{ @$item['vehicle']['name'] }}</small>
                </td>
                <td class="input-sm">
                    {{ Form::hidden('id[]', @$item['id']) }}
                    {{ Form::hidden('type[]', @$item['type']) }}
                    {{ Form::hidden('vehicle[]', @$item['vehicle_id']) }}
                    @if(in_array(@$item['type'], ['ipo', 'iuc', 'insurance', 'tachograph']))
                    {{ Form::select('action[]', ['' => '', 'reset' => __('Reagendar')], null, ['class' => 'form-control select2']) }}
                    @else
                    {{ Form::select('action[]', ['' => '', 'reset' => __('Reagendar'), 'conclude' => __('Concluir')], null, ['class' => 'form-control select2']) }}
                    @endif
                </td>
                <td>
                    <div class="input-group">
                        {{ Form::text('date[]', $date, ['class' => 'form-control input-sm datepicker']) }}
                        <span class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </td>
                <td>
                    {{ Form::number('days_alert[]',  @$item['days_alert'], ['class' => 'form-control input-sm']) }}
                </td>
                @if(in_array(@$item['type'], ['ipo', 'iuc', 'insurance', 'tachograph']))
                    <td colspan="2"></td>
                @else
                <td>
                    {{ Form::number('km[]', null, ['class' => 'form-control input-sm']) }}
                </td>
                <td>
                    {{ Form::number('km_alert[]', @$item['km_alert'], ['class' => 'form-control input-sm']) }}
                </td>
                @endif
            </tr>
            @endforeach
        </table>
    @endforeach
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());
    $('.modal [name="action[]"]').on('change', function () {
        var $tr = $(this).closest('tr');
        if($(this).val() == 'conclude') {
            $tr.find('input, .input-group').hide();
        } else {
            $tr.find('input, .input-group').show();
        }

    })
</script>

