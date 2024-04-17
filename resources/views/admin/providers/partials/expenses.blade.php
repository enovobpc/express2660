<div class="box no-border">
    <div class="box-body">
        {{ Form::model($provider, ['route' => ['admin.providers.expenses.update', $provider->id], 'method' => 'PUT']) }}
        <div class="row row-0">
            <div class="col-sm-9">
                <button class="btn btn-sm btn-primary m-b-10" data-loading-text="A gravar..." type="submit">
                    <i class="fas fa-save"></i> @trans('Gravar')
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

        <div class="row row-5">
            <div class="col-xs-12">
                @if ($shippingExpenses)
                    <table class="table table-condensed table-expenses">
                        <tr>
                            <th class="bg-gray w-60px">@trans('Código')</th>
                            <th class="bg-gray">@trans('Taxa')</th>
                            <th class="bg-gray">@trans('Tipo taxa')</th>
                            <th class="bg-gray">@trans('Serviço')</th>
                            <th class="bg-gray">@trans('Zona')</th>
                            <th class="bg-gray w-85px text-right">@trans('Custo Min.')</th>
                            <th class="bg-gray w-85px text-right">@trans('Custo Max.')</th>
                            <th class="bg-gray w-70px text-right">@trans('Taxa IVA')</th>
                            <th class="bg-gray w-100px">@trans('Custo Forn.')</th>
                        </tr>

                        <?php $lastExpense = null; ?>
                        @foreach ($shippingExpenses as $expense)
                            @if ($expense->zones_arr)
                                @foreach ($expense->zones_arr as $key => $zone)
                                    <?php
                                    $expenseId = empty($expense->service_arr[$key]) ? 'qq' : $expense->service_arr[$key];
                                    $unity = $expense->unity_arr[$key] == 'percent' ? '%' : Setting::get('app_currency');
                                    $uid = @$expense->uid_arr[$key] ? $expense->uid_arr[$key] : $expenseId . '#' . $zone;
                                    $customValue = is_numeric(@$provider->custom_expenses[$expense->id]['price'][$uid]) ? number(@$provider->custom_expenses[$expense->id]['price'][$uid]) : '';
                                    $customMinValue = is_numeric(@$provider->custom_expenses[$expense->id]['min_price'][$uid]) ? number(@$provider->custom_expenses[$expense->id]['min_price'][$uid]) : '';
                                    $customMaxValue = is_numeric(@$provider->custom_expenses[$expense->id]['max_price'][$uid]) ? number(@$provider->custom_expenses[$expense->id]['max_price'][$uid]) : '';
                                    
                                    ?>
                                    <tr class="{{ $lastExpense && $lastExpense != $expense->id ? 'brd-black' : '' }}" data-search="{{ $expense->code }}#{{ $expense->name }}">
                                        @if (!$key)
                                            <td style="vertical-align: top !important;" rowspan="{{ count($expense->zones_arr) }}">{{ $expense->code }}</td>
                                            <td style="vertical-align: top !important;" rowspan="{{ count($expense->zones_arr) }}">{{ $expense->name }}</td>
                                            <td style="vertical-align: top !important;" rowspan="{{ count($expense->zones_arr) }}">{{ trans('admin/expenses.types.' . $expense->type) }}</td>
                                        @endif
                                        <td>{{ @$expense->services_arr[$key] == 'qq' ? 'Qualquer' : @$servicesList[@$expense->services_arr[$key]] }}</td>
                                        <td>{{ @$expense->zones_arr[$key] == 'qqz' ? 'Qualquer' : @$billingZonesList[@$expense->zones_arr[$key]] }}</td>
                                        <td class="text-right">
                                            <div class="input-group input-group-xs input-group-money">
                                                {{ Form::text('custom_expenses[' . $expense->id . '][min_price][' . $uid . ']', $customMinValue, ['class' => 'form-control decimal text-right', 'maxlength' => 5]) }}
                                                <div class="input-group-addon">
                                                    {{ Setting::get('app_currency') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="input-group input-group-xs input-group-money">
                                                {{ Form::text('custom_expenses[' . $expense->id . '][max_price][' . $uid . ']', $customMaxValue, ['class' => 'form-control decimal text-right', 'maxlength' => 5]) }}
                                                <div class="input-group-addon">
                                                    {{ Setting::get('app_currency') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right" style="{{ @$expense->vat_rate_arr[$key] ? '' : 'opacity: 0.3' }}">
                                            {{ @$expense->vat_rate_arr[$key] ? @$vatRates[@$expense->vat_rate_arr[$key]] : 'Auto' }}
                                        </td>
                                        <td>
                                            <div class="input-group input-group-xs input-group-money">
                                                {{ Form::text('custom_expenses[' . $expense->id . '][price][' . $uid . ']', $customValue, ['class' => 'form-control decimalneg text-right', 'maxlength' => 6]) }}
                                                <div class="input-group-addon">
                                                    {{ $unity }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $lastExpense = $expense->id; ?>
                                @endforeach
                            @endif
                        @endforeach
                    </table>
                @endif
            </div>
        </div>

        <button class="btn btn-sm btn-primary" data-loading-text="A gravar..." type="submit">
            <i class="fas fa-save"></i> @trans('Gravar')
        </button>
        {{ Form::close() }}
    </div>
</div>

<style>
    .brd-black {
        border-top: 2px solid #333;
    }

    .table-expenses td {
        padding: 3px 5px !important;
        vertical-align: middle !important;
    }

    .table-expenses .input-group-xs input {
        height: 27px;
    }
</style>

<script>
    window.addEventListener('load', function() {
        $('input[name="prices-expenses-search"]').on('keyup', function() {
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
