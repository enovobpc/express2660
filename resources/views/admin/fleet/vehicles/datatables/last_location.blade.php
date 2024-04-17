@if($row->latitude)
    <?php
    $html = '<ul class="list-unstyled">';
    $html.= '<li><b>ID viatura:</b> '. $row->gps_id.'</li>';
    $html.= '<li><b>Localização:</b> '. $row->gps_zip_code.' '.$row->gps_city.'</li>';
    $html.= '<li><b>País:</b> <i class="flag-icon flag-icon-'.$row->gps_country.'"></i> '. trans('country.'. $row->gps_country).'</li>';
    $html.= '<li><b>Velocidade:</b> '. $row->speed.' km/h</li>';
    $html.= '<li><b>Combustível:</b> '. $row->fuel_level_html.'</li>';
    $html.= '</ul>'
    ?>
    <div data-toggle="popover"
         data-placement="top"
         data-title="Atualizado em {{ $row->last_location }}"
         data-content="{{ $html }}"
         data-html="true">
        @if($row->is_ignition_on)
            <span class="label label-success">
                <i class="fas fa-play"></i> @trans('Andamento')
            </span>
        @else
            <span class="label label-warning">
                <i class="fas fa-pause"></i> @trans('Parado')
            </span>
        @endif
    </div>
    @if($row->gps_city)
    <div>
        <small>
        {{ $row->gps_city }},
        {{ strtoupper($row->gps_country) }}
        </small>
    </div>
    @endif
@endif