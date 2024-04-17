@if($row->provider_id)
<div class="text-center">
    <span class="label" style="background: {{ @$row->provider->color }}">
        {{ @$row->provider->name }}
    </span>
</div>
@endif