@section('title')
    Artigos e Stocks
@stop

@section('content-header')
    Artigos e Stocks
    <small>
        Gerir Artigo
    </small>
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão Logística')</li>
    <li>
        <a href="{{ route('admin.logistic.products.index') }}">@trans('Artigos e Stocks')</a>
    </li>
    <li class="active">@trans('Gerir Artigo')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box no-border m-b-15">
                <div class="box-body p-5">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="pull-left m-r-10">
                                @if($product->filepath)
                                    <a href="{{ asset($product->filepath) }}" target="_blank" class="preview-img">
                                        <img src="{{ asset($product->getCroppa(200, 200)) }}" onerror="this.src ='{{ img_broken(true) }}'" style="border:none;  max-height: 60px" class="w-60px"/>
                                    </a>
                                @else
                                    <img src="{{ asset('assets/img/default/default.thumb.png') }}" style="border:none; max-height: 60px" class="w-60px"/>
                                @endif
                            </div>
                            <div class="pull-left w-85">
                                <h4 class="m-t-5 pull-left customer-name">
                                    {{ $product->name }}
                                </h4>
                                <div class="clearfix"></div>
                                <ul class="list-inline m-b-0">
                                    <li><small>@trans('SKU:')</small> <b>{{ $product->sku }}</b></li>
                                    <li class="text-muted">
                                        <i class="fas fa-user"></i>
                                        <a href="{{ route('admin.customers.edit', $product->customer_id) }}">{{ @$product->customer->code }} - {{ @$product->customer->name }}</a>
                                    </li>
                                    @if($product->created_at)
                                        <li><small>@trans('Registo:')</small> {{ @$product->created_at->format('Y-m-d') }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <ul class="list-inline m-t-8 m-b-0 pull-right hidden-xs">
                               <li class="w-105px">
                                    <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;">
                                        <small>@trans('Disponível')</small><br/>
                                        <b class="balance-total-unpaid">
                                            @if($product->stock_status == \App\Models\Logistic\Product::STATUS_BLOCKED)
                                                <span class="text-red">
                                                    <i class="fas fa-fw fa-ban"></i> {{ $product->stock_total - $product->stock_allocated }}
                                                    <small class="text-uppercase text-red">{{ $product->unity ? trans('admin/global.measure-units-abbrv.'.$product->unity) : __('UN')  }}</small>
                                                </span>
                                            @else
                                                <i class="fas fa-fw fa-circle text-{{ $product->getStockLabel() }}"></i> {{ $product->stock_total - $product->stock_allocated }}
                                                <small class="text-uppercase">{{ $product->unity ? trans('admin/global.measure-units-abbrv.'.$product->unity) : __('UN')  }}</small>
                                            @endif
                                        </b>
                                    </h4>
                                </li>

                                @if(!$allocations->isEmpty())
                                <li class="w-105px">
                                    <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;">
                                        <small>Total</small><br/>
                                        <b class="balance-total-unpaid">
                                            {{ $product->stock_total }}
                                            <small class="text-uppercase">{{ $product->unity ? trans('admin/global.measure-units-abbrv.'.$product->unity) : __('UN')  }}</small>
                                        </b>
                                    </h4>
                                </li>
                                @endif

                                @if($product->lote || $product->serial_no)
                                <li class="w-105px">
                                    <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;" data-toggle="tooltip" title="@trans('Stock global de toda a referência') {{ $product->sku }}">
                                        <small>@trans('Stock Global')</small><br/>
                                        <b class="balance-total-unpaid">
                                            <i class="fas fa-fw fa-globe"></i> {{ $globalStock }}
                                            <small class="text-uppercase">{{ $product->unity ? trans('admin/global.measure-units-abbrv.'.$product->unity) : __('UN')  }}</small>
                                        </b>
                                    </h4>
                                </li>
                                @endif
                                <li class="divider"></li>
                                <li>
                                    <div class="btn-group btn-group-sm pull-right" role="group">
                                        <div class="btn-group btn-group-sm pull-right" role="group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-print"></i> @trans('Imprimir') <i class="fas fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('admin.logistic.products.labels', $product->id) }}"
                                                       data-toggle="modal"
                                                       data-target="#modal-remote">
                                                        <i class="fas fa-print"></i> @trans('Etiquetas por localização')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.logistic.products.labels.print', [$product->id, 'type' => 'product']) }}">
                                                        <i class="fas fa-print"></i> @trans('Etiqueta do Produto')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="btn-group btn-group-sm" role="group" style="margin-top: -30px">
                                        @if($nextId = $product->nextId())
                                            <a href="{{ route('admin.logistic.products.show', [$nextId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="@trans('Anterior')">
                                                <i class="fa fa-fw fa-angle-left"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-default" disabled>
                                                <i class="fa fa-fw fa-angle-left"></i>
                                            </button>
                                        @endif

                                        @if($prevId = $product->previousId())
                                            <a href="{{ route('admin.logistic.products.show', ['id' => $prevId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="@trans('Próximo')">
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

    <div class="row row-5">
        <div class="col-md-3 col-lg-2">
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li>
                            <a href="#tab-info" data-toggle="tab">
                                <i class="fas fa-fw fa-info-circle"></i> @trans('Detalhes do Artigo')
                            </a>
                        </li>
                        <li>
                            <a href="#tab-locations" data-toggle="tab">
                                <i class="fas fa-fw fa-pallet"></i> @trans('Localizações')
                            </a>
                        </li>
                        <li>
                            <a href="#tab-history" data-toggle="tab">
                                <i class="fas fa-fw fa-exchange-alt"></i> @trans('Movimentações')
                            </a>
                        </li>
                        @if(!$allocations->isEmpty())
                            <li>
                                <a href="#tab-allocated" data-toggle="tab">
                                    <i class="fas fa-fw fa-link"></i> @trans('Alocações')
                                </a>
                            </li>
                        @endif

                        @if($product->serial_no || $product->lote)
                        <li>
                            <a href="#tab-serials" data-toggle="tab">
                                @if($product->serial_no)
                                    <i class="fas fa-fw fa-tags"></i> @trans('Números Série')
                                @else
                                    <i class="fas fa-fw fa-tags"></i> @trans('Lotes')
                                @endif
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="#tab-images" data-toggle="tab">
                                <i class="fas fa-fw fa-images"></i> @trans('Imagens')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="tab-content">
                <div class="active tab-pane" id="tab-info">
                    @include('admin.logistic.products.partials.info')
                </div>
                <div class="tab-pane" id="tab-locations">
                    @include('admin.logistic.products.partials.locations')
                </div>
                <div class="tab-pane" id="tab-history" data-empty="1">
                    @include('admin.logistic.products.partials.history')
                </div>

                @if(!$allocations->isEmpty())
                    <div class="tab-pane" id="tab-allocated">
                        @include('admin.logistic.products.partials.allocated')
                    </div>
                @endif

                @if($product->serial_no || $product->lote)
                <div class="tab-pane" id="tab-serials">
                    @include('admin.logistic.products.partials.serials')
                </div>
                @endif
                <div class="tab-pane" id="tab-images">
                    @include('admin.logistic.products.partials.images')
                </div>
            </div>
        </div>
    </div>
@stop

@section('styles')
    {{ Html::style('vendor/dropzone/dist/min/dropzone.min.css') }}
    {{ Html::style('vendor/magnific-popup/dist/magnific-popup.css') }}
    <style>
        .dropzone {
            border: 2px dashed rgba(0,0,0,0.3);
            padding: 10px;
            min-height: 50px;
        }
    </style>
@stop

@section('scripts')
    {{ Html::script('vendor/html.sortable/dist/html.sortable.min.js')}}
    {{ Html::script('vendor/dropzone/dist/min/dropzone.min.js') }}
    {{ Html::script('vendor/magnific-popup/dist/jquery.magnific-popup.js') }}
    <script>

        $("select[name=customer_id]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
        });

        $('[data-dismiss="fileinput"]').on('click', function () {
            $('[name=delete_photo]').val(1);
        })

        $('.preview-img').magnificPopup({
            type:'image'
        });

        $('.uploaded-images .image-item').magnificPopup({
            type:'image'
        });

        $(document).on('change', '[name=has_serial]', function(){
            $('[name=lote],[name=serial_no], [name=expiration_date]').val('').prop('required', false);
            $('.has-lote, .has-serial, .expiration-date').hide();
            if($(this).val() == 'serial') {
                $('.has-serial').show();
                $('[name=serial_no]').prop('required', true);
            } else {
                $('.has-lote, .expiration-date').show();
                $('[name=lote]').prop('required', true);
            }
        })

        /**
         * Separator Images
         *
         * @returns {undefined}
         */
        //$(".dropzone").dropzone(Init.dropzoneTranslations());

        $('#tab-images').find('.sortable').sortable({
            forcePlaceholderSize: true,
            placeholder: '<li><div class="w-150px"></div></li>'
        }).bind('sortupdate', function (e, ui) {
            $('#save-images-order').removeAttr('disabled');
        });

        //save images order
        $(document).on('click', '#save-images-order', function (e) {
            e.preventDefault();

            $button = $(this);
            $button.button('loading');

            //get array of ordered IDs
            var dataList = $(".sortable > li").map(function () {
                return $(this).data("id");
            }).get();

            $.post("{{ route('admin.logistic.products.images.sort.update', $product->id) }}", {'ids[]': dataList}, function (data) {
                $.bootstrapGrowl(data.message, {type: data.type});
            }).fail(function () {
                $.bootstrapGrowl("Ocorreu um erro tente mais tarde", {type: 'danger'});
            }).always(function () {
                $button.button('reset');
            });
        });


        var oTableHistory;
        $(document).on('click', 'a[href="#tab-history"]', function(){
            $tab = $('#tab-history');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                var oTableHistory = $('#datatable-history').DataTable({
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'source.name', name: 'source.name', orderable: false, searchable: false},
                        {data: 'destination.name', name: 'destination.name', orderable: false, searchable: false},
                        {data: 'qty', name: 'qty'},
                        {data: 'document', name: 'document'},
                        {data: 'obs', name: 'obs'},
                        {data: 'user.name', name: 'user.name', orderable: false, searchable: false},
                    ],
                    order: [[1, "desc"]],
                    ajax: {
                        url: "{{ route('admin.logistic.products.history.datatable', $product->id) }}",
                        type: "POST",
                        data: function (d) {
                            d.action        = $('[data-target="#datatable-history"] select[name=action]').val();
                            d.source        = $('[data-target="#datatable-history"] select[name=source]').val();
                            d.destination   = $('[data-target="#datatable-history"] select[name=destination]').val();
                            d.operator      = $('[data-target="#datatable-history"] select[name=operator]').val()
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableHistory) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('[data-target="#datatable-history"] .filter-datatable').on('change', function (e) {
                    oTableHistory.draw();
                    e.preventDefault();
                });
            }
        })

        var oTableSerials;
        $(document).ready(function () {

            oTableSerials = $('#datatable-serials').DataTable({
                columns: [
                    {data: 'sku', name: 'sku'},
                    {data: 'lote', name: 'lote'},
                    {data: 'expiration_date', name: 'expiration_date'},
                    {data: 'stock_total', name: 'stock_total'},
                    {data: 'locations', name: 'locations', searchable: false, orderable: false},
                    {data: 'last_update', name: 'last_update'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'serial_no', name: 'serial_no', visible: false},
                    {data: 'id', name: 'id', visible: false},
                ],
                stateSave: false,
                order: [[1, "desc"]],
                ajax: {
                    url: "{{ route('admin.logistic.products.serials.datatable', $product->id) }}",
                    type: "POST",
                    data: function (d) {
                        d.status        = $('[data-target="#datatable-serials"] select[name=status]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableSerials) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-serials"] .filter-datatable').on('change', function (e) {
                oTableSerials.draw();
                e.preventDefault();
            });
        });

        $(document).on('change', '[name=family_id]', function() {
            $(document).find('[name=subcategory_id]').select2('destroy');
            $(document).find('[name=subcategory_id]').html('<option></option>').select2(Init.select2());
        })

        $(document).on('change', '[data-child]', function() {
            var target   = $(this).data('child');
            var parentId = $(this).val();

            if(target == 'models') {
                type     = 'models';
                $obj     = $(document).find('[name=model_id]')
                $loading = $obj.closest('.form-group').find('.bloading').show();
            } else if(target == 'categories') {
                type     = 'categories';
                $obj     = $(document).find('[name=category_id]')
                $loading = $obj.closest('.form-group').find('.bloading').show();
            } else if(target == 'subcategories') {
                type     = 'subcategories';
                $obj     = $(document).find('[name=subcategory_id]')
                $loading = $obj.closest('.form-group').find('.bloading').show();
            }

            $.post("{{ route('admin.logistic.products.getList', '') }}/" + type, {customer: '{{ $product->customer_id }}', parent_id:parentId}, function(data){
                $obj.select2('destroy')
                $obj.replaceWith(data.html)
            }).always(function(){
                $('.select2').select2(Init.select2());
                $('.bloading').hide();
            })
        })

        $(document).ready(function(){
            $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');
        })
    </script>
@stop