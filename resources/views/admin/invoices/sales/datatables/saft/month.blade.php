<div style="display: none">{{ @$saft->year.str_pad(@$saft->month, 2, 0, STR_PAD_LEFT) }}</div>

@if($saft->issued)
    <div class="bold text-green">
        <i class="fas fa-check-circle"></i> {{ @$saft->year }} {{ trans('datetime.month.' .@$saft->month) }}
    </div>
    @else
    <div class="bold text-red">
        <i class="fas fa-times-circle"></i> {{ @$saft->year }} {{ trans('datetime.month.' .@$saft->month) }}
    </div>
@endif
