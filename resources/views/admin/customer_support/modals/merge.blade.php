{{ Form::open(['route' => ['admin.customer-support.merge.store', $ticketId]]) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Juntar pedidos duplicados')</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('assign_ticket_id', __('Juntar este pedido ao pedido de suporte:')) }}
        {{ Form::select('assign_ticket_id', [], null, ['class' => 'form-control select2', 'data-placeholder' => '']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-compress"></i> @trans('Juntar')</button>
</div>
{{ Form::close() }}

<script>
    $(".modal select[name=assign_ticket_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customer-support.search.ticket') }}")
    });
</script>