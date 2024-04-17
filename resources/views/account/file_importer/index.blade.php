@section('title')
    {{ trans('account/importer.title') }} -
@stop

@section('account-content')

    <div class="row row-10 {{ !@$hasErrors && @$previewRows ? 'hide' : '' }}">
        {{ Form::open(['route' => 'account.importer.import', 'files' => true, 'method' => 'POST', 'class' => 'form-import-file']) }}
        <div class="col-sm-8">
            <div class="form-group m-b-5">
                {{ Form::label('file', 'Ficheiro a importar', ['class' => 'control-label']) }}
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput">
                        <i class="fas fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                    </div>
                    <span class="input-group-addon btn btn-default btn-file">
                    <span class="fileinput-new">Selecionar</span>
                    <span class="fileinput-exists">Alterar</span>
                    <input type="file" name="file" data-file-format="csv,xls,xlsx" {{ ($hasErrors || @!$previewRows) ? 'required' : ''  }}>
                </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Anular</a>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('import_model', 'Modelo Importação', ['class' => 'control-label']) }}
                {!! Form::select('import_model', $models, null, ['class' => 'form-control select2', 'required']) !!}
            </div>
        </div>
        <div class="col-sm-2">
            <input type="hidden" name="filepath" value="{{ @$filepath }}">

            @if($hasErrors || empty(@$previewRows))
                <input type="hidden" name="preview_mode" value="1">
            @else
                <input type="hidden" name="preview_mode" value="0">
            @endif

            @if($hasErrors || @!$previewRows)
                <button type="submit" class="btn btn-block btn-black btn-validate m-t-20" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A validar...">
                    <i class="fas fa-upload"></i> Validar Ficheiro
                </button>
            @else
                <button type="submit" class="btn btn-block btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar..."><i class="fas fa-check"></i> Carregar Dados</button>
                <a href="{{ route('admin.importer.index') }}" class="btn btn-default">Cancelar</a>
            @endif
        </div>
        {{ Form::hidden('rguide') }}
        {{ Form::hidden('rcheck') }}
        {{ Form::hidden('rpack') }}
        {{ Form::close() }}
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="info-upload {{ @$previewRows ? 'hide' : '' }}">
                <h3>Carregar envios em massa</h3>
                <p>
                    Adicione vários envios de uma só vez ao carregar um ficheiro .xlsx
                    <br/>
                    Transfira o Ficheiro Modelo para mostrar o formato a utilizar.
                </p>
                <a href="{{ coreUrl('/uploads/models/modelo_importacao_envios.xlsx') }}" class="btn btn-sm btn-default m-t-20">
                    <i class="fas fa-download"></i> Download Ficheiro Modelo
                </a>
            </div>
            @include('account.file_importer.partials.preview_file')
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(document).on('click', '.btn-conclude', function(){
        $('.form-import-file').submit();
    });

    $(document).find('.item-row .preview-checkbox').each(function(){
        $(this).trigger('click');
        $(this).trigger('click');
        updateCheckboxes($(this));
    });

    $(document).on('change', '.preview-checkbox', function(){
        updateCheckboxes($(this));
    })

    function updateCheckboxes(thisObj) {
        var field = thisObj.data('field');
        var $targetInput = $('[name="'+field+'"]');

        newData = [];
        $('[data-field="'+field+'"]').each(function(){
            if($(this).is(':checked')) {
                newData.push($(this).data('row-id'));
            }
        });

        $targetInput.val(newData.join(','));
    }
</script>
@stop