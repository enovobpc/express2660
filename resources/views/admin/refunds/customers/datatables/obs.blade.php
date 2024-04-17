<?php $ids = [2,695,6,667]?>
@if(config('app.source') == 'asfaltolargo' && in_array(Auth::user()->id, $ids))
    <div>Rcb: {{ @$row->refund_control->received_user->name }}</div>
    <div>Pag: {{ @$row->refund_control->payment_user->name }}</div>
@endif
<div style="max-width: 250px; word-break: break-all">
    @if(@$row->refund_control->canceled)
        <div class="text-red"><i class="fas fa-exclamation-triangle"></i> Reembolso Cancelado</div>
    @endif
    {{ @$row->refund_control->obs }}
</div>
@if(@$row->refund_control->filepath)
    @if($row->refund_control->obs)
        <br/>
    @endif
    <a href="{{ asset($row->refund_control->filepath) }}" class="btn btn-xs btn-default" target="_blank">
        <i class="fas fa-file"></i> Ver Anexo
    </a>
@endif