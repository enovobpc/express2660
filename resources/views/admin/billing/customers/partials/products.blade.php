<div class="box no-border">
    <div class="box-body">
        @if(empty($customer->products))
            <p class="text-center text-muted padding-40 m-t-50 m-b-50">
                <i class="fas fa-info-circle"></i> Não há registo de venda de artigos em {{ trans('datetime.month.'.$month) }} de {{ $year }}
            </p>
        @else
        <table id="datatable-products" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
                <tr>
                    <th class="w-65px">Data</th>
                    <th>Artigo</th>
                    <th class="w-70px">Preço</th>
                    <th class="w-30px">Qt</th>
                    <th class="w-70px">Subtotal</th>
                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'products_sales'))
                    <th class="w-50px">Ações</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        @endif
    </div>
</div>