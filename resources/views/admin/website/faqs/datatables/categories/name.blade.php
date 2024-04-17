<a href="{{ route('admin.website.faqs.categories.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->name }}
</a>
{!! Html::localesOverview($row, 'name') !!}