<div class="lh-1-3">
    @if($row->from_name)
        <div><b>{{ $row->from_name }}</b></div>
        <small><i class="text-muted">{{ $row->from }}</i></small>
    @else
        <b>{{ $row->from }}</b><br/>
    @endif
</div>
<small>
    <i class="text-muted">
        {{ $row->created_at->format('Y-m-d H:i') }}
    </i>
</small>


@if($row->inline_attachments || !@$row->attachments->isEmpty())
    <hr style="margin-top: 10px; margin-bottom: 0"/>
    <p class="bold m-b-2">Anexos</p>
    @if($row->inline_attachments)
        @foreach($row->inline_attachments as $attachment)
            <a href="{{ route('account.customer-support.messages.attachment', [$row->id, str_slug($attachment->name)]) }}" target="_blank" class="budget-attachment">
                <i class="fas fa-file"></i> {{ $attachment->name }}
            </a>
        @endforeach
    @endif

    @if(!@$row->attachments->isEmpty())
        @foreach($row->attachments as $attachment)
            <a href="{{ asset($attachment->filepath) }}" target="_blank" class="budget-attachment">
                <i class="fas fa-file"></i> {{ substr($attachment->filename, 0, 8).'(...)'.substr($attachment->filename, -8) }}
            </a>
        @endforeach
    @endif
@endif
