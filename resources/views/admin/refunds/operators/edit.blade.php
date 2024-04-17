<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Conferir reembolsos</h4>
</div>
<div class="modal-body">
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-shipments">
        <li st>
            <h4 class="m-0" style="margin-top: -10px; padding-bottom: 9px; margin-right: 10px; border-right: 1px solid #ccc; padding-right: 16px;">
                <small>Operador</small><br/>
                <b>{{ $operator->name }}</b>
            </h4>
        </li>
        <li>
            <h4 class="m-0" style="margin-top: -10px; padding-bottom: 9px; margin-right: 10px; border-right: 1px solid #ccc; padding-right: 16px;">
                <small>Reembolsos</small><br/>
                <b>{{ money($operator->total_refunds, Setting::get('app_currency')) }}</b>
            </h4>
        </li>
        <li>
            <h4 class="m-0" style="margin-top: -10px; padding-bottom: 9px; margin-right: 10px; border-right: 1px solid #ccc; padding-right: 16px;">
                <small>Pag. Destino</small><br/>
                <b>{{ money($operator->total_recipient, Setting::get('app_currency')) }}</b>
            </h4>
        </li>
        <li>
            <h4 class="m-0" style="margin-top: -10px; padding-bottom: 9px; margin-right: 10px; border-right: 1px solid #ccc; padding-right: 16px;">
                <small>Total</small><br/>
                <b>{{ money($operator->total_refunds + $operator->total_recipient, Setting::get('app_currency')) }}</b>
            </h4>
        </li>
        <li class="fltr-primary w-150px">
            <div style="top: 0;display: block;position: absolute;">
                <strong>Via</strong><br class="visible-xs"/>
                <div class="w-120px pull-left form-group-sm">
                    {{ Form::select('provider', $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </div>
        </li>
    </ul>
    <table id="datatable-shipments" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th></th>
            <th class="w-90px">TRK</th>
            <th>Destinatário</th>
            <th class="w-1">Serviço</th>
            <th class="w-1">Remessa</th>
            <th class="w-1">Cliente</th>
            <th class="w-1">Reembolso</th>
            <th class="w-1">Pag.Destino</th>
            <th class="w-1">Outras Ações</th>
            <th class="w-1"></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    </div>
</div>

<style>
    .modal-body .select2-container .select2-selection--single {
        padding: 2px 8px;
        height: 26px;
        font-size: 12px;
    }

    .modal-body .select2-container--default,
    .modal-body .select2-selection--single .select2-selection__arrow {
        height: 26px;
        right: 2px;
    }
</style>
<script>

    var oTable2 = $('#datatable-shipments').DataTable({
        columns: [
            {data: 'tracking_code', name: 'tracking_code', visible: false},
            {data: 'id', name: 'id'},
            {data: 'recipient_name', name: 'recipient_name'},
            {data: 'service_id', name: 'service_id', searchable: false},
            {data: 'volumes', name: 'volumes', searchable: false},
            {data: 'customer', name: 'customer', searchable: false},
            {data: 'refund', name: 'refund', searchable: false},
            {data: 'cod', name: 'cod', searchable: false},
            {data: 'extra', name: 'extra', searchable: false},
            {data: 'actions', name: 'actions', orderable: false, searchable: false},

            {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
            {data: 'sender_city', name: 'sender_city', visible: false},
            {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
            {data: 'recipient_city', name: 'recipient_city', visible: false},
        ],
        ajax: {
            url: "{{ route('admin.operator-refunds.operator.datatable', [$operator->id]) }}",
            type: "POST",
            data: function (d) {
                d.date_min   = '{{ $dtMin }}';
                d.date_max   = '{{ $dtMax }}';
                d.date_unity = '{{ $dtUnity }}'
                d.provider   = $('.modal [name="provider_id"]').val()
            },
            beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
            complete: function () {
                $('.select2').select2(Init.select2());

                $("select[name=customer_id]").select2({
                    ajax: {
                        url: "{{ route('admin.shipments.search.customer') }}",
                        dataType: 'json',
                        method: 'post',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                });
            }
        }
    });

    $('.modal .filter-datatable').on('change', function (e) {
        oTable2.draw();
        e.preventDefault();
    });

    $(document).on('click', '.modal-body .edit-price-btn', function(){
        $(this).closest('.edit-btn-group').hide();
        $(this).closest('.editor-block').find('.edit-price').show()
    })

    $(document).on('click', '.modal', function () {
        $('.conferrer-popup').hide()
    });

    $(document).on('click', '.conferrer-popup, .edit-obs', function (e) {
        e.stopPropagation();
    })

    $(document).on('click', '.modal-body .edit-obs', function(){
        $('.conferrer-popup').hide()
        $(this).closest('.editor-block').find('.conferrer-popup').show();
    })

    $(document).on('click', '.edit-customer-btn', function(){
        $(this).hide();
        $(this).closest('.editor-block').find('.edit-customer').show()
    })

    $('.modal-body').on('click', '.save-row', function(){
        var $tr = $(this).closest('tr');
        var url =  "{{ route('admin.operator-refunds.update', '') }}/" + $(this).data('id')
        var totalPriceRecipient    = $tr.find('[name="total_price_for_recipient"]').val();
        var recipientPaymentMethod = $tr.find('[name="recipient_payment_method"]').val();
        var chargePrice            = $tr.find('[name="charge_price"]').val();
        var refundPaymentMethod    = $tr.find('[name="refund_payment_method"]').val();
        var obsRefund              = $tr.find('[name="obs_refund"]').val();
        var obsRecipient           = $tr.find('[name="obs_recipient"]').val();
        var obsCustomer            = $tr.find('[name="obs_customer"]').val();
        var customerId             = $tr.find('[name="customer_id"]').val();
        var ignoreBilling          = $tr.find('[name="ignore_billing"]').is(':checked');
        var registCashier          = $tr.find('[name="regist_cashier"]').is(':checked');
        var printProof             = $tr.find('[name="print_proof"]').is(':checked');
        var valid   = true;
        var $button = $(this);


        if(typeof recipientPaymentMethod !== 'undefined' && recipientPaymentMethod == '') {
            valid = false;
            Growl.error('Pagamento no Destino: Deve indicar a forma de recebimento.')
        }

        if(typeof refundPaymentMethod !== 'undefined' && refundPaymentMethod == '') {
            valid = false;
            Growl.error('Reembolso: Deve indicar a forma de recebimento.')
        }

        if(valid) {
            $button.button('loading');

            $.ajax({
                type: "PUT",
                url: url,
                data: {
                    'total_price_for_recipient' : totalPriceRecipient,
                    'recipient_payment_method' : recipientPaymentMethod,
                    'charge_price' : chargePrice,
                    'refund_payment_method' : refundPaymentMethod,
                    'ignore_billing' : ignoreBilling,
                    'regist_cashier' : registCashier,
                    'obs_refund' : obsRefund,
                    'obs_recipient' : obsRecipient,
                    'obs_customer' : obsCustomer,
                    'customer_id' : customerId,
                    'print_proof' : printProof
                },
                success: function (data) {
                    if(data.result) {
                        $tr.find('.confered').show();
                        $tr.find('.not-confered').hide();
                        Growl.success(data.feedback)

                        if (data.printProof) {
                            if (window.open(data.printProof, '_blank')) {
                                $('#modal-remote').modal('hide');
                            } else {
                                $('#modal-remote').find('.modal').find('.modal-content').html(data.html);
                            }
                        }

                    } else {
                        Growl.error(data.feedback)
                    }
                }
            }).fail(function () {
                Growl.error500()
            }).always(function () {
                $button.button('reset')
            });
        }
    })
</script>