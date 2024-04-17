<div class="box no-border">
    <div class="box-body">
        <div class="col-sm-8 col-md-9 col-lg-12">
            <div class="row row-5">
                <div class="col-sm-4 col-md-3">
                    <label class="m-l-5">@trans('Volumes fora de norma')</label>
                    <div class="checkbox m-t-5 m-l-5">
                        <label style="padding-left: 0">
                            {{ Form::checkbox('allow_out_of_standard') }}
                            @trans('Permitir Vol. Fora Norma')
                        </label>
                        {!! tip(__('Ative esta opção se pretender cubicar a mercadoria para este fornecedor. Se a opção estiver inativa, não será feita cubicagem.')) !!}
                    </div>
                </div>
                <div class="col-sm-4 col-md-2">
                    <div class="form-group">
                        {{ Form::label('volumetric_max_weight', __('Peso superior a')) }}
                        <div class="input-group">
                            {{ Form::text('volumetric_max_weight', null, ['class' => 'form-control']) }}
                            <div class="input-group-addon">@trans('Kg')</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-2">
                    <div class="form-group">
                        {{ Form::label('volumetric_max_length', 'C+A+L superior a') }}
                        <div class="input-group">
                            {{ Form::text('volumetric_max_length', null, ['class' => 'form-control']) }}
                            <div class="input-group-addon">@trans('cm')</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-2">
                    <button class="btn btn-default m-t-19">@trans('Gravar')</button>
                </div>
            </div>
            <hr style="margin: 0 0 10px"/>
            <table id="datatable-cubing" class="table table-condensed table-striped table-dashed table-hover">
                <thead>
                    <tr>
                        <th class="w-1">@trans('Pos.')</th>
                        <th></th>
                        <th class="w-1">@trans('Código')</th>
                        <th>@trans('Serviço')</th>
                        <th class="w-170px">
                            @trans('Volume min para cálculo')
                            {!! tip(__('Indique o valor em cm3 a partir do qual o sistema deve cubicar. Por exemplo, para o valor 100, o sistema só cubicará a partir de 100cm3')) !!}
                        </th>
                        <th class="w-170px">
                            @trans('Coeficiente Vol. Custo')
                            {!! tip(__('Infique o coeficiente de volumetria fornecido pelo fornecedor. Ex: 167, 250, 300, ...')) !!}
                        </th>
                        <th class="w-170px">
                            @trans('Coeficiente Vol. Venda')
                            {!! tip(__('Infique o coeficiente de volumetria fornecido pelo fornecedor. Ex: 167, 250, 300, ...')) !!}
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>