<div><i class="fas fa-fw fa-weight-hanging fs-10"></i> {{ $row->shipments->sum('weight') ?? '0' }}<small class="text-muted">kg</small></div>
<div><i class="fas fa-fw fa-ruler-horizontal fs-10"></i> {{ $row->shipments->sum('ldm') ?? '0' }}<small class="text-muted">ldm</small></div>