{{ Form::model($page, $formOptions) }}
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {{ Form::label('css', 'CSS') }}
            <div class="line-numbers" id="css-editor" style="height: 500px; width: 100%; border: 1px solid #ddd; padding: 8px">{{ $page->css }}</div>
            {{ Form::textarea('css', null, ['class' => 'hide']) }}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            {{ Form::label('js', 'Javascript') }}
            <div id="js-editor" style="height: 500px; width: 100%; border: 1px solid #ddd; padding: 8px">{{ $page->js }}</div>
            {{ Form::textarea('js', null, ['class' => 'hide']) }}
        </div>
    </div>
    <div class="col-sm-12">
        <button type="submit" class="btn btn-primary">Gravar</button>
    </div>
</div>
{{ Form::hidden('published') }}
{{ Form::hidden('show_title') }}
{{ Form::hidden('show_breadcrumb') }}
{{ Form::close() }}
