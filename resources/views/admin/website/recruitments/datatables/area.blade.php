<span class="text-muted">Área:</span> {{ trans('admin/recruitment.areas.'.$row->area) }}
<br/>
<span class="text-muted">Cargo:</span>  {{ str_limit($row->role, 20) }}<br/>
<span class="text-muted">Disponibilidade:</span> {{ trans('admin/recruitment.availability.'.$row->availability) }}