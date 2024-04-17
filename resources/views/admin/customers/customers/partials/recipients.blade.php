<div class="box no-border">
    <div class="box-body">
        @if(@$duplicateRecipients->count())
            <div class="alert alert-danger">
                <a href="{{ route('admin.customers.destroy.duplicates', $customer->id) }}"
                   data-method="post"
                   data-confirm-title="Eliminar moradas duplicadas"
                   data-confirm-label="Eliminar"
                   data-confirm="Confirma a remoção das moradas duplicadas?"
                   class="btn btn-xs btn-default btn-alert">
                    <i class="fas fa-trash-alt"></i> @trans('Eliminar Duplicados')</a>
                <a href="{{ route('admin.customers.destroy.duplicates', $customer->id) }}"
                   data-toggle="modal"
                   data-target="#modal-duplicate-recipients"
                   class="btn btn-xs btn-default btn-alert m-r-5">
                    <i class="fas fa-search"></i> @trans('Ver duplicados') </a>
                <h4><i class="fa fa-exclamation-triangle"></i> @trans('Existem') {{ $duplicateRecipients->count() }} @trans('moradas duplicadas.')</h4>
            </div>
            @include('admin.customers.customers.modals.duplicate_recipients')
        @endif
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-addresses"> 
            <li>
                <a href="{{ route('admin.customers.recipients.create', $customer->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.export.customers.recipients', $customer->id) }}" class="btn btn-default btn-sm">
                    <i class="fas fa-file-excel"></i> @trans('Exportar Excel')
                </a>
            </li>
            <li>
                <a href="#" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-import-recipients">
                    <i class="fas fa-upload"></i> @trans('Importar Excel')
                </a>
            </li>
            <li>
                <a href="{{ coreUrl('/uploads/models/modelo_importacao_destinatarios.xlsx') }}"
                   class="btn btn-default btn-sm"
                   target="_blank"
                   data-placement="right" data-toggle="tooltip"
                   title="Preencha os campos correspondentes do ficheiro modelo para imporar destinatários em massa. Pode importar um ficheiro personalizado desde que as colunas do ficheiro a carregar tenham os mesmos nomes do ficheiro modelo.">
                    <i class="fas fa-file-alt"></i> @trans('Download Ficheiro Modelo')
                </a>
            </li>
        </ul>
        <table id="datatable-addresses" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-1">@trans('Código')</th>
                    <th>@trans('Destinatário')</th>
                    <th>@trans('Morada')</th>
                    @if(!empty($departments))
                    <th>@trans('Departamento')</th>
                    @endif
                    <th class="w-65px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.customers.recipients.selected.destroy', $customer->id]]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
            <a href="{{ route('admin.export.customers.recipients', $customer->id) }}" class="btn btn-sm btn-default m-l-5" data-action-url="datatable-action-url">
                <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
            </a>
        </div>
    </div>
</div>
@include('admin.customers.customers.modals.import_recipients')