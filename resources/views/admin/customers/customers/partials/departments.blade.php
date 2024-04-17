<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-departments"> 
            <li>
                <a href="{{ route('admin.customers.departments.create', $customer->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
        </ul>
        <table id="datatable-departments" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-1"></th>
                    <th class="w-1">@trans('Código')</th>
                    <th>@trans('Departamento')</th>
                    <th>@trans('Contactos')</th>
                    <th class="w-65px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.customers.departments.selected.destroy', $customer->id]]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>