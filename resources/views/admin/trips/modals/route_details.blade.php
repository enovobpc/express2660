
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">@trans('Previsão de viagem')</h4>
</div>
<div class="modal-body">
    <p><i class="text-blue">Esta funcionalidade está em desenvolvimento e aperfeiçoamento.<br/>Ajude-nos a melhorar, comunicando-nos a sua opinião e visão sobre o assunto.</i></p>
    <table class="table table-condensed table-hover table-maps m-0">
        <tr>
            <th class="bg-gray w-85px">Data</th>
            <th class="bg-gray w-40px">Hora</th>
            <th class="bg-gray">Ação</th>
            
        </tr>
        @foreach($routeDetails[0] as $day)
            @foreach($day["events"] as $events)
                <tr>
                    <td>{{$day["date"] }}</td>
                    <td>{{$events["time"] }}</td>
                    <td>{{ trans('admin/shipments.route-details.actions.' . $events["action"]) }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>
</div>
<div class="modal-footer" style="margin-top: -8px; margin-bottom: -10px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>


