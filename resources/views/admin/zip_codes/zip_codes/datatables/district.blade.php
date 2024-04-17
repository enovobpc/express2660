@if($row->district_code)
    {{ $row->district_code }} - {{ trans('districts_codes.districts.'.$row->country.'.'.$row->district_code) }}
@endif