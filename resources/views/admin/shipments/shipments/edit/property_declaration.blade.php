{{ Form::model($shipment, array('route' => array('admin.shipments.get.property-declaration.print', $shipment->id), 'class' => 'form-horizontal', 'method' => 'GET')) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Preencher Declaração de Valor - Envio {{ $shipment->tracking_code }}</h4>
</div>
<div class="modal-body">

    <table class="w-100 m-b-5">
        <tr>
            <td class="w-20px">Eu,</td>
            <td>{{ Form::text('recipient_name', null, ['class' => 'form-control input-sm']) }}</td>
            <td class="w-120px">&nbsp;, portador do CC N.º</td>
            <td class="w-110px">{{ Form::text('cc', null, ['class' => 'form-control input-sm']) }}</td>
            <td class="w-110px">&nbsp;, contribuinte n.º</td>
            <td class="w-100px">{{ Form::text('nif', null, ['class' => 'form-control input-sm']) }}</td>
        </tr>
    </table>
    <table class="w-100">
        <tr>
            <td class="w-100px">Residente em</td>
            <td>{{ Form::text('recipient_address', null, ['class' => 'form-control input-sm']) }}</td>
            <td class="w-80px">&nbsp;, Código Postal</td>
            <td class="w-150px">{{ Form::text('recipient_zip_code', null, ['class' => 'form-control input-sm']) }}</td>
            <td class="w-130px">&nbsp;na Localidade de</td>
            <td>{{ Form::text('recipient_city', null, ['class' => 'form-control input-sm']) }}</td>
        </tr>
    </table>
    <div class="form-grou form-group-s m-b-5">
        <div class="pull-left p-t-5">Eu &nbsp;&nbsp;</div>
        {{ Form::text('recipient_name', null, ['class' => 'form-control input-sm w-200px pull-left']) }}

        <div class="pull-left p-t-5">&nbsp;&nbsp;, Portador do CC N.º&nbsp;&nbsp;</div>
        {{ Form::text('recipient_name', null, ['class' => 'form-control input-sm w-80px pull-left']) }}

        <div class="pull-left p-t-5">&nbsp;&nbsp;, Contribuinte N.º&nbsp;&nbsp;</div>
        {{ Form::text('recipient_name', null, ['class' => 'form-control input-sm w-80px pull-left']) }}

        <div class="pull-left p-t-5">Residente em &nbsp;&nbsp;</div>
        {{ Form::text('recipient_name', null, ['class' => 'form-control input-sm w-200px pull-left']) }}

    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient_address', 'Morada', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-11">
            {{ Form::text('recipient_address', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient__zip_code', 'Cod.P.', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-2 p-r-0">
            {{ Form::text('recipient_zip_code', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
        {{ Form::label('recipient_city', 'Local.', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-4">
            {{ Form::text('recipient_city', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
        {{ Form::label('recipient_phone', 'Contacto', ['class' => 'col-sm-1 control-label p-0']) }}
        <div class="col-sm-3">
            {{ Form::text('recipient_phone', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient__zip_code', 'Portador do BI', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::text('recipient_zip_code', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
        {{ Form::label('recipient_city', 'NIF', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-4">
            {{ Form::text('recipient_city', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
        {{ Form::label('recipient_phone', 'Contacto', ['class' => 'col-sm-1 control-label p-0']) }}
        <div class="col-sm-3">
            {{ Form::text('recipient_phone', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>
    <h4 class="text-primary">Os dados do remetente</h4>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient_name', 'Nome', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-11">
            {{ Form::text('recipient_name', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient_address', 'Morada', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-11">
            {{ Form::text('recipient_address', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient__zip_code', 'Cod.P.', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-2 p-r-0">
            {{ Form::text('recipient_zip_code', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
        {{ Form::label('recipient_city', 'Local.', ['class' => 'col-sm-1 control-label p-r-0']) }}
        <div class="col-sm-4">
            {{ Form::text('recipient_city', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
        {{ Form::label('recipient_phone', 'Contacto', ['class' => 'col-sm-1 control-label p-0']) }}
        <div class="col-sm-3">
            {{ Form::text('recipient_phone', null, ['class' => 'form-control input-sm', 'required']) }}
        </div>
    </div>
    <h4 class="text-primary">Descrição do Conteúdo a Transportar</h4>
    <div class="col-sm-12">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('description', 'Descrição detalhada do conteúdo', ['class' => 'p-r-0']) }}
            {{ Form::textarea('recipient_address', null, ['class' => 'form-control input-sm', 'rows' => 2]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left modal-feedback text-red m-t-5"></div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir
        </button>
    </div>
</div>
{{ Form::close() }}