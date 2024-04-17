{{ Form::model($agency, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group is-required">
                        {{ Form::label('code', 'Código') }}
                        @if(Auth::user()->hasRole([config('permissions.role.admin')]))
                            {{ Form::text('code', null, ['class' => 'form-control', 'required']) }}
                        @else
                            {{ Form::text('code', null, ['class' => 'form-control', 'disabled']) }}
                            {{ Form::hidden('code') }}
                        @endif
                    </div>
                </div>
               {{-- <div class="col-sm-6">
                    <div class="form-group is-required" data-toggle="tooltip" title="Esta desinação apenas pode ser alterada por um administrador.">
                        {{ Form::label('name', 'Designação em Sistema') }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'required', Auth::user()->hasRole(Config::get('permissions.role.admin')) ? '' : 'readonly']) }}
                    </div>
                </div>--}}
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('print_name', 'Designação do Centro Logístico') }}
                        {{ Form::text('print_name', null, ['class' => 'form-control', 'required', 'maxlength' => 40]) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('company_id', 'Empresa') }}
                        {!! Form::selectWithData('company_id', $companies, null, ['class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>
            </div>
            <hr style="margin-top: 5px"/>
        </div>
        <div class="col-sm-12">
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group">
                        {{ Form::label('company', 'Designação Social') }}
                        @if(Auth::user()->isAdmin())
                            <a href="#" class="prefill-data text-blue pull-right">Pré-Preencher Dados Empresa</a>
                        @endif
                        {{ Form::text('company', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('vat', 'NIF') }}
                        {{ Form::text('vat', null, ['class' => 'form-control vat']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('charter', 'Alvará') }}
                        {{ Form::text('charter', null, ['class' => 'form-control decimal']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('addresss', 'Morada') }}
                        {{ Form::text('address', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('zip_code', 'Código Postal') }}
                        {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('city', 'Localidade') }}
                        {{ Form::text('city', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('country', 'País') }}
                        {{ Form::select('country', trans('country'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('phone', 'Telefone') }}
                        {{ Form::text('phone', null, ['class' => 'form-control phone']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('mobile', 'Telemóvel') }}
                        {{ Form::text('mobile', null, ['class' => 'form-control phone']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('email', 'E-mail') }}
                        {{ Form::text('email', null, ['class' => 'form-control email']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('web', 'Website') }}
                        {{ Form::text('web', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group m-0">
                        {{ Form::label('color', 'Côr de Identificação') }}<br/>
                        {{ Form::select('color', $colors, $agency->color) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $('.modal .select2').select2(Init.select2())

    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
    })

    $('.prefill-data').on('click', function(e){
        e.preventDefault();

        var $selectedOp = $('.modal [name="company_id"] option:selected');

        $('[name="company"]').val($selectedOp.data('name'));
        $('[name="vat"]').val($selectedOp.data('vat'));
        $('[name="charter"]').val($selectedOp.data('charter'));
        $('[name="address"]').val($selectedOp.data('address'));
        $('[name="zip_code"]').val($selectedOp.data('zip_code'));
        $('[name="city"]').val($selectedOp.data('city'));
        $('[name="country"]').val($selectedOp.data('country')).trigger('change');
        $('[name="phone"]').val($selectedOp.data('phone'));
        $('[name="mobile"]').val($selectedOp.data('mobile'));
        $('[name="email"]').val($selectedOp.data('email'));
        $('[name="web"]').val($selectedOp.data('website'));
    })
</script>
