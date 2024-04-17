@section('title')
    {{ trans('account/global.settings.title') }} -
@stop

@section('account-content')
    @if(!@$auth->is_validated && @$auth->is_active)
        <div class="notice notice-warning text-orange" style="margin: -10px">
            <h4 class="m-t-0 m-b-5">
                <i class="fas fa-clock"></i> <b>A sua conta está a aguardar aprovação.</b><br/>
                <small>
                    Está quase tudo pronto para enviar as suas encomendas através do nosso serviço!
                    <br/>
                    A sua conta encontra-se em processo de aprovação. Enviaremos um e-mail quando a conta for ativa.
                </small>
            </h4>
        </div>
        <div class="spacer-15"></div>
    @endif
<div class="tabbable-line">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                {{ trans('account/global.settings.tabs.info') }}
            </a>
        </li>
        @if($auth->show_billing)
        <li>
            <a href="#tab-billing" data-toggle="tab">
                {{ trans('account/global.settings.tabs.billing') }}
            </a>
        </li>
        @endif
        <li>
            <a href="#tab-login" data-toggle="tab">
                {{ trans('account/global.settings.tabs.login') }}
            </a>
        </li>
        <li>
            <a href="#tab-contacts" data-toggle="tab">
                {{ trans('account/global.settings.tabs.contacts') }}
            </a>
        </li>
    </ul>
    <div class="tab-content" style="padding-bottom: 0">
        <div class="tab-pane active" id="tab-info">
            @include('account.details.partials.info')
        </div>
        @if($auth->show_billing)
        <div class="tab-pane" id="tab-billing">
            @include('account.details.partials.billing')
        </div>
        @endif
        <div class="tab-pane" id="tab-login">
            @include('account.details.partials.login')
        </div>
        <div class="tab-pane" id="tab-contacts">
            @include('account.details.partials.contacts')
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
    var oTable;

    $('[data-dismiss="fileinput"]').on('click', function(){
        $('[name=delete_photo]').val(1);
    })

    $(document).ready(function () {
        oTable = $('#datatable-contacts').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'mobile', name: 'mobile', visible: false},
                {data: 'department', name: 'department', visible: false},
            ],
            order: [[1, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.contacts.datatable') }}",
                data: function (d) {
                },
                beforeSend: function () {Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });
    });

    $('.filter-datatable').on('change', function (e) {
        oTable.draw();
        e.preventDefault();
    });
</script>
@stop