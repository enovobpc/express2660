<?php

namespace App\Http\Controllers\Admin\Users;

use Html, Response, Cache, Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\UserContract;

class ContractsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'workgroups';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users_profissional_info']);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $userId) {

        $contract = new UserContract();

        $formOptions = ['route' => ['admin.users.contracts.store', $userId]];

        $action = 'Novo contrato';

        $data = compact('action','formOptions','contract');

        return view('admin.users.users.contracts.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId) {
        return $this->update($request, $userId, null);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $userId, $contractId = null) {

        $contract = UserContract::where('user_id', $userId)
                        ->where('id', $contractId)
                        ->firstOrFail();


        $formOptions = ['route' => array('admin.users.contracts.update', $userId, $contractId), 'method' => 'PUT'];

        $action = 'Editar contrato';

        $data = compact('action','formOptions','contract');

        return view('admin.users.users.contracts.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId, $id = null) {

        UserContract::flushCache(UserContract::CACHE_TAG);

        $input = $request->all();
        $input['notification_days'] = $input['notification_days'] ? $input['notification_days'] : 0;

        $contract = UserContract::firstOrNew([
                'id'      => $id,
                'user_id' => $userId
            ]);


        $notificationDate = new Date($input['end_date']);
        $notificationDate = $notificationDate->subDays($input['notification_days']);

        if ($contract->validate($input)) {
            $contract->fill($input);
            $contract->user_id          = $userId;
            $contract->notification_date = $notificationDate;
            $contract->save();

            return Redirect::back()->with('success', 'Contrato adicionado com sucesso.');
        }

        return Redirect::back()->with('error', 'Erro ao inserir contrato.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $id) {

        UserContract::flushCache(UserContract::CACHE_TAG);

        $result = UserContract::where('user_id', $userId)
            ->whereId($id)
            ->delete();

        if ($result) {
            return Redirect::back()->with('success', 'Contrato removido com sucesso.');
        }

        return Redirect::back()->with('error', 'Erro ao remover contrato.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request, $userId) {

        UserContract::flushCache(UserContract::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = UserContract::where('user_id', $userId)
                            ->whereIn('id', $ids)
                            ->delete();
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
}
