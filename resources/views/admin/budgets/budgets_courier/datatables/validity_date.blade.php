@if($row->validity_date)
{{ $row->validity_date->format('Y-m-d') }}
<br/>
<small class="text-muted">{{ $row->validity_days }} dias</small>
@endif