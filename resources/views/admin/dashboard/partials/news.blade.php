<div class="box box-solid box-list">
    <div class="box-header bg-purple with-border">
        <a href="" class="btn btn-xs btn-primary pull-right">
            @trans('Ver todas as Novidades')
        </a>
        <h4 class="box-title">
            <i class="fas fa-newspapper"></i> @trans('Últimas Novidades')
        </h4>
    </div>
    <div class="box-body p-0">
        <table class="table table-fixed">
            <thead>
                <tr>
                    <th class="col-sm-10">@trans('Cliente')</th>
                    <th class="col-sm-1">@trans('Envios')</th>
                    <th class="col-sm-1">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            @foreach($pendingShipments as $customer)
                <tr>
                    <td class="col-sm-10">
                        {{ $customer->customer->code }} - {{ $customer->customer->name }}
                    </td>
                    <td class="col-sm-1">{{ $customer->total_pending }}</td>
                    <td class="col-sm-1">
                        <a href="{{ route('admin.shipments.status.assign-pending.create', [$customer->customer_id, 'source' => 'dashboard']) }}" class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>