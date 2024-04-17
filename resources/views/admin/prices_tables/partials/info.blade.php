{{ Form::model($priceTable, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-12">
                <div class="row row-5">
                    <div class="col-sm-5">
                        <div class="row row-5">
                            <div class="col-sm-8">
                                <div class="form-group is-required">
                                    {{ Form::label('name', __('Designação')) }}
                                    {{ Form::text('name', null, ['class' => 'form-control ucwords', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="checkbox m-t-25 m-b-15">
                                    <label style="padding-left: 0 !important">
                                        {{ Form::checkbox('active', 1, null) }}
                                        @trans('Tabela ativa')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('color', __('Identificador')) }}<br/>
                            {{ Form::select('color', trans('admin/global.colors')) }}
                        </div>
                    </div>
                    <div class="col-sm-4 col-sm-offset-1">
                        <label>@trans('Disponibilizar esta tabela para as Agências')</label>
                        <div class="row row-5">
                            <div class="col-xs-12">
                                @if($agencies->count() >= 6)
                                    <div style="max-height: 155px;overflow: scroll;border: 1px solid #ddd;padding: 0 8px;">
                                        @endif
                                        @foreach($agencies as $agency)
                                            <div class="checkbox m-t-5 m-b-8">
                                                <label style="padding-left: 0">
                                                    {{ Form::checkbox('agencies[]', $agency->id, null) }}
                                                    <span class="label" style="background: {{ $agency->color }}">{{ $agency->code }}</span> {{ $agency->print_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                        @if($agencies->count() >= 6)
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary pull-left">@trans('Gravar')</button>
        <div class="clearfix"></div>
    </div>
</div>
{{ Form::close() }}
