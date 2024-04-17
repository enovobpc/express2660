<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-contacts"> 
            <li>
                <a href="{{ route('admin.customers.contacts.create', $customer->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
        </ul>
        <table id="datatable-contacts" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-1"></th>
                    <th>@trans('Departamento')</th>
                    <th>@trans('Responsável')</th>
                    <th>@trans('Telefone')</th>
                    <th>@trans('Telemóvel')</th>
                    <th>@trans('E-mail')</th>
                    <th class="w-65px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.customers.contacts.selected.destroy', $customer->id]]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>