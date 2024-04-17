@if($row->conferred)
    <div class="confered">
        <button class="btn btn-sm btn-success save-row" data-toggle="tooltip" title="Este envio já foi conferido em {{ $row->conferred }}" type="button" data-id="{{ $row->id }}" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar">
            <i class="fas fa-save"></i> Gravar
        </button>
    </div>
@else
    <div class="confered" style="display: none">
        <button class="btn btn-sm btn-success save-row" data-toggle="tooltip" title="Este envio já foi conferido à minutos" type="button" data-id="{{ $row->id }}" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar">
            <i class="fas fa-save"></i> Gravar
        </button>
    </div>
    <div class="not-confered">
        <button class="btn btn-sm btn-default save-row" type="button" data-id="{{ $row->id }}" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar">
            <i class="fas fa-save"></i> Gravar
        </button>
    </div>
@endif