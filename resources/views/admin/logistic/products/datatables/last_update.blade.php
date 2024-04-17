<?php $dt = new \Jenssegers\Date\Date($row->last_update); ?>
{{ $dt->format('Y-m-d') }}
<br/>
<small>{{ $dt->format('H:i:s') }}</small>
