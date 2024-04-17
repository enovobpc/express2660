{{ Form::open(['route' => 'admin.prices-tables.store']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Adicionar Tabela de Preços Global')</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('name', __('Designação')) }}
        {{ Form::text('name', null, ['class' => 'form-control ucwords', 'autofocus', 'required']) }}
    </div>
{{--    <div class="form-group is-required">
        {{ Form::label('agencies[]', 'Disponibilizar tabela para as Agências') }}
        {{ Form::select('agencies[]', $agencies, null, ['class' => 'form-control select2', 'required', 'multiple' => true]) }}
    </div>--}}
    {{ Form::label('agencies[]', __('Disponibilizar tabela para as Agências')) }}
    <div class="row row-5">
        <div class="col-xs-12">
            @if($agencies->count() >= 12)
                <div style="max-height: 205px;overflow: scroll;border: 1px solid #ddd;padding: 0 8px;">
                    @endif
                    @foreach($agencies as $agency)
                        <div class="checkbox m-t-5 m-b-8">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('agencies[]', $agency->id, null, ['class' => 'row-agency']) }}
                                <span class="label" style="background: {{ $agency->color }}">{{ $agency->code }}</span> {{ $agency->print_name }}
                            </label>
                        </div>
                    @endforeach
                    @if($agencies->count() >= 12)
                </div>
            @endif
        </div>
    </div>
    <div class="form-group m-b-0">
        {{ Form::label('color', __('Identificador')) }}<br/>
        {{ Form::select('color', trans('admin/global.colors')) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('active', 1) }}
{{ Form::close() }}

{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $('.select2').select2(Init.select2());
    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
        $('#modal-remote .select2').select2(Init.select2());
    })
</script>