<div class="nav-tabs-custom">
    <ul class="nav nav-tabs nav-tab-url">
        @if($tab == 'monthly')
        <li class="active">
        @else
        <li>
        @endif
        <a href="{{ route('admin.website.visits.index', array('tab' => 'monthly')) }}">Mensal</a></li>

        @if($tab == 'yearly')
        <li class="active">
        @else
        <li>
        @endif
            <a href="{{ route('admin.website.visits.index', array('tab' => 'yearly')) }}">Anual</a></li>

        @if($tab == 'daily')
        <li class="active">
            @else
        <li>
            @endif
            <a href="{{ route('admin.website.visits.index', array('tab' => 'daily')) }}">Diário</a></li>
    </ul>
    <div class="tab-content no-padding">
        
        <div class="chart tab-pane active" id="revenue-chart">
            <div class="col-sm-12">
                <div class="h-10px"></div>
       
                {{ Form::open(array('class' => 'form-inline', 'method' => 'get')) }}
                <div class="form-group">
                    {{ Form::label('filter-analytics', 'Visitas:')  }}
                    {{ Form::select('filter-analytics', $filters, @$filter, array('class' => 'form-control')) }}
                </div>


                @if($filter == 'custom')
                <span id="filter-analytics-search" class="m-l-15">
                @else
                <span id="filter-analytics-search" class="m-l-15" style="display: none">
                @endif

                    @if($tab == 'daily')
                    <div class="form-group">
                        {{ Form::label('start', 'Data:')  }}
                        {{ Form::text('start', Request::get('start'), array('class' => 'form-control datepicker')) }}
                    </div>
                    @endif

                    @if($tab == 'monthly')
                    <div class="form-group">
                        {{ Form::label('start', 'De:')  }}
                        {{ Form::text('start', Request::get('start'), array('class' => 'form-control datepicker')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('end', 'até:')  }}
                        {{ Form::text('end', Request::get('end'), array('class' => 'form-control datepicker')) }}
                    </div>
                    @endif

                    <div class="form-group">
                        <button href="#" class="btn btn-default" id="submit-filter-analytics">Filtrar</button>
                    </div>
                </span>
                {{ Form::close() }}
                <hr/>
            </div>
            
            
            <div class="col-sm-12">
                <div class="chart ">
                    <canvas id="analyticsChart" height="290"></canvas>
                    <hr/>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <h5 class="description-header">
                            {{ @$analyticsTotals['visits'] }}<br/>
                        </h5>
                        <span class="description-text" data-toggle="tooltip" title="Número total de visitas no período selecionado.">Visitas</span>
                    </div>
                </div>

                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <h5 class="description-header">
                            {{ money(@$analyticsTotals['percentNewSessions']) }}%<br/>
                        </h5>
                        <span class="description-text" data-toggle="tooltip" title="Percentagem de novos visitantes no período selecionado.">Novos Visitantes</span>
                    </div>
                </div>

                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <h5 class="description-header">
                            {{ money(@$analyticsTotals['avgSessionDuration']) }}<br/>
                        </h5>
                        <span class="description-text" data-toggle="tooltip" title="Duração média das visitas no período selecionado.">Duração Média</span>
                    </div>
                </div>

                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <h5 class="description-header">
                            {{ money(@$analyticsTotals['bounceRate']) }}%<br/>
                        </h5>
                        <span class="description-text"  data-toggle="tooltip" title="Percentagem de visitas em que a pessoa abandonou o site na página de entrada sem ter interagido com a mesma.">Taxa de Rejeições</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>