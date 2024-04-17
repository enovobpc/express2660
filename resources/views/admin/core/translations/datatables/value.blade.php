{{ Form::open(['route' => ['core.translations.update', $row->id, 'target' => 'field']]) }}
<span>
    <div style="position: relative">
        {{ Form::textarea('value', $row->value, ['class' => 'form-control', 'style' => 'width: 100%; min-height: 34px;', 'rows' => 1]) }}
        <div>
            <button class="btn btn-default btn-auto-translate" 
                style="position: absolute;top: 0;right: 0;"
                type="button"
                data-toogle="tooltip" 
                title="Traduzir Automáticamente"
                data-text="{{ $row->key }}"
                data-locale="{{ \App\Models\Core\Translation::getISOLocale($row->locale) }}">
                <i class="fas fa-language"></i>
            </button>
        </div>
    </div>
</span>
{{ Form::close() }}
