@if(empty(@$invoiceAttachments))
    <div class="text-center text-muted p-5 m-t-40">
        <h4><i class="fas fa-info-circle"></i> Não Há anexos para este documento.</h4>
        <a href="{{ route('admin.invoices.purchase.attachments.create', $invoice->id) }}"
           class="btn btn-default btn-sm m-t-20 m-b-40"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> Adicionar Anexo
        </a>
    </div>
@else
    <table id="datatable" class="table m-b-0">
        <tr>
            <th class="bg-gray-light">Documento</th>
            <th class="bg-gray-light w-85px">Tamanho</th>
            <th class="bg-gray-light w-140px">Inserido em</th>
            <th class="bg-gray-light w-65px">Ações</th>
        </tr>
        <?php $totalSize = 0; ?>
        @foreach($invoiceAttachments as $item)
            <tr>
                <td>
                    <a href="{{ asset($item->filepath) }}" target="_blank">
                        @if($item->extension)
                            {!! extensionIcon($item->extension) . ' ' . $item->name  !!}
                        @else
                            <?php $extension = File::extension($item->filename); ?>
                            {!! extensionIcon($extension) . ' ' . $item->name  !!}
                        @endif
                    </a>
                    @if($item->obs)
                        <div><small>{{ $item->obs }}</small></div>
                    @endif
                </td>
                <td class="vertical-align-middle">
                <?php
                $size = 0;
                if(File::exists($item->filepath)) {
                    $size = File::size($item->filepath);
                    $totalSize+= $size;
                }
                ?>
                {{ human_filesize($size) }}
                </th>
                <td class="vertical-align-middle">
                    <div>
                        @if($item->customer_id)
                            {{ @$item->created_customer->name }}
                        @else
                            {{ @$item->created_user->name }}
                        @endif
                    </div>
                    <div><small class="text-muted italic">{{ $item->created_at }}</small></div>
                </th>
                <td class="vertical-align-middle text-right">
                    <div class="action-buttons">
                        <a href="{{ route('admin.invoices.purchase.attachments.edit', [$item->source_id, $item->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-green">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="{{ route('admin.invoices.purchase.attachments.destroy', [$item->source_id, $item->id]) }}"
                           data-method="delete"
                           data-confirm="Confirma a remoção do ficheiro selecionado?"
                           class="text-red">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="text-right bold vertical-align-middle">Total</td>
            <td class="bold vertical-align-middle">{{ human_filesize($totalSize) }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <a href="{{ route('admin.invoices.purchase.attachments.create', @$item->source_id) }}"
       class="btn btn-xs btn-success"
       style="margin-top: -48px;"
       data-toggle="modal"
       data-target="#modal-remote">
        <i class="fas fa-plus"></i> Adicionar Anexo
    </a>
@endif