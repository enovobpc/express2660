<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-companies">
    @if(Auth::user()->hasRole([config('permissions.role.admin')]))
        <li>
            <a href="{{ route('admin.companies.create') }}"
               class="btn btn-success btn-sm"
               data-toggle="modal"
               data-target="#modal-remote-lg">
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
    <table id="datatable-companies" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">Logotipo</th>
            <th class="w-1">NIF</th>
            <th>Empresa</th>
            <th>Contactos</th>
            <th>Licenças</th>
            <th class="w-20px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.companies.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>