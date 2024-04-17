@if($row->country)
<i class="flag-icon flag-icon-{{ $row->country }}"></i>{{--  {{ trans('country.' . $row->zone) }} --}}
@else
<i class="fas fa-globle"></i>{{--  Global --}}
@endif