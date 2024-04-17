<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default panel-prices-tables">
        <small class="pull-right p-r-10 p-t-3 btn-pricetb-adv-opts">
            <i class="fas fa-cog" data-toggle="tooltip" title="Configurações avançadas"></i> @trans('Avançado')
        </small>
        <a role="button" class="panel-heading trigger-price-table-load" data-toggle="collapse" data-parent="#accordion" href="#accordion-{{ $unity }}" aria-expanded="true" aria-controls="collapseOne"
            data-unity="{{ $unity }}" data-url="{!! route('admin.customers.price-table', [$customer->id, $groupId] + Request::all()) !!}">
            <h4 class="panel-title">
                <i class="fas fa-spin fa-spinner m-r-5" id="spinner-{{ $unity }}" style="display: none"></i>
                <i class="fas {{ $groupIcon }}"></i>
                {{ $groupName }}
                <small>
                    @foreach($pricesTableData[$unity] as $service)
                    &bull; {{ $service->name }}
                    @endforeach
                </small>
                <i class="fas fa-caret-down pull-right"></i>
            </h4>
        </a>
        <div id="accordion-{{ $unity }}" class="panel-collapse collapse {{ @$collapsed }}" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body p-0">
                <div class="row row-0 pricetb-adv-opts" style="padding: 15px 15px 2px; display: {{ (Request::get('origin_zone') || @$customer->prices_tables[$unity]) ? 'block' : 'none' }};">
                    <div class="col-sm-2">
                        <div class="row row-0">
                            {{--<div class="col-sm-3">
                                {{ Form::label('origin_zone', 'Origem') }}
                            </div>--}}
                            <div class="col-sm-12 input-sm" style="margin-top: -10px; margin-left: -6px">
                                {{ Form::select('origin_zone', ['' => 'Qualquer Zona Origem'] + $billingZonesList, @$originZone, ['class' => 'form-control select2', 'data-unity' => $unity]) }}
                                <span style="position: absolute;right: -25px;padding: 6px;">
                                    {!! knowledgeTip(125, 'Permite definir uma tabela de preços para uma determinada zona de recolha/origem.') !!}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                    </div>
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-6">
                                {{ Form::open(['route' => ['admin.customers.services.store', $customer->id, 'group' => $unity, 'source' => 'group-price-table']]) }}
                                <div class="row row-0">
                                    <div class="col-sm-3 text-right ">
                                        {{ Form::label('prices_tables', 'Preçário') }}
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-group input-sm {{ @$customer->prices_tables[$unity] ? 'fltr-enabled' : '' }}" style="margin-top: -10px;">
                                            {{ Form::select('prices_tables['.$unity.']', ['' => 'Personalizado'] + $pricesTables, $customer->price_table_id && !@$customer->prices_tables[$unity] ? $customer->price_table_id : @$customer->prices_tables[$unity], ['class' => 'form-control select2', $customer->price_table_id ? 'disabled' : '']) }}
                                            <div class="input-group-btn">
                                            @if($customer->price_table_id)
                                                <button type="button" class="btn btn-sm btn-default" disabled style="padding: 5px 10px;">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-default" style="padding: 5px 10px;">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            <div class="col-sm-6">
                                <div style="float: right; margin-top: -5px; margin-bottom: 8px;">
                                    <form action="#" class="form-inline pull-left m-r-15 form-update-prices">
                                        {{ Form::select('update_target', $servicesGroupsList, $unity, ['class' => 'hide']) }}
                                        <div class="input-group input-sm p-0" style="margin-right: -4px;">
                                            {{ Form::select('update_signal', ['add' => 'Aumentar', 'sub' => 'Diminuir'], null, ['class' => 'form-control input-sm select2', $customer->price_table_id ? 'disabled' : '']) }}
                                        </div>
                                        <div class="input-group input-group-sm input-group-money w-60px p-0" style="margin-right: -4px;">
                                            {{ Form::text('update_percent', null, ['class' => 'form-control', 'maxlength' => 3, $customer->price_table_id ? 'disabled' : '']) }}
                                            <span class="input-group-addon">%</span>
                                        </div>
                                        <div class="input-group">
                                            <button class="btn btn-sm btn-block btn-default increment-prices">Aplicar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div id="price-table-{{ $unity }}"></div>
            </div>
        </div>
    </div>
</div>
