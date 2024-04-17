@if(!hasModule('covenants'))
    @include('admin.partials.denied_message')
@else
<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-covenants"> 
            <li>
                <a href="{{ route('admin.customers.covenants.create', $customer->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
        </ul>
        <table id="datatable-covenants" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-1"></th>
                    <th class="w-90px">@trans('Tipo Avença')</th>
                    <th>@trans('Descrição')</th>
                    <th>@trans('Máx. Envios')</th>
                    <th>@trans('Serviço')</th>
                    <th>@trans('Valor')</th>
                    <th>@trans('Início')</th>
                    <th>@trans('Termo')</th>
                    <th class="w-65px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.customers.covenants.selected.destroy', $customer->id]]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endif