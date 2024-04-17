@if(!empty($row->mapping))
    <div>
        <?php

        $i = 0;
        $html = '';
        if(is_array($row->mapping)) {
            $html = implode(', ', $row->mapping);
        }

        ?>
        @foreach($row->mapping as $mapping)
            <?php
            $i++; $count = count($row->mapping) - 10;
            ?>
            @if($i <= 10)
                <span class="label label-default text-uppercase">{{ $mapping }}</span>
            @elseif($i == 11)
                <span class="label label-info text-uppercase" data-toggle="popover" data-placement="top" data-content="{{ $html }}">+{{ $count }} zonas</span>
                @php
                    break;
                @endphp
            @endif
        @endforeach
    </div>
    <div>
    @if(!empty($row->pack_types))
        @foreach($row->pack_types as $packType)
            <span class="label text-uppercase" style="background: #aaa;"><i class="fas fa-box"></i> {{ @$packType }}</span>
        @endforeach
    @endif
    </div>
@endif