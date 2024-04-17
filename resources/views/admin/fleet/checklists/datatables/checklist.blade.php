<a href="{{ route('admin.fleet.checklists.answer.details', [$row->checklist_id, $row->control_hash]) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ @$row->checklist->title }}
</a>