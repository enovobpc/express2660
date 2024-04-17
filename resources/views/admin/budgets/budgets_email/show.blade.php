@section('title')
    Gestão Orçamental
@stop

@section('content-header')
    Gestão Orçamental
    <small>Pedido {{ $budget->budget_no }}</small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.budgets.index') }}">
            Gestão Orçamental
        </a>
    </li>
    <li class="active">
        Pedido {{ $budget->budget_no }}
    </li>
@stop

@section('styles')
    <style>
        /*.original-message {
            white-space: pre-wrap;
            font-family: inherit;
            font-size: 14px;
            margin: -11px -10px -10px -10px;
            border-right: 0;
            border-left: 0;
            line-height: 17px;
            background: #fff;
            border-radius: 0;
        }*/

      /*  .original-message {
            overflow: scroll;
        }*/

     /*   .message-expanded {
            max-height: 99999px;
        }
*/
        .MsoNormal {
            margin: 0 !important;
            line-height: 14px;
        }

        blockquote {
            line-height: 14px;
            font-size: 14px;
        }

        #tab-messages .table>thead>tr>th,
        #tab-messages .table>tbody>tr>th,
        #tab-messages .table>tfoot>tr>th,
        #tab-messages .table>thead>tr>td,
        #tab-messages .table>tbody>tr>td,
        #tab-messages .table>tfoot>tr>td {
            border-top: 2px solid #999;
        }

    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 billing-header">
            @include('admin.budgets.budgets_email.partials.header')
        </div>
    </div>

    <div class="row row-10">
        <div class="col-md-2">
            @if(Auth::user()->allowedAction('show_budget_providers'))
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active">
                            <a href="#tab-messages" data-toggle="tab" style="padding-right: 5px">
                                <i class="fas fa-fw fa-user"></i> Mensagens ao Cliente
                            </a>
                        </li>
                        <li>
                            <a href="#tab-proposes" data-toggle="tab" style="padding-right: 5px">
                                <i class="fas fa-fw fa-truck"></i> Pedido Fornecedores
                                @if($unreadProposes)
                                <span class="badge bg-yellow">{{ $unreadProposes }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
            <div class="box no-border m-b-15">
                <div class="box-body p-5 budget-inline-form" style="display: none;">
                    @include('admin.budgets.budgets_email.partials.edit_inline')
                </div>
                <div class="box-body p-5 budget-inline-details">
                    <button class="btn btn-xs btn-edit-inline pull-right">
                        <i class="fas fa-fw fa-pencil-alt"></i>Editar
                    </button>
                    <p>
                        <b>ESTADO</b>
                        <br/>
                        <span class="label label-{{ trans('admin/budgets.status-labels.'.$budget->status) }} m-r-10 m-t-1" style="font-size: 12px; padding: 1px 7px 3px;">
                            {{ trans('admin/budgets.status.' . $budget->status) }}
                        </span>
                    </p>
                    <hr style="margin: 5px"/>
                    <p>
                        <b>ENVIO ASSOCIADO</b>
                        <br/>
                        @if(!$budget->shipment_id)
                            <button class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-select-shipments" data-empty="1">
                                Associar ordem carga <i class="fas fa-external-link-square-alt"></i>
                            </button>
                            <a href="{{ route('admin.shipments.create') }}" class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-remote-xl" data-empty="1">
                                <i class="fas fa-plus"></i> Novo
                            </a>
                        @else
                            <a href="{{ route('admin.shipments.show', $budget->shipment_id) }}" data-toggle="modal" data-target="#modal-remote-xl">
                                #TRK {{ @$budget->shipment->tracking_code }}
                            </a>
                            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'shipments'))
                            <a href="{{ route('admin.shipments.edit', $budget->shipment_id) }}" class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-remote-xl">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            @endif
                            <button class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-select-shipments" data-empty="1">
                               <i class="fas fa-external-link-square-alt"></i>
                            </button>
                        @endif
                    </p>
                    @if($budget->provider || $budget->total)
                    <hr style="margin: 5px"/>
                    @endif
                    <div class="row row-0">
                        @if($budget->provider)
                        <div class="col-lg-8">
                            <p>
                                <b><i class="fas fa-truck"></i> FORNECEDOR</b>
                                <br/>
                                {{ $budget->provider }}
                            </p>
                        </div>
                        @endif

                        @if($budget->total)
                        <div class="col-lg-4">
                            <p>
                                <b><i class="fas fa-euro-sign"></i> PREÇO</b>
                                <br/>
                                {{ money($budget->total, Setting::get('app_currency')) }}
                            </p>
                        </div>
                        @endif
                    </div>

                    @if($budget->obs)
                    <hr style="margin: 5px"/>
                    <p>
                        <b>OBSERVAÇÕES</b>
                        <br/>
                        <span class="obs-preview">{!! nl2br($budget->obs) !!}</span>
                        {{ Form::textarea('obs', $budget->obs, ['class' => 'form-control hide', 'rows' => 4]) }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-10">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-messages">
                        @include('admin.budgets.budgets_email.partials.messages')
                    </div>
                    @if(Auth::user()->allowedAction('show_budget_providers'))
                    <div class="tab-pane" id="tab-proposes">
                        @include('admin.budgets.budgets_email.partials.proposes')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.budgets.budgets_email.modals.shipments')
@stop

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            var oTable = $('#datatable-messages').DataTable({
                dom: "<'row row-0'<'col-md-6 col-sm-8 datatable-filters-area'><'col-sm-3 datatable-paginate-top' p><'col-sm-4 col-md-3'f><'col-sm-12 datatable-filters-area-extended'>>" +
                "<'row row-0'<'col-sm-12'tr>>",
                columns: [
                    {data: 'from_name', name: 'from_name', visible: false},
                    {data: 'id', name: 'id'},
                    {data: 'subject', name: 'subject'},
                    {data: 'message', name: 'message', visible: false},
                ],
                ajax: {
                    url: "{{ route('admin.budgets.messages.datatable', $budget->id) }}",
                    type: "POST",
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            var oTableProposes = $('#datatable-proposes').DataTable({
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'to', name: 'to'},
                    {data: 'subject', name: 'subject'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.budgets.proposes.datatable', $budget->id) }}",
                    type: "POST",
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });
        });

        $(document).on('click', '.expand-message', function (e) {
            e.preventDefault();
            $(this).prev().toggleClass('message-expanded');
        })

        $(document).on('click', '[data-target="#modal-select-shipments"]', function() {

            var $tab = $(this);

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);
                var oTable = $('#datatable-shipments').DataTable({
                    columns: [
                        {data: 'tracking_code', name: 'tracking_code', visible: false},
                        {data: 'id', name: 'id'},
                        {data: 'sender_name', name: 'sender_name'},
                        {data: 'recipient_name', name: 'recipient_name'},
                        {data: 'service_id', name: 'service_id', searchable: false},
                        {data: 'volumes', name: 'volumes', searchable: false},
                        {data: 'status_id', name: 'status_id', searchable: false},

                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                        {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                        {data: 'sender_city', name: 'sender_city', visible: false},
                        {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                        {data: 'recipient_city', name: 'recipient_city', visible: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.budgets.datatable.shipments') }}",
                        type: "POST",
                        data: function (d) {
                            d.provider = $('#modal-select-shipments select[name=provider]').val()
                            d.status   = $('#modal-select-shipments select[name=status]').val()
                        },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });
            }

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });
        });

        $(document).on('click', '.code-read', function(){
            var trk = $(this).data('trk');
            $('[name="tracking_code"]').val(trk);

            $(document).find('.code-read').html('Associar');
            $(document).find('.code-read').removeClass('btn-success')
            $(document).find('.code-read').addClass('btn-default')
            $(this).addClass('btn-success')
            $(this).html('<i class="fas fa-check"></i> Associado')
        })

        $(document).on('click', '.btn-assign-shipment', function(){
            $('[name="tracking_code"]').closest('form').submit();
        })

        $('.btn-edit-inline').on('click', function(){
            $('.budget-inline-form').show();
            $('.budget-inline-details').hide();
        })

        $('.btn-edit-cancel').on('click', function(){
            $('.budget-inline-form').hide();
            $('.budget-inline-details').show();
        })
    </script>
@stop