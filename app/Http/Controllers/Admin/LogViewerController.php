<?php

namespace App\Http\Controllers\Admin;

use App\Models\LogViewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Html, Croppa, Auth, File, Response;

class LogViewerController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'log_viewer';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',log_erros']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $logFiles = File::allFiles(storage_path('logs'));

        $logFiles = collect($logFiles);
        $logFiles = $logFiles->sortByDesc(function ($file) {
            return @$file->getBaseName();
        })->toArray();
        $logFiles = array_values($logFiles);

        if (empty($request->file)) {
            $file     = @$logFiles[0];
            $filename = empty($file) ? null : @$file->getFilename();
        } else {
            $file     = @$logFiles[0];
            $filename = base64_decode($request->file);
        }

        if (empty($filename)) {
            $logs = [];
        } else {
            LogViewer::setFile($filename);
            $logs = LogViewer::all();
        }

        $totals = [];
        foreach ($logFiles as $logFile) {
            LogViewer::setFile($logFile->getFilename());
            $rows = LogViewer::all();
            $rows = !empty($rows) ? $rows : []; // avoids breaking when the log file is to big

            $levels = array_count_values(array_column($rows, 'level'));
            foreach ($rows as $row) {

                $totals[$logFile->getFilename()] = [
                    'all'       => count($rows),
                    'emergency' => @$levels['emergency'],
                    'alert'     => @$levels['alert'],
                    'critical'  => @$levels['critical'],
                    'error'     => @$levels['error'],
                    'warning'   => @$levels['warning'],
                    'notice'    => @$levels['notice'],
                    'info'      => @$levels['info'],
                    'debug'     => @$levels['debug']
                ];
            }
        }

        $slug = base64_encode($filename);

        return $this->setContent('admin.logs.viewer.index', compact('logFiles', 'logs', 'file', 'slug', 'totals'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($filename)
    {

        $filename = base64_decode($filename);

        try {
            File::delete(storage_path('logs/' . $filename));
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::route('admin.logs.errors.index')->with('success', 'Ficheiro removido com sucesso.');
    }

    /**
     * Download log file
     * @param $file
     * @return mixed
     */
    public function download($file)
    {
        $filename = base64_decode($file);
        return Response::download(LogViewer::pathToLogFile($filename));
    }

    /**
     * Destroy all files in directory
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAll()
    {

        try {
            $file = new Filesystem();
            @$file->cleanDirectory(storage_path('logs'));
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::route('admin.logs.errors.index')->with('success', 'Ficheiros removidos com sucesso.');
    }
}
