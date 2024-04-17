<div class="btn-group btn-table-actions">
    @if($row->is_operator && !hasModule('human_resources'))
    <a href="{{ route('admin.users.edit', [$row->id, 'source' => 'operators']) }}"
       data-toggle="modal"
       data-target="#modal-remote-lg"
       class="btn btn-sm btn-default">
       @trans('Editar')
    </a>
    @else
    <a href="{{ route('admin.users.edit', $row->id) }}" class="btn btn-sm btn-default">
        @trans('Editar')
    </a>
    @endif
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">@trans('Opções Extra')</span>
    </button>
    <ul class="dropdown-menu pull-right">

        @if($row->password)
            <li>
                <a href="{{ route('admin.users.remote-login', $row->id) }}" class="text-yellow"
                   data-method="post" data-confirm-title="Iniciar Sessão Remota" data-confirm-class="btn-success"
                   data-confirm-label="Iniciar Sessão"
                   data-confirm="Pretende iniciar sessão como {{ $row->name }}?" target="_blank">
                    <i class="fas fa-fw fa-sign-in-alt"></i> @trans('Iniciar Sessão')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.login-log.show', ['users', $row->id]) }}" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-fw fa-history"></i> @trans('Histórico de Acessos')
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('admin.change-log.show', ['User', $row->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-history"></i> @trans('Histórico de Edições')
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.users.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
        <li class="divider"></li>
        @if(hasModule('human_resources') && Auth::user()->ability(Config::get('permissions.role.admin'), 'users_absences,users_cards'))
            <li>
                <a href="{{ route('admin.export.operators.absences', ['user' => $row->id]) }}" target="_blank">
                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar Férias/Ausências')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.printer.users.validities', ['user' => $row->id]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> @trans('Documentos a Expirar')
                </a>
            </li>

            <li class="dropdown-submenu pull-left">
                <a tabindex="-1" href="#">
                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir outros...')'
                </a>

                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.printer.users.simcommunications', ['user' => $row->id]) }}" target="_blank">
                            <i class="fas fa-print"></i> @trans('Declaração de Comunicações')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.users.uniform', ['user' => $row->id]) }}" target="_blank">
                            <i class="fas fa-print"></i> @trans('Declaração de Fardamento')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.users.activity', ['user' => $row->id]) }}" target="_blank">
                            <i class="fas fa-print"></i> @trans('Certificado Trabalhador Internacional')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.attributes-declarations.equipment.get', [$row->id, 'source' => 'operators']) }}"  data-toggle="modal"    data-target="#modal-remote">
                            <i class="fas fa-print"></i> @trans('Certificado Aquisição Equipamentos')
                        </a>
                    </li>
                </ul>
            </li>
            
           
        @else
            <li>
                <a href="#" style="cursor: not-allowed">
                    <i class="fas fa-file-excel"></i> @trans('Exportar Férias/Ausências')
                </a>
            </li>
            <li>
                <a href="#" style="cursor: not-allowed">
                    <i class="fas fa-print"></i> @trans('Documentos a Expirar')
                </a>
            </li>
        @endif
    </ul>
</div>