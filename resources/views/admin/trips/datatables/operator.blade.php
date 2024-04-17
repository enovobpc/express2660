{{ @$row->operator->name }}
@if(!empty($row->assistants))
    <div>
        <small class="italic text-muted">
            +{{ implode(', ', $row->assistants()->pluck('name')->toArray()) }}
        </small>
    </div>
@endif