{{ $row->total_items }}
@if(@$row->lines->count() != $row->total_items)
<br/>
<div class="text-red">
    <i class="fas fa-exclamation-triangle"></i> {{ @$row->lines->count() }}
</div>
@endif