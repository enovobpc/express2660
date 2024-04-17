@if($page == 'geral_conditions')
    <div style="height: 250mm;">
        <div style="margin: 30px 0 5px; border-top: 1px solid #222; border-bottom: 1px solid #222; font-size: 14px; font-weight: bold; color: #55c158">
            {{ trans('admin/budgets.'.$budget->type.'.section_geral_conditions', [], 'messages', $budget->locale) }}
        </div>
        <div style="font-size: 11px">
            {!! str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $budget->geral_conditions)) !!}
        </div>
    </div>
@else
<div style="height: 290mm;">
    <p style="float: right; text-align: right; font-size: 11px; line-height: 12px; margin-top: -30px">
        {{ $budget->name }}<br/>
        @if($budget->address)
            {{ $budget->address }}<br/>
            {{ $budget->zip_code }} {{ $budget->city }}
        @endif
        {{ trans('admin/budgets.'.$budget->type.'.email', [], 'messages', $budget->locale) }}: {{ $budget->email }}<br/>
        {{ trans('admin/budgets.'.$budget->type.'.phone', [], 'messages', $budget->locale) }}: {{ $budget->phone }}
    </p>
    <h2 style="float: right; text-align: center; width: 290px; font-size: 15px; font-weight: bold; background: #55c158; color: #fff; padding: 5px; margin: 0">
        {{ trans('admin/budgets.'.$budget->type.'.value', [], 'messages', $budget->locale) }}: {{ money($budget->total + $budget->total_vat) }}
    </h2>
    <div style="clear: both"></div>

    <div style="font-size: 11px; margin-bottom: 5px">
        <br/><br/><br/>
        {{ trans('admin/budgets.'.$budget->type.'.dear', [], 'messages', $budget->locale) }} {{ $budget->name }},
    </div>
    <div style="font-size: 11px">
        {!! str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $budget->intro)) !!}
    </div>

    <div style="margin: 20px 0 5px; border-top: 1px solid #222; border-bottom: 1px solid #222; font-size: 14px; font-weight: bold; color: #55c158">
        {{ trans('admin/budgets.'.$budget->type.'.section_animals', [], 'messages', $budget->locale) }}
    </div>

    @if($budget->type == 'animals')
        <table style="font-size: 11px; width: 100%;" cellspacing="10">
            <tr>
                <th style="width: 80px; padding: 2px">{{ trans('admin/budgets.'.$budget->type.'.specie', [], 'messages', $budget->locale) }}</th>
                <th>{{ trans('admin/budgets.'.$budget->type.'.name', [], 'messages', $budget->locale) }}</th>
                <th style="text-align: right;">{{ trans('admin/budgets.'.$budget->type.'.age', [], 'messages', $budget->locale) }}</th>
                <th style="width: 90px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.weight', [], 'messages', $budget->locale) }}</th>
                <th style="width: 100px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.weight_box', [], 'messages', $budget->locale) }}</th>
            </tr>
            @if($budget->animals)
            <?php
                $totalWeight = 0;
            ?>
            @foreach($budget->animals as $animal)
                <?php
                    $animal = (array) $animal;

                    $animal['weight']     = empty($animal['weight']) ? 0 : forceDecimal($animal['weight']);
                    $animal['weight_box'] = empty($animal['weight_box']) ? 0 : forceDecimal($animal['weight_box']);

                    $totalWeight+= $animal['weight'] + $animal['weight_box'];
                ?>
                <tr>
                    <td style="padding: 2px">
                        @if($animal['type'] == 'dog')
                            {{ trans('admin/budgets.'.$budget->type.'.dog', [], 'messages', $budget->locale) }}
                        @elseif($animal['type'] == 'cat')
                            {{ trans('admin/budgets.'.$budget->type.'.cat', [], 'messages', $budget->locale) }}
                        @else
                            {{ trans('admin/budgets.'.$budget->type.'.other', [], 'messages', $budget->locale) }}
                        @endif
                    </td>
                    <td>{{ $animal['name'] ? $animal['name'] : 'N/A' }} / {{ $animal['specie'] }}</td>
                    <td style="text-align: right;">{{ $animal['age'] }}</td>
                    <td style="text-align: right">{{ money($animal['weight'], 'kg') }}</td>
                    <td style="text-align: right">{{ money($animal['weight_box'], 'kg') }}</td>
                </tr>
            @endforeach
            @endif
        </table>
    @else
        <table style="font-size: 11px; width: 100%;" cellspacing="10">
            <tr>
                <th>{{ trans('admin/budgets.'.$budget->type.'.description', [], 'messages', $budget->locale) }}</th>
                <th style="width: 100px; padding: 2px">{{ trans('admin/budgets.'.$budget->type.'.service', [], 'messages', $budget->locale) }}</th>
                <th style="width: 90px; text-align: right;">{{ trans('admin/budgets.'.$budget->type.'.volumes', [], 'messages', $budget->locale) }}</th>
                <th style="width: 90px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.weight', [], 'messages', $budget->locale) }}</th>
                <th style="width: 100px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.volumetric_weight', [], 'messages', $budget->locale) }}</th>
                <th style="width: 150px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.dimensions', [], 'messages', $budget->locale) }}</th>
            </tr>
            @if($budget->goods)
                <?php
                $totalWeight = 0;
                ?>
                @foreach($budget->goods as $good)
                    <?php
                        $good = (array) $good;

                    $good['weight']     = empty($good['weight']) ? 0 : $good['weight'];
                    $good['weight_box'] = empty($good['weight_box']) ? 0 : $good['weight_box'];

                    $totalWeight+= $good['weight'] + $good['weight_box'];
                    ?>
                    <tr>
                        <td>{{ $good['description'] }}</td>
                        <td style="padding: 2px">{{ @$courierServices[@$good['service']] }}</td>
                        <td style="text-align: right;">{{ $good['volumes'] }}</td>
                        <td style="text-align: right">{{ money($good['weight'], 'kg') }}</td>
                        <td style="text-align: right">{{ money($good['volumetric_weight'], 'kg') }}</td>
                        <td style="text-align: right">{{ $good['dimension'] }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endif

    <div style="width: 160px; border-top: 1px solid #222; text-align: right; font-size: 11px; font-weight: bold; float: right; margin-top: 5px">
        <b>{{ trans('admin/budgets.'.$budget->type.'.total_weight', [], 'messages', $budget->locale) }} &nbsp;&nbsp;&nbsp;{{ money(@$totalWeight, 'kg') }}</b>
    </div>
    <br/>
    @if(!empty($budget->pickup_address))
        <p style="font-size: 11px;">

            <b style="font-weight: bold">{{ trans('admin/budgets.'.$budget->type.'.pickup_address', [], 'messages', $budget->locale) }}:</b> {{ $budget->pickup_address }}
        </p>
    @endif

    @if(!empty($budget->delivery_address))
        <p style="font-size: 11px;">
            <b style="font-weight: bold">{{ trans('admin/budgets.'.$budget->type.'.delivery_address', [], 'messages', $budget->locale) }}:</b> {{ $budget->delivery_address }}
        </p>
    @endif

    <table style="font-size: 11px; width: 100%;" cellpadding="10">
        <tr>
            <th style="width: 140px; padding: 2px">{{ trans('admin/budgets.'.$budget->type.'.source_airport', [], 'messages', $budget->locale) }}</th>
            <th style="width: 140px">{{ trans('admin/budgets.'.$budget->type.'.destination_airport', [], 'messages', $budget->locale) }}</th>
            <th>{{ trans('admin/budgets.'.$budget->type.'.notes', [], 'messages', $budget->locale) }}</th>
        </tr>
        @if($budget->airports)
        @foreach($budget->airports as $airport)
            <?php $airport = (array) $airport; ?>
            <tr>
                <td style="padding: 2px; vertical-align: top">{{ $airport['source'] }}</td>
                <td style="vertical-align: top">{{ $airport['destination'] }}</td>
                <td>{!! nl2br($airport['obs']) !!}</td>
            </tr>
        @endforeach
        @endif
    </table>

    <div style="margin: 50px 0 5px; border-top: 1px solid #222; border-bottom: 1px solid #222; font-size: 14px; font-weight: bold; color: #55c158">
        {{ trans('admin/budgets.'.$budget->type.'.section_services', [], 'messages', $budget->locale) }}
    </div>
    <table style="font-size: 11px; width: 100%;" cellpadding="10">
        <tr>
            <th style="padding: 2px">{{ trans('admin/budgets.'.$budget->type.'.item', [], 'messages', $budget->locale) }}</th>
            <th style="width: 80px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.qty', [], 'messages', $budget->locale) }}</th>
            <th style="width: 80px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.price', [], 'messages', $budget->locale) }}</th>
            <th style="width: 80px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.subtotal', [], 'messages', $budget->locale) }}</th>
            <th style="width: 80px; text-align: right">{{ trans('admin/budgets.'.$budget->type.'.vat', [], 'messages', $budget->locale) }} (%)</th>
        </tr>
        @if($budget->services)
        @foreach($budget->services as $service)
            <?php $service = (array) $service; ?>
            <tr>
                <td style="padding: 2px">{{ @$services[$service['service_id']] }}</td>
                <td style="text-align: right">{{ $service['qt'] }}</td>
                <td style="text-align: right">{{ money($service['price']) }}</td>
                <td style="text-align: right">{{ money($service['subtotal'])}}</td>
                <td style="text-align: right">{{ money($service['vat']) }}</td>
            </tr>
        @endforeach
        @endif
    </table>
    <h2 style="float: right; text-align: center; width: 290px; font-size: 15px; font-weight: bold; background: #55c158; color: #fff; padding: 5px; margin: 0; margin-top: 30px;">
        {{ trans('admin/budgets.'.$budget->type.'.value', [], 'messages', $budget->locale) }}: {{ money($budget->total + $budget->total_vat) }}
    </h2>
    <div style="clear: both">

    </div>
    <div style="width: 290px;float: left; ">
    </div>
    <div style="width: 290px;float: right; ">
        <table style="width: 100%">
            <tr>
                <td style="padding: 5px; border-bottom: 1px solid #222">{{ trans('admin/budgets.'.$budget->type.'.total_net', [], 'messages', $budget->locale) }}</td>
                <td style="padding: 5px; border-bottom: 1px solid #222; text-align: right">{{ money($budget->total) }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; border-bottom: 1px solid #222">{{ trans('admin/budgets.'.$budget->type.'.vat', [], 'messages', $budget->locale) }}</td>
                <td style="padding: 5px; border-bottom: 1px solid #222; text-align: right">{{ money($budget->total_vat) }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; font-weight: bold; font-size: 13px;">{{ trans('admin/budgets.'.$budget->type.'.total', [], 'messages', $budget->locale) }}</td>
                <td style="padding: 5px; font-weight: bold; font-size: 13px; text-align: right">{{ money($budget->total + $budget->total_vat) }}</td>
            </tr>
        </table>
    </div>
</div>
<div style="height: 290mm;">
    {{--<h4 style="float: right; text-align: right; font-weight: bold; font-size: 16px; margin-top: -120px">{{ trans('admin/budgets.'.$budget->type.'.title', [], 'messages', $budget->locale) }}</h4>
    <p style="float: right; text-align: right; font-size: 12px; font-weight: bold; line-height: 15px">
        {{ trans('admin/budgets.'.$budget->type.'.date', [], 'messages', $budget->locale) }}: {{ $budgetDate->format('d/m/Y') }}<br/>
        {{ trans('admin/budgets.'.$budget->type.'.validity', [], 'messages', $budget->locale) }}: {{ $validityDate->format('d/m/Y') }}<br/>
        {{ trans('admin/budgets.'.$budget->type.'.budget_no', [], 'messages', $budget->locale) }}: {{ $budget->budget_no }}
    </p>

    <p style="float: right; text-align: right; font-size: 11px; line-height: 12px">
        {{ $budget->name }}<br/>
        @if($budget->address)
            {{ $budget->address }}<br/>
            {{ $budget->zip_code }} {{ $budget->city }}
        @endif
        {{ trans('admin/budgets.'.$budget->type.'.email', [], 'messages', $budget->locale) }}: {{ $budget->email }}<br/>
        {{ trans('admin/budgets.'.$budget->type.'.phone', [], 'messages', $budget->locale) }}: {{ $budget->phone }}
    </p>--}}

    @if($budget->transport_info)
    <div style="margin: 30px 0 5px; border-top: 1px solid #222; border-bottom: 1px solid #222; font-size: 14px; font-weight: bold; color: #55c158">
        {{ trans('admin/budgets.'.$budget->type.'.section_transport_info', [], 'messages', $budget->locale) }}
    </div>
    <div style="font-size: 11px">
        {!! str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $budget->transport_info)) !!}
    </div>
    <div style="clear: both"></div>
    @endif

    @if($budget->payment_conditions)
    <div style="margin: 30px 0 5px; border-top: 1px solid #222; border-bottom: 1px solid #222; font-size: 14px; font-weight: bold; color: #55c158">
        {{ trans('admin/budgets.'.$budget->type.'.section_payment_info', [], 'messages', $budget->locale) }}
    </div>
    <div style="font-size: 11px">
        {!! str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $budget->payment_conditions)) !!}
    </div>
    @endif

    @if(!$budget->geral_conditions_separated && $budget->geral_conditions)
    <div style="margin: 30px 0 5px; border-top: 1px solid #222; border-bottom: 1px solid #222; font-size: 14px; font-weight: bold; color: #55c158">
        {{ trans('admin/budgets.'.$budget->type.'.section_geral_conditions', [], 'messages', $budget->locale) }}
    </div>
    <div style="font-size: 11px">
        {!! str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $budget->geral_conditions)) !!}
    </div>
    @endif
</div>
@endif