<table class="table table-condensed table-hover table-history m-0">
    <thead>
        <tr>
            <th class="w-190px">Data</th>
            <th>Estado</th>
            <th>Alterado Por</th>
        </tr>
    </thead>
    <tbody>

        @foreach($budget->history as $history)
        <tr>
            <td>{{ $history->created_at }}</td>
            <td>
                <span class="label label-{{ trans('admin/budgets.status-labels.' . $history->status) }}">
                    {{ trans('admin/budgets.status.' . $history->status) }}
                </span>
            </td>
            <td>{{ @$history->operator->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>