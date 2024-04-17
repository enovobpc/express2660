<?php
    $content = $row->message;
    $content = str_replace('style', 'style2', $content);
?>
{!! $content !!}