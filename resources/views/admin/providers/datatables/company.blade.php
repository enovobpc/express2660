{{ $row->company }}
<br/>
{{--<small class="text-muted">
    {{ $row->zip_code }} {{ $row->city }}
</small>--}}
@if($row->vat)
    <small class="text-muted">
        @if($row->vat)
        @trans('NIF:') {{ $row->vat }} &bull;
        @endif
        {{ @$row->category->name }}
    </small>
@endif