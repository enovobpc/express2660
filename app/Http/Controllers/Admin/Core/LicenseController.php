<?php

namespace App\Http\Controllers\Admin\Core;

use App\Models\CacheSetting;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Core\Module;
use Response, Auth, File, Setting, Croppa, DB;

class LicenseController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'modules';

    /**
     * List of stoage directories
     *
     * @var array
     */
    public $settingFields = [
        'debug_mode',
        'api_debug_mode',
        'error_log_email_active',
        'error_log_email',
        'debug_ignore_ip',
        'maintenance_mode',
        'maintenance_time',
        'maintenance_ignore_ip'
    ];

    /**
     * List of stoage directories
     *
     * @var array
     */
    protected $storageDirectories = [
        '/framework/cache',
        '/framework/views',
        '/framework/sessions',
        '/logs',
        '/debugbar',
        '/importer',
        '/invoices',
        '/keyinvoice-logs'
    ];

    /**
     * List of stoage directories
     *
     * @var array
     */
    protected $directoryModels = [
        '/uploads/agencies' => 'Agency',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',modules']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $modules = Module::orderBy('sort')->get();
        $modulesGroupedUnsorted = $modules->groupBy('group');
        $activeModules = Module::getActiveModules();

        $modulesSorted = [
            'ÁREA DE CLIENTE',
            'ENVIOS E RECOLHAS',
            'FATURAÇÃO',
            'CONTROLO FINANCEIRO',
            'ENTIDADES',
            'APLICAÇÃO MOBILE E LOCALIZAÇÃO',
            'MÓDULOS ADICIONAIS',

            'INTEGRAÇÃO COM WEBSITE',
            'WEBSITE'
        ];

        $modulesGrouped = [];
        foreach ($modulesSorted as $moduleName) {
            $modulesGrouped[$moduleName] = $modulesGroupedUnsorted[$moduleName];
        }

        $spaces = [
            '1'  =>'1 GB',
            '2'  =>'2 GB',
            '5'  =>'5 GB',
            '10' =>'10 GB',
            '15' =>'15 GB',
            '20' =>'20 GB',
            '30' =>'30 GB',
            '40' =>'40 GB',
            '50' =>'40 GB',
            '60' =>'60 GB',
            '70' =>'70 GB',
            '80' =>'80 GB',
            '100' =>'100 GB',
            '120' =>'120 GB',
            '150' =>'150 GB',
            '240' =>'240 GB',
        ];

        $emails = [
            '' => 'Ilimitado',
            '2' => '2 contas',
            '3' => '3 contas',
            '5' => '5 contas',
            '10' => '10 contas',
            '15' => '15 contas',
            '20' => '20 contas'
        ];

        $versionsList = DB::connection('mysql_enovo')
            ->table('setup_settings')
            ->where('category', 'version')
            ->pluck('name', 'key')
            ->toArray();

        $planVersion = @$versionsList[CacheSetting::get('plan_version')];

        $filepath = app_path().'/Http/Controllers/Admin/.version';
        $version  = File::get($filepath);
        $version  = trim($version);

        $versionDate = filemtime($filepath);
        $versionDate = date('Y-m-d', $versionDate);

        $storageDirectories = [];
        foreach ($this->storageDirectories as $directory) {

            $folderName = explode('/', $directory);
            $folderName = end($folderName);

            $storageDirectories[$folderName] = ['filepath' => storage_path() . $directory];
        }

        $licenseStatus = File::exists(storage_path() . '/license.json') ? '0' : '1';

        
        if (!File::exists(public_path('uploads/tmp_files'))) {
            File::makeDirectory(public_path('uploads/tmp_files'));
        }

        $data = compact(
            'modulesGrouped',
            'activeModules',
            'spaces',
            'emails',
            'version',
            'versionDate',
            'storageDirectories',
            'licenseStatus',
            'versionsList',
            'planVersion'
        );

        return view('admin.core.license.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        try {

            //action to sync core settings
            if($request->get('action') == 'sync-settings') {
                if(CacheSetting::syncCoreDBSettings()) {
                    return CacheSetting::getLastUpdate();
                } else {
                    return 'Falha na sincronização';
                }
            }

            $myIp  = client_ip();
            $input = $request->all();
            $input['debug_ignore_ip']       = $request->get('debug_ignore_ip');
            $input['maintenance_ignore_ip'] = $request->get('maintenance_ignore_ip');


            if(!empty($input['plan_version'])) {
                //obtem modulos da versão
                $modules = DB::connection('mysql_enovo')
                    ->table('setup_settings')
                    ->where('key', $input['plan_version'])
                    ->first();

                $modules = explode(',', $modules->value);
                Module::setActiveModules($modules);
                CacheSetting::set('plan_version', $request->get('plan_version'));
            } else {
                $modules = $request->get('modules');
                Module::setActiveModules($modules);
            }


            CacheSetting::set('quota', $request->get('quota'));
            CacheSetting::set('emails_total', $request->get('emails_total'));

            $filename = storage_path() . '/license.json';
            if($input['license']) {
                File::delete($filename);
            } else {
                File::put($filename, '');
            }

            //save settings
            foreach ($this->settingFields as $fieldName) {
                Setting::set($fieldName, @$input[$fieldName]);
            }

            Setting::save();


            //maintenance mode
            if(Setting::get('maintenance_mode')) {
                $ips = Setting::get('maintenance_ignore_ip');
                if(!empty($ips)) {
                    $ips = explode(',', Setting::get('maintenance_ignore_ip'));
                } else {
                    $ips = [];
                }

                if(empty($ips) || !in_array(client_ip(), $ips)) { //force to set current ip
                    $ips[] = client_ip();
                }

                touch(storage_path() . '/framework/down');
            } else {
                File::delete(storage_path() . '/framework/down');
            }

            //debug mode
            if(Setting::get('debug_mode')) {
                $ips = Setting::get('debug_ignore_ip');
                if(!empty($ips)) {
                    $ips = explode(',', Setting::get('debug_ignore_ip'));
                } else {
                    $ips = [];
                }

                if(empty($ips) || !in_array(client_ip(), $ips)) { //force to set current ip
                    $ips[] = client_ip();
                }

                $filename = storage_path() . '/framework/debug_ips';
                File::put($filename, implode(',', $ips));

            } else {
                File::delete(storage_path() . '/framework/debug_ips');
            }

            return Redirect::back()->with('success', 'Definições gravadas com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao gravar módulos: ' . $e->getMessage());
        }
    }

    /**
     * Upload File
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request) {

        $input = $request->except('_token');

        $allSettings = Setting::all();
        foreach($allSettings as $setting => $value) {
            Setting::forget($setting);
        }
        Setting::save();

        foreach ($input as $attribute => $value) {

            if(!empty($value)) {
                Setting::set($attribute, $value);
            }
        }
        Setting::save();

        return Redirect::back()->with('success', 'Alterações gravadas com sucesso');
    }

    /**
     * Clean store directories
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storageClean(Request $request) {

        $input = $request->folders;

        foreach ($input as $directory) {
            $file = new Filesystem();
            @$file->cleanDirectory($directory);

            File::put($directory.'/.gitignore', '');
        }

        return Redirect::back()->with('success', 'Dados limpos com sucesso.');
    }

    /**
     * Show directory files
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showDirectory(Request $request) {

        $directory = $request->directory;
        $storage   = $request->get('storage', false);

        $files = File::allFiles($directory);

        $files = array_sort($files, function($file) {
            return @$file->getFilename();
        });

        $directoryName = str_replace(public_path(), '', $directory);

        return view('admin.core.license.modals.files', compact('files', 'directoryName', 'storage'))->render();
    }

    /**
     * Show directory files
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyFile(Request $request) {

        $file = $request->file;

        Croppa::delete($file);

        $result = File::delete(public_path() . $file);

        return Response::json([
            'result'   => $result
        ]);
    }

    /**
     * Clean all directory files
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cleanDirectory(Request $request) {

        $folder = $request->folder;

        $file = new Filesystem();
        @$file->cleanDirectory(public_path() . $folder);

        return Redirect::back()->with('success', 'Diretoria limpa com sucesso.');
    }

    /**
     * Compact all directory files
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function compactDirectory(Request $request) {

        $quality    = 40;
        $folder     = $request->folder;
        $directory  = public_path() . $folder;

        $files = File::allFiles($directory);

        $originalSize = $currentSize = 0;

        try {
            foreach ($files as $file) {

                $originalSize  += @$file->getSize();
                $filename       = @$file->getFilename();
                $absolutePath   = str_replace($filename, '', @$file->getRealPath());
                $realPath       = str_replace(' ', '%20', $absolutePath) . $filename;
                $mimeType       = mime_content_type($realPath);


                $isImage = false;
                if ($mimeType == 'image/jpeg') {
                    $image = imagecreatefromjpeg($realPath);
                    $isImage = true;
                } elseif ($mimeType == 'image/png') {
                    $image = imagecreatefrompng($realPath);
                    $isImage = true;
                }

                if($isImage) {

                    $imgSize = getimagesize($realPath);
                    if(@$imgSize[0] > 800 || @$imgSize[0] > 800) { //resize image if width or height are bigger than 800
                        $thumb = new \Imagick();
                        $thumb->readImage($realPath);
                        //$thumb->resizeImage(800, 600,\Imagick::FILTER_LANCZOS, 1);
                        $thumb->scaleImage(800, 0);
                        $thumb->writeImage($realPath);
                        $thumb->clear();
                        $thumb->destroy();
                    }

                    imagejpeg($image, $realPath, $quality);
                    clearstatcache();
                }

                $currentSize += filesize($realPath);
            }

            $compactSize = $originalSize - $currentSize;

            return Redirect::back()->with('success', 'Compressão com sucesso. Reduzidos ' . human_filesize($compactSize));

        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Clean all directory files
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadFile(Request $request) {
        $file = $request->file;
        return response()->download($file);
    }


    /**
     * Load storage directory
     * @param Request $request
     * @return mixed|null|string
     * @throws \Throwable
     */
    public function loadDirectories(Request $request) {

        if(Auth::user()->hasRole('administrator')) {

            $directories = $this->storageDirectories;

            $storageDirectoriesSize = $storageDirectoriesCount = 0;
            foreach ($directories as $directory) {
                $dirSize = 0;
                $countFiles = 0;

                foreach(File::allFiles(storage_path() . $directory) as $file) {
                    $countFiles++;
                    $dirSize += @$file->getSize();
                }

                $folderName = explode('/', $directory);
                $folderName = end($folderName);

                $storageDirectories[$folderName] = [
                    'filepath'  => storage_path() . $directory,
                    'size'      => $dirSize,
                    'count'     => $countFiles
                ];

                $storageDirectoriesSize+= $dirSize;
                $storageDirectoriesCount+= $countFiles;
            }

            $directories = File::directories(public_path() . '/uploads');

            foreach ($directories as $directory) {
                $dirSize = 0;
                $countFiles = 0;

                foreach(File::allFiles($directory) as $file) {
                    $countFiles++;
                    $dirSize += @$file->getSize();
                }

                $folderName = explode('/', $directory);
                $folderName = end($folderName);

                $uploadDirectories[$folderName] = [
                    'filepath'  => $directory,
                    'size'      => $dirSize,
                    'count'     => $countFiles
                ];
            }

            aasort($uploadDirectories, 'size', SORT_DESC);
            aasort($storageDirectories, 'size', SORT_DESC);
        }

        $data = [
            'uploads' => view('admin.core.license.partials.uploads_directory', compact('uploadDirectories'))->render(),
            'storage' => view('admin.core.license.partials.storage_directory', compact('storageDirectories'))->render(),
            'storage_size'  => human_filesize($storageDirectoriesSize),
            'storage_count' => $storageDirectoriesCount,
        ];

        return response()->json($data);
    }
}
