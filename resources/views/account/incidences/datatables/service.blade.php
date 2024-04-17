<div data-toggle="tooltip" title="{{ @$row->service->name }}">
    {{ @$row->service->display_code }}
</div>

@if($row->is_collection)
    @if($row->start_hour)
        <span class="label ship-info bg-red m-r-5"
              data-toggle="tooltip"
              title="Horário Recolha: {{ $row->start_hour }} {{ $row->end_hour ?  '-' . $row->end_hour : '' }}">
            <i class="far fa-clock"></i>
        </span>
    @endif
@endif

@if($row->charge_price != 0.00)
    <span class="label ship-info bg-purple m-r-5"
          data-toggle="tooltip"
          title="A cobrar: {{ money($row->charge_price, Setting::get('app_currency')) }}">
        <i class="fas fa-euro-sign"></i>
    </span>
@endif

@if($row->has_return)
    <?php $returnLabel = '' ?>
    @foreach($row->has_return as $key => $item)
        <?php
        $key == 0 ? $separator = '' : $separator = ' / ';
        $returnLabel.= $separator . trans('admin/shipments.return_types.' .$item)
        ?>
    @endforeach
    <span class="label ship-info label-success m-r-5"
          data-toggle="tooltip"
          title="Retorno: {{ $returnLabel }}">
        <i class="fas fa-undo"></i>
    </span>
@endif