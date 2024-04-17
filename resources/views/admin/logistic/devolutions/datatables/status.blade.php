@if($row->status == 'concluded')
    <span class="label" style="background: #17a72d">
        @trans('Finalizado')
    </span>
@else
    <span class="label" style="background: #028ce8">
        @trans('Processamento')
    </span>
@endif
{{--
<span class="label" style="background: {{ @$row->status->color }}">
    {{ $row->status_id }}
</span>--}}
