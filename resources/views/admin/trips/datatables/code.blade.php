<div class="text-center">
    @if(@$row->type == 'R')
    <a href="{{ route('admin.trips.show', $row->id) }}">
        <div class="label bg-blue" style="padding: 1px 3px;
                margin: 4px -7px;
                position: absolute;">R</div>&nbsp;&nbsp;{{ substr($row->code, 1) }}
    </a>
  {{--  <div class="label label-success">
        <small><i class="fas fa-arrow-left"></i> Retorno</small>
    </div>--}}
    @else
    <a href="{{ route('admin.trips.show', $row->id) }}">
        {{ $row->code }}
    </a>
    @endif
</div>

{{--
@if(@$row->provider_id)
    <div>
        <span class="label" style="background: {{ @$row->provider->color }}">
            {{ @$row->provider->name }}
        </span>
    </div>
@endif--}}
