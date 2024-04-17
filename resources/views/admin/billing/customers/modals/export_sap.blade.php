<div class="modal " id="modal-export-sap">
    <div class="modal-dialog modal-xs ">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.export.billing.customers.software', 'sap'], 'method' => 'GET' , 'id' => 'form-export-sap']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Código Sequencial para exportação</h4>
            </div>
            <div class="modal-body">
                <div class="row row-12 center-block" >
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group">
                                {{ Form::label('code_export_sap', 'Primeiro Código a Importar:') }}
                                {{ Form::text('code_export_sap', null, ['class' => 'form-control']) }}
                            <p class="m-0 text-info"><b class="fas fa-info-circle"></b> Apenas os algarismos após "PAP"</p>
                                {{ Form::hidden('month', $month) }}
                                {{ Form::hidden('year', $year) }}
                                {{ Form::hidden('period', $period) }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary btn-submit">Exportar Sap</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
