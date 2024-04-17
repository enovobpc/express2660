<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    {{--@if(Auth::user()->isGuest())
        <li>
            <button class="btn btn-primary btn-sm" disabled>
                <i class="fas fa-folder-open"></i> SAF-T
            </button>
        </li>
    @else
    <li>
       <a href="{{ route('admin.invoices.saft') }}"
           class="btn btn-primary btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-folder-open"></i> SAF-T <i class="fas fa-angle-down"></i>
        </a>
    </li>
    @endif--}}
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bolt"></i> Faturação Massiva <i class="fas fa-angle-down"></i>
            </button>
            <ul class="dropdown-menu">
                @if(config('app.source') != 'rlrexpress')
                    <li>
                        <a href="{{ route('admin.billing.customers.mass.billing.edit', ['month' => Request::get('month'), 'year' => Request::get('year'), 'period' => Request::get('period')]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                            class="btn-mass-billing">
                            <i class="fas fa-fw fa-file-invoice"></i> Emitir todas as faturas
                        </a>
                    </li>

                    <li class="divider"></li>
                    @if(hasModule('invoices-advanced'))
                        <li>
                            <a href="{{ route('admin.billing.customers.mass.billing.edit', ['month' => Request::get('month'), 'year' => Request::get('year'), 'period' => Request::get('period')]) }}"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <img src="{{ asset('/assets/img/default/mb-icon.svg') }}" style="width: 13px; margin-right: 2px;"> Emitir tudo como fatura-proforma
                            </a>
                        </li>
                    @else
                    <li data-toggle="tooltip" title="Fecha o mês e emite para cada cliente uma Fatura-Proforma com Referência Multibanco. Quando o cliente efetuar o pagamento, é emitido automáticamente a respetiva fatura-recibo.">
                        <a href="#" data-toggle="modal" data-target="#modal-mass-proforma">
                            <img src="{{ asset('/assets/img/default/mb-icon.svg') }}" style="width: 13px; margin-right: 2px;"> Emitir tudo como fatura-proforma
                        </a>
                    </li>
                    @endif
                @endif
            </ul>
        </div>
    </li>
    <li>
        @include('admin.billing.customers.partials.tools_button')
    </li>
    <li class="fltr-primary w-105px">
        <strong>Ano</strong><br class="visible-xs"/>
        <div class="w-70px pull-left form-group-sm">
            {{ Form::select('year', $years, $year, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-155px">
        <strong>Mês</strong><br class="visible-xs"/>
        <div class="w-115px pull-left form-group-sm" style="position: relative">
            {{ Form::select('month', $months, $month, array('class' => 'form-control input-sm filter-datatable select2')) }}
            <i class="fas fa-spin fa-circle-notch filter-loading" style="display: none; position: absolute; top: 8px; right: -18px;"></i>
        </div>
    </li>
    @if(!empty(Setting::get('billing_method')) && Setting::get('billing_method') != '30d')
        <li class="fltr-primary w-170px">
            <strong>Período</strong><br class="visible-xs"/>
            <div class="w-100px pull-left form-group-sm">
                {{ Form::select('period', trans('admin/billing.periods.' . Setting::get('billing_method')), Request::has('period') ? Request::get('period') : $curPeriod, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    @endif
</ul>