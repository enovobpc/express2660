<div class="modal" id="modal-import-divergences">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.billing.balance.divergences.upload', 'class' => 'import-form','files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Importar Ficheiro KeyInvoice</h4>
            </div>
            <div class="modal-body">
                <h4>Comparar contas correntes entre ENOVO - KeyInvoice</h4>
                <p>
                    Importe o ficheiro excel de contas correntes do software KeyInvoice para analisar possíveis diferenças no software ENOVO.
                    <br/>
                    As contas corrente com diferença devem ser sincronizadas de novo no software ENOVO.
                </p>
                <p>
                    Pode obter o ficheiro excel no software keyinvoice acedendo a: Vendas > Conta Corrente Cliente > Excel (ao lado do filtro de pesquisa)
                </p>
                <div class="form-group m-b-0 import-inputs-area">
                    {{ Form::label('file', 'Ficheiro a importar', ['class' => 'control-label']) }}
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="fas fa-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">Selecionar</span>
                            <span class="fileinput-exists">Alterar</span>
                            <input type="file" name="file" data-file-format="csv,xls,xlsx" required />
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">
                            Remover
                        </a>
                    </div>
                </div>
                <div class="import-results-area"></div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary"
                            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar...">Importar
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>