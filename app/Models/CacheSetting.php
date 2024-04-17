<?php

namespace App\Models;

use DateTime;
use File, DB, Log;

class CacheSetting
{

    public $filename = 'cache_settings.json';

    /**
     * Get variable
     * @param null $settingName
     * @return mixed
     */
    public static function get($settingName = null)
    {
        if($settingName) {
            $dataArr = self::getDataArr();
            return @$dataArr[$settingName];
        }

        return self::getDataArr();
    }

    /**
     * Store or update variable
     *
     * @param null $settingName
     * @return array
     */
    public static function set($varName, $varValue)
    {
        $data = Self::getDataArr();
        $data[$varName] = $varValue;
        return self::storeDataArr($data);
    }

    /**
     * Delete setting variable
     *
     * @param $varName
     * @return mixed
     */
    public static function delete($varName)
    {
        $arr = self::get();
        unset($arr[$varName]);
        return self::storeDataArr($arr);
    }

    /**
     * Get data array
     * @param $dataArr
     * @return mixed
     */
    public static function getDataArr()
    {
        $class = new CacheSetting();
        $filename = $class->filepath();

        try {
            $data = File::get($filename);
            $data = json_decode($data, true);
        } catch (\Exception $e) {
            $data = [];
        }

        return $data;
    }

    /**
     * Store array data
     * @param $dataArr
     * @return mixed
     */
    public static function storeDataArr($dataArr)
    {
        $class = new CacheSetting();
        $filename = $class->filepath();

        return File::put($filename, json_encode($dataArr));
    }

    /**
     * Destroy all cache file
     *
     * @return mixed
     */
    public static function reset()
    {
        $class = new CacheSetting();
        $filename = $class->filepath();
        $result = File::delete($filename);
        return $result;
    }

    /**
     * Return filename
     * @return string
     */
    public function filepath() {
        return storage_path() . '/' . $this->filename;
    }

        /**
     * Syncronize settings from core database
     *
     * @return void
     */
    public static function syncCoreDBSettings() {

        $source = config('app.source');

        try {
            //obtem todas as settings gerais que tenham a variavel com settins_key definida
            $settings = DB::connection('mysql_enovo')
            ->table('setup_settings')
            ->where('key', 'apikey_backoffice')
            ->whereNotNull('settings_key')
            ->where(function($q) use($source) {
                $q->whereNull('source');
                $q->orWhere('source', $source);
            })
            ->orderBy('source', 'desc')
            ->get();


            foreach($settings as $setting) {

                $settingCustomized = $settings->filter(function($item) use($source) {
                    return $item->source == $source;
                })->first();

                if($settingCustomized) {
                    $setting = $settingCustomized; //substitui pela setting persobalizada
                }

                if(empty($setting->value)) {
                    CacheSetting::delete($setting->settings_key);
                } else {
                    CacheSetting::set($setting->settings_key, $setting->value);
                }
            }
        } catch(\Exception $e) {
            $trace = LogViewer::getTrace(null, exceptionMsg($e));
            Log::error(br2nl($trace));

            return false;
        }

        return true;
    }

    public static function getLastUpdate(){
        $class = new CacheSetting();
        $filename = $class->filepath();

        if(File::exists($filename)) {
            $lastmodified = File::lastModified($filename);
            $lastmodified = DateTime::createFromFormat("U", $lastmodified);
            return $lastmodified->format('Y-m-d H:i:s');
        }
        return 'Ficheiro Inexistente.';
    }
}
