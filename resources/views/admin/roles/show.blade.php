@section('title')
Perfis e permissões
@stop

@section('content-header')
Perfis e permissões
@stop

@section('breadcrumb')
<li class="active">
    Administração
</li>
<li class="active">
    Perfis e permissões
</li>
@stop

@section('styles')

<style type="text/css">

    .nav-stacked>li:hover .options {
        display: block;
    }

    .nav-stacked>li .options {
        position: absolute;
        right: 5px;
        top: 10px;
        display: none;
    }

</style>  
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="box no-border">
            <div class="box-header with-border">
                <h3 class="box-title">Perfis personalizados</h3>
                <div class="box-tools pull-right">
                    <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-role"><i class="fas fa-plus"></i> Novo</a>
                </div>
            </div>
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked no-border">
                <?php
                    $rolesDinamic = $roles->filter(function($item) {
                        return $item->is_static == 0;
                    });
                ?>
                @foreach ($rolesDinamic as $role)
                    @if($role->id === $selectedRole->id)
                    <li class="active">
                    @else
                    <li>
                    @endif    
                        <a href="{{ route('admin.roles.show', $role->id)}}">
                            @if($role->is_static)
                                <i class="fas fa-fw fa-lock"></i>
                            @else
                                <i class="fas fa-fw fa-users"></i>
                            @endif
                                {{ $role->display_name }}
                        </a>
                        @if($role->name != Config::get('permissions.role.admin'))
                        <div class="options">
                            @if(!$role->is_static)
                            <a href="{{ route('admin.roles.destroy', $role->id) }}" data-method="delete" data-confirm="Tem a certeza que prentende remover o perfil <b>{{ $role->display_name }}</b>?<br>Todos os utilizadores com o perfil vão deixar de ter as permissões do perfil." class="btn btn-xs btn-danger">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            @endif
                        </div>
                        @endif
                    </li>
                @endforeach
                </ul>
            </div>
        </div>

        <div class="box no-border">
            <div class="box-header with-border">
                <h3 class="box-title">Perfis nativos do sistema</h3>
            </div>
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked no-border">
                    <?php
                    $rolesStatic = $roles->filter(function($item) {
                        return $item->is_static == 1;
                    });
                    ?>
                    @foreach ($rolesStatic as $role)
                    @if($role->id === $selectedRole->id)
                        <li class="active">
                    @else
                        <li>
                            @endif
                            <a href="{{ route('admin.roles.show', $role->id)}}">
                                {{-- @if($role->is_static)
                                    <i class="fas fa-fw fa-lock"></i>
                                @else
                                    <i class="fas fa-fw fa-group"></i>
                                @endif --}}
                                <i class="fas fa-fw fa-users"></i>
                                {{ $role->display_name }}
                            </a>
                            @if($role->name != Config::get('permissions.role.admin'))
                                <div class="options">
                                    @if(!$role->is_static)
                                        <a href="{{ route('admin.roles.destroy', $role->id) }}" data-method="delete" data-confirm="Tem a certeza que prentende remover o perfil <b>{{ $role->display_name }}</b>?<br>Todos os utilizadores com o perfil vão deixar de ter as permissões do perfil." class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash-alt-o"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </li>
                        @endforeach
                </ul>
            </div>

        </div>
    </div>
    <div class="col-md-9">
        <div class="box no-border">
            <div class="box-header with-border">
                @if(Auth::user()->hasRole(config('permissions.role.admin')))
                <button class="btn btn-xs btn-default btn-names pull-right m-l-5"><i class="fas fa-eye"></i> Ver códigos permissões</button>
                @endif

                <button class="btn btn-xs bg-blue btn-select-all pull-right"><i class="fas fa-check-square"></i> Ativar/Desativar todos</button>
                <h3 class="box-title text-uppercase p-b-15"><strong>Permissões para {{ $selectedRole->display_name }}</strong></h3>
                <div class="form-group m-b-0">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fas fa-search"></i></div>
                        {{ Form::text('search_perm', null, ['class' => 'form-control', 'placeholder' => 'Procurar na lista...'])}}
                    </div>
                </div>
                
               {{--  @if(!Auth::user()->hasRole(config('permissions.role.admin')) && $selectedRole->is_static)
                    <h4 class="text-yellow">
                        Este perfíl é nativo do sistema. Não é possível editar as suas permissões.
                    </h4>
                @endif --}}
            </div>
            {{ Form::open(array('route' => array('admin.roles.update', $selectedRole->id), 'method' => 'PUT', 'disabled' => true)) }}
            <div class="box-body">
                @foreach($groupedPermissions as $group => $subgroups)
                <h4 class="perm-group">{{ $group }}</h4>
                <div class="perm-group-container">
                    @foreach($subgroups as $subgroup => $permissions)
                        <h5 class="perm-subgroup">{{ $subgroup ? $subgroup : 'Geral' }}</h4>
                        <div class="perm-subgroup-container">
                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-xs-12 col-sm-4 col-lg-3 check-perm" data-search="{{ $permission->display_name }} {{ $permission->group }} {{ $permission->description }}">
                                        <div class="form-group m-b-5">
                                            <div class="checkbox icheck m-0" >
                                                <label>
                                                    {{-- @if(!Auth::user()->hasRole(config('permissions.role.admin')) && $selectedRole->is_static)
                                                        @if ($selectedRole->perms->contains($permission->id))
                                                            <input type="checkbox" name="permission[]" value="{{ $permission->id }}"  checked disabled> {{ $permission->display_name }}
                                                        @else
                                                            <input type="checkbox" name="permission[]" value="{{ $permission->id }}" disabled> {{ $permission->display_name }}
                                                        @endif
                                                    @else --}}
                                                        @if ($selectedRole->perms->contains($permission->id))
                                                            <input type="checkbox" name="permission[]" value="{{ $permission->id }}"  checked > {{ $permission->display_name }}
                                                        @else
                                                            <input type="checkbox" name="permission[]" value="{{ $permission->id }}" > {{ $permission->display_name }}
                                                        @endif
                                                {{--  @endif --}}
                                                @if(Auth::user()->hasRole(config('permissions.role.admin')))
                                                    <div style="padding-left: 23px; display:none" class="perm-id">
                                                        <small class="text-muted italic">[{{ $permission->id }}] {{ $permission->name }}</small>
                                                    </div>
                                                @endif
                                                </label>
                                                @if($permission->description)
                                                <small><i class="fas fa-info-circle"  data-toggle="tooltip" title="{{ $permission->description }}"></i></small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @endforeach
            </div>
            <div class="box-footer">
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary')) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<style>
    .perm-group {
        background: #1d2937;
        color: #fff;
        font-weight: bold;
        text-transform: uppercase;
        padding: 10px 15px;
        margin: -10px -10px 0;
    }

    .perm-group-container {
        padding: 0 10px;
        margin-bottom: 30px;
    }
    .perm-subgroup {
        padding: 6px 5px;
        margin: 7px 0 -1px;
        background: #d3d3d3;
        font-weight: bold;
        text-transform: uppercase;
        border: 1px solid #ddd;
        border-radius: 3px 3px 0 0;
    }

    .perm-subgroup-container {
        border: 1px solid #ddd;
        border-radius: 0 0 3px 3px;
        padding: 10px;
        margin-bottom: 10px;
    }
</style>
@stop

@section('scripts')
<script>
    $("[name='search_perm']").on("keyup", function() {
  
        var filtro = $(this).val().toLowerCase();

        $(".check-perm").each(function() {
            var textoItem = $(this).data('search').toLowerCase();

            if (textoItem.indexOf(filtro) !== -1) {
                $(this).closest('div').show();
            } else {
                $(this).closest('div').hide();
            }
            
        });

        
        $(".perm-subgroup-container, .perm-subgroup, .perm-group, .perm-group-container").show()
        $(".perm-subgroup-container").each(function() {
            if(!$(this).find('.check-perm:visible').length) {
                $(this).closest('.perm-subgroup-container').hide();
                $(this).prev().hide();
            }
        });
    

        $(".perm-group-container, .perm-group").show();
        $(".perm-group-container").each(function() {
            if(!$(this).find('.perm-subgroup:visible').length) {
                $(this).hide();
                $(this).prev().hide();
            }
        });

    });

    $('.btn-select-all').on('click', function(){
        if($('.perm-group-container [type="checkbox"]:checked').length) {
            $('.perm-group-container [type="checkbox"]').prop('checked', false);
        } else {
            $('.perm-group-container [type="checkbox"]').prop('checked', true);
        }
    })

    $('.btn-names').on('click', function(){
        $('.perm-id').toggle();
    })
</script>
@stop

@section('modals')
    @include('admin.roles.modal_edit')
@stop