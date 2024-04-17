<small>
    @if($row->trigger_fields)
        @foreach($row->trigger_fields as $key => $field)
            <div>
                {{ trans('admin/expenses.trigger-fields.'.$field) }}
                {{ trans('admin/expenses.trigger-operators.'.@$row->trigger_operators[$key]) }}
                @if($field == 'weekday')
                    {{ trans('datetime.weekday.'.@$row->trigger_values[$key]) }}
                @else
                {{ @$row->trigger_values[$key] }}
                @endif
                {{ @$row->trigger_joins[$key] ? (@$row->trigger_joins[$key] == 'or' ? 'ou' : 'e') : '' }}
            </div>
        @endforeach
    @endif
</small>