<div class="form-group is-required">
    {{ Form::label('fields2_title', 'Titulo') }}
    {{ Form::text('fields2_title', null, ['class' => 'form-control']) }}
</div>
<table class="table table-condensed">
    <tr>
        <th class="w-200px">Campo</th>
        <th class="w-100px">Tipo Dados</th>
        <th class="w-100px">Detalhe Dados</th>
        <th class="w-50px">Obrigatório</th>
        <th>Descrição</th>
    </tr>
    @for($i = 0 ; $i<=30 ; $i++)
        <tr>
            <td>
                {{ Form::text('fields2['.$i.'][field]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td class="input-sm">
                {{ Form::select('fields2['.$i.'][type]', ['string' => 'String', 'integer' => 'Integer', 'decimal' => 'Decimal', 'boolean' => 'Boolean', 'date' => 'Date'], null, ['class' => 'form-control select2']) }}
            </td>
            <td class="input-sm">
                {{ Form::text('fields2['.$i.'][length]', null, ['class' => 'form-control']) }}
            </td>
            <td class="input-sm">
                {{ Form::select('fields2['.$i.'][required]', ['0' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
            </td>
            <td>
                {{ Form::text('fields2['.$i.'][description]', null, ['class' => 'form-control input-sm']) }}
            </td>
        </tr>
    @endfor
</table>