<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Agency;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Models\UserAbsenceType;
use Carbon\CarbonPeriod;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\UserAbsence;
use Html, Croppa, Response, File, Redirect, Date, Setting, Auth;

class AbsencesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users_absences']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($userId = null)
    {

        $action = 'Adicionar ausência';

        $absence = new UserAbsence();

        if (empty($userId)) {
            $formOptions = ['route' => ['admin.users.absences.store.global'], 'method' => 'POST'];
        } else {
            $formOptions = ['route' => ['admin.users.absences.store', $userId], 'method' => 'POST'];
        }

        $types = $this->listTypes(UserAbsenceType::where(function ($q) {
            $q->whereNull('source');
            $q->orWhere('source', config('app.source'));
        })
            ->get());

        if (empty($userId)) {
            $operators = User::filterSource()
                ->pluck('name', 'id')
                ->toArray();
        }

        $data = compact(
            'absence',
            'action',
            'formOptions',
            'types',
            'operators'
        );

        return view('admin.users.users.partials.absences.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId = null)
    {

        if ($request->has('user_id') && empty($userId)) {
            $userId = $request->get('user_id');
        }

        return $this->update($request, $userId, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($userId, $id)
    {

        $action = 'Editar Anexo';

        $absence = UserAbsence::whereHas('user', function ($q) {
            $q->filterSource();
            $q->filterAgencies();
        })
            ->where('user_id', $userId)
            ->where('id', $id)
            ->findOrfail($id);

        

        $formOptions = ['route' => ['admin.users.absences.update', $absence->user_id, $absence->id], 'method' => 'PUT'];

        $types = $this->listTypes(UserAbsenceType::where(function ($q) {
            $q->whereNull('source');
            $q->orWhere('source', config('app.source'));
        })
            ->get());

        /**absences accumalation*/
        if($absence->is_adjust == 1){

            $action = "Ajuste de Férias";
            
            $formOptions = ['route' => ['admin.users.absences.store.adjust', $userId, $absence->id], 'method' => 'POST'];
            
            return view('admin.users.users.partials.absences.adjust', compact('absence', 'action', 'formOptions', 'types'))->render();

        }


        return view('admin.users.users.partials.absences.edit', compact('absence', 'action', 'formOptions', 'types'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId, $id = null)
    {

        $input = $request->all();
        $input['calendar'] = $request->get('calendar', false);
        $input['user_id']  = $userId;

        $maxHoldays = Setting::get('rh_max_holidays');

        $maxHoldays = 99; //temporário

        $type = UserAbsenceType::find($input['type_id']);

        if ($input['period'] == 'days') {

            $startDate = new Date($input['start_date']);
            $period = CarbonPeriod::create($input['start_date'], $input['end_date']);

            $dates = [];
            foreach ($period as $date) {

                if (!in_array($date->dayOfWeek, [1, 2, 3, 4, 5])) {
                    unset($date);
                } else {
                    $dates[] = $date->format('Y-m-d');
                }
            }

            $duration = count($dates);

            //count all holidays
            $allHollidays = UserAbsence::where('user_id', $userId)
                ->whereRaw('YEAR(start_date) = "' . $startDate->year . '"')
                ->where('is_holiday', 1)
                ->get()
                ->groupBy('period');


            $holidays = [];
            foreach ($allHollidays as $period => $items) {
                if (in_array($period, ['days'])) {
                    $holidays['days'] = $items->sum('duration');
                } else {
                    $holidays['hours'] = $items->sum('duration');
                }
            }

            if ($type->is_holiday && ($duration + @$holidays['days']) > $maxHoldays) {
                return Redirect::back()->with('error', 'Excedeu os dias de férias permitidos.');
            }
        } elseif ($input['period'] == 'hours') {
            $duration = $input['duration'];
        } else {
            $input['period'] = 'days';
            $duration = 0.5;
        }

        $absence = UserAbsence::findOrNew($id);

        if ($absence->validate($input)) {
            $absence->fill($input);
            $absence->source     = config('app.source');
            $absence->duration   = $duration;
            $absence->is_holiday = $type->is_holiday ? 1 : 0;
            $absence->is_remunerated  = $type->is_remunerated ? 1 : 0;
            $absence->is_meal_subsidy = $type->is_meal_subsidy ? 1 : 0;
            $absence->save();


            if ($input['calendar']) {
                $calendar = CalendarEvent::firstOrNew([
                    'source_id'    => $absence->id,
                    'source_class' => 'UserAbsence'
                ]);

                $calendar->title        = $type->name . ' de ' . $absence->user->name;
                $calendar->created_by   = Auth::user()->id;
                $calendar->user_id      = $absence->user_id;
                $calendar->start        = $absence->start_date;
                $calendar->end          = $absence->end_date->endOfDay();
                $calendar->source_id    = $absence->id;
                $calendar->source_class = 'UserAbsence';
                $calendar->description  = $absence->obs;
                $calendar->color        = $type->color ? $type->color : '#ffcc00';
                $calendar->save();

                //get all users with permission to control human resources
                $agencies = Agency::filterSource()->pluck('id')->toArray();
                $recipients = \App\Models\User::where(function ($q) use ($agencies) {
                    $q->where(function ($q) use ($agencies) {
                        foreach ($agencies as $agency) {
                            $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                        }
                    });
                });


                if (config('app.source') != 'corridadotempo') { //para todas as plataformas, limita a partilhar as férias só com quem tenha recursos humanos. Na CDT ignora esta regra e mostra partilhado para todos
                    $recipients->whereHas('roles.perms', function ($query) {
                        $query->whereName('human_resources');
                        $query->orWhere('name', 'users_absences');
                    });
                }

                $recipients = $recipients->get(['id']);

                $calendar->participants()->sync($recipients);
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $absence->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $id)
    {

        $absence = UserAbsence::whereHas('user', function ($q) {
            $q->filterAgencies();
        })
            ->where('user_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        if (File::exists($absence->filepath)) {
            $result = File::delete($absence->filepath);
        } else {
            $result = true;
        }

        if ($result) {

            //destroy calendar event
            CalendarEvent::where('source_id', $absence->id)
                ->where('source_class', 'UserAbsence')
                ->delete();

            $result = $absence->delete();
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a ausência.');
        }

        return Redirect::back()->with('success', 'Ausência removida com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/features/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $result = false;

        $ids = explode(',', $request->ids);

        $absences = UserAbsence::whereHas('user', function ($q) {
            $q->filterSource();
            $q->filterAgencies();
        })
            ->whereIn('id', $ids)
            ->get();

        foreach ($absences as $absence) {
            $result = File::delete($absence->filepath);
            $absence->delete();
        }

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
    public function datatable(Request $request, $userId)
    {

        $data = UserAbsence::whereHas('user', function ($q) {
            $q->filterSource();
            $q->filterAgencies();
        })
            ->where('user_id', $userId)
            ->select();

        return Datatables::of($data)
            ->edit_column('type_id', function ($row) {
                return view('admin.users.users.datatables.absences.type', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.users.users.datatables.absences.status', compact('row'))->render();
            })
            ->edit_column('start_date', function ($row) {
                return view('admin.users.users.datatables.absences.start_date', compact('row'))->render();
            })
            ->edit_column('end_date', function ($row) {
                return view('admin.users.users.datatables.absences.end_date', compact('row'))->render();
            })
            ->edit_column('duration', function ($row) {
                return view('admin.users.users.datatables.absences.duration', compact('row'))->render();
            })
            ->edit_column('is_remunerated', function ($row) {
                return view('admin.users.users.datatables.absences.remunerated', compact('row'))->render();
            })
            ->edit_column('is_meal_subsidy', function ($row) {
                return view('admin.users.users.datatables.absences.meal_subsidy', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.users.users.datatables.absences.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Return list of providers with data attributes
     *
     * @param type $allProviders
     * @return type
     */
    public function listTypes($allTypes)
    {

        $types[] = ['value' => '', 'display' => ''];
        foreach ($allTypes as $type) {
            $types[] = [
                'value'     => $type->id,
                'display'   => $type->name,
                'periods'   => $type->periods,
            ];
        }

        return $types;
    }


    /** Ajust absences */
    public function adjustAbsence($user = null){

        $action = 'Ajuste de Férias';

        $absence = new UserAbsence();

         $types = $this->listTypes(UserAbsenceType::where(function ($q) {
            $q->whereNull('source');
            $q->orWhere('source', config('app.source'));
        })
        ->where('is_adjust', 1)
        ->get());

        $formOptions = ['route' => ['admin.users.absences.store.adjust', $user], 'method' => 'POST'];
        

        $data = compact(
            'absence',
            'action',
            'formOptions',
            'types'
        );

        return view('admin.users.users.partials.absences.adjust', $data)->render();
    }

    public function storeAdjust(Request $request, $user = null, $id = null){

        $input = $request->all();

        $type = UserAbsenceType::find($input['type_id']);

        $absence = UserAbsence::findOrNew($id);

        if($absence->validate($input)){


            $absence->fill($input);
            $absence->user_id         = $user;
            $absence->source          = config('app.source');
            $absence->is_holiday      = $type->is_holiday ? 1 : 0;
            $absence->is_remunerated  = $type->is_remunerated ? 1 : 0;
            $absence->is_meal_subsidy = $type->is_meal_subsidy ? 1 : 0;   
            $absence->is_adjust       = $type->is_adjust ? 1 : 0;                     
            $absence->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');

        }

        return Redirect::back()->withInput()->with('error', $absence->errors()->first());



    }
}
