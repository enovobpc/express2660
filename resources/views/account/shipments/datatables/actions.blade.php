<?php
$servicesCanDelete = Setting::get('services_can_delete');
$servicesCanDelete = empty($servicesCanDelete) ? [] : $servicesCanDelete
?>
<div class="text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ trans('account/global.word.options') }} <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.shipments.show', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl"
                   class="text-blue">
                    <i class="fas fa-fw fa-list"></i> {{ trans('account/global.word.details') }}
                </a>
            </li>

            {{--<li>
                <a href="{{ route('tracking.index', ['tracking' => $row->tracking_code]) }}" class="text-blue" target="_blank">
                    <i class="fas fa-fw fa-search-location"></i> {{ trans('account/global.word.follow') }}
                </a>
            </li>--}}

            <li>
                <a href="{{ route('account.shipments.show', [$row->id, 'tab' => 'status']) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl"
                   class="text-blue">
                    <i class="fas fa-fw fa-search-location"></i> {{ trans('account/global.word.track-trace') }}
                </a>
            </li>
            <li>
                <a href="{{ route('account.shipments.email.edit', [$row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote"
                   class="text-blue">
                    <i class="fas fa-fw fa-envelope"></i> {{ trans('account/global.word.notify-email') }}
                </a>
            </li>

            @if($row->status_id == \App\Models\ShippingStatus::PAYMENT_PENDING_ID)
                <li class="divider"></li>
                <li>
                    <?php
                    $total = $row->billing_total;
                    ?>
                    {{-- <a href="{{ route('account.shipments.payment.store', $row->id) }}"
                       data-method="post"
                       data-confirm="Confirma o pagamento do envio #{{ $row->tracking_code }}?<br/><small>Vão ser debitados {{ money($total, Setting::get('app_currency')) }} do saldo da sua conta.</small>"
                       data-confirm-title="Efetuar pagamento"
                       data-confirm-label="Efetuar Pagamento"
                       data-confirm-class="btn-success"

                       class="text-green">
                        <i class="fas fa-fw fa-euro-sign"></i> Efetuar Pagamento
                    </a> --}}
                    <a class="pay-shipment"
                        data-trkid={{ $row->id }}
                        data-subtotal={{ $row->billing_subtotal }}
                        data-vat={{ $row->billing_vat }}
                        data-total={{ $row->billing_total }} 
                        style="cursor: pointer; color: green;">
                        <i class="fas fa-fw fa-euro-sign"></i> Efetuar Pagamento
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.shipments.destroy', $row->id) }}"
                       data-method="delete"
                       data-confirm="{{ trans('account/shipments.feedback.destroy.question') }}"
                       class="text-red">
                        <i class="fas fa-fw fa-trash-alt"></i> {{ trans('account/global.word.destroy') }}
                    </a>
                </li>
            @endif

            <li role="separator" class="divider"></li>
            <li>
                <a href="{{ route('account.shipments.get.guide', $row->id) }}"
                   target="_blank"
                   class="text-purple">
                    <i class="fas fa-fw fa-print"></i> {{ trans('account/shipments.print.guide') }}
                </a>
            </li>
            @if(@$row->customer->default_print == 'cmr' || Setting::get('shipment_print_default') == 'cmr' || Setting::get('customers_show_cmr') || in_array(Setting::get('app_mode'), ['cargo', 'freight']))
                <li>
                    <a href="{{ route('account.shipments.get.cmr', $row->id) }}" class="text-purple" target="_blank">
                        <i class="fas fa-fw fa-print"></i> {{ trans('account/shipments.print.cmr') }}
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('account.shipments.get.labels', $row->id) }}"
                   target="_blank"
                   class="text-purple">
                    <i class="fas fa-fw fa-print"></i> {{ trans('account/shipments.print.label') }}
                </a>
            </li>

            @if(Setting::get('shipment_label_a4'))
                <li>
                    <a href="{{ route('account.shipments.get.labelsA4.edit', ['id[]' => $row->id]) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xs"
                       class="text-purple">
                        <i class="fas fa-fw fa-print"></i> {{ trans('account/shipments.print.label') }} A4
                    </a>
                </li>
            @endif

            @if($row->charge_price && $row->webservice_method == 'ctt')
                <li>
                    <a href="{{ route('account.shipments.get.reimbursement-guide', $row->id) }}"
                       target="_blank"
                       class="text-purple">
                        <i class="fas fa-fw fa-print"></i> {{ trans('account/shipments.print.reimbursement-guide') }}
                    </a>
                </li>
            @endif

            @if($row->customer->country == 'pt')
            <li>
                <a href="{{ route('account.shipments.get.value-statement', $row->id) }}"
                   target="_blank"
                   class="text-purple">
                    <i class="fas fa-fw fa-print"></i> Declaração Valores
                </a>
            </li>
            @endif

            <li class="divider"></li>
            @if(config('app.source') !== "aveirofast")
                <li>
                    <a href="{{ route('account.shipments.replicate', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote"
                       class="text-blue">
                        <i class="fas fa-fw fa-clone"></i> {{ trans('account/global.word.replicate') }}
                    </a>
                </li>
            @endif
            @if((((Setting::get('customers_allow_edit_after_webservice') || Setting::get('customers_allow_delete_after_webservice')) && !$row->ignore_billing && in_array($row->status_id, $servicesCanDelete))
            || (empty($row->submited_at) && in_array($row->status_id, $servicesCanDelete) && !$row->ignore_billing))
            && !($auth->customer_id == 1443  && config('app.source') == 'corridadotempo'))
                @if(Setting::get('customers_allow_edit_after_webservice') || empty($row->submited_at))
                    <li>
                        <a href="{{ route('account.shipments.edit', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl"
                           class="text-green">
                            <i class="fas fa-fw fa-pencil-alt"></i> {{ trans('account/global.word.edit') }}
                        </a>
                    </li>
                @endif
                @if(empty($row->submited_at) || Setting::get('customers_allow_delete_after_webservice'))
                    <li>
                        <a href="{{ route('account.shipments.destroy', $row->id) }}"
                           data-method="delete"
                           data-confirm="{{ trans('account/shipments.feedback.destroy.question') }}"
                           class="text-red">
                            <i class="fas fa-fw fa-trash-alt"></i> {{ trans('account/global.word.destroy') }}
                        </a>
                    </li>
                @endif
            @endif

            @if(hasModule('customer_support'))
            <li>
                <a href="{{ route('account.customer-support.create', ['shipment' => $row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg"
                   class="text-purple">
                    <i class="fas fa-fw fa-headset"></i> {{ trans('account/global.word.request-support') }}
                </a>
            </li>
            @endif
        </ul>
    </div>
    {{-- @include('account.shipments.modals.payment'); --}}
</div>