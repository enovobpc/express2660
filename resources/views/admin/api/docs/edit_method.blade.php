{{ Form::model($apiMethod, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>

<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                Configuração Base
            </a>
        </li>
        <li>
            <a href="#tab-params" data-toggle="tab">
                Parametros
            </a>
        </li>
        <li>
            <a href="#tab-body" data-toggle="tab">
                Header & Body
            </a>
        </li>
        <li>
            <a href="#tab-responses" data-toggle="tab">
                Respostas
            </a>
        </li>
        <li>
            <a href="#tab-fields" data-toggle="tab">
                Campos 1
            </a>
        </li>
        <li>
            <a href="#tab-fields2" data-toggle="tab">
                Campos 2
            </a>
        </li>
        <li>
            <a href="#tab-fields3" data-toggle="tab">
                Campos 3
            </a>
        </li>
        <li>
            <a href="#tab-fields4" data-toggle="tab">
                Campos 4
            </a>
        </li>
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0">
    <div class="tab-content m-b-0">
        <div class="tab-pane active" id="tab-info">
            @include('admin.api.docs.partials.info')
        </div>
        <div class="tab-pane" id="tab-params">
            @include('admin.api.docs.partials.params')
        </div>
        <div class="tab-pane" id="tab-body">
            @include('admin.api.docs.partials.body')
        </div>
        <div class="tab-pane" id="tab-responses">
            @include('admin.api.docs.partials.responses')
        </div>
        <div class="tab-pane" id="tab-fields">
            @include('admin.api.docs.partials.fields')
        </div>
        <div class="tab-pane" id="tab-fields2">
            @include('admin.api.docs.partials.fields2')
        </div>
        <div class="tab-pane" id="tab-fields3">
            @include('admin.api.docs.partials.fields3')
        </div>
        <div class="tab-pane" id="tab-fields4">
            @include('admin.api.docs.partials.fields4')
        </div>

    </div>
</div>
<div class="modal-footer">
<!--    <div class="pull-left">
        <div class="checkbox m-b-0 m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_active', 1, $apiMethod->exists ? null : true) }}
                Ativo
            </label>
        </div>
    </div>-->
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
</script>
