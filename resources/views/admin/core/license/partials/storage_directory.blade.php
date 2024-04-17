<table class="table table-condensed m-b-5">
    <?php $totalCount = $totalSize = 0; ?>
    @foreach($storageDirectories as $foldername => $file)
        <?php
        $totalCount+= $file['count'];
        $totalSize+= $file['size'];
        ?>
        <tr>
            <td>
                <a href="{{ route('admin.core.license.directory.show', ['directory' => $file['filepath'], 'storage' => 1]) }}" data-toggle="modal" data-target="#modal-remote-xlg">
                    {{ ucfirst($foldername) }}
                </a>
            </td>
            <td class="w-100px text-right" style="color: {{ $file['count'] ? '' : '#ccc' }}">{{ $file['count'] }} ficheiros</td>
            <td class="w-80px text-right" style="color: {{ $file['size'] ? '' : '#ccc' }}">{{ human_filesize($file['size']) }}</td>
        </tr>
    @endforeach
    <tr>
        <th>Total</th>
        <th class="text-right">{{ $totalCount }} ficheiros</th>
        <th class="text-right">{{ human_filesize($totalSize) }}</th>
    </tr>
</table>