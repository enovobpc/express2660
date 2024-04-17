@if(in_array($row->unity, ['zip_code', 'pack_zip_code', 'matrix']))
<div class="text-center">
    @if($row->country)
        <i class="flag-icon flag-icon-{{ $row->country }}" data-toggle="tooltip" title="{{ trans('country.'.$row->country) }}"></i>
    @else
        <i class="fas fa-globe"  data-toggle="tooltip" title="Qualquer país"></i>
    @endif
</div>
@endif