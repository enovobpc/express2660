<?php
$count = $row->workgroups->count();
$html  = '';
foreach($row->workgroups as $key => $workgroup) {
    $html.= $workgroup->name .'<br/>';
}
?>
@if($count > 2)
    {{ $row->workgroups->first()->name }}
    <br/>
    <small class="text-muted cursor-pointer"
        data-toggle="popover"
        data-title="Perfís do utilizador"
        data-html="true"
        data-content="{!! $html !!}">
        +{{ $count - 2 }} @trans('grupos') <i class="fas fa-external-link-square-alt"></i>
    </small>
@else
    @foreach($row->workgroups as $key => $workgroup)
        {{ $workgroup->name }}<br/>
    @endforeach
@endif
