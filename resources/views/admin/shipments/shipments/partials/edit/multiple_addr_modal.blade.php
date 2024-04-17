<?php 
$originalShipment = $shipment;
$shipment = $shipmentAddress; 
?>
<div class="modal modal-new-addr" id="modal-{{ $hash }}">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Transporte {{ $shipment->tracking_code }}</h4>
            </div>
            <div class="modal-body">
                <div class="row m-b-10">
                    <div class="col-sm-6">
                        <div class="box box-default box-solid m-b-0">
                            <div class="box-header with-border" style="padding: 5px 10px">
                                <h4 class="box-title"><i class="fas fa-sign-in-alt"></i> Local Carga</h4>
                            </div>
                            <div class="box-body p-10">
                                @include('admin.shipments.shipments.partials.edit.sender_block')
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="box box-default box-solid m-b-0">
                            <div class="box-header with-border" style="padding: 5px 10px">
                                <h4 class="box-title"><i class="fas fa-sign-out-alt"></i> Local Descarga</h4>
                            </div>
                            <div class="box-body p-10">
                                @include('admin.shipments.shipments.partials.edit.recipient_block')
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row labels-right">
                    <div class="col-sm-2 p-r-0">
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('volumes', 'Volumes', ['class' => 'col-sm-4'])}}
                            <div class="col-sm-8 p-r-0">
                                <div class="input-group">
                                    {{ Form::text('volumes', $shipmentAddress->volumes, ['class' => 'form-control number iload input-vol '.$triggerPrice, 'required', 'main-modal' => '.vol'])}}
                                    <div class="input-group-btn btn-group-sm" data-toggle="tooltip" title="Detalhar mercadoria">
                                        <button type="button" class="btn btn-default btn-set-dimensions" data-target="#modal-shipment-dimensions">
                                            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHdpZHRoPSI1MTFweCIgaGVpZ2h0PSI1MTFweCIgdmlld0JveD0iMCAwIDUxMSA1MTEiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDUxMSA1MTEiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8cGF0aCBkPSJNNDIyLjQzMiwyNDcuMDQ2Yy0yLjYyNywwLTUuMiwxLjA1Ny03LjA2OCwyLjkyNGMtMS44NTUsMS44NTYtMi45MTYsNC40MjEtMi45MTYsNy4wNTdjMCwyLjYyOCwxLjA2MSw1LjIsMi45MTYsNy4wNTcKCWMxLjg2OCwxLjg1OSw0LjQzNCwyLjkyOCw3LjA2OCwyLjkyOGMyLjYyNCwwLDUuMTg5LTEuMDY4LDcuMDU3LTIuOTI4YzEuODU1LTEuODU2LDIuOTI0LTQuNDI5LDIuOTI0LTcuMDU3CgljMC0yLjYyNC0xLjA2OC01LjIwMS0yLjkyNC03LjA1N0M0MjcuNjIxLDI0OC4xMDMsNDI1LjA1NiwyNDcuMDQ2LDQyMi40MzIsMjQ3LjA0NnoiLz4KPHBhdGggZD0iTTQyLjE4NywyMzguNjI5YzEuODU2LTEuODU2LDIuOTI0LTQuNDI5LDIuOTI0LTcuMDU3YzAtMi42MjQtMS4wNjgtNS4yMDEtMi45MjQtNy4wNTcKCWMtMS44NjctMS44NTYtNC40MzMtMi45MjQtNy4wNTctMi45MjRjLTIuNjM1LDAtNS4yMDEsMS4wNjgtNy4wNjgsMi45MjRjLTEuODU2LDEuODU1LTIuOTE3LDQuNDMzLTIuOTE3LDcuMDU3CgljMCwyLjYyNywxLjA2MSw1LjIwMSwyLjkxNyw3LjA1N2MxLjg2NywxLjg1OSw0LjQzMywyLjkyOCw3LjA2OCwyLjkyOEMzNy43NTQsMjQxLjU1Nyw0MC4zMiwyNDAuNDg4LDQyLjE4NywyMzguNjI5eiIvPgo8cGF0aCBkPSJNNDk2LjEyNCwzMDMuODM3VjE1MC4yN2MxLjUxNywwLjg1NCwzLjIsMS4yOTEsNC44OTMsMS4yOTFjMi41NTgsMCw1LjExMS0wLjk3NSw3LjA2MS0yLjkyNAoJYzMuODk4LTMuODk4LDMuODk4LTEwLjIxOCwwLTE0LjExN0w0OTMuMiwxMTkuNjQzYy0zLjg5OS0zLjg5NS0xMC4yMTktMy44OTUtMTQuMTE3LDBsLTE0Ljg3NywxNC44NzcKCWMtMy44OTksMy44OTktMy44OTksMTAuMjE5LDAsMTQuMTE3YzMuMjM5LDMuMjQsOC4xNDcsMy43NzgsMTEuOTUzLDEuNjMzdjE1My41NjdjLTMuODA2LTIuMTQ3LTguNzE0LTEuNjEtMTEuOTUzLDEuNjMKCWMtMy44OTksMy44OTgtMy44OTksMTAuMjIzLDAsMTQuMTE3bDE0Ljg3NywxNC44NzdjMS45NDksMS45NDksNC41MDMsMi45MjQsNy4wNTcsMi45MjRjMi41NTgsMCw1LjExLTAuOTc1LDcuMDYxLTIuOTI0CglsMTQuODc3LTE0Ljg3N2MzLjg5NS0zLjg5NSwzLjg5NS0xMC4yMTksMC0xNC4xMTdDNTA0LjgzNywzMDIuMjMsNDk5LjkyOSwzMDEuNjkzLDQ5Ni4xMjQsMzAzLjgzN3oiLz4KPHBhdGggZD0iTTQ1MC45MjMsMzY4LjQyNmwtMTkuOTc3LTYuNTkzYy01LjIzNS0xLjczMS0xMC44ODEsMS4xMTUtMTIuNjA4LDYuMzUxYy0xLjU0Myw0LjY3MSwwLjU2Miw5LjY2MSw0Ljc1MywxMS45MWwtMTQ1LjY5Niw4MAoJYy0wLjAyNy00LjI2NS0yLjgwMy04LjE5NC03LjA5OS05LjQ4OWMtNS4yNzgtMS41OTUtMTAuODUxLDEuMzk2LTEyLjQ0LDYuNjcxbC02LjA3OCwyMC4xNDUKCWMtMS41OTEsNS4yNzgsMS4zOTYsMTAuODQ2LDYuNjc0LDEyLjQzN2wyMC4xNDEsNi4wNzhjMC45NjMsMC4yOTIsMS45MzQsMC40MjksMi44ODksMC40MjljNC4yODksMCw4LjI1LTIuNzg0LDkuNTUzLTcuMQoJYzEuMzQ4LTQuNDc2LTAuNjAyLTkuMTU0LTQuNDQxLTExLjQ0MmwxNDQuNjQ3LTc5LjQyN2MwLjIwNiwzLjk5MiwyLjgwNyw3LjYzOCw2LjgzNCw4Ljk2N2MxLjAzNywwLjM0MywyLjA5NCwwLjUwNywzLjEzMSwwLjUwNwoJYzQuMTksMCw4LjA5My0yLjY1OSw5LjQ3OC02Ljg1N2w2LjU5Mi0xOS45NzhjMC44MzEtMi41MTQsMC42MjgtNS4yNTUtMC41NjEtNy42MTdDNDU1LjUyLDM3MS4wNDksNDUzLjQzOCwzNjkuMjU2LDQ1MC45MjMsMzY4LjQyNgoJeiIvPgo8cGF0aCBkPSJNMTk5LjkxOCw0NTcuMjc1Yy0xLjU5NS01LjI3OS03LjE2Ni04LjI2Ni0xMi40NDEtNi42NzVjLTQuMjk2LDEuMjk5LTcuMDcyLDUuMjI5LTcuMDk5LDkuNDkzbC0xNDUuNjkyLTgwCgljNC4xODctMi4yNDksNi4yOTItNy4yMzksNC43NDktMTEuOTFjLTEuNzI3LTUuMjM1LTcuMzc2LTguMDc4LTEyLjYwNC02LjM1MWwtMTkuOTgsNi41OTNjLTAuMDU1LDAuMDItMC4wOSwwLjA0My0wLjEzMywwLjA2NgoJYy0yLjI1NywwLjc3OS00LjI0NiwyLjM1OC01LjQ4OSw0LjYxNWMtMS4zOCwyLjUxNi0xLjU1Niw1LjM1Ny0wLjczNyw3Ljg4M2MwLjAwNCwwLjAxNiwwLjAwNCwwLjAyNywwLjAwOCwwLjA0M2w2LjU5MywxOS45NzgKCWMxLjM4OCw0LjE5OCw1LjI4Niw2Ljg1Nyw5LjQ3OCw2Ljg1N2MxLjAzNywwLDIuMDkzLTAuMTY0LDMuMTMtMC41MDdjNC4wMjctMS4zMjksNi42MjgtNC45NzUsNi44MzQtOC45NjdsMTQ0LjY0Nyw3OS40MjcKCWMtMy44NCwyLjI5Mi01Ljc5LDYuOTY3LTQuNDQxLDExLjQ0MmMxLjMwMiw0LjMxNSw1LjI2Nyw3LjEsOS41NTIsNy4xYzAuOTU1LDAsMS45MjYtMC4xMzcsMi44ODktMC40MjlsMTkuODQ4LTUuOTg4CgljMC4wMTItMC4wMDQsMC4wMjMtMC4wMDgsMC4wMzUtMC4wMTJsMC4yNTctMC4wNzhjMC4wMjMtMC4wMDQsMC4wMzktMC4wMiwwLjA1OS0wLjAyN2MyLjM5NC0wLjczNyw0LjUxNS0yLjM1NCw1LjgxNi00LjcyMgoJYzEuMzU3LTIuNDY4LDEuNTUyLTUuMjU5LDAuNzgtNy43NTRMMTk5LjkxOCw0NTcuMjc1eiIvPgo8cGF0aCBkPSJNNDMyLjQxMiwxMjcuMDU0aC0wLjAwNHYtOS43NTRjMC0zLjY0MS0xLjk4NC02Ljk5NC01LjE3Ny04Ljc0OUwyMzMuNTc5LDIuMjE2Yy0yLjk5LTEuNjQxLTYuNjE2LTEuNjQxLTkuNjEsMAoJTDMwLjMyLDEwOC41NTFjLTMuMTkzLDEuNzU0LTUuMTc4LDUuMTA3LTUuMTc4LDguNzQ5djguMDM1djY3LjQ3djc4LjUyNnY1NC4wMDN2NC42MzdjMCwzLjY0MiwxLjk4NCw2Ljk5NCw1LjE3OCw4Ljc0OQoJbDE5My42NDgsMTA2LjMzNGMxLjQ5NywwLjgyMywzLjE1NCwxLjIzMiw0LjgwNywxLjIzMnMzLjMwNi0wLjQwOSw0LjgwMy0xLjIzMmwxOTMuNjUzLTEwNi4zMzQKCWMzLjE5Mi0xLjc1NSw1LjE3Ny01LjEwNyw1LjE3Ny04Ljc0OXYtMi45MTdoMC4wMDRWMTI3LjA1NHogTTMwNC41OTIsNjMuOTg2bDI0LjEwMiwxMy41OThsLTE3Mi40OTksOTQuODExbC0xOS41MjEtMTAuNzIxCglsLTguMTI1LTQuNTg1bDE3Mi43MjEtOTQuOTMyTDMwNC41OTIsNjMuOTg2eiBNMTQ1Ljg4MywxODkuNzk0djQ0LjU0MmwtMjcuODY0LTE1LjUyOHYtNDQuNjA0bDguOTQsNC45MTNMMTQ1Ljg4MywxODkuNzk0egoJIE0yMjguNzc1LDIyLjM1M2w1MS43NTgsMjguNDIxbC0xNzIuODMsOTQuOTk0bC01MS44NC0yOC40NjhMMjI4Ljc3NSwyMi4zNTN6IE0yMTguNzkxLDQxOS40MzZMNDUuMTA3LDMyNC4wNjJWMjcxLjMzdi03OC41MjYKCXYtNTguNjM1bDUyLjk0NywyOS4wNzZ2NjEuNDI3YzAsMy42MjIsMS45NjEsNi45NTksNS4xMjMsOC43MjFsNDcuODI4LDI2LjY1NWMxLjUxMywwLjg0MiwzLjE4NiwxLjI2Myw0Ljg1OCwxLjI2MwoJYzEuNzUsMCwzLjQ5Ny0wLjQ2LDUuMDU3LTEuMzc2YzMuMDUyLTEuNzk0LDQuOTI4LTUuMDY4LDQuOTI4LTguNjA0di01MC44NjFsNTIuOTQzLDI5LjA3MlY0MTkuNDM2eiBNMjI4Ljc3NSwyMTIuMjQ3CglsLTUxLjg0NC0yOC40NjhsMTcyLjgzLTk0Ljk5NGw1MS45MjYsMjguNTE0TDIyOC43NzUsMjEyLjI0N3ogTTQxMi40NDMsMzI0LjA2MmwtMTczLjY4OCw5NS4zNzNWMjI5LjU0MWwxNzMuNjg4LTk1LjM3MnY4NS45MjIKCWMwLDAuMDI2LDAuMDA0LDAuMDUxLDAuMDA0LDAuMDc3djc1LjcwOWMwLDAuMDI2LTAuMDA0LDAuMDUxLTAuMDA0LDAuMDc3VjMyNC4wNjJ6Ii8+Cjwvc3ZnPgo=" style="height: 20px;margin: -3px"/>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('weight', 'Peso', ['class' => 'col-sm-4'])}}
                            <div class="col-sm-8 p-r-0">
                                <div class="input-group input-group-money">
                                    {{ Form::text('weight', $shipmentAddress->weight, ['class' => 'form-control decimal input-kg '.$triggerPrice, 'required', 'main-modal' => '.kg'])}}
                                    <div class="input-group-addon">Kg</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            <label class="col-sm-4">Volume</label>
                            <div class="col-sm-8 p-r-0">
                                <div class="input-group input-group-money">
                                    {{ Form::text('fator_m3', $shipmentAddress->fator_m3, ['class' => 'form-control decimal input-fm3 '.$triggerPrice, 'main-modal' => '.fm3'])}}
                                    <div class="input-group-addon">M<sup>3</sup></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('ldm', 'LDM', ['class' => 'col-sm-4'])}}
                            <div class="col-sm-8 p-r-0">
                                <div class="input-group input-group-money">
                                    {{ Form::text('ldm', $shipmentAddress->ldm > 0.00 ?? '', ['class' => 'form-control decimal input-ldm '.$triggerPrice, 'main-modal' => '.ldm'])}}
                                    <div class="input-group-addon">mt</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('kms', 'Kms', ['class' => 'col-sm-4'])}}
                            <div class="col-sm-8 p-r-0">
                                {{ Form::text('kms', $shipmentAddress->kms, ['class' => 'form-control decimal input-kms '.$triggerPrice, 'main-modal' => '.kms'])}}
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('date', 'Carga', ['class' => 'col-sm-4 p-0'])}}
                            <div class="col-sm-8">
                                <div class="input-group">
                                    {{ Form::text($hash == 'main' ? '_date' : 'date', null, ['class' => 'form-control input-date datepicker'])}}
                                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('delivery_date', 'Descarga', ['class' => 'col-sm-4 p-0'])}}
                            <div class="col-sm-8">
                                <div class="input-group">
                                    {{ Form::text($hash == 'main' ? '_delivery_date' : 'delivery_date', $shipmentAddress->delivery_date ? $shipmentAddress->delivery_date->format('Y-m-d') : null, ['class' => 'form-control input-dlvr-date datepicker'])}}
                                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        </div>
                         <div class="form-group form-group-sm m-b-5">
                             {{ Form::label('reference', 'Referência', ['class' => 'col-sm-4 p-0'])}}
                             <div class="col-sm-8">
                                 {{ Form::text('reference', $shipmentAddress->reference, ['class' => 'form-control input-sm input-ref', 'maxlength' => Setting::get('shipment_ref_maxsize', 15), 'main-modal' => '.ref']) }}
                             </div>
                         </div>
                         <div class="form-group form-group-sm m-b-5">
                             {{ Form::label('reference2', Setting::get('shipments_reference2_name') ?? 'Referência 2', ['class' => 'col-sm-4 p-0'])}}
                             <div class="col-sm-8">
                                {{ Form::text('reference2', $shipmentAddress->reference2, ['class' => 'form-control input-sm input-ref2', 'placeholder' => Setting::get('shipments_reference2_name'), 'main-modal' => '.ref2']) }}
                             </div>
                         </div>
                         @if(Setting::get('shipments_reference3'))
                         <div class="form-group form-group-sm m-b-5">
                             {{ Form::label('reference3', Setting::get('shipments_reference3_name') ?? 'Referência 3', ['class' => 'col-sm-4 p-0'])}}
                             <div class="col-sm-8">
                                {{ Form::text('reference3', $shipmentAddress->reference3, ['class' => 'form-control input-sm', 'placeholder' => Setting::get('shipments_reference3_name'), 'main-modal' => '.ref3']) }}
                             </div>
                         </div>
                         @endif
                     </div>
                    <div class="col-sm-5">
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('obs', 'Observ. Carga', ['class' => 'col-sm-2 p-0'])}}
                            <div class="col-sm-10">
                                {{ Form::textarea('obs', $shipmentAddress->obs, ['class' => 'form-control input-sm input-obs', 'rows' => 2, 'maxlength'=>'150', 'main-modal' => '.obs']) }}
                             </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('obs_delivery', 'Observ. Descarga', ['class' => 'col-sm-2 p-0'])}}
                            <div class="col-sm-10">
                                {{ Form::textarea('obs_delivery', $shipmentAddress->obs_delivery, ['class' => 'form-control input-sm input-obs-dlvr', 'rows' => 2, 'maxlength'=>'150', 'main-modal' => '.obs-dlvr']) }}
                             </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::hidden('id', $shipment->id, ['class' => 'input-id'])}}
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Cancelar</button>
                    <button type="button" class="btn btn-primary" data-answer="1">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$shipment = $originalShipment; 
?>