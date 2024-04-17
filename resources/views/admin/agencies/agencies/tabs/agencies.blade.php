<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    @if(Auth::user()->hasRole([config('permissions.role.admin')]))
        <li>
            <a href="{{ route('admin.agencies.create') }}"
               class="btn btn-success btn-sm"
                data-toggle="modal"
                data-target="#modal-remote">
                <i class="fas fa-plus"></i> Novo
            </a>
        </li>
    @else
        <li>
            <a href="#"
               class="btn btn-success btn-sm" disabled>
                <i class="fas fa-plus"></i> Novo
            </a>
        </li>
    @endif
    <li>
        <p class="text-yellow bold">
            <i class="fas fa-info-circle"></i> A sua licença inclui limite de empresas e centros logísticos. Para empresas ou armazéns adicionais, consulte-nos.
        </p>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
           {{-- <th class="w-1"></th>--}}
            <th class="w-1">Código</th>
            <th>Centro Logístico</th>
            <th>Empresa</th>
            <th class="w-200px">Contactos</th>
            @if(Auth::user()->hasRole([config('permissions.role.admin')]))
                <th class="w-120px">Disponível para</th>
            @endif
            <th class="w-20px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.agencies.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>