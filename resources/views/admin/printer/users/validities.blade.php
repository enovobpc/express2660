<div>
    <h4 class="bold" style="margin-top: 50px">Cartões e Certificados</h4>
    @if($cards)
    <table class="table table-bordered table-pdf m-b-5">
        <tr>
            <th>Colaborador</th>
            <th>Documento/Certificado</th>
            <th class="w-100px">Nº Documento</th>
            <th class="w-65px">Validade</th>
            <th class="w-120px">Obs.</th>
        </tr>
        @foreach($cards as $card)
            <tr>
                <td>{{ @$card->user->name }}</td>
                <td>{{ $card->name }}</td>
                <td>{{ $card->card_no }}</td>
                <td class="text-center">{{ $card->validity_date ? $card->validity_date->format('Y-m-d') : '' }}</td>
                <td>{{ $card->obs }}</td>
            </tr>
        @endforeach
    </table>
    <div class="clearfix"></div>
    @endif

    @if($contracts)
        <hr/>
        <h4 class="bold m-t-0">Contratos de trabalho</h4>
        <table class="table table-bordered table-pdf m-b-5">
            <tr>
                <th>Colaborador</th>
                <th>Tipo Contrato</th>
                <th class="w-65px">Início</th>
                <th class="w-65px">Termo</th>
                <th class="w-120px">Obs.</th>
            </tr>
            @foreach($contracts as $contract)
                <tr>
                    <td>{{ @$contract->user->name }}</td>
                    <td>{{ trans('admin/users.contract-types.' . $contract->contract_type) }}</td>
                    <td class="text-center">{{ $contract->start_date->format('Y-m-d') }}</td>
                    <td class="text-center">{{ $contract->end_date->format('Y-m-d') }}</td>
                    <td>{{ $contract->obs }}</td>
                </tr>
            @endforeach
        </table>
        <div class="clearfix"></div>
    @endif
</div>