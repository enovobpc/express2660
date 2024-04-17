{{ Form::model($checklist, array('route' => array('admin.fleet.checklists.answer.store', $checklist->id), 'method' => 'POST', 'files' => true)) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Responser:')' {{ $checklist->title }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-6">
            <div class="form-group form-group-sm is-required">
                {{ Form::label('vehicle_id', __('Viatura')) }}
                {{ Form::select('vehicle_id', ['' => ''] + $vehicles, null, ['class' => 'form-control input-sm select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group form-group-sm is-required">
                {{ Form::label('operator_id', __('Motorista')) }}
                {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group form-group-sm is-required">
                {{ Form::label('km', __('Km')) }}
                <div class="input-group">
                    {{ Form::text('km', null, ['class' => 'form-control number', 'required']) }}
                    <div class="input-group-addon">@trans('km')</div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <th class="bg-gray-light w-1">#</th>
                        <th class="bg-gray-light">@trans('Pergunta')</th>
                        <th class="bg-gray-light w-120px">@trans('Resposta')</th>
                        <th class="bg-gray-light w-250px">@trans('Notas')</th>
                    </tr>
                    @if($checklist->items)
                        @foreach($checklist->items as $key => $item)
                            <tr>
                                <td class="vertical-align-middle text-center">{{ $key + 1 }}</td>
                                <td class="vertical-align-middle">
                                    {{ $item->name }}
                                    @if($item->description)
                                        <br/>
                                        <small class="italic text-muted">{{ $item->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    <label class="checkbox-inline">
                                        {{ Form::radio('item['.$item->id.'][answer]', 1) }} @trans('Sim')
                                    </label>
                                    <label class="checkbox-inline">
                                        {{ Form::radio('item['.$item->id.'][answer]', 0) }} @trans('Não')
                                    </label>
                                </td>
                                <td class="input-sm">
                                    @if($item->type == 'input')
                                        {{ Form::text('item['.$item->id.'][obs]', null, ['class' => 'form-control input-sm']) }}
                                    @elseif($item->type == 'numeric')
                                        {{ Form::number('item['.$item->id.'][obs]', null, ['class' => 'form-control input-sm', 'min' => 0]) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
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

