<?php

namespace App\Http\Controllers\Admin;

use App\Models\CacheSetting;
use App\Models\Customer;
use App\Models\FileRepository;
use App\Models\FleetGest\Vehicle;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Response, DB, File, Setting;

class FileRepositoryController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'files_repository';

    /**
     * Guarded folders
     * @var array
     */
    private $guardedFolders = [
        FileRepository::FOLDER_CUSTOMERS,
        FileRepository::FOLDER_USERS,
        FileRepository::FOLDER_VEHICLES,
        FileRepository::FOLDER_SHIPMENTS
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',files_repository']);
        validateModule('files_repository');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $extensions = FileRepository::filterSource()
            ->whereNotNull('extension')
            ->groupBy('extension')
            ->pluck('extension', 'extension')
            ->toArray();

        $curFolder  = null;
        $breadcrumb = null;

        if($request->has('folder')) {
            $curFolder = FileRepository::filterSource()
                ->where('is_folder', 1)
                ->whereId($request->folder)
                ->first();

            if($curFolder) {
                $breadcrumb = FileRepository::getFolderBreadcrumb($curFolder);
            }
        }

        $serverSize = (CacheSetting::get('quota') ?? 10) * 1073741824;
        $totalSize = FileRepository::filterSource()
            ->where('is_folder', 0)
            ->sum('filesize');

        $ocupiedSizePercent = $serverSize > 0 ? ($totalSize*100)/$serverSize : 0;

        if($ocupiedSizePercent >= 90) {
            $ocupiedSizeColor = '#ff0000';
        } elseif($ocupiedSizePercent >= 75) {
            $ocupiedSizeColor = '#f46a00';
        } elseif($ocupiedSizePercent >= 50) {
            $ocupiedSizeColor = '#ffcc01';
        } else {
            $ocupiedSizeColor = '#00A623';
        }


        $guardedFolders = $this->guardedFolders;

        $types = [
            'VehicleAttachment'  => 'Viaturas',
            'CustomerAttachment' => 'Clientes',
            'UserAttachment'     => 'Operadores',
            'ShipmentAttachment' => 'Envios'
        ];

        $data = compact(
            'extensions',
            'types',
            'curFolder',
            'breadcrumb',
            'guardedFolders',
            'totalSize',
            'serverSize',
            'ocupiedSizePercent',
            'ocupiedSizeColor'
        );

        return $this->setContent('admin.repository.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $source = $request->get('source');

        $action = 'Adicionar ficheiro';

        $attachment = new FileRepository();
        $attachment->parent_id = $request->parent;

        if($source == 'folder') {
            $action = 'Adicionar pasta';
            $attachment->is_folder = 1;
        }

        $formOptions = ['route' => ['admin.repository.store'], 'method' => 'POST', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => 1, 'files' => true];

        $guardedFolders = $this->guardedFolders;

        return view('admin.repository.edit', compact('attachment', 'action', 'formOptions', 'guardedFolders'))->render();
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
    public function edit($id) {

        $attachment = FileRepository::filterSource()
            ->where('id', $id)
            ->findOrfail($id);

        $action = 'Editar ficheiro';
        if($attachment->is_folder) {
            $action = 'Editar pasta';
        }

        $formOptions = ['route' => ['admin.repository.update', $attachment->id], 'method' => 'PUT', 'data-toggle' => 'ajax-form', 'data-refresh-datatables' => 1];

        return view('admin.repository.edit', compact('attachment', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) {

        $input = $request->all();

        if(!empty($input['parent_id'])) {

            $parentFolder = FileRepository::filterSource()
                ->where('is_folder', 1)
                ->where('id', $input['parent_id'])
                ->first();

            $input['parent_id'] = @$parentFolder->id;
        }



        $attachment = FileRepository::findOrNew($id);
        if ($attachment->validate($input)) {
            $attachment->fill($input);

            if($request->hasFile('file')) {
                if ($attachment->exists && !empty($attachment->filepath)) {
                    File::delete($attachment->filepath);
                }

                if (!$attachment->upload($request->file('file'), true, 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                }

                $attachment->is_folder = 0;
                $attachment->filesize  = \File::size($attachment->filepath);
            }

            $attachment->save();

            //update target type
            if($attachment->target_type) {
                $class = 'App\Models\\'.$attachment->target_type;
                $masterAttachment = $class::findOrNew($attachment->attachment_id);
                $masterAttachment->fill($attachment->toArray());
                $masterAttachment->save();
            }

            if($request->ajax()) {
                return Response::json([
                    'result'    => true,
                    'type'      => 'success',
                    'feedback'  => 'Ficheiro carregado com sucesso.'
                ]);
            } else {
                return Redirect::back()->with('success', 'Ficheiro carregado com sucesso.');
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
    public function destroy($id) {

        $file = FileRepository::filterSource()
            ->where('id', $id)
            ->where('is_static', 0)
            ->first();

        $result = FileRepository::destroyRecursive($file);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o ficheiro.');
        }

        return Redirect::back()->with('success', 'Ficheiro removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/features/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $ids = explode(',', $request->ids);

        $files = FileRepository::filterSource()
            ->whereIn('id', $ids)
            ->where('is_static', 0)
            ->get();

        $result = true;
        foreach($files as $file) {
            $result = FileRepository::destroyRecursive($file);
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }


    /**
     * Download file
     *
     * @return type
     */
    public function download(Request $request, $id) {

        $disposition = 'inline'; //show on browser
        if($request->get('download')) {
            $disposition = 'attachment'; //force download
        }

        $file = FileRepository::filterSource()
            ->where('id', $id)
            ->where('is_static', 0)
            ->firstOrFail();

        $filepath = public_path() . '/' . $file->filepath;
        if(in_array($file->source_class, ['Customer'])) { //get from private storage
            $filepath = storage_path() . '/' . $file->filepath;
        }

        return response()->download($filepath, null, [], $disposition);
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $vehicles = null;

        $data = FileRepository::filterSource()
                            ->select();

        //filter folder
        if(!($request->has('mode') && $request->mode == 'list')) {
            $value = $request->folder;
            if ($request->has('folder') && !empty($value)) {

                if(in_array($value, $this->guardedFolders)) {
                    if($value == FileRepository::FOLDER_CUSTOMERS) {
                        $data = $data->with('customer');
                    } elseif($value == FileRepository::FOLDER_USERS) {
                        $data = $data->with('user');
                    } elseif($value == FileRepository::FOLDER_SHIPMENTS) {
                        $data = $data->with('shipment');
                    } elseif($value == FileRepository::FOLDER_VEHICLES) {
                        $vehicles = Vehicle::pluck('name', 'id')->toArray();
                    }
                }

                $data = $data->where('parent_id', $value);
            } else {
                $data = $data->whereNull('parent_id');
            }
        }


        //filter mode
        $value = $request->mode;
        if($request->has('mode') && $value == 'list') {
            $data = $data->where('is_folder', 0);
        }

        //filter type
        $value = $request->type;
        if($request->has('type')) {
            $data = $data->whereIn('target_type', $value);
        }

        //filter extension
        $value = $request->extension;
        if($request->has('extension')) {
            $data = $data->whereIn('extension', $value);
        }


        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.repository.datatables.name', compact('row'))->render();
            })
            ->edit_column('target_type', function($row) use($vehicles) {
                return view('admin.repository.datatables.type', compact('row', 'vehicles'))->render();
            })
            ->edit_column('extension', function($row) {
                return view('admin.repository.datatables.extension', compact('row'))->render();
            })
            ->edit_column('filesize', function($row) {
                return human_filesize($row->filesize);
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.repository.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search source
     *
     * @return type
     */
    public function searchSource(Request $request) {

        $parentId = $request->get('parent');

        if($parentId == FileRepository::FOLDER_CUSTOMERS) {
            return $this->searchCustomer($request);
        } elseif($parentId == FileRepository::FOLDER_USERS) {
            return $this->searchUser($request);
        } elseif($parentId == FileRepository::FOLDER_SHIPMENTS) {
            return $this->searchShipment($request);
        } elseif($parentId == FileRepository::FOLDER_VEHICLES) {
            return $this->searchVehicles($request);
        }

        return [];
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'code',
            'name'
        ];

        try {
            $results = [];

            $rows = Customer::filterSource()
                ->filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->get($fields);

            if($rows) {
                $results = array();
                foreach($rows as $row) {
                    $results[] = [
                        'id'   => $row->id,
                        'text' => $row->code. ' - '.str_limit($row->name, 40),
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum resultado encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchUser(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'code',
            'name'
        ];

        try {
            $results = [];

            $rows = User::filterSource()
                ->filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->get($fields);

            if($rows) {
                $results = array();
                foreach($rows as $row) {
                    $results[] = [
                        'id'   => $row->id,
                        'text' => $row->code. ' - '.str_limit($row->name, 40),
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum resultado encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchShipment(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'tracking_code',
            'sender_name',
            'recipient_name'
        ];

        try {
            $results = [];

            $rows = Shipment::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('tracking_code', 'LIKE', $search)
                    ->orWhere('sender_name', 'LIKE', $search)
                    ->orWhere('recipient_name', 'LIKE', $search);
                })
                ->take(100)
                ->get($fields);

            if($rows) {
                $results = array();
                foreach($rows as $row) {
                    $results[] = [
                        'id'   => $row->id,
                        'text' => $row->tracking_code. ' - '.str_limit($row->recipient_name, 40),
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum resultado encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchVehicles(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'license_plate',
            'name'
        ];

        try {
            $results = [];

            $rows = Vehicle::filterSource()
                ->filterAgencies()
                ->where(function($q) use($search){
                    $q->where('license_plate', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->get($fields);

            if($rows) {
                $results = array();
                foreach($rows as $row) {
                    $results[] = [
                        'id'   => $row->id,
                        'text' => $row->license_plate. ' - '.str_limit($row->name, 40),
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum resultado encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }
}
