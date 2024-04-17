@if($row->reference)
<div style="width: 110px; word-break: break-all;" data-toggle="tooltip" title="Ref. Cliente">
    <span class="label label-default">1</span> {{ $row->reference }}
</div>
@endif
@if($row->reference2)
<div style="width: 110px; word-break: break-all;" data-toggle="tooltip" title="{{ @$ref2Name }}">
    <span class="label label-default">2</span> {{ $row->reference2 }}
</div>
@endif
@if($row->reference3)
<div style="width: 110px; word-break: break-all;" data-toggle="tooltip" title="{{ @$ref3Name }}">
    <span class="label label-default">3</span> {{ $row->reference3 }}
</div>
@endif