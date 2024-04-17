<table class="table table-condensed table-hover table-animals m-0">
    <thead>
        <tr>
            <th class="w-110px">Espécie</th>
            <th>Nome</th>
            <th class="w-150px">Raça</th>
            <th class="w-80px">Idade</th>
            <th class="w-110px">Peso Animal</th>
            <th class="w-110px">Peso Caixa</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
    <?php $rowsVisible = 3 ?>
    @for($i=0 ; $i<12 ; $i++)
        <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
            <td style="padding-left: 0">
                {{ Form::select('animals['.$i.'][type]', ['' => '', 'dog' => 'Cão', 'cat' => 'Gato', 'other' => 'Outro'], null, ['class' => 'form-control input-sm select2']) }}
            </td>
            <td class="input-sm">
                {{ Form::text('animals['.$i.'][name]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td>
                {{ Form::text('animals['.$i.'][specie]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td>
                {{ Form::text('animals['.$i.'][age]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td class="input-sm">
                <div class="input-group">
                    {{ Form::text('animals['.$i.'][weight]', null, ['class' => 'form-control input-sm']) }}
                    <span class="input-group-addon">kg</span>
                </div>
            </td>
            <td class="input-sm">
                <div class="input-group">
                    {{ Form::text('animals['.$i.'][weight_box]', null, ['class' => 'form-control input-sm']) }}
                    <span class="input-group-addon">kg</span>
                </div>
            </td>
            <td>
                <a href="#" class="text-red remove-animals">
                    <i class="fas fa-times m-t-8"></i>
                </a>
            </td>
        </tr>
    @endfor
    </tbody>
</table>
<button type="button" class="btn btn-xs btn-default btn-add-animals"><i class="fas fa-plus"></i> Adicionar Animal</button>
<div class="row">
    <div class="col-sm-9">
        <div class="form-group m-t-10">
            {{ Form::label('obs', 'Observações') }}
            {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 1]) }}
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group m-t-10">
            {{ Form::label('box_dimensions', 'Dimensões Caixa') }}
            {{ Form::text('box_dimensions', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
