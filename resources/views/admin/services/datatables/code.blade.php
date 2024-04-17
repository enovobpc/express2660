<b>{{ $row->display_code }}</b>
@if($row->is_collection)
    <span class="label label-warning">Recolha</span>
@endif

@if(Auth::user()->isAdmin())
<br/><small class="text-green"><i class="fas fa-plug"></i> {{ strtoupper($row->code) }}</small>
@endif
<br/>
