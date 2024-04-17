<div class="box">
    <div class="box-body" style="height: 300px">
        @if(config('app.source') == 'lousaestradas')
        <h4 class="bold text-center m-t-0 m-b-5">@trans('Envios por agência')</h4>
        @else
        <h4 class="bold text-center m-t-0 m-b-5">@trans('Envios por fornecedor')</h4>
        @endif
        <div class="chart">
            <canvas id="providersChart" height="105"></canvas>
        </div>
        <hr style="margin: 5px 0"/>
        <h4 class="bold text-center m-t-10 m-b-5">@trans('Envios por Estado')</h4>
        <div class="chart">
            <canvas id="statusChart" height="105"></canvas>
        </div>
        {{--<h4 class="bold text-center m-t-10 m-b-5">Envios por Destino</h4>
        <div class="chart">
            <canvas id="recipientsChart" height="105"></canvas>
        </div>--}}
    </div>
</div>