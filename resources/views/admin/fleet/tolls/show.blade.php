<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Detalhe das portagens em') {{ $date }}</h4>
</div>
<div class="modal-body">
    <table class="table table-condensed m-0">
        <tr>
            <th class="w-1">#</th>
            <th class="w-1">@trans('Entrada')</th>
            <th>@trans('Pórtico')</th>
            <th class="w-1">@trans('Saída')</th>
            <th>@trans('Pórtico')</th>
            <th>@trans('Operador Via Verde')</th>
            <th class="w-1">@trans('Valor')</th>
        </tr>
        <?php $total = 0; ?>
        @foreach($logs as $key => $log)
            <?php $total+= $log->total; ?>
            <tr>
                <td class="italic text-muted">{{ $key + 1 }}</td>
                <td>{{ $log->entry_date->format('H:i') }}</td>
                <td>{{ $log->entry_point }}</td>
                <td>{{ $log->exit_date->format('H:i') }}</td>
                <td>{{ $log->exit_point }}</th>
                <td>{{ $log->toll_provider }}</th>
                <td class="bold">{{ money($log->total, Setting::get('app_currency')) }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></th>
            <td class="text-right">@trans('Total')</th>
            <td class="bold">{{ money($total, Setting::get('app_currency')) }}</td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    </div>
</div>