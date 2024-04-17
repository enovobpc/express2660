<div class="row">
    <div class="col-xs-12">
        <h4 class="bold m-t-0 text-blue">4. FORNECEDORES</h4>
    </div>
    <div class="col-sm-6">
        @for($i=0 ; $i< 3 ; $i++)
        <div class="row row-5">
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('providers['.$i.'][name]', 'Nome Fornecedor '.($i+1)) }}
                    {{ Form::text('providers['.$i.'][name]', null, ['class' => 'form-control', $i ? '' : 'required']) }}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {{ Form::label('providers['.$i.'][webservice_method]', 'Rede associada') }}
                    {{ Form::select('providers['.$i.'][webservice_method]', ['' => ''] + $webserviceMethods, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-7">
                <div class="form-group">
                    {{ Form::label('providers['.$i.'][color]', 'Idêntificador') }}<br/>
                    {{ Form::select('providers['.$i.'][color]', $colors, null, ['class' => 'color']) }}
                </div>
            </div>
        </div>
        @endfor
    </div>
    <div class="col-sm-6">
        @for($i=3 ; $i< 6 ; $i++)
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('providers['.$i.'][name]', 'Nome Fornecedor '.($i+1)) }}
                        {{ Form::text('providers['.$i.'][name]', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('providers['.$i.'][webservice_method]', 'Rede associada') }}
                        {{ Form::select('providers['.$i.'][webservice_method]', ['' => ''] + $webserviceMethods, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group">
                        {{ Form::label('providers['.$i.'][color]', 'Idêntificador') }}<br/>
                        {{ Form::select('providers['.$i.'][color]', $colors, null, ['class' => 'color']) }}
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>

