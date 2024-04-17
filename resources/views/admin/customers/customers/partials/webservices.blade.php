@if(!hasModule('webservices'))
    @include('admin.partials.denied_message')
@else
<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-webservices">
            <li>
                <a href="{{ route('admin.customers.webservices.create', $customer->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Nova ligação a webservice')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.customers.webservices.import', $customer->id) }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-upload"></i> @trans('Importar ligação de outro cliente')
                </a>
            </li>
        </ul>

        <table id="datatable-webservices" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th>@trans('Ligação')</th>
                    <th class="w-1"><div style="white-space: nowrap ">@trans('Envios pelo Fornecedor')</div></th>
                    <th>@trans('Agência')</th>
                    <th>@trans('Utilizador')</th>
                    <th>@trans('Password')</th>
                    <th>@trans('ID de Sessão')</th>
                    <th class="w-1">@trans('Ativo')</th>
                    <th class="w-65px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endif