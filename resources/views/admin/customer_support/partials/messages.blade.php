
<ul class="datatable-filters list-inline {{ $ticket->messages->isEmpty() ? '' : 'hide' }} pull-left" data-target="#datatable-messages">
    <li>
        <a href="{{ route('admin.customer-support.messages.create', $ticket->id) }}"
           class="btn btn-primary btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-envelope"></i> @trans('Nova Resposta')
        </a>
    </li>
    @if($ticket->status != \App\Models\CustomerSupport\Ticket::STATUS_CONCLUDED)
        <li>
            <a href="{{ route('admin.customer-support.conclude', $ticket->id) }}"
               class="btn btn-success btn-sm"
               data-method="post"
               data-confirm-title="@trans('Concluir pedido de suporte')"
               data-confirm-class="btn-success"
               data-confirm-label="@trans('Concluir')"
               data-confirm="@trans('Pretende finalizar o pedido de suporte e marca-lo como concluído?')">
                <i class="fas fa-check"></i> @trans('Fechar Pedido')
            </a>
        </li>
    @endif
    @if(!$ticket->user_id)
    <li>
        <a href="{{ route('admin.customer-support.adjudicate', $ticket->id) }}"
           class="btn btn-info btn-sm"
           data-method="post"
           data-confirm-title="@trans('Adjudicar pedido de suporte')"
           data-confirm-class="btn-success"
           data-confirm-label="@trans('Adjudicar')"
           data-confirm="@trans('Pretende ficar responsável por este pedido de suporte?')">
            <i class="fas fa-user-plus"></i> @trans('Adjudicar-me Pedido Suporte')
        </a>
    </li>
    @endif
</ul>
@if($ticket->messages->isEmpty())
    <div class="clearfix"></div>
    <div class="sp-15"></div>
@else
<table id="datatable-messages" class="table table-condensed">
    <thead>
        <tr>
            <th></th>
            <th class="w-180px">@trans('De')</th>
            <th>@trans('Mensagem')</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
@endif
<table class="table table-condensed">
    <tr>
        <td style="width: 205px">
            @if($ticket->name)
                <b>{{ $ticket->name }}</b><br/>
                <i class="text-muted">{{ $ticket->email }}</i><br/>
            @else
                <b>{{ $ticket->name }}</b><br/>
            @endif
            <i class="text-muted">
                {{ $ticket->created_at->format('Y-m-d H:i') }}
            </i>

            @if($ticket->inline_attachments || !@$ticket->attachments->isEmpty())
                <hr style="margin-top: 10px; margin-bottom: 0"/>
                <h4>@trans('Anexos')</h4>
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