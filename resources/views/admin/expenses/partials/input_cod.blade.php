{{ Form::select('trigger_values[]', ['' => 'Cliente', 'D' => 'Destino', 'S' => 'Remetente'], @$shippingExpense->trigger_values[$key], ['class' => 'form-control input-sm']) }}