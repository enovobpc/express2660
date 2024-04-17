{{--@if(!empty($row->agencies))
    @foreach($row->agencies as $agency)
    <span class="label" style="background: {{ @$agencies[$agency][0]['color'] }}" data-toggle="tooltip" title="{{ @$agencies[$agency][0]['name'] }}">{{ @$agencies[$agency][0]['code'] }}</span>
    @endforeach
@endif--}}

@if(!empty($row->agencies))
    <?php
    $i = 0;
    $html = '';
    ?>
    @foreach($row->agencies as $agency)
        <?php $i++; ?>
        @if($i <= 6)
            <span class="label" style="background: {{ @$agencies[$agency][0]['color'] }}" data-toggle="tooltip" title="{{ @$agencies[$agency][0]['name'] }}">{{ @$agencies[$agency][0]['code'] }}</span>
        @elseif($i == 7)
            <?php
            $count = count($row->agencies) - 6;
            foreach($row->agencies as $agency) {
                $html.= @$agencies[$agency][0]['name'].'<br/>';
            }
            ?>
            <span class="label label-info text-uppercase" data-toggle="popover" data-placement="top" data-content="{{ $html }}">+{{ $count }} @trans('Agencias')</span>
        @endif
    @endforeach
@endif