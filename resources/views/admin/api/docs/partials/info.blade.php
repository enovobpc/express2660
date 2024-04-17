<div class="row row-5">
    <div class="col-sm-1">
        <div class="form-group is-required">
            {{ Form::label('api_version', 'Versão') }}
            {{ Form::text('api_version', null, ['class' => 'form-control nospace lowercase', 'required']) }}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group is-required">
            {{ Form::label('category_id', 'Categoria') }}
            {{ Form::select('category_id', [''=>''] + $categories, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group is-required">
            {{ Form::label('section_id', 'Secção') }}
            {{ Form::select('section_id', [''=>''] + $sections, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group is-required">
            {{ Form::label('levels', 'Levels') }}
            {{ Form::select('levels', ['public' => 'Public', 'partners' => 'Partners', 'mobile' => 'Mobile'], null, ['class' => 'form-control select2', 'required', 'multiple']) }}
        </div>
    </div>
</div>
<div class="row row-5">
    <div class="col-sm-3">
        <div class="form-group is-required">
            {{ Form::label('name', 'Título') }}
            {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 35]) }}
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            {{ Form::label('method', 'Método') }}
            {{ Form::select('method', ['' => '', 'get' => 'GET', 'post' => 'POST', 'put' => 'PUT', 'delete'=> 'DELETE'], null, ['class' => 'form-control select2']) }}
        </div>
    </div>
    <div class="col-sm-8">
        <div class="form-group">
            {{ Form::label('url', 'URL') }}
            {{ Form::text('url', null, ['class' => 'form-control nospace']) }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group is-required">
            {{ Form::label('description', 'Descrição') }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'required', 'rows' => 10]) }}
        </div>
    </div>
</div>