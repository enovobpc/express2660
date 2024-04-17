@if ($row->product == 'fuel')
    <span class="label" style="background: #25618c">@trans('Combustível')</span>
@else
    <span class="label label-info">AdBlue</span>
@endif
