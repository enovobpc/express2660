@if($row->zones_arr)

    <?php $html = ''; ?>
    @foreach($row->zones_arr as $key => $zone)
        <?php $service = @$row->services_arr[$key] ? @$row->services_arr[$key] : null; ?>
        @if($key <= 2)
            <?php
            $price   = @$row->values_arr[$key] ? @$row->values_arr[$key] : 0;
            ?>
            <span class="label label-default m-l-3"
                  style="min-width: 30px; display: inline-block;"
                  data-toggle="tooltip"
                  data-html="true"
                  title="{{ @$servicesArr[$service] ? @$servicesArr[$service] : 'Qualquer' }}">
                    {{ @$servicesCodes[$service] ? @$servicesCodes[$service] : 'QQ' }}
            </span>
            <span class="label label-default m-l-3"
                  style="min-width: 30px; display: inline-block;"
                  data-toggle="tooltip"
                  data-html="true"
                  title="Zona de Faturação: {{ strtoupper($zone) }}">
                {{ strtoupper($zone) }}
            </span>
        @if($price == 0)
            <span class="text-red">
                &nbsp;{{ money($price, @$row->unity_arr[$key] == 'euro' ? Setting::get('app_currency') : '%') }}
            </span>
        @else
            &nbsp;{{ money($price, @$row->unity_arr[$key] == 'euro' ? Setting::get('app_currency') : '%') }}
        @endif
        <br/>
    @else
        <?php
        $html.= '<span class="label label-default m-l-3" style="min-width: 30px; display: inline-block;">' . (@$servicesCodes[$service] ? @$servicesCodes[$service] : 'QQ').'</span>
                 <span class="label label-default m-l-3" style="min-width: 30px; display: inline-block;">' . strtoupper($zone).'</span> ';
        if($price == 0) {
            $html.= '<span class="text-red">&nbsp;' . (money($price, @$row->unity_arr[$key] == 'euro' ? Setting::get('app_currency') : '%')) . '</span>';
        } else {
            $html.= money($price, @$row->unity_arr[$key] == 'euro' ? Setting::get('app_currency') : '%');
        }
        $html.='<br/>'
        ?>
    @endif
@endforeach


@if(count($row->zones_arr) > 2)
    <span class="label label-info text-uppercase" data-toggle="popover" data-placement="top" data-content="{{ $html }}">+{{ count($row->zones_arr) - 3 }} ZONAS</span>
@endif
@endif