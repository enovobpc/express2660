{{--
<a href="{{ route('admin.trips.show', $row->children_id) }}" target="_blank">
    {{ $row->children_code }}
</a>--}}
<div class="text-center">
    @if($row->children_code)
    <span class="label bg-blue" style="cursor: pointer" data-s-filter="{{ $row->code }}">
        {{ $row->children_code }}
    </span>
    @endif
</div>
