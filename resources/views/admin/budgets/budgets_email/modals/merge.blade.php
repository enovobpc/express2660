{{ Form::open(['route' => ['admin.budgets.merge.store', $budgetId]]) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Juntar orçamentos duplicados</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('assign_budget_id', 'Juntar este pedido ao orçamento:') }}
        {{ Form::select('assign_budget_id', [], null, ['class' => 'form-control select2']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-compress"></i> Juntar</button>
</div>
{{ Form::close() }}

<script>
    $("select[name=assign_budget_id]").select2({
        ajax: {
            url: "{{ route('admin.budgets.search.budget') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
</script>