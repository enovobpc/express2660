<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">SOBRE O SOFTWARE ENOVO TMS</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="text-center">
                <img src="{{ asset('assets/img/default/logo/logo.svg') }}" style="margin-top: 10px; height: 40px;"/>
                <h3>
                    Versão {{ $version }}<br/>
                    <small>{{ $date }}</small>
                </h3>
                @if (Auth::user()->isAdmin() && @$serverDetails->ip)
                <hr/>
                Server {{ @$serverDetails->name }} ({{ @$serverDetails->ip }})
                @endif
            </div>
        </div>
        <div class="col-sm-12">
            <hr/>
            <p class="text-center">
            ©2016-{{ date('Y') }}. ENOVO TMS - Todos os direitos reservados.<br/>
            A Enovo detém os direitos de autor e os direitos de propriedade industrial
            de todo o sistema informático Enovo TMS disponibilizada ao cliente
            assim como de todos os websites e/ou outras plataformas adjacentes à mesma.
            </p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>