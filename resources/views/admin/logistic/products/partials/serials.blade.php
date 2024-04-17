<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-12">
                {{--<h4 class="form-divider no-border" style="margin-top: -8px; margin-bottom: 20px;">
                    <i class="fas fa-fw fa-tags"></i> {{ $product->serial_no ? 'Números Série' : 'Lotes' }}
                </h4>--}}
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-serials">
                    <li class="fltr-primary w-155px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="w-100px pull-left form-group-sm">
                            {{ Form::select('status', array('' => __('Todos')) + trans('admin/logistic.products.status'), fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-220px">
                        <strong>@trans('Localização')</strong><br class="visible-xs"/>
                        <div class="w-130px pull-left form-group-sm">
                            {{ Form::select('locations', array('' => __('Todos')) + $locations, fltr_val(Request::all(), 'locations'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable-serials" class="table table-striped table-dashed table-hover">
                        <thead>
                            <tr>
                                <th class="w-100px">@trans('SKU')</th>
                                <th>{{ $product->serial_no ? __('Número Série') : __('Lote') }}</th>
                                <th class="w-80px">@trans('Validade')</th>
                                <th class="w-1">@trans('Stock')</th>
                                <th class="w-120px">@trans('Localização')</th>
                                <th class="w-70px">@trans('Alterado')</th>
                                <th class="w-70px"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>