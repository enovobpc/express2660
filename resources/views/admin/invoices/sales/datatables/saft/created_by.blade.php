@if($saft->issued)
    @if(@$saft->created_by)
        {{ @$saft->user->name }}
    @else
        <span data-toggle="tooltip" title="Este SAFT foi gerado automático pelo sistema na data limite de emissão de SAFT.">Sistema</span>
    @endif
@endif