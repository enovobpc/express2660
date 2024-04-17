@section('title')
    Conferir Valores por Motorista
@stop

@section('content-header')
    Conferir Valores por Motorista
@stop

@section('breadcrumb')
<li class="active">Conferir Valores por Motorista</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
               <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                  {{-- <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
                       <strong>Filtar Data</strong><br/>
                       <div class="w-100px m-r-4" style="position: relative; z-index: 5;">
                           {{ Form::select('date_unity', ['' => 'Data Envio', '3' => 'Transporte', '4' => 'Distribuição', '5' => 'Entregue'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                       </div>
                   </li>
                   <li class="shp-date col-xs-6">
                       <strong>Data</strong><br/>
                       <div class="input-group input-group-sm w-220px">
                           {{ Form::select('date_unity', ['' => 'Data Envio', '3' => 'Transporte', '4' => 'Distribuição', '5' => 'Entregue'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                           {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                           <span class="input-group-addon">até</span>
                           {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                       </div>
                   </li>--}}
                   <strong>Data</strong>
                   <div class="input-group input-group-sm w-230px">
                       {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : date('Y-m-d'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                       <span class="input-group-addon">até</span>
                       {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                   </div>
                   <li class="fltr-primary w-170px">
                       <strong>Tipo Data</strong><br class="visible-xs"/>
                       <div class="w-100px pull-left form-group-sm">
                           {{ Form::select('date_unity', ['' => 'Data Envio', '3' => 'Transporte', '4' => 'Distribuição', '5' => 'Entregue'], fltr_val(Request::all(), 'date_unity', 5), array('class' => 'form-control input-sm filter-datatable select2')) }}
                       </div>
                   </li>
                   <li class="fltr-primary w-190px">
                       <strong>Agência</strong><br class="visible-xs"/>
                       <div class="w-130px pull-left form-group-sm">
                           {{ Form::selectMultiple('agency', $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
                       </div>
                   </li>
                   <li class="fltr-primary w-200px">
                       <strong>Operador</strong><br class="visible-xs"/>
                       <div class="w-130px pull-left form-group-sm">
                           {{ Form::selectMultiple('agency', $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
                       </div>
                   </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table w-100 table-striped table-condensed table-dashed table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-60px">Data</th>
                                <th>Operador</th>
                                <th class="w-1">Guias</th>
                                <th class="w-60px">Reembolsos</th>
                                <th class="w-60px">Pg. Dest.</th>
                                <th class="w-60px">Total</th>
                                <th class="w-1">Conferido</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;

    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'date', name: 'date'},
                {data: 'operator', name: 'operator', orderable: false, searchable: false},
                {data: 'guides', name: 'guides', class: 'text-center'},
                {data: 'charge_price', name: 'charge_price', class: 'text-right'},
                {data: 'total_price_for_recipient', name: 'total_price_for_recipient', class: 'text-right'},
                {data: 'total', name: 'total', class: 'text-right'},
                {data: 'total_conferred', name: 'total_conferred', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.operator-refunds.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.operator   = $('select[name=operator]').val();
                    d.agency     = $('select[name=agency]').val();
                    d.date_unity = $('select[name=date_unity]').val();
                    d.date_min   = $('input[name=date_min]').val();
                    d.date_max   = $('input[name=date_max]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

</script>
@stop