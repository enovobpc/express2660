<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title text-white">
        @if($shipment->is_collection)
            Pedido de Recolha #{{ $shipment->tracking_code }}
        @else
            @if(in_array(Setting::get('app_mode'), ['cargo', 'freight']))
                Dossier #{{ $shipment->tracking_code }}
            @else
                Envio #{{ $shipment->tracking_code }}
            @endif
        @endif
    </h4>
</div>
<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="{{ @$tab ? : 'active' }}">
            <a href="#tab-info" data-toggle="tab">
                Detalhes
            </a>
        </li>
        <li class="{{ @$tab == 'status' ? 'active' : '' }}">
            <a href="#tab-status" data-toggle="tab">
                Tracking
            </a>
        </li>
       {{-- @if($shipment->children_type == 'M' && !empty($groupedShipments))
            <li>
                <a href="#tab-grouped-shipments" data-toggle="tab">
                    Agrupados
                </a>
            </li>
        @endif--}}
        @if(!$shipment->pack_dimensions->isEmpty())
            <li>
                <a href="#tab-dimensions" data-toggle="tab">
                    Dimensões
                </a>
            </li>
        @endif
        <li class="{{ @$tab == 'incidences' ? 'active' : '' }}">
            <a href="#tab-incidences" data-toggle="tab">
                Incidências
            </a>
        </li>
        @if(!$shipment->is_collection)
        <li class="{{ @$tab == 'traceability' ? 'active' : '' }}">
            <a href="#tab-traceability" data-toggle="tab">
                Rastreabilidade
            </a>
        </li>
        @endif
        @if(hasModule('transhipments') || app_mode_cargo())
        <li class="{{ @$tab == 'transhipments' ? 'active' : '' }}">
            <a href="#tab-transhipments" data-toggle="tab">
                Transbordos
            </a>
        </li>
        @endif
        <li>
            <a href="#tab-interventions" data-toggle="tab">
                Intervenções
            </a>
        </li>
        <li>
            <a href="#tab-attachments" data-toggle="tab">
                Anexos
            </a>
        </li>
        @if(Auth::user()->showPrices() && (!$userAgencies || $userAgencies && in_array($shipment->agency_id, $userAgencies)))
            <li>
                <a href="#tab-expenses" data-toggle="tab">
                    Preços
                </a>
            </li>
        @endif

        @if($shipment->webservice_method || $shipment->provider_tracking_code)
        <li>
            @if($shipment->hasSyncError())
            <a href="#tab-webservice" data-toggle="tab" class="text-red">
                <i class="fas fa-exclamation-triangle text-red"></i> Webservice
            </a>
            @else
            <a href="#tab-webservice" data-toggle="tab">
                Webservice
            </a>
            @endif
        </li>
        @endif
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0 modal-shipment modal-shipment-detail">
    @if($shipment->deleted_at)
        <div class="alert alert-danger" style="margin: -15px -15px 15px; border-radius: 0;">
            <h4 class="m-0"><i class="fas fa-exclamation-triangle"></i> Este envio foi eliminado.</h4>
        </div>
    @endif
        <div class="tab-content m-b-0" style="padding-bottom: 10px;">
            <div class="tab-pane {{ @$tab ? '' : 'active' }}" id="tab-info">
                {{-- @if(!Setting::get('shipment_list_detail_master') && ($shipment->children_type == 'M' && !empty($groupedShipments)))
                    @include('admin.shipments.shipments.partials.show.info_grouped')
                @else --}}
                    @include('admin.shipments.shipments.partials.show.info')
                {{-- @endif --}}
            </div>

           {{-- @if($shipment->children_type == 'M' && !empty($groupedShipments))
                <div class="tab-pane" id="tab-grouped-shipments">
                    @include('admin.shipments.shipments.partials.show.grouped_shipments')
                </div>
            @endif
--}}
            @if(hasModule('transhipments') || in_array(Setting::get('app_mode'), ['cargo', 'freight']))
                <div class="tab-pane" id="tab-transhipments">
                    @include('admin.shipments.shipments.partials.show.transhipments')
                </div>
            @endif
            
            @if(!$shipment->pack_dimensions->isEmpty())
            <div class="tab-pane" id="tab-dimensions">
                @include('admin.shipments.shipments.partials.show.dimensions')
            </div>
            @endif

            @if(Auth::user()->showPrices())
            <div class="tab-pane" id="tab-expenses">
                @include('admin.shipments.shipments.partials.show.expenses')
            </div>
            @endif
            
            <div class="tab-pane {{ @$tab == 'history' ? 'active' : '' }}" id="tab-status">
                @include('admin.shipments.shipments.partials.show.history')
                @if($shipment->webservice_method || $shipment->provider_tracking_code)
                <hr style="margin: 5px 0 10px"/>
                <a href="{{ route('admin.shipments.history.sync', $shipment->id) }}" class="btn btn-xs btn-success btn-sync m-t-0"
                   style="padding: 4px 10px !important;"
                   data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> A sincronizar...">
                    <i class="fas fa-sync-alt"></i> Sincronizar estados do envio
                </a>
                @endif
                <a class="btn btn-primary btn-xs m-t-0" href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}" target="__blank">
                    {{ trans('account/global.word.public-tracking') }}
                </a>
            </div>

            <div class="tab-pane {{ @$tab == 'incidences' ? 'active' : '' }}" id="tab-incidences">
                @include('admin.shipments.shipments.partials.show.incidences')
            </div>

            <div class="tab-pane {{ @$tab == 'traceability' ? 'active' : '' }}" id="tab-traceability">
                @include('admin.shipments.shipments.partials.show.traceability')
            </div>

            <div class="tab-pane" id="tab-attachments">
                @include('admin.shipments.shipments.partials.show.attachments')
            </div>

            <div class="tab-pane" id="tab-interventions">
                @include('admin.shipments.shipments.partials.show.interventions')
            </div>


            @if($shipment->webservice_method || $shipment->provider_tracking_code || $shipment->last_webservice_method || $shipment->last_provider_tracking_code)
            <div class="tab-pane" id="tab-webservice">
                @include('admin.shipments.shipments.partials.show.webservice')
            </div>
            @endif
        </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<script>
    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    $('.modal .btn-sync').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);

        $this.button('loading');

        $.post($this.attr('href'), function(data){
           $('#tab-status').html(data.html);
            Growl.success(data.feedback);
            $('[data-toggle="popover"]').popover()
        }).fail(function(){
            Growl.error500();
        }).always(function(){
           $this.button('reset');
        });
    });

    //mark incidence as resolved
    $('[name="incidence_resolve"]').on('change', function(e) {
        e.preventDefault();
        var resolved  = $(this).is(':checked');
        var historyId = $(this).val();

        $.post("{{ route('admin.shipments.incidences.resolve', $shipment->id) }}", {historyId:historyId, resolved: resolved}, function(data){
            Growl.success(data.feedback);
        }).fail(function(){
            Growl.error500();
        });
    });

    $('[data-target="#modal-signature"]').on('click', function(){
        var receiver  = $(this).data('receiver');
        var signature = $(this).data('signature');

        $('#modal-signature').find('img').attr('src', signature);
        $('#modal-signature').find('.receiver b').html(receiver)
        if(receiver != '') {
            $('#modal-signature').find('.receiver').show()
        }
        $('#modal-signature').addClass('in').show();
    })

    $('#modal-signature button').on('click', function(){
        $('#modal-signature').find('img').attr('src', '');
        $('#modal-signature').find('.receiver b').html('')
        $('#modal-signature').find('.receiver').hide();
        $('#modal-signature').removeClass('in').hide();
    })


    $('.modal .create-incidence-resolution').on('click', function(){
        $('.btn-reply-incidence').eq(0).trigger('click');
    })
</script>