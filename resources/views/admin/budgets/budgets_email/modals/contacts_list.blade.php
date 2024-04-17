{{ Form::open(['route' => array('admin.budgets.contacts.list.update')]) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Lista de Contactos de Fornecedor</h4>
</div>
<div class="modal-body">
    <table class="table table-condensed">
        <tr class="bg-gray-light">
            <th class="w-150px">Grupo</th>
            <th>Contactos</th>
        </tr>
        @if($contacts)
            @foreach($contacts as $group => $emails)
            <tr>
                <td>{{ Form::textarea('group[]', $group, ['class' => 'form-control', 'rows' => 3]) }}</td>
                <td>{{ Form::textarea('contacts[]', $emails, ['class' => 'form-control', 'rows' => 3]) }}</td>
            </tr>
            @endforeach
        @endif
        @for($i = count(array_keys((array)$contacts)) ; $i <= 5 ; $i++)
        <tr>
            <td>{{ Form::textarea('group[]', null, ['class' => 'form-control', 'rows' => 3]) }}</td>
            <td>{{ Form::textarea('contacts[]', null, ['class' => 'form-control', 'rows' => 3]) }}</td>
        </tr>
        @endfor
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}