<div class="box no-border m-b-15">
    <div class="box-body p-5">
        <div class="row">
            <div class="col-sm-7">
                @if($customer->filepath)
                    <img src="{{ asset($customer->getThumb(200)) }}" class="pull-left h-50px"/>
                @else
                    <img src="{{ asset('/assets/img/default/avatar.png') }}" class="pull-left h-50px"/>
                @endif
                <div class="pull-left m-l-10">
                    <h3 class="m-t-7 m-b-0 fs-18">
                        @if($customer->code)
                            {{ $customer->code }} -
                        @endif
                        {{ str_limit($customer->name, 50) }}

                        @if($customer->billing_closed)
                            <small class="label label-success fs-12 p-1" style="position: relative;top: -2px;left: 15px;padding: 3px 6px;">Faturação Fechada</small>
                        @else
                            <small class="label label-danger fs-12 p-1" style="position: relative;top: -2px;left: 15px;padding: 3px 6px;">Faturação Em aberto</small>
                        @endif
                    </h3>
                    <h4 class="m-t-3 m-b-0 text-muted fs-15">
                        {{ \App\Models\Billing::getPeriodName($year, $month, $period) }}
                        &bull; <a href="{{ route('admin.customers.edit', $customer->id) }}" target="_blank"><small>Ir para ficha cliente <i class="fas fa-external-link-alt"></i></small></a>
                    </h4>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="m-r-10 m-t-10 pull-right">
                    <div class="btn-group btn-group-sm pull-right m-l-5">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Imprimir/Exportar <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.printer.billing.customers.shipments.summary', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period, 't' => time()]) }}" target="_blank">
                                    <i class="fas fa-fw fa-print"></i> Imprimir resumo total
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.export.billing.customers.shipments.mass', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period]) }}" target="_blank">
                                    <i class="fas fa-fw fa-file-excel"></i> Exportar resumo total
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.export.shipments.dimensions', ['customer'=>$customer->id, 'month' => $month, 'year' => $year, 'billing_period' => $period]) }}" 
                                    data-export-url="export"
                                    target="_blank">
                                    <i class="fas fa-fw fa-file-excel"></i> Listagem mercadoria
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.billing.customers.email.edit', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period]) }}"
                                    data-toggle="modal"
                                    data-target="#modal-remote">
                                    <i class="fas fa-fw fa-envelope"></i> Enviar resumo por e-mail
                                </a>
                            </li>
                        </ul>
                    </div>

                    @if(!$customer->billing_closed)
                        <a href="{{ route('admin.billing.customers.edit', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period]) }}"
                           class="btn btn-sm btn-success pull-right"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
                            @if(hasModule('invoices'))
                                <i class="fas fa-check"></i> Faturar Tudo
                            @else
                                <i class="fas fa-check"></i> Confirmar Tudo
                            @endif
                        </a>
                    @endif

                    @if(@$billedItems['invoices'])
                        @if(count($billedItems['invoices']) == 1)
                            @foreach($billedItems['invoicesDetails'] as $invoiceId => $invoice)
                                @if($invoiceId)
                                    <div class="btn-group">
                                        <button type="button"
                                                class="btn btn-sm btn-primary pull-right m-r-5 dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                            <i class="fas fa-file-invoice"></i>
                                            @if(in_array($invoiceId, $billedItems['nodoc_ids']))
                                                {{ $invoice['name'] }}
                                            @elseif(in_array($invoiceId, @$billedItems['draft_ids']))
                                                Rascunho {{ $invoice['name'] }}
                                            @else
                                                Fatura {{ $invoice['name'] }}
                                            @endif
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach($billedItems['invoices'] as $invoiceId => $invoiceName)
                                                @if(!in_array($invoiceId, $billedItems['nodoc_ids']) && !in_array($invoiceId, @$billedItems['draft_ids']))
                                                <li>
                                                    <a href="{{ route('admin.invoices.download', [$customer->customer_id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}" target="_blank">
                                                        <i class="fas fa-fw fa-print"></i> Imprimir fatura
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <a href="{{ route('admin.invoices.summary', [$customer->id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}" target="_blank">
                                                        <i class="fas fa-fw fa-print"></i> Imprimir resumo
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.invoices.email.edit', [$customer->id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}"
                                                       data-toggle="modal"
                                                       data-target="#modal-remote">
                                                        <i class="fas fa-fw fa-envelope"></i> Enviar por e-mail
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                @if(in_array($invoiceId, @$billedItems['draft_ids']))
                                                    <li>
                                                        <a href="{{ route('admin.invoices.convert', @$invoice['invoice_id']) }}"
                                                           data-method="post"
                                                           data-confirm="Confirma a conversão do rascunho criado em fatura?"
                                                           data-confirm-title="Confirmar conversão de rascunho."
                                                           data-confirm-label="Converter"
                                                           data-confirm-class="btn-success">
                                                            <i class="fas fa-fw fa-exchange-alt"></i> Converter em Fatura
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(@$billedItems['invoicesDetails'][$invoiceId]['doc_type'] == 'proforma-invoice')
                                                    <li>
                                                        <a href="{{ route('admin.invoices.replicate', @$invoice['invoice_id']) }}"
                                                           data-method="post"
                                                           data-confirm="Confirma a conversão da fatura-proforma em fatura?"
                                                           data-confirm-title="Confirmar conversão de fatura-proforma."
                                                           data-confirm-label="Converter"
                                                           data-confirm-class="btn-success"
                                                            class="text-purple">
                                                            <i class="fas fa-fw fa-exchange-alt"></i> Converter em Fatura
                                                        </a>
                                                    </li>
                                                    <li content="divider"></li>
                                                @endif
                                                <li>
                                                    <a href="{{ route('admin.invoices.destroy.edit', [$customer->id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}"
                                                       data-toggle="modal"
                                                       data-target="#modal-remote"
                                                       class="text-red">
                                                        <i class="fas fa-fw fa-trash-alt"></i>
                                                        @if(in_array($invoiceId, $billedItems['nodoc_ids']))
                                                            Anular como faturado
                                                        @elseif(in_array($invoiceId, @$billedItems['draft_ids']))
                                                            Anular Rascunho
                                                        @else
                                                            Anular fatura
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-sm btn-primary pull-right m-r-5 dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                    <i class="fas fa-copy"></i> {{ count($billedItems['invoices']) }} Documentos <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($billedItems['invoicesDetails'] as $invoiceId => $invoice)
                                        <li class="dropdown-submenu pull-left">
                                            <a tabindex="-1" href="#" class="text-blue">
                                                @if(in_array($invoiceId, $billedItems['nodoc_ids']))
                                                    {{ $invoice['name'] }}
                                                @elseif(in_array($invoiceId, @$billedItems['draft_ids']))
                                                    Rascunho {{ $invoice['name'] }}
                                                @else
                                                    Fatura {{ $invoice['name'] }}
                                                @endif
                                            </a>
                                            <ul class="dropdown-menu">
                                                @if(!in_array($invoiceId, $billedItems['nodoc_ids']) && !in_array($invoiceId, @$billedItems['draft_ids']))
                                                <li>
                                                    <a href="{{ route('admin.invoices.download', [$customer->customer_id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}" target="_blank">
                                                        <i class="fas fa-fw fa-print"></i> Imprimir fatura
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <a href="{{ route('admin.invoices.summary', [$customer->id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}" target="_blank">
                                                        <i class="fas fa-fw fa-print"></i> Imprimir resumo
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.invoices.email.edit', [$customer->id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}"
                                                       data-toggle="modal"
                                                       data-target="#modal-remote">
                                                        <i class="fas fa-fw fa-envelope"></i> Enviar por e-mail
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                @if(in_array($invoiceId, @$billedItems['draft_ids']))
                                                    <li>
                                                        <a href="{{ route('admin.invoices.convert', @$invoice['invoice_id']) }}"
                                                           data-method="post"
                                                           data-confirm="Confirma a conversão do rascunho criado em fatura?"
                                                           data-confirm-title="Confirmar conversão de rascunho."
                                                           data-confirm-label="Converter"
                                                           data-confirm-class="btn-success">
                                                            <i class="fas fa-fw fa-exchange-alt"></i> Converter em Fatura
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="{{ route('admin.invoices.destroy.edit', [$customer->id, $invoiceId, 'id' => @$invoice['invoice_id'], 'type' => @$invoice['doc_type'], 'key' => $invoice['key']]) }}"
                                                       data-toggle="modal"
                                                       data-target="#modal-remote"
                                                       class="text-red">
                                                        <i class="fas fa-fw fa-trash-alt"></i>
                                                        @if(in_array($invoiceId, $billedItems['nodoc_ids']))
                                                            Anular como faturado
                                                        @elseif(in_array($invoiceId, @$billedItems['draft_ids']))
                                                            Anular Rascunho
                                                        @else
                                                            Anular fatura
                                                        @endif
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>