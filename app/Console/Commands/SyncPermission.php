<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Jenssegers\Date\Date;
use App\Models\Permission;

class SyncPermission extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync application permissions';

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
        
        $this->info("Sync application permissions\n");

        $current = Permission::pluck('id', 'name')->toArray(); //database permissions

        $permissions = $this->formatPermissions(); //permissions on config file

        /**
         * Remove permission that are not in the permission list and are in DB
         */
        $detached = array_diff_key($current, $permissions);

        if (!empty($detached)) {
            Permission::destroy(array_values($detached));
            $this->info("Permissions removed: " . implode(', ', array_keys($detached)));
        } else {
            $this->comment("No Permission removed from DB");
        }

        /**
         * Add permissions that are not in BD
         */
        $attached = array_diff_key($permissions, $current);

        if (!empty($attached)) {

            $newPerms = array();
            //create array off data to insert in database
            foreach ($attached as $name => $displayName) {
                $newPerms[] = array(
                    'name' => $name,
                    'display_name' => $displayName,
                    'created_at' => new Date,
                    'updated_at' => new Date,
                );
            }

            Permission::insert($newPerms);
            $this->info(" Permissions inserted: " . implode(', ', array_keys($attached)));
        } else {
            $this->comment(" No Permissions inserted in DB");
        }

        $this->info("Sync completed");
        return;
    }

    /**
     * Get formated configs permissions <br>
     * ('name' => 'display_name', ...)
     * 
     * @return array
     */
    protected function formatPermissions() {
        
        $permissions = Config::get('permissions.list');

        $formatedPerms = array();
        foreach ($permissions as $perm) {
            $formatedPerms += array($perm['name'] => $perm['display_name']);
        }

        return $formatedPerms;
    }

}
