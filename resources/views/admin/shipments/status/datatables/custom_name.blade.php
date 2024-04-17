<?php

$alias = @$row->custom_attrs;
$alias = @$alias[config('app.source')];

?>

@if(!empty($alias))
    <a href="{{ route('admin.tracking.status.edit', [$row->id, 'custom' => '1']) }}" data-toggle="modal" data-target="#modal-remote">
        <i class="fas fa-square" style="color: {{ $row->color }}"></i> {{ $row->name }}
    </a>
    <br/>
    <small class="text-muted">{{ $row->description }}</small>
@endif
