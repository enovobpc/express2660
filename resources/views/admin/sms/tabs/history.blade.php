<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-history">
    <li>
        <a href="{{ route('admin.sms.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <h4 class="text-yellow" style="margin: 0; margin-left: 5px;
    line-height: 14px;
    margin-top: -17px;
    top: 9px;
    font-size: 16px;
    position: relative;
    font-weight: bold;">
            {{ $remainingSms }} SMS<br/>
            <small>Disponíveis</small>
        </h4>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-history" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-65px">Origem</th>
            <th class="w-120px">Destinatário</th>
            <th>Mensagem</th>
            <th class="w-150px">Estado</th>
            <th class="w-50px">Nº SMS</th>
            <th class="w-65px">Data</th>
            <th class="w-20px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.sms.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>
<div class="clearfix"></div>