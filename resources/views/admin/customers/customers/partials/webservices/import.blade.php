{{ Form::open(['route' => ['admin.customers.webservices.import', $customerId]]) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Importar métodos do webservice')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('method', __('Método do Webservice')) }}
                {{ Form::select('method', ['' => ''] + $webserviceMethods, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('import_customer_id', __('Importar dados do cliente:')) }}
                {{ Form::select('import_customer_id', [], null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $("select[name=import_customer_id]").select2({
        ajax: {
            url: "{{ route('admin.customers.search') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
</script>
