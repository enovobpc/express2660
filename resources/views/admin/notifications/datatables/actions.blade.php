<a href="{{ route('admin.notifications.destroy', $row->id) }}"
   data-method="delete"
   data-confirm-title="{{ trans('admin/global.feedback.destroy.header') }}"
   data-confirm="{{ trans('admin/global.feedback.destroy.title') }}"
   class="btn btn-sm btn-default">
    <i class="fas fa-trash-alt"></i> {{ trans('admin/global.word.remove') }}
</a>
