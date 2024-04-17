<div class="modal-header">
    <button class="close" data-dismiss="modal" type="button">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gestor de Tarefas por Operador</h4>
</div>
<div class="modal-body p-0">
    <div class="row p-5 p-b-0">
        <div class="col-sm-2">
            <button class="btn btn-success" id="btn-add-operator-task">Nova Tarefa</button>
        </div>

        <div class="col-sm-10">
            <ul class="list-inline pull-right">
                <li class="input-sm" style="width: 150px; padding: 0; margin-top: 1px">
                    {{ Form::select('operator_task_operator', ['' => 'Todos Operadores'] + $operators, Request::has('operator') ? Request::get('operator') : null, ['class' => 'form-control input-sm select2']) }}
                </li>
                <li style="width: 125px; padding: 0">
                    <div class="input-group input-group-sm" style="margin-bottom: -13px;">
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        {{ Form::text('operator_task_date', @$date, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) }}
                    </div>
                </li>
                <li style="width: 195px; padding: 0">
                    <div class="input-group input-group-sm" style="margin-bottom: -13px;">
                        <div class="input-group-addon">
                            <i class="fas fa-search"></i>
                        </div>
                        {{ Form::text('operator_task_search', Request::has('search') ? Request::get('search') : null, ['class' => 'form-control input-sm']) }}
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="row row-0" style="border-top: 1px solid #ccc">
        <div class="col-sm-3" id="div-add-operator-task" style="display: none">
            <div class="p-5" style="border-right: 1px solid #ccc;">
                {{ Form::open(['route' => ['admin.operator.tasks.store'], 'class' => 'add-operator-task']) }}
                <div class="form-group is-required">
                    {{ Form::label('name', 'O que fazer?', ['class' => 'control-label']) }}
                    @if (!Setting::get('operators_tasks_require_hours'))
                    <div class="input-group">
                        {{ Form::text('name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofill' => 'off']) }}
                        <span class="input-group-btn">
                            <button class="btn btn-default open-calendar-input" data-toggle="tooltip" type="button" title="Agendar tarefa para outra data">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </span>
                    </div>
                    @else
                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'autofill' => 'off']) }}
                    @endif
                </div>
                <div class="calendar-input" style="{{ !Setting::get('operators_tasks_require_hours') ? 'display: none' : '' }}">
                    <div class="form-group">
                        {{ Form::label('date', 'Agendar tarefa para dia...', ['class' => 'control-label']) }}
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                            {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off', 'autofill' => 'off']) }}
                        </div>
                    </div>

                    <div class="row row-0">
                        <div class="col-sm-6">
                            <div class="form-group {{ Setting::get('operators_tasks_require_hours') ? 'is-required' : '' }}">
                                {{ Form::label('start_hour', 'Hora Início') }}
                                {{ Form::select('start_hour', ['' => '--:--'] + listHours(5), null, ['class' => 'form-control select2', Setting::get('operators_tasks_require_hours') ? 'required' : '']) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group {{ Setting::get('operators_tasks_require_hours') ? 'is-required' : '' }}">
                                {{ Form::label('end_hour', 'Hora Fim') }}
                                {{ Form::select('end_hour', ['' => '--:--'] + listHours(5), null, ['class' => 'form-control select2', Setting::get('operators_tasks_require_hours') ? 'required' : '']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('description', 'Descrição ou detalhes', ['class' => 'control-label']) }}
                    {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
                </div>

                <div class="form-group {{ Setting::get('operators_tasks_require_service_type') ? 'is-required' : '' }}">
                    {{ Form::label('transport_type_id', 'Tipo de Transporte', ['class' => 'control-label']) }}
                    {{ Form::select('transport_type_id', ['' => ''] + $serviceTypes, null, ['class' => 'form-control select2', Setting::get('operators_tasks_require_service_type') ? 'required' : '']) }}
                </div>

                <div class="form-group">
                    <div>
                        {{ Form::label('customer', 'Cliente') }}
                        <small>
                            <a id="edit-customer-input" href="#" style="display: none;">
                                Alterar local recolha
                            </a>
                        </small>
                    </div>
                    {{ Form::select('customer', [], null, ['class' => 'form-control select2']) }}
                </div>

                <div class="customer-input" style="display: none;">
                    <div class="form-group">
                        {{ Form::label('address', 'Morada') }}
                        {{ Form::text('address', null, ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('zip_code', 'Código Postal') }}
                        {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('city', 'Localidade') }}
                        {{ Form::text('city', null, ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('phone', 'Telefone') }}
                        {{ Form::text('phone', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('shipments[]', 'Associar aos envios', ['class' => 'control-label']) }}
                    <a class="pull-right select-all-shipments" href="#">Todos</a>
                    {{ Form::select('shipments[]', $shipments, null, ['class' => 'form-control select2 selectbox-shipments', 'multiple']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('agencies[]', 'Enviar para as agências', ['class' => 'control-label']) }}
                    <a class="pull-right select-all-agencies" href="#">Todos</a>
                    <select class="form-control select2 selectbox-agencies" name="agencies[]" multiple>
                        @foreach ($agencies as $agency)
                            <option data-ids="{{ json_encode(array_column($agency['users'], 'id')) }}" value="{{ $agency['id'] }}">{{ $agency['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    {{ Form::label('operators[]', 'Enviar para', ['class' => 'control-label']) }}
                    <a class="pull-right select-all-operators" href="#">Todos</a>
                    {{ Form::select('operators[]', $operators, null, ['class' => 'form-control select2 selectbox-operators', 'multiple']) }}
                </div>

                <button class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A adicionar...">Adicionar Tarefa</button>
                {{ Form::close() }}
            </div>
        </div>

        <div class="col-sm-12" id="div-operator-tasks-lists">
            <div id="operator-tasks-lists">
                @include('admin.operator_tasks.partials.tabs')
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" type="button">Fechar</button>
</div>

<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $('.modal [data-toggle="popover"]').popover({
        'placement': 'right',
        'html': true,
        'trigger': 'click'
    })

    $('.open-calendar-input').on('click', function() {
        $('.calendar-input').slideToggle();
    })

    $('[name="operator_task_search"]').on('keyup', function() {
        var search = $(this).val();

        if (search == '') {
            $('.task-row').show();
        } else {
            $('.task-row').hide();

            $(".task-row").each(function() {
                if ($(this).text().toUpperCase().indexOf(search.toUpperCase()) != -1) {
                    console.log('teste');
                    $(this).show();
                }
            })

        }
    })

    var operatorTaskTab = null;
    $(document).on('click', '.modal [data-toggle="tab"]', function () {
        operatorTaskTab = $(this).attr('href');
    });

    $('[name="operator_task_operator"], [name="operator_task_date"]').on('change', function() {

        var operator = $('[name="operator_task_operator"]').val();
        var date = $('[name="operator_task_date"]').val();

        $.post('{{ route('admin.operator.tasks.tabs') }}', {
            operator_task_operator: operator,
            operator_task_date: date
        }, function(data) {
            if (data.html) {
                $(data.target).html(data.html);
            }

            $(document).find('[href="'+ operatorTaskTab +'"][data-toggle="tab"]').trigger('click');

            $('[data-toggle="popover"]').popover({
                'placement': 'right',
                'html': true,
                'trigger': 'click'
            })
        })
    })

    $('.add-operator-task').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $button = $form.find('button');

        $button.button('loading');
        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.result) {
                $form.find('input,select,textarea').val('').trigger('change');
                // $(data.target).html(data.html);

                if (data.trigger_change) {
                    $(data.trigger_change).trigger('change');
                }

                $.bootstrapGrowl(data.feedback, {
                    type: 'success',
                    align: 'center',
                    width: 'auto',
                    delay: 8000
                });
            } else {
                $.bootstrapGrowl(data.feedback, {
                    type: 'error',
                    align: 'center',
                    width: 'auto',
                    delay: 8000
                });
            }

        }).always(function() {
            $button.button('reset');

            $('[data-toggle="popover"]').popover({
                'placement': 'right',
                'html': true,
                'trigger': 'click'
            })
        })
    })


    $('.select-all-shipments').on('click', function(e) {
        e.preventDefault();
        $('.selectbox-shipments option').prop('selected', true);
        $('.selectbox-shipments').trigger('change')
    })

    $('.select-all-operators').on('click', function(e) {
        e.preventDefault();
        $('.selectbox-operators option').prop('selected', true);
        $('.selectbox-operators').trigger('change')
    })

    $('.select-all-agencies').on('click', function(e) {
        e.preventDefault();
        $('.selectbox-agencies option').prop('selected', true);
        $('.selectbox-agencies').trigger('change')
    })

    var selectedUsersFromAgencies = [];
    $('.selectbox-agencies').on('change', function(ev) {
        var selectedUsers = $('.selectbox-agencies :selected').map(function(i, el) {
            return $(el).data('ids');
        });

        var removedUsers = selectedUsersFromAgencies.map(function(i, id) {
            return jQuery.inArray(id, selectedUsers) !== -1 ? null : id;
        });

        selectedUsersFromAgencies = selectedUsers;
        selectedUsersFromAgencies.each(function(i, id) {
            $('.selectbox-operators option[value=' + id + ']').prop('selected', true);
            $('.selectbox-operators').trigger('change');
        });

        if (removedUsers.length) {
            removedUsers.each(function(i, id) {
                $('.selectbox-operators option[value=' + id + ']').prop('selected', false);
                $('.selectbox-operators').trigger('change');
            });
        }
    })


    $('#edit-customer-input').on('click', function() {
        $('.customer-input').slideToggle();
    });

    $('.modal [name="customer"]').select2({
        minimumInputLength: 2,
        placeholder: "",
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customers.search', ['with_departments' => true]) }}")
    });

    $('.modal [name="customer"]').on('select2:select', function(ev) {
        var data = ev.params.data;
        $('.modal [name="address"]').val(data.address);
        $('.modal [name="zip_code"]').val(data.zip_code);
        $('.modal [name="city"]').val(data.city);
        $('.modal [name="phone"]').val(data.phone);
    });

    $('.modal [name="customer"]').on('change', function() {
        var $this = $(this);
        if ($this.val()) {
            $('.modal #edit-customer-input').show();

            $('.modal [name="shipments[]"]').parent().hide();
            $('.modal [name="shipments[]"]').val("");
        } else {
            $('.modal #edit-customer-input').hide();
            if ($('.customer-input').attr('display') != 'none') {
                $('.customer-input').hide();
            }

            $('.modal [name="shipments[]"]').parent().show();
        }
    });

    $('.modal [name="shipments[]"]').on('change', function() {
        var $this = $(this);
        if ($this.val().length) {
            $('.modal [name="customer"]').parent().hide();
            $('.modal [name="customer"]').val("");
        } else {
            $('.modal [name="customer"]').parent().show();
        }
    });

    // Show/hide add operator task form
    $('#btn-add-operator-task').on('click', function() {
        var $divAddOperatorTask = $('#div-add-operator-task');
        var $divOperatorTasksLists = $('#div-operator-tasks-lists');

        if ($divOperatorTasksLists.hasClass('col-sm-12')) {
            $divOperatorTasksLists.removeClass('col-sm-12');
            $divOperatorTasksLists.addClass('col-sm-9');
            $divAddOperatorTask.slideToggle();
        } else {
            $divAddOperatorTask.slideToggle(400, function () {
                $divOperatorTasksLists.removeClass('col-sm-9');
                $divOperatorTasksLists.addClass('col-sm-12');
            });
        }
    });
</script>
