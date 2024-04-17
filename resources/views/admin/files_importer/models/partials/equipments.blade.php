<div class="row row-5">
    <div class="col-sm-6">
        <div class="form-group">
            <label>@trans('Nº Cliente') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Todos os registos vão ser associados ao Nº de cliente indicado. Não é necessário preencher os dados do remetente.')"></i></label>
            {{ Form::text('customer_code', null, ['class' => 'form-control uppercase nospace']) }}
        </div>
    </div>
</div>
<div class="checkbox m-t-5 m-b-0">
    <label style="padding-left: 0">
        {{ Form::checkbox('available_customers') }}
        @trans('Disponível na área cliente')
    </label>
</div>