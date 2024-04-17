<a href="#" class="edit-datatable-field">
    {{ $row->name }}
</a>
<div class="clearfix"></div>
{{ Form::open(['route' => ['admin.attributes-declarations.update', $row->id], 'method' => 'PUT', 'data-remote' => 'true', 'class' => "form-edit-datatable-field form-inline hide"]) }}
{{Form::hidden('type', $row->getTable())}}
<div class="input-group" style='width: 100%'>
    {{ Form::text('name', $row->name, ['class' => 'form-control input-sm target-datatable-field']) }}
    <div class="input-group-btn">
        <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-check bigger-110"></i></button>
        <button class="btn btn-default btn-sm edit-datatable-field-cancel" type="button"><i class="fas fa-times bigger-110"></i></button>
    </div>
</div>
{{ Form::close() }}