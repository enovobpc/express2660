<div class="row">
    <div class="col-sm-6">
        <h4>Encargos para o Agente e Transportador</h4>
        <table class="table table-condensed m-0 table-expenses">
            <tr class="bg-gray-light">
                <th>Encargo</th>
                <th class="w-140px">Preço</th>
                <th class="w-1"></th>
            </tr>
            <?php
            $rowsVisible = 3;

            if($waybill->exists) {
                $totalGoods  = count($waybill->expenses);
                $rowsVisible = $totalGoods > $rowsVisible ? $totalGoods : $rowsVisible;
            }
            ?>
            @for($i=0 ; $i<15; $i++)
                <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
                    <td style="padding-left: 0">
                        {{ Form::select('expenses['.$i.'][expense]', ['' => ''] + $expenses, null, ['class' => 'form-control input-sm select2 expense-id', 'data-id' => "x-".@$waybill->expenses[$i]['expense'] ]) }}
                    </td>
                    <td>
                        <div class="input-group">
                            {{ Form::text('expenses['.$i.'][price]', null, ['class' => 'form-control input-sm expense-price', 'autocomplete' => 'off']) }}
                            <span class="input-group-addon price-currency-symbol">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="#" class="text-red remove-expenses">
                            <i class="fas fa-times m-t-8"></i>
                        </a>
                    </td>
                </tr>
            @endfor
        </table>
        <button type="button" class="btn btn-xs btn-default btn-add-expenses"><i class="fas fa-plus"></i> Adicionar Encargo</button>
        <button type="button" class="btn btn-xs btn-default btn-update-prices" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A atualizar..."><i class="fas fa-sync-alt"></i> Atualizar Preços</button>
    </div>
    <div class="col-sm-6">
        <h4>Outras Taxas e Despesas</h4>
        <table class="table table-condensed m-0 table-other-expenses">
            <tr class="bg-gray-light">
                <th>Encargo</th>
                <th class="w-140px">Preço</th>
                <th class="w-1"></th>
            </tr>
            <?php
            $rowsVisible = 3;

            if($waybill->exists) {
                $totalGoods  = count($waybill->other_expenses);
                $rowsVisible = $totalGoods > $rowsVisible ? $totalGoods : $rowsVisible;
            }
            ?>
            @for($i=0 ; $i<15; $i++)
                <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
                    <td style="padding-left: 0">
                        {{ Form::select('other_expenses['.$i.'][expense]', ['' => ''] + $otherExpenses, null, ['class' => 'form-control input-sm select2 expense-id']) }}
                    </td>
                    <td>
                        <div class="input-group">
                            {{ Form::text('other_expenses['.$i.'][price]', null, ['class' => 'form-control input-sm expense-price']) }}
                            <span class="input-group-addon price-currency-symbol">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="#" class="text-red remove-other-expenses">
                            <i class="fas fa-times m-t-8"></i>
                        </a>
                    </td>
                </tr>
            @endfor
        </table>
        <button type="button" class="btn btn-xs btn-default btn-add-other-expenses"><i class="fas fa-plus"></i> Adicionar Taxa ou Custo</button>
        <button type="button" class="btn btn-xs btn-default btn-update-prices" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A atualizar..."><i class="fas fa-sync-alt"></i> Atualizar Preços</button>
    </div>
</div>