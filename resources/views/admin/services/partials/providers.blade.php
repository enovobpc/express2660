<div class="row">
    <div class="col-xs-2">
        <div class="list-group providers-sidebar-list" style="height: 425px;overflow-y: auto;margin-bottom: 0;">
        <?php $i = 0; ?>
        @foreach($providers as $id => $providerName)
            <a href="#" class="list-group-item {{ $i ? null : 'active' }}"
               data-provider-url="{{ route('admin.services.provider-details', [$service->id, 'providerId' => $id]) }}"
               data-provider-id="{{ $id }}">
                {{ $providerName }}
                @if(!empty(@$service->zones_provider[$id]) || !empty(@$service->webservice_mapping[$id]))
                <i class="fas fa-circle pull-right m-t-3 text-green"></i>
                @endif
            </a>
            <?php $i++ ?>
        @endforeach
        </div>
    </div>
    <div class="col-xs-10">
        <div class="provider-options">
            @include('admin.services.partials.provider_options')
        </div>
    </div>
</div>
