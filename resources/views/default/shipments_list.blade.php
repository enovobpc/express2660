@extends('layouts.api_docs')

@section('title')
    Consulta de Envios
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    {{ Form::open([ 'method' => 'get']) }}
                    <div class="form-group">
                        {{ Form::label('albaran', 'Albarán') }}<br/>
                        {{ Form::text('albaran', null) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('min_date', 'Fecha Min') }}<br/>
                        {{ Form::date('min_date', null, ['class' => 'datepicker']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('max_date', 'Fecha Max') }}<br/>
                        {{ Form::date('max_date', null, ['class' => 'datepicker']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('status', 'Estado') }}<br/>
                        {{ Form::select('status', ['' => 'Todos'] + $statusList, null) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('reco', 'Tipo') }}<br/>
                        {{ Form::select('reco', ['' => 'Todos', '0' => 'Envío','1' => 'Recogida'], null) }}
                    </div>
                    @if(config('app.source') == 'asfaltolargo')
                    <div class="form-group">
                        {{ Form::label('delegacion', 'Delegacion') }}<br/>
                        {{ Form::select('delegacion', ['' => 'Todos', '606' => '606 Beiras', '610' => '610 Viseu'], null) }}
                    </div>
                    @endif
                    <div class="form-group">
                        <br/>
                        <button type="submit" class="btn btn-default">Buscar</button>
                    </div>
                    <div class="form-group">
                        <br/>
                        <a href="{{ route('sending.shipments.list', 'E7LfgV61rMDenTN8KnGhIw1V9jvZ3NjYJDBxH5KK') }}" class="btn">Limpiar</a>
                    </div>
                    {{ Form::close() }}
                    <div style="clear: both"></div>
                    <hr/>
                    <p style="margin: 0; font-weight: bold; color: #000">Sincronizar Ficheros</p>
                    {{ Form::open(['method' => 'GET']) }}
                    <div class="form-group">
                        {{ Form::label('action', 'Acion') }}<br/>
                        {{ Form::select('action', ['' => '', 'import' => 'Importar envíos', 'export-tracking' => 'Exportar Estados', 'export-traceability' => 'Exportar Lecturas', 'export-refunds' => 'Exportar Reembolsos'], null, ['required']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('agency', 'Delegacion') }}<br/>
                        {{ Form::text('agency', null, ['required']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('date', 'Fecha') }}<br/>
                        {{ Form::date('date', \Request::get('date') ? \Request::get('date') : date('Y-m-d'), ['class' => 'datepicker', 'required']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('download', 'Download') }}<br/>
                        {{ Form::select('download', ['' => 'Submit FTP', 'file' => 'Download File'], null) }}
                    </div>
                    <div class="form-group">
                    <button type="submit" class="btn btn-default" style="padding: 2px 10px; margin-top: 17px;">Sincronizar</button>
                    </div>
                    <div class="form-group">
                    @if(@$syncFile == '2')
                        <p style="color: green; margin-top: 17px;">SYNC OK!</p>
                    @elseif(@$syncFile == '1')
                        <p style="color: red; margin-top: 17px;">SYNC FAIL!</p>
                    @endif
                    </div>
                    {{ Form::close() }}
                    <div style="clear: both"></div>
                    <hr/>
                    <h4>{{ $shipments->count() }} envíos
                        <small style="font-weight: normal">| {{ $pendingTotal }} no leídos | {{ $distribuitionTotal }} Reparto | {{ $incidenceTotal }} Incidencias | {{ $deliveredTotal }} Entregados | {{ ($shipments->count() - ($distribuitionTotal+$deliveredTotal+$pendingTotal+$incidenceTotal)) }} Otros estados</small></h4>
                    <table class="table-condensed table-bordered table-hover w-100">
                        <tr>
                            <th>Albarán</th>
                            <th>Referencia</th>
                            <th>Remitente</th>
                            <th>Destinatario</th>
                            <th>Vol/Kg</th>
                            <th>Fecha/cobro</th>
                            <th>Estado</th>
                            <th style="width: 100px">Importado</th>
                            <th style="width: 50px"></th>
                        </tr>
                        @foreach($shipments as $shipment)
                        <tr>
                            <td class="w-1">
                                <b>{{ $shipment->provider_tracking_code }}

                                    @if($shipment->is_collection)
                                    <span style="color: #0a4f77">[RECO]</span>
                                    @endif
                                </b>
                                <br/>
                                <small><b>{{ $shipment->provider_sender_agency }}</b> > <span style="color: #0a58ca; font-weight: bold">{{ $shipment->provider_recipient_agency }}</span></small>
                            </td>
                            <td>
                                {{ $shipment->reference2 }}<br/>
                                {{--{{ $shipment->reference3 }}--}}
                            </td>
                            <td>
                                {{ $shipment->sender_name }}<br/>
                                <small>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</small>
                            </td>
                            <td>
                                {{ $shipment->recipient_name }}<br/>
                                <small>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</small>
                            </td>
                            <td class="w-1">
                                {{ $shipment->volumes }}<br/>
                                {{ money($shipment->weight) }}kg
                            </td>
                            <td>
                                {{ $shipment->date }}<br/>
                                {{ $shipment->charge_price > 0.00 ? money($shipment->charge_price) : '' }}
                            </td>
                            <td class="text-center">
                                <span class="label" style="background:{{ @$shipment->status->color }} ">
                                    {{ @$shipment->status->name }}
                                </span>
                                <br/>
                                @if(config('app.source') == 'fozpost')
                                    <small>{{ @$shipment->lastHistory->created_at }}</small>
                                @else
                                    <small>{{ @$shipment->status_date }}</small>
                                @endif
                            </td>
                            <td>
                                {{ @$shipment->created_at }}
                            </td>
                            <th>
                                <a href="/trk/{{ $shipment->tracking_code }}" target="_blank">TRACKING</a>
                            </th>
                        </tr>
                        @endforeach
                    </table>

                    @if($shipments->isEmpty())
                        <h3 style="margin-top: 10px; text-align: center; font-weight: normal">No hay envíos.</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            font-size: 14px;
            padding: 30px !important;
        }

        .label {
            background: #48AD01;
            border-radius: 3px;
            padding: 2px 5px;
            color: #fff;
            font-size: 12px;
            white-space: nowrap;
        }

        th {
            background: #ccc;
        }


        .table-hover tr:hover {
            background: #f2f2f2;
        }

        .form-group {
            float: left;
            padding-right: 20px;
        }

        .form-group .btn {
            padding: 3px 10px;
            margin: -10px 0;
        }

        footer {
            display: none;
        }
    </style>
@stop

@section('scripts')

@stop
