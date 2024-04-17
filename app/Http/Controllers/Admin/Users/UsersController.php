<?php

namespace App\Http\Controllers\Admin\Users;

use Auth, App, Session, Html, Croppa, File, Hash, Cache, Response, Date, Mail, DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Provider;
use App\Models\Agency;
use App\Models\Core\Setting;
use App\Models\Role;
use App\Models\User;
use App\Models\UserWorkgroup;
use App\Models\UserContract;
use App\Models\UserCard;
use App\Models\PurchaseInvoiceType;

class UsersController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    public $sidebarActiveOption = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::listRoles();

        $agencies = Auth::user()->listsAgencies();

        $workgroups = UserWorkgroup::remember(config('cache.query_ttl'))
            ->cacheTags(UserWorkgroup::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('admin.users.users.index', compact('roles', 'agencies', 'workgroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $user = new User;
        $user->code  = $user->setCode(false);
        //$user->email = strtolower($user->code.'.'.config('app.source').'@'.$request->server ("HTTP_HOST"));

        $action    = 'Adicionar Colaborador';

        $roles = Role::listRoles();
        $rolePermissions = Role::with('perms')->get(['id'])->toArray();

        $assignedRoles = [];

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        $agenciesList = $agencies->pluck('name', 'id')->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.users.store', 'source' => 'users'), 'files' => true);

        $loginFormOptions = ['route' => array('admin.users.store.login', 'source' => 'users'), 'method' => 'PUT', 'files' => true];

        $workgroups = UserWorkgroup::remember(config('cache.query_ttl'))
            ->cacheTags(UserWorkgroup::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $selectedWorkgroups = $user->workgroups()->pluck('id')->toArray();

        $allCards = UserCard::filterSource()
            ->where('user_id', $user->id)
            ->get();

        $customCards = $allCards->filter(function ($item) {
            return !in_array($item->type, array_keys(trans('admin/users.default-cards')));
        });

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $attendanceTypes = trans('admin/fleet.usages-logs.types');


        $data = compact(
            'action',
            'formOptions',
            'loginFormOptions',
            'user',
            'password',
            'roles',
            'rolePermissions',
            'assignedRoles',
            'agencies',
            'providers',
            'workgroups',
            'selectedWorkgroups',
            'allCards',
            'customCards',
            'operatorsList',
            'agenciesList',
            'attendanceTypes'
        );

        return $this->setContent('admin.users.users.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $action = 'Editar Colaborador';

        $user = User::findOrfail($id);

        $contracts = UserContract::remember(config('cache.query_ttl'))
            ->cacheTags(UserContract::CACHE_TAG)
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        $user->contracts = $contracts;

        if ((!Auth::user()->hasRole([config('permissions.role.admin')]) && $user->hasRole([config('permissions.role.admin')]))
            || (!Auth::user()->hasRole([config('permissions.role.admin')]) && !empty($user->agencies) && !$user->hasAgency(Auth::user()->agencies))
        ) {
            App::abort(403);
        }

        $roles = Role::listRoles();

        $rolePermissions = Role::with('perms')->get(['id'])->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $attendanceTypes = trans('admin/fleet.usages-logs.types');

        $assignedRoles = $user->roles()
            ->pluck('role_id')
            ->toArray();

        $assignedRoles = array_map('intval', $assignedRoles);

        $formOptions = ['route' => array('admin.users.update', $user->id), 'method' => 'PUT', 'files' => true];

        $loginFormOptions = ['route' => array('admin.users.update.login', $user->id, 'source' => 'users'), 'method' => 'PUT', 'files' => true];

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $agenciesList = $agencies->pluck('name', 'id')->toArray();

        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $workgroups = UserWorkgroup::remember(config('cache.query_ttl'))
            ->cacheTags(UserWorkgroup::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $expensesTypes = PurchaseInvoiceType::remember(config('cache.query_ttl'))
            ->cacheTags(PurchaseInvoiceType::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $selectedWorkgroups = $user->workgroups()->pluck('id')->toArray();

        $allCards = UserCard::filterSource()
            ->where('user_id', $user->id)
            ->get();

        $customCards = $allCards->filter(function ($item) {
            return !in_array($item->type, array_keys(trans('admin/users.default-cards')));
        });


        //count and store on customer
        $now = new Date();
        $lastYear = date("Y", strtotime("-1 year"));

        //absences adjusted
        $allAdjustments = App\Models\UserAbsence::where('user_id', $user->id)
            ->whereRaw('YEAR(created_at) = "' . $now->year . '"')
            ->get();


        $allAbsences = App\Models\UserAbsence::where('user_id', $user->id)
            ->whereRaw('YEAR(start_date) = "' . $now->year . '"')
            ->get();

        $lastAbsences = App\Models\UserAbsence::where('user_id', $user->id)
            ->whereRaw('YEAR(start_date) = "' . $lastYear . '"')
            ->get();

        $nextYearAllAbsences = App\Models\UserAbsence::where('user_id', $user->id)
            ->whereRaw('YEAR(start_date) = "' . $now->addYear()->year . '"')
            ->get();

        $holidaysData = $allAbsences->filter(function ($item) {
            return $item->is_holiday == 1;
        })->groupBy('period');

        $holidays = [];
        foreach ($holidaysData as $period => $items) {
            $duration = $items->sum('duration');
            if (in_array($period, ['days'])) {
                $holidays['days'] = $duration;
            } else {
                $holidays['hours'] = $duration;
            }
        }

        $nextYearHolidaysData = $nextYearAllAbsences->filter(function ($item) {
            return $item->is_holiday == 1;
        })->groupBy('period');

        $holidaysNextYear = [];
        foreach ($nextYearHolidaysData as $period => $items) {
            $duration = $items->sum('duration');
            if (in_array($period, ['days'])) {
                $holidaysNextYear['days'] = $duration;
            } else {
                $holidaysNextYear['hours'] = $duration;
            }
        }

        $lastYearHolidaysData = $lastAbsences->filter(function ($item) {
            return $item->is_holiday == 1;
        })->groupBy('period');

        $holidaysLastYear = [];
        foreach ($lastYearHolidaysData as $period => $items) {
            $duration = $items->sum('duration');
            if (in_array($period, ['days'])) {
                $holidaysLastYear['days'] = $duration;
            } else {
                $holidaysLastYear['hours'] = $duration;
            }
        }

        $absencesData = $allAbsences->filter(function ($item) {
            return $item->is_holiday == 0;
        })->groupBy('period');


        $absencesData = $allAbsences->filter(function ($item) {
            return $item->is_holiday == 0;
        })->groupBy('period');

        $absences = [];
        foreach ($absencesData as $period => $items) {
            $duration = $items->sum('duration');
            if ($period == 'days') {
                $absences['days'] = $duration;
            } else {
                $absences['hours'] = $duration;
            }
        }

        //absences adjusted
        $adjustments = $allAdjustments->filter(function ($item) {
            return ($item->is_adjust == 1 && $item->is_holiday == 1);
        })->groupBy('period');


        $absenceaAjusted = [];
        foreach ($adjustments as $period => $item) {
            $duration = $item->sum('duration');
            if ($period == 'days') {
                $absenceaAjusted['days']  = $duration;
            } else {
                $absenceaAjusted['hours'] = $duration;
            }
        }

        $data = compact(
            'user',
            'action',
            'formOptions',
            'loginFormOptions',
            'roles',
            'rolePermissions',
            'assignedRoles',
            'agencies',
            'providers',
            'workgroups',
            'operatorsList',
            'customCards',
            'allCards',
            'selectedWorkgroups',
            'holidays',
            'absences',
            'holidaysNextYear',
            'operators',
            'expensesTypes',
            'agenciesList',
            'holidaysLastYear',
            'absenceaAjusted',
            'attendanceTypes'
        );

        return $this->setContent('admin.users.users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        User::flushCache(User::CACHE_TAG);
        UserCard::flushCache(UserCard::CACHE_TAG);

        $input = $request->all();
        $input['active'] = $request->get('active', false);
        $roleId = $request->get('role_id');

        if ($roleId == 3) {
            $input['login_app']   = 1;
            $input['login_admin'] = 0;
            $input['is_operator'] = 1;
        } else {
            $input['login_app']   = 1;
            $input['login_admin'] = 1;
            $input['is_operator'] = 0;
        }

        if (!is_numeric(@$input['holidays_days'])) {
            $input['holidays_days'] = "";
        }

        if (Auth::user()->id == $id) {
            unset($input['active']);
        }

        $workgroups = $request->get('workgroup', []);

        $user  = User::findOrNew($id);

        $validator = Validator::make($input, []);

        if ($validator->passes()) {
            $user->fill($input);

            if ($user->holidays_days == "") {
                $user->holidays_days = 22;
            }

            if (empty($user->name) && !empty($user->fullname)) {
                $user->name = split_name($user->fullname);
            }

            if (empty($user->name)) {
                $user->name = $user->fullname;
            }

            if (!empty($user->resignation_date)) {
                $user->active = 0;
            }

            if (empty($user->agencies)) {
                $user->agencies = [$request->get('agency_id')];
            }

            $user->source = config('app.source');
            $user->workgroups_arr = $workgroups;

            //delete image
            if (@$input['delete_photo'] && !empty($user->filepath)) {
                if (File::exists(public_path($user->filepath))) {
                    Croppa::delete($user->filepath);
                }

                $user->filepath = null;
                $user->filename = null;
                $user->filehost = null;
            }

            //upload image
            if ($request->hasFile('image')) {

                if ($user->exists && !empty($user->filepath) && File::exists(public_path() . '/' . $user->filepath)) {
                    Croppa::delete($user->filepath);
                }

                if (!$user->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem do perfil.');
                }
            } else {
                $user->save();
            }

            //store cards
            if (!empty($user->id_card)) {
                $this->saveCard($user, 'cc', $user->id_card);
            }

            if (!empty($user->ss_card)) {
                $this->saveCard($user, 'ss', $user->ss_card);
            }

            //save workgroups
            $user->workgroups()->sync($workgroups);

            if ($request->has('role_id')) {
                $roleId = $request->get('role_id');
                $roles = [$roleId];
                $user->roles()->sync($roles);
            }

            return Redirect::route('admin.users.edit', $user->id)->with('success', 'Alterações gravadas com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $validator->errors()->first());
    }

    /**
     * Store the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeLogin(Request $request)
    {
        return $this->updateLogin($request, null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateLogin(Request $request, $id)
    {

        User::flushCache(User::CACHE_TAG);

        $user  = User::findOrNew($id);

        $input = $request->except('role_id');

        $input['active']            = $request->get('active', false);
        $input['login_app']         = $request->get('login_app', false);
        $input['login_admin']       = $request->get('login_admin', false);
        $input['allowed_actions']   = $request->get('allowed_actions', false);
        $input['allowed_actions']   = $request->get('allowed_actions', false);
        $input['location_enabled']  = $request->get('location_enabled', false);
        $input['email']             = trim(strtolower(@$input['email']));
        $input['is_operator']       = false;

        if (Auth::user()->id == $id) {
            unset(
                $input['active'],
                $input['is_operator'],
                $input['login_app'],
                $input['login_admin'],
                $input['allowed_actions']
            );
        }

        $changePass = false;
        $checkEmail = true;
        $isOperator = false;

        $feedback = 'Dados gravados com sucesso.';
        $rules = [];
        if ($user->exists && empty($input['password'])) {
            $rules['name']  = 'required';
            $checkEmail = true;
        } elseif ($user->exists && $user->password) {
            $changePass = true;
            $checkEmail = false;
            $feedback = 'Palavra-passe alterada com sucesso.';
            $rules['password'] = 'confirmed';
        } elseif (!$user->exists) {
            $checkEmail = true;
            $rules['name']  = 'required';
        }


        if ($checkEmail) {
            $userEmail = User::where('email', $input['email'])
                ->where('source', config('app.source'));

            if ($user->exists) {
                $userEmail = $userEmail->where('id', '<>', $user->id);
            }

            $userEmail = $userEmail->first();

            if ($userEmail) {
                return Redirect::back()->withInput()->with('error', 'Já existe outro utilizador com o e-mail ' . $input['email']);
            }
        }


        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {

            if (empty($input['password'])) {
                unset($input['password']);
            } else {
                $input['uncrypted_password'] = $input['password'];
                $input['password'] = bcrypt($input['password']);
            }

            $input['source'] = config('app.source');
            if ($request->get('role_id') == 1 && Auth::user()->isAdmin()) {
                $input['source']      = null;
                $input['agencies']    = null;
                $input['login_admin'] = 1;
            } elseif ($request->get('role_id') == 3) { //motorista
                $isOperator           = true;
                $input['source']      = config('app.source');
                $input['is_operator'] = 1;
                $input['login_admin'] = 0;
            }

            $user->fill($input);
            $user->fullname = $user->fullname ? $user->fullname : $user->name;

            //delete image
            if (@$input['delete_photo'] && !empty($user->filepath)) {
                Croppa::delete($user->filepath);
                $user->filepath = null;
                $user->filename = null;
                $user->filehost = null;
            }

            //upload image
            if ($request->hasFile('image')) {

                if ($user->exists && !empty($user->filepath) && File::exists(public_path() . '/' . $user->filepath)) {
                    Croppa::delete($user->filepath);
                }

                if (!$user->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem do perfil.');
                }
            } else {
                $user->save();
            }

            if (!$changePass && $user->id != Auth::user()->id) {
                if ($isOperator) {
                    $roleId = Role::whereName(config('permissions.role.operator'))->first()->id;
                    $roles = [$roleId];
                } else {
                    //$roles = $request->has('role_id') ? $request->get('role_id') : [];
                    $roles = $request->has('role_id') ? [$request->get('role_id')] : [];
                }
                $user->roles()->sync($roles);
            }

            //send email with password
            if (@$input['send_email']) {

                $input['password'] = @$input['uncrypted_password'];

                Mail::send('emails.users.password', compact('input', 'user', 'isOperator'), function ($message) use ($input, $user, $isOperator) {
                    $message->to($user->email)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject($isOperator ? 'Dados acesso à Aplicação Móvel' : 'Dados de acesso à Área de Gestão');
                });
            }

            return Redirect::back()->with('success', $feedback);
        }

        return Redirect::back()->withInput()->with('error', $validator->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        User::flushCache(User::CACHE_TAG);

        $user = User::findOrFail($id);

        if ((!Auth::user()->hasRole([config('permissions.role.admin')]) && $user->hasRole([config('permissions.role.admin')]))
            || (!Auth::user()->hasRole([config('permissions.role.admin')]) && !$user->hasAgency(Auth::user()->agencies))
        ) {
            App::abort(403);
        }

        $user->email = '_' . time() . '_' . $user->email;
        $user->save();

        $result = $user->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o utilizador.');
        }

        return Redirect::back()->with('success', 'Utilizador removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        User::flushCache(User::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = User::whereIn('id', $ids)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $agencies = Agency::get(['id', 'code', 'name', 'color', 'source']);

        $myAgencies = $agencies->filter(function ($item) {
            return $item->source == config('app.source');
        })->groupBy('id')->toArray();

        $agencies = $agencies->groupBy('id')
            ->toArray();

        $user = Auth::user();

        $data = User::with('roles', 'agency')
            //->filterAgencies(array_keys($myAgencies), true)
            ->where(function ($q) {
                $q->where('source', config('app.source'));
                $q->orWhereNull('source');
            })
            ->where(function ($q) {
                $q->has('roles', '=', 0);
                $q->orWhereHas('roles', function ($q) {
                    $q->where('role_id', '<>', 1);
                });
            })
            ->select();

        //força utilizador a ver só das suas agencias. Nota: não usar filterAgencies pois faz conflito
        if (!empty($myAgencies)) {
            $data = $data->where(function ($q) use ($myAgencies) {
                foreach ($myAgencies as $agencyId => $agency) {
                    $q = $q->orWhere('agencies', 'like', '%"' . $agencyId . '"%');
                }
            });
        }

        //filter role
        if ($request->role) {
            $data = $data->whereHas('roles', function ($q) use ($request) {
                $q->where('role_id', $request->role);
            });
        }

        //filter workgroups
        if ($request->workgroup) {
            $data = $data->whereHas('workgroups', function ($q) use ($request) {
                $q->whereIn('workgroup_id', $request->workgroup);
            });
        }

        //filter login admin
        $value = $request->login_admin;
        if ($request->has('login_admin')) {
            $data = $data->where('login_admin', $value);
        }

        //filter is operator
        $value = $request->login_app;
        if ($request->has('login_app')) {
            $data = $data->where('login_app', $value);
        }

        //filter active
        $value = $request->active;
        if ($request->has('active')) {
            $data = $data->where('active', $value);
        }

        //filter login
        $value = $request->login;
        if ($request->has('login')) {
            if ($value) {
                $data = $data->whereNotNull('password');
            } else {
                $data = $data->whereNull('password');
            }
        }

        //filter agency
        $value = $request->get('agency');
        if ($request->has('agency')) {
            $data = $data->where('agencies', 'like', '%"' . $value . '"%');
        }

        $data = $data->select([
            'users.*',
            DB::raw("CAST(REPLACE(REPLACE(REPLACE(code, 'M', '-13'), 'S', '-19'), 'A', '-1') AS SIGNED) as unsigned_code"), //replace M pelo numero no alfabeto. Valor negativo para que a ordenação fique no final
            //DB::raw('CAST(code as UNSIGNED) as unsigned_code'),
        ]);

        return Datatables::of($data)
            ->add_column('photo', function ($row) {
                $row->avatar = 1;
                return view('admin.partials.datatables.photo', compact('row'))->render();
            })
            ->edit_column('unsigned_code', function ($row) use ($request) {
                return view('admin.users.users.datatables.code', compact('row'))->render();
            })
            ->edit_column('name', function ($row) use ($request) {
                return view('admin.users.users.datatables.name', compact('row'))->render();
            })
            ->edit_column('vat', function ($row) use ($request) {
                return view('admin.users.users.datatables.vat', compact('row'))->render();
            })
            ->edit_column('email', function ($row) use ($request) {
                return view('admin.users.users.datatables.email', compact('row'))->render();
            })
            /*->edit_column('agencies', function($row) use($agencies) {
                return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
            })*/
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('workgroup', function ($row) {
                return view('admin.users.users.datatables.workgroups', compact('row'))->render();
            })
            ->add_column('roles', function ($row) {
                return view('admin.users.users.datatables.roles', compact('row'))->render();
            })
            ->add_column('login_app', function ($row) {
                return view('admin.users.users.datatables.login_app', compact('row'))->render();
            })
            ->add_column('login_admin', function ($row) {
                return view('admin.users.users.datatables.login_admin', compact('row'))->render();
            })
            ->add_column('active', function ($row) {
                return view('admin.users.users.datatables.active', compact('row'))->render();
            })
            /*->add_column('last_login', function($row) {
                return view('admin.users.users.datatables.last_login', compact('row'))->render();
            })*/
            ->edit_column('location_last_update', function ($row) {
                return view('admin.users.users.datatables.location', compact('row'))->render();
            })
            ->add_column('actions', function ($row) use ($request) {
                $row->is_operator = $request->source == 'operators' ? true : false;
                return view('admin.users.users.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Start a remote login 
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remoteLogin(Request $request, $customerId)
    {

        $currentUser = Auth::user();

        $user = User::findOrFail($customerId);

        Session::set('source_user_id', $currentUser->id);

        Auth::login($user);

        return Redirect::route('admin.dashboard')->with('success', 'Sessão iniciada com sucesso.');
    }

    /**
     * Save or update card info
     *
     * @param $input
     * @param $user
     * @param $cardType
     */
    public function saveCard($user, $cardType, $cardNo)
    {

        $card = UserCard::firstOrNew([
            'user_id' => $user->id,
            'type'    => $cardType,
            'source'  => config('app.source')
        ]);

        $card->source  = config('app.source');
        $card->type    = $cardType;
        $card->card_no = $cardNo;
        $card->save();
    }
}
