@if($row->doc_serie == 'SIND')
    Saldo Anterior {!! tip('Saldo em aberto que transitou do software de faturação anterior. Questione a empresa sobre os números das faturas a que diz respeito este valor.') !!}
@else
    {{ $row->reference }}
@endif