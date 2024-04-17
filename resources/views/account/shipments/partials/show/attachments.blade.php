@if(@$shipmentAttachments->isEmpty())
    <div class="text-center text-muted p-5 m-t-30">
        <h4 style="font-weight: normal"><i class="fas fa-info-circle"></i> {{ trans('account/shipments.modal-attachments.empty') }}</h4>
        @if(@$customer->settings['upload_shipment_attachments'])
        <a href="{{ route('account.shipments.attachments.create', $shipment->id) }}"
           class="btn btn-default btn-sm m-t-20 m-b-40"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> {{ trans('account/shipments.modal-attachments.title') }}
        </a>
        @endif
        <div class="sp-25"></div>
    </div>
@else
    <table id="datatable" class="table m-b-0">
        <tr>
            <th>{{ trans('account/global.word.document') }}</th>
            <th class="w-85px">{{ trans('account/global.word.size') }}</th>
            <th class="w-160px">{{ trans('account/global.word.uploaded_by') }}</th>
            <th class="w-155px">{{ trans('account/global.word.created_at') }}</th>
            @if(@$customer->settings['upload_shipment_attachments'])
            <th class="w-65px">{{ trans('account/global.word.actions') }}</th>
            @endif
        </tr>
        <?php $totalSize = 0; ?>
        @foreach($shipmentAttachments as $item)
            <tr>
                <td>
                    <a href="{{ asset($item->filepath) }}" target="_blank">
                        <?php $extension = File::extension($item->filename); ?>
                        {!! extensionIcon($extension) . ' ' . $item->name  !!}
                    </a>
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
                    @if($item->customer_id)
                        {{ @$item->created_customer->name }}
                    @else
                        {{ @$item->created_user->name }}
                    @endif
                </th>
                <td class="vertical-align-middle">{{ $item->created_at }}</th>
                @if(@$customer->settings['upload_shipment_attachments'])
                <td class="vertical-align-middle text-right">
                    @if($item->customer_id)
                    <div class="action-buttons">
                        <a href="{{ route('account.shipments.attachments.edit', [$item->source_id, $item->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-green m-r-5">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="{{ route('account.shipments.attachments.destroy', [$item->source_id, $item->id]) }}"
                           data-method="delete"
                           data-confirm="{{  trans('account/shipments.modal-attachments.feedback.destroy.message') }}"
                           class="text-red">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    @endif
                </td>
                @endif
            </tr>
        @endforeach
        <tr>
            <td class="text-right bold vertical-align-middle">{{ trans('account/global.word.total') }}</td>
            <td class="bold vertical-align-middle">{{ human_filesize($totalSize) }}</td>
            <td></td>
            <td></td>
            @if(@$customer->settings['upload_shipment_attachments'])
            <td></td>
            @endif
        </tr>
    </table>
    @if(@$customer->settings['upload_shipment_attachments'])
    <a href="{{ route('account.shipments.attachments.create', $item->source_id) }}"
       class="btn btn-xs btn-black"
       style="margin-top: -48px;"
       data-toggle="modal"
       data-target="#modal-remote">
        <i class="fas fa-plus"></i> {{ trans('account/shipments.modal-attachments.title') }}
    </a>
    @endif
@endif