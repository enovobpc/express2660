<input type="hidden" name="zone_provider_id" value="{{ $provider->id }}"/>
<div class="row">
    <div class="col-xs-6">
        <div class="provider-zones">
            <h4 class="text-uppercase text-primary fs-15 m-t-0">Zonas de Faturação {!! tip('Escolha as zonas que vão fazer parte da tabela de custos do fornecedor. Cada zona de faturação corresponderá a uma coluna na tabela de preços. Se não indicadas, são assumidas as zonas de compra.') !!}</h4>
            <a href="#" class="select-all-zones pull-right">Sel. Todos</a>
            <span class="count-selected">{{ count(@$service->zones_provider[$provider->id]) }}</span> zonas selecionadas
            <div style="height: 350px;overflow: scroll;border: 1px solid #ddd;padding: 8px; position: relative;">
                <div style="position: relative;
        left: -9px;
        top: -9px;
        right: -9px;
        height: 30px;
        width: 105%;">
                    {{ Form::text('filter_box_provider', null, ['class' => 'form-control', 'placeholder' => 'Encontrar na lista...']) }}
                </div>
                @foreach($billingZones as $unity => $zones)
                    <p style="display: block; margin-bottom: 5px" class="bold text-uppercase m-t-0 m-b-0" data-label="{{ $unity }}">{{ $unity == 'zip_code' ? 'Zonas Por Códigos Postais' : ($unity == 'pack_type' ? 'Preços por Tipo Embalagem' : 'Zonas Por País') }}</p>
                    @foreach($zones as $zone)
                        <div class="checkbox m-t-5 m-b-8" data-unity="{{ $unity }}" data-filter-provider-text="{{ $zone->code }} {{ strtolower($zone->name) }}">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('zones_provider['.$provider->id.'][]', $zone->code, @$service->zones_provider[$provider->id] ? in_array($zone->code, @$service->zones_provider[$provider->id]) : [], ['class' => 'row-zone']) }}
                                <span class="label label-default text-uppercase" target="_blank" style="min-width: 55px;font-size: 11px;display: inline-block;">{{ $zone->code }}</span> {{ $zone->name }}
                            </label>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xs-4">
        <h4 class="text-uppercase text-primary fs-15 m-t-0"><i class="fas fa-plug"></i> Serviço Webservice Default</h4>
        @if(Auth::user()->hasRole(Config::get('permissions.role.admin')))
            <div class="form-group">
                {{ Form::label('code', 'Código Standard Webservice') }}
                {{ Form::text('code', $service->code, ['class' => 'form-control']) }}
            </div>
        @endif

       {{-- <h4 class="text-uppercase text-primary fs-15 m-t-0"><i class="fas fa-plug"></i> Ligação Serviços Webservice</h4>
        @if(!$provider->webservice_method)
            <div>
                <p class="text-muted"><i class="fas fa-info-circle"></i> Este fornecedor não tem nenhum método de webservice associado.</p>
                <a href="{{ route('admin.providers.edit', $provider->id) }}" class="btn btn-xs btn-default">Editar Fornecedor</a>
            </div>
        @else
            <p class="bold">Ligação {{ @$provider->webservice->name }}</p>
            @if($provider->webservice_method == 'ctt')
                @include('admin.services.partials.provider_webservice.ctt')
            @elseif($provider->webservice_method == 'chronopost')
                @include('admin.services.partials.provider_webservice.chronopost')
            @else
                @include('admin.services.partials.provider_webservice.geral')
            @endif
        @endif--}}
    </div>
</div>