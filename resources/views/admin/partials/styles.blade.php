{{ Html::style('/assets/admin/fonts/exo_2.css') }}
{{ Html::style('/vendor/font-awesome/css/all.min.css') }}
{{ Html::style('/vendor/flag-icon-css/css/flag-icon.min.css') }}
{{ Html::style('/vendor/bootstrap/dist/css/bootstrap.min.css') }}
{{ Html::style('/vendor/iCheck/skins/minimal/blue.css') }}
{{ Html::style('/vendor/intl-tel-input/build/css/intlTelInput.min.css') }}

{!! Minify::stylesheet([
        '/assets/admin/css/template.css',
        '/assets/admin/css/skins/' . app_skin() . '.css',

        '/vendor/datepicker/datepicker3.css',
        '/vendor/datatables/dataTables.bootstrap.css',
        '/vendor/select2/dist/css/select2.css',

        '/vendor/jasny-bootstrap/dist/css/jasny-bootstrap.min.css',
        '/vendor/magicsuggest/magicsuggest-min.css',
        '/vendor/animate.css/animate.css',

        '/assets/admin/css/helper.css',
        '/assets/admin/css/main.css',
    ])->withFullUrl()
!!}