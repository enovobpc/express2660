@if(1 || $attachments->isEmpty())
    <div class="m-t-160 m-b-160 text-center">
        <h3 class="text-muted">
            <i class="fas fa-file-alt fs-40"></i><br/>
            @trans('Sem documentos associados.')
        </h3>
        <p class="text-muted">
            @trans('Não existem documentos associados a este mapa.')
        </p>
        <a href="{{ route('admin.trips.attachments.create', $trip->id) }}" data-toggle="modal" data-target="#modal-remote" class="btn btn-sm btn-default m-t-10">
            <i class="fas fa-plus"></i> @trans('Adicionar Anexo')
        </a>
    </div>
@else

@endif