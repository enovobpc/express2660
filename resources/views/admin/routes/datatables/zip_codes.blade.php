@if(!empty($row->zip_codes))
    <div>
        <?php

        $i = 0;
        $html = '';
        if(is_array($row->zip_codes)) {
            $html = implode(', ', $row->zip_codes);
        }

        ?>
        @foreach($row->zip_codes as $mapping)
            <?php
            $i++; $count = count($row->zip_codes) - 25;
            ?>
            @if($i <= 25)
                <span class="label label-default text-uppercase">{{ $mapping }}</span>
            @elseif($i == 26)
                <span class="label label-info text-uppercase" data-toggle="popover" data-placement="top" data-content="{{ $html }}">+{{ $count }} códigos</span>
                @php
                    break;
                @endphp
            @endif
        @endforeach
    </div>
@endif