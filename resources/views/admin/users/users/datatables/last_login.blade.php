@if($row->last_login)
<?php
$html = "<div class='text-center'>" . $row->last_login . '</div>';
$html.= "<table class='table table-condensed m-0'>";
$html.= "<tr>";
$html.= "<td>País</td>";
$html.= "<td><i class='fas fa-spin fa-circle-notch'></i></td>";
$html.= "</tr><tr>";
$html.= "<td>Localidade</td>";
$html.= "<td><i class='fas fa-spin fa-circle-notch'></i></td>";
$html.= "</tr><tr>";
$html.= "<td style='width: 80px'>Cód. Postal</td>";
$html.= "<td><i class='fas fa-spin fa-circle-notch'></i></td>";
$html.= "</tr><tr>";
$html.= "<td>ISP</td>";
$html.= "<td class='ip-isp'><i class='fas fa-spin fa-circle-notch'></i></td>";
$html.= "</tr>";
$html.= "</table>";
?>
<div class="cursor-pointer" data-ip="{{ $row->ip }}" data-time="{{ $row->last_login }}" data-loaded="0" data-toggle="popover" data-trigger="hover" data-placement="left" data-title="Histórico do último acesso" data-content="{!! $html !!}" data-placement="top">
    {{ 'Há ' . timeElapsedString($row->last_login) }}
    <br/>
    <small>IP: {{ $row->ip }} <i class="fas fa-external-link-square-alt"></i></small>
</div>
@endif