<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title text-white">{{ trans('account/shipments.modal-show.title') }} {{ $shipment->tracking_code }}</h4>
</div>
<div class="modal-body p-t-0 p-b-0 modal-shipment">
    <div class="tabbable-line">
        <ul class="nav nav-tabs">
            <li class="{{ Request::get('tab') == 'status' ? '' : 'active' }}">
                <a href="#tab-info" data-toggle="tab">
                    {{ trans('account/shipments.modal-show.tabs.info') }}
                </a>
            </li>
            <li class="{{ Request::get('tab') == 'status' ? 'active' : '' }}">
                <a href="#tab-status" data-toggle="tab">
                    {{ trans('account/shipments.modal-show.tabs.track') }}
                </a>
            </li>
            @if(!$shipment->pack_dimensions->isEmpty())
            <li class="{{ Request::get('tab') == 'status' ? 'active' : '' }}">
                <a href="#tab-dimensions" data-toggle="tab">
                    {{ trans('account/shipments.modal-show.tabs.dimensions') }}
                </a>
            </li>
            @endif

            @if(@$customer->settings['show_shipment_attachments'] || Setting::get('show_shipment_attachments'))
            <li class="{{ Request::get('tab') == 'status' ? 'active' : '' }}">
                <a href="#tab-attachments" data-toggle="tab">
                    {{ trans('account/shipments.modal-show.tabs.attachments') }}
                </a>
            </li>
            @endif

            @if($customer->show_billing)
            <li class="{{ Request::get('tab') == 'expenses' ? 'active' : '' }}">
                <a href="#tab-expenses" data-toggle="tab">
                    {{ trans('account/shipments.modal-show.tabs.expenses') }}
                </a>
            </li>
            @endif
        </ul>
        <div class="tab-content m-b-0">
            <div class="tab-pane {{ Request::get('tab') == 'status' ? '' : 'active' }}" id="tab-info">
                @include('account.shipments.partials.show.info')
            </div>

            <div class="tab-pane {{ Request::get('tab') == 'status' ? 'active' : '' }}" id="tab-status">
                @include('account.shipments.partials.show.status')
            </div>

            @if(!$shipment->pack_dimensions->isEmpty())
            <div class="tab-pane {{ Request::get('tab') == 'dimensions' ? 'active' : '' }}" id="tab-dimensions">
                @include('account.shipments.partials.show.dimensions')
            </div>
            @endif

            @if(@$customer->settings['show_shipment_attachments'] || Setting::get('show_shipment_attachments'))
            <div class="tab-pane attachments-content {{ Request::get('tab') == 'attachments' ? 'active' : '' }}" id="tab-attachments">
                @include('account.shipments.partials.show.attachments')
            </div>
            @endif

            @if($customer->show_billing)
            <div class="tab-pane {{ Request::get('tab') == 'expenses' ? 'active' : '' }}" id="tab-expenses">
                @include('account.shipments.partials.show.expenses')
            </div>
            @endif
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
</div>

<script>
    $('[data-target="#modal-signature"]').on('click', function(){
        var receiver  = $(this).data('receiver');
        var signature = $(this).data('signature');

        $('#modal-signature').find('img').attr('src', signature);
        $('#modal-signature').find('.receiver b').html(receiver)
        if(receiver != '') {
            $('#modal-signature').find('.receiver').show()
        }
        $('#modal-signature').addClass('in').show();
    })

    $('#modal-signature button').on('click', function(){
        $('#modal-signature').find('img').attr('src', '');
        $('#modal-signature').find('.receiver b').html('')
        $('#modal-signature').find('.receiver').hide();
        $('#modal-signature').removeClass('in').hide();
    })
</script>