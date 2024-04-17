{{ Form::model($vehicle, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-9">
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group is-required">
                                    {{ Form::label('license_plate', __('Matrícula')) }}
                                    {{ Form::text('license_plate', null, ['class' => 'form-control', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group is-required">
                                    {{ Form::label('name', __('Designação em sistema'), 'required') }}
                                    {{ Form::text('name', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('code', __('Nº Frota')) }}
                                    {{ Form::text('code', null, ['class' => 'form-control', 'maxlength' => 6]) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('reference', __('Ref. Interna')) }}
                                    {{ Form::text('reference', null, ['class' => 'form-control', 'maxlength' => 50]) }}
                                </div>
                            </div>
                            @if($vehicle->type == 'trailer')
                            <div class="col-sm-3">
                                <div class="form-group is-required">
                                    {{ Form::label('trailer_type', __('Tipo Reboque')) }}
                                    {{ Form::select('trailer_type', ['' => ''] + trans('admin/fleet.trailers.types'), null, ['class' => 'form-control select2', 'required']) }}
                                    {{ Form::hidden('type', 'trailer') }}
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {{ Form::label('increase_roof', __('Teto Elevar')) }}
                                    {{ Form::select('increase_roof', ['' => __('Não'), '1' => __('Sim')], null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            @else
                            <div class="col-sm-3">
                                <div class="form-group is-required">
                                    {{ Form::label('type', __('Tipo Viatura')) }}
                                    {{ Form::select('type', ['' => ''] + trans('admin/fleet.vehicles.types'), null, ['class' => 'form-control select2', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {{ Form::label('category', __('Categoria')) }}
                                    {{ Form::select('category', ['' => ''] + trans('admin/fleet.vehicles.categories'), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('status', __('Estado')) }}
                                    {{ Form::select('status', ['' => ''] + trans('admin/fleet.vehicles.status'), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>

                            {{--<div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('class', 'Afetação') }}
                                    {{ Form::select('class', ['' => ''], null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>--}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('contract', __('Situação Contratual')) }}
                                    {{ Form::select('contract', ['' => __('Sem contrato'), 'loan' => __('Empréstimo'), 'leasing' => __('Leasing'), 'rental' => __('Aluguer')], null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group is-required">
                                    {{ Form::label('agency_id', __('Agência Responsável')) }}
                                    {{ Form::select('agency_id', ['' => ''] + $agencies, null, ['class' => 'form-control select2', 'required']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="p-r-30">
                            <div class="form-group is-required">
                                <a href="{{ route('admin.fleet.brands.index', ['tab' => 'brands']) }}" class="pull-right"><i class="fas fa-plus"></i> @trans('Nova marca')</a>
                                {{ Form::label('brand_id', __('Marca')) }}
                                {{ Form::select('brand_id', ['' => ''] + $brands, null, ['class' => 'form-control select2', 'required']) }}
                            </div>
                            <div class="form-group">
                                <a href="{{ route('admin.fleet.brands.index', ['tab' => 'models']) }}" class="btn-add-model pull-right"><i class="fas fa-plus"></i> @trans('Novo modelo')</a>
                                {{ Form::label('model_id', __('Modelo')) }}
                                <span class="brand-loading" style="display: none;"><i class="fas fa-spin fa-circle-notch"></i></span>
                                <span id="model-id">
                                    {{ Form::select('model_id', ['' => ''] + $models, null, ['class' => 'form-control select2']) }}
                                </span>
                            </div>
                            {{-- <div class="form-group">
                                {{ Form::label('version', 'Versão') }}
                                {{ Form::text('version', null, ['class' => 'form-control']) }}
                            </div> --}}
                            <div class="form-group">
                                {{ Form::label('provider_id', __('Subcontratado')) }}
                                {{ Form::select('provider_id', ['' => ''] + $subProviders, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('operator_id', __('Motorista Responsável')) }}
                            {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                        </div>
                        @if($vehicle->type != 'trailer')
                        <div class="form-group">
                            {{ Form::label('trailer_id', __('Reboque Associado')) }}
                            {{ Form::select('trailer_id', ['' => ''] + $trailers, null, ['class' => 'form-control select2']) }}
                        </div>
                        @endif
                        <div class="form-group">
                            {{ Form::label('assistants[]', __('Assistentes/Auxiliares'), ['class' => 'control-label']) }}
                            {!! Form::select('assistants[]', $operators, $vehicle->assistants, ['class' => 'form-control select2', 'multiple']) !!}
                        </div>
                    </div>
                </div>
                <div class="row row-5">

                    <div class="col-sm-8">
                        <div class="p-r-30">
                            @if($vehicle->type != 'trailer')
                                <h4 class="form-divider">@trans('Motorização e Características')</h4>
                                <div class="row row-5">
                                    <div class="col-sm-3">
                                        <div class="form-group is-required">
                                            {{ Form::label('fuel', __('Combustível')) }}
                                            {{ Form::select('fuel', ['' => ''] + trans('admin/fleet.fuel'), null, ['class' => 'form-control select2', 'required']) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group is-required">
                                            {{ Form::label('average_consumption', __('Consumo Médio')) }} {!! tip(__('Consumo médio indicado pelo fabricante. Este consumo valor será usado para comparação de médias de condução.')) !!}
                                            <div class="input-group">
                                                {{ Form::text('average_consumption', null, ['class' => 'form-control', 'required']) }}
                                                <span class="input-group-addon">l/100km</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('co2', __('Emissão Co2')) }}
                                            <div class="input-group">
                                                {{ Form::text('co2', null, ['class' => 'form-control decimal']) }}
                                                <span class="input-group-addon">g/km</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('transmission', __('Transmissão')) }}
                                            {{ Form::select('transmission', ['manual' => __('Manual'), 'auto' => __('Automática'), 'semiauto' => __('Semi-automática')], null, ['class' => 'form-control select2']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-5">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('power', __('Potencia')) }}
                                            <div class="input-group">
                                                {{ Form::text('power', null, ['class' => 'form-control number']) }}
                                                <span class="input-group-addon">cv</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('engine_capacity', __('Cilindrada')) }}
                                            <div class="input-group">
                                                {{ Form::text('engine_capacity', null, ['class' => 'form-control number']) }}
                                                <span class="input-group-addon">cm<sup>3</sup></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {{ Form::label('chassis', __('Nº Chassis')) }}
                                            {{ Form::text('chassis', null, ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-5">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('color', __('Cor')) }}
                                            {{ Form::text('color', null, ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{ Form::label('seats', __('Lugares')) }}
                                            {{ Form::select('seats', ['' => '', '2' => '2', '3' => '3','4' => '4','5' => '5','6' => '6','7' => '7'], null, ['class' => 'form-control select2']) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('class', __('Via Verde')) }}
                                            {{ Form::select('class', ['' => '', '1' => 'Classe 1', '2' => 'Classe 2', '3' => 'Classe 3', '4' => 'Classe 4'], null, ['class' => 'form-control select2']) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('km_initial', __('Km Compra')) }}
                                            <div class="input-group">
                                                {{ Form::text('km_initial', null, ['class' => 'form-control']) }}
                                                <span class="input-group-addon">km</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row row-5">
                                <div class="col-xs-12">
                                <h4 class="form-divider">@trans('Dimensões e Peso')</h4>
                                <div class="row row-5">
                                    <div class="col-sm-7">
                                        <div class="row row-5">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    {{ Form::label('width', __('Comprimento')) }}
                                                    <div class="input-group">
                                                        {{ Form::text('width', null, ['class' => 'form-control decimal']) }}
                                                        <div class="input-group-addon">m</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    {{ Form::label('length', __('Largura')) }}
                                                    <div class="input-group">
                                                        {{ Form::text('length', null, ['class' => 'form-control decimal']) }}
                                                        <div class="input-group-addon">m</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    {{ Form::label('height', __('Altura')) }}
                                                    <div class="input-group">
                                                        {{ Form::text('height', null, ['class' => 'form-control decimal']) }}
                                                        <div class="input-group-addon">m</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{ Form::label('axles', __('Nº Eixos')) }}
                                            {{ Form::select('axles', ['2'=>'2','3'=>'3','4'=>'4','5'=>'5'], null, ['class' => 'form-control select2']) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('axles_distance', __('Distância Eixos')) }}
                                            <div class="input-group">
                                                {{ Form::text('axles_distance', null, ['class' => 'form-control decimal']) }}
                                                <span class="input-group-addon">cm</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {{ Form::label('box_max_width', __('Comp. Max Caixa')) }}
                                            <div class="input-group">
                                                {{ Form::text('box_max_width', null, ['class' => 'form-control decimal']) }}
                                                <span class="input-group-addon">cm</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="row row-5">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    {{ Form::label('gross_weight', __('Peso Bruto')) }}
                                                    <div class="input-group">
                                                        {{ Form::text('gross_weight', $vehicle->gross_weight > 0 ? number($vehicle->gross_weight, 0) : '', ['class' => 'form-control number', 'maxlength' => 5]) }}
                                                        <span class="input-group-addon">kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    {{ Form::label('usefull_weight', __('Peso Útil')) }}
                                                    <div class="input-group">
                                                        {{ Form::text('usefull_weight',null, ['class' => 'form-control number']) }}
                                                        <span class="input-group-addon">kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($vehicle->type != 'trailer')
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{ Form::label('deposit_capacity', __('Depósito')) }}
                                            <div class="input-group">
                                                {{ Form::text('deposit_capacity', null, ['class' => 'form-control number']) }}
                                                <span class="input-group-addon">L</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <h4 class="form-divider">@trans('Datas e Registo')</h4>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group is-required">
                                    {{ Form::label('registration_date', __('Data Matrícula')) }}
                                    <div class="input-group">
                                        {{ Form::text('registration_date', $vehicle->exists && $vehicle->registration_date ? $vehicle->registration_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group is-required">
                                    {{ Form::label('ipo_date', __('Próxima IPO')) }}
                                    <div class="input-group">
                                        {{ Form::text('ipo_date', $vehicle->exists && $vehicle->ipo_date ? $vehicle->ipo_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            @if($vehicle->type != 'trailer')
                            <div class="col-sm-6">
                                <div class="form-group is-required">
                                    {{ Form::label('iuc_date', __('Próximo IUC')) }}
                                    <div class="input-group">
                                        {{ Form::text('iuc_date', $vehicle->exists && $vehicle->iuc_date ? $vehicle->iuc_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-6">
                                <div class="form-group is-required">
                                    {{ Form::label('insurance_date', __('Data Seguro')) }}
                                    <div class="input-group">
                                        {{ Form::text('insurance_date', $vehicle->exists && $vehicle->insurance_date ? $vehicle->insurance_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('tachograph_date', __('Aferição Tacógrafo')) }} {!! tip(__('Data da próxima Aferição de Tacógrafo')) !!}
                                    <div class="input-group">
                                        {{ Form::text('tachograph_date', $vehicle->exists && $vehicle->tachograph_date ? $vehicle->tachograph_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('buy_date', __('Data Compra')) }}
                                    <div class="input-group">
                                        {{ Form::text('buy_date', null, ['class' => 'form-control datepicker']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('manufacturing_date', 'Data Fabrico') }}
                                    <div class="input-group">
                                        {{ Form::text('manufacturing_date', $vehicle->exists && $vehicle->manufacturing_date ? $vehicle->manufacturing_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>--}}
                        </div>
                        @if($vehicle->type != 'trailer')
                        <h4 class="form-divider">@trans('Pneumáticos')</h4>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('tires_front', __('Medida Frente')) }}
                                    {{ Form::text('tires_front', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('tires_rear', __('Medida Trás')) }}
                                    {{ Form::text('tires_rear', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('tires_others', __('Outras medidas')) }}
                                    {{ Form::text('tires_others', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="p-r-30">
                            <h4 class="form-divider">@trans('Titular do Certificado')</h4>
                            <div class="form-group">
                                {{ Form::label('titular_name', __('Nome do Titular (do certificado)')) }}
                                {{ Form::text('titular_name', null, ['class' => 'form-control']) }}
                            </div>
                            <div class="row row-5">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{ Form::label('titular_address', __('Morada')) }}
                                        {{ Form::text('titular_address', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {{ Form::label('titular_zip_code', __('Código postal')) }}
                                        {{ Form::text('titular_zip_code', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::label('titular_zip_code', __('Localidade')) }}
                                        {{ Form::text('titular_zip_code', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {{ Form::label('titular_country', __('País')) }}
                                        {{ Form::select('titular_country', ['' => ''] + trans('country'), $vehicle->titular_country ? null : 'pt', ['class' => 'form-control select2']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div>
                            <h4 class="form-divider">@trans('Proprietário')</h4>
                            <div class="form-group">
                                {{ Form::label('proprietary_name', __('Nome Completo')) }}
                                {{ Form::text('proprietary_name', null, ['class' => 'form-control']) }}
                            </div>
                            <div class="row row-5">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{ Form::label('proprietary_address', __('Morada')) }}
                                        {{ Form::text('proprietary_address', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {{ Form::label('proprietary_zip_code', __('Código postal')) }}
                                        {{ Form::text('proprietary_zip_code', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::label('proprietary_zip_code', __('Localidade')) }}
                                        {{ Form::text('proprietary_zip_code', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {{ Form::label('proprietary_country', __('País')) }}
                                        {{ Form::select('proprietary_country', ['' => ''] + trans('country'), $vehicle->proprietary_country ? null : 'pt', ['class' => 'form-control select2']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="p-l-30">
                    {{ Form::label('image', __('Fotografia Principal'), array('class' => 'form-label')) }}<br/>
                    <div class="fileinput {{ $vehicle->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                        <div class="fileinput-new thumbnail" style="width: 100%; max-height: 200px;">
                            <img src="{{ asset('assets/img/default/default.png') }}">
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;">
                            @if($vehicle->filepath)
                                <img src="{{ asset($vehicle->getThumb()) }}">
                            @endif
                        </div>
                        <div>
                            <span class="btn btn-default btn-block btn-sm btn-file">
                                <span class="fileinput-new">@trans('Procurar...')</span>
                                <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> @trans('Alterar')</span>
                                <input type="file" name="image">
                            </span>
                            <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                                <i class="fas fa-close"></i> @trans('Remover')
                            </a>
                        </div>
                    </div>
                    <div class="form-group m-0">
                        {{ Form::label('obs', __('Observações')) }}
                        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 14]) }}
                    </div>
                    <h4 class="form-divider no-border" style="margin-top: 12px;">@trans('Seguro')</h4>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('insurer', __('Seguradora')) }}
                            {{ Form::select('insurer', ['' => ''] + $insurerProviders, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('insurer_number', __('Nº Apólice')) }}
                            {{ Form::text('insurer_number', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <h4 class="form-divider">@trans('Cartões Combustível')</h4>
                    <div class="col-sm-8">
                        <div class="form-group">
                            {{ Form::label('fuel_1', __('Posto 1')) }}
                            {{ Form::select('fuel_1', ['' => ''] + $fuelProviders, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('pin_1', __('PIN 1')) }}
                            {{ Form::text('pin_1', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            {{ Form::label('fuel_2', __('Posto 2')) }}
                            {{ Form::select('fuel_2', ['' => ''] + $fuelProviders, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('pin_2', __('PIN 2')) }}
                            {{ Form::text('pin_2', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            {{ Form::label('fuel_3', __('Posto 3')) }}
                            {{ Form::select('fuel_3', ['' => ''] + $fuelProviders, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('pin_3', __('PIN 3')) }}
                            {{ Form::text('pin_3', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
    </div>
</div>
{{ Form::hidden('delete_photo') }}
{{ Form::close() }}
