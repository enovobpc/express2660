<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Visualizar e-mail</h4>
</div>
<div class="modal-body">
    <div>
        <h4 class="m-t-0 bold">{{ $email->subject }}</h4>
        <table class="table table-condensed w-100 m-0">
            <tr>
                <td class="w-80px">Enviado:</td>
                <td>
                    {{ $email->sended_at }} (<i>E-mail dispultado por {{ @$email->sended_by ? @$email->user->name : 'Sistema, automáticamente' }}</i>)
                    <button class="btn btn-xs btn-default pull-right btn-show-headers">Ver headers <i class="fas fa-angle-down"></i></button>
                </td>
            </tr>
            <tr>
                <td>De:</td>
                <td>{{ $email->from }}</td>
            </tr>
            <tr>
                <td>Para:</td>
                <td>{{ $email->to }}</td>
            </tr>
            @if($email->cc)
            <tr>
                <td>Cópia (CC):</td>
                <td>{{ $email->cc }}</td>
            </tr>
            @endif
            @if($email->bcc)
            <tr>
                <td>Oculto (Bcc):</td>
                <td>{{ $email->bcc }}</td>
            </tr>
            @endif
            @if($email->attached_docs || $email->attached_files)
            <tr>
                <td>Anexos:</td>
                <td>
                    @if($email->attached_docs)
                    @foreach ($email->attached_docs as $attachment)
                        <span class="label label-default">{{ @$attachment->name }}</span>
                    @endforeach
                    @endif

                    @if($email->attached_files)
                    @foreach ($email->attached_files as $attachment)
                        <span class="label label-default">{{ @$attachment->name }}</span>
                    @endforeach
                    @endif
                </td>
            </tr>
            @endif
        </table>
        <br/>
    </div>
    
    <div class="headers" style="display: none;     
    padding: 10px;
    border: 1px solid #ddd;
    margin-bottom: 5px;
    background: #eee;">
        {!! nl2br($email->headers) !!}
    </div>
    <div style="border: 1px solid #ddd;
    height: 550px;
    overflow: scroll;">
    {!! $email->message !!}
    </div>
</div>
<div class="modal-footer text-right">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    </div>
</div>

<script>
    $('.modal .btn-show-headers').on('click', function(){
        $('.headers').slideToggle();
    })
</script>
