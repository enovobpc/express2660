@if($row->id_card)
<div><small>@trans('CC')</small> {{ $row->id_card }}</div>
@endif
@if($row->vat)
<div><small>@trans('NIF')</small> {{ $row->vat }}</div>
@endif