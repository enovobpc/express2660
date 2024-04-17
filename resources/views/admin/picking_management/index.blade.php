@section('title')
    Gestão Massiva Envios
@stop

@section('content-header')
    Gestão Massiva Envios
@stop

@section('breadcrumb')
<li class="active">Gestão Massiva Envios</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                {{ Form::open(['route' => ['admin.picking.management.store']]) }}
                <div class="row">
                    <div class="col-sm-3 col-md-2">
                        {{--<div class="form-group is-required">
                            {{ Form::label('shipment_trk', 'Código de Envio') }}
                            {{ Form::text('shipment_trk', null, ['class' => 'form-control nospace', 'required']) }}
                        </div>--}}
                        <div class="form-group is-required">
                            <label>Códigos de Envio {!! tip('Coloque o cursor ativo na caixa de texto e utilize o leitor de códigos de barras para picar a mercadoria.') !!}</label>
                            <div class="input-group">
                                {{ Form::text('tracking_code', null, ['class' => 'form-control nospace number', 'required']) }}
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modal-select-shipments" data-empty="1">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('provider_id', 'Fornecedor') }}
                                    {{ Form::select('provider_id', ['' => '- Não alterar -'] + $providers, null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('status_id', 'Alterar estado para') }}
                                    {{ Form::select('status_id', ['' => '- Não alterar -'] + $status, null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('date', 'Data') }}
                                    <div class="input-group">
                                        {{ Form::text('date', null, ['class' => 'form-control datepicker', 'placeholder' => 'Não Alterar']) }}
                                        <div class="input-group-addon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('weight', 'Peso Real') }}
                                    {{ Form::text('weight', null, ['class' => 'form-control', 'placeholder' => 'Não Alterar']) }}
                                </div>
                            </div>
                            @if(Setting::get('shipments_custom_provider_weight'))
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('provider_weight', 'Peso Etq:') }}
                                    {{ Form::text('provider_weight', null, ['class' => 'form-control', 'placeholder' => 'Não Alterar']) }}
                                </div>
                            </div>
                            @endif
                        </div>
                        <hr style="margin: 5px 0 15px"/>
                        <div class="form-group">
                            <div class="checkbox m-t-5">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('reference', 1) }}
                                    Ler também referência
                                </label>
                            </div>
                            <div class="checkbox m-t-5">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('play_sound', 1, true) }}
                                    Aviso sonoro
                                </label>
                            </div>
                            <div class="checkbox m-t-5 m-b-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('play_sound_repeated', 1, true) }}
                                    Aviso se o envio já lido
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-10" style="border-left: 1px dashed #ddd; min-height: 405px; padding-left: 0; padding-right: 10px; margin-bottom: -10px; margin-top: -10px">
                        <div class="nav-tabs-custom" style="box-shadow: none">
                            {{--<ul class="nav nav-tabs">
                                <li class="active"><a href="#tab-readed" data-toggle="tab">Serviços Processados</a></li>
                            </ul>--}}
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-readed">
                                    @include('admin.picking_management.partials.readed_shipments')
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
@stop

@section('styles')
    <style>
        .tr > td{
           /* padding: 2px 2px !important;*/
        }

        .tr .inpt {
            padding: 2px 1px;
        }

        .tr > td > input{
            margin: 3px 2px 3px 2px;
            text-align: center;
            padding: 2px !important;
        }

        .tr select.input-sm {
            height: 30px;
            line-height: 30px;
            margin-top: 4px !important;
        }

        .text-center, .table td.text-center {
            text-align: center;
        }

        .inpt {
            vertical-align: middle;
        }

        .modal-shipment-detail .btn-group-xs>.btn, .btn-xs {
            padding: 3px 5px 3px 5px !important;
            margin-top: 6px;
        }
    </style>
@stop

@section('scripts')
<script type="text/javascript">

    $(document).on('change', '[name="select-all[]"]', function (e) {
        if($(this).is(':checked')) {
            selectRows();
            $(document).find('[name="select[]"]').prop('checked', true);
        } else {
            $('.print-selected-labels').hide();
            $(document).find('[name="select[]"]').prop('checked', false);
        }
    })

    $(document).on('change', '[name=operator_id]', function (e) {
        e.preventDefault();
        if($(document).find('[data-readed-code]').length) {
            $('#modal-change-operator-confirm').modal('show');
        }
    })


    $('.print-selected-labels, .print-manifest').on('click', function(){
        var url = $(this).attr('href');
        markAsPrinted(url);
    })

    function selectRows() {
        var ids = [];
        $(document).find('.selected-codes tr').each(function(){
            if($(this).data('shpid')) {
                ids.push('id[]=' + $(this).data('shpid'))
            }
        })

        query = ids.join('&');

        var url = "{{ route('admin.printer.shipments.labels') }}?" + query
        $('.print-selected-labels').attr('href', url).show();
    }

    function markAsPrinted(url) {
        console.log(url);
        url = url.split('?id[]=');

        url = url[1];
        console.log(url);
        ids = url.split('&id[]=');

        console.log(ids)
        ids.forEach(function(id) {
            $('[data-shpid="'+id+'"]').find('.fa-times-circle')
                .removeClass('text-red')
                .removeClass('fa-times-circle')
                .addClass('fa-check-circle')
                .addClass('text-green')

        })
    }

    $('[name="disable_autosave"]').on('change', function(){
        if($(this).is(':checked')) {
            $('.btn-save').show()
        } else {
            $('.btn-save').hide()
        }
    })

    $('[name=tracking_code]').focusout(function () {
        selectCode($(this).val());
    })

    $('[name=tracking_code]').on('change', function () {
        selectCode($(this).val());
    })

    $('[name=tracking_code]').keypress(function (e) {
        if (e.which == 13) {
            selectCode($(this).val());
        }
        $('[name=tracking_code]').focus();
    });

    $(document).on('click', '.btn-sv-tr', function (e) {
        e.preventDefault();
        var $btn   = $(this);
        var $tr    = $(this).closest('tr');
        var action = $(this).closest('.form').attr('action')
        var params = {
            id : $tr.find('[name="id"]').val(),
            provider : $tr.find('[name="provider"]').val(),
            volumes : $tr.find('[name="bx_volumes"]').val(),
            weight : $tr.find('[name="bx_weight"]').val(),
            label_weight : $tr.find('[name="label_weight"]').val()
        }

        $btn.button('loading')
        $.post(action, params, function (data) {

            if(data.result) {
                if(data.printLabel) {
                    window.open(data.printLabel, '_blank')
                }
                Growl.success(data.feedback);
            } else {
                Growl.error(data.feedback);
            }

        }).always(function () {
            $btn.button('reset');
        })
    });

    $(document).on('keypress', '.tr-inpt', function (e) {
        if (e.which == 13) {
            $(this).closest('tr').find('form').submit()
        }
    });

    $('form').on('keyup keypress', function (e) {
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

        var exists = false;
        var html;
        var manifestUrl     = "{{ route('admin.printer.shipments.labels') }}";
        var providerId      = $('[name=provider_id]').val();
        var statusId        = $('[name=status_id]').val();
        var weight          = $('[name=weight]').val();
        var date            = $('[name=date]').val();
        var providerWeight  = $('[name=provider_weight]').val();
        var playSound       = $('[name=play_sound]').is(':checked');
        var playSoundRepeated = $('[name=play_sound_repeated]').is(':checked');

        if (fullTrkCode != '') {

            $('.scanner-result-block').show();
            $('.wellcome-image').hide();

            //trkCode = fullTrkCode.split('#')
            trkCode = trkCode.trim();
            if(trkCode.length == 15 || trkCode.length == 18) { //codigos antigos xxxxxxxxxxxx001 = 15 digitos, codigos novos = 18
                trkCode = trkCode.substr(0, 12)
            }

            $('.selected-codes tr').each(function () {
                if ($(this).data('id') == trkCode || $(this).data('readed-code') == trkCode) {
                    exists = true;
                }
            })

            if(exists) {
                $(document).find('[data-readed-code="' + trkCode + '"] .fa').removeClass('fa-exclamation-triangle').addClass('fa-spin fa-circle-notch');
                checkList = $(document).find('[data-readed-code="' + trkCode + '"]').find('[name="check_list"]').val()
            } else {
                checkList = '';
                html = '<tr data-id="'+trkCode+'" data-readed-code="'+trkCode+'"><td>'+trkCode+'</td><td colspan="4"><i class="fas fa-spin fa-circle-notch"></i> A validar...</td></tr>';
                $('.selected-codes tr:first').after(html);
            }

            $.post("{{ route('admin.picking.management.get.shipment') }}", {
                'code': fullTrkCode,
                'check_list': checkList,
                'provider': providerId,
                'status' : statusId,
                'weight': weight,
                'provider_weight': providerWeight,
                'date' : date
            }, function (data) {
                $(document).find('[data-id="' + data.trk + '"]').replaceWith(data.html);
                $(document).find('[data-readed-code="' + data.readedTrk + '"]').replaceWith(data.html);

                if(data.allRead) {
                    if($(document).find('.selected-codes [data-trk="' + data.trk + '"]').length) {
                        var totalUnread = parseInt($('.total-unread-shipments').html());
                        totalUnread = totalUnread - 1;
                        $('.total-unread-shipments').html(totalUnread)
                    }
                    $(document).find('.pending-shipments [data-shipment-trk="' + data.trk + '"]').remove();
                }

                $(document).find('.pending-shipments [data-shipment-trk="' + data.trk + '"] .count-readed').html(data.totalRead);
                $(document).find('.pending-shipments [data-shipment-trk="' + data.trk + '"] .count-readed').closest('tr').css('color', '#f39c12')

                $(document).find('[data-id="' + data.trk + '"]').find('.select2').select2(Init.select2())

                if(data.result) {
                    if(playSoundRepeated && data.alreadyReaded){
                        Notifier.soundWarning();
                    } else if(playSound) {
                        Notifier.soundOk();
                    }
                } else {
                    Notifier.soundError();
                }

                ids = [];
                $('tr[data-shpid]').each(function(){
                    ids.push('id[]='+$(this).data('shpid'))
                })

                manifestUrl+='?' + ids.join('&')
                $('.print-manifest').attr('href', manifestUrl);

                var totalWeight  = 0;
                var totalVolumes = 0;
                var totalCharge  = 0;
                var totalCod     = 0;
                $(document).find('[data-readed-code]').each(function () {
                    var volumes = parseInt($(this).data('volumes'));
                    var weight  = parseFloat($(this).data('weight'));
                    var charge  = parseFloat($(this).data('charge'));
                    var cod     = parseFloat($(this).data('cod'));

                    volumes = isNaN(volumes) ? 0 : volumes;
                    weight  = isNaN(weight) ? 0 : weight;
                    charge  = isNaN(charge) ? 0 : charge;
                    cod     = isNaN(cod) ? 0 : cod;

                    totalWeight+= weight;
                    totalVolumes+= volumes;
                    totalCharge+= charge;
                    totalCod+= cod;
                })

                $('.ttl-vol').html(totalVolumes);
                $('.ttl-kg').html(totalWeight.toFixed(2));
                $('.ttl-charge').html(totalCharge.toFixed(2));
                $('.ttl-cod').html(totalCod.toFixed(2));
                $('.ttl-total').html((totalCharge + totalCod).toFixed(2));

            }).fail(function () {
                $(document).find('[data-readed-code="' + trkCode + '"] td').css('color', 'red')
            })
        }
        $('[name=tracking_code]').val('');
    }
</script>
@stop