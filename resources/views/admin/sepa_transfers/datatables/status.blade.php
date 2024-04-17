@if($row->status == \App\Models\SepaTransfer\Payment::STATUS_EDITING)
    <span class="label label-info">Em Edição</span>
@elseif($row->status == \App\Models\SepaTransfer\Payment::STATUS_CONCLUDED)
    <span class="label label-success">Concluído</span>
@elseif($row->status == \App\Models\SepaTransfer\Payment::STATUS_PENDING)
    <span class="label label-warning">Pendente</span>
@elseif($row->status == \App\Models\SepaTransfer\Payment::STATUS_CONCLUDED_PARTIAL)
    <span class="label bg-orange"
          data-toggle="tooltip"
          title="{{ $row->error_code }} - {{ $row->error_msg }}">
        Concluído Parcial
    </span>
@elseif($row->status == \App\Models\SepaTransfer\Payment::STATUS_REJECTED)
    <span class="label label-danger"
      data-toggle="tooltip"
      title="{{ $row->error_code }} - {{ $row->error_msg }}">
        Rejeitado
    </span>
    @if($row->errors_processed)
        <br/><small data-toggle="tooltip" title="Os erros já foram processados e comunicados aos clientes." class="text-muted">Notificado</small>
    @else
        <br/><small data-toggle="tooltip" title="Os erros ainda não foram processados e comunicados aos clientes." class="text-red">Por Notificar</small>
    @endif
@endif