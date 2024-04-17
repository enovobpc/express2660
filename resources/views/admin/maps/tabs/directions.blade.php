<div style="overflow-y: auto;
    position: absolute;
    top: 60px;
    bottom: 0;
    right: 5px;
    left: 15px;">
    <div class="waypoint-reference-html hide">
        <div class="input-group m-t-5 route-destination-input">
            <div class="input-group-addon" style="border: none; padding: 0 5px 0 0;">
                <div class="route-line"></div>
                <i class="fas fa-fw fa-circle"></i>
            </div>
            {{ Form::text('searchbox_waypoint', null, ['class' => 'form-control route-waypoint', 'placeholder' => 'Morada ou local intermédio...', 'autocomplete' => 'off','autofill' => 'search1']) }}
            {{ Form::hidden('waypoint_name') }}
            {{ Form::hidden('waypoint_trk') }}
            {{ Form::hidden('waypoint_id') }}
            <div class="input-group-addon" style="border: none; padding: 5px 0 0 8px;">
                <i class="fas fa-trash-alt text-red remove-waypoint"></i>
            </div>
        </div>
    </div>
    <div class="input-group">
        <div class="input-group-addon" style="border: none; padding: 0 5px 0 0;">
            <i class="fas fa-fw fa-car"></i>
        </div>
        {{ Form::text('searchbox', Request::get('address') ? Request::get('address') : Setting::get('zip_code_1').' '. Setting::get('city_1'), ['class' => 'form-control', 'id' => 'pac-input', 'placeholder' => 'Procurar uma morada ou local...', 'autocomplete' => 'search2','autofill' => 'search1']) }}
        <div class="input-group-addon" style="border: none; padding: 5px 0 0 10px;">
            &nbsp;&nbsp;&nbsp;&nbsp;
        </div>
    </div>
    <div class="input-group m-t-5 route-destination-input">
        <div class="input-group-addon" style="border: none; padding: 0 5px 0 0;">
            <div class="route-line"></div>
            <i class="fas fa-fw fa-circle"></i>
        </div>
        {{ Form::text('searchbox_destination', null, ['class' => 'form-control', 'id' => 'pac-input2', 'placeholder' => 'Qual é o Destino?', 'autocomplete' => 'search2','autofill' => 'search1']) }}
        <button type="button"
                style="background: transparent;
    border-left: none;
    position: absolute;
    right: 22px;
    z-index: 10;
    border-color: transparent;"
                class="btn btn-default btn-warehouse"
                data-placement="bottom"
                data-toggle="tooltip"
                title="Inserir morada do armazém"
                data-address="{{ Request::get('address') ? Request::get('address') : Setting::get('zip_code_1').' '. Setting::get('city_1') }}"
                style="background: transparent; border-left: none;">
            <i class="fas fa-home"></i>
        </button>
        <div class="input-group-addon" style="border: none; padding: 5px 0 0 8px;" data-toggle="tooltip" title="Adicionar outro destino">
            <i class="fas fa-plus-circle text-green add-waypoint"></i>
        </div>
    </div>
    <div class="btn-group btn-group-sm" style="margin: 6px 0 0 22px;">
        <button type="button" class="btn bg-blue calc-directions" style="width: 193px;"><i class="fas fa-location-arrow"></i> Traçar Percurso</button>
        <button type="button" class="btn bg-blue dropdown-toggle" data-toggle="dropdown">
            <i class="fas fa-cog"></i> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu directions-route-options" role="menu">
            <li>
                <a href="#">
                    {{ Form::checkbox('avoid_highways', 1, null) }}
                    Evitar Autoestradas
                </a>
            </li>
            <li>
                <a href="#">
                    {{ Form::checkbox('avoid_tolls', 1, null) }} Evitar Portagens
                </a>
            </li>
        </ul>
    </div>
    <button type="button" style="margin-left: 22px; width: 231px;" class="btn btn-sm btn-block btn-default m-t-5" data-empty="1" data-toggle="modal" data-target="#modal-select-shipments">
        Selecionar moradas de envios <i class="fas fa-external-link-square-alt"></i>
    </button>
</div>
