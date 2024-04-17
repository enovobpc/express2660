<table class="table table-condensed">
    <tr>
        <th class="w-200px">Parâmetro</th>
        <th class="w-100px">Tipo</th>
        <th class="w-50px">Obrigatório</th>
        <th>Descrição</th>
    </tr>
    @for($i = 0 ; $i<=10 ; $i++)
        <tr>
            <td>
                {{ Form::text('params['.$i.'][param]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td class="input-sm">
                {{ Form::select('params['.$i.'][type]', ['string' => 'String', 'integer' => 'Integer', 'decimal' => 'Decimal', 'boolean' => 'Boolean'], null, ['class' => 'form-control select2']) }}
            </td>
            <td class="input-sm">
                {{ Form::select('params['.$i.'][required]', ['0' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
            </td>
            <td>
                {{ Form::text('params['.$i.'][description]', null, ['class' => 'form-control input-sm']) }}
            </td>
        </tr>
    @endfor
</table>