<?php
    $rowSources = $row->sources && is_array($row->sources) ? array_values($row->sources) : [];
?>

@if(!empty($row->sources))
    @foreach($sources as $source => $sourceName)
        @if(in_array($source, $rowSources))
            <span class="label bg-green">{{ @$sourceName }}</span>
        @else
            <span class="label bg-gray">{{ @$sourceName }}</span>
        @endif
    @endforeach
@endif
