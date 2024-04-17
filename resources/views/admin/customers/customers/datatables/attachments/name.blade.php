<a href="{{ asset($row->filepath) }}" target="_blank">
    <?php $extension = File::extension($row->filename); ?>
    {!! extensionIcon($extension) . ' ' . $row->name  !!}
</a>