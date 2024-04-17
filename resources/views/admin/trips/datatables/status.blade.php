<?php
    $total     = $row->shipments->count();
    $concluded = $row->shipments->filter(function($item){
        return in_array($item->status_id, [
            \App\Models\ShippingStatus::DELIVERED_ID, 
            \App\Models\ShippingStatus::DOCS_RECEIVED_ID,
            \App\Models\ShippingStatus::BILLED_ID,
            \App\Models\ShippingStatus::CANCELED_ID, 
            \App\Models\ShippingStatus::DEVOLVED_ID]);
    })->count();

    $percent = $total > 0.00 ? (($concluded * 100) / $total) : 0;
?>

<div class="text-center">
@if($row->end_date && $row->end_kms && $concluded)
    <span class="label bg-success">@trans('Terminado')</span>
@elseif($row->start_date > date('Y-m-d'))
    <span class="label bg-warning">@trans('Agendada')</span>
@else
    <span class="label label-info">@trans('Em viagem')</span>
        <div class="text-center m-t-0" style="margin: 3px -1px -4px; z-index: 1;position: relative; line-height: 10px;">
            <table style="width: 100%">
                <tr>
                    <td style="width: {{ $percent }}%; height: 4px; background: #1da506"></td>
                    <td style="width: {{ (100 - $percent) }}%; height: 4px; background: rgba(204,204,204,0.94)"></td>
                </tr>
            </table>
            <small style="font-size: 10px">
                @if($percent == 100)
                    @trans('Terminado')
                @else
                    {{ money($percent, '%', 0) }}
                @endif
            </small>
        </div>
@endif
</div>



