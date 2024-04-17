<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            {{ Form::label('customer_request', 'Cópia da mensagem de pedido do cliente') }}
            {{ Form::textarea('customer_request', null, ['class' => 'form-control', 'rows' => 12]) }}
        </div>
    </div>
</div>