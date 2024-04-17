{{ Form::open(['route' => 'admin.webservices.sync.shipments', 'class' => 'webservice-sync-date']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="fas fa-file-refresh"></i> Sincronizar Envios</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('webservice_sync_customer', 'Sincronizar envios do cliente', ['class' => 'control-label']) }}
                {{ Form::select('webservice_sync_customer',  [], null, ['class' => 'form-control select2', 'data-placeholder' => 'Todos os clientes']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('webservice', 'Webservice', ['class' => 'control-label']) }}
                {{ Form::select('webservice',  ['' => 'Todos'] + $webserviceMethods, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required m-b-0">
                {{ Form::label('start_date', 'Data Inicial', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('start_date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required m-b-0">
                {{ Form::label('end_date', 'Data Final', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('end_date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group m-0">
                <div class="checkbox m-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('sync_only_active', 1, true) }}
                        Sincronizar só clientes com atividade entre as datas indicadas.
                    </label>
                    {!! tip('Esta opção torna a sincronização mais rápida e permite poupar recursos em sistema.
                    Só vão ser sincronizados os envios de clientes que tenham realizado envios no sistema ENOVO TMS
                    no perido de datas indicado. Clientes inativos ou que não tenham envios no sistema
                    ENOVO nas datas indicadas serão ignorados.') !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> A sincronizar...">Sincronizar</button>
</div>
{{ Form::close() }}

<script>
    $('.webservice-sync-date .select2').select2(Init.select2());
    $('.webservice-sync-date [name=start_date], .webservice-sync-date [name=end_date]').datepicker(Init.datepicker());

    $("select[name=webservice_sync_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $(".webservice-sync-date [name=start_date]").on('change', function(){
        var date = $(this).val()
        $('.webservice-sync-date [name=end_date]').datepicker('remove')
        $(".webservice-sync-date [name=end_date]").val(date)

        if(date != '') {
            $('.webservice-sync-date [name=end_date]').datepicker({
                format: 'yyyy-mm-dd',
                language: 'pt',
                todayHighlight: true,
                startDate: date
            });
        }
    })

    $("select[name=webservice_sync_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $('form.webservice-sync-date').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.button('loading')

        var webservice = $form.find('[name="webservice"]').val();
        var customer   = $('[name="webservice_sync_customer"]').val();
        var startDate  = $form.find('[name="start_date"]').val();
        var endDate    = $form.find('[name="end_date"]').val();
        var onlyActive = $form.find('[name="sync_only_active"]').is(':checked');

        $.post($form.attr('action'), {webservice:webservice, start_date: startDate, end_date: endDate, customer:customer, onlyActive:onlyActive}, function(){
            oTable.draw();
            $('#modal-webservice-sync').modal('hide');
            Growl.success("Sincronização com sucesso.");
        }).fail(function(){
            Growl.error500();
        }).always(function(){
            $submitBtn.button('reset');
        })
    })

    $('.webservice-sync-customer-reset').on('click', function(){
        $('[name="webservice_sync_customer"]').html('<option value="">Todos</option>').trigger('change');
    })
</script>