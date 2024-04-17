<div class="box no-border">
   {{--  SE HOUVER AFERICAO APLICAR CORES NOS PNEUS CONFORME O REGISTO, desde que registo há menos de 6 meses<BR/>
    SE HOUVER DESGASTE PROGRESSIVO, CALCULAR A % DE TEMPO E CALCULAR O VALOR DO DESGASTE.<BR/>
     --}}<div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-tyres">
            <li>
                <a href="{{ route('admin.fleet.tyres.create', ['vehicle' => $vehicle->id]) }}"
                     class="btn btn-success btn-sm" 
                     data-toggle="modal" 
                     data-target="#modal-remote-lg">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
        </ul>
        <table id="datatable-tyres" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-1"></th>
                    <th class="w-60px">@trans('Montagem')</th>
                    <th>@trans('Posição')</th>
                    <th class="w-60px">Km</th>
                    <th>@trans('Referência')</th>
                    <th class="w-60px">@trans('Marca')</th>
                    <th class="w-60px">@trans('Modelo')</th>
                    <th class="w-80px">@trans('Tamanho')</th>
                    <th class="w-60px">@trans('Data Fim')</th>
                    <th class="w-60px">@trans('KM Fim')</th>
                    <th class="w-80px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.tyres.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
        </div>
    </div>
</div>