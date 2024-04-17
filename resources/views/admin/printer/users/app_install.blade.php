<div>
    <div style="margin-bottom: 50px">
        <h1 style="font-size: 30px; text-align: center">Instruções de Instalação</h1>
        <h4 style="text-align: center">Aplicação Android para Motoristas</h4>
    </div>
    <div style="width: 65%; float: left">
        <h4 class="m-t-0 m-b-15">Para instalar a aplicação:</h4>
        <ol style="font-size: 17px">
            <li style="border-bottom: 1px solid #ddd;
        padding: 10px 0 15px 0;
        margin-bottom: 5px;
        font-weight: 500;
        color: #0070bf;">Use a câmara do seu telemóvel para ler este código.</li>
            <li style="border-bottom: 1px solid #ddd;
        padding: 10px 0 15px 0;
        margin-bottom: 5px;
        font-weight: 500;
        color: #0070bf;">
                Vai ser descarregado para o seu telemóvel o ficheiro <span class="btn-icn">enovo_tms.apk</span>.
                Clique no ficheiro para o instalar.
                <br/>
                <small class="text-muted" style="font-weight: normal">
                    <i class="fa fa-info-circle"></i>
                    É necessário que permita nas configurações do telemóvel a instalação de aplicações de fontes desconhecidas.
                </small>
            </li>
            <li style="border-bottom: 1px solid #ddd;
        padding: 10px 0 15px 0;
        margin-bottom: 5px;
        font-weight: 500;
        color: #0070bf;">
                Execute a aplicação. No ecrã inicial da aplicação, clique em "Configurar"
                e no campo "Servidor de Ligação" insira o endereço: {{ $hostname }}
                <br/>
                <small class="text-muted" style="font-weight: normal">
                    Após este passo, o logótipo da aplicação deverá mudar para o logótipo da sua empresa.
                </small>
            </li>
            <li style="padding: 10px 0 15px 0;
        margin-bottom: 5px;
        font-weight: 500;
        color: #0070bf;">
                Inicie sessão na aplicação com o seu código de motorista e palavra-passe.
            </li>
        </ol>
    </div>
    <div style="width: 34%; margin-left: 50px; float: left">
        <div>
            <img src="{{ $qrCode }}" style="margin-top: 45px">
            <ul class="list-unstyled version-info" style="margin-top: 30px; font-size: 15px">
                <li style="margin-bottom: 5px"><span class="text-muted">Última Versão:</span> {{ $version }}</li>
                <li style="margin-bottom: 5px"><span class="text-muted">Data Versão:</span> {{ $updated }} </li>
                <li style="margin-bottom: 5px"><span class="text-muted">Tamanho:</span> {{ $fileSize }}</li>
                <li style="margin-bottom: 5px"><span class="text-muted">Android:</span> 5.0.0 ou superior</li>
            </ul>
        </div>
    </div>
</div>
</div>