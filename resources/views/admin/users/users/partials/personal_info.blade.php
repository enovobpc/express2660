@if(!hasModule('human_resources'))
    @include('admin.users.users.partials.denied_message')
@else
{{ Form::model($user, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-4 col-lg-9">
                <div class="row row-5">
                    <div class="col-sm-8">
                        <div class="row row-5">
                            <div class="col-sm-2">
                                <div class="form-group is-required">
                                    {{ Form::label('code', __('Código')) }} {!! tip(__('Número ou código interno do colaborador')) !!}
                                    {{ Form::text('code', null, array('class' =>'form-control nospace uppercase', 'maxlength' => 5, 'required')) }}
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-group is-required">
                                    {{ Form::label('fullname', __('Nome Completo')) }}
                                    {{ Form::text('fullname', null, ['class' => 'form-control', 'required']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            {{ Form::label('role_id', __('Perfíl da Conta')) }}
                            @if(Auth::user()->perm('admin_roles'))
                                <a href="{{ route('admin.roles.index') }}" class="pull-right m-l-10" target="_blank">@trans('Gerir Perfis')</a>
                            @endif
                            @if(Auth::user()->id == $user->id)
                                {{ Form::select('role_id', [@$user->roles->first()->id => @$user->roles->first()->display_name], @$user->roles->first()->id, array('class' =>'form-control select2 roles-selection', 'required', 'disabled')) }}
                            @else
                                {{ Form::select('role_id', ['' => ''] + $roles, @$user->roles->first()->id, array('class' =>'form-control select2 roles-selection', 'required')) }}
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-1 check">
                        <div class="form-group" style="padding-left: 5px">
                            {{ Form::label('active', __('Ativo')) }}
                            <div class="sp-3"></div>
                            {{ Form::checkbox('active', 1, $user->exists ? null : 1, ['class' => 'ios', Auth::user()->id == $user->id ? 'disabled' : ''] ) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-8">
                        <div class="row row-5">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {{ Form::label('code_abbrv', __('Cod. Abrv')) }} {!! tip(__('A abreviatura permite identificar de forma mais clara o colaborador nas listagens do sistema.')) !!}
                                    {{ Form::text('code_abbrv', null, ['class' => 'form-control nospace uppercase', 'maxlength' => 6]) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('gender', __('Género')) }}
                                    {{ Form::select('gender', ['' => ''] + trans('admin/users.gender'), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('birthdate', __('Data Nascimento')) }}
                                    <div class="input-group">
                                        {{ Form::text('birthdate', null, ['class' => 'form-control datepicker']) }}
                                        <div class="input-group-addon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('civil_status', __('Estado Civíl')) }}
                                    {{ Form::select('civil_status', ['' => ''] + trans('admin/users.civil-status'), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('nacionality', __('Nacionalidade')) }}
                            {{ Form::text('nacionality', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                <h4 class="form-divider"><i class="fas fa-user-tie"></i> @trans('Informação Profissional')</h4>
                <div class="row row-5">
                    <div class="col-sm-7">
                        <div class="row row-5">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('admission_date', __('Data Admissão'))}}
                                    <div class="input-group">
                                        {{ Form::text('admission_date', null, array('class' =>'form-control datepicker')) }}
                                        <div class="input-group-addon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('resignation_date', __('Data Demissão'))}}
                                    <div class="input-group">
                                        {{ Form::text('resignation_date', null, array('class' =>'form-control datepicker')) }}
                                        <div class="input-group-addon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group is-required">
                                    {{ Form::label('agency_id', __('Delegação Principal'))}}
                                    {{ Form::select('agency_id', [''=>''] + $agenciesList, null, array('class' =>'form-control select2', 'required')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('professional_role', __('Cargo ou Função'))}}
                            {{ Form::text('professional_role', null, array('class' =>'form-control')) }}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {{ Form::label('professional_chief_id', __('Chefe/Responsável')) }}
                            {{ Form::select('professional_chief_id', ['' => ''] + $operatorsList, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <a href="{{ route('admin.users.workgroups.index') }}" data-toggle="modal" data-target="#modal-remote" class="pull-right">
                                @trans('Gerir Grupos')
                            </a>
                            {{ Form::label('workgroup[]', __('Grupos de Trabalho')) }} {!! tip("Organize os seus colaboradores pelo grupo de trabalho deles. Pode editar ou adicionar grupos de trabalho no menu 'Entidades > Colaboradores > Ferramentas'.") !!}
                            {{ Form::select('workgroup[]', $workgroups, @$selectedWorkgroups, ['class' => 'form-control select2', 'multiple']) }}
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('professional_email', __('E-mail Empresa')) }}
                            {{ Form::text('professional_email', null, ['class' => 'form-control nospace lowercase email']) }}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {{ Form::label('professional_mobile', __('Telemóvel Empresa')) }}
                            {{ Form::text('professional_mobile', null, ['class' => 'form-control phone']) }}
                        </div>
                    </div>
                </div>

                <div class="row row-5">
                    <div class="col-sm-8" style="padding-right: 15px">
                        <h4 class="form-divider"><i class="fas fa-phone"></i> @trans('Contactos Pessoais/Residência')</h4>
                        <div class="form-group">
                            {{ Form::label('address', __('Morada Residência')) }}
                            {{ Form::text('address', null, ['class' => 'form-control']) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('zip_code', __('Código Postal')) }}
                                    {{ Form::text('zip_code', null, ['class' => 'form-control trim']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('city', __('Localidade')) }}
                                    {{ Form::text('city', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('country', __('País')) }}
                                    {{ Form::select('country', ['' => ''] + trans('country'), $user->exists ? null : 'pt', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('personal_email', __('E-mail pessoal')) }}
                                    {{ Form::text('personal_email', null, ['class' => 'form-control nospace lowercase email']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('personal_mobile', __('Telemóvel pessoal')) }}
                                    {{ Form::text('personal_mobile', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('personal_phone', __('Telefone')) }}
                                    {{ Form::text('personal_phone', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><i class="fas fa-heartbeat"></i> @trans('Pessoa Contacto Emergência')</label>
                                    {{ Form::text('emergency_name', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                           {{-- <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('emergency_kinship', 'Parentesco') }}
                                    {{ Form::text('emergency_kinship', null, ['class' => 'form-control']) }}
                                </div>
                            </div>--}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-heartbeat"></i> @trans('Telemóvel SOS')</label>
                                    {{ Form::text('emergency_mobile', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-heartbeat"></i> @trans('Telefone SOS')</label>
                                    {{ Form::text('emergency_phone', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4" style="padding-left: 15px">
                        <h4 class="form-divider"><i class="fas fa-landmark"></i> @trans('Dados Fiscais')</h4>
                       {{-- <div class="form-group">
                            {{ Form::label('fiscal_address', 'Morada') }}
                            {{ Form::text('fiscal_address', null, ['class' => 'form-control']) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('fiscal_zip_code', 'C. Postal') }}
                                    {{ Form::text('fiscal_zip_code', null, ['class' => 'form-control trim']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('fiscal_city', 'Localidade') }}
                                    {{ Form::text('fiscal_city', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('fiscal_country', 'País') }}
                                    {{ Form::select('fiscal_country', ['' => ''] + trans('country'), $user->exists ? null : 'pt', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>--}}
                        <a href="?tab=cards" class="pull-right" style="margin-bottom: -15px;">
                            <small><i class="fas fa-cog"></i> @trans('Gerir Cartões')</small>
                        </a>
                        <div class="row row-5">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ Form::label('id_card', __('Cartão Cidadão')) }}
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        {{ Form::text('id_card', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ Form::label('vat', __('Contribuinte')) }}
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        {{ Form::text('vat', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ Form::label('ss_card', __('Nº Seg. Social')) }}
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        {{ Form::text('ss_card', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('fiscal_dependents', __('Dependentes')) }}
                                    {{ Form::select('fiscal_dependents', range(0,10), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('fiscal_titularity', __('Rendimentos')) }}
                                    {{ Form::select('fiscal_titularity', ['' => '', '1' => '1 Titular', '2' => '2 Titulares'], null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('fiscal_deficiency', __('Deficiencia')) }}
                                    {{ Form::select('fiscal_deficiency', ['' => 'Não'] + trans('admin/users.deficiencies'), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg-3">
                <div class="p-l-30 ">
                    {{ Form::label('image', __('Fotografia'), array('class' => 'form-label')) }}<br/>
                    <div class="fileinput {{ $user->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                        <div class="fileinput-new thumbnail">
                            <img src="{{ asset('assets/img/default/avatar.png') }}" class="img-responsive">
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail">
                            @if($user->filepath)
                                <img src="{{ asset($user->getCroppa(200, 200)) }}" onerror="this.src = '{{ img_broken(true) }}'" class="img-responsive">
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
                </div>
                <div class="p-l-30">
                    <h4 class="form-divider">@trans('Notas e Observações')</h4>
                    <div class="row row-5">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::textarea('about', null, ['class' => 'form-control', 'rows' => 16]) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-5">
            <div class="col-sm-8">
                <h4 class="form-divider"><i class="fas fa-graduation-cap"></i> @trans('Formação Académica')</h4>
                <div class="row row-5">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('academic_degree', __('Grau Académico')) }}
                            {{ Form::select('academic_degree', ['' => ''] + trans('admin/users.academic-degrees'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('teaching_institution', __('Instituição de Ensino')) }}
                            {{ Form::text('teaching_institution', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('course', __('Curso/Formação')) }}
                            {{ Form::text('course', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('course_avaliation', __('Avaliação')) }}
                            {{ Form::text('course_avaliation', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                <h4 class="form-divider">@trans('Informação Bancária')</h4>
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('bank_name', __('Nome Banco')) }}
                            {{ Form::text('bank_name', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('bank_iban', __('IBAN')) }}
                            {{ Form::text('bank_iban', null, ['class' => 'form-control iban']) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('bank_swift', __('BIC/SWIFT')) }}
                            {{ Form::text('bank_swift', null, ['class' => 'form-control uppercase']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="p-l-30">
                    <h4 class="form-divider">@trans('Redes Sociais')</h4>
                    <div class="form-group">
                        {{ Form::label('facebook', 'Facebook') }}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fab fa-facebook-square"></i>
                            </div>
                            {{ Form::url('facebook', null, ['class' => 'form-control url nospace lowercase']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('linkedin', 'Linkedin') }}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fab fa-linkedin"></i>
                            </div>
                            {{ Form::url('linkedin', null, ['class' => 'form-control url nospace lowercase']) }}
                        </div>
                    </div>
                    <div class="form-group m-0">
                        {{ Form::label('twitter', 'Twitter') }}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fab fa-twitter"></i>
                            </div>
                            {{ Form::url('twitter', null, ['class' => 'form-control url nospace lowercase']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <button type="submit" class="btn btn-primary pull-left">@trans('Gravar')</button>
        <div class="clearfix"></div>
    </div>
</div>
{{ Form::hidden('delete_photo') }}
{{ Form::close() }}
@endif