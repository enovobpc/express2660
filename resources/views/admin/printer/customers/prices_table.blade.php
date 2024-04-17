@if($cover && $page == 1)
    <div style="width: 21cm; height: 29.7cm; background: yellow; border-left: 40px solid {{ env('APP_MAIL_COLOR_PRIMARY') }}">
        <div style="width: 100%; height: 21.7cm; padding-left: 80px; background: #fff">
            <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="margin-top: 200px; margin-bottom: 20px; height: 60px"/>
            <h1 style="font-size: 60px; text-transform: uppercase">{{ $title }}</h1>
            <h1 style="font-size: 30px">{{ $subtitle }}</h1>
            <h4>{{ $date }}</h4>
        </div>
        <div style=" background: {{ env('APP_MAIL_COLOR_PRIMARY') }}; color: #fff; width: 100%; height: 8cm">
            <div style="height: 6.5cm">
                <div style="width: 39%; float: left">
                    <br/>
                    <br/>
                    <p style="font-size: 13px; text-align: left;">
                        <b style="font-weight: bold">{{ @$customer->agency->company }}</b><br/>
                        NIF {{ @$customer->agency->vat }}<br/>
                        {{ @$customer->agency->address }}<br/>
                        {{ @$customer->agency->zip_code }} {{ @$customer->agency->city }}
                        <br/>
                        Tlf.: {{ @$customer->agency->phone }}<br/>
                        E-mail: {{ @$customer->agency->email }}<br/>
                        <span style="font-weight: bold">{{ @$customer->agency->website }}</span>
                        @if(@$sellerName)
                            Comercial: {{ $sellerName }}
                        @endif
                    </p>
                    <br/>
                </div>
                <div style="width: 56%; float: left;  border-left: 1px solid #fff; padding-left: 20px; margin-top: 30px">
                    <h4 style="color: #fff; font-size: 12px; margin-top: 0">Preparada para</h4>
                    <p style="font-size: 16px; text-align: left;">
                        <b style="font-weight: bold">{{ $customer->billing_name }}</b><br/>
                        NIF {{ $customer->vat }}<br/>
                        {{ $customer->billing_address }}<br/>
                        {{ $customer->billing_zip_code }} {{ $customer->billing_city }}
                    </p>
                    @if(1)
                        <h4 style="color: #fff; font-size: 14px">Válida durante 30 dias</h4>
                    @endif
                </div>
            </div>
            <div style="width: 100%; float: left;">
                <p>
                    <span style="font-weight: bold">Esta proposta é confidencial.</span>
                    As informações contidas em todas as partes deste documento não podem
                    ser usadas ou divulgadas sem prévia autorização de <br/> {{ @$customer->agency->company }} para propósitos que não
                    sejam os de avaliação da proposta. A divulgação não autorizada incorre em ação penal.
                </p>
            </div>
        </div>
    </div>
@endif


@if($page == 2)
    <div style="height: 26cm; margin-left: 0.5cm; font-size: 15px; font-family: Arial">
        {!! $presentation !!}
    </div>
@endif

@if($page == 3)
    <br/>
    <div class="customers-prices-tables">
        @foreach($servicesGroups as $serviceGroup)
            @if(@$pricesTableData[$serviceGroup->code] && !$pricesTableData[$serviceGroup->code]->isEmpty())
                <?php
                $groupCode  = $serviceGroup->code;
                $groupName  = $serviceGroup->name;
                $unity      = $groupCode;
                $fullServiceName = true;
                ?>
                @include('admin.printer.customers.table')
            @endif
        @endforeach
    </div>
    @if(!$complementarServices->isEmpty() || $request->get('fuel_tax')  || $request->get('insurance_tax'))
    <h5 class="bold text-uppercase m-t-0 m-b-3">
        Serviços Complementares
    </h5>
    <div>
        <?php $customerComplementarServices = $customer->complementar_services;?>

        @foreach($complementarServices as $service)
            <div style="float: left; width: 25%">
                <div class="form-group">
                    <div class="bold" style="font-size: 13px; color: {{ env('APP_MAIL_COLOR_PRIMARY') }}">
                        {{ $service->name }}
                    </div>
                    @if($service->zones_arr)
                        @foreach($service->zones_arr as $key => $zone)
                            <?php $unity = $service->unity_arr[$key] == 'percent' ? '%' : Setting::get('app_currency'); ?>
                                <span style="font-size: 12px" class="text-uppercase">
                                    <?php
                                        $uid = @$service->uid_arr[$key] ? $service->uid_arr[$key] : $service->id.'#'.$zone;
                                        $customExpense = @$customerComplementarServices[$service->id]['price'][$uid];
                                    ?>
                                    
                                    @if($customExpense)
                                        {{ $zone != 'qqz' ? $zone. ' -' : '' }} {{ money($customExpense, $unity) }}
                                    @else
                                        {{ $zone != 'qqz' ? $zone. ' -' : '' }} {{ money($service->values_arr[$key], $unity) }}
                                    @endif
                                </span>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach

        @if(Setting::get('fuel_tax') && $request->get('fuel_tax'))
            <div style="float: left; width: 25%">
                <div class="form-group">
                    <div class="bold" style="font-size: 13px; color: {{ env('APP_MAIL_COLOR_PRIMARY') }}">
                       Taxa Combustivel
                    </div>
                    
                    <span style="font-size: 12px" class="text-uppercase">
                        @if(empty($customer->fuel_tax))
                            {{ money(Setting::get('fuel_tax'),'%') }}
                        @else
                            {{ money($customer->fuel_tax, '%') }}
                        @endif
                     <br/>
                    </span>
                </div>
            </div>
            @endif
            @if(Setting::get('insurance_tax') && $request->get('insurance_tax'))
            <div style="float: left; width: 25%">
                <div class="form-group">
                    <div class="bold" style="font-size: 13px; color: {{ env('APP_MAIL_COLOR_PRIMARY') }}">
                       Taxa Seguro
                    </div>
                    
                    <span style="font-size: 12px" class="text-uppercase">
                        @if(empty($customer->insurance_tax))
                            {{ money(Setting::get('insurance_tax'),'%') }}
                        @else
                            {{ money($customer->insurance_tax, '%') }}
                        @endif
                     <br/>
                    </span>
                </div>
            </div>
            @endif
    </div>
    @endif
@endif

@if($page == '4')
    <?php
    $conditions = Setting::get('prices_table_general_conditions');
    $conditions = str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $conditions))
    ?>
    {!! $conditions !!}
@endif