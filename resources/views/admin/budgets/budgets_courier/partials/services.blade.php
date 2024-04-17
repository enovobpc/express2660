<table class="table table-condensed table-hover table-services m-0">
    <thead>
        <tr>
            <th>Serviço</th>
            <th class="w-80px">Qt</th>
            <th class="w-110px">Preço Un</th>
            <th class="w-110px">Subtotal</th>
            <th class="w-100px">IVA</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
    <?php $rowsVisible = 6 ?>
    @for($i=0 ; $i<12 ; $i++)
        <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
            <td style="padding-left: 0">
                {{ Form::select('services['.$i.'][service_id]', ['' => ''] + $services, null, ['class' => 'form-control input-sm select2']) }}
            </td>
            <td>
                {{ Form::text('services['.$i.'][qt]', $budget->exists ? null : 1, ['class' => 'form-control input-sm service-qty']) }}
            </td>
            <td class="input-sm">
                <div class="input-group">
                    {{ Form::text('services['.$i.'][price]', null, ['class' => 'form-control input-sm service-price']) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </td>
            <td class="input-sm">
                <div class="input-group">
                    {{ Form::text('services['.$i.'][subtotal]', null, ['class' => 'form-control input-sm service-subtotal']) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </td>
            <td class="input-sm">
                <div class="input-group">
                    {{ Form::text('services['.$i.'][vat]', null, ['class' => 'form-control input-sm']) }}
                    <span class="input-group-addon">%</span>
                </div>
            </td>
            <td>
                <a href="#" class="text-red remove-services">
                    <i class="fas fa-times m-t-8"></i>
                </a>
            </td>
        </tr>
    @endfor
    </tbody>
</table>
<button type="button" class="btn btn-xs btn-default btn-add-services"><i class="fas fa-plus"></i> Adicionar Serviço</button>