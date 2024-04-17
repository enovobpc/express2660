<div>
    <?php $row->type = $row->type ? $row->type : 'other'; ?>
    {{ trans('admin/expenses.types.'.$row->type) }}
</div>