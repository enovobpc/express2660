<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFilesRepository extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('files_repository');

        if(!Schema::hasTable('files_repository')) {
            Schema::create('files_repository', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('parent_id')->unsigned()->nullable();
                $table->string('name');
                $table->string('filepath')->index();
                $table->string('filename');
                $table->string('filehost');
                $table->string('extension', '6')->nullable();
                $table->string('filesize')->nullable();
                $table->string('source_class')->nullable();
                $table->integer('source_id')->nullable();
                $table->boolean('is_folder')->default(0);
                $table->boolean('is_static')->default(0);
                $table->boolean('operator_visible')->default(0);
                $table->integer('count_folders')->default(0);
                $table->integer('count_files')->default(0);
                $table->integer('sort')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('parent_id')
                    ->references('id')
                    ->on('files_repository');
            });
        }


        try {
            $exists = DB::table('permissions')->where('name', 'files_repository')->first();
            if(!$exists) {
                DB::table('permissions')->insert([
                    'name' => 'files_repository',
                    'display_name' => 'Gerir Arquivo de Ficheiros',
                    'module' => 'files_repository',
                    'group' => 'Arquivo de Ficheiros',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }


            DB::table('files_repository')->insert([
                'id' => 1,
                'source'        => config('app.source'),
                'name'          => 'Clientes',
                'created_at'    => date('Y-m-d H:i:s'),
                'is_folder'     => 1,
                'is_static'     => 1,
            ]);

            DB::table('files_repository')->insert([
                'id' => 2,
                'source'        => config('app.source'),
                'name'          => 'Colaboradores',
                'created_at'    => date('Y-m-d H:i:s'),
                'is_folder'     => 1,
                'is_static'     => 1,
            ]);

            DB::table('files_repository')->insert([
                'id' => 3,
                'source'        => config('app.source'),
                'name'          => 'Viaturas',
                'created_at'    => date('Y-m-d H:i:s'),
                'is_folder'     => 1,
                'is_static'     => 1,
            ]);
            DB::table('files_repository')->insert([
                'id' => 4,
                'source'        => config('app.source'),
                'name'          => 'Envios',
                'created_at'    => date('Y-m-d H:i:s'),
                'is_folder'     => 1,
                'is_static'     => 1,
            ]);



            $attachments = DB::table('customers_attachments')->whereNull('deleted_at')->get();
            if($attachments) {
                $type = 'Customer';
                foreach ($attachments as $attachment) {

                   /* if(\File::exists(public_path($attachment->filepath))) {
                        $filesize  = \File::size(public_path($attachment->filepath));*/
                        $extension = strtolower(\File::extension(public_path($attachment->filepath)));

                        DB::table('files_repository')->insert([
                            'parent_id'     => 1,
                            'source'        => config('app.source'),
                            'name'          => $attachment->name,
                            'filepath'      => $attachment->filepath,
                            'filename'      => $attachment->filename,
                            'filehost'      => env('APP_URL'),
                            'filesize'      => @$filesize,
                            'extension'     => @$extension,
                            'created_at'    => $attachment->created_at,
                            'source_class'   => $type,
                            'source_id'     => $attachment->customer_id,
                        ]);
                    /*}*/
                }
            }

            $attachments = DB::table('users_attachments')->whereNull('deleted_at')->get();
            if($attachments) {
                $type = 'User';
                foreach ($attachments as $attachment) {
                    /*if(\File::exists(public_path($attachment->filepath))) {
                        $filesize  = \File::size(public_path($attachment->filepath));*/
                    $extension = \File::extension(public_path($attachment->filepath));

                    DB::table('files_repository')->insert([
                        'parent_id'     => 2,
                        'source' => config('app.source'),
                        'name' => $attachment->name,
                        'filepath' => $attachment->filepath,
                        'filename' => $attachment->filename,
                        'filehost' => env('APP_URL'),
                        'filesize' => @$filesize,
                        'extension' => @$extension,
                        'created_at' => $attachment->created_at,
                        'source_class' => $type,
                        'source_id' => $attachment->user_id,
                    ]);
                    /*}*/
                }
            }

            $attachments = DB::table('shipments_attachments')->whereNull('deleted_at')->get();
            if($attachments) {
                $type = 'ShipmentAttachment';
                foreach ($attachments as $attachment) {


                    /*if(\File::exists(public_path($attachment->filepath))) {
                        $filesize  = \File::size(public_path($attachment->filepath));*/
                        $extension = \File::extension(public_path($attachment->filepath));

                        DB::table('files_repository')->insert([
                            'parent_id'     => 4,
                            'source'        => config('app.source'),
                            'name'          => $attachment->name,
                            'filepath'      => $attachment->filepath,
                            'filename'      => $attachment->filename,
                            'filehost'      => env('APP_URL'),
                            'filesize'      => @$filesize,
                            'extension'     => @$extension,
                            'created_at'    => $attachment->created_at,
                            'source_class'  => $type,
                            'source_id'     => $attachment->shipment_id,
                            'operator_visible' => $attachment->operator_visible
                        ]);
                    /*}*/
                }
            }

            $attachments = DB::connection('mysql_fleet')->table('fleet_vehicle_attachments')->whereNull('deleted_at')->get();
            if($attachments) {
                $type = 'FleetGest\Vehicle';
                foreach ($attachments as $attachment) {
                    /*if(\File::exists(public_path($attachment->filepath))) {
                        $filesize  = \File::size(public_path($attachment->filepath));*/
                        $extension = \File::extension(public_path($attachment->filepath));

                        DB::table('files_repository')->insert([
                            'parent_id'     => 3,
                            'source'        => config('app.source'),
                            'name'          => $attachment->name,
                            'filepath'      => $attachment->filepath,
                            'filename'      => $attachment->filename,
                            'filehost'      => env('APP_URL'),
                            'filesize'      => @$filesize,
                            'extension'     => @$extension,
                            'created_at'    => $attachment->created_at,
                            'source_class'   => $type,
                            'source_id'     => $attachment->vehicle_id,
                        ]);
                    /*}*/
                }
            }

            Schema::dropIfExists('customers_attachments');
            Schema::dropIfExists('users_attachments');
            Schema::dropIfExists('shipments_attachments');

        } catch (\Exception $e) {
            dd($e->getMessage(). ' on file ' . $e->getFile(). ' Line ' . $e->getLine());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_repository');
    }
}
