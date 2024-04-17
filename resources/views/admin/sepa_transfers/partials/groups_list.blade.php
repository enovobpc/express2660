<table class="table table-condensed table-hover m-b-0">
    <tr>
        <th class="bg-gray-light">ID Pagam.</th>
        <th class="bg-gray-light">Processamento</th>
        @if($payment->type == 'dd')
            <th class="bg-gray-light">Serviço</th>
            <th class="bg-gray-light">Freq.</th>
            <th class="bg-gray-light">Ordenante</th>
        @else
            <th class="bg-gray-light">Categoria Transf.</th>
            <th class="bg-gray-light">Banco</th>
        @endif
        <th class="bg-gray-light">IBAN</th>
        <th class="bg-gray-light">
            @if($payment->type == 'dd')
                Ref. Ordenante
            @else
                NIF
            @endif
        </th>
        <th class="bg-gray-light w-1">Trans.</th>
        <th class="bg-gray-light text-right">Total</th>
        @if($payment->edit_mode)
            <th class="bg-gray-light w-1">Ações</th>
        @else
            <td class="text-center">Estado</td>
        @endif
    </tr>
    @foreach($groups as $key => $group)
        <tr class="{{ $key ? : 'active' }}" data-url="{{ route('admin.sepa-transfers.groups.show', [$group->payment_id, $group->id]) }}"  style="{{ $group->transactions_count ? : 'background: #ffcfcf; color: red' }}">
            <td>{{ $group->code }}</td>
            <td>{{ $group->processing_date->format('Y-m-d') }}</td>
            @if($payment->type == 'dd')
            <td>{{ $group->service_type }}</td>
            <td>{{ $group->sequence_type }}</td>
            <td>{{ $group->company }}</td>
            @else
            <td>{{ trans('admin/billing.sepa-category-types.'.$group->category) }}</td>
            <td>{{ $group->bank_name }}</td>
            @endif
            <td>{{ $group->bank_iban }}</td>
            <td>{{ $group->credor_code }}</td>
            <td>{{ $group->transactions_count }}</td>
            <td class="text-right bold">{{ money($group->transactions_total, '€') }}</td>
            @if($payment->edit_mode)
                <td>
                    <a href="{{ route('admin.sepa-transfers.groups.edit', [$group->payment_id, $group->id]) }}"
                       data-toggle="modal"
                       data-target="#modal-remote"
                       data-toggle=""
                       class="text-green">
                        <i class="fas fa-fw fa-pencil-alt"></i>
                    </a>
                    <a href="{{ route('admin.sepa-transfers.groups.destroy', [$group->payment_id, $group->id]) }}"
                       class="text-red remove-group-line">
                        <i class="fas fa-fw fa-trash-alt"></i>
                    </a>
                </td>
            @else
                <td class="text-center">
                    @if($group->status == \App\Models\SepaTransfer\PaymentGroup::STATUS_ACCEPTED)
                        <span class="label label-success">Aceite</span>
                    @elseif($group->status == \App\Models\SepaTransfer\PaymentGroup::STATUS_PENDING)
                        <span class="label label-warning">Pendente</span>
                    @elseif($group->status == \App\Models\SepaTransfer\PaymentGroup::STATUS_ACCEPTED_PARTIAL)
                        <span class="label bg-orange">Aceite Parcial</span>
                    @elseif($group->status == \App\Models\SepaTransfer\PaymentGroup::STATUS_REJECTED)
                        <span class="label label-danger"
                          data-toggle="tooltip"
                          title="{{ $group->error_code }} - {{ $group->error_msg }}">
                            Rejeitado
                        </span>
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
</table>