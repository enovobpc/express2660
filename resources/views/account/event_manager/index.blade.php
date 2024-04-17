@section('title')
    {{ trans('account/event-manager.title') }} -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        <li>
            <a href="{{ route('account.event-manager.create') }}"
               class="btn btn-sm btn-black"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-plus"></i> Novo pedido
            </a>
        </li>
        <li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <i class="fas fa-angle-down"></i>
            </button>
        </li>
    </ul>
    @include("account.event_manager.partials.filters")
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th>Evento</th>
                    <th>Tipo</th>
                    <th>Data Inicio</th>
                    <th>Data Final</th>
                    <th class="w-1">Ativo</th>
                    <th class="w-60px">Criado</th>
                    <th class="w-65px">Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="selected-rows-action hide">
        <div>
            {{--{{ Form::open(array('route' => 'account.event-manager.selected.destroy')) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-confirm-title="{{ trans('account/global.word.destroy-selected') }}">
                <i class="fas fa-trash-alt"></i> {{ trans('account/global.word.destroy-selected') }}
            </button>
            {{ Form::close() }}--}}
        </div>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'type', name: 'type', searchable: false},
                {data: 'start_date', name: 'start_date', class:'text-center', searchable: false},
                {data: 'end_date', name: 'end_date', class:'text-center', searchable: false},
                {data: 'is_active', name: 'is_active', class:'text-center', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at', class:'text-center', searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                type: "POST",
                url: "{{ route('account.event-manager.datatable') }}",
                data: function (d) {
                    d.status   = $('.datatable-filters-extended select[name=status]').val();
                    d.types    = $('.datatable-filters-extended select[name=types]').val();
                    d.date_min = $('.datatable-filters-extended input[name=date_min]').val();
                    d.date_max = $('.datatable-filters-extended input[name=date_max]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { 
                    Datatables.complete();
                    $('a.confirmationBootBox').on('click',function(e){
                        e.preventDefault();
                        Confirmation(e.currentTarget);
                        return false; // Stops page from reload
                    }); 
                }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        function Confirmation(currentTarget) {
            var $this = currentTarget.dataset;

            var method  = $this.method ?? "POST";
            var href    = $this.href ?? "#";
            var title   = $this.title ?? "Confirmação";
            var body    = $this.body ?? "Confirma a ação que está preste a realizar?";
            var confirmLabel = $this.confirmLabel ?? "Confirmar";
            var confirmClass = $this.confirmClass ?? "btn-success";
            bootbox.confirm({
                title: title,
                message: "<h4 style='font-weight: normal'>" + body + "</h4>",
                buttons: {
                    confirm: {
                        label: confirmLabel,
                        className: confirmClass
                    },
                    cancel: {
                        label: "Cancelar",
                        className: "btn-default"
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.post(href).then(function() {
                            oTable.ajax.reload(null , false); //Keeps in the same page
                        }).catch(function() {
                            alert("Erro em: " + title)
                        });
                    }
                }
            });
        }
    });
</script>
@stop