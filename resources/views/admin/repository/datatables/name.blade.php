@if($row->is_folder)
    <a href="{{ route('admin.repository.index', ['folder' => $row->id]) }}" class="bold">
        {!! extensionIcon(strtolower($row->extension)) . ' ' . $row->name  !!}
    </a>
    <br/>
    <small>{{ number($row->count_folders, 0) }} @trans('Pastas') &bullet; {{ number($row->count_files, 0) }} @trans('Ficheiros')</small>
@else
<a href="{{ asset($row->filepath) }}" target="_blank">
    {!! extensionIcon(strtolower($row->extension)) . ' ' . $row->name  !!}
</a>
@endif