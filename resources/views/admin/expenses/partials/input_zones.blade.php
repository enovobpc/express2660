{{ Form::select('trigger_values[]', [''=> ''] + $billingZones, @$shippingExpense->trigger_values[$key], ['class' => 'form-control input-sm']) }}