@if(@$row->provider)
    <div class="text-center">
        <label class="label" style="background: {{ @$row->provider->color }}">
            {{ @$row->provider->name }}
        </label>
    </div>
@endif