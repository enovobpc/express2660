<a href="{{ route('admin.fleet.maintenances.edit', [$row->id, 'vehicle' => $row->vehicle_id]) }}"
   class="text-uppercase"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ $row->title }}
    @if($row->description)
        <i data-toggle="tooltip" data-html="true" title="{!! nl2br($row->description) !!}" class="fa fa-info-circle"></i>
    @endif
</a>
<br/>
<small class="text-muted">
    <?php $i = 0 ?>
    @foreach($row->parts as $part)
        {!! $i > 0 ? '&bullet;' : '' !!} {{ $part->name }}
        <?php $i++ ?>
    @endforeach
</small>