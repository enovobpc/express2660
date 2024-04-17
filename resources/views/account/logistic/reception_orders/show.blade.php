<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Detalhes Receção Artigos #{{ $receptionOrder->code }}</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-9">
            <div class="form-group">
                {{ Form::label('document', 'Fatura/Documento associado') }}
                {{ Form::text('document', $receptionOrder->document, ['class' => 'form-control', 'disabled']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('requested_date', ($receptionOrder->received_date ? 'Data Recepção' : 'Data Prevista Recepção')) }}
                <div class="input-group">
                    {{ Form::text('requested_date', $receptionOrder->received_date ?: $receptionOrder->requested_date, ['class' => 'form-control datepicker', 'disabled']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-condensed m-b-5 table-products">
        <tr>
            <th style="padding-left: 0">Artigo (SKU ou Designação)</th>
            <th class="w-90px">Qtd</th>
            <th class="w-90px">Qtd. Receb.</th>
            <th class="w-180px">SKU</th>
        </tr>

        @foreach ($receptionOrder->lines as $line)
        <tr>
            <td style="padding-left: 0">
                {{ Form::text('name[]', $line->product->name, ['class' => 'form-control', 'disabled']) }}
            </td>
            <td>{{ Form::text('qty[]', $line->qty, ['class' => 'form-control', 'disabled']) }}</td>
            <td>{{ Form::text('qty_received[]', $line->qty_received ?? 0, ['class' => 'form-control', 'disabled']) }}</td>
            <td>{{ Form::text('sku[]', $line->product->sku, ['class' => 'form-control', 'disabled']) }}</td>
        </tr>
        @endforeach
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
