@if(!hasModule('customers_attachments'))
    @include('admin.partials.denied_message')
@else
<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-attachments">
            <li>
                <a href="{{ route('admin.customers.attachments.create', $customer->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i>@trans(' Novo')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.customers.attachments.sort', [$customer->id]) }}" class="btn btn-default btn-sm sort-attachments" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-sort-amount-down"></i> @trans('Ordenar')
                </a>
            </li>
        </ul>
        <table id="datatable-attachments" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th>@trans('Documento')</th>
                <th class="w-65px">@trans('Criado em')</th>
                <th class="w-1">@trans('Pos.')</th>
                <th class="w-65px">@trans('Ações')</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.customers.attachments.selected.destroy', $customer->id]]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endif