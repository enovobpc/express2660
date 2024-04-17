<div class="box no-border">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-business-tipology" data-toggle="tab">@trans('Tipologia Negócio')</a></li>
            <li><a href="#tab-business-history" data-toggle="tab">@trans('Histórico de Negócio')</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-business-tipology" data-empty="1">
                {{ Form::model($customer, ['route'=> ['admin.customers.update', $customer->id, 'save' => 'business'], 'method' => 'PUT']) }}
                @include('admin.prospects.partials.tipology_form')
                {{ Form::close() }}
                <div class="clearfix"></div>
            </div>
            <div class="tab-pane" id="tab-business-history" data-empty="1">
                <table id="datatable-business-history" class="table table-condensed table-striped table-dashed table-hover" style="width: 100%">
                    <thead>
                    <tr>
                        <th class="w-100px">@trans('Data')</th>
                        <th class="w-1">@trans('Acção')</th>
                        <th>@trans('Descrição')</th>
                        <th>@trans('Alterado Por')</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>