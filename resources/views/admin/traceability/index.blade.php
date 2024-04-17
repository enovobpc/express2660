@section('title')
    Rastreabilidade
@stop

@section('content-header')
    Rastreabilidade
@stop

@section('breadcrumb')
    <li class="active">Rastreabilidade</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    {{ Form::open(['route' => ['admin.traceability.store']]) }}
                    <div class="row">
                        <div class="col-sm-3">
                            {{-- <div class="row row-5">
                                <div class="col-sm-5">
                                    <div class="form-group is-required">
                                        {{ Form::label('read_point', 'Leitura:') }}
                                        {{ Form::select('read_point', ['' => ''] + trans('admin/traceability.read-points'), null, ['class' => 'form-control select2','required']) }}
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="form-group is-required">
                                        {{ Form::label('agency_id', 'Armazém:') }}
                                        {{ Form::select('agency_id', count($agencies) == 1 ? $agencies : ['' => ''] + $agencies, null, ['class' => 'form-control select2','required']) }}
                                    </div>
                                </div>
                            </div> --}}

                            <div class="form-group is-required">
                                <a href="{{ route('admin.traceability.events.index') }}" data-toggle="modal" data-target="#modal-remote-lg" class="pull-right">
                                    <small><i class="fas fa-cog"></i> Gerir</small>
                                </a>
                                {{ Form::label('event_id', 'Leitura') }}
                                {!! Form::selectWithData('event_id', $events, null, ['class' => 'form-control select2', 'required']) !!}
                                {{ Form::hidden('read_point') }}
                                {{ Form::hidden('agency_id') }}
                                {{ Form::hidden('status_id') }}
                            </div>

                            <div class="form-group">
                                <a href="{{ route('admin.traceability.locations.index') }}" data-toggle="modal" data-target="#modal-remote-lg" class="pull-right">
                                    <small><i class="fas fa-cog"></i> Gerir</small>
                                </a>
                                {{ Form::label('location_id', 'Local armazenagem') }}
                                <span class="loading-locations" style="display: none">
                                    <i class="fas fa-spin fa-circle-notch"></i>
                                </span>
                                {{ Form::select('location_id', [], null, ['class' => 'form-control select2']) }}
                            </div>

                            @if (!Auth::user()->hasRole('operador'))
                                {{-- <div class="form-group is-required">
                                    {{ Form::label('status_id', 'Alterar envios para o estado') }}
                                    {{ Form::select('status_id', ['' => ''] + $status, null, ['class' => 'form-control select2', 'required']) }}
                                </div>
                                <div class="form-group is-required incidence-options" style="display: none">
                                    {{ Form::label('incidence_id', 'Motivo de Incidência') }}
                                    {{ Form::select('incidence_id', ['' => ''] + $incidences, null, ['class' => 'form-control select2']) }}
                                </div> --}}

                                @if (!$vehicles || ($vehicles && $trailers))
                                    <div class="form-group is-required">
                                        {{ Form::label('operator_id', 'Motorista') }}
                                        {{ Form::select('operator_id', $operators, null, ['class' => 'form-control select2', 'required']) }}
                                    </div>
                                @endif

                                @if ($vehicles && !$trailers)
                                    <div class="row row-5">
                                        <div class="col-sm-7">
                                            <div class="form-group is-required">
                                                {{ Form::label('operator_id', 'Motorista') }}
                                                {{ Form::select('operator_id', $operators, null, ['class' => 'form-control select2', 'required']) }}
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                {{ Form::label('vehicle', 'Viatura') }} {!! tip('Ao selecionar a viatura, os envios lidos serão associados à viatura indicada.') !!}
                                                {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2', 'required']) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($vehicles && $trailers)
                                    <div class="row row-5">
                                        <div class="col-sm-6">
                                            <div class="form-group is-required">
                                                {{ Form::label('vehicle', 'Viatura') }} {!! tip('Ao selecionar a viatura, os envios lidos serão associados à viatura indicada.') !!}
                                                {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2', 'required']) }}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('trailer', 'Reboque') }}
                                                {{ Form::select('trailer', ['' => ''] + $trailers, null, ['class' => 'form-control select2']) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{ Form::hidden('operator_id', Auth::user()->id) }}
                                {{-- <div class="form-group is-required">
                                    {{ Form::label('status_id', 'Alterar envios para o estado') }}
                                    {{ Form::select('status_id', ['' => ''] + $status, null, ['class' => 'form-control select2', 'required']) }}
                                </div>
                                <div class="form-group is-required incidence-options" style="display: none">
                                    {{ Form::label('incidence_id', 'Motivo de Incidência') }}
                                    {{ Form::select('incidence_id', ['' => ''] + $incidences, null, ['class' => 'form-control select2']) }}
                                </div> --}}
                            @endif

                            <div class="form-group">
                                <label>Selecionar códigos de Envio {!! tip('Coloque o cursor ativo na caixa de texto e utilize o leitor de códigos de barras para picar a mercadoria.') !!}</label>
                                <div class="input-group">
                                    {{ Form::text('tracking_code', null, ['class' => 'form-control', 'disabled']) }}
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" data-toggle="modal"
                                            data-target="#modal-select-shipments" data-empty="1">
                                            <i class="fas fa-external-link-square-alt"></i> Manual
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="checkbox m-t-5">
                                    <label style="padding-left: 0">
                                        {{ Form::checkbox('disable_autosave', 1) }}
                                        Gravar alterações só no fim da picagem
                                        {!! tip('Se ativar esta opção, o programa não irá gravar automáticamente a alteração estados de cada vez que picar um envio. Poderá picar toda a mercadoria e gravar no final.') !!}
                                    </label>
                                </div>
                                <div class="checkbox m-t-5">
                                    <label style="padding-left: 0">
                                        {{ Form::checkbox('read_provider_trk', 1) }}
                                        Permitir ler código barras fornecedor
                                    </label>
                                </div>
                                <div class="checkbox m-t-5">
                                    <label style="padding-left: 0">
                                        {{ Form::checkbox('play_sound', 1, true) }}
                                        Aviso sonoro quando o volume é lido
                                    </label>
                                </div>
                                <div class="checkbox m-t-5 m-b-0">
                                    <label style="padding-left: 0">
                                        {{ Form::checkbox('play_sound_repeated', 1, true) }}
                                        Aviso sonoro se o volume já tiver sido lido
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success btn-save"
                                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar..."
                                style="display: none">
                                <i class="fas fa-check"></i> Gravar alteração de estados
                            </button>
                        </div>
                        <div class="col-sm-9"
                            style="border-left: 1px dashed #ddd; min-height: 405px; padding-left: 0; padding-right: 10px; margin-bottom: -10px; margin-top: -10px">
                            <div class="nav-tabs-custom" style="box-shadow: none">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab-readed" data-toggle="tab">Volumes Lidos</a>
                                    </li>
                                    <li><a href="#tab-pending" data-toggle="tab">Volumes Pendentes de Leitura <span
                                                class="badge badge-default total-unread-shipments"
                                                style="margin-top: -4px; display: none"></span></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab-readed">
                                        @include('admin.traceability.partials.readed_shipments')
                                    </div>
                                    <div class="tab-pane" id="tab-pending">
                                        @include('admin.traceability.partials.readed_shipments')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    @include('admin.traceability.modals.shipments')
    @include('admin.traceability.modals.change_operator_confirm')
@stop

@section('scripts')
    <script type="text/javascript">

        $('[name="event_id"]').on('change', function(){

            var event  = $(this).val();

            $optionSelected = $(this).find('option:selected');
            var agency   = $optionSelected.data('agency');
            var action   = $optionSelected.data('action');
            var status   = $optionSelected.data('status');
            var location = $optionSelected.data('location');
            
            $('[name=read_point]').val(action).trigger('change');
            $('[name=agency_id]').val(agency).trigger('change');
            $('[name=status_id]').val(status).trigger('change');

            $('.loading-locations').show();
            $.post('{{ route("admin.traceability.get.locations") }}', {agency: agency, location: location}, function(data){
                if(data) {
                    $('[name=location_id]').html(data);
                }
            }).always(function(){
                $('.loading-locations').hide();
            })
        })

        $('[name=status_id], [name=read_point], [name=operator_id], [name=agency_id]').change(function() {

            if ($('[name=status_id]').val() != '' &&
                $('[name=read_point]').val() != '' &&
                $('[name=agency_id]').val() != '' &&
                $('[name=operator_id]').val() != '') {
                $('[name=tracking_code]').prop('disabled', false)
            } else {
                $('[name=tracking_code]').prop('disabled', true)
            }

            if ($('[name=status_id]').val() == '9') {
                $('[name=incidence_id]').val('').prop('required', true).trigger('change')
                $('.incidence-options').show()
            } else {
                $('[name=incidence_id]').val('').prop('required', false).trigger('change')
                $('.incidence-options').hide()
            }
        })

        $(document).on('change', '[name=operator_id]', function(e) {
            e.preventDefault();
            if ($(document).find('[data-readed-code]').length) {
                $('#modal-change-operator-confirm').modal('show');
            }
        })

        $('#modal-change-operator-confirm [data-answer]').on('click', function() {
            if ($(this).data('answer') == '1') {
                $(document).find('[data-readed-code]').remove();
                $('.ttl-vol').html(0);
                $('.ttl-kg,.ttl-charge,.ttl-cod,.ttl-total').html('0.00');
            }
            $('#modal-change-operator-confirm').modal('hide');
        });

        $(document).on('change', '[name=read_point], [name=agency_id], [name=date_min], [name=date_max]', function() {
            var read_point = $('[name=read_point]').val();
            var agency_id = $('[name=agency_id]').val();
            var date_min = $('[name=date_min]').val();
            var date_max = $('[name=date_max]').val();

            $(document).find('.datepicker').datepicker('hide');
            if (agency_id != '' && read_point != '') {
                $('#tab-pending').html(
                    '<div class="m-t-20"><p class="text-muted text-center"><i class="fas fa-spin fa-circle-notch"></i> A carregar envios...</p></div>'
                );

                $.post('{{ route('admin.traceability.list.shipments') }}', {
                    agency_id: agency_id,
                    read_point: read_point,
                    date_min: date_min,
                    date_max: date_max
                }, function(data) {
                    $('#tab-pending').html(data.html);
                    $('.total-unread-shipments').html(data.totalShipments).show();
                    $('.datepicker').datepicker(Init.datepicker());
                    $('.select2').not('.modal .select2').select2(Init.select2());

                }).fail(function() {
                    $('#tab-pending').html(
                        '<div class="m-t-20"><p class="text-red text-center"><i class="fas fa-exclamation-triangle"></i> Ocorreu um erro ao carregar a lista.</p></div>'
                    );
                })
            }
        })

        $('[name="disable_autosave"]').on('change', function() {
            if($(this).is(':checked')) {
                $('.btn-save').show()
            } else {
                $('.btn-save').hide()
            }
        })

        $('[name=tracking_code]').focusout(function() {
            selectCode($(this).val());
        })

        $('[name=tracking_code]').on('change', function() {
            selectCode($(this).val());
        })

        $('[name=tracking_code]').keypress(function(e) {
            if (e.which == 13) {
                selectCode($(this).val());
            }
            $('[name=tracking_code]').focus();
        });

        $('form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        function selectCode(trkCode) {

            $('[name=read_point]').prop('disabled', true);

            fullTrkCode = trkCode;
            fullTrkCode = fullTrkCode.trim();

            var manifestUrl = "{{ route('admin.printer.shipments.delivery-map') }}?operator=" + $('[name=operator_id]').val();
            manifestUrl += "&vehicle=" + ($('[name=vehicle]').val() ? $('[name=vehicle]').val() : '');
            manifestUrl += "&trailer=" + ($('[name=trailer]').val() ? $('[name=trailer]').val() : '');


            var exists = false;
            var html;
            var eventId     = $('[name=event_id]').val();
            var operatorId  = $('[name=operator_id]').val();
            var vehicle     = $('[name=vehicle]').val();
            var trailer     = $('[name=trailer]').val();
            var agency      = $('[name=agency_id]').val();
            var location    = $('[name=location_id]').val();
            var statusId    = $('[name=status_id]').val();
            var incidenceId = $('[name=incidence_id]').val();
            var readPoint   = $('[name=read_point]').val();
            var autosave    = !$('[name=disable_autosave]').is(':checked');
            var playSound   = $('[name=play_sound]').is(':checked');
            var playSoundRepeated = $('[name=play_sound_repeated]').is(':checked');
            var providerTrk = $('[name=read_provider_trk]').is(':checked');

            if (fullTrkCode != '') {

                $('.scanner-result-block').show();
                $('.wellcome-image').hide();

                //trkCode = fullTrkCode.split('#')
                trkCode = trkCode.trim();
                if (trkCode.length == 15 || trkCode.length == 18) { //codigos antigos xxxxxxxxxxxx001 = 15 digitos, codigos novos = 18
                    trkCode = trkCode.substr(0, 12)
                }

                if (trkCode.length == 26) { //codigos tipsa & sending
                    trkCode = trkCode.substr(0, 12); //sending
                }

                $('.selected-codes tr').each(function() {
                    if ($(this).data('id') == trkCode || $(this).data('readed-code') == trkCode) {
                        exists = true;
                    }
                })

                if (exists) {
                    $(document).find('[data-readed-code="' + trkCode + '"] .fa').removeClass('fa-exclamation-triangle')
                        .addClass('fa-spin fa-circle-notch');
                    checkList = $(document).find('[data-readed-code="' + trkCode + '"]').find('[name="check_list"]').val()
                } else {
                    checkList = '';
                    html = '<tr data-id="' + trkCode + '" data-readed-code="' + trkCode + '"><td>' + trkCode +
                        '</td><td colspan="4"><i class="fas fa-spin fa-circle-notch"></i> A validar...</td></tr>';
                    $('.selected-codes tr:first').after(html);
                }

                $.post("{{ route('admin.traceability.get.shipment') }}", {
                    'code': fullTrkCode,
                    'check_list': checkList,
                    'read_point': readPoint,
                    'event': eventId,
                    'operator': operatorId,
                    'vehicle': vehicle,
                    'trailer': trailer,
                    'agency': agency,
                    'location': location,
                    'status': statusId,
                    'autosave': autosave,
                    'incidence': incidenceId,
                    'provider_trk': providerTrk
                }, function(data) {
                    $(document).find('[data-id="' + data.trk + '"]').replaceWith(data.html);
                    $(document).find('[data-readed-code="' + data.readedTrk + '"]').replaceWith(data.html);

                    if(data.result && $(document).find('[data-id="' + data.trk + '"]').length > 1) {

                        $target = $(document).find('[data-id="' + data.trk + '"]:last');

                        totalIndividualVols = $(document).find('[data-id="' + data.trk + '"]').length;
                        totalVolumes = $target.data('volumes');

                        if(totalIndividualVols < totalVolumes) {
                            html = '<i class="fas fa-exclamation-triangle"></i> ' + totalIndividualVols+'/'+totalVolumes;
                            $target.find('.reader-vols').html(html).addClass('text-red').removeClass('text-green');
                            $target.css('background-color', '#ff000036');
                        } else {
                            html = '<i class="fas fa-check"></i> ' + totalVolumes+'/'+totalVolumes;
                            $target.find('.reader-vols').html(html).removeClass('text-red').addClass('text-green');
                            $target.css('background-color', '');
                        }

                        $(document).find('[data-id="' + data.trk + '"]').not(':last').hide()
                    }

                    if (data.allRead) {
                        if ($(document).find('.selected-codes [data-trk="' + data.trk + '"]').length) {
                            var totalUnread = parseInt($('.total-unread-shipments').html());
                            totalUnread = totalUnread - 1;
                            $('.total-unread-shipments').html(totalUnread)
                        }
                        $(document).find('.pending-shipments [data-shipment-trk="' + data.trk + '"]').remove();
                    }

                    if (data.hasDelnext == true) {
                        $('#print-devolution-labels').css("display", "initial")
                    }

                    $(document).find('.pending-shipments [data-shipment-trk="' + data.trk + '"] .count-readed')
                        .html(data.totalRead);
                    $(document).find('.pending-shipments [data-shipment-trk="' + data.trk + '"] .count-readed')
                        .closest('tr').css('color', '#f39c12')

                    if (data.result) {
                        if (playSoundRepeated && data.alreadyReaded) {
                            Notifier.soundWarning();
                        } else if (playSound) {
                            Notifier.soundOk();
                        }
                    } else {
                        Notifier.soundError();
                    }

                    $('tr[data-id]').each(function() {
                        manifestUrl += '&trk[]=' + $(this).data('id');
                    })

                    $('.print-manifest').attr('href', manifestUrl);

                    if (statusId) {
                        manifestUrl += '&status_id=' + statusId;
                    }
                    if (agency) {
                        manifestUrl += '&agency_id=' + agency;
                    }
                    $('.print-manifest-and-save').attr('href', manifestUrl + '&save=true');

                    var totalWeight = 0;
                    var totalVolumes = 0;
                    var totalCharge = 0;
                    var totalCod = 0;
                    $(document).find('[data-readed-code]').each(function() {
                        var volumes = parseInt($(this).data('volumes'));
                        var weight = parseFloat($(this).data('weight'));
                        var charge = parseFloat($(this).data('charge'));
                        var cod = parseFloat($(this).data('cod'));

                        volumes = isNaN(volumes) ? 0 : volumes;
                        weight = isNaN(weight) ? 0 : weight;
                        charge = isNaN(charge) ? 0 : charge;
                        cod = isNaN(cod) ? 0 : cod;

                        totalWeight += weight;
                        totalVolumes += volumes;
                        totalCharge += charge;
                        totalCod += cod;
                    })

                    $('.ttl-vol').html(totalVolumes);
                    $('.ttl-kg').html(totalWeight.toFixed(2));
                    $('.ttl-charge').html(totalCharge.toFixed(2));
                    $('.ttl-cod').html(totalCod.toFixed(2));
                    $('.ttl-total').html((totalCharge + totalCod).toFixed(2));

                }).fail(function() {
                    $(document).find('[data-readed-code="' + trkCode + '"] td').css('color', 'red')
                })
            }
            $('[name=tracking_code]').val('');
        }

        $(document).on('click', '[data-target="#modal-select-shipments"]', function() {

            var $tab = $(this);

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);
                var oTable = $('#datatable-shipments').DataTable({
                    columns: [{
                            data: 'tracking_code',
                            name: 'tracking_code',
                            visible: false
                        },
                        {
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'sender_name',
                            name: 'sender_name'
                        },
                        {
                            data: 'recipient_name',
                            name: 'recipient_name'
                        },
                        {
                            data: 'service_id',
                            name: 'service_id',
                            searchable: false
                        },
                        {
                            data: 'volumes',
                            name: 'volumes',
                            searchable: false
                        },
                        {
                            data: 'status_id',
                            name: 'status_id',
                            searchable: false
                        },

                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'sender_zip_code',
                            name: 'sender_zip_code',
                            visible: false
                        },
                        {
                            data: 'sender_city',
                            name: 'sender_city',
                            visible: false
                        },
                        {
                            data: 'recipient_zip_code',
                            name: 'recipient_zip_code',
                            visible: false
                        },
                        {
                            data: 'recipient_city',
                            name: 'recipient_city',
                            visible: false
                        },
                    ],
                    ajax: {
                        url: "{{ route('admin.traceability.shipments.datatable') }}",
                        type: "POST",
                        data: function(d) {
                            d.sender_agency = $('select[name=sender_agency]').val()
                            d.recipient_agency = $('select[name=recipient_agency]').val()
                            d.provider = $('select[name=provider]').val()
                            d.operator = $('select[name=operator]').val()
                            d.status = $('select[name=status]').val()
                        },
                        beforeSend: function() {
                            Datatables.cancelDatatableRequest(oTable)
                        },
                        complete: function() {
                            Datatables.complete()
                        }
                    }
                });

                $('.filter-datatable').on('change', function(e) {
                    oTable.draw();
                    e.preventDefault();
                });
            }
        });


        $(document).on('click', '.code-read', function() {
            var trkCode = $(this).data('trk');

            $(this).prop('disabled', true);
            $('[name=tracking_code]').val(trkCode).trigger('change');
        })
    </script>
@stop
