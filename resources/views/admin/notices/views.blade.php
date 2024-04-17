<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Histórico de visualização</h4>
</div>
<div class="modal-body">
    <div class="row">
        @foreach($usersGrouped as $source => $users)
        <div class="col-sm-3">
            <h4 class="fs-15 fw-500 m-b-3">
                {{ $source ? (@$sources[$source] ? @$sources[$source] : $source) : 'Utilizadores Globais' }}
                <small class="pull-right">{{ $users->filter(function($item){ return $item->pivot->readed == 1; })->count() }}/{{ count($users) }}</small>
            </h4>
            <ul class="list-unstyled readed-list">
                @foreach($users as $user)
                <li><i class="fa fa-circle {{ $user->pivot->readed ? 'text-green' : 'text-muted' }}"></i> {{ $user->name }}</li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    </div>
</div>
<style>
    .readed-list {
        height: 100px;
        overflow: scroll;
        border: 1px solid #ddd;
        padding: 5px;
        font-size: 12px;
    }
</style>