{{ Form::model($status, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-8">
            <div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#locale-pt" role="tab" data-toggle="tab">PT</a></li>
                    <li><a href="#locale-en" role="tab" data-toggle="tab">EN</a></li>
                    <li><a href="#locale-fr" role="tab" data-toggle="tab">FR</a></li>
                    <li><a href="#locale-es" role="tab" data-toggle="tab">ES</a></li>
                </ul>
                
                <div class="tab-content" style="padding-top: 15px">
                    <div role="tabpanel" class="tab-pane active" id="locale-pt">
                        <div class="form-group is-required">
                            {{ Form::label('name', 'Designação PT') }}
                            {{ Form::text('name', substr($status->name, 0, 20), ['class' => 'form-control', 'maxlength' => 20]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('description', 'Descrição PT') }}
                            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="locale-en">
                        <div class="form-group">
                            {{ Form::label('name_en', 'Designação EN') }}
                            {{ Form::text('name_en', substr($status->name_en, 0, 20), ['class' => 'form-control', 'maxlength' => 20]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('description_en', 'Descrição EN') }}
                            {{ Form::textarea('description_en', null, ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="locale-fr">
                        <div class="form-group">
                            {{ Form::label('name_fr', 'Designação FR') }}
                            {{ Form::text('name_fr', substr($status->name_fr, 0, 20), ['class' => 'form-control', 'maxlength' => 20]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('description_fr', 'Descrição FR') }}
                            {{ Form::textarea('description_fr', null, ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="locale-es">
                        <div class="form-group">
                            {{ Form::label('name_es', 'Designação ES') }}
                            {{ Form::text('name_es', substr($status->name_es, 0, 20), ['class' => 'form-control', 'maxlength' => 20]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('description_es', 'Descrição ES') }}
                            {{ Form::textarea('description_es', null, ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                    </div>
                </div>

            </div>
            

        </div>
        <div class="col-sm-4">
            
            {{ Form::label('type', 'Tipo de Estado') }}
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_shipment', 1, $status->exists ? null : true) }}
                    Estado de envio
                </label>
                {!! tip('Selecione esta caixa se este estado estiver associado aos envios') !!}
            </div>
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_collection', 1) }}
                    Estado de recolha
                </label>
                {!! tip('Selecione esta caixa se este estado estiver associado aos pedidos de recolha') !!}
            </div>
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_final', 1) }}
                    Estado Final
                </label>
                {!! tip('Selecione esta caixa se este estado significar o término do processo de entrega.') !!}
            </div>
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_traceability', 1, $status->exists ? null : true) }}
                    Picking/Rastreab.
                </label>
                {!! tip('Estado disponível para fazer leituras de chegada/saida no menu de picking/rastreabilidade') !!}
            </div>
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_public', 1, $status->exists ? null : true) }}
                    Visivel Público/Clientes
                </label>
                {!! tip('Tornar este estado público') !!}
            </div>
            @if(Auth::user()->isAdmin())
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_static', 1) }}
                    Estático (só admins)
                </label>
            </div>
            @endif
            <div class="form-group">
                {{ Form::label('tracking_step', 'Etapa Tracking') }}
                {{ Form::select('tracking_step', ['' => '', 'pending' => 'Documentado', 'accepted' => 'Aceite', 'pickup' => 'Recolhido', 'transport'=>'Transporte', 'delivered'=>'Entregue', 'incidence'=>'Incidência', 'returned' => 'Devolvido'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group m-0">
                {{ Form::label('color', 'Idêntificador') }}<br/>
                {{ Form::select('color', trans('admin/global.colors')) }}
            </div>
        </div>
    </div>
    {{--<hr class="m-t-5 m-b-5"/>
    <label>Ativar estado para as plataformas</label>
    <div class="row">
        @foreach($sources as $id => $agency)
            <div class="col-sm-4">
                <div class="checkbox m-t-5 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('sources[]', $id) }}
                        {{ $agency }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>--}}
</div>
<div class="modal-footer">
    <div>
        <div class="checkbox pull-left">
            <label style="padding-left: 0">
                {{ Form::checkbox('is_visible', 1, $status->exists ? null : true) }}
                Estado ativo/visivel
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $('.modal .select2').select2(Init.select2());

    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
    })
</script>
