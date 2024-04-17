{{ Form::model($vehicle, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('license_plate', 'Matrícula') }}
                {{ Form::text('license_plate', null, ['class' => 'form-control uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control uppercase', 'required', 'placeholder' => 'Ex.: Citroën Berlingo 19-SB-32']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('type', 'Tipo') }}
                {{ Form::select('type', ['' => ''] + trans('admin/fleet.vehicles.types'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('gross_weight', 'Peso Bruto') }}
                <div class="input-group">
                    {{ Form::text('gross_weight', null, ['class' => 'form-control decimal']) }}
                    <span class="input-group-addon">kg</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('usefull_weight', 'Carga útil') }}
                <div class="input-group">
                    {{ Form::text('usefull_weight', null, ['class' => 'form-control decimal']) }}
                    <span class="input-group-addon">kg</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('operator_id', 'Motorista Responsável') }}
                {{ Form::select('operator_id', ['' => 'Nenhum'] + $operators, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <label>Agências onde opera esta viatura</label>
            <div class="clearfix"></div>
            @foreach($agencies as $agency)
                <div class="col-sm-6">
                    <div class="checkbox m-t-5 m-b-0">
                        <label style="padding-left: 0">
                            {{ Form::checkbox('agencies[]', $agency->id) }}
                            <span class="label" style="background: {{ $agency->color }}">{{ $agency->code }}</span> {{ $agency->print_name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left text-left w-30">
        <div class="checkbox m-b-0 m-t-4">
            <label style="padding-left: 10px !important;">
                {{ Form::checkbox('is_default') }}
                Viatura por defeito
            </label>
            {!! tip('Caso não seja indicada a viatura nos envios, a guia de transporte assumirá esta viatura como por defeito.') !!}
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button class="btn btn-primary btn-submit">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('[data-toggle="tooltip"]').tooltip();
</script>
