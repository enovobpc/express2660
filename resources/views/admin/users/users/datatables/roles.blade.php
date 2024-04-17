<?php
/*$count = $row->roles->count();
$html  = '';
foreach($row->roles as $key => $role) {
    $html.= $role->display_name .'<br/>';
}*/
?>
{{--
@if($count > 2)
    {{ $row->roles->first()->display_name }}
    <br/>
    <small class="text-muted cursor-pointer"
        data-toggle="popover"
        data-title="Perfís do utilizador"
        data-html="true"
        data-content="{!! $html !!}">
        +{{ $count - 2 }} perfis <i class="fas fa-external-link-square-alt"></i>
    </small>
@else
    @if($row->roles->isEmpty())
        <i class="text-muted">Sem Login</i>
    @else
        @foreach($row->roles as $key => $role)
            {{ $role->display_name }}<br/>
        @endforeach
    @endif
@endif
--}}
<div class="text-center">
    @if(@$row->roles->first()->id == 2)
        <span class="label bg-red"><i class="fas fa-star"></i> @trans('Gerência')</span>
    @elseif(@$row->roles->first()->id == 4)
        <span class="label bg-orange"><i class="fas fa-user-edit"></i> @trans('Administrativo')</span>
    @elseif(@$row->roles->first()->id == 4)
        <span class="label bg-yellow"><i class="fas fa-store-alt"></i> @trans('Balcão')</span>
    @elseif(@$row->roles->first()->id == 3)
        <span class="label bg-blue"><i class="fas fa-truck"></i> @trans('Motorista')</span>
    @elseif(@$row->roles->first()->id == 6)
        <span class="label bg-purple"><i class="fas fa-suitcase"></i> @trans('Comercial')</span>
    @else
        <span class="label bg-gray">{{ @$row->roles->first()->display_name }}</span>
    @endif
</div>