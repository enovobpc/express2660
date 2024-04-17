{{ Html::link(route('admin.prospects.edit', $row->id), $row->name) }}
<br/>
<i class="text-muted">{{ @$row->type->name }}</i>