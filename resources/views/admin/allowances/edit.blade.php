{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        @if ($trips->isEmpty())
        <div class="m-t-60 m-b-60 text-center">
            <h3 class="text-muted">
                <i class="fas fa-euro-sign fs-40"></i><br />
                Sem viagens
            </h3>
        </div>
        @else
            @php
                $totalAllowances = 0;
                $totalWeekend = 0;
                $total = 0;
            @endphp
            <table class="table table-striped table-hover table-condensed m-b-0">
                <thead>
                    <tr class="bg-gray">
                        <th>Viagem</th>
                        <th>Local Início</th>
                        <th>Local Fim</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Kms</th>
                        <th class="w-105px">Ajudas Custo</th>
                        <th class="w-105px">Fim Semana</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trips as $trip)
                        @php
                            $allowanceValue = $trip->allowances_price ?? 0.0;
                            $weekendValue   = $trip->weekend_price ?? 0.0;
                            
                            $totalAllowances += $allowanceValue;
                            $totalWeekend    += $weekendValue;
                            $total            = $totalAllowances + $totalWeekend;
                        @endphp

                        <tr>
                            <td>@include('admin.trips.datatables.code', ['row' => $trip])</td>
                            <td>@include('admin.trips.datatables.start_location', ['row' => $trip])</td>
                            <td>@include('admin.trips.datatables.end_location', ['row' => $trip])</td>
                            <td>@include('admin.trips.datatables.pickup_date', ['row' => $trip])</td>
                            <td>@include('admin.trips.datatables.delivery_date', ['row' => $trip])</td>
                            <td>@include('admin.trips.datatables.kms', ['row' => $trip])</td>
                            <td>
                                <div class="input-group">
                                    {{ Form::text('allowance[' . $trip->id . ']', $allowanceValue, ['class' => 'form-control input-sm decimal']) }}
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    {{ Form::text('weekend[' . $trip->id . ']', $weekendValue, ['class' => 'form-control input-sm decimal']) }}
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
<div class="modal-footer">
    @if ($trips->isNotEmpty())
    <button type="submit" class="btn btn-primary pull-right">Gravar</button>
    <button type="button" class="btn btn-default pull-right m-r-5" data-dismiss="modal">Fechar</button>
    <div class="pull-right m-r-10">
        <h5 class="pull-left m-0 p-r-10">
            <small>Total Ajudas Custo</small><br/>
            {{ money($totalAllowances, Setting::get('app_currency')) }}
        </h5>
        <h5 class="pull-left m-0 p-r-10">
            <small>Total Fim Semana</small><br/>
            {{ money($totalWeekend, Setting::get('app_currency')) }}
        </h5>
        <h5 class="pull-left m-0">
            <small>Total</small><br/>
            {{ money($total, Setting::get('app_currency')) }}
        </h5>
    </div>
    @else
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @endif
</div>
{{ Form::close() }}

<script>
$('.calc-allowance').on('click', function () {
    var $this     = $(this);
    var $formData = $('.form-trip :not(input[name=_method]').serializeArray();
    var id        = $this.data('id');

    $this.children('i').addClass('fa-spin');
    $.post($this.data('url'), $formData, function (resp){
        var data = resp.data;

        $("[name='allowance["+ id +"]']").val(data.allowances_price);
        $("[name='weekend["+ id +"]']").val(data.weekend_price);
    }).fail(function() {
        Growl.error('Não foi possível calcular ajudas de custo.');
    }).always(function() {
        $this.children('i').removeClass('fa-spin');
    });
});
</script>
