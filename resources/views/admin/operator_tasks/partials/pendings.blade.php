@if($tasksPending->isEmpty())
    <h4 class="text-muted text-center m-t-50">Não há tarefas para apresentar.</h4>
@else
    <table class="table table-condensed table-hover m-0">
        <tr>
            {{-- <th class="bg-gray-light w-1">Estado</th> --}}
            <th class="bg-gray-light w-90px">Data</th>
            <th class="bg-gray-light w-100px">Horário</th>
            <th class="bg-gray-light w-90px">Tipo</th>
            <th class="bg-gray-light w-1">Enviado</th>
            <th class="bg-gray-light w-120px">Tarefa</th>
            <th class="bg-gray-light">Endereço</th>
            <th class="bg-gray-light">Localidade</th>
            <th class="bg-gray-light w-20px"><i class="fas fa-truck"></i></th>
            <th class="bg-gray-light w-90px"><i class="fas fa-boxes"></i> Vols</th>
            <th class="bg-gray-light w-50px"><i class="fas fa-weight-hanging"></i> KG</th>
            <th class="bg-gray-light w-100px">Cliente</th>
            <th class="bg-gray-light w-45px"></th>
        </tr>
        @foreach($tasksPending as $task)
            @if (($date && $task->date < $date) || (!$date && $task->date < date('Y-m-d')))
            <tr class="task-row" style="background-color: #ffea9a">
            @else
            <tr class="task-row">
            @endif
                {{-- <td>
                    <span class="label label-default">Pendente</span>
                </td> --}}
                <td>{{ @$task->date }}</td>
                <td>
                    @if (!$task->start_hour && !$task->end_hour)
                        N/A
                    @else
                        @if ($task->start_hour)
                            <span class="label label-default">{{ $task->start_hour }}</span>
                        @endif

                        @if ($task->end_hour)
                            @if ($task->start_hour)
                                <span>-</span>
                            @endif

                            <span class="label label-default">{{ $task->end_hour }}</span>
                        @endif
                    @endif
                </td>
                <td>{{ @$task->transport_type->name ?? 'N/A' }}</td>
                @if ($task->operator)
                    <td class="text-uppercase">
                        <a data-toggle="modal" data-target="#modal-remote" href="{{ route('admin.operator.tasks.change-operator', [$task->id]) }}">
                            {{ $task->operator->name }}
                        </a>
                    </td>
                @else
                    <td class="text-center">
                        @if (!empty($task->operators))
                        <?php
                        $content = '';
                        foreach ($task->operators as $operatorId) {
                            $content.= @$operators[$operatorId] ? $operators[$operatorId] . '<br/>' : '';
                        }
                        ?>
                        <span data-toggle="popover" data-title="Tarefa enviada para" data-content="{!! $content !!}">
                        @endif
                            <a data-toggle="modal" data-target="#modal-remote" href="{{ route('admin.operator.tasks.change-operator', [$task->id]) }}">
                                <i class="fas fa-users"></i> {{ count($task->operators) }}
                            </a>
                        @if (!empty($task->operators))
                        </span>
                        @endif
                        </span>
                    </td>
                @endif
                <td class="text-uppercase">
                    {{ $task->name }}
                    @if($task->description)
                        <i class="fas fa-info-circle text-blue" data-toggle="tooltip" title="{{ $task->description }}"></i>
                    @endif
                </td>
                <td>
                    {!! empty($task->address) ? nl2br($task->full_address) : $task->address !!}
                </td>
                <td>
                    {{ $task->city ?? 'N/A' }}
                </td>
                <td class="text-center">{{ $task->shipments ? count($task->shipments) : '' }}</td>
                <td>
                    <?php
                    $packsTypes = [];
                        if(!empty($task->shipments)) {
                            $shipments = \App\Models\Shipment::whereIn('id', $task->shipments)->get(['id','volumes', 'packaging_type']);

                            if($shipments) {
                                foreach ($shipments as $shipment) {
                                    if($shipment->packaging_type) {
                                        foreach ($shipment->packaging_type as $type => $qty) {
                                            $packsTypes[$type] = @$packsTypes[$type] + $qty;
                                        }
                                    }
                                    // else {
                                    //     $packsTypes['box'] = @$packsTypes['box'] + $shipment->volumes;
                                    // }
                                }
                            }
                        }

                    ?>
                    @if(!empty($packsTypes))
                        @foreach($packsTypes as $type => $qty)
                            {{ $qty }} {{ @$packTypes[$type] }}<br/>
                        @endforeach
                    @else
                        {{ $task->volumes ? $task->volumes .' Vol.' : '' }}
                    @endif
                </td>
                <td>{{ $task->weight > 0.00 ? number($task->weight) : '' }}</td>
                <td>
                    <span data-toggle="tooltip" title="{{ @$task->customer->name }}">
                        @if (strlen(@$task->customer->name) > 10)
                        {{ @$task->customer->code }} - {{ substr(@$task->customer->name, 0, 10) }}...
                        @else
                        {{ @$task->customer->code }} - {{ @$task->customer->name }}
                        @endif
                    </span>
                </td>
                <td class="text-center">
                    <a href="{{ route('admin.operator.tasks.edit', [$task->id]) }}" data-toggle="modal" data-target="#modal-remote">
                        <i class="fas fa-pencil-alt text-green"></i>
                    </a>
                    <a href="{{ route('admin.operator.tasks.destroy', [$task->id]) }}" data-toggle="ajax-confirm" data-ajax-method="delete" data-ajax-confirm="Pretende remover esta tarefa?">
                        <i class="fas fa-trash-alt text-red"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
@endif