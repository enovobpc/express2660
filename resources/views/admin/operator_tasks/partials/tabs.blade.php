<div class="tabbable-line m-b-0">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-pending" data-toggle="tab">
                Pendentes <span class="badge label-default">{{ count($tasksPending) }}</span>
            </a>
        </li>
        <li>
            <a href="#tab-pending-with-operator" data-toggle="tab">
                Pendentes (Com operador) <span class="badge label-warning">{{ count($tasksPendingWithOperator) }}</span>
            </a>
        </li>
        <li>
            <a href="#tab-accepted" data-toggle="tab">
                Aceites <span class="badge label-info">{{ count($tasksAccepted) }}</span>
            </a>
        </li>
        <li>
            <a href="#tab-concluded" data-toggle="tab">
                Concluídos <span class="badge label-success">{{ count($tasksConcluded) }}</span>
            </a>
        </li>
    </ul>
</div>
<div class="tab-content" style="height: 400px; overflow-y: scroll; overflow-x: auto;">
    <div role="tabpanel" class="tab-pane active" id="tab-pending">
        @include('admin.operator_tasks.partials.pendings')
    </div>
    <div role="tabpanel" class="tab-pane" id="tab-pending-with-operator">
        @include('admin.operator_tasks.partials.pendings', ['tasksPending' => $tasksPendingWithOperator])
    </div>
    <div role="tabpanel" class="tab-pane" id="tab-accepted">
        @include('admin.operator_tasks.partials.accepted')
    </div>
    <div role="tabpanel" class="tab-pane" id="tab-concluded">
        @include('admin.operator_tasks.partials.concluded')
    </div>
</div>