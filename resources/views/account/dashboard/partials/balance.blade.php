@if(Setting::get('invoice_software') != 'EnovoTms')
    <button class="btn btn-sm btn-success btn-refresh-account pull-right m-l-5" data-toggle="modal" data-target="#modal-sync-balance">
        <i class="fas fa-sync-alt"></i>
    </button>
    <a href="{{ route('account.billing.index') }}" class="btn btn-sm btn-default pull-right">{{ trans('account/global.word.see-balance')  }}</a>
@endif
<ul class="list-inline">
    <li>
        <h4 style="margin-top: -5px">
            <small>{{ trans('account/billing.word.unpaid') }}</small><br/>
            <b class="balance-total-unpaid {{ $customer->balance_total ? 'text-red' : 'text-green' }}">{{ money($customer->balance_total, Setting::get('app_currency')) }}</b>
        </h4>
    </li>
    <li>
        <h4 class="m-l-15" style="margin-top: -5px">
            <small>{{ trans('account/billing.word.expired-docs') }}</small><br/>
            <b class="balance-total-expired {{ $customer->balance_expired_count ? 'text-red' : 'text-green' }}">{{ $customer->balance_expired_count }} {{trans('account/billing.word.docs') }}</b>
        </h4>
    </li>
    @if(@$customer->paymentCondition->name)
        <li>
            <h4 class="m-l-15" style="margin-top: -5px">
                <small>{{ trans('account/global.word.payment') }}</small><br/>
                <b>{{ @$customer->paymentCondition->name }}</b>
            </h4>
        </li>
    @endif
</ul>


<div class="table-responsive">
    <table id="datatable" class="table table-condensed table-hover">
        <tr style="background: #f2f2f2">
            <th class="w-95px">{{ trans('account/global.word.date') }}</th>
            <th>{{ trans('account/global.word.document') }}</th>
            <th class="w-90px">{{ trans('account/global.word.total') }}</th>
            <th class="w-150px">{{ trans('account/global.word.due_date') }}</th>
            <th class="w-90px">{{ trans('account/global.word.status') }}</th>
        </tr>
        @forelse($invoices as $row)
        <tr>
            <td>{{ $row->doc_date }}</td>
            <td>{!! view('account.billing.datatables.invoices.serie', compact('row'))->render() !!}</td>
            <td>{!! view('account.billing.datatables.invoices.doc_total', compact('row'))->render() !!}</td>
            <td>{!! view('account.billing.datatables.invoices.due_date', compact('row', 'today'))->render() !!}</td>
            <td class="text-center">{!! view('account.billing.datatables.invoices.is_settle', compact('row'))->render() !!}</td>
        </tr>
        @empty
            <tr>
                <td colspan="5" class="text-muted text-center">
                    <div class="spacer-60"></div>
                    <i class="fas fa-info-circle"></i> {{ trans('account/billing.word.empty-billing') }}
                    <div class="spacer-60"></div>
                </td>
            </tr>
        @endforelse
    </table>
</div>

@if(Setting::get('invoice_software') != 'EnovoTms')
@include('account.billing.modals.sync_balance')
@endif