{{ Form::open(['route' => 'admin.trips.shipments.store-selected', 'method' => 'POST', 'class' => 'form-manifest-add-shipment']) }}
<div class="modal-header">
    <h4 class="modal-title">
        @if(app_mode_cargo())
        @trans('Adicionar serviços a viagem')
        @else
        @trans('Adicionar serviços a mapa distribuição')
        @endif
    </h4>
</div>
<div class="modal-body">
    <div class="row row-5 filter-list" style="background: #f2f2f2;
    padding: 15px 15px 0;
    margin: -15px -15px 15px;
    border-bottom: 1px solid #ddd;">
        <div class="col-xs-12">
            <h5 class="m-t-0 m-b-15">
                <i class="fas fa-search"></i> @trans('Filtrar mapas da lista. Apenas são apresentados mapas não finalizados.')<br/>
            </h4>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('code', __('Nº Mapa/Folha'), ['class' => 'control-label']) }}
                {{ Form::text('code', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('start_date', __('Data Início'), ['class' => 'control-label']) }}
                <div class="input-group input-group-money">
                    {{ Form::text('start_date', null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        @if(app_mode_cargo())
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('start_country', __('País Origem'), ['class' => 'control-label']) }}
                {{ Form::select('start_country', [''=>''] + trans('country'), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('end_country', __('País Destino'), ['class' => 'control-label']) }}
                {{ Form::select('end_country', [''=>''] + trans('country'), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        @endif
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('operator', __('Motorista'), ['class' => 'control-label']) }}
                {{ Form::select('operator', [''=>''] + $operators, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('vehicle', __('Viatura'), ['class' => 'control-label']) }}
                {{ Form::select('vehicle', [''=>''] + $vehicles, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="manifests-list" style="height: 300px; border: 1px solid #999; border-radius: 3px; overflow: scroll;">
                @include('admin.trips.modals.add_selected_shipments_table')
            </div>
        </div>
    </div>
    @if($shipmentsWithManifest->count())
        <div class="alert alert-warning m-b-0 m-t-5">
            <p class="m-0">
                @if(app_mode_cargo())
                    <i class="fas fa-info-circle"></i> @trans('Atenção, :total das cargas já estão já associadas a outras viagens. Serão movidos para a viagem que selecionar caso não estejam entregues.', ['total' => $shipmentsWithManifest->count()])
                @else
                    <i class="fas fa-info-circle"></i> @trans('Atenção, :total dos serviços já está já associados a outro mapa de distribuição. Serão movidos para o mapa que selecionar caso não estejam entregues.', ['total' => $shipmentsWithManifest->count()])
                @endif
            </p>
        </div>
    @endif
</div>
<div class="modal-footer">
    {{ Form::hidden('shipments', implode(',', $ids)) }}
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Cancelar')</button>
    <button type="submit" class="btn btn-primary" data-loading-text="@trans('Aguarde...')">@trans('Adicionar ao mapa selecionado')</button>
</div>
{{ Form::close() }}

<style>
    .modal table tr {
        cursor: pointer;
    }

    .modal table tr.active td {
        background: #abd9ff;
    }

    .modal table tr.active:hover td {
        background: #abd9ff !important;
    }
</style>
<script>
    $('.form-manifest-add-shipment .select2').select2(Init.select2());
    $('.form-manifest-add-shipment .datepicker').datepicker(Init.datepicker());

    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $(document).on('click', '.table-maps tr', function(){
        $('.form-manifest-add-shipment .table-maps tr').removeClass('active');
        $('.form-manifest-add-shipment [name="trip_id"]').prop('checked', false).trigger('change');

        $(this).addClass('active');
        $(this).find('[name="trip_id"]').prop('checked', true).trigger('change');
    })

    $('.form-manifest-add-shipment .filter-list input, .form-manifest-add-shipment .filter-list select').on('change', function(e){
        e.preventDefault();

        $('.form-manifest-add-shipment .manifests-list').html('<div class="text-center m-t-140 text-muted"><i class="fas fa-spin fa-circle-notch"></i> A carregar...</div>')

        inputData = {
            'filter': true,
            'code' : $('.modal [name="code"]').val(),
            'start_date' : $('.form-manifest-add-shipment [name="start_date"]').val(),
            'start_country' : $('.form-manifest-add-shipment [name="start_country"]').val(),
            'end_country' : $('.form-manifest-add-shipment [name="end_country"]').val(),
            'operator' : $('.form-manifest-add-shipment [name="operator"]').val(),
            'vehicle' : $('.form-manifest-add-shipment [name="vehicle"]').val(),
            'id': $('.form-manifest-add-shipment [name="shipments"]').val().split(','),
        }

        $.get("{{ route('admin.trips.shipments.add-selected') }}", inputData, function(data){
            $('.form-manifest-add-shipment .manifests-list').html(data.html);
        })
    })

    $('.form-manifest-add-shipment').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $(this);
        var $btn = $form.find('button[type=button]');

        $btn.button('loading');

        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: 'POST',
            success: function(data) {
                if(data.result) {
                    if(typeof oTable != 'undefined') {
                        oTable.draw(false);
                    }

                    if($('.trip-content .shipments-table').length) {
                        tripRefreshShipmentsList();
                    }

                    $('#modal-remote-lg').modal('hide');
                    Growl.success(data.feedback);
                    
                } else {
                    Growl.error(data.feedback);
                }
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $btn.button('reset');
        });
    });


</script>