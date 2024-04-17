 @if($row->login_admin)
     <div class="text-center" data-toggle="tooltip" data-html="true" title="Último acesso:<br/>{{ $row->last_login ? $row->last_login : 'Nunca' }}">
        <i class="fas fa-check-circle text-green"></i>
     </div>
@elseif($row->password && !$row->is_operator && !$row->login_admin)
     <div class="text-center" data-toggle="tooltip" data-html="true" title="BLOQUEADO | Último acesso:<br/>{{ $row->last_login ? $row->last_login : 'Nunca' }}">
        <i class="fas fa-ban text-red"></i>
     </div>
@endif