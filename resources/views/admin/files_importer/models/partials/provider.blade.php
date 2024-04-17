<div class="form-group">
    <label>@trans('Associar fornecedor às agências') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Todos os clientes vão ser associados à agência escolhida.')"></i></label>
    {{ Form::select('agencies[]', $agencies, null, ['class' => 'form-control select2', 'multiple']) }}
</div>
{{--
<div class="form-group">
    <label>Tipo Cliente <i class="fas fa-info-circle" data-toggle="tooltip" title="Todos os clientes vão ser associados ao tipo escolhido."></i></label>
    {{ Form::select('type_id',  ['' => ''] + $customerTypes, null, ['class' => 'form-control select2']) }}
</div>--}}
