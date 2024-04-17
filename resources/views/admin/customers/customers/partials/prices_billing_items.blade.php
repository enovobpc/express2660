@if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
{{ Form::model($customer, ['route' => ['admin.customers.update', $customer->id], 'method' => 'PUT']) }}
<div class="row row-0">
    <div class="col-sm-9">
        <button type="submit" class="btn btn-sm btn-primary m-b-10" data-loading-text="A gravar...">
            <i class="fas fa-save"></i> @trans('Gravar Artigos Faturação')
        </button>
    </div>

    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fas fa-search"></i>
            </div>
            <input class="form-control input-sm" name="billing-items-search" type="search">
        </div>
    </div>
</div>
@endif

<div class="row row-5">
    <div class="col-xs-12">
        @if($allBillingItems)
            <table class="table table-condensed" id="table-billing-items">
                <tr>
                    <th class="bg-gray w-100px">@trans('Referência')</th>
                    <th class="bg-gray">@trans('Artigo')</th>
                    <th class="bg-gray">@trans('Fornecedor')</th>
                    <th class="bg-gray w-100px">@trans('Unidade')</th>
                    <th class="bg-gray w-100px">@trans('Preço Venda')</th>
                </tr>

                @php $first = true; @endphp
                @foreach($allBillingItems as $item)
                    <tr class="{{ !$first ? 'brd-black' : '' }}" data-search="{{ $item->reference }}|{{ $item->name }}">
                        <td>{{ $item->reference }}</td>
                        <td>@include('admin.billing.items.datatables.name', ['row' => $item, 'withoutLink' => true])</td>
                        <td>@include('admin.billing.items.datatables.provider', ['row' => $item])</td>
                        <td>{{ trans('admin/billing.items-unities.' . $item->unity) }}</td>
                        <td>
                            <div class="input-group input-group-xs input-group-money">
                                {{ Form::text('custom_billing_items['. $item->id .']', @$customer->custom_billing_items[$item->id], ['class' => 'form-control decimal input-sm text-right', 'placeholder' => $item->sell_price ?? '0.00']) }}
                                <div class="input-group-addon">
                                    {{ Setting::get('app_currency') }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    @php $first = false; @endphp
                @endforeach
            </table>
        @endif
    </div>
</div>

@if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
    <button type="submit" class="btn btn-sm btn-primary" data-loading-text="A gravar...">
        <i class="fas fa-save"></i> @trans('Gravar Artigos Faturação')
    </button>
    {{ Form::close() }}
@endif

<script>
    window.addEventListener('load', function () {
        $('input[name="billing-items-search"]').on('keyup', function () {
            var $this = $(this);
            var value = $this.val().trim().toLowerCase();

            $('#table-billing-items > tbody > tr[data-search]').show();
            if (!value) {
                return;
            }

            var $trs = $('#table-billing-items > tbody > tr[data-search]');
            $trs.each(function() {
                if (!$(this).data('search').toLowerCase().includes(value)) {
                    $(this).hide();
                }
            });
        });
    });
</script>