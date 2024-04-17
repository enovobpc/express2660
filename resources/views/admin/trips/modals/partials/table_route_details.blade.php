<!-- <table class="table table-condensed table-hover table-maps m-0">
    <tr>
        <th class="bg-gray w-50">Ação</th>
        <th class="bg-gray w-30">Data</th>
        <th class="bg-gray w-20">Hora</th>
    </tr>
    @foreach($routeDetails as $key => $day)
        @foreach($day["events"] as $events)
            <tr>
                <td>{{ trans('admin/shipments.route-details.actions.' . $events["action"]) }}</td>
                <td>{{$day["date"]}}</td>
                <td>{{$events["time"]}}</td>
            </tr>
        @endforeach
    @endforeach
</table> -->