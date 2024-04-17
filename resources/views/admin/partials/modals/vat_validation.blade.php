<div class="modal" id="modal-vat-validation">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Validação de Contribuinte</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-9">
                        <h3 class="m-t-5">
                            <span class="vv-vat"></span><br/>
                            <small class="vv-valid text-green"><i class="fas fa-check"></i> O NIF é válido para <span class="vv-country-name"></span></small>
                            <small class="vv-invalid text-red" style="display: none"><i class="fas fa-times"></i> O NIF é inválido para <span class="vv-country-name"></span></small>
                        </h3>
                    </div>
                    <div class="col-sm-3">
                        <img src="{{ img_default(true) }}" class="img-responsive vv-logo b-1 p-3 radius-3px" style="margin-top: -5px"/>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-condensed m-t-5 m-b-0">
                            <tr>
                                <td class="text-muted w-1">Entidade</td>
                                <td class="vv-name"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Morada</td>
                                <td class="vv-address"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telefone</td>
                                <td class="vv-phone"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telemóvel</td>
                                <td class="vv-mobile"></td>
                            </tr>
                        </table>
                        <div class="text-center fw-400 b-t-1 m-t-5 vv-feedback">
                            <h4 class="m-b-0 fw-400"><i class="fas fa-spin fa-circle-notch"></i> A procurar dados associados ao contribuinte.</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    {{ Form::hidden('vv-address') }}
                    {{ Form::hidden('vv-city') }}
                    {{ Form::hidden('vv-zip-code') }}
                    {{ Form::hidden('vv-phone') }}
                    {{ Form::hidden('vv-mobile') }}
                    {{ Form::hidden('vv-name') }}
                    {{ Form::hidden('vv-logo') }}
                    {{ Form::hidden('vv-target') }}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success vv-accept" disabled>Usar estes dados</button>
                </div>
            </div>
        </div>
    </div>
</div>