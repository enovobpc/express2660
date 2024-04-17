<div class="row row-10">
    <div class="col-sm-12">
        <div class="col-sm-6" style="padding: 0 30px 0 15px;">
            <div class="form-group m-b-5">
                {{ Form::label('obs', 'Observações') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 10]) }}
            </div>
        </div>
        <div class="col-sm-6" style="padding: 0 15px 0 30px;">
            <div class="form-group m-b-5">
                {{ Form::label('accounting_info', 'Informação para Contabilidade') }}
                {{ Form::textarea('accounting_info', null, ['class' => 'form-control', 'rows' => 10]) }}
            </div>
        </div>
    </div>
</div>