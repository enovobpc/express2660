<a href="{{ route('admin.budgets.proposes.show', [$row->budget_id, $row->email]) }}" data-toggle="modal" data-target="#modal-remote-xl">
    @if($row->read)
        <b class="text-black">{{ $row->subject }}</b>
    @else
        <span class="text-yellow bold">{{ $row->subject }} <span class="label label-warning">NOVO</span></span>
    @endif
</a>
<br/>
<i>{{ str_limit(trim(strip_tags($row->message))) }}</i>
