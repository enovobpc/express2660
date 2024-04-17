<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="fas fa-download"></i> App Android Motoristas</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8">
            <h4 class="bold m-t-0 m-b-15">Para instalar a aplicação:</h4>
            <ol class="fs-14 p-l-15 m-0 ol-steps">
                <li>
                    Use a câmara do seu telemóvel para ler este código.
                    <small class="text-muted" style="font-weight: normal">
                        Se não conseguir ler o código, pode fazer o download diretamente do endereço
                        <a href="https://{{ $hostname }}/apk" style="font-style: normal">
                            <span class="btn-icn">{{ $hostname }}/apk</span>
                        </a>
                    </small>

                </li>
                <li>
                    Vai ser descarregado para o seu telemóvel o ficheiro <span class="btn-icn">enovo_tms.apk</span>.
                        Clique no ficheiro para o instalar.
                    <br/>
                    <small class="text-muted" style="font-weight: normal">
                        <i class="fa fa-info-circle"></i>
                            É necessário que permita nas configurações do telemóvel a instalação de aplicações de fontes desconhecidas.
                    </small>
                </li>
                <li class="m-b-10 lh-1-2">
                    Execute a aplicação. No ecrã inicial da aplicação, clique em <span class="btn-icn"><i class="fas fa-cog"></i> Configurar</span>
                    e no campo "Servidor de Ligação" insira o endereço: {{ $hostname }}
                    <br/>
                    <small class="text-muted" style="font-weight: normal">
                        <i class="fa fa-info-circle"></i>
                        Após este passo, o logótipo da aplicação deverá mudar para o logótipo da sua empresa.
                    </small>
                </li>
                <li style="border-bottom: none; margin-bottom: 0">
                    Inicie sessão na aplicação com o seu código de motorista e palavra-passe.
                </li>
            </ol>
        </div>
        <div class="col-sm-4">
            <div class="text-center m-b-30">
                <img src="{{ $qrCode }}" class="img-responsive m-t-30">
            </div>
                <ul class="list-unstyled version-info">
                    <li><span class="text-muted">Última Versão:</span> {{ $version }}</li>
                    <li><span class="text-muted">Data Versão:</span> {{ $updated }} </li>
                    <li><span class="text-muted">Tamanho:</span> {{ $fileSize }}</li>
                    <li><span class="text-muted">Android:</span> 5.0.0 ou superior</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="{{ route('admin.mobile.install.print') }}"
       target="_blank"
       class="btn btn-default">
        <i class="fas fa-print"></i> Imprimir esta página
    </a>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
<style>
    .version-info li {
        margin-bottom: 4px;
    }

    .ol-steps li {
        border-bottom: 1px solid #ddd;
        padding: 10px 0 15px 0;
        margin-bottom: 5px;
        font-weight: 500;
        color: #0070bf;
    }

    .ol-steps .btn-icn {
        background: #eee;
        border: 1px solid #ddd;
        padding: 0 3px 1px 3px;
        border-radius: 3px;
        font-size: 12px;
        color: #000;
    }

    .ol-steps .btn-icn .fa {
        font-size: 11px;
    }

    .ol-steps small {
        font-weight: normal;
        line-height: 1.1;
        display: inline-block;
        margin-top: 4px;
        font-style: italic;
    }

    .ol-steps small .fa {
        font-size: 11px;
    }
</style>