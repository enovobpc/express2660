@if($row->county_code)
    @if(is_array(trans('districts_codes.counties.'.$row->country.'.'.$row->district_code)))
        {{ $row->county_code}} - {{ trans('districts_codes.counties.'.$row->country.'.'.$row->district_code.'.'.$row->county_code) }}
    @else
        {{ $row->county_code }}
    @endif
@endif