<?php

namespace App\Http\Controllers\Admin;

use App\Models\Allowance;
use App\Models\Role;
use App\Models\UserWorkgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Provider;
use App\Models\User;
use App\Models\Route;
use App\Models\Agency;
use App\Models\Trip\Trip;
use Response, DB, Auth, Setting;

class AllowancesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'allowances';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',allowances']);
        validateModule('allowances');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $month      = $request->has('month') ? $request->month : date('n');
        $year       = $request->has('year') ? $request->year : date('Y');
        $years      = yearsArr(2016, date('Y') + 1, true);

        if ($month == date('n') && date('d') <= '5') {
            $month = date("n", strtotime("previous month"));

            if (date('n') == '1') {
                $year = $year - 1;
            }
        }

        $months = trans('datetime.list-month');

        $agencies = Auth::user()->listsAgencies();

        $roles = Role::listRoles();

        $workgroups = UserWorkgroup::remember(config('cache.query_ttl'))
            ->cacheTags(UserWorkgroup::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'years',
            'months',
            'year',
            'month',
            'agencies',
            'workgroups',
            'roles'
        );

        return $this->setContent('admin.allowances.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $operatorId = $id;
        $year       = $request->has('year') ? $request->year : date('Y');
        $month      = $request->has('month') ? $request->month : date('n');

        $action = 'Detalhes Ajudas de Custo';
        $formOptions = ['route' => ['admin.allowances.update', $operatorId, 'month' => $month, 'year' => $year], 'method' => 'PUT'];

        $trips = Trip::filterSource()
            ->where('operator_id', $operatorId)
            ->whereBetween('start_date', [date("$year-$month-01"), date("$year-$month-31")])
            ->get();

        $allowance = Allowance::where('operator_id', $operatorId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $data = compact(
            'action',
            'formOptions',
            'trips',
            'allowance'
        );

        return view('admin.allowances.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $input = $request->all();
        $year       = $request->has('year') ? $request->year : date('Y');
        $month      = $request->has('month') ? $request->month : date('n');

        $totalAllowance = $totalWeekend = 0;
        foreach ($input['allowance'] as $key => $value) {
            $value        = !empty($value) ? $value : 0.0;
            $weekendValue = !empty($input['weekend'][$key]) ? $input['weekend'][$key] : 0.0;
            $trip = Trip::find($key);
            if (!$trip) {
                continue;
            }

            $trip->allowances_price = $value;
            $trip->weekend_price    = $weekendValue;
            $trip->save();

            $totalAllowance += $trip->allowances_price;
            $totalWeekend   += $trip->weekend_price;
        }

        // $allowance = Allowance::firstOrNew([
        //     'operator_id' => $id,
        //     'month' => $month,
        //     'year' => $year
        // ]);

        // $allowance->allowance_price     = $totalAllowance;
        // $allowance->weekend_price       = $totalWeekend;
        // $allowance->total_price         = ($totalAllowance + $totalWeekend);
        // $allowance->save();

        return Redirect::back()->with('success', 'Dados gravados com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id) {}

    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $currency = Setting::get('app_currency');
        $year     = $request->has('year') ? $request->year : date('Y');
        $month    = $request->has('month') ? $request->month : date('n');

        $usersWithTrips = Trip::filterSource()
            ->filterAgencies()
            ->whereBetween('start_date', [date("$year-$month-01"), date("$year-$month-31")])
            ->whereNotNull('operator_id')
            ->groupBy('operator_id')
            ->pluck('operator_id');

        $data = User::whereIn('id', $usersWithTrips)
            ->with(['trips' => function ($q) use ($month, $year) {
                $q->whereBetween('start_date', [date("$year-$month-01"), date("$year-$month-31")]);
            }]);

        return Datatables::of($data)
            ->add_column('period', function($row) use($year, $month) {
                return view('admin.allowances.datatables.period', compact('row', 'year', 'month'))->render();
            })
            ->edit_column('code', function($row) use($year, $month) {
                return view('admin.allowances.datatables.code', compact('row', 'year', 'month'))->render();
            })
            ->edit_column('name', function($row) use($year, $month) {
                return view('admin.allowances.datatables.name', compact('row', 'year', 'month'))->render();
            })
            ->add_column('trips', function($row){
                return view('admin.allowances.datatables.trips', compact('row'))->render();
            })
            ->add_column('shipments', function($row){
                return view('admin.allowances.datatables.shipments', compact('row'))->render();
            })
            ->edit_column('allowances_price', function($row) use($currency) {
                return view('admin.allowances.datatables.allowance_price', compact('row', 'currency'))->render();
            })
            ->edit_column('weekend_price', function($row) use($currency) {
                return view('admin.allowances.datatables.weekend_price', compact('row', 'currency'))->render();
            })
            ->edit_column('total_price', function($row) use($currency) {
                return view('admin.allowances.datatables.total_price', compact('row', 'currency'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) use($year, $month) {
                return view('admin.allowances.datatables.actions', compact('row', 'year', 'month'))->render();
            })
            ->make(true);
    }

}
