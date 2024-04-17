<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-accessories">
            <li>
                <a href="{{ route('admin.fleet.accessories.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
        </ul>
        <table id="datatable-accessories" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-60px">@trans('Referência')</th>
                    <th>@trans('Acessório')</th>
                    <th>@trans('Tipo')</th>
                    <th class="w-60px">@trans('Marca')</th>
                    <th class="w-40px">@trans('Modelo')</th>
                    <th class="w-65px">@trans('Compra')</th>
                    <th class="w-65px">@trans('Validade')</th>
                    <th class="w-40px">@trans('Estado')</th>
                    <th class="w-80px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.accessories.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>