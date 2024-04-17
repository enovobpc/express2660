@if(!hasModule('human_resources'))
    @include('admin.users.users.partials.denied_message')
@else
{{ Form::model($user, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        {{--<div class="row row-5">
            <div class="col-sm-7">
                <div class="row row-5">
                    <div class="col-sm-9">
                        <div class="form-group">
                            {{ Form::label('professional_role', 'Cargo Profissional') }}
                            {{ Form::text('professional_role', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('date', 'Data Admissão')}}
                            <div class="input-group">
                                {{ Form::text('date', null, array('class' =>'form-control datepicker')) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('professional_email', 'E-mail Empresa') }}
                            {{ Form::text('professional_email', null, ['class' => 'form-control nospace lowercase email']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('professional_mobile', 'Telemóvel Empresa') }}
                            {{ Form::text('professional_mobile', null, ['class' => 'form-control phone']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('professional_phone', 'Telefone Empresa') }}
                            {{ Form::text('professional_phone', null, ['class' => 'form-control phone']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <a href="{{ route('admin.users.workgroups.index') }}" data-toggle="modal" data-target="#modal-remote" class="pull-right">
                                Gerir Grupos
                            </a>
                            {{ Form::label('workgroup[]', 'Grupos de Trabalho') }} {!! tip("Organize os seus colaboradores pelo grupo de trabalho deles. Pode editar ou adicionar grupos de trabalho no menu 'Entidades > Colaboradores > Ferramentas'.") !!}
                            {{ Form::select('workgroup[]', $workgroups, @$selectedWorkgroups, ['class' => 'form-control select2', 'multiple']) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('professional_chief_id', 'Chefe/Responsável') }}
                            {{ Form::select('professional_chief_id', ['' => ''] + $operatorsList, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-2">

                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="p-l-30">
                    <div class="form-group">
                        {{ Form::label('professional_obs', 'Anotações') }}
                        {{ Form::textarea('professional_obs', null, ['class' => 'form-control', 'rows' => 9]) }}
                    </div>
                </div>
            </div>
        </div>--}}
        <div class="row row-5">
            <div class="col-sm-4">
                <h4 class="form-divider no-border" style="margin-top: 0">@trans('Informação Salarial')</h4>
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('salary_price', __('Salário Base')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('salary_price', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('salary_food_allowance', __('Subsidio Alim.')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('salary_food_allowance', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('salary_expenses', __('Despesas')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('salary_expenses', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('ss_allowance', __('Valor Seg. Social')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('ss_allowance', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('christmas_allowance', __('Valor Sub. Natal')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('christmas_allowance', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('holiday_allowance', __('Valor Sub. Férias')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('holiday_allowance', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('salary_value_hour', __('Valor/Hora')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('salary_value_hour', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('salary_working_time_exemption', 'Isenção Horário') }}
                            <div class="input-group">
                                {{ Form::text('salary_working_time_exemption', null, ['class' => 'form-control decimal']) }}
                                <span class="input-group-addon">
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </span>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('holidays_days', __('Dias Férias/ano')) }}
                            {{ Form::text('holidays_days', null, ['class' => 'form-control number', 'maxlength' => 2]) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('comission_percent', __('Comissão')) }}
                            @if(hasModule('prospection'))
                                <div class="input-group input-group-money">
                                    {{ Form::text('comission_percent', null, array('class' =>'form-control decimal', 'maxlength' => 5)) }}
                                    <div class="input-group-addon">%</div>
                                </div>
                            @else
                                <div class="input-group input-group-money" data-toggle="tooltip" title="Não possui o módulo de gestão comercial ativo.">
                                    {{ Form::text('comission_percent', null, array('class' =>'form-control', 'disabled')) }}
                                    <div class="input-group-addon">%</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <h4 class="form-divider no-border" style="margin-top: 0">@trans('Ajudas de Custo')</h4>
                <div class="row row-5">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('allowance_value_nacional', __('Nacional')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('allowance_value_nacional', null, array('class' =>'form-control decimal')) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('allowance_value_spain', __('Ibérico')) }}
                            <div class="input-group input-group-money"> 
                                {{ Form::text('allowance_value_spain', null, array('class' =>'form-control decimal')) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('allowance_value_internacional', __('Internacional')) }}
                            <div class="input-group input-group-money">
                                {{ Form::text('allowance_value_internacional', null, array('class' =>'form-control decimal')) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="form-divider no-border" style="margin-top: 0">@trans('Subcontratos')</h4>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('provider_id', __('Associar Fornec.')) }}
                            {!! tip(__('Se este motorista é um subcontratado e não pertence aos funcionários da sua empresa, escolha qual o fornecedor a que ele pertence.')) !!}
                            {{ Form::select('provider_id', ['' => ''] + $providers, null, array('class' =>'form-control select2')) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="p-l-30">
                    <h4 class="form-divider no-border" style="margin-top: 0">
                        @if(!$user->contracts->isEmpty())
                            <div class="pull-right" style="margin-top: -5px">
                                <a href="{{ route('admin.users.contracts.create', $user->id) }}"
                                   class="btn btn-xs btn-success m-t-5"
                                   data-toggle="modal"
                                   data-target="#modal-remote">
                                    <i class="fas fa-plus"></i> @trans('Adicionar contrato')
                                </a>
                            </div>
                        @endif
                            @trans('Contratos de Trabalho')
                    </h4>
                    <div style="padding: 0;
    border: 1px solid #ddd;
    border-radius: 3px;
    min-height: 230px;">
                    <table class="table table-condensed" style="border-radius: 4px">
                        <tr>
                            <td class="bg-gray-light bold">@trans('Tipo Contrato')</td>
                            <td class="bg-gray-light bold w-85px">@trans('Início')</td>
                            <td class="bg-gray-light bold w-85px">@trans('Fim')</td>
                            <td class="bg-gray-light bold w-60px">@trans('Aviso')</td>
                            <td class="bg-gray-light bold w-45px"></td>
                        </tr>
                        @forelse($user->contracts as $contract)
                            <tr>
                                <td style="border-bottom: 1px solid #ddd">
                                    {{ trans('admin/users.contract-types.' . $contract->contract_type) }}
                                    @if($contract->obs)
                                        {!! tip($contract->obs) !!}
                                    @endif
                                </td>
                                <td style="border-bottom: 1px solid #ddd">{{ $contract->start_date->format('Y-m-d') }}</td>
                                <td style="border-bottom: 1px solid #ddd">{{ $contract->end_date->format('Y-m-d') }}</td>
                                <td style="border-bottom: 1px solid #ddd" class="text-center">
                                    @if($contract->notification_date->gt(Date::today()))
                                        <span data-toggle="tooltip" title="Vai ser notificado do fim do contrato {{ $contract->notification_days }} dias antes. Notificação agendada para {{ $contract->notification_date->format('Y-m-d') }}">
                                        {{ $contract->notification_days ? $contract->notification_days : 0 }} @trans('dias')
                                        </span>
                                    @else
                                        <span class="text-red"data-toggle="tooltip" title="Vai ser notificado do fim do contrato {{ $contract->notification_days }} dias antes. Notificação agendada para {{ $contract->notification_date->format('Y-m-d') }}">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $contract->notification_days ? $contract->notification_days : 0 }} d
                                        </span>
                                    @endif
                                </td>
                                <td style="border-bottom: 1px solid #ddd">
                                    <a href="{{ route('admin.users.contracts.edit', [$contract->user_id, $contract->id]) }}"
                                       class="text-green m-r-3"
                                       data-toggle="modal"
                                       data-target="#modal-remote">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.users.contracts.destroy', [$contract->user_id, $contract->id]) }}"
                                       data-method="delete"
                                       data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-muted text-center m-t-40">
                                        <i class="fas fa-file-signature fs-30 m-b-0"></i><br/>
                                        <h4 class="m-b-10">@trans('Não inseriu nenhum contrato.')</h4>
                                        <a href="{{ route('admin.users.contracts.create', $user->id) }}"
                                           class="btn btn-xs btn-default"
                                           data-toggle="modal"
                                           data-target="#modal-remote">
                                            <i class="fas fa-plus"></i> @trans('Inserir Contrato')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <button type="submit" class="btn btn-primary pull-left">@trans('Gravar')</button>
        <div class="clearfix"></div>
    </div>
</div>
{{ Form::hidden('active', $user->active) }}
{{ Form::close() }}
@endif