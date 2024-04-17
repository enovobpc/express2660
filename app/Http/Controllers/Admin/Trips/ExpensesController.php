<?php

namespace App\Http\Controllers\Admin\Trips;

use App\Models\Trip\Trip;
use App\Models\Trip\TripExpense;
use App\Models\User;
use Response, Redirect;
use Illuminate\Http\Request;

class ExpensesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'delivery_management';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',delivery_management']);
    }

    /**
     * Display create form
     * 
     * @param Request $request 
     * @param int $tripId 
     * @return string
     */
    public function create(Request $request, int $tripId) {

        $expense = new TripExpense();
        $action = 'Adicionar Despesa';
        $formOptions = ['route' => ['admin.trips.expenses.store', $tripId], 'method' => 'POST', 'data-toggle' => 'ajax-form'];

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(true)
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        return view('admin.trips.modals.expenses.edit', compact('expense', 'action', 'formOptions', 'operators'))->render();
    }

    /**
     * Display edit form
     * 
     * @param Request $request 
     * @param int $tripId 
     * @param int $id 
     * @return string
     */
    public function edit(Request $request, int $tripId, int $id) {

        $expense = TripExpense::findOrNew($id);
        $action = 'Editar Despesa';
        $formOptions = ['route' => ['admin.trips.expenses.update', $tripId, $id], 'method' => 'PUT', 'data-toggle' => 'ajax-form'];

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(true)
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        return view('admin.trips.modals.expenses.edit', compact('expense', 'action', 'formOptions', 'operators'))->render();
    }

    /**
     * Store resource
     * 
     * @param Request $request 
     * @param int $tripId 
     * @return string 
     */
    public function store(Request $request, int $tripId) {
        return $this->update($request, $tripId, null);
    }

    /**
     * Update resource
     * 
     * @param Request $request 
     * @param int $tripId 
     * @param int|null $id 
     * @return string 
     */
    public function update(Request $request, int $tripId, int $id = null) {

        $input = $request->all();
        $tripExpense = TripExpense::findOrNew($id);

        $tripExpense->trip_id = $tripId;
        if ($tripExpense->validate($input)) {
            $tripExpense->fill($input);
            $tripExpense->type = TripExpense::OTHER;
            $tripExpense->save();

            $trip = Trip::find($tripId);
            $expenses         = $trip->expenses ?? [];
            return Response::json([
                'result'    => true,
                'feedback'  => 'Despesa guardada com sucesso!',
                'target'    => '.expenses-table',
                'html'      => view('admin.trips.partials.expenses_table', compact('trip', 'expenses'))->render()
            ]);
        }

        return Response::json([
            'result'    => false,
            'feedback'  => $tripExpense->errors()->first()
        ]);
    }

    /**
     * Destroy resource
     * 
     * @param int $tripId 
     * @param int $id 
     * @return mixed 
     */
    public function destroy(int $tripId, int $id) {
        $tripExpense = TripExpense::find($id);
        if (!$tripExpense) {
            return Redirect::back()->with('error', 'Nenhum registo encontrado!');
        }

        $tripExpense->delete();
        return Redirect::back()->with('sucess', 'Despesa eliminada com sucesso!');
    }

}
