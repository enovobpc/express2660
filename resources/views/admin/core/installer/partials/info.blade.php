<div class="col-xs-12">
    <h4 class="bold m-t-0 text-blue">2. DADOS DA AGÊNCIA</h4>
</div>
<div class="row row-10">
    <div class="col-sm-8 col-md-9">
        @if(!$agency->exists)
        <div class="col-sm-12">
            <label class="m-t-0 font-weight-normal">Escolher um ID de Agência</label><br/>
            @foreach($availableIds as $agencyId)
                <div class="checkbox-inline m-t-0" style="margin-left: 0; margin-right: 5px">
                    <label style="padding-left: 0">
                        {{ Form::radio('agency_id', $agencyId) }}
                        {{ str_pad($agencyId, 3, STR_PAD_LEFT, '0') }}
                    </label>
                </div>
            @endforeach
            <hr/>
        </div>
        @endif
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('code', 'Código') }}
                {{ Form::text('code', null, ['class' => 'form-control', 'maxlength' => '6', 'required', 'placeholder' => 'xxx01']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required" data-toggle="tooltip" title="Esta desinação apenas pode ser alterada por um administrador.">
                {{ Form::label('name', 'Nome no Sistema') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => '[xxx01] CENTRAL _nome_concelho_']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('print_name', 'Designação a ser Impressa') }}
                {{ Form::text('print_name', null, ['class' => 'form-control', 'required', 'placeholder' => 'CENTRAL _nome_concelho_']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('company', 'Designação Social') }}
                {{ Form::text('company', null, ['class' => 'form-control', 'required', 'placeholder' => 'Obrigatório ser o nome fiscal']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('vat', 'NIF') }}
                @if(Auth::user()->isAdmin())
                    <a href="#" class="prefill-data text-blue pull-right">Pré-Preencher</a>
                @endif
                {{ Form::text('vat', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {{ Form::label('charter', 'N.º Alvará') }}
                    {{ Form::text('charter', null, ['class' => 'form-control']) }}
                </div>
            </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('addresss', 'Morada') }}
                {{ Form::text('address', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('zip_code', 'Código Postal') }}
                {{ Form::text('zip_code', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('city', 'Localidade') }}
                {{ Form::text('city', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('country', 'País') }}
                {{ Form::select('country', trans('country'), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
            <div class="col-sm-4">
                <div class="form-group is-required">
                    {{ Form::label('web', 'Website') }}
                    {{ Form::text('web', null, ['class' => 'form-control', 'required', 'placeholder' => 'www.XXXXXXXXX.pt']) }}
                </div>
            </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('email', 'E-mail') }}
                {{ Form::text('email', null, ['class' => 'form-control', 'required', 'placeholder' => 'Só geral empresa']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('phone', 'Telefone') }}
                {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Sem Espaços']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('mobile', 'Telemóvel') }}
                {{ Form::text('mobile', null, ['class' => 'form-control', 'required', 'placeholder' => 'Sem Espaços']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('color', 'Idêntificador') }}<br/>
                {{ Form::select('color', ['' => ''] + $colors, null, ['required']) }}
            </div>
            <ol>
                <li>Conferir a côr do logotipo do cliente.</li>
                <li>Garantir que no ficheiro .ENV está configurada a cor base da aplicação.</li>
                <li>Nunca usar e-mails pessoais no contacto da empresa. Só email principal da empresa.</li>
            </ol>
        </div>

    </div>
    <div class="col-sm-4 col-md-3 p-r-30">
        {{ Form::label('image', 'Logótipo Cores', array('class' => 'form-label')) }}<br/>
        <div class="fileinput {{ $agency->filepath ? 'fileinput-exists' : 'fileinput-new'}}"
             data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                <img src="{{ asset('assets/img/default/default.thumb.png') }}">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail"
                 style="max-width: 200px; max-height: 200px;">
                @if($agency->filepath)
                    <img src="{{ asset(@$agency->filehost . $agency->getCroppa(250)) }}">
                @endif
            </div>
            @if(empty($agency->filehost) || (!empty($agency->filehost) && $agency->filehost == config('app.url').'/'))
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
                <p class="text-blue"><i class="fas fa-info-circle"></i> Apenas pode alterar a imagem desta agência no servidor {{ $agency->filehost }}</p>
            @endif
        </div>
        <hr class="m-t-5 m-b-5"/>
        {{ Form::label('image_black', 'Logótipo Preto', array('class' => 'form-label')) }}<br/>
        <div class="fileinput {{ $agency->filepath_black ? 'fileinput-exists' : 'fileinput-new'}}"
             data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                <img src="{{ asset('assets/img/default/default.thumb.png') }}">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail"
                 style="max-width: 200px; max-height: 200px;">
                @if($agency->filepath_black)
                    <img src="{{ asset(@$agency->filehost . $agency->getCroppaField('filepath_black', 250)) }}">
                @endif
            </div>
            {{--@if(empty($agency->filehost) || (!empty($agency->filehost) && $agency->filehost == config('app.url').'/'))--}}
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
                 <p class="text-blue"><i class="fas fa-info-circle"></i> Apenas pode alterar a imagem desta agência no servidor {{ $agency->filehost }}</p>
             @endif--}}
        </div>
    </div>
</div>
{{ Form::hidden('delete_photo') }}

