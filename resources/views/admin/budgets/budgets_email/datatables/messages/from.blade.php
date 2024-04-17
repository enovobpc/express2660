@if($row->from_name)
    <b>{{ $row->from_name }}</b><br/>
    <i class="text-muted">{{ $row->from }}</i><br/>
@else
    <b>{{ $row->from }}</b><br/>
@endif
<i class="text-muted">
    {{ $row->created_at->format('Y-m-d H:i') }}
</i>

@if($row->attachments)
    <hr style="margin-top: 10px; margin-bottom: 0"/>
    <h4>Anexos</h4>
    @foreach($row->attachments as $attachment)
        <a href="{{ route('admin.budgets.messages.attachment', [$row->id, str_slug($attachment->name)]) }}" target="_blank" class="budget-attachment">
            <i class="fas fa-file"></i> {{ $attachment->name }}
        </a>
    @endforeach

@endif
