<div class="box no-border">
    <div class="box-body">
        <div class="col-sm-12">
            <div class="alert alert-info">
                <p>
                    <i class="fas fa-info-circle"></i> @trans('Sempre que criar um envio com um dos códigos abaixo listados, o sistema irá atribuir automáticamente o envio a este fornecedor.')'
                    <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@trans('A gestão de todos os códigos postais poderá ser feita em "Configurações > Códigos Postais".')
                </p>
            </div>
            <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-zip-codes">
                <li>
                    <a href="{{ route('admin.zip-codes.create', ['provider' => $provider->id]) }}" class="btn btn-success btn-sm"
                       data-toggle="modal" data-target="#modal-remote">
                        <i class="fas fa-plus"></i> @trans('Novo')
                    </a>
                </li>
                <li>
                    <a href="#" class="btn btn-default btn-sm" data-toggle="modal"
                       data-target="#modal-import-zip-codes">
                        <i class="fas fa-upload"></i> @trans('Carregar em Massa')
                    </a>
                </li>
            </ul>
            <table id="datatable-zip-codes" class="table table-striped table-dashed table-hover table-condensed">
                <thead>
                    <tr>
                        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                        <th></th>
                        <th class="w-50px">@trans('Código')</th>
                        <th class="w-20">@trans('Agência Associada')</th>
                        <th>@trans('Localidade')</th>
                        <th>@trans('País')</th>
                        <th class="w-1"><span style="white-space: nowrap" data-toggle="tooltip" title="Kms desde a agência">@trans('Kms')</span></th>
                        <th class="w-1"><span style="white-space: nowrap">@trans('Serviços Disponíveis')</span></th>
                        <th class="w-1"><span style="white-space: nowrap" data-toggle="tooltip" title="Código postal local/regional">@trans('Reg')</span></th>
                        <th class="w-1">@trans('Fornecedor')</th>
                        <th class="w-1">@trans('Ações')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="selected-rows-action hide">
                {{ Form::open(array('route' => 'admin.zip-codes.selected.destroy')) }}
                <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados">
                    <i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
                </button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>