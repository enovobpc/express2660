@section('title')
Definições Gerais
@stop

@section('content-header')
Definições Gerais 
@stop

@section('breadcrumb')
<li>Administração</li>
<li class="active">
    Definições Gerais
</li>
@stop

@section('content')
    {{ Form::open(['route' => 'admin.settings.store', 'class' => 'form-horizontal', 'files' => true]) }}
    <div class="row box-settings">
        <div class="col-md-3 col-lg-2 p-r-0">
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active">
                            <a href="#tab-geral" data-toggle="tab"><i class="fas fa-fw fa-globe"></i> Configurações Gerais</a>
                        </li>
                        <li>
                            <a href="#tab-shipments" data-toggle="tab"><i class="fas fa-fw fa-truck"></i> Gestão de Serviços</a>
                        </li>
                        <li>
                            <a href="#tab-customers" data-toggle="tab"><i class="fas fa-fw fa-users"></i> Área de Cliente</a>
                        </li>
                        <li>
                            <a href="#tab-printer" data-toggle="tab"><i class="fas fa-fw fa-print"></i> Impressão/Exportação</a>
                        </li>
                        <li>
                            <a href="#tab-mobile-app" data-toggle="tab"><i class="fas fa-fw fa-mobile-alt"></i> Aplicação Móvel</a>
                        </li>
                        <li>
                            <a href="#tab-billing" data-toggle="tab"><i class="fas fa-fw fa-euro-sign"></i> Faturação e Preços</a>
                        </li>
                        <li>
                            <a href="#tab-notifications" data-toggle="tab"><i class="fas fa-fw fa-bell"></i> Envio de Notificações</a>
                        </li>
                        <li>
                            <a href="#tab-texts" data-toggle="tab"><i class="fas fa-fw fa-align-left"></i> Textos Legais</a>
                        </li>
                        <li>
                            <a href="#tab-customization" data-toggle="tab"><i class="fas fa-fw fa-paint-brush"></i> Personalização</a>
                        </li>
                        @if((hasModule('budgets') || hasModule('animal-budgets') || hasModule('courier-budgets')) && (Auth::user()->hasRole(Config::get('permissions.role.admin')) || Auth::user()->ability(Config::get('permissions.role.admin'), 'budgets')))
                            <li>
                                <a href="#tab-budgets" data-toggle="tab"><i class="fas fa-fw fa-file-alt"></i> Orçamentos</a>
                            </li>
                        @endif
                        @if(hasModule('customer_support') && (Auth::user()->isAdmin() || Auth::user()->ability(Config::get('permissions.role.admin'), 'customer_support')))
                            <li>
                                <a href="#tab-customer-support" data-toggle="tab"><i class="fas fa-fw fa-headset"></i> Suporte Cliente</a>
                            </li>
                        @endif
                        @if(config('app.source') == 'asfaltolargo' || config('app.source') == 'royalexpress')
                            <li>
                                <a href="#tab-guides" data-toggle="tab"><i class="fas fa-fw fa-print"></i> Guias Globais</a>
                            </li>
                        @endif
                        <li>
                            <a href="#tab-contacts" data-toggle="tab"><i class="fas fa-fw fa-building"></i> Dados Empresa</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="tab-content tab-content-settings">
                <div class="active tab-pane" id="tab-geral">
                    @include('admin.settings.settings.partials.geral')
                </div>
                <div class="tab-pane" id="tab-shipments">
                    @include('admin.settings.settings.partials.shipments')
                </div>
                <div class="tab-pane" id="tab-mobile-app">
                    @include('admin.settings.settings.partials.mobile')
                </div>
                <div class="tab-pane" id="tab-billing">
                    @include('admin.settings.settings.partials.billing')
                </div>
                <div class="tab-pane" id="tab-customers">
                    @include('admin.settings.settings.partials.customers')
                </div>
                <div class="tab-pane" id="tab-printer">
                    @include('admin.settings.settings.partials.printer')
                </div>
                <div class="tab-pane" id="tab-notifications">
                    @include('admin.settings.settings.partials.notifications')
                </div>
                @if(hasModule('budgets') || Auth::user()->ability(Config::get('permissions.role.admin'), 'budgets'))
                    <div class="tab-pane" id="tab-budgets">
                        @include('admin.settings.settings.partials.budgets')
                    </div>
                @endif
                @if(hasModule('customer_support') && (Auth::user()->isAdmin() || Auth::user()->ability(Config::get('permissions.role.admin'), 'customer_support')))
                    <div class="tab-pane" id="tab-customer-support">
                        @include('admin.settings.settings.partials.customer_support')
                    </div>
                @endif
                <div class="tab-pane" id="tab-guides">
                    @include('admin.settings.settings.partials.guides')
                </div>
                <div class="tab-pane" id="tab-contacts">
                    @include('admin.settings.settings.partials.contacts')
                </div>
                <div class="tab-pane" id="tab-texts">
                    @include('admin.settings.settings.partials.texts')
                </div>
                <div class="tab-pane" id="tab-customization">
                    @include('admin.settings.settings.partials.customization')
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
@stop

@section('styles')
    <style>
        .form-horizontal .control-label {
            text-align: left;
        }
    </style>
    {{ HTML::style('vendor/ios-checkbox/dist/css/iosCheckbox.min.css')}}
@stop

@section('scripts')
{{ HTML::script('vendor/ios-checkbox/dist/js/iosCheckbox.min.js')}}
{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    $('.select2country').select2(Init.select2Country());

    $(".tab-content-settings .ios").iosCheckbox();

    $(document).on('click', '.btn-remove-bg-img', function () {
        $('[name="delete_pdf_bg"]').val(1);
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

    $('select[name="notification_sound"],select[name="customization_notification_sound"]').on('change', function(){
        $.playSound("/assets/sounds/" + $(this).val());
    })
    $('.btn-play-notification').on('click', function(){
        var sound = $(this).closest('tr').find('select').val();
        $.playSound("/assets/sounds/" + sound);
    })


    CKEDITOR.editorConfig = function( config ) {
        config.toolbar = [
            { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
            { name: 'about', items: [ 'About' ] }
        ];
    };

    CKEDITOR.replace('ckeditor-conditions', { height: 500 });

    var smallConfig = [
        { name: 'document', items: [ 'Source'] },
        { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
        { name: 'links', items: [ 'Link', 'Unlink' ] },
        { name: 'styles', items: [ 'Format', 'FontSize' ] },
        { name: 'colors', items: [ 'TextColor'] },
    ];

    CKEDITOR.replace('ckeditor-presentation', { height: 500 });

    CKEDITOR.replace('ckeditor-presentation-en', { height: 500 });

    CKEDITOR.replace('budget-default-answer', {
        height: 370,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-signature', {
        height: 120,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-auto-response', {
        height: 300,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-geral-answer', {
        height: 200,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-geral-answer-en', {
        height: 200,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-animals-answer', {
        height: 200,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-animals-answer-en', {
        height: 200,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-reminder', {
        height: 200,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-reminder-en', {
        height: 200,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-cancel', {
        height: 250,
        toolbar: smallConfig
    });

    CKEDITOR.replace('budget-cancel-en', {
        height: 250,
        toolbar: smallConfig
    });

    CKEDITOR.replace('ticket-signature', {
        height: 120,
        toolbar: smallConfig
    });

    CKEDITOR.replace('ticket-default-answer', {
        height: 370,
        toolbar: smallConfig
    });

    CKEDITOR.replace('ticket-auto-response', {
        height: 300,
        toolbar: smallConfig
    });
</script>
@stop