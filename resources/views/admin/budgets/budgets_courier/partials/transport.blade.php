<table class="table table-condensed table-hover table-transport m-0">
    <thead>
        <tr>
            @if($budget->type == 'animals')
            <th style="width: 150px">Aeroporto Origem</th>
            <th style="width: 150px">Aeroporto Destino</th>
            @else
            <th style="width: 150px">Origem</th>
            <th style="width: 150px">Destino</th>
            @endif
            <th>Nota</th>
        </tr>
    </thead>
    <tbody>
        <?php $rowsVisible = 2 ?>
        @for($i=0 ; $i<4 ; $i++)
            <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
                <td style="padding-left: 0">
                    {{ Form::textarea('airports['.$i.'][source]', null, ['class' => 'form-control input-sm volumes', 'rows' => 2, 'style' => 'resize: none']) }}
                </td>
                <td class="input-sm">
                    {{ Form::textarea('airports['.$i.'][destination]', null, ['class' => 'form-control input-sm', 'rows' => 2, 'style' => 'resize: none']) }}
                </td>
                <td>
                    {{ Form::textarea('airports['.$i.'][obs]', null, ['class' => 'form-control input-sm', 'rows' => 2]) }}
                </td>
                <td>
                    <a href="#" class="text-red remove-transport">
                        <i class="fas fa-times m-t-8"></i>
                    </a>
                </td>
            </tr>
        @endfor
    </tbody>
</table>
<button type="button" class="btn btn-xs btn-default btn-add-transport"><i class="fas fa-plus"></i> Adicionar Transporte</button>
<hr style="margin-top: 15px; margin-bottom: 10px"/>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group m-b-0">
            {{ Form::label('pickup_address', 'Morada de Recolha') }}
            {{ Form::textarea('pickup_address', null, ['class' => 'form-control input-sm volumes', 'rows' => 2, 'style' => 'resize: none']) }}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group m-b-0">
            {{ Form::label('delivery_address', 'Morada de Entrega') }}
            {{ Form::textarea('delivery_address', null, ['class' => 'form-control input-sm volumes', 'rows' => 2, 'style' => 'resize: none']) }}
        </div>
    </div>
</div>