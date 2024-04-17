<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Detalhe das Incidências</h4>
</div>
<div class="modal-body">
    {{ Form::open(['route' => ['admin.statistics.incidences.details', 'source' => $source, 'datemin' => $startDate, 'datemax' => $endDate], 'class' => 'form-filter-incidences']) }}
    <ul class="datatable-filters list-inline">
        @if($source == 'providers')
            <li class="fltr-primary w-490px">
                <strong>Fornecedor</strong><br class="visible-xs"/>
                <div class="w-405px pull-left form-group-sm">
                    {{ Form::select('provider', $providers, null, ['class' => 'form-control input-sm filter-datatable select2']) }}
                </div>
            </li>
        @elseif($source == 'customers')
            <li class="fltr-primary w-490px">
                <strong>Cliente</strong><br class="visible-xs"/>
                <div class="w-405px pull-left form-group-sm">
                    {{ Form::select('customer', [@$customer->id => @$customer->name], null, ['class' => 'form-control input-sm filter-datatable select2-customer']) }}
                </div>
            </li>
        @else
            <li class="fltr-primary w-490px">
                <strong>Serviço</strong><br class="visible-xs"/>
                <div class="w-405px pull-left form-group-sm">
                    {{ Form::select('service', $services, null, ['class' => 'form-control input-sm filter-datatable select2']) }}
                </div>
            </li>
        @endif
        <li>
            <button type="submit" class="btn btn-sm btn-block btn-success">Atualizar</button>
        </li>
    </ul>
    {{ Form::close() }}
    <div class="clearfix"></div>
    <hr class="m-t-15 m-b-15"/>
    <table class="table table-hover m-0">
        <thead>
        <tr>
            <th class="bg-gray-light" colspan="2">Motivo Incidência</th>
            <th class="bg-gray-light w-60px">Total</th>
        </tr>
        </thead>
    </table>
    <div class="table-responsive" style="border: 1px solid #eee; height: 220px; overflow: scroll;">
        <table class="table table-hover m-0">
            <tbody>
            <?php $count = $i = 0;?>
            @foreach($incidences as $incidence => $total)
                <?php $count+= $total; $i++ ?>
                <tr>
                    <td class="w-50px"><span class="badge">{{ $i }}</span></td>
                    <td>{{ $incidence ? $incidence : 'Sem motivo especificado' }}</td>
                    <td class="w-60px text-center">{{ $total }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <table class="table table-hover m-0">
        <thead>
        <tr>
            <th class="bg-gray-light"></th>
            <td class="text-right bg-gray-light"><b>TOTAL*</b></td>
            <td class="w-60px text-center bg-gray-light"><b>{{ $count }}</b></td>
        </tr>
        </thead>
    </table>
    <small>*O valor total inclui repetições da mesma incidência em cada envio.</small>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<script>
    $('.select2').select2(Init.select2());

    $('.select2-customer').select2({
        ajax: {
            url: "{{ route('admin.shipments.search.customer') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()

                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('.form-filter-incidences').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);

        $('#modal-remote .modal-body').html('<h4 class="modal-title text-center m-t-40 m-b-40 text-muted"><i class="fas fa-circle-notch fa-spin"></i> A carregar...</h4>')
        $.get($form.attr('action'), $form.serialize(), function(data){
            $('#modal-remote .modal-content').html(data)
        })
    })
</script>
