<div>
    {{ $row->unity ? trans('admin/global.measure-units.' . $row->unity) : 'Un' }}

    @if ($row->unities_by_pack)
        <br />
        <small class="italic">{{ $row->unities_by_pack }} Un. Pac.</small>
    @endif
</div>