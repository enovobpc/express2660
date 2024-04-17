@if (!$customer->has_prices)
    <div class="alert alert-danger">
        <h4><i class="fa fa-exclamation-triangle"></i> @trans('Este cliente não tem associada nenhuma tabela de preços.')</h4>
    </div>
@endif
@if (Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
    <div class="row" style="
    padding: 10px;
    background: #fff;
    margin: -10px 0 20px;
    border-bottom: 1px solid #ccc;">
        <div class="col-sm-12 col-lg-6">
            <?php
            $route = 'admin.customers.update';
            if (@$source == 'prospects') {
                $route = 'admin.prospects.update';
            }
            ?>
            {{ Form::open(['route' => [$route, $customer->id, 'source' => 'prices'], 'method' => 'PUT', 'class' => 'form-inline pull-left m-l-15']) }}
            <div class="form-group">
                {{ Form::label('price_table_id', 'Preçário Geral') }} {!! tip("Escolha um preçário global para aplicar a todos os serviços disponíveis. Se escolher 'Personalizado', poderá definir manualmente os preços ou atribuir uma tarifa a cada grupo de serviços.") !!}
                <div class="input-group input-sm" style="min-width: 200px; padding:0">
                    {{ Form::select('price_table_id', ['' => __('Personalizado')] + $pricesTables, @$customer->price_table_id, ['class' => 'form-control input-sm select2']) }}
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-success update-table-prices" style="padding: 5px 10px">@trans('Aplicar')</button>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false" style="padding: 5px 10px; border-radius: 0 !important">
                                @trans('Pré-preencher') <i class="fas fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a data-toggle="modal" data-target="#modal-import-prices-table" href="#">
                                        @trans('Preencher tabela a partir de outro cliente')
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="modal" data-target="#modal-import-global-prices-table" href="#">
                                        @trans('Preencher tabela a partir das tabelas gerais')
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-print-price-table" href="#" style="padding: 5px 10px;  border-radius: 0 !important">
                            <i class="fas fa-print"></i> @trans('Imprimir')
                        </a>
                    </span>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <div class="col-sm-12 col-lg-6">
            <a class="prices-tables-toggle" href="#">
                <i class="fas fa-expand-alt"></i> @trans('Mostrar/Ocultar tudo')
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @if ($customer->price_table_id)
        <div class="alert alert-info" style="padding: 8px">
            <h4 class="m-0">@trans('Este cliente está a usar a Tabela de Preços') "{{ @$pricesTables[$customer->price_table_id] }}"</h4>
            <p>
                @trans('Os preços apenas podem ser alterados editando a tabela de preços em "Definições" > "Tabelas de Preço Globais".')
                <br />
                @trans('Se quer usar para este cliente a tabela') "{{ @$pricesTables[$customer->price_table_id] }}" @trans('e editar alguns valores, deve
                ou importar a tabela para o cliente acedendo a "Ferramentas" > "Importar das Tabelas Globais".')
            </p>
        </div>
    @endif
@endif

<div class="customers-prices-tables" style="{{ $customer->price_table_id ? 'opacity: 0.5' : '' }}">
    <?php
    $readonly = '';
    
    if (!Auth::user()->hasRole(Config::get('permissions.role.admin'))) {
        if (Auth::user()->can('prices_tables_view') || $customer->price_table_id) {
            $readonly = 'readonly';
        }
    }
    ?>
    @foreach ($servicesGroups as $serviceGroup)
        @if (@$pricesTableData[$serviceGroup->code] && !$pricesTableData[$serviceGroup->code]->isEmpty())
            <?php
            $groupId = $serviceGroup->id;
            $groupCode = $serviceGroup->code;
            $groupName = $serviceGroup->name;
            $groupIcon = $serviceGroup->icon;
            $unity = $groupCode;
            $originZone = Request::get('origin-zone');
            $collapsed = !$customer->price_table_id && $customer->services->isEmpty() ? 'in' : '';
            $collapsed = Request::get('unity') == $unity ? 'in' : $collapsed;
            ?>
            @include('admin.customers.customers.partials.prices.price_table')
        @endif
    @endforeach
</div>

<script>
    window.addEventListener('load', function() {
        function loadTable($button) {
            if (!$('#price-table-' + $button.data('unity')).length) {
                return;
            }

            $('#spinner-' + $button.data('unity')).show();
            $.get($button.data('url'))
            .done(function(html) {
                $('#spinner-' + $button.data('unity')).hide();
                $('#price-table-' + $button.data('unity')).replaceWith(html);
                $('#accordion-' + $button.data('unity')).addClass('in');
                $('#accordion-' + $button.data('unity')).css('height', 'auto');
            });
        }

        $('.trigger-price-table-load').on('click', function() {
            loadTable($(this));
        });

        @if (!$customer->price_table_id && $customer->services->isEmpty())
            $('.trigger-price-table-load').each(function() {
                loadTable($(this));
            });
        @else
            @if (Request::get('unity', false))
                loadTable($('.trigger-price-table-load[data-unity="{{ Request::get('unity') }}"]'));
            @endif
        @endif
    });
</script>
