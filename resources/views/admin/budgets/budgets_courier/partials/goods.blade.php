<table class="table table-condensed table-hover table-goods m-0">
    <thead>
        <tr>
            <th>Descrição mercadoria</th>
            <th class="w-150px">Serviço</th>
            <th class="w-80px">Volumes</th>
            <th class="w-120px">Peso</th>
            <th class="w-120px">Peso Vol.</th>
            <th class="w-150px">Dimensão (CxLxA)</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
    <?php $rowsVisible = 3 ?>
    @for($i=0 ; $i<12 ; $i++)
        <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
            <td class="input-sm">
                {{ Form::text('goods['.$i.'][description]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td class="input-sm" style="padding-left: 0">
                {{ Form::select('goods['.$i.'][service]', ['' => ''] + $courierServices, null, ['class' => 'form-control input-sm select2']) }}
            </td>
            <td class="input-sm">
                {{ Form::text('goods['.$i.'][volumes]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td>
                <div class="input-group">
                    {{ Form::text('goods['.$i.'][weight]', null, ['class' => 'form-control input-sm']) }}
                    <span class="input-group-addon">kg</span>
                </div>
            </td>
            <td class="input-sm">
                <div class="input-group">
                    {{ Form::text('goods['.$i.'][volumetric_weight]', null, ['class' => 'form-control input-sm']) }}
                    <span class="input-group-addon">kg</span>
                </div>
            </td>
            <td class="input-sm">
                {{ Form::text('goods['.$i.'][dimension]', null, ['class' => 'form-control input-sm']) }}
            </td>
            <td>
                <a href="#" class="text-red remove-goods">
                    <i class="fas fa-times m-t-8"></i>
                </a>
            </td>
        </tr>
    @endfor
    </tbody>
</table>
<button type="button" class="btn btn-xs btn-default btn-add-goods"><i class="fas fa-plus"></i> Adicionar Mercadoria</button>