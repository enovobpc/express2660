<i class="fas fa-square" style="color: {{ $row->color }}"></i>
{{ Html::link(route('admin.prices-tables.edit', $row->id), $row->name) }}