<div class="modal" id="modal-edit-history">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.shipments.history.selected.update'], 'files' => true, 'class' => 'form-update-history']) }}
            @include('admin.shipments.history.partials.form')
            {{ Form::close() }}
        </div>
    </div>
</div>