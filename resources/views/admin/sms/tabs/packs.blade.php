<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-packs">
    <li>
        <a href="{{ route('admin.sms.packs.create') }}" class="btn btn-success btn-sm btn-new-pack" data-toggle="modal" data-target="#modal-remote">
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
    <table id="datatable-packs" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-130px">Data compra</th>
            <th class="w-120px">SMS Totais</th>
            <th class="w-120px">SMS Disponíveis</th>
            <th class="w-70px">Preço/SMS</th>
            <th class="w-90px">Total (IVA Inc)</th>
            <th class="w-180px">Pagamento</th>
            <th class="w-1">Ativo</th>
            <th>Registo por</th>
            @if(Auth::user()->isAdmin())
            <th class="w-20px">Ações</th>
            @endif
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.sms.packs.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>
<div class="clearfix"></div>