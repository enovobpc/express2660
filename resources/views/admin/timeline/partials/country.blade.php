<div class="row row-10">
    <div class="col-md-2" style="width: 14.5%">
        <h4 class="text-blue bold m-t-0 m-b-15">
            Eventos
            <a href="{{ route('admin.timeline.types.create') }}"
               class="btn btn-xs btn-default pull-right"
               data-toggle="modal"
               data-target="#modal-remote">
                <i class="fas fa-plus"></i> Novo
            </a>
        </h4>
        <div id="external-events">
            @forelse($eventsTypes as $type)
                <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'
                     style="background-color: {{ $type->color }}; border-color: {{ $type->color }}"
                     id="{{ $type->id }}"
                     color="{{ $type->color }}">
                    <div class='fc-event-main text-white'>
                        {{ str_limit($type->title, 20) }}
                        <div class="event-type-actions">
                            <a href="{{ route('admin.timeline.types.edit', $type->id) }}"
                               data-toggle="modal"
                               data-target="#modal-remote"
                               class="text-green">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a href="{{ route('admin.timeline.types.destroy', $type->id) }}"
                               data-method="delete"
                               data-confirm="Confirma a remoção do registo selecionado?"
                               class="text-red">
                                <i class="fas fa-fw fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-muted"><i class="fas fa-info-circle"></i> Sem eventos</div>
            @endforelse
        </div>
        <hr class="m-t-10 m-b-5"/>
        <h4 class="text-blue bold m-t-0 m-b-15">Filtrar</h4>
        {{ Form::open(['method' => 'get']) }}
        <ul class="list-unstyled m-t-0 datatable-filters">
            <li class="fltr-primary">
                <div class="form-group form-group-sm">
                    {{ Form::label('vehicle', 'Viatura') }}
                    {{ Form::selectMultiple('vehicle', $vehiclesList, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
                </div>
            </li>
            <li class="fltr-primary">
                <div class="form-group form-group-sm">
                    {{ Form::label('operator', 'Motorista') }}
                    {{ Form::selectMultiple('operator', $operatorsList, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
                </div>
            </li>
            <li class="fltr-primary">
                <div class="form-group form-group-sm">
                    {{ Form::label('service', 'Serviço') }}
                    {{ Form::selectMultiple('service', $servicesList, fltr_val(Request::all(), 'service'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
                </div>
            </li>
            <li class="fltr-primary">
                <div class="form-group form-group-sm">
                    {{ Form::label('sender_country', 'País Carga') }}
                    {{ Form::selectMultiple('sender_country', trans('country'),fltr_val(Request::all(), 'sender_country'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
                </div>
            </li>
            <li class="fltr-primary">
                <div class="form-group form-group-sm">
                    {{ Form::label('recipient_country', 'País Descarga') }}
                    {{ Form::selectMultiple('recipient_country', trans('country'), fltr_val(Request::all(), 'recipient_country'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
                </div>
            </li>
        </ul>
        {{ Form::hidden('start') }}
        {{ Form::hidden('end') }}
        <div class="clearfix"></div>
        <button type="submit" class="btn btn-sm btn-block btn-primary">
            Aplicar Filtros
        </button>
        {{ Form::close() }}
    </div>
    <div class="col-md-10" style="width: 85.5%">
        <div id='calendar'></div>
    </div>
</div>