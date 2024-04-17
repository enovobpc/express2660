<?php
    $documentTotal = 0;
    $collectionsAssigned = [];
    $today = date('Y-m-d')
?>
<div>
    @if($documents)
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt;">
        <tr>
            <th style="width: 90px">Data</th>
            <th>Documento</th>
            <th>Total</th>
            <th>Vencimento</th>
            <th>Pagamento</th>
        </tr>
        @foreach($documents as $document)
            <tr>
                <td>{{ $document->date->format('Y-m-d') }}</td>
                <td>{{ $document->doc_serie }} {{ $document->doc_id }}</td>
                <td>{{ money($document->total, Setting::get('app_currency')) }}</td>
                <td>
                    <?php $date = new Date($document->due_date); ?>
                    @if($date < $today)
                        <span style="color: red">
                        {{ $date->format('Y-m-d') }}<br/>
                        <small>{{ $date->diffInDays($today) }} dias em atraso</small>
                    </span>
                    @else
                        {{ $date->format('Y-m-d') }}
                    @endif
                </td>
                <td>
                </td>
            </tr>
        @endforeach
        </table>
        <h4 class="pull-right text-right m-t-0" style="width: 100%">
            <div style="width: 100px; float: right">
                <small style="width: 100px; float: left">Em Dívida: <br/><b class="bold" style="color: #000;">{{ money($totalDocuments, Setting::get('app_currency')) }}</b></small>
            </div>
            <div style="width: 100px; float: right">
                <small>Documentos: <br/><b class="bold" style="color: #000;">{{ $countDocuments }}</b></small>
            </div>
        </h4>
    @endif
</div>