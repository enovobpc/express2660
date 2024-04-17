<?php

namespace App\Http\Controllers\Admin\Core;

use App\Models\Billing\VatRate;
use App\Models\Timeline\EventType;
use App\Models\Core\Source;
use App\Models\Core\TerminalCommand;
use App\Models\Trip\Trip;
use App\Models\Trip\TripPeriod;
use App\Models\FileRepository;
use App\Models\ImporterModel;
use App\Models\IncidenceType;
use App\Models\PackType;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\ProviderCategory;
use App\Models\ServiceGroup;
use App\Models\ShippingExpense;
use App\Models\WebserviceMethod;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Response, DB, Auth, Hash, File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TerminalController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'terminal';


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
    public function index(Request $request) {

        $password = $request->get('password');
        if($request->has('password')) {

            $userPassword = Auth::user()->password;
            if(Hash::check($password, $userPassword)) {
                Session::set('terminal_pwd', $password);
                return Redirect::back()->with('success', 'Login efetuado.');
            } else {
                return Redirect::back()->with('error', 'Password errada');
            }
        }

        $terminalCommands = TerminalCommand::get();
        $commands = [];
        foreach ($terminalCommands as $command) {
            $commands[] = [
                'value' => $command->command,
                'data' => $command->command
            ];
        }

        $commands = json_encode($commands);

        return $this->setContent('admin.core.terminal.index', compact('commands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function execute(Request $request) {

        try {
            $sourceName = config('app.source');
            $command = trim($request->command);

            //migra a base de dados para forçar a que o source seja o atual
            if($command == 'migrate' || $command == 'migrate --force') {
                try {
                    ProviderCategory::where('id', '>=', 1)->update(['source' => $sourceName]);
                    WebserviceMethod::where('id', '>=', 1)->update(['sources' => '["' . $sourceName . '"]']);
                    EventType::where('id', '>=', 1)->update(['source' => $sourceName]);
                    FileRepository::where('id', '>=', 1)->update(['source' => $sourceName]);
                    IncidenceType::where('id', '>=', 1)->update(['source' => $sourceName]);
                    PackType::where('id', '>=', 1)->update(['source' => $sourceName]);
                    PaymentCondition::where('id', '>=', 1)->update(['source' => $sourceName]);
                    PaymentMethod::where('id', '>=', 1)->update(['source' => $sourceName]);
                    ServiceGroup::where('id', '>=', 1)->update(['source' => $sourceName]);
                    VatRate::where('id', '>=', 1)->update(['source' => $sourceName]);
                    TripPeriod::where('id', '>=', 1)->update(['source' => $sourceName]);
                    ShippingExpense::where('id', '>=', 1)->update(['source' => $sourceName]);
                } catch (\Exception $e) {}
                
            }

            $terminalCommand = TerminalCommand::firstOrNew(['command' => $command]);

            if(!$terminalCommand->exists) {
                $terminalCommand->command = $command;
                $terminalCommand->save();
            }

            $commandParts = explode(' ', $command);
            $command = $commandParts[0];
            unset($commandParts[0]);
            $params  = $commandParts;

            if(empty($params)) {
                $paramsArr = [];
            } else {
                foreach ($params as $param) {

                    if(str_contains($param, '=')) {

                        $paramParts = explode('=', $param);

                        $key = @$paramParts[0];
                        $val = @$paramParts[1];
                        $key = empty($key) ? 'name' : $key;
                    } else {

                        if(str_contains($param, '--')) {
                            $key = $param;
                            $val = true;
                        } else {
                            $key = 'name';
                            $val = $param;
                        }
                    }

                    $paramsArr[$key] = $val;
                }
            }

            Artisan::call($command, $paramsArr);
            $output = '<pre>' . Artisan::output().'</pre>';

            //ATUALIZA EM SISTEMA CENTRAL A VERSÃO DA APP
            $filepath = app_path().'/Http/Controllers/Admin/.version';
            $version  = File::get($filepath);
            $version  = trim($version);

            $versionDate = filemtime($filepath);
            $versionDate = date('Y-m-d', $versionDate);

            $source = Source::where('source', config('app.source'))->first();
            if($source && $source->version != $version) {
                $source->old_version = $source->version;
                $source->old_version_date = $source->version_date;
                $source->version = $version;
                $source->version_date = $versionDate;
                $source->save();
            }

        } catch (\Exception $e) {
            $output = '<pre>'.$e->getMessage().' on file '. $e->getFile().' line ' .$e->getLine().'</pre>';
        }

        $result = [
            'result' => true,
            'output' => $output,
        ];

        return response()->json($result);
    }
}
