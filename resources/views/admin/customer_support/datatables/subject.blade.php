<a href="{{ route('admin.customer-support.show', $row->id) }}">
    <b class="text-black">{{ $row->subject }}</b>
</a>
<?php $content = strip_tags(br2nl(str_replace('style', 'style2', $row->message))) ?>
<a data-toggle="popover"
   title="{{ $row->subject }}"
   data-content="{!! $content !!}">
    <i class="fas fa-external-link-square-alt"></i>
</a>
<br/>
@if(@$row->customer_id)
    <i class="text-muted">{{ @$row->customer->name }}</i>
@else
    <i class="text-muted">{{ $row->name }} - {{ $row->email }}</i>
@endif
