<div class="box no-border">
    <div class="box-body">
        <ul class="list-unstyled">
            <li>
                <strong class="pull-left p-r-5 p-t-7">@trans('Tabela Distribuição para Agência')</strong>
                <div class="pull-left w-250px input-sm" style="margin-top: -5px">
                    {{ Form::select('agency', ['' => __('-- Selecione uma agência --')] + $agenciesList, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm select2', 'data-type' => 'delivery')) }}
                </div>
                <div class="pull-left p-t-7 p-l-10"><i class="fas fa-spin fa-circle-notch loading-table" style="display: none"></i></div>
            </li>
            @if(Request::has('agency'))
                <li>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @trans('Pré-preencher') <i class="fas fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" data-toggle="modal" data-target="#modal-import-prices-table">
                                    @trans('Preencher tabela a partir de outra agência')
                                </a>
                            </li>
                            <li>
                                <a href="#" data-toggle="modal" data-target="#modal-import-global-prices-table">
                                    @trans('Preencher tabela a partir das tabelas gerais')
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
        </ul>
        <div class="clearfix"></div>
        <hr class="m-t-10"/>
        @if(Request::has('agency'))
            <div class="customers-prices-tables">
                <?php
                $readonly        = '';
                $pricesTableData = $pricesTablesDelivery;
                $rowsWeight      = $rowsDelivery;
                $type            = 'delivery';
                ?>
                @foreach($servicesGroups as $serviceGroup)
                    @if(@$pricesTableData[$serviceGroup->code] && !$pricesTableData[$serviceGroup->code]->isEmpty())
                            <?php
                            $groupCode = $serviceGroup->code;
                            $groupName = $serviceGroup->name;
                            $groupIcon = $serviceGroup->icon;
                            $unity     = $groupCode;
                            ?>
                        @include('admin.providers.partials.prices.price_table')
                        <hr/>
                    @endif
                @endforeach
            </div>
        @else
            <h4 class="text-muted text-center p-t-50 p-b-50"><i class="fas fa-info-circle"></i> @trans('Selecione uma agência da lista para gerir a tabela de preços.')</h4>
        @endif
    </div>
</div>
