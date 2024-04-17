<div class="row row-5">
    <div class="col-sm-6">
        <div class="form-group">
            <label>@trans('Nº Cliente') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Todas as ordens de recepção vão ser associados ao Nº de cliente indicado.')"></i></label>
            {{ Form::text('customer_code', null, ['class' => 'form-control uppercase nospace']) }}
        </div>
    </div>
</div>