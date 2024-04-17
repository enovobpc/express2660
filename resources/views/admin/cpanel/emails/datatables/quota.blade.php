<?php

    $quota = $row->quota * (1000000);
    $usage = $row->usage * (1024 * 1000);

    $percent = $row->quota ? ($row->usage * 100) / $row->quota : 0;

    $color = '#5cb85c';
    if($percent <= 60) {
        $color = '#5cb85c'; //green
    } else if($percent > 60 && $percent <= 70) {
        $color = '#ffd400';  //yellow
    } else if($percent > 70 && $percent <= 80) {
        $color = '#FF8A18';  //orange
    } else if($percent > 80) {
        $color = '#F90000';  //red
    }
?>
<div style="width: 250px; color: #000;">
    <div class="m-b-2 pull-right text-right">
        {{ human_filesize($usage) }}/ <b>{!! $row->quota ? human_filesize($quota) : '<i class="fas fa-infinity"></i>' !!}</b>
    </div>
    <div class="m-b-2 pull-left">
        <span class="progress-loading" style="display: none"><i class="fas fa-spin fa-circle-notch"></i> </span><small>{{ money($percent, '%') }}</small>
    </div>
    <div class="clearfix"></div>
    <table class="quota-progress" cellpadding="0" cellspacing="0">
        <tr>
            <td class="quota-progress-left" style="width: {{ $percent }}%; background: {{ $color }}"></td>
            <td class="quota-progress-right" style="width: {{ 100-$percent }}%;"></td>
        </tr>
    </table>
</div>