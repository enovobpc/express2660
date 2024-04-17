<div class="col-sm-3">
    <div class="form-group">
        {{ Form::label('country', 'País a procurar') }}
        {{ Form::select('country', trans('districts_codes.countries'), $country, ['class' => 'form-control select2']) }}
    </div>
</div>
@if(is_array(trans('districts_codes.districts.'.$country)))
    <div class="{{ !in_array($country, ['es']) ? 'col-sm-3' : 'col-sm-7' }}">
        <div class="form-group">
            {{ Form::label('district', 'Distrito/Região/Província') }} <i class="fas fa-spin fa-circle-notch hide"></i>
            {{ Form::select('district', ['' => ''] + trans('districts_codes.districts.' . $country), null, ['class' => 'form-control select2']) }}
        </div>
    </div>
    @if(!in_array($country, ['es']) && is_array(trans('districts_codes.counties.' . $country . '.'.$district)))
    <div class="col-sm-4">
        <div class="form-group">
            {{ Form::label('county', 'Concelho/Município') }}
            @if($district && !in_array($district, ['ac', 'md']))
            {{ Form::select('county', ['' => ''] + trans('districts_codes.counties.' . $country . '.'.$district), null, ['class' => 'form-control select2']) }}
            @else
            {{ Form::select('county', ['' => ''], null, ['class' => 'form-control select2']) }}
            @endif
            <div class="hide">
                {{ Form::select('all_counties', trans('districts_codes.counties.pt')) }}
            </div>
        </div>
    </div>
    @endif
@endif
<div class="col-sm-2">
    <button class="btn btn-block btn-success m-t-20 search-zip-codes" type="button">
        <i class="fas fa-search"></i> Procurar
    </button>
</div>