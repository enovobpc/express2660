@section('title')
Recrutamento
@stop

@section('content-header')
Recrutamento 
<small>Consultar</small>
@stop

@section('breadcrumb')
<li class="active">Registos do Sistema</li>
<li>
    <a href="{{ route('admin.website.recruitments.index') }}">
        Recrutamento
    </a>
</li>
<li class="active">
    Consultar
</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="mailbox-read-info p-t-0">
                    <h3>
                        {{ $recruitment->name }}
                        @if($recruitment->filepath)
                        <a href="{{ asset($recruitment->filepath) }}" class="btn btn-primary pull-right" target="_blank">
                            <i class="fas fa-file"></i> Download Currículo
                        </a>
                        @endif
                    </h3>

                    <h5>
                        Email: 
                        <a href="mailto:{{ $recruitment->email }}" class="text-muted">{{ $recruitment->email }}</a>
                </div>
                <div class="row">
                    <div class="col-sm-7">
                        <h4>Dados Pessoais</h4>
                        <dl class="dl-horizontal">
                            <dt>Nome</dt>
                            <dd>{{ $recruitment->name }}</dd>
                            <dt>Data Nascimento</dt>
                            <dd>{{ $recruitment->birthdate }} ({{ age($recruitment->birthdate) }} anos)</dd>
                            <dt>Sexo</dt>
                            <dd>{{ ($recruitment->gender =='m') ? 'Masculino' : 'Feminino' }}</dd>
                        </dl>
                        <h4>Dados de Contacto</h4>
                        <dl class="dl-horizontal">
                            <dt>Morada</dt>
                            <dd>
                                {{ $recruitment->address }}<br/>
                                {{ $recruitment->zip_code }} {{ $recruitment->city }}
                            </dd>
                            <dt>E-mail</dt>
                            <dd>{{ $recruitment->email }}</dd>
                            <dt>Telefone</dt>
                            <dd>{{ $recruitment->phone }}</dd>
                            <dt>Telemóvel</dt>
                            <dd>{{ $recruitment->mobile }}</dd>
                        </dl>
                        <h4>Formação Académica</h4>
                        <dl class="dl-horizontal">
                            <dt>Habilitações</dt>
                            <dd>{{ trans('admin/recruitment.qualifications.'. $recruitment->qualifications) }}</dd>
                            <dt>Área de Formação</dt>
                            <dd>{{ $recruitment->formation_area }}</dd>
                        </dl>
                    </div>
                    <div class="col-sm-5">
                        <h4>Candidatura</h4>
                        <dl class="dl-horizontal dl-sm">
                            <dt>Área</dt>
                            <dd>{{ trans('admin/recruitment.areas.'.$recruitment->area) }}</dd>
                            <dt>Função</dt>
                            <dd>{{ $recruitment->role }}</dd>
                            <dt>Disp.</dt>
                            <dd>{{ trans('admin/recruitment.availability.'.$recruitment->availability) }}</dd>
                            <dt>C. Condução</dt>
                            <dd>{{ $recruitment->driving_licence ? 'Sim' : 'Não' }}</dd>
                        </dl>
                        <h4>Experiência</h4>
                        <dl class="dl-horizontal">
                            <dt>Possui Experiência?</dt>
                            <dd>{{ $recruitment->has_experience ? 'Sim' : 'Não' }}</dd>
                            <dt>Situação Profissional</dt>
                            <dd>{{ $recruitment->professional_situation ? 'Empregado' : 'Desempregado' }}</dd>
                            <dt>Empresa Anterior</dt>
                            <dd>{{ $recruitment->company }}</dd>
                            <dt>Cargo Anterior</dt>
                            <dd>{{ $recruitment->company_role }}</dd>
                            <dt>Tempo na empresa</dt>
                            <dd>{{ $recruitment->company_time }}</dd>
                        </dl>
                    </div>
                    <div class="col-sm-12">
                        <h4>Outras informações</h4>
                        <dl class="dl-horizontal">
                            <dt>Data de criação</dt>
                            <dd>{{ $recruitment->created_at }}</dd>
                            <dt>Observações</dt>
                            <dd>{{ $recruitment->obs }}</dd>
                        </dl>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@stop