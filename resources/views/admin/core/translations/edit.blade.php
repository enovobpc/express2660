{{ Form::model($translation, $formOptions) }}
<div class="modal-header">
    <button class="close" data-dismiss="modal" type="button">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('key', 'Texto') }}
                @if($translation->exists)
                {{ Form::textarea('_key_', $translation->key, ['class' => 'form-control', 'disabled', 'rows' => 1]) }}
                @else
                {{ Form::textarea('key', $translation->key, ['class' => 'form-control', 'rows' => 1]) }}
                @endif
            </div>
            <hr/>
        </div>
        {{ Form::label('Traduções') }}
        @foreach (trans('locales') as $locale => $localeName)
        <?php
        $value = \App\Models\Core\Translation::where('key', $translation->key)->where('locale', $locale)->first();
        $value = @$value->value;
        ?>
        <div class="col-sm-12">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="flag-icon flag-icon-{{ $locale }}"></i>
                    </div>
                    {{ Form::textarea('value['.$locale.']', $value, ['class' => 'form-control', 'rows' => 1]) }}
                </div>
                
            </div>
        </div>
        @endforeach
    </div>
</div>
</div>
<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" type="button">Fechar</button>
    <button class="btn btn-primary" type="submit">Gravar</button>
</div>
{{ Form::close() }}

<script>
   
</script>
