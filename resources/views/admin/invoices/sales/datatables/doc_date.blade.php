<!--
<div class="text-center">
    {{ $row->doc_date }}
    <br/>
    @if($row->doc_type == 'credit-note')
        <span class="label" style="background: #ed3c31">
            Nota Crédito
        </span>
    @elseif($row->doc_type == 'receipt')
        <span class="label" style="background: #97cf47">
            Recibo
        </span>
    @elseif($row->doc_type == 'nodoc')
        <span class="label" style="background: #777">
            Sem Doc.
        </span>
    @elseif($row->doc_type == 'regularization')
        <span class="label" style="background: #40899f">
            Regularização
        </span>
    @else
    <span class="label" style="background: {{ trans('admin/billing.targets-colors.' . $row->target) }}">
        {{ trans('admin/billing.targets.' . $row->target) }}
    </span>
    @endif
</div>-->


@if($row->doc_type == 'invoice')
    <div class="text-center">
        {{ $row->doc_date }}
        <br/>
        <span class="label" style="background: {{ trans('admin/billing.types_color.' . $row->doc_type) }}">
            @if($row->target == 'CustomerBilling')
                Fatura Mensal
            @else
                {{ trans('admin/billing.types_color_text.' . $row->doc_type) }}
            @endif
        </span>
    </div>
@else
    <div class="text-center">
        {{ $row->doc_date }}
        <br/>
        <span class="label" style="background: {{ trans('admin/billing.types_color.' . $row->doc_type) }}">
            {{ trans('admin/billing.types_color_text.' . $row->doc_type) }}
        </span>
    </div>
@endif
