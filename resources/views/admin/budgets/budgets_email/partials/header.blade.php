<div class="box no-border m-b-15">
    <div class="box-body p-5">
        <div class="row">
            <div class="col-sm-10">
                <div class="pull-left m-l-10">
                    <h3 class="m-0">
                        <b>{{ $budget->budget_no }} - {{ $budget->subject }}</b>
                    </h3>
                    <p class="m-t-10 m-b-0 text-muted">
                        <i class="fas fa-envelope"></i> {{ $budget->name }} ({{ $budget->email }}) | <i class="far fa-clock"></i> {{ $budget->created_at }}
                    </p>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="pull-right">
                    <span class="label label-{{ trans('admin/budgets.status-labels.'.$budget->status) }} pull-right m-r-10 m-t-1 bigger-130">{{ trans('admin/budgets.status.' . $budget->status) }}</span>
                    {{--<a href="{{ route('admin.budgets.edit', $budget->id) }}" class="btn btn-sm btn-default pull-right" data-toggle="modal" data-target="#modal-remote-lg">
                        <i class="fas fa-pencil-alt"></i> Editar
                    </a>--}}
                </div>
            </div>
        </div>
    </div>
</div>
