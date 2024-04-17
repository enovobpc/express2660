<div class="text-center">
    @if($row->password_client)
        <span class="label label-success" data-toggle="tooltip" title="grant_type: password">Credenciais + Password</span><br/>
    @elseif($row->personal_access_client)
        <span class="label label-warning" data-toggle="tooltip" title="grant_type: client_credentials">Apenas Credenciais</span><br/>
    @else
        <span class="label label-danger">Sem acesso</span>
    @endif
</div>