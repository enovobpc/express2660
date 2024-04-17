{{ Form::open(['route' => ['core.license.store'], 'method' => 'post']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Licença e Manutenção</h4>
</div>
<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-maintenance" data-toggle="tab">
                Licença e Manutenção
            </a>
        </li>
        <li>
            <a href="#tab-modules" data-toggle="tab">
                Módulos da Aplicação
            </a>
        </li>
        <li>
            <a href="#tab-cache" data-toggle="tab">
                Dados e Cache
            </a>
        </li>
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0">
    <div class="tab-content m-b-0" style="padding-bottom: 10px;">
        <div class="tab-pane active" id="tab-maintenance">
            @include('admin.core.license.partials.maintenance')
        </div>
        <div class="tab-pane" id="tab-modules">
            @include('admin.core.license.partials.modules')
        </div>
        <div class="tab-pane" id="tab-cache">
            @include('admin.core.license.partials.cache')
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}
@include('admin.core.license.modals.clean_storage')

{{ HTML::style('vendor/ios-checkbox/dist/css/iosCheckbox.min.css')}}
<style>
    table label {
        font-weight: normal;
    }

    .box-settings .icheckbox_minimal-blue,
    td.check .icheckbox_minimal-blue {
        display: initial !important;
    }
    #tab-maintenance td {
        vertical-align: middle !important;
    }

    #modal-remote-xlg {
        z-index: 2000 !important;
    }
</style>

{{ HTML::script('vendor/ios-checkbox/dist/js/iosCheckbox.min.js')}}
{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    $(".modal .select2").select2(Init.select2());
    $(".modal .ios").iosCheckbox();

    $('.btn-clean-storage').on('click', function(){
        $('#files-storage').addClass('in').show();
    })

    $('.btn-close-clean-storage').on('click', function(){
        $('#files-storage').removeClass('in').hide();
    })

    $('.btn-sync-settings').on('click', function(){
        $btn = $(this);
        $btn.button('loading');
        $.post('{{ route('core.license.store') }}', {'action':'sync-settings'},function(data){
            $('.settings-last-sync').html(data)
        }).always(function(){
            $btn.button('reset');
        });
    })

    $(document).ready(function() {
        $.post('{{ route('admin.core.license.directory.load') }}', function(data){
            $('.modal .storage-dir-content').html(data.storage)
            $('.modal .uploads-dir-content').html(data.uploads)

            var totalFiles = data.storage_count + ' ficheiros';
            var totalSize  = data.storage_size;
            var text = totalFiles + ', '+ totalSize

            $('.modal label[for=cache_size]').html(text)
        }).fail(function(){
            var html = '<div class="text-center text-red m-t-20">' +
                '<i class="fas fa-exclamation-circle fs-20"></i><br/>' +
                'Erro de carregamento' +
                '</div>';
            $('.modal .storage-dir-content,.modal .uploads-dir-content').html(html)
        })
    })

</script>

