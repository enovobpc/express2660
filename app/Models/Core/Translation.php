<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\SoftDeletes;
use File;

class Translation extends \App\Models\BaseModel {

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_core';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_translations';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'system_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'value', 'file', 'locale', 'is_published'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'key' => 'required'
    );

        /**
     * Encontra em sistema todas as traduções e atualiza os ficheiros de tradução
     *
     * @return null|array 
     */
    public static function import2DB($locales = [])
    {
        if(empty($locales)) {
            $locales = array_keys(app_locales()); 
        }
        
        foreach($locales as $locale) {

            $filepath = base_path('resources/lang/'.$locale.'.json');

            $fileContents = File::get($filepath);
            $fileContents = json_decode($fileContents, true);

            if(!empty($fileContents)) {

                
                foreach($fileContents as $key => $value) {

                    $translation = Translation::firstOrNew([
                        'locale' => $locale,
                        'key' => $key
                    ]);

                    $originalValue = $translation->value;
                    $value = $originalValue && empty($value) ? $translation->value : $value;

                    $translation->key   = $key;
                    $translation->value = $value;
                    $translation->is_published = 1;
                    $translation->save();
                }
            }
        }

        return true;
    }

    /**
     * Encontra em sistema todas as traduções e atualiza os ficheiros de tradução
     *
     * @return null|array 
     */
    public static function findTranslations()
    {
        $response = [];

        $directories = [
            base_path('app/Http/Controllers/Admin/'),
            base_path('resources/views/admin/')
        ];

        //percorre todos os ficheiros das diretorias selecionadas 
        //e cria um array com todas as traduções encontradas.
        $transArr = [];
        foreach($directories as $directory) {

            $viewFiles = File::allFiles($directory);
            
            foreach($viewFiles as $file) {
                $fileContent = File::get($file);
    
                if($fileContent) {

                    $pattern = "/(?:__|@trans)\(['\"]([^'\"]+)['\"]/"; //Expressão regular que valida plicas @trans('xxxx' ou __('xxxx

                    preg_match_all($pattern, $fileContent, $matches);
        
                    if (isset($matches[1])) {
                        $transArr = array_merge($transArr, $matches[1]);
                    }
                }
            }

            $transArr = array_unique($transArr);
        }

        //percorre todos os locales da aplicação e sincroniza para cada um 
        //as novas traduções caso existam.
        $locales = app_locales(); //config('app.locales');
      
        $localesArr = [];
        foreach($locales as $locale => $localeName) {

            $filepath = base_path('resources/lang/'.$locale.'.json');

            if(File::exists($filepath)) {
                $transFile  = File::get($filepath);
                $localesArr = json_decode($transFile, true);
                $localesArr = $localesArr ? $localesArr : [];
            } else {
                $localesArr = [];
            }

            $localesArrKeys = array_keys($localesArr); //obtem as keys já existentes
            $newKeys        = array_diff($transArr, $localesArrKeys); //compara as keys já existentes com todas as keys encontradas e devolve apenas as que estão a faltar
            
            //existem chaves novas encontradas
            if(!empty($newKeys)) {

                $fileRows = [];
                foreach($newKeys as $newKey) {
                    $fileRows[$newKey] = '';

                    $translation = Translation::firstOrNew([
                        'key'    => $newKey,
                        'locale' => $locale
                    ]);

                    $translation->key    = $newKey;
                    $translation->locale = $locale;
                    $translation->file   = null;
                    $translation->save();

                }

                /* $newFileContent = array_merge($localesArr, $fileRows); //junta as novas chaves com o ficheiro original
                $newFileContent = json_encode($newFileContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); */

                //File::put($filepath, $newFileContent);

                $response[$locale] = $newKeys;
            }
        }

        return $response;
    }

        /**
     * Encontra em sistema todas as traduções e atualiza os ficheiros de tradução
     *
     * @return null|array 
     */
    public static function publishTranslations($locales = [])
    {

        //percorre todos os locales da aplicação e sincroniza para cada um 
        //as novas traduções caso existam.
        if(empty($locales)) {
            $locales = array_keys(app_locales()); 
        }
        
        foreach($locales as $locale) {

            $filepath = base_path('resources/lang/'.$locale.'.json');

            $translations = Translation::where('locale', $locale)
                                ->orderBy('key', 'asc')
                                ->get(['key', 'value']);

            $fileRows = [];
            foreach($translations as $translation) {
                $fileRows[$translation->key] = $translation->value ?? '';
            }

            $fileContent = json_encode($fileRows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            File::put($filepath, $fileContent);

            Translation::where('locale', $locale)->update(['is_published' => true]);
        }

        return true;
    }

    /**
     * Return ISO 639-1 standard language codes
     *
     * @param $locale
     * @return void
     */
    public static function getISOLocale($locale) {

        if($locale == 'pt' || $locale == 'br') {
            return 'pt-'.$locale;
        } elseif($locale == 'en') {
            return 'en-gb';
        }

        return $locale.'-'.$locale;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when
    | getting or setting them. Simply define a 'getFooAttribute' method on your model
    | to declare an accessor. Keep in mind that the methods should follow camel-casing,
    | even though your database columns are snake-case.
    |
    */
    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = trim($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = empty($value) ? null : trim($value);
    }

}