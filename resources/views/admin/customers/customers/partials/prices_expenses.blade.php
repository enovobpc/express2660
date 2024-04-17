@if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
{{ Form::model($customer, ['route' => ['admin.customers.update', $customer->id], 'method' => 'PUT']) }}
<div class="row row-0">
    <div class="col-sm-9">
        <button type="submit" class="btn btn-sm btn-primary m-b-10" data-loading-text="A gravar...">
            <i class="fas fa-save"></i> @trans('Gravar Taxas Adicionais')
        </button>
    </div>

    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fas fa-search"></i>
            </div>
            <input class="form-control input-sm" name="prices-expenses-search" type="search">
        </div>
    </div>
</div>
@endif

<div class="row row-5">
    <div class="col-xs-12">
    @if($complementarServices)
        <table class="table table-condensed table-expenses">
            <tr>
                <th class="bg-gray w-60px">@trans('Código')</th>
                <th class="bg-gray">@trans('Taxa')</th>
                <th class="bg-gray">@trans('Tipo taxa')</th>
                <th class="bg-gray">@trans('Serviço')</th>
                <th class="bg-gray">@trans('Zona')</th>
                <th class="bg-gray w-85px text-right">@trans('Preço Min')</th>
                <th class="bg-gray w-85px text-right">@trans('Preço Max')</th>
                <th class="bg-gray w-70px text-right">@trans('Taxa IVA')</th>
                <th class="bg-gray w-90px text-right p-r-10">@trans('Preço PVP')</th>
                <th class="bg-gray w-100px">@trans('Preço Cliente')</th>
            </tr>

            <?php $lastService = null ?>
            @foreach($complementarServices as $service)
                @if($service->zones_arr)
                    @foreach($service->zones_arr as $key => $zone)
                        <?php
                        $serviceId = empty($service->service_arr[$key]) ? 'qq' : $service->service_arr[$key];
                        $unity   = $service->unity_arr[$key] == 'percent' ? '%' : Setting::get('app_currency');
                        $uid     = @$service->uid_arr[$key] ? $service->uid_arr[$key] : $serviceId.'#'.$zone;
                        $customValue    = is_numeric(@$customer->custom_expenses[$service->id]['price'][$uid])     ? number(@$customer->custom_expenses[$service->id]['price'][$uid]) : '';
                        $customMinValue = is_numeric(@$customer->custom_expenses[$service->id]['min_price'][$uid]) ? number(@$customer->custom_expenses[$service->id]['min_price'][$uid]) : '';
                        $customMaxValue = is_numeric(@$customer->custom_expenses[$service->id]['max_price'][$uid]) ? number(@$customer->custom_expenses[$service->id]['max_price'][$uid]) : '';

                        ?>
                            <tr class="{{ $lastService && $lastService != $service->id ? 'brd-black' : ''}}" data-search="{{ $service->code }}#{{ $service->name }}">
                                @if(!$key)
                                <td rowspan="{{ count($service->zones_arr) }}" style="vertical-align: top !important;">{{ $service->code }}</td>
                                <td rowspan="{{ count($service->zones_arr) }}" style="vertical-align: top !important;">{{ $service->name }}</td>
                                <td rowspan="{{ count($service->zones_arr) }}" style="vertical-align: top !important;">{{ trans('admin/expenses.types.'.$service->type) }}</td>
                                @endif
                                <td>{{ @$service->services_arr[$key] == 'qq' ? 'Qualquer' : @$servicesList[@$service->services_arr[$key]] }}</td>
                                <td>{{ @$service->zones_arr[$key] == 'qqz' ? 'Qualquer' : @$billingZonesList[@$service->zones_arr[$key]] }}</td>
                                <td class="text-right" style="{{ @$service->min_price_arr[$key] > 0.00 ? '' : 'opacity: 0.3' }}">
                                    @if(@$service->min_price_arr[$key] )
                                        <div class="input-group input-group-xs input-group-money">
                                            {{ Form::text('custom_expenses['.$service->id.'][min_price]['.$uid.']', $customMinValue, ['class' => 'form-control decimal text-right', 'maxlength' => 5, 'placeholder' => number($service->min_price_arr[$key])]) }}
                                            <div class="input-group-addon">
                                                {{ Setting::get('app_currency') }}
                                            </div>
                                        </div>
                                    @else
                                        @trans('N/A')
                                    @endif
                                </td>
                                <td class="text-right" style="{{ @$service->max_price_arr[$key] > 0.00 ? '' : 'opacity: 0.3' }}">
                                    @if(@$service->max_price_arr[$key] )
                                    <div class="input-group input-group-xs input-group-money">
                                        {{ Form::text('custom_expenses['.$service->id.'][max_price]['.$uid.']', $customMaxValue, ['class' => 'form-control decimal text-right', 'maxlength' => 5, 'placeholder' => number($service->max_price_arr[$key])]) }}
                                        <div class="input-group-addon">
                                            {{ Setting::get('app_currency') }}
                                        </div>
                                    </div>
                                    @else
                                        @trans(' N/A')
                                    @endif
                                </td>
                                <td class="text-right" style="{{ @$service->vat_rate_arr[$key] ? '' : 'opacity: 0.3' }}">{{ @$service->vat_rate_arr[$key] ? @$vatRates[@$service->vat_rate_arr[$key]] : 'Auto' }}</td>
                                <td class="text-right {{ @$service->values_arr[$key] > 0.00 ? 'text-blue' : 'text-red' }}" style="font-weight: bold">{{ money(@$service->values_arr[$key], $unity) }}</td>
                                <td>
                                    <div class="input-group input-group-xs input-group-money">
                                        {{ Form::text('custom_expenses['.$service->id.'][price]['.$uid.']', $customValue, ['class' => 'form-control decimalneg text-right', 'maxlength' => 6]) }}
                                        <div class="input-group-addon">
                                            {{ $unity }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php $lastService = $service->id; ?>
                    @endforeach

                @endif
            @endforeach
        </table>
    @endif
    </div>
</div>

@if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
{{ Form::hidden('seller_id') }}
<button type="submit" class="btn btn-sm btn-primary" data-loading-text="A gravar...">
    <i class="fas fa-save"></i> @trans('Gravar Taxas Adicionais')
</button>
{{ Form::close() }}
@endif

<script>
    window.addEventListener('load', function () {
        $('input[name="prices-expenses-search"]').on('keyup', function () {
            var $this = $(this);
            var value = $this.val().trim().toLowerCase();

            $('tr[data-search]').show();
            if (!value) {
                return;
            }

            var $trs = $('tr[data-search]');
            $trs.each(function() {
                if (!$(this).data('search').toLowerCase().includes(value)) {
                    $(this).hide();
                }
            });
        });
    });
</script>