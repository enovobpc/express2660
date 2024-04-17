<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Destinatários da Mensagem')</h4>
</div>
<div class="modal-body">
    <div class="table-responsive">
        @if($showEmails)
            <?php $emails = (array) $customerMessage->to_emails;?>
            <p>{{ count($emails) }} @trans('E-mails enviados')</p>
            <div style="height: 314px;overflow-y: auto;border: 1px solid #ccc;">
                <table class="table table-condensed table-striped table-dashed table-hover">
                    <tr>
                        <th class="w-1">@trans('Cliente')</th>
                        <th class="w-1">@trans('E-mail')</th>
                    </tr>
                    @foreach($emails as $email => $customerName)
                        <tr>
                            <td>{{ $customerName }}</td>
                            <td>{{ $email }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
        <table id="datatable-readed" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
            <tr>
                <th class="w-1">@trans('Código')</th>
                <th>@trans('Cliente')</th>
                <th>@trans('E-mail')</th>
                <th class="w-1">@trans('Visto')</th>
                <th class="w-1">@trans('Apagado')</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        @endif
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#datatable-readed').DataTable({
            columns: [
                {data: 'customers.code', name: 'customers.code'},
                {data: 'customers.name', name: 'customers.name'},
                {data: 'customers.contact_email', name: 'customers.contact_email'},
                {data: 'is_read', name: 'is_read'},
                {data: 'deleted_at', name: 'deleted_at'},
            ],
            ajax: {
                url: "{{ route('admin.customers.messages.datatable.recipients', $customerMessage->id) }}",
                type: "POST"
            }
        });
    });

</script>