<div class="row">
    <div class="col-sm-12">
        <div class="row row-5 modal-filters" style="margin: -15px -15px 10px;
    background: #eee;
    padding: 6px 10px 0px;
    border-bottom: 1px solid #ddd;">
            @include('admin.zip_codes.agencies.partials.filters')
        </div>
        <div class="import-search-results">
            <div class="helper">
                <i class="fas fa-search"></i>
                Escolha um distrito ou concelho para procurar códigos postais.
            </div>
        </div>
        <div class="form-group m-t-15">
            {{ Form::label('zip_codes', 'Este serviço está disponível apenas para os códigos (separados por vírgula):') }}
            {{ Form::textarea('zip_codes', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>