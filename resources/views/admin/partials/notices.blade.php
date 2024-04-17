<div class="notice-body notice-{!! @Auth::user()->last_notice->first()->level !!}">
    <div class="pull-right text-right">
        <a class="btn btn-default btn-sm btn-notice-readed"
           href="{{ route('admin.notices.read', @Auth::user()->last_notice->first()->pivot->notice_id) }}"
           data-method="post"
           data-confirm-title="Confirmar leitura"
           data-confirm="Confirma que tomou conhecimento desta informação?<br/>A mensagem não será apresentada de novo."
           data-confirm-label="Confirmar"
           data-confirm-class="btn-success"
        >
            <i class="fas fa-check"></i> Marcar como Lido
        </a>
        <a class="btn btn-default btn-sm btn-notice-readed"
           href="{{ route('admin.notices.read', 'all') }}"
           data-method="post"
           data-confirm-title="Confirmar leitura"
           data-confirm="Confirma que tomou conhecimento desta informação?<br/>A mensagem não será apresentada de novo."
           data-confirm-label="Confirmar"
           data-confirm-class="btn-success"
        >
            <i class="fas fa-check"></i> Marcar todos como Lido
        </a>
    </div>
    <h4>
        {!! @Auth::user()->last_notice->first()->title !!}
        @if(@Auth::user()->last_notice->first()->content)
        <a href="{{ route('admin.notices.show', @Auth::user()->last_notice->first()->pivot->notice_id) }}"
           data-toggle="modal"
           data-target="#modal-remote-lg">Ler tudo <i class="fas fa-angle-right"></i>
        </a>
        @endif
    </h4>
    <p class="summary">
        {!! nl2br(@Auth::user()->last_notice->first()->summary) !!}
    </p>
</div>
