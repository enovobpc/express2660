{{ Form::open(['route' => ['admin.printer.shipments.delivery-map', ''], 'method' => 'GET']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Imprimir mapa de entregas</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('manifest_date', 'Período de datas', ['class' => 'control-label']) }}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                    </span>
                    {{ Form::text('manifest_date_start', Date::today()->subDays(1)->format('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon">
                       Até
                    </span>
                    {{ Form::text('manifest_date_end', Date::today()->format('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('manifest_type', 'Data', ['class' => 'control-label']) }}
                {{ Form::select('manifest_type', ['' => 'Data do Envio', 'delivery' => 'Data de entrega'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('manifest_sort', 'Ordenar por', ['class' => 'control-label']) }}
                {{ Form::select('manifest_sort', ['' => 'Código Postal', 'city' => 'Localidade',  'name' => 'Nome', 'tracking_code' => 'Código de envio'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('manifest_sort_dir', 'Direção Ordenação', ['class' => 'control-label']) }}
                {{ Form::select('manifest_sort_dir', ['asc' => 'Ascendente', 'desc' => 'Descendente'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <hr style="margin: 3px 0 15px 0"/>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="selection-container">
                <div class="select-all pull-right">
                    <label style="font-weight: normal">
                        {{ Form::checkbox('all', 1, true) }}
                        Todos
                    </label>
                </div>
                <h4 class="pull-left m-0">Motorista</h4>
                <div class="clearfix"></div>
                <div class="selection-container-fltr">
                    {{ Form::text('manifest_fltr', null, ['class' => 'form-control input-sm', 'placeholder' => 'Procurar na lista...']) }}
                </div>
                <div class="item-content nicescroll">
                    <ul class="list-unstyled">
                        @foreach($operatorsList as $id => $value)
                        <li class="check-item" data-filter-text="{{ strtolower(removeAccents($value)) }}">
                            <div>
                                <label>
                                    {{ Form::checkbox('manifest_operators[]', $id, true) }}
                                    {{ $value }}
                                </label>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="selection-container">
                <div class="select-all pull-right">
                    <label style="font-weight: normal">
                        {{ Form::checkbox('all', 1, true) }}
                        Todos
                    </label>
                </div>
                <h4 class="pull-left m-0">Estado</h4>
                <div class="clearfix"></div>
                <div class="selection-container-fltr">
                    {{ Form::text('manifest_fltr', null, ['class' => 'form-control input-sm', 'placeholder' => 'Procurar na lista...']) }}
                </div>
                <div class="item-content nicescroll">
                    <ul class="list-unstyled">
                        @foreach($statusList as $id => $value)
                            <li class="check-item" data-filter-text="{{ strtolower(removeAccents($value)) }}">
                                <div>
                                    <label>
                                        {{ Form::checkbox('manifest_status[]', $id, in_array($id, $activeStatusList) ? true : false) }}
                                        {{ $value }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="selection-container">
                <div class="select-all pull-right">
                    <label style="font-weight: normal">
                        {{ Form::checkbox('all', 1, true) }}
                        Todos
                    </label>
                </div>
                <h4 class="pull-left m-0">Serviço</h4>
                <div class="clearfix"></div>
                <div class="selection-container-fltr">
                    {{ Form::text('manifest_fltr', null, ['class' => 'form-control input-sm', 'placeholder' => 'Procurar na lista...']) }}
                </div>
                <div class="item-content nicescroll">
                    <ul class="list-unstyled">
                        @foreach($servicesList as $id => $value)
                            <li class="check-item" data-filter-text="{{ strtolower(removeAccents($value)) }}">
                                <div class="check-item">
                                    <label>
                                        {{ Form::checkbox('manifest_services[]', $id, true) }}
                                        {{ $value }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="selection-container">
                <div class="select-all pull-right">
                    <label style="font-weight: normal">
                        {{ Form::checkbox('all', 1, true) }}
                        Todos
                    </label>
                </div>
                <h4 class="pull-left m-0">Fornecedor</h4>
                <div class="clearfix"></div>
                <div class="selection-container-fltr">
                    {{ Form::text('manifest_fltr', null, ['class' => 'form-control input-sm', 'placeholder' => 'Procurar na lista...']) }}
                </div>
                <div class="item-content nicescroll">
                    <ul class="list-unstyled">
                        @foreach($providersList as $id => $value)
                            <li class="check-item" data-filter-text="{{ strtolower(removeAccents($value)) }}">
                                <div class="check-item">
                                    <label>
                                        {{ Form::checkbox('manifest_providers[]', $id, true) }}
                                        {{ $value }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <a href="" data-toggle="print-manifest-url" class="btn btn-primary" target="_blank">Imprimir</a>
</div>
{{ Form::close() }}

<style>
    .item-content {
        height: 200px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 8px;
        border-radius: 3px;
    }

    .item-content label {
        font-weight: normal;
    }

    .selection-container-fltr input {
        margin-bottom: -3px;
        border-radius: 3px 3px 0 0;
        z-index: 1;
        position: relative;
    }
</style>

<script>
    setBtnUrl();

    $(".nicescroll").niceScroll(Init.niceScroll());
    $(".modal .select2").select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $('.modal [name=manifest_date_start], .modal [name=manifest_date_end],.modal [name=manifest_sort],.modal [name=manifest_sort_dir]').on('change', function(){
        setBtnUrl();
    })

    $('[name="all"]').on('change', function () {
        if($(this).is(':checked')) {
            $(this).closest('.selection-container').find('.check-item input').prop('checked', true);
        } else {
            $(this).closest('.selection-container').find('.check-item input').prop('checked', false);
        }

        setBtnUrl();
    })

    $('.check-item input').on('change', function () {
        setBtnUrl();
    })

    $('.modal [name="manifest_fltr"]').on('keyup', function(){
        var value = $(this).val().toLowerCase();
        var regex = new RegExp(value + '\\w*\\b');
        var $target = $(this).closest('.selection-container').find('.check-item');

        $target.show();
        $(this).closest('.selection-container').find('[data-filter-text]').hide().filter(function () {
            return regex.test($(this).data('filter-text'));
        }).show();
    })

    function setBtnUrl() {
        var url    = $('[data-toggle="print-manifest-url"]').closest('form').attr('action')
        var params = $('[data-toggle="print-manifest-url"]').closest('form').serialize()
        $('[data-toggle="print-manifest-url"]').attr('href', url + params);
    }
</script>