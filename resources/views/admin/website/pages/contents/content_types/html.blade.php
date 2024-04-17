<div class="row">
    <div class="tabbable-line margin-bottom-15" style="margin-top: -15px">
        <ul class="nav nav-tabs">
            @foreach(app_locales() as $code => $lang)
                <li class="{{ $code == 'pt' ? 'active' : '' }}">
                    <a href="#tab-text-{{ $code }}" data-toggle="tab" class="text-uppercase">
                        <i class="flag-icon flag-icon-{{ $code }}"></i> {{ $code }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="tab-content margin-bottom-0" style="padding-bottom: 10px;">
        @foreach(app_locales() as $code => $lang)
            <div class="tab-pane {{ $code == 'pt' ? 'active' : '' }}" id="tab-text-{{ $code }}">
                <div class="col-sm-12">
                    <div class="form-group is-required">
                        {{ Form::label('content', 'Código HTML') }}
                        <div class="line-numbers html-editor-{{ $code }}" style="height: 450px; width: 100%; border: 1px solid #ddd; padding: 8px">{{ @$content->translate($code)->content }}</div>
                        {!! Form::textarea($code . '[content]', @$content->translate($code)->content, ['class' => 'hide'])  !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
