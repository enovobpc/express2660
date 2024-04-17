
<div class="row row-10">
    <div class="col-xs-12">
        <h4 class="bold text-blue">3. UTILIZADOR PRINCIPAL</h4>
    </div>
    <div class="col-sm-12">
        <div class="form-group is-required">
            {{ Form::label('user_name', 'Nome') }}
            {{ Form::text('user_name', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group is-required">
            {{ Form::label('user_email', 'E-mail') }}
            {{ Form::text('user_email', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group is-required">
            {{ Form::label('password', 'Password') }}
            {{ Form::text('password', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>

