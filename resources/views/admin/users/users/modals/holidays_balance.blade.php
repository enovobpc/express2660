<div class="modal" id="modal-print-holidays-balance">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.export.operators.holidays-balance', 'method' => 'GET', 'target' => '_blank']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-file-refresh"></i> @trans('Balanço de Férias')</h4>
            </div>
            <div class="modal-body">

                <div class="row row-5">
                    <div class="col-sm-7">
                        <p class="m-t-8 bold">@trans('Listar balanço de férias para:')</p>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group is-required m-b-0">
                            {{ Form::select('year', yearsArr(2019, date('Y')), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-excel"></i> @trans('Exportar')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
</script>