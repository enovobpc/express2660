@section('title')
    Instalação e Setup
@stop

@section('content-header')
    Instalação e Setup
@stop

@section('breadcrumb')
<li class="active">
    Instalação e Setup
</li>
@stop

@section('content')

    @include('admin.core.installer.partials.import_logos')

    @if(!$agency->exists)
        {{ Form::open(['route' => 'admin.core.install.store', 'files' => true]) }}
    @else
        <div class="overlay" style="opacity: 0.5">
    @endif
    <div class="row">
        <div class="col-sm-3 col-md-2">
            <div class="box no-border">
                <div class="box-body">
                    <div class="row row-10">
                        <div class="col-xs-12">
                                <a href="{{ route('admin.core.terminal.index') }}" 
                                target="_blank" class="btn btn-block btn-sm bg-purple m-b-5">
                                <i class="fas fa-terminal"></i> Terminal</a>
                                <a href="{{ route('admin.core.install.test-email') }}"
                                data-method="post"
                                data-confirm-title="Enviar e-mail teste"
                                data-confirm="Confirma o envio do email de teste para {{ Auth::user()->email }}?"
                                data-confirm-label="Enviar E-mail"
                                data-confirm-class="btn-success"
                                class="btn btn-block btn-sm btn-default m-b-5">
                                    <i class="fas fa-envelope"></i> Testar envio e-mail
                                </a>
                                <a href="{{ route('core.translations.index')}}" target="_blank"
                                class="btn btn-block btn-sm btn-default m-b-5">
                                    <i class="fas fa-font"></i> Gestão Traduções
                                </a>
                                <a href="#"
                                data-toggle="modal"
                                data-target="#modal-import-logos"
                                class="btn btn-block btn-sm btn-default m-b-5">
                                    <i class="fas fa-images"></i> Importar Logos
                                </a>
                        </div>
                    </div>
                    <hr/>
                    <div class="row row-10">
                        <div class="col-sm-12">
                            <h4 class="bold m-t-0 text-blue">CONFIGURAÇÃO.ENV</h4>
                            <i class="fas fa-square" style="color: {{ env('APP_COLOR_PRIMARY') }}"></i> Cor 1<br/>
                            <i class="fas fa-square" style="color: {{ env('APP_COLOR_SECUNDARY') }}"></i> Cor 2<br/>
        
                        </div>
                        {{-- <div class="col-xs-12">
                            <h4 class="bold m-t-0 text-blue">CHECKLIST</h4>
                        </div>
                        <div class="col-sm-12">
                            <ul class="list-unstyled">
                                <li class="m-b-5">
                                    <label style="font-weight: normal">
                                        {{ Form::checkbox('checklist', 1) }}
                                        1. Correr migrates
                                    </label>
                                </li>
                                <li class="m-b-5">
                                    <label style="font-weight: normal">
                                        {{ Form::checkbox('checklist', 1) }}
                                        2. Confirmar Cores .env
                                    </label>
                                </li>
                                <li class="m-b-5">
                                    <label style="font-weight: normal">
                                        {{ Form::checkbox('checklist', 1) }}
                                        3. Testar envio e-mail
                                    </label>
                                </li>
                                <li><hr/></li>
                                <li class="m-b-5">
                                    <label style="font-weight: normal">
                                        {{ Form::checkbox('checklist', 1) }}
                                        1. Configurar cores - Agência
                                    </label>
                                </li>
                                <li class="m-b-5">
                                    <label style="font-weight: normal">
                                        {{ Form::checkbox('checklist', 1) }}
                                        2. Ver se há módulos adicionais contratados
                                    </label>
                                </li>
                                <li class="m-b-5">
                                    <label style="font-weight: normal">
                                        {{ Form::checkbox('checklist', 1) }}
                                        3. Ver se há definições Gerais adicionais
                                    </label>
                                </li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9 col-md-10">
            <div class="box no-border">
                <div class="box-body">
                    @include('admin.core.installer.partials.settings')
                    <hr/>
                    @include('admin.core.installer.partials.info')
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    @include('admin.core.installer.partials.providers')
                </div>
            </div>
        </div>

    @if(!$agency->exists)
        <div class="row">
            <div class="col-xs-12">
                <div class="box-footer">
                    <button class="btn btn-primary">Gravar</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    @else
        </div>
    @endif

    <style>
        ol {
            background: #00a6fb;
            font-size: 13px;
            line-height: 19px;
            color: #fff;
            padding: 10px 25px;
            border-radius: 4px;
        }
    </style>

@stop


@section('styles')
    {{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
    {{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
@stop

@section('scripts')
    {{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
    <script type="text/javascript">
        $(document).ready(function () {
            $('select[name="color"], .color').simplecolorpicker({theme: 'fontawesome'});
        })

        $(document).on('mouseover', '.select2-results__option', function(){
            var skinText = $(this).html();
            var skin = $('[name="app_skin"] option, [name="customization_app_skin"] option').filter(function () { return $(this).html() == skinText; }).val();
            var currentSkin = $('.skin-preview').data('current-skin');

            $('#try-skin').remove();

            $('.skin-master').parent().removeClass().addClass(skin)
            $('body').addClass(skin)
            $("head").append("<link id='try-skin' href='/assets/admin/css/skins/"+skin+".css' type='text/css' rel='stylesheet' />");
        });

        $('.prefill-data').on('click', function(e){
            e.preventDefault();

            $('[name="company"]').val("{!! Setting::get('company_name')  !!}");
            $('[name="vat"]').val("{{ Setting::get('vat') }}");
            $('[name="phone"]').val("{{ trim(Setting::get('company_phone')) }}");
            $('[name="mobile"]').val("{{ trim(Setting::get('company_mobile')) }}");
            $('[name="address"]').val("{{ trim(Setting::get('company_address')) }}");
            $('[name="zip_code"]').val("{{ trim(Setting::get('company_zip_code')) }}");
            $('[name="city"]').val("{{ trim(Setting::get('company_city')) }}");
            $('[name="country"]').val({{ Setting::get('company_country') }}).trigger('change');
            $('[name="email"]').val("{{ trim(strtolower(Setting::get('company_email'))) }}");
            $('[name="web"]').val("{{ request()->getHost() }}");
        })
    </script>
@stop