{{ Form::model($company, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8 col-md-9">
            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group is-required">
                        {{ Form::label('vat', 'NIF') }}
                        {{ Form::text('vat', null, ['class' => 'form-control vat nospace', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group is-required">
                        {{ Form::label('name', 'Designação Social') }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('display_name', 'Designação Sistema') }}
                        {{ Form::text('display_name', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('charter', 'Alvará') }}
                        {{ Form::text('charter', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <hr style="margin: 0 0 15px;"/>
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group is-required">
                        {{ Form::label('address', 'Morada') }}
                        {{ Form::text('address', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('zip_code', 'Código Postal') }}
                        {{ Form::text('zip_code', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('city', 'Localidade') }}
                        {{ Form::text('city', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('country', 'País') }}
                        {{ Form::select('country', trans('country'), null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
            <hr style="margin: 0 0 15px;"/>
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('capital', 'Capital Social') }}
                        {{ Form::text('capital', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('conservatory', 'Conservatória') }}
                        {{ Form::text('conservatory', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('website', 'Website') }}
                        {{ Form::text('website', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('phone', 'Telefone') }}
                        {{ Form::text('phone', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('mobile', 'Telemóvel') }}
                        {{ Form::text('mobile', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('email', 'E-mail') }}
                        {{ Form::text('email', null, ['class' => 'form-control email']) }}
                    </div>
                </div>

            </div>
        </div>
        <div class="col-sm-4 col-md-3">
            {{ Form::label('image', 'Logótipo - Cores', array('class' => 'form-label')) }}<br/>
            <div class="fileinput {{ $company->filepath ? 'fileinput-exists' : 'fileinput-new'}}"
                 data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 200px; max-height: 200px;">
                    <img src="{{ asset('assets/img/default/default.png') }}">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail"
                     style="max-width: 200px; max-height: 200px;">
                    @if($company->filepath)
                        <img src="{{ asset(@$company->filehost . $company->getCroppa(250)) }}">
                    @endif
                </div>
                @if(empty($company->filehost) || (!empty($company->filehost) && $company->filehost == config('app.url').'/'))
                    <div>
                        <span class="btn btn-default btn-block btn-sm btn-file">
                            <span class="fileinput-new">Procurar...</span>
                            <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> Alterar</span>
                            <input type="file" name="image">
                        </span>
                        <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                            <i class="fas fa-close"></i> Remover
                        </a>
                    </div>
                @else
                    <p class="text-blue"><i class="fas fa-info-circle"></i> Apenas pode alterar a imagem desta agência no servidor {{ $company->filehost }}</p>
                @endif
            </div>
            <hr class="m-t-5 m-b-5"/>
            {{ Form::label('image_black', 'Logótipo Preto e Branco', array('class' => 'form-label')) }}<br/>
            <div class="fileinput {{ $company->filepath_black ? 'fileinput-exists' : 'fileinput-new'}}"
                 data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 200px; max-height: 200px;">
                    <img src="{{ asset('assets/img/default/default.png') }}">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail"
                     style="max-width: 200px; max-height: 200px;">
                    @if($company->filepath_black)
                        <img src="{{ asset(@$company->filehost . $company->getCroppaField('filepath_black', 250)) }}">
                    @endif
                </div>
                {{--@if(empty($company->filehost) || (!empty($company->filehost) && $company->filehost == config('app.url').'/'))--}}
                <div>
                        <span class="btn btn-default btn-block btn-sm btn-file">
                            <span class="fileinput-new">Procurar...</span>
                            <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> Alterar</span>
                            <input type="file" name="image_black">
                        </span>
                    <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                        <i class="fas fa-close"></i> Remover
                    </a>
                </div>
                {{-- @else
                     <p class="text-blue"><i class="fas fa-info-circle"></i> Apenas pode alterar a imagem desta agência no servidor {{ $company->filehost }}</p>
                 @endif--}}
            </div>

        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
</script>
