<a href="#" class="edit-datatable-field">
    {{ $row->name }}
</a>
<div class="clearfix"></div>
{{ Form::open(['route' => ['admin.customers-types.update', $row->id], 'method' => 'PUT', 'data-remote' => 'true', 'class' => "form-edit-datatable-field form-inline hide"]) }}
<div class="input-group">
    {{ Form::text('name', $row->name, ['class' => 'form-control input-sm target-datatable-field', 'style' => 'width: 429px']) }}
    <div class="input-group-btn">
        <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-check bigger-110"></i></button>
        <button class="btn btn-default btn-sm edit-datatable-field-cancel" type="button"><i class="fas fa-times bigger-110"></i></button>
    </div>
</div>
{{ Form::close() }}