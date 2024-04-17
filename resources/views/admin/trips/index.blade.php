@section('title')
    @if(app_mode_cargo())
    @trans('Mapas de Viagem')
    @else
    @trans('Mapas de Distribuição')
    @endif
@stop

@section('content-header')
    @if(app_mode_cargo())
    @trans('Mapas de Viagem')
    @else
    @trans('Mapas de Distribuição')
    @endif
@stop

@section('breadcrumb')
    <li class="active">
        @if(app_mode_cargo())
        @trans('Mapas de Viagem')
        @else
        @trans('Gestão de Distribuição')
        @endif
    </li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.trips.create') }}"
                           class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    
                    <li>
                        @include('admin.trips.partials.tools_button')
                    </li>
                    
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li class="fltr-primary w-270px">
                        <strong>@trans('Data')</strong><br class="visible-xs"/>
                        <div class="pull-left input-group input-group-sm w-220px">
                            {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                            <span class="input-group-addon">@trans('até')</span>
                            {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-200px">
                        <strong>@trans('Motorista')</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-130px">
                            {{ Form::selectMultiple('operator', array('not-assigned' => 'Sem operador') + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        @if(count($agencies) > 1)
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Agência')</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('agency', $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        @endif
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Pais Inicio')</strong><br/>
                            <div class="w-130px">
                                {{ Form::selectMultiple('start_country', trans('country'), fltr_val(Request::all(), 'start_country'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Pais Fim')</strong><br/>
                            <div class="w-130px">
                                {{ Form::selectMultiple('end_country', trans('country'), fltr_val(Request::all(), 'end_country'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Viatura')</strong><br/>
                            <div class="w-100px">
                                {{ Form::selectMultiple('vehicle', $vehicles, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        @if($trailers)
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>@trans('Reboque')</strong><br/>
                                <div class="w-100px">
                                    {{ Form::selectMultiple('trailer', $trailers, fltr_val(Request::all(), 'trailer'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                        @endif
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Acompanhante')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('assistant', ['' => __('Todos')] + ($operators ?? []), fltr_val(Request::all(), 'assistant'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        {{--<li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Rota Carga</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('pickup_route', $routes, fltr_val(Request::all(), 'pickup_route'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>--}}
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Rota')</strong><br/>
                            <div class="w-120px">
                                {{ Form::selectMultiple('delivery_route', $routes, fltr_val(Request::all(), 'delivery_route'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>@trans('Subcontrato')</strong><br/>
                                <div class="w-160px">
                                    {{ Form::selectMultiple('provider', ['all' => __('Todos')] +  $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple select2-multiple-special')) }}
                                </div>
                            </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Conclusão')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('concluded', [''=>__('Todos'), '1' => __('Concluído'), '0' => __('Por concluir')], fltr_val(Request::all(), 'concluded'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            @if(app_mode_cargo())
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th class="w-60px">@trans('Viagem')</th>
                                    <th>@trans('Local Início')</th>
                                    <th>@trans('Local Fim')</th>
                                    <th class="w-70px">@trans('Data Início')</th>
                                    <th class="w-70px">@trans('Data Fim')</th>
                                    <th>@trans('Motorista')</th>
                                    <th class="w-65px">@trans('Viatura')</th>
                                    <th class="w-30px"><i class="fas fa-truck"></i></th>
                                    <th class="w-30px"><i class="fas fa-boxes"></i></th>
                                    <th class="w-80px">@trans('Carga')</th>
                                    <th class="w-30px">@trans('Kms')</th>
                                    @if(Auth::user()->showPrices())
                                    <th class="w-60px">@trans('Preço')</th>
                                    @endif
                                    <th class="w-60px">@trans('Retorno')</th>
                                    <th class="w-1">@trans('Estado')</th>
                                    <th class="w-1">@trans('Ações')</th>
                                </tr>
                            @else
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th class="w-1">@trans('Folha')</th>
                                    <th class="w-70px">@trans('Data')</th>
                                    <th>@trans('Motorista')</th>
                                    <th class="w-100px">@trans('Rota')</th>
                                    <th class="w-90px">@trans('Período')</th>
                                    <th class="w-80px">@trans('Viatura')</th>
                                    <th class="w-50px">@trans('Kms')</th>
                                    <th class="w-30px"><i class="fas fa-truck"></i></th>
                                    <th class="w-30px"><i class="fas fa-boxes"></i></th>
                                    <th class="w-60px">@trans('Peso KG')</th>
                                    @if(Auth::user()->showPrices())
                                        <th class="w-50px">@trans('COD')</th>
                                        <th class="w-60px">@trans('Preço')</th>
                                    @endif
                                    <th class="w-1">@trans('Estado')</th>
                                    <th class="w-1">@trans('Ações')</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.trips.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" 
                        data-action="confirm" 
                        data-title="@trans('Apagar selecionados')">
                        <i class="fas fa-trash-alt"></i> @trans('Apagar')</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
    <style>
        .brdlft {
            border-left: 2px solid #000 !important;
        }
    </style>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;

    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'sort', name: 'sort'},

                @if(Setting::get('app_mode') == 'cargo')
                    {data: 'start_location', name: 'start_location'},
                    {data: 'end_location', name: 'end_location'},
                    {data: 'pickup_date', name: 'pickup_date'},
                    {data: 'delivery_date', name: 'delivery_date'},
                    {data: 'operator_id', name: 'operator_id', searchable: false},
                    {data: 'vehicle', name: 'vehicle'},
                    {data: 'count', name: 'count', class: 'text-center brdlft', orderable: false, searchable: false},
                    {data: 'volumes', name: 'volumes', orderable: false, class: 'text-center', searchable: false},
                    {data: 'weight', name: 'weight', orderable: false, searchable: false},
                    {data: 'kms', name: 'kms'},
                    @if(Auth::user()->showPrices())
                        {data: 'total', name: 'total', orderable: false, searchable: false},
                    @endif
                    {data: 'children_code', name: 'children_code', orderable: false, searchable: false},
                @else
                    {data: 'pickup_date', name: 'pickup_date'},
                    {data: 'operator_id', name: 'operator_id', searchable: false},
                    {data: 'delivery_route_id', name: 'delivery_route_id', searchable: false},
                    {data: 'period_id', name: 'period_id', orderable: false, searchable: false},
                    {data: 'vehicle', name: 'vehicle'},
                    {data: 'kms', name: 'kms'},
                    {data: 'count', name: 'count', class: 'text-center brdlft', orderable: false, searchable: false},
                    {data: 'volumes', name: 'volumes', orderable: false, class: 'text-center', searchable: false},
                    {data: 'weight', name: 'weight', orderable: false, searchable: false},
                    @if(Auth::user()->showPrices())
                    {data: 'cod', name: 'cod', orderable: false, searchable: false},
                    {data: 'total', name: 'total', orderable: false, searchable: false},
                    @endif
                @endif
                {data: 'status', name: 'status', class: 'brdlft', orderable: false, orderable: false, searchable: false},
                {data: 'actions', name: 'actions', class: 'w-1', orderable: false, searchable: false},
                {data: 'code', name: 'code', visible: false},
                {data: 'parent_code', name: 'parent_code', visible: false},
                {data: 'obs', name: 'obs', visible: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.trips.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.date_min  = $('input[name=date_min]').val();
                    d.date_max  = $('input[name=date_max]').val();
                    d.agency    = $('select[name=agency]').val();
                    d.operator  = $('select[name=operator]').val();
                    d.auxiliar  = $('select[name=auxiliar]').val();
                    d.provider  = $('select[name=provider]').val();
                    d.vehicle   = $('select[name=vehicle]').val();
                    d.trailer   = $('select[name=trailer]').val();
                    d.start_country     = $('select[name=start_country]').val();
                    d.end_country       = $('select[name=end_country]').val();
                    d.concluded         = $('select[name=concluded]').val();
                    d.pickup_route      = $('select[name=pickup_route]').val();
                    d.delivery_route    = $('select[name=delivery_route]').val();
                    d.assistant         = $('select[name=assistant]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                //error: function () { Datatables.error(); }
            },
        });

        $('.filter-datatable').on('change', function (e) {
            e.preventDefault();
            oTable.draw();

            $('[data-toggle="export-url"]').each(function() {
                var exportUrl = Url.removeQueryString($(this).attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())

                $('.datatable-filters-area-extended [type="checkbox"]').each(function(){ //add checkbox filters
                    checkStatus = $(this).is(':checked') ? 1 : 0;
                    varName = $(this).attr('name');
                    exportUrl+= '&'+varName+'=' + checkStatus;
                })

                $(this).attr('href', exportUrl);
            })
           
            if(!$('.datepicker-dropdown').is(':visible')) {
                oTable.draw();
                e.preventDefault();
            }
        });

        $(document).on('click', '[data-s-filter]', function(e){
            e.preventDefault();
            var fltr = $(this).data('s-filter');
            oTable.search(fltr).draw();
        });
    });
</script>
@stop