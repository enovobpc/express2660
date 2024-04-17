<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\Module;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;
use App\Models\Role;
use App\Models\Permission;
use Cache, Auth;

class RolesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'roles';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',admin_roles']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $role = Role::filterSource()
            ->whereNotIn('id', [1,3])
            ->first();

        $this->show($role->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $role = new Role;
        $role->display_name = $request->input('display_name');
        $role->source       = config('app.source');

        if ($role->save()) {
            return Redirect::route('admin.roles.show', array($role->id))->with('success', 'Perfil criado com sucesso.');
        }
        
        return Redirect::route('admin.roles.index')->with('error', $role->errors()->first());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        
        $roles = Role::filterSource()
                    ->whereNotIn('id', [1,3,5])
                    ->orderBy('sort')
                    ->get();

        $role = Role::filterSource()
            ->whereNotIn('id', [1,3,5])
            ->findOrFail($id);

        $selectedRole = $role->load('perms');

        if(!empty($role->source)) {
            $activeModules = getActiveModules();
            $activeModules[] = 'base';

            if(Auth::user()->hasRole([config('permissions.role.admin')])) {
                $activeModules[] = 'admin';
            }

            $groupedPermissions = Permission::whereIn('module', $activeModules)
                ->orderBy('display_name')
                ->orderBy('subgroup')
                ->get();

        } else {
            $groupedPermissions = Permission::orderBy('display_name')->orderBy('subgroup')->get();
        }

        $groupedPermissions = $groupedPermissions->groupBy('group');


        $groups = Permission::orderBy('group_sort', 'asc')
            ->orderBy('group')
            ->orderBy('display_name')
            ->groupBy('group')
            ->pluck('group', 'group')
            ->toArray();

        $arr = [];
        foreach($groups as $group) {

            if(@$groupedPermissions[$group]) {
                $subgroups = $groupedPermissions[$group]
                            ->sortBy('subgroup')
                            ->sortBy('subgroup_sort')
                            ->sortBy('display_name')
                            ->groupBy('subgroup');

                $arr[$group] = $subgroups;
            }
        }
        
        $groupedPermissions = $arr;

        return $this->setContent('admin.roles.show', compact('roles', 'groupedPermissions', 'selectedRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Cache::flush();

        $role = Role::filterSource()->findOrFail($id);

        //block admin role permissions change
        if ($role->name == Config::get('permissions.role.admin')) {
            return Redirect::back()->with('error', 'Não é possivel editar as permissões do perfil de ' . $role->display_name);
        }

        $permission = $request->input('permission');

        $permission = is_null($permission) ? array() : $permission;

        $role->perms()->sync($permission);

        return Redirect::back()->with('success', 'Alterações guardadas com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Cache::flush();

        $deleted = Role::filterSource()
                ->where('is_static', 0)
                ->where('id', '>', 1)
                ->where('id', $id)
                ->delete();

        if (!$deleted) {
            return Redirect::route('admin.roles.index')->with('error', 'Ocorreu um erro ao tentar remover o perfil.');
        }

        return Redirect::route('admin.roles.index')->with('success', 'Perfil removido com sucesso.');
    }

}
