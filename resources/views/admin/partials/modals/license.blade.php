<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Estado da Licença e Módulos</h4>
</div>
<div class="modal-body">
    <div class="license-header">
        <div class="row">
            <div class="col-sm-9">
                <h4 class="m-0">
                    @if($license->status == 'active')
                    <span class="bold text-green">
                        <i class="fas fa-circle"></i> Licença Ativa
                    </span>
                    @elseif($license->status == 'wainting')
                        <span class="bold text-yellow">
                        <i class="fas fa-circle"></i> Pagamentos Pendente
                    </span>
                    @else
                        <span class="bold text-red">
                            <i class="fas fa-circle"></i> Licença Expirada
                        </span>
                    @endif
                    <br/>
                    <small style="padding-left: 20px">{{ $license->description }}</small>
                </h4>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="m-0" style="margin-top: -5px">
                            <small>Registado em</small>
                            <br/>
                            @if($license->regist_date)
                            {{ $license->regist_date->format('Y-m-d') }}
                            @endif
                        </h4>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="m-0" style="margin-top: -5px">
                            <small>Renova em</small>
                            <br/>
                            @if($license->renew_date)
                            {{ $license->renew_date->format('Y-m-d') }}
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-sm-8">
        <h4 class="m-t-0">
            <i class="fas fa-credit-card"></i> Pagamentos
            @if(!$unpaid->isEmpty())
            <span class="text-red bold"><i class="fas fa-exclamation-triangle"></i> {{ $unpaid->count() }} pagamentos, {{ money($unpaid->sum('total'), Setting::get('app_currency')) }} em atraso</span>
            @endif
        </h4>
        <div class="table-responsive" style="margin-top: -35px">
            <table id="datatable-license-payments" class="table table-striped table-dashed table-hover table-condensed">
                <thead>
                    <tr>
                        <th class="w-1">Emissão</th>
                        <th>Descrição</th>
                        <th class="w-50px">Total</th>
                        <th class="w-50px">Recebido</th>
                        <th class="w-80px">Pagar até</th>
                        <th class="w-80px">Pagamento</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="col-sm-4">
        <h4 class="m-t-0"><i class="fas fa-puzzle-piece"></i> Módulos Adicionais</h4>
        <div class="modules-list">
            <ul class="list-unstyled">
                @foreach($modules as $module)
                <li>
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading">
                                @if(hasModule($module->module))
                                <span class="text-green">
                                    <i class="fas fa-check-circle"></i> {{ $module->name }}
                                </span>
                                @else
                                    <span class="text-red">
                                    <i class="fas fa-times-circle"></i> {{ $module->name }}
                                </span>
                                @endif
                            </h4>
                            <p>{{ $module->description }}</p>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<script type="text/javascript">
    var oTable;
    $(document).ready(function () {

        oTable = $('#datatable-license-payments').DataTable({
            columns: [
                {data: 'issuance_date', name: 'issuance_date'},
                {data: 'description', name: 'description'},
                {data: 'total', name: 'total'},
                {data: 'paid_value', name: 'paid_value'},
                {data: 'payment_deadline', name: 'payment_deadline'},
                {data: 'payment_date', name: 'payment_date'},
                {data: 'id', name: 'id', visible: false},
            ],
            pageLength: 10,
            order: [[0, "desc"]],
            ajax: {
                url: "{{ route('admin.licenses.payments.datatable.details', $license->id) }}",
                type: "POST",
                data: function (d) {},
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

</script>