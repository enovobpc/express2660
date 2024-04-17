<div class="modal" id="modal-print-mass-invoices">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.invoices.pdf.massive', 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Impressão de documentos em massa.</h4>
            </div>
            <div class="modal-body">
                {{--@if(!hasModule('invoices-advanced'))
                    <h4 class="text-center m-b-15">
                        <i class="fas fa-lock fs-20 m-b-10"></i>
                        <br/>
                        Esta funcionalidade não está incluída no valor da sua licença.
                    </h4>
                    <div class="overflow-div" style="opacity: 0.6;
    background: #fff;
    width: 95%;
    height: 67px;
    position: absolute;
    z-index: 1;"></div>
                @endif--}}
                <div class="row row-5">
                    <div class="col-sm-5">
                        <?php
                            $list = trans('admin/billing.types-list');
                            unset($list['nodoc']);
                        ?>
                        <div class="form-group is-required">
                            {{ Form::label('doctype', 'Tipo Documento', ['class' => 'control-label']) }}
                            {{ Form::select('doctype', $list, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('month', 'Mês', ['class' => 'control-label']) }}
                            {{ Form::select('month', trans('datetime.list-month'), date('m'), ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            {{ Form::label('year', 'Ano', ['class' => 'control-label']) }}
                            {{ Form::select('year', yearsArr(date('Y')-2, date('Y'), true), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="downloads-area" style="display: none">
                            <table class="table table-condensed m-b-0">
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{--<div class="col-sm-6">
                        <div class="form-group is-required">
                            {{ Form::label('start_date', 'Data Inicial', ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('start_date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>--}}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gerar download..."><i class="fas fa-print"></i> Gerar Ficheiro Massivo</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
</script>