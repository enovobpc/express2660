<a href="{{ route('admin.budgets.show', $row->id) }}">
    <b class="text-black">{{ $row->subject }}</b>
</a>
<?php $content = str_replace('style', 'style2', $row->message) ?>
<a data-toggle="popover"
   title="{{ $row->subject }}"
   data-content="{{ $content }}">
    <i class="fas fa-external-link-square-alt"></i>
</a>
<br/>
<i class="text-muted">{{ $row->name }} - {{ $row->email }}</i>
