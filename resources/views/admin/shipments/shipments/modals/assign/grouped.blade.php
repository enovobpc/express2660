{{ Form::open(['route' => ['admin.shipments.selected.grouped.store'], 'class' => 'group-shipments']) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">
        @if(count($shipmentsMaster) == 1)
            Criar serviço agrupado
        @else
            Editar serviço agrupado
        @endif
    </h4>
</div>
<div class="modal-body">
    @if(count($shipmentsMaster) > 1)
        <div class="alert alert-danger">
            <h4 class="m-b-15 bold"><i class="fas fa-exclamation-triangle"></i> Impossível agrupar</h4>
            <br/>
            <p>
                Não pode agrupar os serviços selecionados porque um ou mais serviços pertencem a serviços agrupados diferentes.
                <br/>
                Só pode agrupar serviços se:
            </p>
            <ul>
                <li>Nenhuma das cargas pertença a um serviço agrupado</li>
                <li>As cargas selecionadas pertençam ao mesmo serviço agrupado.</li>
            </ul>
        </div>
    @elseif(count($shipmentsMaster) == 1)
        <h4 class="bold lh-1-3">Adicionar/remover cargas do serviço agrupado #{{ @$shipmentsMaster[0] }}</h4>
        {{ Form::hidden('assign_master_trk', @$shipmentsMaster[0]) }}
    @else
    {{ Form::label('assign_master_trk', 'Selecione um serviço para ser o principal:') }}
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::select('assign_master_trk', ['' => ''] + $shipmentsTrk, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    @endif
    @if(count($shipmentsMaster) <= 1)
        <hr class="m-t-10 m-b-10"/>
        <p>Qual o preço de custo/preço venda total para este serviço?</p>
        <div class="row row-5">
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('assign_master_cost', 'Preço Custo') }}
                    <div class="input-group">
                        {{ Form::text('assign_master_cost', @$cost, ['class' => 'form-control decimal']) }}
                        <div class="input-group-addon">€</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('assign_master_price', 'Preço Frete') }}
                    <div class="input-group">
                        {{ Form::text('assign_master_price', $price, ['class' => 'form-control decimal']) }}
                        <div class="input-group-addon">€</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        @if(count($shipmentsMaster) == 1)
        <button type="button" class="btn btn-default btn-disagroup" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Desagrupar</button>
        @endif
        <button type="submit" class="btn btn-primary btn-group" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Agrupar</button>
    </div>
</div>
{{ Form::hidden('ids', $ids) }}
{{ Form::hidden('assign_master_ungroup', 0) }}
{{ Form::close() }}
<script>
    $('.group-shipments .select2').select2(Init.select2());

    $('.group-shipments .btn-group').on('click', function(e){
        e.preventDefault();

        if($('.group-shipments [name="assign_master_price"]').val() == '') {
            Growl.error('Deve indicar o valor total acordado pelo preço de frete para o serviço.')
        } else {
            $('.group-shipments [name="assign_master_ungroup"]').val(0);
            $('.group-shipments').submit();
        }
    })

    $('.group-shipments .btn-disagroup').on('click', function(e){
        e.preventDefault();
        $('.group-shipments [name="assign_master_ungroup"]').val(1);
        $('.group-shipments').submit();
    })
</script>