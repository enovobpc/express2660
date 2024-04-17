@if(empty($row->zip_codes))
    <i>Todos</i>
@else
    <div>
        <?php
        $i = 0;
        $html = '';
        if(is_array($row->zip_codes)) {
            $html = implode(', ', $row->zip_codes);
        }

        ?>
        @foreach($row->zip_codes as $zipCode)

            <?php
            $i++; $count = count($row->zip_codes) - 10;
            ?>
            @if($i <= 10)
                <span class="label label-default text-uppercase">{{ $zipCode }}</span>
            @elseif($i == 11)
                <span class="label label-info text-uppercase" data-toggle="popover" data-placement="top" data-content="{{ $html }}">+{{ $count }} zonas</span>
            @endif
        @endforeach
    </div>
@endif