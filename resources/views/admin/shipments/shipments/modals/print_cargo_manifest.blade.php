<div class="modal" id="modal-print-cargo-manifest">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Imprimir manifesto de carga</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 style="margin: 0 0 15px;">Como pretende imprimir o documento?</h4>
                        <div class="form-group">
                            <div class="checkbox m-b-15">
                                <label>
                                    {{ Form::radio('print_cargo_manifest', 'none', true, ['data-href' => route('admin.printer.shipments.cargo-manifest', ['none'])]) }}
                                    Listagem simples
                                </label>
                            </div>
                            <div class="checkbox m-b-15">
                                <label>
                                    {{ Form::radio('print_cargo_manifest', 'customer', false, ['data-href' => route('admin.printer.shipments.cargo-manifest', ['customers'])]) }}
                                    Listagem agrupada por cliente
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::radio('print_cargo_manifest', 'provider', false, ['data-href' => route('admin.printer.shipments.cargo-manifest', ['providers'])]) }}
                                    Listagem agrupada por fornecedor
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a href="{{ route('admin.printer.shipments.cargo-manifest', ['none']) }}"
                   data-toggle="datatable-action-url"
                   target="_blank"
                   class="btn btn-primary"><i class="fas fa-print"></i> Imprimir</a>
            </div>
        </div>
    </div>
</div>