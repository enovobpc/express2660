<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Aprovar ou rejeitar novos clientes')</h4>
</div>
<div class="modal-body">
    <table class="table table-condensed m-b-0">
        <thead>
            <tr>
                <th class="w-1 bg-gray-light">@trans('Código')</th>
                <th class="bg-gray-light">@trans('Nome')</th>
                <th class="bg-gray-light">@trans('Morada')</th>
                <th class="bg-gray-light w-80px">@trans('Registo')</th>
                <th class="w-85px bg-gray-light"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($customers as $customer)
            <tr>
                <td class="w-1">
                    {{ $customer->code }}
                </td>
                <td>
                    {{ $customer->name }}
                    <br/>
                    <small class="text-muted">{{ $customer->email }}</small>
                </td>
                <td>
                    @if($customer->address)
                    {{ $customer->address }}<br/>
                    {{ $customer->zip_code }} {{ $customer->city }}<br/>
                    @endif
                    {{ trans('country.'. $customer->country) }}
                </td>
                <td>{{ $customer->created_at }}</td>
                <td class="text-center">
                    <a href="{{ route('admin.customers.validate.store', [$customer->id, 'validated'=>'1']) }}"
                       class="btn btn-xs btn-success btn-validate btn-approve">
                        <i class="fas fa-fw fa-check"></i>
                    </a>
                    <a href="{{ route('admin.customers.validate.store', [$customer->id, 'validated'=>'0']) }}"
                       class="btn btn-xs btn-danger btn-validate btn-decline">
                        <i class="fas fa-fw fa-times"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    </div>
</div>

<script>

    $('.btn-validate').on('click', function(e){
        e.preventDefault();

        var $row      = $(this).closest('tr');
        var $target   = $(this);
        var savedHtml = $(this).html();
        var action    = $(this).attr('href');

        $target.html('<i class="fas fa-fw fa-spin fa-circle-notch"></i>');

        $.ajax({
            url: action,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function(data){
                if (data.result) {
                    Growl.success(data.feedback)

                    if(data.validated) {
                        $row.find('.btn-validate:last-child').before('<div class="text-green m-t-5"><i class="fas fa-check"></i> Aprovado</div>')
                    } else {
                        $row.find('.btn-validate:last-child').before('<div class="text-red m-t-5"><i class="fas fa-times"></i> Rejeitado</div>')
                    }
                    $row.find('.btn-validate').hide()
                } else {
                    Growl.error(data.feedback)
                }
            }
        }).fail(function () {
            Growl.error500()
        }).always(function () {
            $target.html(savedHtml)
        });

    })
</script>