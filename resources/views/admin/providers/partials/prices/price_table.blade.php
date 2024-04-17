<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default panel-prices-tables">
        <a class="panel-heading trigger-price-table-load" data-toggle="collapse" data-parent="#accordion" data-unity="{{ $unity }}" data-url="{!! route('admin.providers.price-table', [@$provider->id, $groupId] + Request::all()) !!}" href="#accordion-{{ $unity }}" role="button"
            aria-expanded="true" aria-controls="collapseOne">
            <h4 class="panel-title">
                <i class="fas fa-spin fa-spinner m-r-5" id="spinner-{{ $unity }}" style="display: none"></i>
                <i class="fas {{ $groupIcon }}"></i>
                {{ $groupName }}
                <small>
                    @foreach ($pricesTableData[$unity] as $service)
                        &bull; {{ $service->name }}
                    @endforeach
                </small>
                <i class="fas fa-caret-down pull-right"></i>
            </h4>
        </a>
        <div class="panel-collapse collapse {{ @$collapsed }}" id="accordion-{{ $unity }}" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body p-0">
                <div id="price-table-{{ $unity }}"></div>
            </div>
        </div>
    </div>
</div>
