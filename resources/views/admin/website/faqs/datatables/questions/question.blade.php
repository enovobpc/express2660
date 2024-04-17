<a href="{{ route('admin.website.faqs.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ $row->question }}
</a>
{!! Html::localesOverview($row, 'question') !!}