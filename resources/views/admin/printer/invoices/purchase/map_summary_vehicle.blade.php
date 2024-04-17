
<div style="background: #eee; border: 1px solid #ccc; padding: 10px; font-weight: bold; font-size: 15px; line-height: 16px">
    {{--<h1 style="margin: 0; float: left; width: 410px;">Resumo geral de despesas</h1>--}}
    <div style="margin: 0; float: left; width: 380px;">
        <small style="font-weight: normal">Período</small><br/>
        {{ $startDate }} a {{ $endDate }}
    </div>

    <div style="float: left; width: 110px; text-align: right;">
        <small style="font-weight: normal">Faturação</small><br/>
        +{{ money(@$totals['sales']['subtotal'], Setting::get('app_currency')) }}
    </div>
    <div style="float: left; width: 110px; text-align: right;">
        <small style="font-weight: normal">Despesas</small><br/>
        -{{ money(@$totals['purchases']['subtotal'], Setting::get('app_currency')) }}
    </div>
    <div style="float: left; width: 110px; text-align: right;">
        <small style="font-weight: normal">Balanço</small><br/>
        {{ @$totals['balance'] >= 0.00 ? '+' : '' }}{{ money(@$totals['balance'], Setting::get('app_currency')) }}
    </div>
    <div style="clear: both"></div>
</div>

@if(@$data['types'])
    <?php
    $geralSubtotal = $geralVat = $geralTotal = 0;
    ?>
    <h1 style="font-size: 20px">Resumo de despesas por tipo</h1>
    <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
        <tr>
            <th>Despesa</th>
            <th class="w-70px text-right">Subtotal</th>
            <th class="w-50px text-right">IVA</th>
            <th class="w-70px text-right">Total</th>
        </tr>
        <?php
        $totalInvoices = $totalReceipts = $totalVat = $totalDiff = 0;
        ?>
        @foreach($data['types'] as $key => $row)
            <?php
            $geralSubtotal+= @$row['subtotal'];
            $geralTotal+= @$row['total'];
            $geralVat+= @$row['vat'];
            ?>
            <tr>
                <td>{{ @$row['name'] }}</td>
                <td class="text-right">{{ money(@$row['subtotal']) }}</td>
                <td class="text-right">{{ money(@$row['vat']) }}</td>
                <td class="text-right">{{ money(@$row['total']) }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="text-right" style="border: none">
                Total
            </td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($geralSubtotal) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($geralVat) }}</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($geralTotal) }}</td>
        </tr>
    </table>
@endif


@if(@$data['fleet'])
    <h1 style="font-size: 20px">Detalhe por Viatura</h1>
    @if($details)
        <?php $vehiclesTotalCost = $vehiclesTotalGain = 0; ?>
        <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0 0 5px 0">
            <tr>
                <th>Viatura</th>
                <th class="text-right bold">Serviços</th>
                <th class="text-right bold">Despesas</th>
                <th class="text-right bold">Balanço</th>
            </tr>
            @foreach(@$data['fleet'] as $vehicleId => $vehicleDetails)

                @if(!empty(@$vehicleDetails['costs']) || !empty(@$vehicleDetails['gains']))
                <?php

                $vehicleCosts = 0;
                $rowsHtml = '';

                $rowDetails = $vehicleDetails['costs'];

                if(!empty($rowDetails) && $rowDetails) {
                    foreach($rowDetails as $row) {
                        $vehicleCosts+= @$row['subtotal'];

                        $rowsHtml.="<tr>";
                        $rowsHtml.="<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;". @$row['type'] . "</td>";
                        $rowsHtml.="<td class='text-right'></td>";
                        $rowsHtml.="<td class='text-right'>-" . money(@$row['subtotal']) . "</td>";
                        $rowsHtml.="<td class='text-right'></td>";
                        $rowsHtml.="</tr>";
                    }
                }

                $vehicleName    = @$vehicleDetails['name'];
                $vehicleGain    = @$vehicleDetails['gains'];
                $vehicleBalance = $vehicleGain - $vehicleCosts;
                $vehiclesTotalGain+= $vehicleGain;
                $vehiclesTotalCost+= $vehicleCosts;
                ?>
                <tr>
                    <td style="background: #eee; font-weight: bold">{{ $vehicleId }} | {{ @$vehicleName }}</td>
                    <td style="background: #eee; {{ $vehicleGain > 0.00 ? 'font-weight: bold' : 'color: #777' }}" class="text-right w-70px">+{{ money($vehicleGain) }}</td>
                    <td style="background: #eee; {{ $vehicleCosts > 0.00 ? 'font-weight: bold' : 'color: #777' }}" class="text-right w-70px">-{{ money($vehicleCosts) }}</td>
                    <td style="background: #eee; {{ $vehicleBalance > 0.00 ? 'font-weight: bold' : 'color: #777' }}" class="text-right w-70px">{{ $vehicleBalance > 0.00 ? '+' : '' }}{{ money($vehicleBalance) }}</td>
                </tr>
                {!! $rowsHtml !!}
                @endif
            @endforeach
            <tr>
                <td style="text-align: right; border: 0px">Total</td>
                <td class="text-right bold">+{{ money($vehiclesTotalGain) }}</td>
                <td class="text-right bold">-{{ money($vehiclesTotalCost) }}</td>
                <td class="text-right bold">{{ money($vehiclesTotalGain - $vehiclesTotalCost) }}</td>
            </tr>
        </table>
    @else
        <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
            <tr>
                <th>Viatura</th>
                <th class="w-70px text-right">Serviços</th>
                <th class="w-70px text-right">Despesas</th>
                <th class="w-70px text-right">Balanço</th>
            </tr>
            <?php $rowTotalCosts = $rowTotalGains = 0; ?>
            @foreach(@$data['fleet'] as $row)
                @if($row['gains'] > 0.00 || $row['costs'] > 0.00)
                    <?php
                    $rowTotalCosts+= @$row['costs'];
                    $rowTotalGains+= @$row['gains'];
                    ?>
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td class="w-70px text-right">{{ money(@$row['gains']) }}</td>
                        <td class="w-70px text-right">{{ money(@$row['costs']) }}</td>
                        <td class="w-70px text-right">{{ money(@$row['gains'] - @$row['costs']) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="text-right" style="border: none">Total</td>
                <td class="w-70px text-right bold">{{ money($rowTotalGains) }}</td>
                <td class="w-70px text-right bold">{{ money($rowTotalCosts) }}</td>
                <td class="w-70px text-right bold">{{ money($rowTotalGains - $rowTotalCosts) }}</td>
            </tr>
        </table>
    @endif
@endif

@if(@$data['users'])
    <h1 style="font-size: 20px">Detalhe por Colaborador</h1>
    @if($details)
        <?php $usersTotalCost = $usersTotalGain = 0; ?>
        <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0 0 5px 0">
            <tr>
                <th>Operador</th>
                <th class="text-right bold">Serviços</th>
                <th class="text-right bold">Despesas</th>
                <th class="text-right bold">Balanço</th>
            </tr>
            @foreach(@$data['users'] as $userId => $userDetails)

                @if(!empty(@$userDetails['costs']) || !empty(@$userDetails['gains']))
                <?php

                $userCosts = 0;
                $rowsHtml = '';

                $rowDetails = $userDetails['costs'];

                if($details) {
                    if($rowDetails) {
                        foreach($rowDetails as $row) {
                            $userCosts+= @$row['subtotal'];

                            $rowsHtml.="<tr>";
                            $rowsHtml.="<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;". @$row['type'] . "</td>";
                            $rowsHtml.="<td class='text-right'></td>";
                            $rowsHtml.="<td class='text-right'>-" . money(@$row['subtotal']) . "</td>";
                            $rowsHtml.="<td class='text-right'></td>";
                            $rowsHtml.="</tr>";
                        }
                    } else {
                        $userCosts+= @$userDetails['subtotal'];
                        $usersTotalCost+= @$userDetails['subtotal'];
                    }
                }

                $userName    = @$userDetails['name'];
                $userGain    = @$userDetails['gains'];
                $userBalance = $userGain - $userCosts;
                $usersTotalGain+= $userGain;
                $usersTotalCost+= $userCosts;
                ?>
                <tr>
                    <td style="background: #eee; font-weight: bold">{{ @$userName }}</td>
                    <td style="background: #eee; {{ $userGain > 0.00 ? 'font-weight: bold' : 'color: #777' }}" class="text-right w-70px">+{{ money($userGain) }}</td>
                    <td style="background: #eee; {{ $userCosts > 0.00 ? 'font-weight: bold' : 'color: #777' }}" class="text-right w-70px">-{{ money($userCosts) }}</td>
                    <td style="background: #eee; {{ $userBalance > 0.00 ? 'font-weight: bold' : 'color: #777' }}" class="text-right w-70px">{{ $userBalance > 0.00 ? '+' : '' }}{{ money($userBalance) }}</td>
                </tr>
                {!! $rowsHtml !!}

                @endif
            @endforeach
            <tr>
                <td style="text-align: right; border: 0px">Total</td>
                <td class="text-right bold">+{{ money($usersTotalGain) }}</td>
                <td class="text-right bold">-{{ money($usersTotalCost) }}</td>
                <td class="text-right bold">{{ money($usersTotalGain - $usersTotalCost) }}</td>
            </tr>
        </table>
    @else
        <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
            <tr>
                <th>Operador</th>
                <th class="text-right bold">Serviços</th>
                <th class="text-right bold">Despesas</th>
                <th class="text-right bold">Balanço</th>
            </tr>
            <?php $rowTotalCosts = $rowTotalGains = 0; ?>
            @foreach(@$data['users'] as $row)
                @if($row['gains'] > 0.00 || $row['costs'] > 0.00)
                    <?php
                    $rowTotalCosts+= @$row['costs'];
                    $rowTotalGains+= @$row['gains'];
                    ?>
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td class="w-70px text-right">{{ money(@$row['gains']) }}</td>
                        <td class="w-70px text-right">{{ money(@$row['costs']) }}</td>
                        <td class="w-70px text-right">{{ money(@$row['gains'] - @$row['costs']) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td class="text-right" style="border: none">Total</td>
                <td class="w-70px text-right bold">{{ money($rowTotalGains) }}</td>
                <td class="w-70px text-right bold">{{ money($rowTotalCosts) }}</td>
                <td class="w-70px text-right bold">{{ money($rowTotalGains - $rowTotalCosts) }}</td>
            </tr>
        </table>
    @endif
@endif