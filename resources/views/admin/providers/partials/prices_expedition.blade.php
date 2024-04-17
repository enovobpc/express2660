<div class="box no-border">
    <div class="box-body">
        {{--@if(Setting::get('app_mode') == 'courier')--}}
            {{ Form::model($provider, $formOptions) }}
            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group is-required">
                        {{ Form::label('name', __('Custo por serviço')) }}<i class="fas fa-info-circle" data-toggle="tooltip" title="Preencha este campo caso o fornecedor ganhe uma percentagem do preço total do seu serviço"></i>
                        <div class="input-group">
                            {{ Form::text('percent_total_price_gain', null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="form-group">
                        {{ Form::label('fuel_tax', __('Taxa Combustível')) }}
                        <div class="input-group">
                            {{ Form::text('fuel_tax', null, ['class' => 'form-control decimal', 'placeholder' => Setting::get('fuel_tax')]) }}
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <button class="btn btn-primary" style="margin-top: 18px;">@trans('Gravar')</button>
                </div>
<!--                <div class="col-sm-6 italic text-muted">
                    <div class="h-25px"></div>
                    <i class="fas fa-info-circle"></i> Em alternativa, pode configurar para o fornecedor uma tabela de preços dependendo do serviço.
                </div>-->
            </div>
            {{ Form::hidden('daily_report') }}
            {{ Form::hidden('autodetect_agencies') }}
            {{ Form::hidden('allow_out_of_standard') }}
            {{ Form::hidden('type') }}
            {{ Form::hidden('color') }}
            {{ Form::close() }}
        <hr class="m-t-0 m-b-5"/>
        <h4 style="font-weight: bold;color: #377db9; text-transform: uppercase;">@trans('Tabela de Preços do Fornecedor')</h4>

        {{--@endif--}}
        <ul class="list-unstyled">
            <li>
                <strong class="pull-left p-r-0 p-t-7">@trans('Preços para Agência')</strong>
                <div class="pull-left p-t-7"><i class="fas fa-spin fa-circle-notch loading-table" style="display: none"></i></div>
                <div class="pull-left w-200px input-sm" style="margin-top: -5px">
                    {{ Form::select('agency', $agenciesList, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control select2', 'data-type' => 'expedition')) }}
                </div>
            </li>
            <li>
                <strong class="pull-left p-r-0 p-t-7">@trans('e para o Cliente')</strong>
                <div class="pull-left p-t-7"><i class="fas fa-spin fa-circle-notch loading-table" style="display: none"></i></div>
                <div class="pull-left w-200px input-sm" style="margin-top: -5px">
                    {{ Form::select('customer_id', [@$customer->id => @$customer->name], Request::has('customer_id') ? Request::get('customer_id') : null, array('class' => 'form-control', 'data-placeholder' => '-- Todos --', 'data-type' => 'expedition')) }}
                </div>
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
        <div class="price-table">
            <?php
                $readonly        = '';
                $type            = 'expedition';
            ?>
            @foreach($servicesGroups as $serviceGroup)
                @if(@$pricesTableData[$serviceGroup->code] && !$pricesTableData[$serviceGroup->code]->isEmpty())
                    <?php
                        $groupId = $serviceGroup->id;
                        $groupCode = $serviceGroup->code;
                        $groupName = $serviceGroup->name;
                        $groupIcon = $serviceGroup->icon;
                        $unity = $groupCode;
                        $originZone = Request::get('origin-zone');
                        $collapsed = Request::get('unity') == $unity ? 'in' : '';
                    ?>
                    @include('admin.providers.partials.prices.price_table')
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
    .panel-prices-tables {
        position: relative;
        margin-bottom: 0;
        border-radius: 3px;
        border: 1px solid #83878b2b;
        box-shadow: 0 1px 1px rgb(154 161 168);
    }

    .panel-prices-tables .panel-heading {
        display: block;
        color: #222;
        background-color: #8d949a1c;
        border-color: transparent;
    }

    .panel-prices-tables .panel-title {
        color: #195f9b;
        font-weight: bold;
    }

    .panel-prices-tables .panel-title small {
        color: #777;
    }

    .prices-tables-toggle {
        float: right;
        padding: 6px 0 5px;
    }
</style>

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

        @if ($provider->services->isEmpty())
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