
<ul class="datatable-filters list-inline {{ $budget->messages->isEmpty() ? '' : 'hide' }} pull-left" data-target="#datatable-messages">
    <li>
        <a href="{{ route('admin.budgets.messages.create', $budget->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
            <i class="fas fa-envelope"></i> Nova Resposta
        </a>
    </li>
    @if(!$budget->user_id)
    <li>
        <a href="{{ route('admin.budgets.adjudicate', $budget->id) }}" class="btn btn-info btn-sm" data-method="post" data-confirm-title="Adjudicar Orçamento" data-confirm-class="btn-success" data-confirm-label="Adjudicar" data-confirm="Pretende ficar responsável por este orçamento?">
            <i class="fas fa-user-plus"></i> Adjudicar-me Orçamento
        </a>
    </li>
    @endif
</ul>
@if($budget->messages->isEmpty())
    <div class="clearfix"></div>
    <div class="sp-15"></div>
@else
<table id="datatable-messages" class="table table-condensed">
    <thead>
        <tr>
            <th></th>
            <th class="w-180px">De</th>
            <th>Mensagem</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
@endif
<table class="table table-condensed">
    <tr>
        <td style="width: 205px">
            @if($budget->name)
                <b>{{ $budget->name }}</b><br/>
                <i class="text-muted">{{ $budget->email }}</i><br/>
            @else
                <b>{{ $budget->name }}</b><br/>
            @endif
            <i class="text-muted">
                {{ $budget->created_at->format('Y-m-d H:i') }}
            </i>

            @if($budget->attachments)
                <hr style="margin-top: 10px; margin-bottom: 0"/>
                <h4>Anexos</h4>
                @foreach($budget->attachments as $attachment)
                    <a href="{{ route('admin.budgets.attachment', [$budget->id, str_slug($attachment->name)]) }}" target="_blank" class="budget-attachment">
                        <i class="fas fa-file"></i> {{ $attachment->name }}
                    </a>
                @endforeach
            @endif
        </td>
        <td>
            <?php
            $content = $budget->message;
            $content = str_replace('style', 'style2', $content);
            ?>
            {!! $content !!}
        </td>
    </tr>
</table>