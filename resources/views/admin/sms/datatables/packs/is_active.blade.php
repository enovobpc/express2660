@if($row->is_active && $row->remaining_sms > 0)
    <i class="fas fa-check-circle text-green"></i>
@else
    <i class="fas fa-times-circle text-muted"></i>
@endif