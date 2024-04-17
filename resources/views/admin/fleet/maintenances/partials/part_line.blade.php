<tr>
    <td>{{ $part->part->reference }}</td>
    <td>{{ $part->part->name }}</td>
    <td>{{ Form::text('qty', $part->qty, ['class'=>'form-control'])}}</td>
</tr>