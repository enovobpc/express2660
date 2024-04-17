{{ @$row->answers->count() }}<br/>
@if(@$row->answers->count())
<small>
    <a href="{{ route('admin.fleet.checklists.show', [$row->id]) }}"
       data-toggle="modal"
       data-target="#modal-remote-lg">
       @trans('Ver Respostas')
    </a>
</small>
@endif