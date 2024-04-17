<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-scheduled">
    <li>
        <a href="{{ route('admin.invoices.purchase.create', ['scheduled' => true]) }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li class="fltr-primary w-230px">
        <strong>Tipo Doc.</strong><br class="visible-xs"/>
        <div class="w-150px pull-left form-group-sm" style="position: relative">
            {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list-purchase'), fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-scheduled">
    <ul class="list-inline pull-left">
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Tipo despesa</strong><br/>
            <div class="w-180px">
                {{ Form::selectMultiple('type', $purchasesTypes, fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Fornecedor</strong><br/>
            <div class="w-230px">
                {{ Form::select('provider',  Request::has('provider') ? [''=>'', Request::get('provider') => Request::get('provider-text')] : [''=>''], fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Despesas imputadas a</strong><br/>
            <div class="w-90px pull-left">
                {{ Form::select('target',  ['' => 'Todos', 'Invoice' => 'Nada', 'Vehicle' => 'Viatura', 'User' => 'Colaborador', 'Shipment' => 'Envio'], fltr_val(Request::all(), 'target'), array('class' => 'form-control input-sm w-100 filter-datatable select2')) }}
            </div>
            <div class="w-250px pull-left">
                {{ Form::select('target_id',  [''=>''], fltr_val(Request::all(), 'target_id'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Procurar viatura, motorista ou envio')) }}
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable-scheduled" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">Agendamento</th>
            <th>Fornecedor</th>
            <th class="w-120px">Tipo Despesa</th>
            <th class="w-80px">Documento</th>
            <th class="w-70px">Total</th>
            <th class="w-1"><i class="fas fa-copy" data-toggle="tooltip" title="Fatura já contemplada em sistema"></i></th>
            <th class="w-65px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.invoices.purchase.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Anular selecionados"><i class="fas fa-trash-alt"></i> Anular</button>
    {{ Form::close() }}
    <div class="pull-left">
        <h4 style="margin: -2px 0 -6px 10px;
                        padding: 1px 3px 3px 9px;
                        border-left: 1px solid #999;
                        line-height: 17px;">
            <small>Total Selecionado</small><br/>
            <span class="dt-sum-total bold"></span><b>€</b>
        </h4>
    </div>
</div>
<div class="clearfix"></div>