@section('title')
    Terminal
@stop

@section('content-header')
    <i class="fas fa-terminal"></i> Terminal
@stop

@section('breadcrumb')
    <li class="active">Core</li>
    <li class="active">Terminal</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                @if(!Session::has('terminal_pwd'))
                    <div class="col-xs-12 col-sm-3 col-sm-offset-4">
                        {{ Form::open(['route' => 'admin.core.terminal.auth', 'method' => 'post']) }}
                            <div class="form-group m-t-50 m-b-50">
                                {{ Form::label('password', 'Palavra-passe') }}
                                <div class="input-group">
                                    {{ Form::password('password', ['class' => 'form-control']) }}
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default">Login</button>
                                    </div>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                @else
                    {{ Form::open(['route' => 'admin.core.terminal.execute', 'method' => 'post', 'class' => 'form-execute']) }}
                    <div class="row row-5">
                        {{--<div class="col-xs-3">
                            <div class="form-group">
                                {{ Form::label('command', 'Últimos existentes') }}
                                {{ Form::select('command', [], null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>--}}
                        <div class="col-xs-10 col-md-11">
                            <div class="form-group">
                                {{ Form::label('command', 'Comando a executar') }}
                                {{ Form::text('command', null, ['class' => 'form-control', 'required']) }}
                            </div>
                        </div>
                        <div class="col-xs-2 col-md-1">
                            <button type="submit" class="btn btn-block btn-success m-t-18">Executar</button>
                        </div>
                        <div class="col-xs-12">

                            <code class="terminal-result" style="min-height: 400px; border: 1px solid #ccc; overflow-y: scroll; display: block">
                            </code>
                        </div>
                    </div>
                    {{ Form::close() }}
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('styles')
    <style>
        code hr {
            margin-top: 10px;
            margin-bottom: 10px;
            border: 0;
            border-top: 1px dashed #af6060;
        }
        pre {
            display: block;
            padding: 5px 0 5px;
            margin: 0;
            font-size: 13px;
            line-height: 1.42857143;
            color: #333;
            word-break: break-all;
            word-wrap: break-word;
            background-color: transparent;
            border: none;
            border-radius: 4px;
        }
    </style>
@stop

@section('scripts')
{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.min.js')) }}
<script type="text/javascript">
    $('.form-execute').on('submit', function (e) {
        e.preventDefault()

        var $form   = $('.form-execute');
        var $button = $form.find('button');
        var $result = $('.terminal-result');
        var action  = $form.attr('action');
        var command = $('[name="command"]').val();

        if(command == 'clear') {
            $result.html('');
            $button.button('reset');
        } else {
            $result.prepend('<hr/>')
            $result.prepend('<pre class="loading"><i class="fas fa-spin fa-circle-notch"></i> A executar...</pre>');

            $.post(action, {command: command}, function (data) {
                if (data.result) {
                    $result.prepend(data.output)
                } else {
                    $result.prepend(data.output)
                }
            }).fail(function () {
                $result.html('<i class="fas fa-exclamation-circle"></i> Falha na execução.');
            }).always(function () {
                $button.button('reset');
                $(document).find('pre.loading').remove();
            });
        }
    })

    /**
     * SEARCH SENDER
     * ajax method
     */
    $('[name="command"]').autocomplete({
        lookup: {!! $commands !!},
        minChars: 2
    });
</script>
@stop