<div class="row">
    <div class="col-sm-4">
        <div class="row row-5">
            <div class="col-sm-3">
                <div class="form-group m-b-10 is-required">
                    {{ Form::label('display_code', __('Código')) }}
                    {{ Form::text('display_code', null, ['class' => 'form-control uppercase nospace', 'required', 'maxlength' => 6, 'style' => 'padding: 5px']) }}
                    @if(!Auth::user()->hasRole(Config::get('permissions.role.admin')))
                        {{ Form::hidden('code', null, ['class' => 'form-control']) }}
                    @endif
                </div>
            </div>
            <div class="col-sm-9">
                <div class="form-group m-b-10 is-required">
                    {{ Form::label('name', __('Designação')) }}
                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 25]) }}
                </div>
            </div>
        </div>
        <h4 class="form-divider" style="margin-top: 0"><i class="fas fa-clock"></i> @trans('Zonas e Tempos Trânsito')</h4>
        <div class="row row-5">
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('transit_time', __('Tempo de Trânsito')) }}
                    <div class="row row-0">
                        <div class="col-sm-6">
                            <div class="input-group">
                                {{ Form::text('transit_time', null, ['class' => 'form-control nospace number', 'maxlength' => 5, 'placeholder' => ' Min']) }}
                                <span class="input-group-addon">h</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                {{ Form::text('transit_time_max', null, ['class' => 'form-control nospace number', 'maxlength' => 5, 'placeholder' => ' Max']) }}
                                <span class="input-group-addon">h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('delivery_hour', __('Entrega até')) }}
                    {{ Form::select('delivery_hour', ['' => ''] + $hours, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('priority_level', __('Urgência')) }} {!! tip('Especifica o nivel de prioridade do serviço. Serviços mais prioritários aparecem primeiro na aplicação do telemóvel.') !!}
                    {{ Form::select('priority_level', ['' => '--'] + $priorLevels, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
        </div>
        <div class="row row-5 m-b-5">
            <div class="col-xs-12">
                <a href="#" class="select-all-zones pull-right">@trans('Sel. Todos')</a>
                {{ Form::label('zones[]', __('Zonas de faturação')) }} (<span class="count-selected">{{ count($service->zones) }}</span> @trans('selecionados'))
                <div class="zones-list" style="height: 489px;overflow-y: scroll;overflow-x: hidden;border: 1px solid #ddd;padding: 8px; position: relative;">
                    <div style="position: relative;left: -9px; top: -9px;
right: -9px;height: 30px; width: 105%;margin-bottom: -5px;">
                        {{ Form::text('filter_box', null, ['class' => 'form-control', 'placeholder' => __('Filtrar na lista...')]) }}
                    </div>
                    @foreach($billingZones as $unity => $zones)
                        <p class="zone-group" data-label="{{ $unity }}">
                            @if($unity == 'zip_code')
                            @trans('Zonas Por Códigos Postais')
                            @elseif($unity == 'pack_type')
                            @trans('Zonas por Tipo Embalagem')
                            @elseif($unity == 'pack_zip_code')
                            @trans('Zonas por Tipo Embalagem + Cód. Postal')
                            @elseif($unity == 'matrix')
                            @trans('Zonas por Matriz Códigos Postais')
                            @else
                            @trans('Zonas Por País')
                            @endif
                        </p>
                        <table class="w-100" data-label="{{ $unity }}" cellpadding="0" cellspacing="0">
                            <tr>
                                <th>@trans('Zona')</th>
                                <th><span data-toggle="tooltip" title="Tempo Transito Mínimo">T.Min</span></th>
                                <th><span data-toggle="tooltip" title="Tempo Transito Máximo">T.Max</span></th>
                            </tr>
                            @foreach($zones as $zone)
                                <tr  data-filter-text="{{ $zone->code }} {{ strtolower($zone->name) }}">
                                    <td>
                                        <div class="checkbox m-0" data-unity="{{ $unity }}">
                                            <label style="padding-left: 0">
                                                {{ Form::checkbox('zones[]', $zone->code, null, ['class' => 'row-zone']) }}
                                                <span class="label label-default text-uppercase" style="min-width: 55px;font-size: 11px;display: inline-block;">{{ $zone->code }}</span> {{ $zone->name }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="w-40px p-r-3">
                                        {{ Form::text('zones_transit['.$zone->code.']', null, ['class' => 'form-control zones-transit b-r-0', 'placeholder' => $service->transit_time ? (int) $service->transit_time : $service->transit_time]) }}
                                    </td>
                                    <td class="w-40px">
                                        {{ Form::text('zones_transit_max['.$zone->code.']', null, ['class' => 'form-control zones-transit', 'placeholder' =>  $service->transit_time_max ? (int) $service->transit_time_max : $service->transit_time_max]) }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="row row-5">
            <div class="col-sm-7">
                <div class="form-group m-b-5 is-required">
                    <small class="pull-right">
                        <a href="{{ route('admin.services.groups.index') }}" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-cog"></i> @trans('Gerir')
                        </a>
                    </small>
                    {{ Form::label('group', __('Grupo de Serviços')) }}
                    {{ Form::select('group', ['' => ''] + $servicesGroups, null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group m-b-5">
                    {{ Form::label('transport_type_id', __('Tipo Transporte')) }}
                    {{ Form::select('transport_type_id', ['' => ''] + $servicesTypes, null, ['class' => 'form-control select2']) }}
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group m-b-5 input-unity is-required">
                    {{ Form::label('unity', __('Regra Cálculo Preços')) }}
                    {!! tip('Os preços serão calculados com base na opção escolhida. Por exemplo, se escolher "baseado no peso", os preço do serviço baseia-se no peso total do serviço.') !!}
                    {{ Form::select('unity', ['' => ''] + $types, null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>

            <div class="col-sm-8">
                <div class="form-group m-b-0">
                    {{ Form::label('provider_id', __('Força fornecedor')) }}
                    <span style="position: absolute">{!! tip('Quando este serviço for escolhido, o sistema irá submeter obrigatóriamente o envio pelo fornecedor que aqui indicar.') !!}</span>
                    {{ Form::select('provider_id', ['' => '- Nenhum -'] + $providers, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group m-b-0">
                    {{ Form::label('vat_rate', __('Força IVA')) }}
                    {!! tip(__('Força a taxa de IVA. Auto permite que o sistema decida.')) !!}
                    {{ Form::select('vat_rate', ['' => 'Auto'] + $vatRates, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
        </div>

        <div class="row row-0">
            <div class="col-sm-4">
                <div data-toggle="tooltip" title="@trans('Ative esta opção se o preço do serviço for apenas para um volume. Mais do que um volume o sistema multiplicará o preço pelo total de volumes')">
                    <div class="checkbox m-b-0">
                        <label style="padding-left: 0">
                            {{ Form::checkbox('price_per_volume', 1) }}
                            @trans('Preço x Vol')
                        </label>
                        {!! tip(__('O preço da expedição é dado pelo preço tabela x numero de volumes.')) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="checkbox m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('multiply_price', 1) }}
                        @trans('Preço') x m<sup>3</sup>
                    </label>
                    {!! tip(__('Multiplica o preço do serviço pelo valor de metros cúbicos da expedição.')) !!}
                </div>
            </div>
        </div>

        <h4 class="form-divider" style="margin-top: 10px"><i class="fas fa-weight-hanging"></i> @trans('Limites de Peso e Volumes')</h4>
        <div class="row row-5">
            <div class="col-sm-12">
               <table class="table-condensed table-doc-types m-b-5">
                   <tr>
                       <td class="bold w-70px">@trans('Permitir')</td>
                       <td class="bold w-70px">@trans('Peso Max')</td>
                       <td class="bold">Dim. Max (L/C/A)</td>
                       <td class="bold w-80px">C+L+A Max.</td>
                   </tr>
                   <tr>
                       <td>
                           <div class="checkbox m-t-5 m-b-0">
                               <label style="padding-left: 0">
                                   {{ Form::checkbox('allow_docs', 1) }}
                                   Docs
                               </label>
                           </div>
                       </td>
                       <td>
                           <div class="form-group m-b-0">
                               <div class="input-group input-group-money">
                                   {{ Form::text('max_weight_docs', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8]) }}
                                   <div class="input-group-addon">kg</div>
                               </div>
                           </div>
                       </td>
                       <td>
                           <div class="row row-0">
                               <div class="col-xs-4">
                                   {{ Form::text('max_length_docs', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '']) }}
                               </div>
                               <div class="col-xs-4">
                                   {{ Form::text('max_width_docs', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '', 'style' => 'border-left: none;']) }}
                               </div>
                               <div class="col-xs-4">
                                   {{ Form::text('max_height_docs', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '', 'style' => 'border-left: none;']) }}
                               </div>
                           </div>
                       </td>
                       <td>
                           <div class="form-group m-b-0">
                               <div class="input-group input-group-money">
                                   {{ Form::text('max_dims_docs', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8]) }}
                                   <div class="input-group-addon">cm</div>
                               </div>
                           </div>
                       </td>
                   </tr>
                   <tr>
                       <td>
                           <div class="checkbox m-t-5 m-b-0">
                               <label style="padding-left: 0">
                                   {{ Form::checkbox('allow_boxes', 1) }}
                                   @trans('Caixas')
                               </label>
                           </div>
                       </td>
                       <td>
                           <div class="form-group m-b-0">
                               <div class="input-group input-group-money">
                                   {{ Form::text('max_weight_boxes', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8]) }}
                                   <div class="input-group-addon">kg</div>
                               </div>
                           </div>
                       </td>
                       <td>
                           <div class="row row-0">
                               <div class="col-xs-4">
                                   {{ Form::text('max_length_boxes', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '']) }}
                               </div>
                               <div class="col-xs-4">
                                   {{ Form::text('max_width_boxes', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '', 'style' => 'border-left: none;']) }}
                               </div>
                               <div class="col-xs-4">
                                   {{ Form::text('max_height_boxes', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '', 'style' => 'border-left: none;']) }}
                               </div>
                           </div>
                       </td>
                       <td>
                           <div class="form-group m-b-0">
                               <div class="input-group input-group-money">
                                   {{ Form::text('max_dims_boxes', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8]) }}
                                   <div class="input-group-addon">cm</div>
                               </div>
                           </div>
                       </td>
                   </tr>
                   <tr>
                       <td>
                           <div class="checkbox m-t-5 m-b-0">
                               <label style="padding-left: 0">
                                   {{ Form::checkbox('allow_pallets', 1) }}
                                   @trans('Paletes')
                               </label>
                           </div>
                       </td>
                       <td>
                           <div class="form-group m-b-0">
                               <div class="input-group input-group-money">
                                   {{ Form::text('max_weight_pallets', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8]) }}
                                   <div class="input-group-addon">kg</div>
                               </div>
                           </div>
                       </td>
                       <td>
                            <div class="row row-0">
                                <div class="col-xs-4">
                                    {{ Form::text('max_length_pallets', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '']) }}
                                </div>
                                <div class="col-xs-4">
                                    {{ Form::text('max_width_pallets', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '', 'style' => 'border-left: none;']) }}
                                </div>
                                <div class="col-xs-4">
                                    {{ Form::text('max_height_pallets', null, ['class' => 'form-control nospace decimal pack-dim', 'maxlength' => 8, 'placeholder' => '', 'style' => 'border-left: none;']) }}
                                </div>
                            </div>
                       </td>
                       <td>
                           <div class="form-group m-b-0">
                               <div class="input-group input-group-money">
                                   {{ Form::text('max_dims_pallets', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8]) }}
                                   <div class="input-group-addon">cm</div>
                               </div>
                           </div>
                       </td>
                   </tr>
               </table>
            </div>
            <div class="col-sm-6">
                {{ Form::label('max_volumes', __('Volumes totais Exp.')) }}
                <div class="row row-0">
                    <div class="col-sm-6">
                        <div class="form-group m-b-0">
                            {{ Form::text('min_volumes', null, ['class' => 'form-control nospace number', 'maxlength' => 8, 'placeholder' => 'Min']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-5">
                            {{ Form::text('max_volumes', null, ['class' => 'form-control nospace number', 'maxlength' => 8, 'placeholder' => 'Max']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                {{ Form::label('max_weight', __('Peso total Expedição')) }}
                <div class="row row-0">
                    <div class="col-sm-6">
                        <div class="form-group m-b-0">
                            <div class="input-group input-group-money">
                                {{ Form::text('min_weight', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8, 'placeholder' => 'Min']) }}
                                <div class="input-group-addon">kg</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-5">
                            <div class="input-group input-group-money">
                                {{ Form::text('max_weight', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8, 'placeholder' => 'Max']) }}
                                <div class="input-group-addon">kg</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 hide">
                <div class="form-group m-b-0">
                    {{ Form::label('max_dims', __('C+A+L Máx.')) }}
                    <div class="input-group">
                        {{ Form::text('max_dims', null, ['class' => 'form-control nospace decimal', 'maxlength' => 8, 'placeholder' => 'Max']) }}
                        <div class="input-group-addon">cm</div>
                    </div>
                </div>
            </div>
        </div>
        
        <h4 class="form-divider" style="margin-top: 10px"><i class="fas fa-calendar-alt"></i> @trans('Limites Horários')</h4>
        <div class="row row-5">
            <div class="col-sm-5">
                <div class="form-group m-b-0 input-hour">
                    {{ Form::label('min_hour', __('Horário Recolha')) }}
                    <div class="row row-0">
                        <div class="col-sm-6">
                            {{ Form::select('min_hour', $hours, $service->min_hour ? null : '00:00', ['class' => 'form-control select2']) }}
                        </div>
                        <div class="col-sm-6">
                            {{ Form::select('max_hour', $hours, $service->max_hour ? null : '23:55', ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-b-0 m-t-5">
                    {{ Form::label('pickup_hour_difference', __('Dif. Horária Mínima'), ['data-toggle' => 'tooltip', 'title' => 'Diferença Horária Mínima de Recolha']) }}
                    {{ Form::select('pickup_hour_difference', ['' => __('- Nenhuma -')] + listHours(10, 1, 0, 0, 8), null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-7">
                <div class="form-group m-b-0" style="margin-top: -5px;">
                    {{ Form::label('pickup_weekdays[]', __('Dias Recolha')) }}
                    <div>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('pickup_weekdays[]', 1) }}
                            @trans('Seg')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('pickup_weekdays[]', 2) }}
                            @trans('Ter')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 48px">
                            {{ Form::checkbox('pickup_weekdays[]', 3) }}
                            @trans('Qua')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0;">
                            {{ Form::checkbox('pickup_weekdays[]', 4) }}
                            @trans('Qui')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('pickup_weekdays[]', 5) }}
                            @trans('Sex')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('pickup_weekdays[]', 6) }}
                            @trans('Sab')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 55px">
                            {{ Form::checkbox('pickup_weekdays[]', 0) }}
                            @trans('Dom')
                        </label>
                    </div>
                </div>

                <div class="form-group m-b-0 m-t-5">
                    {{ Form::label('delivery_weekdays[]', __('Dias Entrega')) }}
                    <div>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('delivery_weekdays[]', 1) }}
                            @trans('Seg')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('delivery_weekdays[]', 2) }}
                            @trans('Ter')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 48px">
                            {{ Form::checkbox('delivery_weekdays[]', 3) }}
                            @trans('Qua')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0;">
                            {{ Form::checkbox('delivery_weekdays[]', 4) }}
                            @trans('Qui')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('delivery_weekdays[]', 5) }}
                            @trans('Sex')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 45px">
                            {{ Form::checkbox('delivery_weekdays[]', 6) }}
                            @trans('Sab')
                        </label>
                        <label class="checkbox-inline" style="padding: 0; margin: 0; width: 55px">
                            {{ Form::checkbox('delivery_weekdays[]', 0) }}
                            @trans('Dom')
                        </label>
                    </div>
                </div>
            </div>

            <h4 class="form-divider" style="margin-top: 10px">@trans('Serviço de Recolha')</h4>
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group {{ $service->is_collection ? '' : 'is-required' }}">
                        {{ Form::label('assigned_service_id', __('Serv. Recolha Associado')) }}
                        {!! tip('Este serviço ficará associado ao serviço de recolha indicado.') !!}
                        {{ Form::select('assigned_service_id', ['' => '- Nenhum -'] + $pickupServices, null, ['class' => 'form-control select2', $service->is_collection ? 'disabled' : '']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group {{ $service->is_collection ? '' : 'is-required' }}">
                        {{ Form::label('assigned_intercity_service_id', __('Serv. Rec. Intercidades')) }}
                        {{ Form::select('assigned_intercity_service_id', ['' => '- Nenhum -'] + $pickupServices, null, ['class' => 'form-control select2', $service->is_collection ? 'disabled' : '']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <h4 class="text-uppercase text-blue fs-15 m-t-0 m-b-10"><i class="fas fa-tag"></i> @trans('Características')</h4>
        <div class="row row-0">
            <div class="col-sm-6">
                <div class="checkbox m-t-0 m-b-2">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_collection', 1) }}
                        <i class="fas fa-fw fa-cube"></i> @trans('Só Pedido Recolha')
                    </label>
                </div>
            </div>
        </div>
        <div class="row row-0">
            <div class="col-sm-6">
                <div class="checkbox m-t-0 m-b-2">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_mail', 1) }}
                        <i class="fas fa-fw fa-envelope"></i> @trans('Correio (IVA 0%)')
                    </label>
                    {!! tip(__('Considerar este serviço como envio de cartas/correio (Isento IVA por lei)')) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-0 m-b-2">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_courier', 1) }}
                        <i class="fas fa-fw fa-motorcycle"></i> @trans('Estafetagem')
                    </label>
                    {!! tip(__('Descrimina e separa na faturação os serviços de estafetagem dos restantes serviços.')) !!}
                </div>
            </div>
        </div>
        <div class="row row-0">
            <div class="col-sm-6">
                <div class="checkbox m-t-0 m-b-2">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_internacional', 1) }}
                        <i class="fas fa-fw fa-globe"></i> @trans('Internacional')
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-0 m-b-2">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_import', 1) }}
                        <i class="fas fa-fw fa-sign-in-alt"></i> @trans('Serviço Importação')
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-0 m-b-2">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_maritime', 1) }}
                        <i class="fas fa-fw fa-ship"></i> @trans('Via Marítima')
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-0 m-b-2">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_air', 1) }}
                        <i class="fas fa-fw fa-plane"></i> @trans('Via Aérea')
                    </label>
                </div>
            </div>
        </div>
        <h4 class="form-divider" style="margin-top: 15px"><i class="fas fa-puzzle-piece"></i> @trans('Serviços Adicionais')</h4>
        <div class="row row-0">
            <div class="col-sm-6">
                <div class="checkbox m-t-5 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::hidden('allow_cod', 0) }}
                        {{ Form::checkbox('allow_cod', 1, $service->exists ? null : 1) }}
                        @trans('Permite Reembolso')
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-5 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::hidden('allow_return', 0) }}
                        {{ Form::checkbox('allow_return', 1, $service->exists ? null : 1) }}
                        @trans('Permite Retorno')
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-5 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('allow_pudos', 1) }}
                        @trans('Permite PUDOs')
                    </label>
                    {!! tip('Pontos de Recolha e Entrega') !!}
                </div>
            </div>
        </div>
        <h4 class="form-divider" style="margin-top: 10px"><i class="fas fa-wrench"></i> @trans('Definições')</h4>
        <div class="row row-0">
            <div class="col-sm-6">
                <div class="checkbox m-t-3 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::hidden('settings[email_required]', 0) }}
                        @if (!Setting::get('tracking_email_active') || Setting::get('customer_account_email_required'))
                        {{ Form::checkbox('settings[email_required]', 0, false, ['disabled']) }}
                        @else
                        {{ Form::checkbox('settings[email_required]', 1, $service->exists ? @$service->settings['email_required'] : Setting::get('customer_account_email_required')) }}
                        @endif
                        @trans('E-mail obrigatório')
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-3 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::hidden('settings[without_pickup]', 0) }}
                        @if (!Setting::get('customer_shipment_without_pickup'))
                        {{ Form::checkbox('settings[without_pickup]', 0, false, ['disabled']) }}
                        @else
                        {{ Form::checkbox('settings[without_pickup]', 1, $service->exists ? @$service->settings['without_pickup'] : Setting::get('customer_shipment_without_pickup')) }}
                        @endif
                        @trans('Sem Recolha')
                    </label>
                    {!! tip(__('Permite ao cliente indicar que deixa os volumes na agência, sem necessidade de recolha pelo motorista')) !!}
                </div>
            </div>
        </div>
        <div class="row row-0">
            <div class="col-sm-6">
                <div class="checkbox m-t-3 m-b-0">
                    <label style="padding-left: 0" data-toggle="tooltip">
                        {{ Form::hidden('settings[webservices_auto_submit]', 0) }}
                        @if (!Setting::get('webservices_auto_submit'))
                        {{ Form::checkbox('settings[webservices_auto_submit]', 0, false, ['disabled']) }}
                        @else
                        {{ Form::checkbox('settings[webservices_auto_submit]', 1, $service->exists ? @$service->settings['webservices_auto_submit'] : Setting::get('webservices_auto_submit')) }}
                        @endif
                        Sub. Auto. Webservice
                    </label>
                    {!! tip('Submeter automaticamente aos parceiros') !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-3 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('dimensions_required', 1) }}
                        @trans('Obriga Dimensões')
                    </label>
                </div>
            </div>
        </div>
        <div class="row row-0">
            <div class="col-sm-6">
                <div class="checkbox m-t-3 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('allow_kms', 1) }}
                        @trans('Obriga Inserir KM')
                    </label>
                    {!! tip(__('Mostra o campo de inserção dos KM.')) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="btn-group btn-marker btn-group-sm" style="margin: -7px 0px -10px;">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ $service->marker_icon ? asset($service->marker_icon) : asset('assets/img/default/map/marker_blue.svg') }}" style="height: 16px" class="service-marker-icn"/> Idêntificador mapa <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach (trans('admin/shipments.services-map-markers') as $markerCode => $markerName)
                            <li><a href="#"><img src="{{ asset('assets/img/default/map/'.$markerCode.'.svg')}}" style="height: 16px"/> {{ $markerName }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    {{ Form::hidden('marker_icon', null) }}
                </div>
            </div>
        </div>

        <h4 class="form-divider" style="margin-top: 10px">@trans('Limitar por cliente ou Agência')</h4>
        <div class="row row-5">
            <div class="col-xs-12">
                <div class="form-group">
                    {{ Form::label('customers', __('Serviço exclusivo só apenas para os clientes:')) }}
                    {{ Form::select('customers[]', $customers, $assignedCustomers, ['class' => 'form-control search-customers', 'multiple']) }}
                </div>
            </div>
        </div>
        <a href="#" class="select-all-agencies pull-right">@trans('Sel. Todos')</a>
        <label>@trans('Disponibilizar este serviço para:')</label>
        <div class="row row-5">
            <div class="col-xs-12">
                @if($agencies->count() >= 7)
                    <div style="max-height: 155px;overflow: scroll;border: 1px solid #ddd;padding: 0 8px;">
                        @endif
                        @foreach($agencies as $agency)
                            <div class="checkbox m-t-5 m-b-8" style="{{ $agency->source != config('app.source') ? 'display:none' : '' }}">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('agencies[]', $agency->id, null, ['class' => 'row-agency']) }}
                                    <span class="label" style="background: {{ $agency->color }}">{{ $agency->code }}</span> {{ $agency->print_name }}
                                </label>
                            </div>
                        @endforeach
                        @if($agencies->count() >= 12)
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<style>

    .btn-marker button {
        padding: 1px !important;
        background: transparent;
        border: none;
        font-size: 13px !important;
    }

    .table-doc-types td {
        padding: 1px !important;
    }

    .table-doc-types td input[type="text"] {
        height: 29px
    }

    .input-group-money .input-group-addon {
        top: 3px
    }

    .table-doc-types td .input-group-money .input-group-addon {
        top: 0px
    }

    .pack-dim {
        padding: 0 5px;
        text-align: center;
    }
</style>