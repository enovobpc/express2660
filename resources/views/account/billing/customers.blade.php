<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true">
            <i class="fas fa-times"></i>
        </span>
        <span class="sr-only">{{ trans('account.word.close') }}</span>
    </button>
    <h4 class="modal-title">{{ trans('account/billing.word.customer-detail') }}</h4>
</div>
<div class="modal-body">
    <h4 class="m-t-0 m-b-20">{{ trans('datetime.month.' . $month) }} {{ $year }}</h4>
    <table class="table table-condensed table-hover m-b-0">
        <tr style="background: #f2f2f2; border-bottom: 1px solid #ddd;">
            <th class="bg-gray-light">{{ trans('account/global.word.recipient') }}</th>
            <th class="bg-gray-light w-80px text-center">{{ trans('account/global.word.pickups') }}</th>
            <th class="bg-gray-light w-80px text-center">{{ trans('account/global.word.shipments') }}</th>
            <th class="bg-gray-light w-80px">{{ trans('account/global.word.total') }}</th>
        </tr>
    </table>
    <div style="max-height: 350px; overflow: hidden; overflow-y: scroll;">
        <table class="table table-condensed table-hover m-0">
            @foreach($billing as $customer)
                <tr>
                    <td>{{ $customer->recipient }}</td>
                    <td class="w-80px text-center" style="{{ $customer->collections ? '' : 'color: #ddd' }}">{{ $customer->collections }}</td>
                    <td class="w-80px text-center">{{ $customer->shipments }}</td>
                    <td class="w-80px bold">{{ money($customer->total , Setting::get('app_currency')) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account.word.close') }}</button>
</div>