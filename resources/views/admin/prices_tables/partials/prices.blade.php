<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-12 col-lg-6">
                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
                    <a href="#" data-toggle="modal" data-target="#modal-import-prices-table" class="btn btn-sm btn-success pull-left m-t-3 m-r-15">
                        <i class="fas fa-upload"></i> @trans('Pré-carregar preços de outra tabela')
                    </a>

                @endif
            </div>
            <div class="col-sm-12 col-lg-6">
                <a href="#" class="prices-tables-toggle">
                    <i class="fas fa-expand-alt"></i> @trans('Mostrar/Ocultar tudo')
                </a>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr class="m-t-10"/>

        <?php
        $readonly = '';
        if(!Auth::user()->hasRole(Config::get('permissions.role.admin'))) {
            if(Auth::user()->can('prices_tables_view')) {
                $readonly = 'readonly';
            }
        }
        ?>
        @foreach($servicesGroups as $serviceGroup)
            @if(@$pricesTableData[$serviceGroup->code] && !$pricesTableData[$serviceGroup->code]->isEmpty())
                <?php
                $groupCode = $serviceGroup->code;
                $groupName = $serviceGroup->name;
                $groupIcon = $serviceGroup->icon;
                $unity     = $groupCode;
                $originZone= Request::get('origin-zone');
                $collapsed = $priceTable->services->isEmpty() ? 'in' : '';
                $collapsed = Request::get('unity') == $unity ? 'in' : $collapsed;
                ?>
                @include('admin.prices_tables.partials.prices.price_table')
            @endif
        @endforeach
        <div class="spacer-15"></div>
    </div>
</div>