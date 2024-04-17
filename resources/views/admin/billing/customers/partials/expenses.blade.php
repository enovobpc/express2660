<div class="box no-border">
    <div class="box-body">
        @if(!$customer->total_expenses)
            <p class="text-center text-muted padding-40 m-t-50 m-b-50">
                <i class="fas fa-info-circle"></i> Não há registo de encargos extra em {{ trans('datetime.month.'.$month) }} de {{ $year }}
            </p>
        @else
            <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-billing-expenses">
                <li class="fltr-primary w-230px">
                    <strong>Encargo</strong><br class="visible-xs"/>
                    <div class="w-170px pull-left form-group-sm">
                        {{ Form::select('expenseid', ['' => 'Todos'] + $expensesTypes, Request::has('expenseid') ? Request::get('expenseid') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
            </ul>
            <table id="datatable-billing-expenses" class="table table-striped table-dashed table-hover table-condensed">
                <thead>
                    <tr>
                        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                        <th class="w-65px">Data</th>
                        <th class="w-1">TRK</th>
                        <th>Encargo</th>
                        <th class="w-60px">Preço</th>
                        <th class="w-30px">Qtd</th>
                        <th class="w-60px">Subtotal</th>
                        <th class="w-30px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="selected-rows-action hide">
                {{ Form::open(array('route' => 'admin.billing.customers.expenses.selected.destroy')) }}
                <button class="btn btn-sm btn-danger" data-action="confirm"><i class="fas fa-trash-alt"></i> Apagar</button>
                {{ Form::close() }}
                <div class="pull-left">
                    <h4 style="margin: -2px 0 -6px 10px;
                        padding: 1px 3px 3px 9px;
                        border-left: 1px solid #999;
                        line-height: 17px;">
                        <small>Total Selecionado</small><br/>
                        <span class="dt-sum-total bold"></span><b>€</b>
                    </h4>
                </div>
            </div>
        @endif
    </div>
</div>