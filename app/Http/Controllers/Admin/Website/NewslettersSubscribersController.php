<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\NewsletterSubscriber;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;
use Html, Croppa, Response, File, Excel;

class NewslettersSubscribersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'newsletters';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',newsletters_subscribers']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.website.newsletters_subscribers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Subscritor';
        
        $subscriber = new NewsletterSubscriber();
                
        $formOptions = ['route' => ['admin.website.newsletters.subscribers.store'], 'method' => 'POST', 'files' => true];
        
        return view('admin.website.newsletters_subscribers.edit', compact('subscriber', 'action', 'formOptions'))->render();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//        
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Subscritor';
        
        $subscriber = NewsletterSubscriber::findOrfail($id);

        $formOptions = ['route' => ['admin.website.newsletters.subscribers.update', $subscriber->id], 'method' => 'PUT', 'files' => true];

        return view('admin.website.newsletters_subscribers.edit', compact('subscriber', 'action', 'formOptions'))->render();
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
        $input['active'] = $request->get('active', false);
        
        $subscriber = NewsletterSubscriber::findOrNew($id);

        if ($subscriber->validate($input)) {
            $subscriber->fill($input);
            $subscriber->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $subscriber->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = NewsletterSubscriber::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a marca.');
        }

        return Redirect::back()->with('success', 'Marca removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/subscribers/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = NewsletterSubscriber::whereIn('id', $ids)->delete();
        
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
    public function datatable(Request $request) {

        $data = NewsletterSubscriber::select();

        //filter active
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->where('active', $value);
        }

        return Datatables::of($data)
            ->edit_column('email', function($row) {
                return view('admin.website.newsletters_subscribers.datatables.email', compact('row'))->render();
            })
            ->edit_column('active', function($row) {
                return $row->active ? '<div class="text-center"><i class="fa fa-check-circle text-green"></i></div>' : '<div class="text-center"><i class="fa fa-times-circle text-muted"></i></div>';
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.website.newsletters_subscribers.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/subscribers/mail/list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mailList(Request $request) {

        $ids = $request->id;

        $emails = NewsletterSubscriber::where('active', true);

        if(!empty($ids)) {
            $emails = $emails->whereIn('id', $ids);
        }

        $emails = $emails->pluck('email')->toArray();

        $emails = implode(';', array_map('strtolower', $emails));

        return view('admin.website.newsletters_subscribers.list', compact('emails'))->render();
    }

    /**
     * Download list as CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadCSV(Request $request) {

        $ids = $request->id;

        $emails = NewsletterSubscriber::where('active', true);

        if(!empty($ids)) {
            $emails = $emails->whereIn('id', $ids);
        }

        $emails = $emails->pluck('email')->toArray();

        $header = [
            'E-mail'
        ];

        Excel::create('Subscritores Newsletter', function($file) use($emails, $header){

            $file->sheet('Listagem', function($sheet) use($emails, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach($emails as $email) {
                    $rowData = [$email];
                    $sheet->appendRow($rowData);
                }
            });

        })->export('csv');
    }
}
