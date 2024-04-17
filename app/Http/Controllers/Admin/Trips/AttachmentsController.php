<?php

namespace App\Http\Controllers\Admin\Trips;

use App\Models\FileRepository;
use Html, Croppa, Response, File, Redirect;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;

class AttachmentsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($customerId) {
        
        $action = 'Adicionar anexo';

        $attachment = new FileRepository();

        $formOptions = ['route' => ['admin.customers.attachments.store', $customerId], 'method' => 'POST', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => true, 'files' => true];

        return view('admin.customers.customers.partials.attachments.edit', compact('attachment', 'action', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $customerId) {
        return $this->update($request, $customerId, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($customerId, $id) {
        
        $action = 'Editar Anexo';

        $attachment = FileRepository::whereHas('customer', function($q){
                $q->filterAgencies();
            })
            ->where('source_class', 'Customer')
            ->where('source_id', $customerId)
            ->where('id', $id)
            ->findOrfail($id);
                
        $formOptions = ['route' => ['admin.customers.attachments.update', $attachment->source_id, $attachment->id], 'method' => 'PUT', 'data-toggle' => 'ajax-form'];
        
        return view('admin.customers.customers.partials.attachments.edit', compact('attachment', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customerId, $id = null) {
        
        $input = $request->all();

        $attachment = FileRepository::findOrNew($id);
        if ($attachment->validate($input)) {
            $attachment->fill($input);
            $attachment->parent_id    = FileRepository::FOLDER_CUSTOMERS;
            $attachment->source_class = 'Customer';
            $attachment->source_id    = $customerId;
            
            if($request->hasFile('file')) {
                if ($attachment->exists && !empty($attachment->filepath)) {
                    File::delete($attachment->filepath);
                }

                if (!$attachment->upload($request->file('file'), true, 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                }
            } else {
                $attachment->save();
            }
            
            if($request->ajax()) {
                return Response::json([
                    'result'    => true,
                    'type'      => 'success',
                    'feedback'  => 'Anexo carregado com sucesso.'
                ]);
            } else {
                return Redirect::back()->with('success', 'Anexo carregado com sucesso.');
            }
        }
        
        if($request->ajax()) {
            return Response::json([
                'result'    => false,
                'type'      => 'error',
                'feedback'  => $attachment->errors()->first()
            ]);
        } else {
            return Redirect::back()->withInput()->with('error', $attachment->errors()->first());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($customerId, $id) {

        $attachment = FileRepository::whereHas('customer', function($q){
                $q->filterAgencies();
            })
            ->where('source_class', 'Customer')
            ->where('source_id', $customerId)
            ->where('id', $id)
            ->firstOrfail();

        if(File::exists($attachment->filepath)) {
            $result = File::delete($attachment->filepath);
        } else {
            $result = true;
        }
        
        if($result) {
            $result = $attachment->delete();
        }
        
        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o anexo.');
        }

        return Redirect::back()->with('success', 'Anexo removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/features/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $result = false;

        $ids = explode(',', $request->ids);
        
        $attachments = FileRepository::whereHas('customer', function($q){
                $q->filterAgencies();
            })
            ->where('source_class', 'Customer')
            ->whereIn('id', $ids)
            ->get();
        
        foreach($attachments as $attachment) {
            $result = File::delete($attachment->filepath);
            $attachment->delete();
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
    public function datatable(Request $request, $customerId) {

        $data = FileRepository::whereHas('customer', function($q){
                $q->filterAgencies();
            })
            ->where('source_class', 'Customer')
            ->where('source_id', $customerId)
            ->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.customers.customers.datatables.attachments.name', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.customers.customers.datatables.attachments.actions', compact('row'))->render();
                })
                ->make(true);
    }
    
    /**
     * Remove the specified resource from storage.
     * GET /admin/vehicles/sort
     *
     * @return Response
     */
    public function sortEdit(Request $request, $customerId) {

        $items = FileRepository::whereHas('customer', function($q){
                $q->filterAgencies();
            })
            ->where('source_class', 'Customer')
            ->where('source_id', $customerId)
            ->orderBy('sort')
            ->get();
  
        $route = route('admin.customers.attachments.sort.update', $customerId);
        
        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }
    
    /**
     * Update the specified resource order in storage.
     * POST /admin/vehicles/versions/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        try {
            FileRepository::setNewOrder($request->ids);
            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }
}
