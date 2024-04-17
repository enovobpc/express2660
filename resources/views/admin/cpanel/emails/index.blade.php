@section('title')
    Espaço e Contas E-mail
@stop

@section('content-header')
    Espaço e Contas E-mail
@stop

@section('breadcrumb')
    <li class="active">@trans('Servidor')</li>
    <li class="active">@trans('Espaço e Contas E-mail')</li>
@stop

@section('content')
<div class="row">
    <div class="col-sm-12">
        @if(@$quota['usage'] > @$quota['quota'])
        <div class="alert alert-danger">
            <h4 class="m-0" style="font-weight: bold"><i class="fas fa-exclamation-triangle"></i> @trans('O espaço usado é superior ao espaço contratado.')</h4>
            <p>@trans('Atualmente está a consumir mais') <b>{{ human_filesize(@$quota['usage'] - @$quota['quota']) }}</b> @trans('do que o espaço contratado. Poderão ser cobrado custos extra por espaço adicional. Consulte o seu comercial para saber mais.')</p>
        </div>
        @endif
    </div>
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <div class="col-sm-2" style="width: 145px">
                    <h4 class="m-0">
                        <small style="margin: 1px 0 -12px;display: block;">@trans('Espaço Contratado')</small><br/>
                        <i class="fas fa-server"></i> <b>{{ human_filesize(@$quota['quota']) }}</b>
                    </h4>
                </div>
                <div class="col-sm-2" style="width: 130px">
                    <h4 class="m-0 {{ @$quota['usage'] > @$quota['quota'] ? 'text-red' : '' }}">
                        <small style="margin: 1px 0 -12px;display: block;">@trans('Espaço Usado')</small><br/>
                        @if(@$quota['usage'] > @$quota['quota'])
                        <i class="fas fa-exclamation-triangle"></i>
                        @endif
                        <b>{{ human_filesize(@$quota['usage']) }}</b>
                    </h4>
                </div>
                <div class="col-sm-3" style="margin-left: -28px">
                    <div style="width: 250px; margin-top: 3px; {{ @$quota['usage'] > @$quota['quota'] ? 'color: red' : 'color: #000'  }}">
                        <div class="m-b-2 pull-right text-right">
                            {{ human_filesize(@$quota['usage']) }} / <b>{{ human_filesize(@$quota['quota']) }}</b>
                        </div>
                        <div class="m-b-2 pull-left">
                            <span class="progress-loading" style="display: none"><i class="fas fa-spin fa-circle-notch"></i> </span><small>{{ money(@$quota['percent']) }}%</small>
                        </div>
                        <div class="clearfix"></div>
                        <table class="quota-progress" cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="quota-progress-left" style="width: {{ @$quota['percent'] }}%; height: 12px; background: {{ @$quota['color'] }}"></td>
                                    <td class="quota-progress-right" style="width: {{ 100 - @$quota['percent'] }}%;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-2">
                    <h4 class="m-0">
                        <small style="margin: 1px 0 -15px;display: block;">@trans('Contas E-mail')</small><br/>
                        @if($emails['quota'] && $emails['quota'] - $emails['usage'] <= 2)
                        <span class="text-red">
                            <i class="fa fa-exclamation-triangle"></i>
                            {{ $emails['usage'] }}/{{ $emails['quota'] ? $emails['quota'] : __('Ilimitado') }}
                        </span>
                        @else
                            {{ $emails['usage'] }}/{{ $emails['quota'] ? $emails['quota'] : __('Ilimitado') }}
                        @endif
                    </h4>
                </div>
            </div>
        </div>
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.cpanel.emails.create') }}" class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cpanel.emails.configs') }}" class="btn btn-default btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-cog"></i> @trans('Ver Configurações Outlook')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cpanel.emails.configs', 'list') }}" class="btn btn-default btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-list-ul"></i> @trans('Lista E-mails')
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())

                        <li>
                            <a href="{{ route('admin.cpanel.emails.configs', 'install') }}" class="btn btn-default btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Instalar')
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>@trans('Conta E-mail')</th>
                                <th class="w-1">@trans('Espaço')</th>
                                <th class="w-1">@trans('Ativo')</th>
                                <th class="w-1">@trans('Envio')</th>
                                <th class="w-1">@trans('Receção')</th>
                                <th class="w-1">@trans('Reenc.')</th>
                                <th class="w-60px">@trans('R.Auto')</th>
                                <th class="w-65px">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                   {{-- {{ Form::open(array('route' => 'admin.vehicles.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}--}}
                </div>
            </div>
        </div>
    </div>
</div>
    <style>
        .quota-progress {
            width: 100%;
            border-collapse: separate;
            border-radius: 30px;
            border: 1px solid #999;
        }

        .quota-progress-left {
            border-radius: 30px;
            height: 6px;
            background: #ffd400;
            /*border: 1px solid #999;
            border-right: 0 !important;*/
        }

        .quota-progress-right {
            background: #eee;
            border-radius: 0 30px 30px 0;
            /*border: 1px solid #999;
            border-left: 0 !important;*/
        }
    </style>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable
    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'email', name: 'email'},
                {data: 'quota', name: 'quota'},
                {data: 'login_suspended', name: 'login_suspended', class:'vertical-align-middle'},
                {data: 'outgoing_suspended', name: 'outgoing_suspended', class:'vertical-align-middle'},
                {data: 'incoming_suspended', name: 'incoming_suspended', class:'vertical-align-middle'},
                {data: 'forwarding_active', name: 'forwarding_active', class:'vertical-align-middle'},
                {data: 'autoresponder_active', name: 'autoresponder_active', class:'vertical-align-middle'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.cpanel.emails.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop