@extends(app_email_layout())

@section('content')
<div style='font-family: Helvetica, Arial, "Lucida Grande", sans-serif; font-size: 12px'>
    @if($isTicketAnswer)
        <h3>Cliente respondeu: Suporte #{{ $ticket->code }}</h3>
    @else
        <h3>Novo Pedido Suporte #{{ $ticket->code }}</h3>
    @endif
    <table style="width: 100%; width: 600px; font-size: 14px" colspan="0">
        <tr>
            <td>Tipo</td>
            <td>{{ trans('admin/customers_support.categories.'.@$ticket->category) }}</td>
        </tr>
        <tr>
            <td>Cliente</td>
            <td>{{ @$ticket->customer->name }}</td>
        </tr>
        <tr>
            <td>Assunto</td>
            <td>{{ $ticket->subject }}</td>
        </tr>
        @if($isTicketAnswer)
        <tr>
            <td>Resposta</td>
            <td>{!! @$ticketMessage->message !!}</td>
        </tr>
        @endif
    </table>
    <p style="font-size: 14px">Para aceder e responder ao ticket, <a href="{{ route('admin.customer-support.show', $ticket->id) }}">Clique Aqui</a></p>
</div>
@stop