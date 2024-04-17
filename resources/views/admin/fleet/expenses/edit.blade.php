{{ Form::model($expense, $formOptions) }}
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
            <div class="form-group is-required">
                {{ Form::label('vehicle_id', __('Viatura')) }}
                {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('provider_id', __('Fornecedor')) }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('title', __('Descrição')) }}
                {{ Form::text('title', null, ['class' => 'form-control autocomplete', 'required', 'placeholder' => __('Ex.: Pagamento do seguro 2017.')]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('date', __('Data')) }}
                <div class="input-group">
                    {{ Form::text('date', $expense->exists ? $expense->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('operator_id', __('Operador')) }}
                {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('km', __('Km')) }}
                <div class="input-group">
                    {{ Form::text('km', null, ['class' => 'form-control number']) }}
                    <span class="input-group-addon">@trans('km')</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('total', __('Total')) }}
                <div class="input-group">
                    {{ Form::text('total', null, ['class' => 'form-control decimal', 'required']) }}
                    <span class="input-group-addon">
                        {{ Setting::get('app_currency') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group" style="display: {{ ($expense->exists && $expense->filepath) ?  'none' : 'block' }};" }}>
                {{ Form::label('name', __('Ficheiro a anexar')) }}
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput">
                        <i class="fas fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                    </div>
                    <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">@trans('Procurar...')</span>
                <span class="fileinput-exists">@trans('Alterar')</span>
                <input type="file" name="file">
            </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@trans('Remover')</a>
                </div>
            </div>
            <div class="form-group" style="display: {{ ($expense->exists && $expense->filepath) ?  'block' : 'none' }};" }}>
                {{ Form::label('name', __('Ficheiro a anexar')) }}
                <div>
                    <a href="{{ asset($expense->filepath) }}" target="_blank" class="">
                        <i class="fas fa-file"></i> {{ $expense->filename }}
                    </a>
                </div>
                <button class="btn btn-danger btn-xs m-t-10 btn-delete">
                    <i class="fas fa-trash-alt"></i> @trans('Eliminar o anexo') {{ $expense->filename }}
                </button>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('obs', __('Observações'), ['data-content' => '']) }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2, 'maxlength' => 150]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('delete_file') }}
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $(document).on('click', '.btn-delete', function(e){
        e.preventDefault();
        $(this).closest('.form-group').hide();
        $(this).closest('.form-group').prev().show();
        $('[name="delete_file"]').val(1);
    })

    $('.autocomplete').autocomplete({
        serviceUrl: '{{ route('admin.fleet.expenses.search.expense') }}',
        /*lookup: countries,*/
        onSelect: function (suggestion) {
            //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
        }
    });
</script>

