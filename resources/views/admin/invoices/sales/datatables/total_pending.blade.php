@if($row->doc_type == 'nodoc')
    <b class="text-red">{{ money($row->doc_subtotal, '€') }}</b>
@elseif(!$row->is_settle && $row->doc_type != 'receipt')
    @if($row->doc_total_pending)
        @if($row->doc_total_pending > $row->doc_total)
            <?php
                    //Caso o total pendente seja maior que o total é porque ja está liquidado. Erro de importação, corrige esse erro
            \App\Models\Invoice::where('id', $row->id)->update(['is_settle' => 1, 'doc_total_pending' => null]);
            ?>
        @else
            @if($row->doc_type == 'credit-note' && $row->doc_total_pending > 0.00)
                <b class="text-yellow">-{{ money($row->doc_total_pending, '€') }}</b>
            @else
                <b class="text-yellow">{{ money($row->doc_total_pending, '€') }}</b>
            @endif
        @endif
    @else
        <b class="text-red">{{ money($row->doc_total, '€') }}</b>
    @endif
@endif