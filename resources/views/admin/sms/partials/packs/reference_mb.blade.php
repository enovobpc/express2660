<div class="row row-0 m-t-15">
    <div class="col-sm-12">
        <h4 class="text-center m-t-0 bold">{{ $title }}</h4>
        <hr/>
    </div>
    <div class="col-sm-3 col-sm-offset-2 text-r">
        <img class="w-65px" src="{{ asset('assets/img/default/mb.svg') }}">
    </div>
    <div class="col-sm-7">
        <ul class="list-unstyled fs-18">
            <li class="m-b-10">Entidade: <b>{{ $pack->entity }}</b></li>
            <li class="m-b-10">Referência: <b>{{ chunk_split($pack->reference, 3, ' ') }}</b></li>
            <li>Valor: <b>{{ money($total, '€') }}</b></li>
        </ul>
    </div>
    <div class="col-sm-12">
        <hr/>
        <p class="text-center text-blue">Tem 48h para efetuar o pagamento.<br/>O talão emitido faz prova de pagamento.</p>
    </div>
</div>