<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" style="float: right">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Vincular envio ao código do fornecedor</h4>
</div>
<div class="modal-body p-10">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('assign_provider', 'Fornecedor', ['class' => 'control-label']) }}
                {{ Form::select('assign_provider', $providers, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('assign_trk', 'Código Barras Envio', ['class' => 'control-label', 'focus']) }}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-barcode"></i>
                            </div>
                            {{ Form::text('assign_trk', null, ['class' => 'form-control', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('assign_provider_trk', 'Código Barras Fornecedor', ['class' => 'control-label']) }}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-barcode"></i>
                            </div>
                            {{ Form::text('assign_provider_trk', null, ['class' => 'form-control', 'required']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <button type="text" class="btn btn-block btn-primary m-t-18 assign-btn"
                    data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A associar...">
                Associar
            </button>
        </div>
    </div>

    <div style="border: 1px solid #ccc; height: 300px; overflow-y: scroll">
        <table class="table table-condensed table-results">
            <thead>
                <tr>
                    <th class="bg-gray-light w-150px">Fornecedor</th>
                    <th class="bg-gray-light">TRK Envio</th>
                    <th class="bg-gray-light">Código Vinculado</th>
                    <th class="bg-gray-light w-1"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Concluir</button>
    </div>
</div>
<script>
    $('.modal .select2').select2(Init.select2());

    $('.assign-btn').on('click', function (e){
        e.preventDefault();

        var providerId  = $('.modal [name="assign_provider"]').val();
        var trk         = $('.modal [name="assign_trk"]').val();
        var trkProvider = $('.modal [name="assign_provider_trk"]').val();

        if(providerId == '') {
            Growl.error('Deve indicar o fornecedor.');
        } else if(trk == '') {
            Growl.error('Deve indicar o código de envio.');
        } else if(trkProvider == '') {
            Growl.error('Deve indicar o código de envio do fornecedor.');
        } else {

            $('.modal [name="assign_trk"]').val('').trigger('focus')
            $('.modal [name="assign_provider_trk"]').val('');

            $.post("{{ route('admin.traceability.assign.ctt-correios.store') }}", {
                provider_id: providerId,
                provider_trk: trkProvider,
                trk: trk
            }, function (data) {
                if (data.result) {
                    $('.modal .table-results tbody').append(data.html)
                } else {
                    Growl.error(data.feedback)
                }
            }).always(function () {
                $('.form-assign button').button('reset');
            }).fail(function () {
                Growl.error500();
            })
        }
    })

    $('.modal [name="assign_trk"]').on('keyup', function(e) {
        e.preventDefault();
        if(e.which == 13) {
            $('.modal [name="assign_provider_trk"]').val('').trigger('focus');
        }
    })

    $('.modal [name="assign_provider_trk"]').on('keyup', function(e) {
        e.preventDefault();
        if(e.which == 13) {
            $('.modal .assign-btn').trigger('click');
        }
    })

    $(document).on('click', '.modal .btn-desvinculate', function(){

        var trk = $(this).data('trk');
        var $tr = $(this).closest('tr');

        $tr.hide();

        $.post("{{ route('admin.traceability.assign.ctt-correios.store') }}", {
            action: 'desvinculate',
            trk: trk
        }, function (data) {
            if (!data.result) {
                $tr.show();
                Growl.error(data.feedback)
            }
        }).always(function () {
            $('.form-assign button').button('reset');
        }).fail(function () {
            Growl.error500();
        })
    });
</script>
