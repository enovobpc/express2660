<div class="map-tools">
    <ul class="list-inline pull-left m-0">
        <li>
            <div class="map-item-box">
                @trans('Adicionar')
            </div>
        </li>
    </ul>
    <ul class="list-inline pull-left m-0" style="border-left: 1px solid #333">
        <li>@trans('Objeto')</li>
        <li style="width: 520px;">
            <small>@trans('Cor')</small>
            {{ Form::select('color', trans('admin/global.colors'), null, ['class' => 'form-control input-xs']) }}
        </li>
        <li>
            <small>@trans('Texto')</small>
            {{ Form::text('box_title', null, ['class' => 'form-control input-xs']) }}
        </li>
        <li>
            <small>@trans('Rack')</small>
            {{ Form::select('label', [], null, ['class' => 'form-control select2']) }}
        </li>
        <li>
            <small>@trans('Bay')</small>
            {{ Form::select('label', [], null, ['class' => 'form-control select2']) }}
        </li>
    </ul>
    <div class="clearfix"></div>
</div>

<div class="map-container">
    <div class="resize-drag" id="ob01">
        <span class="title">@trans('AC-02')</span>
        {{ Form::hidden('left') }}
        {{ Form::hidden('top') }}
        {{ Form::hidden('width') }}
        {{ Form::hidden('height') }}
        {{ Form::hidden('color') }}
        {{ Form::hidden('border') }}
        {{ Form::hidden('title') }}
    </div>
    <div class="resize-drag" style="background: red; width: 200px; height: 30px; left: 20px; top: 120px" id="ob02">
        <span class="title">@trans('AC-01')</span>
        {{ Form::hidden('left') }}
        {{ Form::hidden('y') }}
        {{ Form::hidden('width') }}
        {{ Form::hidden('height') }}
        {{ Form::hidden('color') }}
        {{ Form::hidden('border') }}
        {{ Form::hidden('title') }}
    </div>
</div>
<div class="resize-drag recize-model" id="ob01">
    <span class="title">@trans('xxxx')</span>
    {{ Form::text('left') }}
    {{ Form::text('top') }}
    {{ Form::text('width') }}
    {{ Form::text('height') }}
    {{ Form::text('color') }}
    {{ Form::text('border') }}
    {{ Form::text('title') }}
</div>
{{ Form::text('selected_obj') }}

