<div class="modal" id="modal-shipment-expenses">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Encargos e Taxas Adicionais</h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed m-b-0 table-expenses">
                    <tr>
                        <th class="bg-gray">Taxa</th>
                        <th class="bg-gray w-55px">Qtd</th>
                        <th class="bg-gray w-85px">Preço</th>
                        <th class="bg-gray w-80px">Subtotal</th>
                        <th class="bg-gray w-70px" style="border-right: 2px solid #333">IVA</th>
                        <th class="bg-gray w-100px expense-provider-detail" style="display:none">Fornecedor</th>
                        <th class="bg-gray w-70px">Custo ({{ $appCurrency }})</th>
                        <th class="bg-gray w-70px">Subtotal</th>
                        <th class="bg-gray w-70px expense-provider-detail" style="display:none">IVA</th>
                        <th class="bg-gray w-1"></th>
                        <th class="bg-gray w-1"></th>
                    </tr>
                    @if($shipment->exists && !$shipment->expenses->isEmpty())
                        <?php
                        $expenses = $shipment->expenses->toArray();
                        $rowsVisible = count($expenses);
                        $rowsVisible = $rowsVisible > 6 ? $rowsVisible + 1 : 6;
                        ?>
                        @for($i = 0 ; $i < $rowsVisible ; $i++)
                        <tr class="row-expenses" data-auto="{{ @$expenses[$i]['pivot']['auto'] }}" style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
                            <td class="input-sm">
                                {!! Form::selectWithData('expense_id[]', $allExpenses, @$expenses[$i]['pivot']['expense_id'], ['class' => 'form-control w-100 select2 xpto-'.$i]) !!}
                                {{ Form::hidden('expense_auto[]',  @$expenses[$i]['pivot']['auto']) }}
                                {{ Form::hidden('expense_billing_item[]',  @$expenses[$i]['billing_item_id']) }}
                            </td>
                            <td>
                                {{ Form::text('expense_qty[]', !is_null(@$expenses[$i]['pivot']['qty']) ? @$expenses[$i]['pivot']['qty'] : 1, ['class' => 'form-control input-sm number m-0 text-center']) }}
                            </td>
                            <td>
                                <div class="input-group input-group-money">
                                    {{ Form::text('expense_price[]', @$expenses[$i]['pivot']['price'], ['class' => 'form-control input-sm decimalneg m-0 text-right']) }}
                                    <div class="input-group-addon">
                                        @if(@$expenses[$i]['pivot']['unity'] == 'percent')
                                            %
                                        @else
                                        {{ $appCurrency }}
                                        @endif
                                    </div>
                                </div>
                                {{ Form::hidden('expense_unity[]', @$expenses[$i]['pivot']['unity']) }}
                            </td>
                            <td>
                                {{ Form::text('expense_subtotal[]', @$expenses[$i]['pivot']['subtotal'], ['class' => 'form-control input-sm decimal m-0 text-right bold text-blue', 'readonly']) }}
                                {{ Form::hidden('expense_vat[]', @$expenses[$i]['pivot']['vat']) }}
                                {{ Form::hidden('expense_total[]', @$expenses[$i]['pivot']['subtotal']) }}
                            </td>
                            <td class="input-sm" style="border-right: 2px solid #333">
                                {{ Form::select('expense_vat_rate_id[]', ['' => 'Auto'] + $vatTaxes, @$expenses[$i]['pivot']['vat_rate_id'], ['class' => 'form-control input-sm m-0 select2']) }}
                            </td>
                            <td class="expense-provider-detail input-sm"  style="display:none">
                                {{ Form::select('expense_provider_id[]', ['' => ''], @$expenses[$i]['pivot']['provider_id'], ['class' => 'form-control input-sm m-0 select2']) }}
                            </td>
                            <td>
                                {{ Form::text('expense_cost_price[]',  @$expenses[$i]['pivot']['cost_price'], ['class' => 'form-control input-sm decimal m-0 text-right']) }}
                            </td>
                            <td>
                                {{ Form::text('expense_cost_subtotal[]',  @$expenses[$i]['pivot']['cost_subtotal'], ['class' => 'form-control input-sm decimal m-0 text-right', 'readonly']) }}
                                {{ Form::hidden('expense_cost_vat[]', @$expenses[$i]['pivot']['cost_vat']) }}
                                {{ Form::hidden('expense_cost_total[]', @$expenses[$i]['pivot']['cost_total']) }}
                            </td>
                            <td class="input-sm expense-provider-detail" style="display:none">
                                {{ Form::select('expense_cost_vat_rate_id[]', $vatTaxes, @$expenses[$i]['pivot']['cost_vat_rate_id'], ['class' => 'form-control input-sm m-0 select2']) }}
                            </td>
                            <td>
                                @if(!@$expenses[$i]['pivot']['auto'])
                                <span class="remove-expenses">
                                    <i class="fas fa-spin fa-circle-notch m-t-8 hide" style="margin-right: -5px"></i>
                                    <i class="fas fa-times m-t-8 text-red"></i>
                                </span>
                                @endif
                            </td>
                            <td>
                                <span class="update-expenses">
                                    <i class="fas fa-sync-alt m-t-8 text-muted"></i>
                                </span>
                            </td>
                        </tr>
                        @endfor
                    @else
                        @for($i = 0 ; $i < 6 ; $i++)
                            <tr class="row-expenses" data-auto="">
                                <td class="input-sm">
                                    {!! Form::selectWithData('expense_id[]', $allExpenses, null, ['class' => 'form-control w-100 select2']) !!}
                                    {{ Form::hidden('expense_auto[]', 0) }}
                                    {{ Form::hidden('expense_billing_item[]', null) }}
                                </td>
                                <td>
                                    {{ Form::text('expense_qty[]', 1, ['class' => 'form-control input-sm number m-0 text-center']) }}
                                </td>
                                <td>
                                    <div class="input-group input-group-money">
                                        {{ Form::text('expense_price[]', null, ['class' => 'form-control input-sm decimalneg m-0 text-right']) }}
                                        <div class="input-group-addon">{{ $appCurrency }}</div>
                                    </div>
                                    {{ Form::hidden('expense_unity[]', 'euro') }}
                                </td>
                                <td>
                                    {{ Form::text('expense_subtotal[]', null, ['class' => 'form-control input-sm decimal m-0 text-right bold text-blue', 'readonly']) }}
                                    {{ Form::hidden('expense_vat[]') }}
                                    {{ Form::hidden('expense_total[]') }}
                                </td>
                                <td class="input-sm" style="border-right: 2px solid #333">
                                    {{ Form::select('expense_vat_rate_id[]', ['' => 'Auto'] + $vatTaxes, null, ['class' => 'form-control input-sm m-0 select2']) }}
                                </td>
                                <td class="expense-provider-detail"  style="display:none">
                                    {{ Form::text('expense_provider_id[]', null, ['class' => 'form-control input-sm m-0']) }}
                                </td>
                                <td>
                                    {{ Form::text('expense_cost_price[]', null, ['class' => 'form-control input-sm decimal m-0 text-right']) }}
                                </td>
                                <td>
                                    {{ Form::text('expense_cost_subtotal[]', null, ['class' => 'form-control input-sm decimal m-0 text-right', 'readonly']) }}
                                    {{ Form::hidden('expense_cost_vat[]', null) }}
                                    {{ Form::hidden('expense_cost_total[]', null) }}
                                </td>
                                <td class="input-sm expense-provider-detail" style="display:none">
                                    {{ Form::select('expense_cost_vat_rate_id[]', $vatTaxes, null, ['class' => 'form-control input-sm m-0 select2']) }}
                                </td>
                                <td>
                                    <span class="remove-expenses">
                                        <i class="fas fa-spin fa-circle-notch m-t-8 hide" style="margin-right: -5px"></i>
                                        <i class="fas fa-times m-t-8 text-red"></i>
                                    </span>
                                </td>
                                <td>
                                <span class="update-expenses">
                                    <i class="fas fa-sync-alt m-t-8 text-muted"></i>
                                </span>
                                </td>
                            </tr>
                        @endfor
                    @endif
                </table>
                {{ Form::hidden('expenses_subtotal') }}
                {{ Form::hidden('expenses_vat') }}
                {{ Form::hidden('expenses_total') }}
                {{ Form::hidden('expenses_cost_subtotal') }}
                {{ Form::hidden('expenses_cost_vat') }}
                {{ Form::hidden('expenses_cost_total') }}
                <button class="pull-left btn btn-xs btn-default btn-add-expenses m-l-5" type="button"><i class="fas fa-plus"></i> Adicionar outra taxa</button>
                <div class="m-l-5 m-t-5 pull-left">
                    <a href="#" class="btn-expenses-costs" type="button"><i class="fas fa-plus"></i> Ver/Ocultar detalhe custos</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer" style="padding: 10px 15px;">
                <button type="button" class="btn btn-default pull-right m-l-15 confirm-expenses">Confirmar</button>
                <div class="pull-right">
                    <h5 class="m-0 p-l-10 pull-left">
                        <small>Subtotal</small><br/>
                        <span class="expenses-subtotal">0,00</span>{{ $appCurrency }}
                    </h5>
                    <h5 class="m-0 p-l-10 pull-left">
                        <small>IVA</small><br/>
                        <span class="expenses-vat">0,00</span>{{ $appCurrency }}
                    </h5>
                    <h5 class="m-0 p-l-10  pull-left">
                        <small>Total</small><br/>
                        <span class="expenses-total">0,00</span>{{ $appCurrency }}
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-money .form-control {
        padding-right: 18px
    }
    .input-group-money .input-group-addon {
        padding: 7px 0;
    }
</style>