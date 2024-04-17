@if(empty($billedItems))
    <p class="text-center text-muted">
        <i class="fas fa-info-circle fs-20 m-t-75 m-b-5"></i><br/>
        Não existem faturas por emitir<br/>para os clientes selecionados.
    </p>
@else
<div>
    {{ Form::text('search', null, ['class' => 'form-control', 'placeholder' => 'Procurar...']) }}
</div>
<div style="height: 244px; overflow-y: auto; border-radius: 3px;">
    <table class="table table-condensed">
        <tr>
            <th class="bg-gray">Cliente</th>
            <th class="bg-gray w-80px">Fatura</th>
        </tr>
        @foreach($billedItems as $row)
            <tr data-filter-text="{{ strtolower(removeAccents($row['customer_name'])) }}" style="{{ @$row['invoice_id'] ? : 'background: #ffdddd' }}">
                <td>
                    @if(@$row['invoice_id'])
                        {{ $row['customer_name'] }}
                    @else
                        {{ $row['customer_name'] }}
                        <a href="{{ route('admin.billing.customers.show', [$row['customer_id'], 'month' => $month, 'year' => $year, 'period' => $period]) }}" target="_blank">
                            <i class="fas fa-external-link-alt"></i>
                        </a><br/>
                        <span class="text-red" style="    word-break: break-word;">
                            <i><small>{!! $row['feedback'] ? $row['feedback'] : 'Sem informação.'  !!}</small></i>
                        </span>
                    @endif
                </td>
                <td>
                    @if(@$row['invoice_id'])
                    <a href="{{ route('admin.invoices.download.pdf', @$row['invoice_id']) }}" target="_blank">
                        <i class="fas fa-file-alt"></i> FT {{ @$row['invoice_doc_id'] }}
                    </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endif