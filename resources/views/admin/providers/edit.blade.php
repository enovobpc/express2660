@section('title')
    Fornecedores
@stop

@section('content-header')
    Fornecedores
    <small>
        {{ $action }}
    </small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.providers.index', ['tab' => $provider->type]) }}">
            @trans('Fornecedores')
        </a>
    </li>
    <li class="active">
        {{ $action }}
    </li>
@stop

@section('content')
@if($provider->exists)
<div class="row">
    <div class="col-md-12">
        <div class="box no-border m-b-15">
            <div class="box-body p-5">
                <div class="row">
                    <div class="col-xs-12 col-md-5">
                        {{--<div class="pull-left m-r-10">
                            @if($provider->filepath)
                                <img src="{{ asset($provider->getCroppa(200)) }}" onerror="this.src ='{{ img_broken(true) }}'" style="border:none" class="w-60px"/>
                            @else
                                <img src="{{ asset('assets/img/default/default.thumb.png') }}" style="border:none" class="w-60px"/>
                            @endif
                        </div>--}}
                        <div class="pull-left w-85 p-l-10">
                            <h4 class="m-t-5 m-b-5 pull-left customer-name">
                                <i class="fas fa-square" style="color: {{ $provider->type == 'carrier' ? @$provider->color : @$provider->category->color }}"></i>
                                {{ $provider->name }}
                            </h4>
                            <div class="clearfix"></div>
                            <ul class="list-inline m-b-0">
                                <li><small>@trans('Código:')</small> <b>{{ $provider->code }}</b></li>
                                @if($provider->category_id)
                                    <li><small>@trans('Categoria:')</small> <b>{{ @$provider->category->name }}</b></li>
                                @endif
                                @if($provider->created_at)
                                    <li><small>@trans('Registo:')</small> {{ @$provider->created_at->format('Y-m-d') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-7">
                        <ul class="list-inline m-t-8 m-b-0 pull-right hidden-xs">
                            @if(hasModule('purchase_invoices') && Auth::user()->ability(Config::get('permissions.role.admin'), 'purchase_invoices'))
                                <li class="w-105px">
                                    <h4 class="m-0 pull-right" style="position: absolute; margin-top: -25px">
                                        <small>@trans('Doc. Vencidos')</small><br/>
                                        <b class="balance-total-unpaid {{ $totalExpired > 1 ? 'text-red' : '' }}">{{ $totalExpired ? $totalExpired : 0 }} Docs.</b>
                                    </h4>
                                </li>
                                <li class="w-105px">
                                    <h4 class="m-0 pull-right" style="position: absolute; margin-top: -25px">
                                        <small>{{ $provider->balance_total_unpaid > 0.00 ? __('Por Liquidar') : __('A Receber') }}</small><br/>
                                        <b class="balance-total-unpaid {{ $provider->balance_total_unpaid > 0.00 ? 'text-red' : 'text-green' }}">{{ money($provider->balance_total_unpaid * -1, Setting::get('app_currency')) }}</b>
                                    </h4>
                                </li>
                            @endif

                            <li>
                                <div class="btn-group btn-group-sm" role="group" style="margin-top: -1px">
                                    @if($prevId = $provider->previousId())
                                        <a href="{{ route('admin.providers.edit', ['id' => $prevId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Anterior">
                                            <i class="fa fa-fw fa-angle-left"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-default" disabled>
                                            <i class="fa fa-fw fa-angle-left"></i>
                                        </button>
                                    @endif

                                    @if($nextId = $provider->nextId())
                                        <a href="{{ route('admin.providers.edit', [$nextId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Próximo">
                                            <i class="fa fa-fw fa-angle-right"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-default" disabled>
                                            <i class="fa fa-fw fa-angle-right"></i>
                                        </button>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row row-5">
    <div class="col-sm-3 col-md-2">
        <div class="box box-solid box-sidebar">
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active">
                        <a href="#tab-info" data-toggle="tab">
                            <i class="fas fa-fw fa-id-card"></i> @trans('Dados Gerais')
                        </a>
                    </li>
                    @if($provider->type == 'carrier')
                        <li class="{{ $provider->exists ? '' : 'disabled' }}">
                            <a href="#tab-prices" data-toggle="{{ $provider->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-euro-sign"></i> @trans('Tabela de Preços')
                            </a>
                        </li>
                        {{--<li class="{{ $provider->exists ? '' : 'disabled' }}">
                            <a href="#tab-prices-delivery" data-toggle="{{ $provider->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-euro-sign"></i> Preços Distribuição
                            </a>
                        </li>--}}
                        <li class="{{ $provider->exists ? '' : 'disabled' }}">
                            <a href="#tab-expenses" data-toggle="{{ $provider->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-euro-sign"></i> @trans('Custos dos Encargos')
                            </a>
                        </li>
                        <li class="{{ $provider->exists ? '' : 'disabled' }}">
                            <a href="#tab-cubing" data-toggle="{{ $provider->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-cube"></i> @trans('Volumetrias')
                            </a>
                        </li>
                        <li class="{{ $provider->exists ? '' : 'disabled' }}">
                            <a href="#tab-zip-codes" data-toggle="{{ $provider->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-map"></i> @trans('Códigos Postais')
                            </a>
                        </li>
                    @endif

                    @if(hasModule('purchase_invoices') && Auth::user()->ability(Config::get('permissions.role.admin'), 'purchase_invoices'))
                        <li class="{{ $provider->exists ? '' : 'disabled' }}">
                            <a href="#tab-purchase-invoices" data-toggle="{{ $provider->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-file-invoice"></i>@trans(' Conta Corrente')
                            </a>
                        </li>
                    @elseif(!hasModule('purchase_invoices'))
                        <li class="disabled">
                            <a href="#" data-toggle="tooltip" title="Não possui contratado o módulo de registo de despesas">
                                <i class="fas fa-fw fa-file-invoice"></i> @trans('Conta Corrente')
                                <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                            </a>
                        </li>
                    @endif

                    <li class="{{ $provider->exists ? '' : 'disabled' }}">
                        <a href="#tab-attachments" data-toggle="{{ $provider->exists ? 'tab' : '' }}">
                            <i class="fas fa-fw fa-file"></i> @trans('Documentação')
                            @if(!hasModule('customers_attachments'))
                                <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-sm-9 col-md-10">
        <div class="tab-content">
            <div class="tab-pane active" id="tab-info">
                @include('admin.providers.partials.info')
            </div>
            @if($provider->exists)
                @if($provider->type == 'carrier')
                    <div class="tab-pane" id="tab-prices">
                        @include('admin.providers.partials.prices_expedition')
                    </div>
                    {{--<div class="tab-pane" id="tab-prices-delivery">
                        @include('admin.providers.partials.prices_delivery')
                    </div>--}}
                    <div class="tab-pane" id="tab-expenses" data-empty="1">
                        @include('admin.providers.partials.expenses')
                    </div>
                    @endif
                    <div class="tab-pane" id="tab-cubing" data-empty="1">
                        @include('admin.providers.partials.cubing')
                    </div>
                    <div class="tab-pane" id="tab-zip-codes" data-empty="1">
                        @include('admin.providers.partials.zip_codes')
                    </div>
                <div class="tab-pane" id="tab-purchase-invoices" data-empty="1">
                    @include('admin.providers.partials.purchase_invoices')
                </div>
                <div class="tab-pane" id="tab-attachments" data-empty="1">
                    @include('admin.providers.partials.attachments')
                </div>
            @endif
        </div>
    </div>
</div>

@if($provider->exists && $provider->type == 'carrier')
    @include('admin.providers.modals.import_services_table')
    @include('admin.providers.modals.import_global_services_table')
@endif
@include('admin.providers.modals.send_balance_email')
@include('admin.zip_codes.agencies.modals.import')

@stop

@section('styles')
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
@stop

@section('scripts')
{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
<script type="text/javascript">
    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
    })

    $(document).on('change', '.form-update-price input', function(){
        $(this).css('background', '#ffffdd').css('border-color', '#fcc500');
    })

    $(document).on('change', '[name=type]', function () {
        if($(this).is(':checked')) {
            $('.carrier-options').show();
        } else {
            $('.carrier-options').hide();
            $('[name=color],[name="webservice_method"]').val('')
        }
    })

    var EXISTING_VATS = {!! json_encode($existingVats) !!}
    $('[name=vat]').on('change', function(){
        var value = $(this).val();

        if($.inArray(value, EXISTING_VATS) >= 0) {
            $('.vat-alert').show();
        } else {
            $('.vat-alert').hide();
        }
    })


    $(document).on('click', '.form-update-price button', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');

        $.post($form.attr('action'), $form.serialize(), function(data){
            $.bootstrapGrowl(data.feedback,
                {type: data.type, align: 'center', width: 'auto', delay: 8000});
            $form.find('.form-control').css('background', '#fff').css('border-color', '#ccc')
        }).error(function(){
            $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Ocorreu um erro interno ao obter os dados da tabela.",
                {type: 'error', align: 'center', width: 'auto', delay: 8000});
        })
    });


    $(document).ready(function () {

        @if($provider->type == 'carrier')
        var oTableoTableCubing;
        $(document).on('click', 'a[href="#tab-cubing"]', function(){
            $tab = $('#tab-cubing');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableoTableCubing = $('#datatable-cubing').DataTable({
                    columns: [
                        {data: 'sort', name: 'sort'},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'display_code', name: 'display_code'},
                        {data: 'name', name: 'name'},
                        {data: 'volume_min', name: 'volume_min', orderable: false, searchable: false},
                        {data: 'factor_provider', name: 'factor_provider', orderable: false, searchable: false},
                        {data: 'factor', name: 'factor', orderable: false, searchable: false},
                    ],
                    order: [[0, "asc"]],
                    ajax: {
                        url: "{{ route('admin.providers.services.datatable', $provider->id) }}",
                        type: "POST",
                        data: function (d) {},
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableoTableCubing) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });
            }
        });

        var oTableZipCode;
        $(document).on('click', 'a[href="#tab-zip-codes"]', function(){
            $tab = $('#tab-zip-codes');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableZipCode = $('#datatable-zip-codes').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'zip_code', name: 'zip_code', class: 'text-center'},
                        {data: 'agency', name: 'agency', orderable: false, searchable: false},
                        {data: 'city', name: 'city'},
                        {data: 'zone', name: 'zone', orderable: false, searchable: false},
                        {data: 'kms', name: 'kms', class: 'text-center'},
                        {data: 'services', name: 'services', orderable: false, searchable: false},
                        {data: 'is_regional', name: 'is_regional', class: 'text-center'},
                        {data: 'provider_id', name: 'provider_id', class: 'text-center'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.zip-codes.datatable') }}",
                        type: "POST",
                        data: function (d) {
                            d.provider  = ["{{ $provider->id }}"]
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableZipCode) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });
            }
        });
        @endif

        /**
         * Tab attachments
         */
        var oTableAttachments;
        $(document).on('click', 'a[href="#tab-attachments"]', function(){
            $tab = $('#tab-attachments');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableAttachments = $('#datatable-attachments').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'name', name: 'name'},
                        {data: 'sort', name: 'sort'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[3, "desc"]],
                    ajax: {
                        type: "POST",
                        url: "{{ route('admin.providers.attachments.datatable', $provider->id) }}",
                        data: function (d) {
                            d.type_id = $('[data-target="#datatable-attachments"] select[name=type]').val();
                            d.active  = $('[data-target="#datatable-attachments"] select[name=active]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableAttachments) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('[data-target="#datatable-attachments"] .filter-datatable').on('change', function (e) {
                    oTableAttachments.draw();
                    e.preventDefault();
                });
            }
        })

        $(document).on('hidden.bs.modal','#modal-remote', function(event, data){
            oTableAttachments.draw();
        })

        /**
         * Tab Invoices
         */
        var oTableInvoices;
        $(document).on('click', 'a[href="#tab-purchase-invoices"]', function(){
            $tab = $('#tab-purchase-invoices');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableInvoices = $('#datatable-purchase-invoices').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'doc_date', name: 'doc_date'},
                        {data: 'code', name: 'code'},
                        {data: 'reference', name: 'reference'},
                        {data: 'doc_type', name: 'doc_type'},
                        @if(Setting::get('billing_show_cred_deb_column'))
                        {data: 'debit', name: 'debit', class:'text-right'},
                        {data: 'credit', name: 'credit', class:'text-right'},
                        @else
                        {data: 'total', name: 'total', class:'text-right'},
                        @endif
                        {data: 'total_unpaid', name: 'total_unpaid', class:'text-right'},
                        {data: 'due_date', name: 'due_date', class: 'text-center'},
                        {data: 'payment_date', name: 'payment_date', class: 'text-center', searchable: false},
                        {data: 'actions', name: 'actions', class: 'text-center', orderable: false, searchable: false},
                        {data: 'billing_name', name: 'billing_name', visible: false},
                        {data: 'vat', name: 'vat', visible: false},
                        {data: 'total', name: 'total', visible: false},
                    ],
                    order: [[2, "desc"]],
                    ajax: {
                        type: "POST",
                        url: "{{ route('admin.billing.balance.datatable.balance', [$provider->id, 'source' => 'providers']) }}",
                        data: function (d) {
                            d.hide_payments     = $('[data-target="#datatable-purchase-invoices"] input[name=hide_payments]:checked').length;
                            d.provider          = $('[data-target="#datatable-purchase-invoices"] select[name=provider]').val();
                            d.paid              = $('[data-target="#datatable-purchase-invoices"] select[name=paid]').val();
                            d.type              = $('[data-target="#datatable-purchase-invoices"] select[name=type]').val();
                            d.ignore_stats      = $('[data-target="#datatable-purchase-invoices"] select[name=ignore_stats]').val();
                            d.doc_type          = $('[data-target="#datatable-purchase-invoices"] select[name=doc_type]').val();
                            d.doc_id            = $('[data-target="#datatable-purchase-invoices"] input[name=doc_id]').val();
                            d.date_unity        = $('[data-target="#datatable-purchase-invoices"] select[name=date_unity]').val();
                            d.date_min          = $('[data-target="#datatable-purchase-invoices"] input[name=date_min]').val();
                            d.date_max          = $('[data-target="#datatable-purchase-invoices"] input[name=date_max]').val();
                            d.payment_method    = $('[data-target="#datatable-purchase-invoices"] select[name=payment_method]').val();
                            d.target            = $('[data-target="#datatable-purchase-invoices"] select[name=target]').val();
                            d.target_id         = $('[data-target="#datatable-purchase-invoices"] select[name=target_id]').val();
                            d.sense             = $('[data-target="#datatable-purchase-invoices"] select[name=sense]').val();
                            d.deleted           = $('[data-target="#datatable-purchase-invoices"] input[name=deleted]:checked').length;
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableInvoices) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                //show deleted shipments
                $('[data-target="#datatable-purchase-invoices"]').on('change', '[name="deleted"],[name="hide_payments"]', function (e) {
                    oTableInvoices.draw();
                });

                $('[data-target="#datatable-purchase-invoices"] .filter-datatable').on('change', function (e) {
                    oTableInvoices.draw();
                    e.preventDefault();

                    var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                    exportUrl = exportUrl + '?provider={{ $provider->id }}&' + Url.getQueryString(Url.current())
                    $('[data-toggle="export-url"]').attr('href', exportUrl);

                    var printUrl = Url.removeQueryString($('[data-toggle="print-url"]').attr('href'));
                    printUrl = printUrl + '?provider={{ $provider->id }}&' + Url.getQueryString(Url.current())
                    $('[data-toggle="print-url"]').attr('href', printUrl);
                });
            }
        })

        $(document).on('hidden.bs.modal','#modal-remote', function(event, data){
            oTableAttachments.draw();
        })
    });


    $('.btn-check-all').on('click', function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('[data-id="'+target+'"]').prop('checked', true);
    })

    $('select[name="agency"], select[name="customer_id"]').on('change', function(){
        var $target     = $(this).closest('ul');
        var agencyId    = $target.find('select[name="agency"]').val();
        var customerId  = $target.find('select[name="customer_id"]').val();
        var type        = $target.find('select[name="agency"]').data('type');
        var url = Url.current();

        url = Url.updateParameter(url, 'agency', agencyId);
        url = Url.updateParameter(url, 'type', type);

        if(type == 'expedition'){
            url = Url.updateParameter(url, 'customer', customerId);
        }

        $('.loading-table').show();
        window.location = url;
    })

    $(document).on('click', '[href="#tab-prices-delivery"]', function () {
        $('.modal [name="type"]').val('delivery')
    })

    $(document).on('click', '[href="#tab-prices"]', function () {
        $('.modal [name="type"]').val('expedition')
    })

    $(document).ready(function(){
        $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');
    })

    var parentTab = $('a[href="#tab-{{ Request::get("tab") }}"]').data('parent-tab');
    $('a[href="' + parentTab + '"]').trigger('click');

    /**
     * SEARCH CUSTOMER
     * ajax method
     */
    $("select[name=customer_id], select[name=assigned_customer_id]").select2({
        ajax: {
            url: '{{ route("admin.providers.search.customer") }}',
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()

                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        allowClear: true
    });



    //export selected
    $(document).on('change', '.row-select',function(){
        var queryString = '';
        $('input[name=row-select]:checked').each(function(i, selected){
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
        });

        var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
        $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
    });

    $(document).on('click', '.search-zip-codes', function(){
        var district = $('#modal-import-zip-codes [name="district"]').val();
        var country  = $('#modal-import-zip-codes [name="country"]').val();
        var county   = $('#modal-import-zip-codes [name="county"]').val();

        $('.import-search-results').html('<div class="helper">' +
            '<i class="fas fa-spin fa-circle-notch"></i> A procurar Códigos Postais...' +
            '</div>')

        $.post('{{ route('admin.zip-codes.search') }}', {district: district, county:county, country:country}, function(data){
            $('.import-search-results').html(data);
        })
    })

    $(document).on('change', '[name=select-all-zip-codes]', function(){

        if($(this).is(':checked')) {
            $('.select-zip-code').prop('checked', true)
        } else {
            $('.select-zip-code').prop('checked', false)
        }
    })

    $(document).on('change', '.datatable-filters-area-extended [name="district"]', function () {
        var district = $(this).val();

        var options = $('[name="all_counties"]').find('optgroup[label="'+district+'"]').html();
        $('select[name="county"]').html('<option></option>' + options)
    })

    $(document).on('change', '#modal-import-zip-codes [name="country"]', function(){
        var country = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {country:country}, function(data){
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })

    $(document).on('change', '#modal-import-zip-codes [name="district"]', function(){
        var country = $('#modal-import-zip-codes [name="country"]').val();
        var district = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {country:country, district:district}, function(data){
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })
    </script>
@stop