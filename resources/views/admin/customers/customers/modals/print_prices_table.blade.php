<div class="modal" id="modal-print-price-table">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.printer.customers.prices-table', $customer->id], 'method' => 'GET']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Imprimir proposta de preços')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-9">
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group m-b-0">
                                    {{ Form::label('title', __('Título do Documento')) }}
                                    {{ Form::text('title', __('Proposta Comercial'), ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-0">
                                    {{ Form::label('subtitle', __('Subtítulo do Documento')) }}
                                    {{ Form::text('subtitle', __('Serviços de Transporte'), ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            {{ Form::label('date', __('Data Documento')) }}
                            <div class="input-group">
                                {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <label class="m-t-5">@trans('Que páginas pretende incluir?')</label>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="checkbox m-b-0 m-t-3">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('cover', 1, true) }}
                                @trans('Capa')
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox m-b-0 m-t-3">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('presentation', 1, true) }}
                                @trans('Apresentação')
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="checkbox m-b-0 m-t-3">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('conditions', 1, true) }}
                                @trans('Condições Gerais')
                            </label>
                        </div>
                    </div>
                </div>
                <hr style="margin-bottom: 10px; margin-top: 10px"/>
                <label class="m-t-5">@trans('Que serviços incluir na impressão?')</label>
                <div class="row">
                    @foreach($servicesList as $id => $name)
                        <div class="col-sm-4">
                            <div class="checkbox m-b-0 m-t-3">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('service[]', $id) }}
                                    {{ $name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <hr style="margin: 15px 0 5px"/>
                <label class="m-t-5">@trans('Que serviços complementares incluir na impressão?')</label>
                <div class="row">
                    @foreach($complementarServices as $service)
                        <div class="col-sm-4">
                            <div class="checkbox m-b-0 m-t-3">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('complementar_service[]', $service->id) }}
                                    {{ $service->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    @if(Setting::get('insurance_tax'))
                    <div class="col-sm-4">
                        <div class="checkbox m-b-0 m-t-3">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('insurance_tax', 1, false) }}
                                @trans('Taxa seguro')
                            </label>
                        </div>
                    </div>
                    @endif
                    @if(Setting::get('fuel_tax'))
                    <div class="col-sm-4">
                        <div class="checkbox m-b-0 m-t-3">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('fuel_tax', 1, false) }}
                                @trans('Taxa combustivel')
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <div class="input-group pull-left m-r-20" style="width: 280px">
                        <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                            <i class="fas fa-envelope"></i>
                            {{ Form::checkbox('send_email', 1, false) }}
                        </div>
                        {{ Form::text('email', $customer->billing_email, ['class' => 'form-control pull-left', 'placeholder' => 'E-mail do cliente']) }}
                    </div>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> @trans('Imprimir')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>