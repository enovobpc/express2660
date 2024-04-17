<?php
$maxMatrix = count(@$service->matrix_from);
$maxMatrix = $maxMatrix > 6 ? $maxMatrix : 6;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto">
            <table class="table table-condensed table-matrix m-0" style="width: 100%">
                <thead>
                    <tr>
                        <th>Códigos Postais Origem</th>
                        <th>Códigos Postais Destino</th>
                        <th class="w-160px">Zona Taxável</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i=0 ; $i<= $maxMatrix; $i++)
                    <tr>
                        <td>
                            <textarea name="matrix_from[]" class="form-control nospace" rows="1">{{ @$service->matrix_from[$i] }}</textarea>
                        </td>
                        <td>
                            <textarea name="matrix_to[]" class="form-control nospace" rows="1">{{ @$service->matrix_to[$i] }}</textarea>
                        </td>
                        <td style="vertical-align: top">
                            {{ Form::select('matrix_zones[]', [''=>''] + $billingZonesList, @$service->matrix_zones[$i], ['class' => 'select2']) }}
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
            <button type="button" class="btn btn-xs btn-default btn-add-matrix m-l-5 m-b-10">
                <i class="fas fa-plus"></i> Adicionar linha
            </button>
        </div>
    </div>
</div>