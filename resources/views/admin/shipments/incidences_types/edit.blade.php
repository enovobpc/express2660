{{ Form::model($incidenceType, $formOptions) }}
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
                    <li class="active"><a href="#locale-pt" role="tab" data-toggle="tab">{{ strtoupper(Setting::get('app_country')) }}</a></li>
                    <li><a href="#locale-en" role="tab" data-toggle="tab">EN</a></li>
                    <li><a href="#locale-fr" role="tab" data-toggle="tab">FR</a></li>
                    <li><a href="#locale-es" role="tab" data-toggle="tab">ES</a></li>
                </ul>

                <div class="tab-content" style="padding-top: 15px">
                    <div role="tabpanel" class="tab-pane active" id="locale-pt">
                        <div class="form-group is-required">
                            {{ Form::label('name', 'Designação '. strtoupper(Setting::get('app_country'))) }}
                            {{ Form::text('name', substr($incidenceType->name, 0, 50), ['class' => 'form-control', 'maxlength' => 50]) }}
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="locale-en">
                        <div class="form-group">
                            {{ Form::label('name_en', 'Designação EN') }}
                            {{ Form::text('name_en', substr($incidenceType->name_en, 0, 50), ['class' => 'form-control', 'maxlength' => 50]) }}
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="locale-fr">
                        <div class="form-group">
                            {{ Form::label('name_fr', 'Designação FR') }}
                            {{ Form::text('name_fr', substr($incidenceType->name_fr, 0, 50), ['class' => 'form-control', 'maxlength' => 50]) }}
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="locale-es">
                        <div class="form-group">
                            {{ Form::label('name_es', 'Designação ES') }}
                            {{ Form::text('name_es', substr($incidenceType->name_es, 0, 50), ['class' => 'form-control', 'maxlength' => 50]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            {{ Form::label('type', 'Características') }}
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_active', 1, $incidenceType->exists ? null : 1) }}
                    Ativo
                </label>
            </div>
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_shipment', 1, $incidenceType->exists ? null : 1) }}
                    Incidência de Expedição
                </label>
            </div>
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('is_pickup', 1, $incidenceType->exists ? null : 1) }}
                    Incidência de Recolha
                </label>
            </div>
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('operator_visible', 1, $incidenceType->exists ? null : 1) }}
                    Visivel na App Motorista
                </label>
            </div>
        </div>
    </div>
    <hr style="margin: 0"/>
    <div class="row">
        <div class="col-sm-6">
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::checkbox('photo_required', 1) }}
                    Obrigra inserir fotografia
                </label>
            </div>
            <div class="checkbox m-b-0">
                <label style="padding-left: 0">
                    {{ Form::checkbox('date_required', 1) }}
                    Obriga inserir nova data
                </label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="checkbox m-b-0">
                <label style="padding-left: 0">
                    {{ Form::checkbox('pudo_required', 1) }}
                    Obriga inserir ponto PUDO
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());
</script>
