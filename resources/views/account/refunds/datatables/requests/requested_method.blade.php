@if(@$row->requested_method)
<span class="text-green">
    <i class="fas fa-fw fa-check-circle"></i>
    {{ str_replace(' Bancária', '', trans('admin/refunds.refunds-methods.'.@$row->requested_method)) }}
</span>
@endif