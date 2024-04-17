<?php

namespace App\Http\Controllers\Admin\Emails;

use Illuminate\Http\Request;
use App\Models\Email\MailingList;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Cache, Response, DB, Mail;

class ListsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'mailing_lists';

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',mailing_lists']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $mailingList = MailingList::get();

        return $this->setContent('admin.emails.lists.index', compact('mailingList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $action = 'Criar nova lista e-mails';

        $mailingList = new MailingList();

        $formOptions = array('route' => array('admin.emails.lists.store'), 'method' => 'POST');

        $data = compact(
            'mailingList',
            'action',
            'formOptions'
        );

        return view('admin.emails.lists.edit', $data)->render();
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
    public function edit($id)
    {

        $action = 'Editar lista de e-mails';

        $mailingList = MailingList::filterSource()
            ->whereId($id)
            ->firstOrfail();

        $formOptions = array('route' => array('admin.emails.lists.update', $mailingList->id), 'method' => 'PUT');


        $data = compact(
            'mailingList',
            'action',
            'formOptions'
        );

        return view('admin.emails.lists.edit', $data)->render();
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

        $input = $request->all();


        $mailingList = MailingList::findOrNew($id);

        $exists = $mailingList->exists;
        if ($mailingList->validate($input)) {
            $mailingList->fill($input);
            $mailingList->source = config('app.source');
            $mailingList->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $mailingList->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $result = MailingList::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar eliminar o grupo.');
        }

        return Redirect::back()->with('success', 'Grupo eliminado com sucesso.');
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

        $ids = explode(',', $request->ids);

        $result = MailingList::filterSource()
            ->whereIn('id', $ids)
            ->delete();

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

        $data = MailingList::filterSource()
            ->orderBy('sort')
            ->select();

        return Datatables::of($data)
            ->edit_column('name', function ($row) {
                return view('admin.emails.lists.datatables.name', compact('row'))->render();
            })
            ->edit_column('count', function ($row) {
                return view('admin.emails.lists.datatables.count', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.emails.lists.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/documents/sort
     *
     * @return Response
     */
    public function sortEdit() {
   
        $items = MailingList::orderBy('sort')
                        ->get(['id', 'name']);
        
        $route = route('admin.emails.lists.sort.update');
        
        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }
    
    /**
     * Update the specified resource order in storage.
     * POST /admin/documents/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = MailingList::setNewOrder($request->ids);
        
        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}
