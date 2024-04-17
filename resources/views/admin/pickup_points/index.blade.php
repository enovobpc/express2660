@section('title')
    Pickup Points
@stop

@section('content-header')
    Pickup Points
@stop

@section('breadcrumb')
    <li class="active">@trans('Entidades')</li>
    <li class="active">@trans('Pickup Points')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.pickup-points.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <a href="#" class="btn btn-default btn-sm btn-webservice-sync" data-toggle="modal" data-target="#modal-sync-pudo">
                            <i class="fas fa-fw fa-sync-alt"></i> @trans('Sincronizar Pontos Pickup')
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left m-b-5">
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>@trans('Fornecedor')</strong><br/>
                            <div class="w-120px">
                                {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>@trans('País')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('fltr_country', ['' => 'Todos'] + trans('country'), fltr_val(Request::all(), 'fltr_country'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>@trans('Distrito')</strong><br/>
                            <div class="w-120px">
                                {{ Form::select('fltr_district', ['' => 'Todos'] + trans('districts_codes.districts.pt'), fltr_val(Request::all(), 'fltr_district'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>@trans('Concelho')<i class="fas fa-spin fa-circle-notch load-county" style="display: none"></i></strong><br/>
                            <div class="w-120px">
                                {{ Form::select('fltr_county', $recipientCounties ? ['' => __('Todos')] + $recipientCounties : ['' => 'Selec. Distrito'], fltr_val(Request::all(), 'fltr_recipient_county'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>@trans('Código Postal')</strong><br/>
                            <div class="w-90px" data-toggle="tooltip" title="Separe vários codigos postais por vírgula.">
                                {{ Form::text('fltr_zp', fltr_val(Request::all(), 'fltr_zp'), array('class' => 'form-control input-sm filter-datatable', 'style' => 'width:100%')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>@trans('Ativo')</strong><br/>
                            <div class="w-80px">
                                {{ Form::select('active', ['' => __('Todos'), '1' => __('Ativo'), '0' => __('Inativo')], fltr_val(Request::all(), 'active'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                    <ul class="list-inline pull-left m-l-10 m-t-20">
                        <li>
                            <a href="{{ route('admin.pickup-points.index') }}" class="cleanflr">
                                <i class="fas fa-eraser"></i> @trans('Limpar Filtros')
                            </a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">@trans('Fornecedor')</th>
                                <th class="w-1">@trans('Código')</th>
                                <th>@trans('Nome')</th>
                                <th class="w-350px">@trans('Contacto')</th>
                                <th class="w-1"><i class="fas fa-flag"></i></th>
                                <th class="w-1"><span data-toggle="tooltip" title="Aberto no Sabado">@trans('Sábado')</span></th>
                                <th class="w-1"><span data-toggle="tooltip" title="Aberto no Domingo">@trans('Domingo')</span></th>
                                <th class="w-1"><span data-toggle="tooltip" title="Aberto no Domingo">@trans('Ativo')</span></th>
                                <th class="w-65px">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.pickup-points.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.pickup_points.modal.sync_history')
@stop

@section('scripts')
<script type="text/javascript">
    var oTable
    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'provider_id', name: 'provider_id', class: 'text-center', orderable: false, searchable: false},
                {data: 'code', name: 'code', class: 'text-center'},
                {data: 'name', name: 'name'},
                {data: 'contact', name: 'contact', orderable: false, searchable: false},
                {data: 'country', name: 'country', class: 'text-center', orderable: false, searchable: false},
                {data: 'saturday', name: 'saturday', class: 'text-center', orderable: false, searchable: false},
                {data: 'sunday', name: 'sunday', class: 'text-center', orderable: false, searchable: false},
                {data: 'is_ative', name: 'is_ative', class: 'text-center', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'address', name: 'address', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.pickup-points.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.provider  = $('select[name=provider]').val();
                    d.country   = $('select[name=fltr_country]').val();
                    d.district  = $('select[name=fltr_district]').val();
                    d.county    = $('select[name=fltr_county]').val();
                    d.zip_code  = $('input[name=fltr_zp]').val();
                    d.active    = $('select[name=active]').val();
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

        $(document).on('click', '[data-s-filter]', function(e){
            e.preventDefault();
            var fltr = $(this).data('s-filter');
            oTable.search(fltr).draw();
        })

        $(document).on('change', '[name=fltr_district]', function(){
            var district = $(this).val();
            $('.load-county').show();

            $.post('{{ route('admin.shipments.get.counties') }}', {district:district}, function (data) {
                $('[name=fltr_county]').empty().select2({data:data}).trigger('change');
            }).always(function () {
                $('.load-county').hide();
            })
        })

        //show concluded shipments
        $(document).on('change', '[name="active"]', function (e) {
            oTable.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);
        });
    });
</script>
@stop