<div class="pull-left m-r-8 m-t-4">
    <img class="w-25px" src="{{ asset('assets/img/default/mb-icon.svg') }}" />
</div>
<div class="pull-left">
    <span class="text-muted">Entidade:</span> {{ $row->entity }}<br/>
    <span class="text-muted">Referência:</span> {{ chunk_split($row->reference, 3, ' ') }}
</div>
<div class="clearfix"></div>
