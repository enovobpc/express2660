{{ Form::checkbox('row-select', $row->id, null, array('class' => 'row-select')) }}
@if($row->closed_at)
    <div class="w-100 text-center" style="margin-top: -2px">
        <i class="fas fa-check green fs-11" data-toggle="tooltip" title="Fechado em {{ $row->closed_at }}"></i>
    </div>
@endif