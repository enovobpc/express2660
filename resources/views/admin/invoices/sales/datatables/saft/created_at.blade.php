@if($saft->issued)
    {{ @$saft->created_at ? @$saft->created_at->format('Y-m-d H:i') : '' }}
@endif