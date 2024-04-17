<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Consultar troca de e-mails a fornecedor</h4>
</div>
<div class="modal-body">
    <table class="table table-condensed">
        <tr class="bg-gray-light">
            <th class="w-150px">De</th>
            <th>Mensagem</th>
        </tr>
        @foreach($proposeMessages as $message)
            <tr>
                <td>
                    <b>{{ $message->from_name }}</b>
                    <br/>
                    <p>
                        <small class="text-muted font-size-13px">
                            <i>{{ $message->from }}</i><br/>
                            {{ $message->created_at }}
                        </small>
                    </p>
                    @if($message->attachments)
                        @foreach($message->attachments as $attachment)
                            <a href="{{ route('admin.budgets.proposes.attachment', [$message->id, str_slug($attachment->name)]) }}" target="_blank" class="budget-attachment">
                                <i class="fas fa-file"></i> {{ $attachment->name }}
                            </a>
                        @endforeach
                    @endif
                </td>
                <td>
                    <?php
                        $content = $message->message;
                        $content = str_replace('style', 'style2', $content);
                    ?>
                    {!! $content !!}
                </td>
            </tr>
        @endforeach
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>