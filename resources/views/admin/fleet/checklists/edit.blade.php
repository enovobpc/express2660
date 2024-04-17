{{ Form::model($checklist, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required m-b-5">
                {{ Form::label('title', __('Título')) }}
                {{ Form::text('title', null, ['class' => 'form-control', 'required']) }}
            </div>
            <div class="checkbox">
                <label style="padding-left: 0; display: block;">
                    {{ Form::checkbox('is_active', 1) }}
                    @trans('Formulário ativo')
                </label>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('description', __('Descrição')) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div style="border: 1px solid #ddd; max-height: 300px; overflow-y: scroll">
                <table class="table table-condensed m-b-0">
                    <tr>
                        <th class="bg-gray-light w-1">#</th>
                        <th class="bg-gray-light">@trans('Pergunta')</th>
                        <th class="bg-gray-light w-260px">@trans('Ajuda/Descrição')</th>
                        <th class="bg-gray-light w-120px">@trans('Campo Anotações')</th>
                    </tr>
                    @if($checklist->items)
                        @foreach($checklist->items as $key => $item)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    {{ Form::text('item_name[]', $item->name, ['class' => 'form-control input-sm']) }}
                                    {{ Form::hidden('item_id[]', $item->id) }}
                                </td>
                                <td>
                                    {{ Form::text('item_obs[]', $item->obs, ['class' => 'form-control input-sm']) }}
                                </td>
                                <td class="input-sm">
                                    {{ Form::select('item_type[]', trans('admin/fleet.checklists.items-types'), $item->type, ['class' => 'form-control select2']) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    @for($i = $checklist->items ? $checklist->items->count() : 0 ; $i < 20 ; $i++)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>
                                {{ Form::text('item_name[]', null, ['class' => 'form-control input-sm']) }}
                            </td>
                            <td>
                                {{ Form::text('item_obs[]', null, ['class' => 'form-control input-sm']) }}
                            </td>
                            <td class="input-sm">
                                {{ Form::select('item_type[]', trans('admin/fleet.checklists.items-types'), null, ['class' => 'form-control select2']) }}
                            </td>
                        </tr>
                    @endfor
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());
</script>

