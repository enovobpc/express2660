<a href="{{ asset($row->filepath) }}" target="_blank">
    <?php $extension = File::extension($row->filename); ?>
    {!! extensionIcon($extension) . ' ' . $row->name  !!}
</a>
@if(!empty($row->locales))
    <div>
    @foreach($row->locales as $locale)
        <span class="m-r-2">
            <i class="flag-icon flag-icon-{{ $locale }}"></i>
        </span>
    @endforeach
    </div>
@endif