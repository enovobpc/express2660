<a href="{{ route('admin.website.testimonials.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-lg">
    {{ str_limit($row->message) }}
</a>
{!! Html::localesOverview($row, 'message') !!}