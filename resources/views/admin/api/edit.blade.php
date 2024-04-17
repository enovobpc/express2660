{{ Form::model($oauthClient, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('user_id', 'Cliente') }}
        {{ Form::select('user_id', [@$oauthClient->customer->id => @$oauthClient->customer->name], null, ['class' => 'form-control', 'data-placeholder' => 'Não associar nenhum cliente.']) }}
    </div>
    <div class="form-group is-required">
        {{ Form::label('name', 'Nome') }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    @if($oauthClient->exists)
    <div class="form-group is-required">
        {{ Form::label('__secret', 'Secret') }}
        {{ Form::text('__secret', $oauthClient->secret, ['class' => 'form-control', 'readonly']) }}
    </div>
    @else
    <div class="form-group is-required">
        {{ Form::label('secret', 'Secret') }}
        {{ Form::text('secret', str_random(40), ['class' => 'form-control', 'required']) }}
    </div>
    @endif
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('redirect', 'Redirect') }}
                {{ Form::text('redirect', $oauthClient->exists ? null : config('app.url'), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('authentication', 'Autenticação') }}
                {{ Form::select('authentication', ['' => 'Nenhum', 'password' => 'Credenciais + Password', 'credentials' => 'Apenas Credenciais'], $oauthClient->exists ? null : 'password', ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('revoked', 'Ativo') }}
                {{ Form::select('revoked', ['0' => 'Sim', '1' => 'Não'], $oauthClient->exists ? null : 'password', ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('daily_limit', 'Max Calls') }}
                {{ Form::text('daily_limit', $oauthClient->exists ? null : '1000', ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());

    $("select[name=user_id]").select2({
        ajax: {
            url: '{{ route('admin.api.search.customer') }}',
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()

                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        allowClear: true
    });

    $("select[name=user_id]").on('change', function () {
        var username = $(this).find('option:selected').text();
        $('[name="name"]').val(username)
    })
</script>

