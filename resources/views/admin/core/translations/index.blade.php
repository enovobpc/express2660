@section('title')
Gestão de Traduções
@stop

@section('content-header')
    Gestão de Traduções
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Gestão de Traduções</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('core.translations.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-wrench"></i> Ferramentas <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('core.translations.find') }}" 
                                            data-method="post"
                                            data-confirm-title="Encontrar traduções"
                                            data-confirm="Confirma a procura de novas traduções?"
                                            data-confirm-label="Encontrar"
                                            data-confirm-class="btn-success">
                                            <i class="fas fa-fw fa-search"></i> Encontrar novas traduções
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('core.translations.import') }}" 
                                            data-method="post"
                                            data-confirm-title="Importar traduções do ficheiro"
                                            data-confirm="Confirma a importação das traduções a partir dos ficheiros de tradução? As traduções em base de dados serão substituidas."
                                            data-confirm-label="Publicar"
                                            data-confirm-class="btn-success">
                                            <i class="fas fa-fw fa-file-alt"></i> Importar traduções do ficheiro
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('core.translations.publish') }}" 
                                            data-method="post"
                                            data-confirm-title="Publicar traduções"
                                            data-confirm="Confirma a publicação das traduções?"
                                            data-confirm-label="Publicar"
                                            data-confirm-class="btn-success">
                                            <i class="fas fa-fw fa-check"></i> Publicar traduções
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            {{-- <button type="button" class="btn btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button> --}}
                        </div>
                    </li>
                    <li class="fltr-primary w-180px">
                        <strong>Idioma</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-130px">
                            {{ Form::select('locale', ['' => 'Todos']+trans('locales'), fltr_val(Request::all(), 'locale', Setting::get('app_locale')), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-150px">
                        <strong>Traduzido</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-80px">
                            {{ Form::select('translated', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), null), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-150px">
                        <strong>Publicado</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-80px">
                            {{ Form::select('published', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), null), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-40">Texto</th>
                                <th class="w-30px"></th>
                                <th>Tradução</th>
                                <th class="w-30px"><i class="fas fa-file-alt"></i></th>
                                <th class="w-1">Estado</th>
                                <th class="w-65px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'core.translations.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">

    $(document).on('change', 'table [name="value"]', function(){
        var $tr       = $(this).closest('tr');
        var action    = $(this).closest('form').attr('action');
        var formData  = $(this).closest('form').serialize();

        $.ajax({
            url: action,
            data: formData,
            type: 'PUT',
            success: function (data) {
               if(data.result) {
                    //Growl.success(data.feedback);
                    $tr.find('.published').html('<i class="fas fa-times-circle text-red" data-toggle="tooltip" title="Não Publicado"></i>')
               } else {
                    Growl.error(data.feedback);
               }
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {});
    })


    $(document).on('click', '.btn-auto-translate', function(e){
        e.preventDefault();

        var apiKey = 'AIzaSyC-VtycgJLEFrFb4OdG97bT7ZBifmwTZ1I';
        var $this  = $(this);
        var target = $this.closest('td').find('[name="value"]');
        var text   = $this.data('text');
        var locale = $this.data('locale');

        $this.html('<i class="fas fa-spin fa-circle-notch"></i>');
        
        $.ajax({
            url: 'https://translation.googleapis.com/language/translate/v2',
            method: 'POST',
            dataType: 'json',
            data: {
                q: text,
                target: locale,
                key: apiKey
            },
            success: function (response) {
                var traducao = response.data.translations[0].translatedText;
                target.val(traducao).trigger('change');
            },
            error: function (error) {
                Growl.error('Erro na tradução');
                console.log(JSON.stringify(error))
            },
            
        }).fail(function(){
            Growl.error500();
        }).always(function(){
            $this.html('<i class="fas fa-language"></i>');
        })
    })

    var oTable;

    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'key', name: 'key'},
                {data: 'locale', name: 'locale', class: 'text-center'},
                {data: 'value', name: 'value'},
                {data: 'is_published', name: 'is_published', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'file', name: 'file', visible: false},
            ],
            order: [[2, "asc"]],
            ajax: {
                url: "{{ route('core.translations.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.locale     = $('select[name=locale]').val();
                    d.translated = $('select[name=translated]').val();
                    d.published  = $('select[name=published]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop