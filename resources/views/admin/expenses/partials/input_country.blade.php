{{ Form::select('trigger_values[]', trans('country'), @$shippingExpense->trigger_values[$key], ['class' => 'form-control input-sm']) }}