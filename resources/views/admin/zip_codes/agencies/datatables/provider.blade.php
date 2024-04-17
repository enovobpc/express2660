@if(@$row->provider)
    <label class="label" style="background: {{ @$row->provider->color }}">
        {{ @$row->provider->name }}
    </label>
@endif