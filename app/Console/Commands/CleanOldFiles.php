<?php

namespace App\Console\Commands;

use App\Models\LogViewer;
use App\Models\ShipmentHistory;
use Illuminate\Console\Command;
use File, Date, Log;

class CleanOldFiles extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:oldfiles {daysOld?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean uploaded old files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        //remove ficheiros com mais de x dias [default = 90]
        $limitDays = $this->argument('daysOld') ?? 90; 
        $limitDate = Date::now()->subDays($limitDays)->format('Y-m-d');
        $counter   = 0;

        try {
            //apaga provas de entrega com data anterior à definida
            $shipmentsHistory = ShipmentHistory::withTrashed()
                ->whereRaw('DATE(created_at) < "'.$limitDate.'"')
                ->whereNotNull('filepath')
                ->get(['filepath', 'created_at']);

           
            foreach($shipmentsHistory as $history) {

                $filepath = public_path($history->filepath);
                
                if(File::exists($filepath)) {
                    $file = File::delete($filepath);
                    $counter++;

                    $info = $counter . ' | Arquivo removido: ['.$history->created_at.'] ' . $history->filepath;
                    //echo $info.'<br/>';
                    $this->info($info);
                }
            }
        } catch(\Exception $e) {
            $trace = LogViewer::getTrace(null, exceptionMsg($e));
            Log::error(br2nl($trace));
        }


        //apaga ficheiros de outras pastas ou ficheiros que por ventura estejam em servidor mas não estejam registados na base de dados
        $folders = [
            public_path('uploads/tmp_files'),
            public_path('uploads/shipments'),
            public_path('uploads/shipments_attachments')
        ];

       
        try {
            
            $limitDate = strtotime($limitDate);
            foreach($folders as $folder) {
                
                if(is_dir($folder)) {
                
                    $files = glob($folder . '/*');

                    foreach ($files as $file) {
                        if (is_file($file) && filemtime($file) < $limitDate) {
                            unlink($file);
                            $counter++;

                            $info = $counter . ' | Arquivo removido: ['.filemtime($file).'] ' . $file;
                            //echo $info.'<br/>';
                            $this->info($info);
                        }
                    }
                }
            }
            
        } catch(\Exception $e) {
            $trace = LogViewer::getTrace(null, exceptionMsg($e));
            Log::error(br2nl($trace));
        }

        $this->info('Remoção de arquivos concluída. '.$counter.' ficheiros removidos');
        return;
    }

}
