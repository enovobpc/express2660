@if(@$row->status == 'pending' || @$row->status == 'PENDING')
    <span style="color:rgb(226, 230, 1)"><i class="fas fa-fw fa-circle"></i></span> Pendente
@elseif(@$row->status == 'refused' || @$row->status == 'REFUSED')
<span style="color:rgb(255, 0, 0)"><i class="fas fa-fw fa-circle"></i></span> Recusado
@elseif(@$row->status == 'accept' || @$row->status == 'ACCEPT')
<span style="color:rgb(38, 192, 0)"><i class="fas fa-fw fa-circle"></i></span> Aceite
@else
    <i class="fas fa-fw fa-circle"></i> {{@$row->status}}
@endif