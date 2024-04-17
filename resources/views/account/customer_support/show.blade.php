@section('title')
    {{ trans('account/customers-support.title') }} -
@stop

@section('account-content')

    <div class="account-panel-header">
        <div class="row">
            <div class="col-sm-10">
                <a href="{{ route('account.customer-support.index') }}"
                   class="btn btn-default pull-left m-r-5 panel-header-back-btn">
                    <i class="fa fa-angle-left"></i>
                </a>
                <div class="pull-left">
                    <h4 class="m-t-0 m-b-5 bold">
                        #{{ $ticket->code }} - {{ $ticket->subject }}
                    </h4>
                    <p class="text-muted m-0">
                        <span class="label label-{{ trans('admin/customers_support.categories-labels.'.$ticket->category) }}">
                            {{ trans('admin/customers_support.categories.'.$ticket->category) }}
                        </span>
                        &nbsp;&nbsp;|&nbsp;
                        <small><i class="far fa-clock"></i> {{ $ticket->created_at }}</small>

                        @if($ticket->shipment_id)
                            &nbsp;&nbsp;|&nbsp;
                        <small><i class="fas fa-truck"></i> Envio TRK#
                            <a href="{{ route('account.shipments.show', $ticket->shipment_id) }}"
                               data-toggle="modal"
                               data-target="#modal-remote-xl">{{ $ticket->shipment->tracking_code }}</a>
                        </small>
                        @endif
                    </p>
                </div>

            </div>
            <div class="col-sm-2">
                <div class="pull-right margin-top-7">
                    <span class="fs-20px label {{ trans('admin/customers_support.status-labels.'.$ticket->status) }}"
                          style="font-size: 20px;margin-top: 6px;display: block;">
                        {{ trans('admin/customers_support.status.'.$ticket->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        <li>
            <a href="{{ route('account.customer-support.messages.create', $ticket->code) }}"
               class="btn btn-sm btn-black"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-plus"></i> Nova Mensagem
            </a>
        </li>
        @if($ticket->status != \App\Models\CustomerSupport\Ticket::STATUS_CONCLUDED)
            <li>
                <a href="{{ route('account.customer-support.conclude', $ticket->code) }}"
                   class="btn btn-success btn-sm"
                   data-method="post"
                   data-confirm-title="Concluir pedido de suporte"
                   data-confirm-class="btn-success"
                   data-confirm-label="Concluir"
                   data-confirm="Pretende finalizar o pedido de suporte e marca-lo como concluído? Pode reabrir a qualquer momento.">
                    <i class="fas fa-check"></i> Fechar Pedido
                </a>
            </li>
        @endif
    </ul>
    <div class="table-responsive w-100">
        @if($ticket->messages->isEmpty() && $ticket->status == \App\Models\CustomerSupport\Ticket::STATUS_PENDING)
            <div class="alert bg-blue text-white">
                <h4 class="m-0"><i class="fas fa-info-circle"></i> Este pedido ainda não tem respostas</h4>
                <p class="m-0">O seu pedido ainda não tem respostas por parte do apoio ao cliente.</p>
            </div>
        @endif
        <table id="datatable" class="table table-condensed table-hove" style="margin: 0 !important; border-bottom: none;">
            <thead>
                <tr>
                    <th></th>
                    {{--<th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>--}}
                    <th class="w-160px">{{ trans('account/global.word.from') }}</th>
                    <th>{{ trans('account/global.word.message') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <table class="table table-condensed" style="border-top: none;">
            <tr>
                <td style="width: 205px">
                    <div>
                    @if($ticket->name)
                        <b>{{ $ticket->name }}</b><br/>
                        <small><i class="text-muted">{{ $ticket->email }}</i></small>
                    @else
                        <b>{{ $ticket->name }}</b><br/>
                    @endif
                    </div>
                    <small>
                        <i class="text-muted">{{ $ticket->created_at->format('Y-m-d H:i') }}</i>
                    </small>

                    @if($ticket->inline_attachments || !@$ticket->attachments->isEmpty())
                        <hr style="margin-top: 10px; margin-bottom: 0"/>
                        <p class="bold m-b-2">Anexos</p>
                        @if($ticket->inline_attachments)
                            @foreach($ticket->inline_attachments as $attachment)
                                <a href="{{ route('admin.customer-support.messages.attachment', [$ticket->id, str_slug($attachment->name)]) }}" target="_blank" class="budget-attachment">
                                    <i class="fas fa-file"></i> {{ $attachment->name }}
                                </a>
                            @endforeach
                        @endif

                        @if(!@$ticket->attachments->isEmpty())
                            @foreach($ticket->attachments as $attachment)
                                <a href="{{ asset($attachment->filepath) }}" target="_blank" class="budget-attachment">
                                    <i class="fas fa-file"></i> {{ substr($attachment->filename, 0, 8).'(...)'.substr($attachment->filename, -8) }}
                                </a>
                            @endforeach
                        @endif
                    @endif
                </td>
                <td>
                    <?php
                    $content = $ticket->message;
                    $content = str_replace('style', 'style2', $content);
                    ?>
                    {!! $content !!}
                </td>
            </tr>
        </table>
    </div>
    <div class="selected-rows-action hide">
        <div>
            {{--{{ Form::open(array('route' => 'account.customer-support.selected.destroy')) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-confirm-title="{{ trans('account/global.word.destroy-selected') }}">
                <i class="fas fa-trash-alt"></i> {{ trans('account/global.word.destroy-selected') }}
            </button>
            {{ Form::close() }}--}}
        </div>
    </div>
@stop

@section('styles')
    <style>
        .budget-attachment {
            padding: 3px 5px;
            margin-bottom: 5px;
            background: #eee;
            border: 1px solid #ccc;
            border-radius: 3px;
            color: #555;
            display: block;
            width: 97%;
            font-size: 12px;
            line-height: 14px;
        }

         .MsoNormal {
             margin: 0 !important;
             line-height: 14px;
         }

        blockquote {
            line-height: 14px;
            font-size: 14px;
        }
    </style>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            dom: "<'row row-0'<'col-md-6 col-sm-8 datatable-filters-area'><'col-sm-3 datatable-paginate-top' p><'col-sm-4 col-md-3'f><'col-sm-12 datatable-filters-area-extended'>>" +
                "<'row row-0'<'col-sm-12'tr>>",
            columns: [
                {data: 'id', name: 'id', visible: false},
               /* {data: 'select', name: 'select', orderable: false, searchable: false},*/
                {data: 'created_at', name: 'created_at'},
                {data: 'message', name: 'message'}
            ],
            order: [[1, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.customer-support.messages.datatable', $ticket->code) }}",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop