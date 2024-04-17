@section('title')
    Suporte ao Cliente
@stop

@section('content-header')
    @trans('Suporte ao Cliente')
    <small>@trans('Pedido N.º') {{ $ticket->code }}</small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.customer-support.index') }}">
            @trans('Suporte ao Cliente')
        </a>
    </li>
    <li class="active">
        @trans('Pedido N.º') {{ $ticket->code }}
    </li>
@stop

@section('styles')
    <style>
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

        .ticket-status:hover {
            cursor: pointer;
            opacity: 0.7;
        }

    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 billing-header">
            @include('admin.customer_support.partials.header')
        </div>
    </div>

    <div class="row row-10">
        <div class="col-md-2">
            {{--@if(Auth::user()->allowedAction('show_budget_providers'))
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
                                @if(@$unreadProposes)
                                <span class="badge bg-yellow">{{ $unreadProposes }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif--}}
            <div class="box no-border m-b-15">
                <div class="box-body p-5 budget-inline-form" style="display: none;">
                    @include('admin.customer_support.partials.edit_inline')
                </div>
                <div class="box-body p-5 budget-inline-details">
                    <div class="ticket-status label {{ trans('admin/customers_support.status-labels.'.$ticket->status) }} m-r-10 m-t-1"
                         style="    font-size: 16px;
    padding: 9px;
    display: block;
    width: 100%;
    margin: 0 0 15px;
    border-radius: 3px;">
                        {{ trans('admin/customers_support.status.' . $ticket->status) }}
                    </div>
                    <div class="p-l-5 p-r-5">
                        <div style="margin-bottom: 5px; border-bottom: 1px solid #ddd">
                            <p>
                                <small class="text-blue"><i class="fas fa-clock"></i> @trans('DURAÇÃO DO PEDIDO')</small><br/>
                                @if($ticket->duration_hours > 72)
                                    <span class="text-red" data-toggle="tooltip" title="@trans('Ultrapassou o limite de 3 dias para resposta.')">
                                    <i class="fas fa-exclamation-triangle text-red"></i>
                                @endif
                                @if($ticket->status == \App\Models\CustomerSupport\Ticket::STATUS_CONCLUDED)
                                    {{ human_time($ticket->created_at, true, true, @$ticket->last_message->created_at) }}
                                @else
                                    {{ human_time($ticket->created_at, true, true) }}
                                @endif
                                @if($ticket->duration_hours > 72)
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div style="margin-bottom: 5px; border-bottom: 1px solid #ddd">
                            <p>
                                <small class="text-blue">
                                    <i class="fas fa-user"></i> @trans('CLIENTE')
                                </small>
                                <br/>
                                @if(!$ticket->customer_id)
                                    @trans('N/A')
                                    <a href="#" class="pull-right" data-toggle="modal" data-target="#modal-select-shipments" data-empty="1">
                                        <small>@trans('Associar')</small>
                                    </a>
                                @else
                                    <a href="{{ route('admin.customers.edit', $ticket->customer_id) }}"
                                       class="text-black"
                                       target="_blank">
                                        {{ @$ticket->customer->name }}
                                    </a>
                                @endif
                            </p>
                        </div>
                        <div style="margin-bottom: 5px; border-bottom: 1px solid #ddd">
                            <p>
                                <small class="text-blue">
                                    <i class="fas fa-truck"></i> @trans('ENVIO ASSOCIADO')
                                </small>
                                <br/>
                                @if(!$ticket->shipment_id)
                                    <button class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-select-shipments" data-empty="1">
                                        @trans('Associar') <i class="fas fa-external-link-square-alt"></i>
                                    </button>
                                @else
                                    <a href="{{ route('admin.shipments.show', $ticket->shipment_id) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote-xl"
                                        class="text-black">
                                        @trans('#TRK') {{ @$ticket->shipment->tracking_code }}
                                    </a>
                                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'shipments'))
                                        <a href="{{ route('admin.shipments.edit', $ticket->shipment_id) }}"
                                           class="text-black"
                                           data-toggle="modal"
                                           data-target="#modal-remote-xl">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endif
                                @endif
                            </p>
                        </div>
                        <div style="margin-bottom: 5px; border-bottom: 1px solid #ddd">
                            <p>
                                <small class="text-blue">
                                    <i class="fas fa-headset"></i> @trans('RESPONSÁVEL')
                                </small>
                                <br/>
                                @if(!$ticket->user_id)
                                    <a href="{{ route('admin.customer-support.edit', $ticket->id) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote-lg"
                                        class="btn btn-xs btn-default">
                                        @trans('Associar') <i class="fas fa-external-link-square-alt"></i>
                                    </a>
                                @else
                                    {{ $ticket->user->name }}
                                @endif
                            </p>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <p>
                                <small class="text-blue">
                                    <i class="fas fa-info-circle"></i> @trans('OBSERVAÇÕES INTERNAS')
                                </small>
                                <br/>
                                @if($ticket->obs)
                                    <span class="obs-preview">{!! nl2br($ticket->obs) !!}</span>
                                @else
                                    <span class="obs-preview">@trans('Sem observações.')</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.customer-support.edit', $ticket->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-lg"
                       class="btn btn-block btn-default btn-xs">
                        <i class="fas fa-pencil-alt"></i> @trans('Editar Pedido')
                    </a>
                </div>

            </div>
        </div>

        <div class="col-md-10">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-messages">
                        @include('admin.customer_support.partials.messages')
                    </div>
                   {{-- @if(Auth::user()->allowedAction('show_budget_providers'))
                    <div class="tab-pane" id="tab-proposes">
                        @include('admin.customer_support.partials.proposes')
                    </div>
                    @endif--}}
                </div>
            </div>
        </div>
    </div>
    @include('admin.customer_support.modals.shipments')
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function () {

            oTable = $('#datatable-messages').DataTable({
                dom: "<'row row-0'<'col-md-6 col-sm-8 datatable-filters-area'><'col-sm-3 datatable-paginate-top' p><'col-sm-4 col-md-3'f><'col-sm-12 datatable-filters-area-extended'>>" +
                "<'row row-0'<'col-sm-12'tr>>",
                columns: [
                    {data: 'from_name', name: 'from_name', visible: false},
                    {data: 'id', name: 'id'},
                    {data: 'subject', name: 'subject'},
                    {data: 'message', name: 'message', visible: false},
                ],
                ajax: {
                    url: "{{ route('admin.customer-support.messages.datatable', $ticket->id) }}",
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
                        url: "{{ route('admin.customer-support.datatable.shipments') }}",
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

        $('.btn-edit-inline, .ticket-status').on('click', function(){
            $('.budget-inline-form').show();
            $('.budget-inline-details').hide();
        })

        $('.btn-edit-cancel').on('click', function(){
            $('.budget-inline-form').hide();
            $('.budget-inline-details').show();
        })

        $("select[name=customer_id]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
        });
    </script>
@stop