@section('title')
    Emitir Avisos e Notificações
@stop

@section('content-header')
    Emitir Avisos e Notificações
    <small>{{ $action }}</small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.notices.index') }}">
            Emitir Avisos e Notificações
        </a>
    </li>
    <li class="active">
        {{ $action }}
    </li>
@stop

@section('plugins')
    {{ HTML::script('vendor/ckeditor/ckeditor.js')}}
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-solid">
            {{ Form::model($notice, $formOptions) }}
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-10">
                        <div class="form-group is-required">
                            <label>Título</label>
                            {{ Form::text('title', null, array('class' =>'form-control', 'required')) }}
                        </div>
                        <div class="form-group">
                            <label>Resumo</label>
                            {{ Form::textarea('summary', null, array('class' =>'form-control', 'rows' => 2)) }}
                        </div>
                        <div class="form-group is-required">
                            {{ Form::label('content', 'Detalhe')}}
                            {{ Form::textarea('content', null, array('class' =>'form-control ckeditor', 'required', 'rows' => 20)) }}
                        </div>
                        <div style="display: none">
                            <label>Enviar aviso para as plataformas</label>
                            <div class="pull-right">
                                <a href="#" data-check="all">Todos</a> |
                                <a href="#" data-check="gls">GLS</a> |
                                <a href="#" data-check="envialia">Enviália</a> |
                                <a href="#" data-check="tipsa">Tipsa</a> |
                                <a href="#" data-check="courier">Estafetas</a>
                            </div>
                            <div class="row m-b-15" id="sources">
                                @foreach($sources as $source)
                                    <div class="col-sm-2">
                                        <div class="checkbox m-t-5 m-b-0">
                                            <label style="padding-left: 0">
                                                {{ Form::checkbox('sources[]', $source->source, null, ['class' => 'source-item', 'data-partner' => $source->partners]) }}
                                                {{ $source->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-2">
                        <div class="form-group">
                            {{ Form::label('date', 'Data') }}
                            <div class="input-group">
                                @if($notice->date)
                                {{ Form::text('date', date('Y-m-d', strtotime($notice->date)), array('class' =>'form-control datepicker', 'required' => true)) }}
                                @else
                                {{ Form::text('date', date('Y-m-d'), array('class' =>'form-control datepicker', 'required' => true)) }}
                                @endif
                                <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('image', 'Logótipo', array('class' => 'form-label')) }}<br/>
                            <div class="fileinput {{ $notice->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 100%; height: 200px;">
                                    <img src="{{ asset('assets/img/default/default.thumb.png') }}">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100%; max-height: 200px;">
                                    @if($notice->filepath)
                                    <img src="{{ asset($notice->getCroppa(250)) }}">
                                    @endif
                                </div>
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
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label style="padding-left: 0 !important">
                                    {{ Form::checkbox('published', 1) }}
                                    Notícia Publicada
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label style="padding-left: 0 !important">
                                    {{ Form::checkbox('notify', 1, $notice->exists ? false : true) }}
                                    Notificar destinatários
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        {{ Form::hidden('delete_photo') }}
                        {{ Form::submit('Gravar', array('class' => 'btn btn-primary' )) }}
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>  
    </div>
</div>
@stop

@section('scripts')
<script>

    $('[data-check]').on('click', function(e) {
        e.preventDefault();

        var curPartner = $(this).data('check');
        if(curPartner == 'all') {
            if($('.source-item:checked').length) {
                $('.source-item').prop('checked', false);
            } else {
                $('.source-item').prop('checked', true);
            }
        } else {

            $('.source-item').each(function (item) {
                partners = $(this).data('partner');
                partners = partners.split(',');

                if(partners.includes(curPartner)) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            })
        }
    });

    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })

    CKEDITOR.config.height = '250px';
    CKEDITOR.replace('.ckeditor');

</script>
@stop

