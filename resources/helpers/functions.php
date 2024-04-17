<?php

/*
  |--------------------------------------------------------------------------
  | Global Helper Functions
  |--------------------------------------------------------------------------
  |
  | Functions for global access for help the developers.
  |
 */

use App\Models\CacheSetting;
use App\Models\Map;
use App\Models\ZipCode;

/**
 * Return app selected skin
 * @return mixed
 */
function app_skin()
{

    if (Auth::check()) {
        if (Auth::user()->setting('app_skin')) {
            return Auth::user()->setting('app_skin');
        }
    }

    return Setting::get('app_skin', 'skin-orange');
}

/**
 * @return string
 */
function app_email_layout()
{
    return 'layouts.email';
}


/**
 * Return app brand logo
 * @return mixed
 */
function app_brand($returnOption = null, $logoClass = 'm-t-20 h-20px', $logoStyle = '')
{

    $appBrand = env('APP_BRAND');
    $url   = 'https://tms.enovo.pt';
    $image = 'https://enovo.pt/assets/img/logo/enovo-tms.svg';
    $title = 'ENOVO TMS - Software para Transportes e Logística';
    $docsSignature = 'ENOVO TMS - Software Transportes e Logísitica | tms.enovo.pt';

    if ($appBrand == 'transgest') {
        $url  = 'https://transgest.pt/';
        $image = 'https://transgest.pt/assets/img/logo/transgest-tms.svg';
        $title = 'TRANSGEST TMS - Software para transportes e logística';
        $docsSignature = 'TRANSGEST - Software Gestão de Transportes | transgest.pt';
    } elseif ($appBrand == 'transgest-br') {
        $url  = 'https://transgest.com.br';
        $image = 'https://transgest.com.br/assets/img/logo/transgest-tms.svg';
        $title = 'TRANSGEST - Software TMS para transportadoras e logística';
        $docsSignature = 'TRANSGEST TMS - Software Gestão de Transportadoras. | transgest.com.br';
    } elseif ($appBrand == 'quickbox') {
        $url  = 'https://quickbox.pt';
        $image = 'https://software-transportes-logistica.com/assets/img/logo/logo_color.svg';
        $title = 'QUICKBOX - Software TMS para transportes e logística';
        $docsSignature = 'QUICKBOX - Software Gestão de Transportes e Logística. | quickbox.pt';
    }

    if ($returnOption == 'image') {
        return $image;
    } elseif ($returnOption == 'url') {
        return $url;
    } elseif ($returnOption == 'docsignature') {
        return $docsSignature;
    }

    return '<a href="' . $url . '" data-toggle="tooltip" data-placement="left" title="' . $title . '"><img src="' . $image . '" class="' . $logoClass . '" style="' . $logoStyle . '"/></a>';
}

/**
 * Return app notification sound
 * @return mixed
 */
function app_notification_sound()
{

    if (Auth::check()) {
        if (Auth::user()->setting('notification_sound')) {
            return Auth::user()->setting('notification_sound');
        }
    }

    return Setting::get('notification_sound', 'notification09');
}


function app_mode_cargo()
{

    $mode = Setting::get('app_mode');

    if ($mode == 'cargo' || $mode == 'freight') {
        return true;
    }

    return false;
}

function app_mode_courier()
{

    if (Setting::get('app_mode') == 'courier') {
        return true;
    }

    return false;
}

function app_mode_transfers() {

    if(Setting::get('app_mode') == 'transfers') {
        return true;
    }

    return false;
}

/**
 * Return array with app locales
 * @return array
 */
function app_locales()
{
    return trans('locales');
}

/**
 * Return default image
 * @return string
 */
function img_default($thumbnail = false)
{
    if ($thumbnail) {
        return asset('assets/img/default/default.thumb.png');
    }
    return asset('assets/img/default/default.png');
}

/**
 * Return default broken image
 * @return string
 */
function img_broken($thumbnail = false)
{
    if ($thumbnail) {
        return asset('assets/img/default/broken.thumb.png');
    }
    return asset('assets/img/default/broken.png');
}

/**
 * Return setting value by key
 * @param $key
 * @return mixed
 */
function coreUrl($path)
{
    $path = substr($path, 0, 1) === "/" ? substr($path, 1) : $path;
    return config('app.core') . '/' . $path;
}

/**
 * Return all active modules
 *
 * @param [array|string] $module
 * @return bool
 */
function getActiveModules()
{
    $filename = storage_path() . '/framework/modules';
    $modules = file_get_contents($filename);
    $modules = explode(',', $modules);
    return $modules;
}

/**
 * Check if filepath contains the specified extension
 * @param $filepath
 * @param $extension [string|array] extension or array of extensions to compare
 * @return bool
 */
function isExtension($filepath, $extension)
{

    $ext = pathinfo($filepath, PATHINFO_EXTENSION);

    if (is_array($extension)) {
        $exists = false;

        foreach ($extension as $item) {
            if ($item == $ext) {
                $exists = true;
            }
        }

        return $exists;
    } else if ($ext == $extension) {
        return true;
    }

    return false;
}

/**
 * Verifies that the system has permission for a module
 *
 * @param [array|string] $module
 * @return bool
 */
function hasModule($module)
{

    $filename = storage_path() . '/framework/modules';
    $modules = file_get_contents($filename);
    $modules = explode(',', $modules);

    if (is_array($module) && count(array_intersect($module, $modules)) == count($module)) {
        return true;
    } else {
        return in_array($module, $modules) ? true : false;
    }

    return false;
}

/**
 * Check if user has role
 * @param $permissionSlug For more permissions add comma between slugs without space
 * @return mixed
 */
function hasRole($roleSlug)
{
    return Auth::user()->hasRole($roleSlug);
}


/**
 * Check if user has permission
 * @param $permissionSlug For more permissions add comma between slugs without space
 * @return mixed
 */
function hasPermission($permissionSlug)
{
    return Auth::user()->ability(Config::get('permissions.role.admin'), $permissionSlug);
}

/**
 * Validate access to module
 * @param $module
 * @return $this
 */
function validateModule($module)
{
    if (!hasModule($module)) {
        return redirect()->route('admin.denied', ['module' => $module])->send();
    }
}

/**
 * Return html to show tip
 *
 * @param $string
 * @return string
 */
function tip($string)
{
    return '<i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" title="' . $string . '"></i>';
}

/**
 * Return html to show tip
 *
 * @param $string
 * @return string
 */
function knowledgeTip($knowledgeArticle, $string=null)
{
    $string = $string ? $string.'<hr/>' : '';

    $url = knowledgeArticle($knowledgeArticle);
    return '<a href="'.$url.'" target="_blank" class="text-black"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" title="'.$string.'Clique para Saber Mais"></i></a>';
}

/**
 * Return html to show agency tip
 * @param $agency
 * @return string
 */
function agencyTip($agency, $publicView = false)
{

    if (@$agency) {
        $html = '<i class="fas fa-fw fa-envelope"></i> E-mail: <a href="mailto:' . @$agency->email . '">' . @$agency->email . '</a><br/>';

        if ($agency->phone) {
            $html .= '<i class="fas fa-fw fa-phone"></i> Telef.:' . @$agency->phone . '</a><br/>';
        }

        if ($agency->mobile) {
            $html .= '<i class="fas fa-fw fa-mobile"></i> Telem.:' . @$agency->mobile . '</a><br/>';
        }

        if ($agency->phone2) {
            $html .= '<i class="fas fa-fw fa-phone"></i> Telef.:' . @$agency->phone2 . '</a><br/>';
        }

        if ($agency->phone3) {
            $html .= '<i class="fas fa-fw fa-phone"></i> Telef.:' . @$agency->phone3 . '</a><br/>';
        }

        if ($agency->phone4) {
            $html .= '<i class="fas fa-fw fa-phone"></i> Telef.:' . @$agency->phone2 . '</a><br/>';
        }

        $html .= '<hr style="margin: 6px 0;"/>';

        if (@$agency->company) {
            $html .= $agency->company . '<br/>';
        }

        $html .= @$agency->address . '<br/>';
        $html .= @$agency->zip_code . ' ' . @$agency->city . '<br/>';
        return $html;
    }
    return '';
}

/**
 *  Return html to show sms tip
 *
 * @param $counter
 * @return string
 */
function smsTip($counter)
{
    $html = "<div class='text-center'>Enviar notificação SMS.";
    $html .= "<hr class='m-t-5 m-b-5'/>";
    $html .= "Tem <b>" . @$counter . "</b> SMS disponíveis.<br/>";

    if (hasPermission('sms_packs')) {
        $html .= "<a href='" . route('admin.sms.index', ['tab' => 'packs', 'action' => 'new']) . "' target='_blank' class='btn btn-xs btn-default m-t-5'>";
        $html .= "<i class='fas fa-shopping-cart'></i> Adquirir pacotes SMS</a>";
    }

    $html .= '</div>';

    return $html;
}

/**
 * Return google maps api key
 *
 * @param string $level
 * @return string
 */
function getGoogleMapsApiKey($level = 'backoffice') {

    if($level == 'public') {
        return (\App\Models\CacheSetting::get('GOOGLE_MAPS_KEY_PUBLIC') ?? env('GOOGLE_MAPS_KEY_PUBLIC'));
    }

    return (\App\Models\CacheSetting::get('GOOGLE_MAPS_KEY') ?? env('GOOGLE_MAPS_KEY'));
}

/**
 * Return PTV api key
 *
 * @param string $level
 * @return string
 */
function getPTVApiKey($level = 'backoffice') {
    return env('PTV_KEY');
}

/**
 * @param $lat
 * @param $lng
 * @param string $size defines the rectangular dimensions of the map image (horizontal x vertical).
 * @param string $zoom
 * @return string
 */
function static_map($lat, $lng, $size = '600x300', $zoom = "12", $mapType = "roadmap")
{
    return "https://maps.googleapis.com/maps/api/staticmap?center=" . $lat . "," . $lng . "&zoom=" . $zoom . "&size=" . $size . "&maptype=" . $mapType . "&markers=size:normal|" . $lat . "," . $lng . "&format=png&language=pt&scale=2&key=" . getGoogleMapsApiKey();
}

/**
 * Return setting value by key
 * @param $key
 * @return mixed
 */
function setting($key)
{
    return Setting::get($key);
}

/**
 * Insert filter value on datatable filters
 * @param $var
 * @param $defaultValue
 */
function fltr_val($request, $varname, $defaultValue = null)
{
    return isset($request[$varname]) ? $request[$varname] : $defaultValue;
}

/**
 * Remove all spaces on string
 *
 * @param $str
 * @return mixed
 */
function nospace($str)
{
    return str_replace(' ', '', trim($str));
}

/**
 * Returns money as a formatted string
 *
 * @param integer $amount
 * @param string $symbol currency symbol
 * @return string
 */
function money($amount, $symbol = '', $decimals = 2)
{
    $amount = (float) $amount;
    return is_null($amount) ? null : number_format($amount, $decimals, ',', '.') . $symbol;
}

/**
 * Return a number formated to 2 decimals
 *
 * @param $number
 * @param int $decimals
 * @return string
 */
function number($number, $decimals = 2, $forceDecimal = false)
{
    $number = (float) $number;

    $number = number_format($number, $decimals, '.', '');

    if ($forceDecimal) {
        return (float) $number;
    }

    return $number;
}

/**
 * Force replacement of "," by "." on decimal values
 *
 * @param integer $value
 * @return float
 */
function forceDecimal($value)
{
    return (float) str_replace(',', '.', trim($value));
}

/**
 * returns number format to excel files
 *
 * @param integer $amount
 * @param string $symbol currency symbol
 * @return string
 */
function excelNumber($value, $decimals = 2)
{
    return is_null($value) ? null : number_format($value, $decimals, '.', '');
}

/**
 * Round fractions up
 *
 * @param [array|string] $value
 * @return integer
 */
function roundUp($value)
{
    return ceil($value);
}

/**
 * Round fractions down
 *
 * @param [array|string] $value
 * @return integer
 */
function roundDown($value)
{
    return floor($value);
}


/**
 * Create trackingcode [baseado no algoritmo mb]
 * @param $agencyId
 * @param $shipmentId
 * @return string
 */
function trk_algorithm($agencyId, $shipmentId)
{

    $agency   = str_pad($agencyId, 3, STR_PAD_LEFT, '0'); //3 chars
    $shipment = str_pad($shipmentId, 7, STR_PAD_LEFT, '0'); //7 chars

    $code = time() . $agency . $shipment; //20 chars

    $multiplicate = [51, 73, 17, 89, 38, 62, 45, 53, 15, 50, 5, 49, 34, 81, 76, 27, 90, 9, 30, 3];

    $sum = 0;
    foreach ($multiplicate as $key => $val) {
        $sum += $val * $code[$key];
    }

    $checkdigit = 98 - ($sum % 97);

    $checkdigit = $checkdigit < 10 ? '0' . $checkdigit : $checkdigit;

    $trk = $agency . '0' . substr($shipment, 1) . $checkdigit;

    return $trk;
}

/**
 * Calculate checkdigit for one value
 * @param $number
 * @param bool $mod5 [if false, use mod10]
 * @return bool
 */
function luhn_algorithm($number)
{

    $sumTable = array(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), array(0, 2, 4, 6, 8, 1, 3, 5, 7, 9));

    $length = strlen($number);
    $sum = 0;
    $flip = 1;
    // Sum digits (last one is check digit, which is not in parameter)
    for ($i = $length - 1; $i >= 0; --$i) $sum += $sumTable[$flip++ & 0x1][$number[$i]];
    // Multiply by 9
    $sum *= 9;
    // Last digit of sum is check digit
    return (int)substr($sum, -1, 1);
}


function validate_luhn_algorithm($number)
{
    settype($number, 'string');
    $sumTable = array(
        array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
        array(0, 2, 4, 6, 8, 1, 3, 5, 7, 9)
    );
    $sum = 0;
    $flip = 0;
    for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $sum += $sumTable[$flip++ & 0x1][$number[$i]];
    }
    return $sum % 10 === 0;
}

/**
 * Find a specific word in a string
 *
 * @param $needle
 * @param $string
 */
function in_string($needle, $string)
{
    if (strpos($string, $needle) !== false) {
        return true;
    }
    return false;
}

/**
 * Return a new array with diference between 2 arrays
 * @param $aArray1
 * @param $aArray2
 * @return array
 */
function array_diff_recursive($aArray1, $aArray2)
{
    $aReturn = array();

    foreach ($aArray1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                if (count($aRecursiveDiff)) {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
            } else {
                if ($mValue != $aArray2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
            }
        } else {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
}

/**
 * Remove value from array
 *
 * @param $array
 * @param $value
 * @return mixed
 */
function array_remove_val(&$array, $value)
{
    if (($key = array_search($value, $array)) !== false) {
        unset($array[$key]);
    }

    return $array;
}

/**
 * Trim data ignoring if array contain array values
 * @param $data
 * @return array|null|string
 */
function trim_data($data)
{
    if ($data == null)
        return null;

    if (is_array($data)) {
        return array_map('trim_data', $data);
    } else return trim($data);
}


/**
 * Convert bytes to readable
 * @param $size
 * @return string
 */
function human_filesize2($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

/**
 * Convert filesize in bytes to human filesize
 *
 * @param decimal $bytes
 * @param integer $decimals
 * @return string
 */
function human_filesize($bytes, $decimals = 2)
{
    for ($i = 0; ($bytes / 1024) > 0.9; $i++, $bytes /= 1024) {
    }
    return round($bytes, $decimals) . ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
}

/**
 * Convert a datetime to a readable string
 * @param $datetime
 * @param bool $full
 * @return string
 */
function human_time($datetime, $full = false, $abbrv = false, $endDate = null)
{
    return timeElapsedString($datetime, $full, $abbrv, $endDate);
}

function timeElapsedString($datetime, $full = false, $abbrv = false, $endDate = null)
{

    $now = new DateTime;

    if ($endDate) {
        $now = new DateTime($endDate);
    }

    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'anos',
        'm' => 'meses',
        'w' => 'semanas',
        'd' => 'dias',
        'h' => 'horas',
        'i' => 'minutos',
        's' => 'segundos',
    );

    $stringSingular = array(
        'y' => 'ano',
        'm' => 'mês',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );

    if ($abbrv) {
        $string = array(
            'y' => 'a',
            'm' => 'm',
            'w' => 's',
            'd' => 'd',
            'h' => 'h',
            'i' => 'm',
            's' => 's',
        );

        $stringSingular = $string;
    }

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k > 1 ? $v : $stringSingular[$k];
            $v = $diff->$k . ($abbrv ? '' : ' ') . $v;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . '' : 'agora';
}

/**
 * Convert date to human readable text
 *
 * @param decimal $bytes
 * @param integer $decimals
 * @return string
 */
function human_date($date, $defaultFormat = 'l, d F')
{
    $date = $date . ' 23:59:00';
    $date = new Jenssegers\Date\Date($date);
    $today = new Jenssegers\Date\Date();

    $diff = $today->diffInDays($date);
    $future = $date->gt($today);

    switch ($diff) {
        case 0:
            return "Hoje";
            break;
        case 1:
            return $future ? "Amanhã" : "Ontem";
            break;
        case 2:
            return $future ? "Depois de Amanhã" : "Anteontem";
            break;
        default:
            return $date->format($defaultFormat);
    }
}

/**
 * Convert DMS coordenates to decimal coordenates
 *
 * @param string $dms DMS coordenates
 * @return decimal
 */
function DMStoDEC($dms)
{

    $dms = explode('º', $dms);
    if (empty($dms[0]) || empty($dms[1])) {
        return 0;
    }

    $deg = trim($dms[0]);
    $dms = explode("'", $dms[1]);
    $min = trim(@$dms[0]);
    $dms = explode('"', @$dms[1]);
    $sec = trim(@$dms[0]);
    $cardial = strtolower(trim(@$dms[1]));
    $signal = null;

    if ($cardial == 's' || $cardial == 'w') {
        $signal = '-';
    }

    return $signal . round($deg + ((($min * 60) + ($sec)) / 3600), 6);
}

/**
 * Converts decimal longitude / latitude to DMS
 *
 * @param type $dec
 * @return array
 */
function DECtoDMS($dec)
{

    $vars = explode(".", $dec);
    $deg = $vars[0];
    $tempma = "0." . $vars[1];

    $tempma = $tempma * 3600;
    $min = floor($tempma / 60);
    $sec = $tempma - ($min * 60);

    return array("deg" => $deg, "min" => $min, "sec" => $sec);
}

/**
 * Return google maps cordinates from a given address
 *
 * @param type $address
 * @return type
 */
function getCoordinatesFromAddress($address)
{

    //replace all the white space with "+" sign to match with google search pattern
    $address = str_replace(" ", "+", strtolower($address));
    $address = str_replace("º", '', $address);
    $address = str_replace("ª", '', $address);

    $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
    $response = file_get_contents($url);

    //generate array object from the response from the web
    $json = json_decode($response, TRUE);

    if (!empty(@$json['error_message'])) {
        return $json;
    }

    $result = [
        'lat' => $json['results'][0]['geometry']['location']['lat'],
        'lng' => $json['results'][0]['geometry']['location']['lng']
    ];

    return $result;
}

/**
 * Return vat value
 *
 * @param decimal $price
 * @param int $tax
 * @return type
 */
function getVat($price, $vat = null)
{

    if (is_null($vat)) {
        $vat = Setting::get('vat_rate_normal');
    }

    return ($price * $vat) / 100;
}

/**
 * Validate vat PT
 * @param $vat
 * @return bool
 */
function validateVatPT($vat)
{
    //Verificar se é um numero e se é composto exactamente por 9 digitos
    if (!is_numeric($vat) || strlen($vat) != 9) {
        return false;
    }


    $narray = str_split($vat);

    //verificar se o primeiro digito é valido. O primeiro digito indica o tipo de contribuinte.
    if ($narray[0] != 1 && $narray[0] != 2 && $narray[0] != 5 && $narray[0] != 6 && $narray[0] != 8 && $narray[0] != 9) {
        return false;
    }


    $checkbit = $narray[0] * 9;

    for ($i = 2; $i <= 8; $i++) {
        $checkbit += $vat[$i - 1] * (10 - $i);
    }

    $checkbit = 11 - ($checkbit % 11);

    if ($checkbit >= 10) $checkbit = 0;

    if ($vat[8] == $checkbit) {
        return true;
    }

    return false;
}

/**
 * Return value with vat
 *
 * @param decimal $price
 * @param int $tax
 * @return type
 */
function valueWithVat($price, $vat = null)
{

    if (is_null($vat)) {
        $vat = Setting::get('vat_rate_normal') / 100;
    } else {
        $vat = $vat / 100;
    }

    return $price * (1 + $vat);
}

/**
 * returns percentage of two values
 *
 * @param type $total
 * @param type $value
 * @param type $precision
 * @return type
 */
function percent($total, $value, $precision = 0)
{
    return empty($total) ? round(0, $precision) : round(($value * 100) / $total, $precision);
}

/**
 * returns percentage diference between two values
 *
 *
 * @param decimal $oldValue
 * @param decimal $newValue
 * @param int $precision
 * @return int
 */
function percentVariation($oldValue, $newValue, $precision = 0)
{

    if (!$oldValue) {
        return 100;
    }

    $variation = (($newValue / $oldValue) - 1) * 100;

    return round($variation, $precision);
}

/**
 * Return age from a given birthdate
 *
 * @param type $birthdate
 * @return type
 */
function age($birthdate)
{
    return date_diff(date_create($birthdate), date_create('now'))->y;
}

/**
 * Convert br tag to nl
 *
 * @param string $string
 * @return string
 */
function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

/**
 * Validate PT NIF
 * @param type $nif
 * @return boolean
 */
function validarNIF($nif)
{

    $nif = str_replace('PT', '', $nif);

    if ((!is_null($nif)) && (is_numeric($nif)) && (strlen($nif) == 9) && ($nif[0] == 1 || $nif[0] == 2 || $nif[0] == 5 || $nif[0] == 6 || $nif[0] == 8 || $nif[0] == 9)) {
        $dC = $nif[0] * 9;
        for ($i = 2; $i <= 8; $i++)
            $dC += ($nif[$i - 1]) * (10 - $i);
        $dC = 11 - ($dC % 11);
        $dC = ($dC >= 10) ? 0 : $dC;
        if ($dC == $nif[8])
            return TRUE;
    }
}

/**
 * Return array of years
 *
 * @param type $max max year
 * @param type $min min year
 * @return array
 */
function yearsArr($min = 1970, $max = null, $inverse = false)
{

    if (empty($max)) {
        $max = date('Y');
    }

    $arr = array();
    for ($i = $min; $i <= $max; $i++) {
        $arr += array($i => $i);
    }

    if ($inverse) {
        $arr = array_reverse($arr, true);
    }
    return $arr;
}

/**
 * Return all days of year
 * @return array
 */
function getYearDaysArr()
{

    $arr = [];

    $monthDays = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    foreach (trans('datetime.list-month') as $month => $monthName) {
        for ($day = 1; $day <= $monthDays[$month - 1]; $day++) {
            $dayStr = $day < 10 ? '0' . $day : $day;
            $key    = $month . '-' . $dayStr;
            $value  = $dayStr . ' ' . $monthName;

            $arr[$key] = $value;
        }
    }

    return $arr;
}

/**
 * Return array of numbers
 *
 * @param type $max max year
 * @param type $min min year
 * @return array
 */
function listArr($min, $max, $inverse = false)
{

    $arr = array();
    for ($i = $min; $i <= $max; $i++) {
        $arr += array($i => $i);
    }

    if ($inverse) {
        $arr = array_reverse($arr, true);
    }
    return $arr;
}

/**
 * The function returns the no. of business days between two dates and it skips the holidays
 *
 * @param type $startDate
 * @param type $endDate
 * @param type $holidays
 * @return int
 */
function getWorkingDays($startDate, $endDate, $holidays = array())
{
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week)
            $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week)
            $no_remaining_days--;
    } else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)
        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        } else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
    //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
    $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0) {
        $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach ($holidays as $holiday) {
        $time_stamp = strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N", $time_stamp) != 6 && date("N", $time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}

/**
 * Faz o mapeamento de nomes de um array
 *
 * @param array $source array com os nomes e seus valores
 * @param array $target array com os nomes e nome respondente local
 * @return array array mapeado
 * @version 2 version used in new platform
 */
function mapArrayKeys(array $source, array $target)
{

    $mapArray = array();

    foreach ($target as $sourceKey => $targetKey) {

        $value = isset($source[$sourceKey]) ? $source[$sourceKey] : '';

        $mapArray[$targetKey] = is_string($value) ? trim($value) : $value;
    }

    return $mapArray;
}

/**
 * Filter array by given keys
 * @param $arrayToFilter
 * @param $arrayOfKeys
 * @return array
 */
function array_filter_keys($arrayToFilter, $arrayOfKeys)
{
    return array_intersect_key($arrayToFilter, array_flip($arrayOfKeys));
}
/**
 * Separe a string and add a character each count character
 *
 * @param type $string
 * @param type $count
 * @param type $separator
 * @return type
 */
function str_separe($string, $count = 3, $separator = ' ')
{
    return trim(chunk_split($string, $count, $separator));
}


/**
 * Split name
 * @param $name
 * @param bool $returnStr
 * @return array|bool|string
 */
function split_name($name, $returnStr = true)
{

    $name = trim($name);
    $name = explode(' ', $name);

    $count = count($name);

    $name = [
        'first_name' => @$name[0],
        'last_name'  => $count > 1 ? @$name[$count - 1] : '',
    ];

    if ($returnStr) {
        return $name['first_name'] . ' ' . $name['last_name'];
    }
    return $name;
}

/**
 * Return last name
 * @param $name
 * @param bool $returnStr
 * @return mixed|string
 */
function last_name($name, $returnStr = true)
{
    $name = explode(' ', trim($name));
    $elements = count($name);
    return $name[$elements-1];
}

/**
 * Convert a string in sentense case
 * @param $str
 * @return string
 */
function sentence_case($str, $prepositions = [])
{

    if (empty($prepositions)) {
        $prepositions = [
            'a', 'e', 'o', 'as', 'os',
            'de', 'da', 'das', 'do', 'dos',
            'ante', 'após', 'apos', 'até',  'ate',
            'com', 'contra', 'desde', 'em', 'entre',
            'para', 'perante', 'por', 'sem', 'sob',
            'ao', 'à', 'aos', 'às', 'á', 'ás', 'as',
            'por', 'no', 'nos', 'na', 'nas', 'um', 'uma', 'uns', 'umas',
            'que'
        ];
    }

    // Let's split our string into an array of words
    $words = explode(' ', $str);
    foreach ($words as &$word) {

        // ignora palavras todas escritas em maiúsculas
        if ($word == mb_strtoupper($word, 'UTF-8')) {
            continue;
        }

        // Coloca em maiúscula a primeira letra de cada palavra.

        if (in_array($word, $prepositions)) {
            $word = mb_strtolower($word, 'UTF-8');
        } else {
            $word = ucfirst(mb_strtolower($word, 'UTF-8'));
        }
    }

    // Join the individual words back into a string
    return implode(' ', $words);
}

/**
 * Replate all Hex characters with the corresponding utf-8 character
 * @param $string
 */
function str_Hex2Utf8($string)
{

    $hexChars = [
        '&#x21;' => '!',
        '&#x22;' => '"',
        '&#x23;' => '#',
        '&#x24;' => '$',
        '&#x25;' => '%',
        '&#x26;' => '&',
        '&#x28;' => '(',
        '&#x29;' => ')',
        '&#x2a;' => '*',
        '&#x2b;' => '+',
        '&#x2c;' => ',',
        '&#x2d;' => '-',
        '&#x2e;' => '.',
        '&#x2f;' => '/',
        '&#x30;' => '0',
        '&#x31;' => '1',
        '&#x32;' => '2',
        '&#x33;' => '3',
        '&#x34;' => '4',
        '&#x35;' => '5',
        '&#x36;' => '6',
        '&#x37;' => '7',
        '&#x38;' => '8',
        '&#x39;' => '9',
        '&#x3a;' => ':',
        '&#x3b;' => ';',
        '&#x3c;' => '<',
        '&#x3d;' => '=',
        '&#x3e;' => '>',
        '&#x3f;' => '?',
        '&#x40;' => '@',
        '&#x41;' => 'A',
        '&#x42;' => 'B',
        '&#x43;' => 'C',
        '&#x44;' => 'D',
        '&#x45;' => 'E',
        '&#x46;' => 'F',
        '&#x47;' => 'G',
        '&#x48;' => 'H',
        '&#x49;' => 'I',
        '&#x4a;' => 'J',
        '&#x4b;' => 'K',
        '&#x4c;' => 'L',
        '&#x4d;' => 'M',
        '&#x4e;' => 'N',
        '&#x4f;' => 'O',
        '&#x50;' => 'P',
        '&#x51;' => 'Q',
        '&#x52;' => 'R',
        '&#x53;' => 'S',
        '&#x54;' => 'T',
        '&#x55;' => 'U',
        '&#x56;' => 'V',
        '&#x57;' => 'W',
        '&#x58;' => 'X',
        '&#x59;' => 'Y',
        '&#x5a;' => 'Z',
        '&#x5b;' => '[',
        '&#x5c;' => ' ',
        '&#x5d;' => ']',
        '&#x5e;' => '^',
        '&#x5f;' => '_',
        '&#x60;' => '`',
        '&#x61;' => 'a',
        '&#x62;' => 'b',
        '&#x63;' => 'c',
        '&#x64;' => 'd',
        '&#x65;' => 'e',
        '&#x66;' => 'f',
        '&#x67;' => 'g',
        '&#x68;' => 'h',
        '&#x69;' => 'i',
        '&#x6a;' => 'j',
        '&#x6b;' => 'k',
        '&#x6c;' => 'l',
        '&#x6d;' => 'm',
        '&#x6e;' => 'n',
        '&#x6f;' => 'o',
        '&#x70;' => 'p',
        '&#x71;' => 'q',
        '&#x72;' => 'r',
        '&#x73;' => 's',
        '&#x74;' => 't',
        '&#x75;' => 'u',
        '&#x76;' => 'v',
        '&#x77;' => 'w',
        '&#x78;' => 'x',
        '&#x79;' => 'y',
        '&#x7a;' => 'z',
        '&#x7b;' => '{',
        '&#x7c;' => '|',
        '&#x7d;' => '}',
        '&#x7e;' => '~',
        '&#x80;' => '',
        '&#x81;' => '',
        '&#x8d;' => '',
        '&#x8e;' => '',
        '&#x8f;' => '',
        '&#x90;' => '',
        '&#x9d;' => '',
        '&#x9e;' => '',
        '&#xa1;' => '¡',
        '&#xa2;' => '¢',
        '&#xa3;' => '£',
        '&#xa4;' => '¤',
        '&#xa5;' => '¥',
        '&#xa6;' => '¦',
        '&#xa7;' => '§',
        '&#xa8;' => '¨',
        '&#xa9;' => '©',
        '&#xaa;' => 'ª',
        '&#xab;' => '«',
        '&#xac;' => '¬',
        '&#xad;' => '­',
        '&#xae;' => '®',
        '&#xaf;' => '¯',
        '&#xb0;' => '°',
        '&#xb1;' => '±',
        '&#xb2;' => '²',
        '&#xb3;' => '³',
        '&#xb4;' => '´',
        '&#xb5;' => 'µ',
        '&#xb6;' => '¶',
        '&#xb7;' => '·',
        '&#xb8;' => '¸',
        '&#xb9;' => '¹',
        '&#xba;' => 'º',
        '&#xbb;' => '»',
        '&#xbc;' => '¼',
        '&#xbd;' => '½',
        '&#xbe;' => '¾',
        '&#xbf;' => '¿',
        '&#xc0;' => 'À',
        '&#xc1;' => 'Á',
        '&#xc2;' => 'Â',
        '&#xc3;' => 'Ã',
        '&#xc4;' => 'Ä',
        '&#xc5;' => 'Å',
        '&#xc6;' => 'Æ',
        '&#xc7;' => 'Ç',
        '&#xc8;' => 'È',
        '&#xc9;' => 'É',
        '&#xcb;' => 'Ë',
        '&#xcc;' => 'Ì',
        '&#xcd;' => 'Í',
        '&#xce;' => 'Î',
        '&#xcf;' => 'Ï',
        '&#xd0;' => 'Ð',
        '&#xd1;' => 'Ñ',
        '&#xd2;' => 'Ò',
        '&#xd3;' => 'Ó',
        '&#xd4;' => 'Ô',
        '&#xd5;' => 'Õ',
        '&#xd6;' => 'Ö',
        '&#xd7;' => '×',
        '&#xd8;' => 'Ø',
        '&#xd9;' => 'Ù',
        '&#xda;' => 'Ú',
        '&#xdb;' => 'Û',
        '&#xdc;' => 'Ü',
        '&#xdd;' => 'Ý',
        '&#xde;' => 'Þ',
        '&#xdf;' => 'ß',
        '&#xe0;' => 'à',
        '&#xe1;' => 'á',
        '&#xe2;' => 'â',
        '&#xe3;' => 'ã',
        '&#xe4;' => 'ä',
        '&#xe5;' => 'å',
        '&#xe6;' => 'æ',
        '&#xe7;' => 'ç',
        '&#xe8;' => 'è',
        '&#xe9;' => 'é',
        '&#xea;' => 'ê',
        '&#xeb;' => 'ë',
        '&#xec;' => 'ì',
        '&#xed;' => 'í',
        '&#xee;' => 'î',
        '&#xef;' => 'ï',
        '&#xf0;' => 'ð',
        '&#xf1;' => 'ñ',
        '&#xf2;' => 'ò',
        '&#xf3;' => 'ó',
        '&#xf4;' => 'ô',
        '&#xf5;' => 'õ',
        '&#xf6;' => 'ö',
        '&#xf7;' => '÷',
        '&#xf8;' => 'ø',
        '&#xf9;' => 'ù',
        '&#xfa;' => 'ú',
        '&#xfb;' => 'û',
        '&#xfc;' => 'ü',
        '&#xfd;' => 'ý',
        '&#xfe;' => 'þ',
        '&#xff;' => 'ÿ'
    ];

    foreach ($hexChars as $hexChar => $symbol) {
        $string = str_replace($hexChar, $symbol, $string);
    }

    return $string;
}

/**
 * Random color
 * @return string
 */
function rand_color()
{
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}


/**
 * Encrypt email
 * @param $email
 * @return string
 */
function cryptedmail($email)
{

    $parts = explode('@', "$email");

    $name   = $parts[0];
    $domain = $parts[1];

    $parts  = explode('.', $domain);
    $domain = $parts[0];
    $tld    = $parts[1];

    return '<span data-name="' . $name . '" data-domain="' . $domain . '" data-tld="' . $tld . '"></span>';
}

/**
 * Format phone numbers
 * @param $string
 * @return mixed
 */
function formatPhone($string)
{
    $string = trim($string);
    return preg_replace('/[^0-9+]/', '', $string);
}

/**
 * Convert phone indicative 2 country
 * @param $phoneIndicative
 * @return string|null
 */
function convertPhoneIndicative2Country($phoneIndicative) {

    $phoneIndicative = str_replace('+', '', $phoneIndicative);

    $countryArray = array(
        'AD'=>array('name'=>'ANDORRA','code'=>'376'),
        'AE'=>array('name'=>'UNITED ARAB EMIRATES','code'=>'971'),
        'AF'=>array('name'=>'AFGHANISTAN','code'=>'93'),
        'AG'=>array('name'=>'ANTIGUA AND BARBUDA','code'=>'1268'),
        'AI'=>array('name'=>'ANGUILLA','code'=>'1264'),
        'AL'=>array('name'=>'ALBANIA','code'=>'355'),
        'AM'=>array('name'=>'ARMENIA','code'=>'374'),
        'AN'=>array('name'=>'NETHERLANDS ANTILLES','code'=>'599'),
        'AO'=>array('name'=>'ANGOLA','code'=>'244'),
        'AQ'=>array('name'=>'ANTARCTICA','code'=>'672'),
        'AR'=>array('name'=>'ARGENTINA','code'=>'54'),
        'AS'=>array('name'=>'AMERICAN SAMOA','code'=>'1684'),
        'AT'=>array('name'=>'AUSTRIA','code'=>'43'),
        'AU'=>array('name'=>'AUSTRALIA','code'=>'61'),
        'AW'=>array('name'=>'ARUBA','code'=>'297'),
        'AZ'=>array('name'=>'AZERBAIJAN','code'=>'994'),
        'BA'=>array('name'=>'BOSNIA AND HERZEGOVINA','code'=>'387'),
        'BB'=>array('name'=>'BARBADOS','code'=>'1246'),
        'BD'=>array('name'=>'BANGLADESH','code'=>'880'),
        'BE'=>array('name'=>'BELGIUM','code'=>'32'),
        'BF'=>array('name'=>'BURKINA FASO','code'=>'226'),
        'BG'=>array('name'=>'BULGARIA','code'=>'359'),
        'BH'=>array('name'=>'BAHRAIN','code'=>'973'),
        'BI'=>array('name'=>'BURUNDI','code'=>'257'),
        'BJ'=>array('name'=>'BENIN','code'=>'229'),
        'BL'=>array('name'=>'SAINT BARTHELEMY','code'=>'590'),
        'BM'=>array('name'=>'BERMUDA','code'=>'1441'),
        'BN'=>array('name'=>'BRUNEI DARUSSALAM','code'=>'673'),
        'BO'=>array('name'=>'BOLIVIA','code'=>'591'),
        'BR'=>array('name'=>'BRAZIL','code'=>'55'),
        'BS'=>array('name'=>'BAHAMAS','code'=>'1242'),
        'BT'=>array('name'=>'BHUTAN','code'=>'975'),
        'BW'=>array('name'=>'BOTSWANA','code'=>'267'),
        'BY'=>array('name'=>'BELARUS','code'=>'375'),
        'BZ'=>array('name'=>'BELIZE','code'=>'501'),
        'CA'=>array('name'=>'CANADA','code'=>'1'),
        'CC'=>array('name'=>'COCOS (KEELING) ISLANDS','code'=>'61'),
        'CD'=>array('name'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE','code'=>'243'),
        'CF'=>array('name'=>'CENTRAL AFRICAN REPUBLIC','code'=>'236'),
        'CG'=>array('name'=>'CONGO','code'=>'242'),
        'CH'=>array('name'=>'SWITZERLAND','code'=>'41'),
        'CI'=>array('name'=>'COTE D IVOIRE','code'=>'225'),
        'CK'=>array('name'=>'COOK ISLANDS','code'=>'682'),
        'CL'=>array('name'=>'CHILE','code'=>'56'),
        'CM'=>array('name'=>'CAMEROON','code'=>'237'),
        'CN'=>array('name'=>'CHINA','code'=>'86'),
        'CO'=>array('name'=>'COLOMBIA','code'=>'57'),
        'CR'=>array('name'=>'COSTA RICA','code'=>'506'),
        'CU'=>array('name'=>'CUBA','code'=>'53'),
        'CV'=>array('name'=>'CAPE VERDE','code'=>'238'),
        'CX'=>array('name'=>'CHRISTMAS ISLAND','code'=>'61'),
        'CY'=>array('name'=>'CYPRUS','code'=>'357'),
        'CZ'=>array('name'=>'CZECH REPUBLIC','code'=>'420'),
        'DE'=>array('name'=>'GERMANY','code'=>'49'),
        'DJ'=>array('name'=>'DJIBOUTI','code'=>'253'),
        'DK'=>array('name'=>'DENMARK','code'=>'45'),
        'DM'=>array('name'=>'DOMINICA','code'=>'1767'),
        'DO'=>array('name'=>'DOMINICAN REPUBLIC','code'=>'1809'),
        'DZ'=>array('name'=>'ALGERIA','code'=>'213'),
        'EC'=>array('name'=>'ECUADOR','code'=>'593'),
        'EE'=>array('name'=>'ESTONIA','code'=>'372'),
        'EG'=>array('name'=>'EGYPT','code'=>'20'),
        'ER'=>array('name'=>'ERITREA','code'=>'291'),
        'ES'=>array('name'=>'SPAIN','code'=>'34'),
        'ET'=>array('name'=>'ETHIOPIA','code'=>'251'),
        'FI'=>array('name'=>'FINLAND','code'=>'358'),
        'FJ'=>array('name'=>'FIJI','code'=>'679'),
        'FK'=>array('name'=>'FALKLAND ISLANDS (MALVINAS)','code'=>'500'),
        'FM'=>array('name'=>'MICRONESIA, FEDERATED STATES OF','code'=>'691'),
        'FO'=>array('name'=>'FAROE ISLANDS','code'=>'298'),
        'FR'=>array('name'=>'FRANCE','code'=>'33'),
        'GA'=>array('name'=>'GABON','code'=>'241'),
        'GB'=>array('name'=>'UNITED KINGDOM','code'=>'44'),
        'GD'=>array('name'=>'GRENADA','code'=>'1473'),
        'GE'=>array('name'=>'GEORGIA','code'=>'995'),
        'GH'=>array('name'=>'GHANA','code'=>'233'),
        'GI'=>array('name'=>'GIBRALTAR','code'=>'350'),
        'GL'=>array('name'=>'GREENLAND','code'=>'299'),
        'GM'=>array('name'=>'GAMBIA','code'=>'220'),
        'GN'=>array('name'=>'GUINEA','code'=>'224'),
        'GQ'=>array('name'=>'EQUATORIAL GUINEA','code'=>'240'),
        'GR'=>array('name'=>'GREECE','code'=>'30'),
        'GT'=>array('name'=>'GUATEMALA','code'=>'502'),
        'GU'=>array('name'=>'GUAM','code'=>'1671'),
        'GW'=>array('name'=>'GUINEA-BISSAU','code'=>'245'),
        'GY'=>array('name'=>'GUYANA','code'=>'592'),
        'HK'=>array('name'=>'HONG KONG','code'=>'852'),
        'HN'=>array('name'=>'HONDURAS','code'=>'504'),
        'HR'=>array('name'=>'CROATIA','code'=>'385'),
        'HT'=>array('name'=>'HAITI','code'=>'509'),
        'HU'=>array('name'=>'HUNGARY','code'=>'36'),
        'ID'=>array('name'=>'INDONESIA','code'=>'62'),
        'IE'=>array('name'=>'IRELAND','code'=>'353'),
        'IL'=>array('name'=>'ISRAEL','code'=>'972'),
        'IM'=>array('name'=>'ISLE OF MAN','code'=>'44'),
        'IN'=>array('name'=>'INDIA','code'=>'91'),
        'IQ'=>array('name'=>'IRAQ','code'=>'964'),
        'IR'=>array('name'=>'IRAN, ISLAMIC REPUBLIC OF','code'=>'98'),
        'IS'=>array('name'=>'ICELAND','code'=>'354'),
        'IT'=>array('name'=>'ITALY','code'=>'39'),
        'JM'=>array('name'=>'JAMAICA','code'=>'1876'),
        'JO'=>array('name'=>'JORDAN','code'=>'962'),
        'JP'=>array('name'=>'JAPAN','code'=>'81'),
        'KE'=>array('name'=>'KENYA','code'=>'254'),
        'KG'=>array('name'=>'KYRGYZSTAN','code'=>'996'),
        'KH'=>array('name'=>'CAMBODIA','code'=>'855'),
        'KI'=>array('name'=>'KIRIBATI','code'=>'686'),
        'KM'=>array('name'=>'COMOROS','code'=>'269'),
        'KN'=>array('name'=>'SAINT KITTS AND NEVIS','code'=>'1869'),
        'KP'=>array('name'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF','code'=>'850'),
        'KR'=>array('name'=>'KOREA REPUBLIC OF','code'=>'82'),
        'KW'=>array('name'=>'KUWAIT','code'=>'965'),
        'KY'=>array('name'=>'CAYMAN ISLANDS','code'=>'1345'),
        'KZ'=>array('name'=>'KAZAKSTAN','code'=>'7'),
        'LA'=>array('name'=>'LAO PEOPLES DEMOCRATIC REPUBLIC','code'=>'856'),
        'LB'=>array('name'=>'LEBANON','code'=>'961'),
        'LC'=>array('name'=>'SAINT LUCIA','code'=>'1758'),
        'LI'=>array('name'=>'LIECHTENSTEIN','code'=>'423'),
        'LK'=>array('name'=>'SRI LANKA','code'=>'94'),
        'LR'=>array('name'=>'LIBERIA','code'=>'231'),
        'LS'=>array('name'=>'LESOTHO','code'=>'266'),
        'LT'=>array('name'=>'LITHUANIA','code'=>'370'),
        'LU'=>array('name'=>'LUXEMBOURG','code'=>'352'),
        'LV'=>array('name'=>'LATVIA','code'=>'371'),
        'LY'=>array('name'=>'LIBYAN ARAB JAMAHIRIYA','code'=>'218'),
        'MA'=>array('name'=>'MOROCCO','code'=>'212'),
        'MC'=>array('name'=>'MONACO','code'=>'377'),
        'MD'=>array('name'=>'MOLDOVA, REPUBLIC OF','code'=>'373'),
        'ME'=>array('name'=>'MONTENEGRO','code'=>'382'),
        'MF'=>array('name'=>'SAINT MARTIN','code'=>'1599'),
        'MG'=>array('name'=>'MADAGASCAR','code'=>'261'),
        'MH'=>array('name'=>'MARSHALL ISLANDS','code'=>'692'),
        'MK'=>array('name'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','code'=>'389'),
        'ML'=>array('name'=>'MALI','code'=>'223'),
        'MM'=>array('name'=>'MYANMAR','code'=>'95'),
        'MN'=>array('name'=>'MONGOLIA','code'=>'976'),
        'MO'=>array('name'=>'MACAU','code'=>'853'),
        'MP'=>array('name'=>'NORTHERN MARIANA ISLANDS','code'=>'1670'),
        'MR'=>array('name'=>'MAURITANIA','code'=>'222'),
        'MS'=>array('name'=>'MONTSERRAT','code'=>'1664'),
        'MT'=>array('name'=>'MALTA','code'=>'356'),
        'MU'=>array('name'=>'MAURITIUS','code'=>'230'),
        'MV'=>array('name'=>'MALDIVES','code'=>'960'),
        'MW'=>array('name'=>'MALAWI','code'=>'265'),
        'MX'=>array('name'=>'MEXICO','code'=>'52'),
        'MY'=>array('name'=>'MALAYSIA','code'=>'60'),
        'MZ'=>array('name'=>'MOZAMBIQUE','code'=>'258'),
        'NA'=>array('name'=>'NAMIBIA','code'=>'264'),
        'NC'=>array('name'=>'NEW CALEDONIA','code'=>'687'),
        'NE'=>array('name'=>'NIGER','code'=>'227'),
        'NG'=>array('name'=>'NIGERIA','code'=>'234'),
        'NI'=>array('name'=>'NICARAGUA','code'=>'505'),
        'NL'=>array('name'=>'NETHERLANDS','code'=>'31'),
        'NO'=>array('name'=>'NORWAY','code'=>'47'),
        'NP'=>array('name'=>'NEPAL','code'=>'977'),
        'NR'=>array('name'=>'NAURU','code'=>'674'),
        'NU'=>array('name'=>'NIUE','code'=>'683'),
        'NZ'=>array('name'=>'NEW ZEALAND','code'=>'64'),
        'OM'=>array('name'=>'OMAN','code'=>'968'),
        'PA'=>array('name'=>'PANAMA','code'=>'507'),
        'PE'=>array('name'=>'PERU','code'=>'51'),
        'PF'=>array('name'=>'FRENCH POLYNESIA','code'=>'689'),
        'PG'=>array('name'=>'PAPUA NEW GUINEA','code'=>'675'),
        'PH'=>array('name'=>'PHILIPPINES','code'=>'63'),
        'PK'=>array('name'=>'PAKISTAN','code'=>'92'),
        'PL'=>array('name'=>'POLAND','code'=>'48'),
        'PM'=>array('name'=>'SAINT PIERRE AND MIQUELON','code'=>'508'),
        'PN'=>array('name'=>'PITCAIRN','code'=>'870'),
        'PR'=>array('name'=>'PUERTO RICO','code'=>'1'),
        'PT'=>array('name'=>'PORTUGAL','code'=>'351'),
        'PW'=>array('name'=>'PALAU','code'=>'680'),
        'PY'=>array('name'=>'PARAGUAY','code'=>'595'),
        'QA'=>array('name'=>'QATAR','code'=>'974'),
        'RO'=>array('name'=>'ROMANIA','code'=>'40'),
        'RS'=>array('name'=>'SERBIA','code'=>'381'),
        'RU'=>array('name'=>'RUSSIAN FEDERATION','code'=>'7'),
        'RW'=>array('name'=>'RWANDA','code'=>'250'),
        'SA'=>array('name'=>'SAUDI ARABIA','code'=>'966'),
        'SB'=>array('name'=>'SOLOMON ISLANDS','code'=>'677'),
        'SC'=>array('name'=>'SEYCHELLES','code'=>'248'),
        'SD'=>array('name'=>'SUDAN','code'=>'249'),
        'SE'=>array('name'=>'SWEDEN','code'=>'46'),
        'SG'=>array('name'=>'SINGAPORE','code'=>'65'),
        'SH'=>array('name'=>'SAINT HELENA','code'=>'290'),
        'SI'=>array('name'=>'SLOVENIA','code'=>'386'),
        'SK'=>array('name'=>'SLOVAKIA','code'=>'421'),
        'SL'=>array('name'=>'SIERRA LEONE','code'=>'232'),
        'SM'=>array('name'=>'SAN MARINO','code'=>'378'),
        'SN'=>array('name'=>'SENEGAL','code'=>'221'),
        'SO'=>array('name'=>'SOMALIA','code'=>'252'),
        'SR'=>array('name'=>'SURINAME','code'=>'597'),
        'ST'=>array('name'=>'SAO TOME AND PRINCIPE','code'=>'239'),
        'SV'=>array('name'=>'EL SALVADOR','code'=>'503'),
        'SY'=>array('name'=>'SYRIAN ARAB REPUBLIC','code'=>'963'),
        'SZ'=>array('name'=>'SWAZILAND','code'=>'268'),
        'TC'=>array('name'=>'TURKS AND CAICOS ISLANDS','code'=>'1649'),
        'TD'=>array('name'=>'CHAD','code'=>'235'),
        'TG'=>array('name'=>'TOGO','code'=>'228'),
        'TH'=>array('name'=>'THAILAND','code'=>'66'),
        'TJ'=>array('name'=>'TAJIKISTAN','code'=>'992'),
        'TK'=>array('name'=>'TOKELAU','code'=>'690'),
        'TL'=>array('name'=>'TIMOR-LESTE','code'=>'670'),
        'TM'=>array('name'=>'TURKMENISTAN','code'=>'993'),
        'TN'=>array('name'=>'TUNISIA','code'=>'216'),
        'TO'=>array('name'=>'TONGA','code'=>'676'),
        'TR'=>array('name'=>'TURKEY','code'=>'90'),
        'TT'=>array('name'=>'TRINIDAD AND TOBAGO','code'=>'1868'),
        'TV'=>array('name'=>'TUVALU','code'=>'688'),
        'TW'=>array('name'=>'TAIWAN, PROVINCE OF CHINA','code'=>'886'),
        'TZ'=>array('name'=>'TANZANIA, UNITED REPUBLIC OF','code'=>'255'),
        'UA'=>array('name'=>'UKRAINE','code'=>'380'),
        'UG'=>array('name'=>'UGANDA','code'=>'256'),
        'US'=>array('name'=>'UNITED STATES','code'=>'1'),
        'UY'=>array('name'=>'URUGUAY','code'=>'598'),
        'UZ'=>array('name'=>'UZBEKISTAN','code'=>'998'),
        'VA'=>array('name'=>'HOLY SEE (VATICAN CITY STATE)','code'=>'39'),
        'VC'=>array('name'=>'SAINT VINCENT AND THE GRENADINES','code'=>'1784'),
        'VE'=>array('name'=>'VENEZUELA','code'=>'58'),
        'VG'=>array('name'=>'VIRGIN ISLANDS, BRITISH','code'=>'1284'),
        'VI'=>array('name'=>'VIRGIN ISLANDS, U.S.','code'=>'1340'),
        'VN'=>array('name'=>'VIET NAM','code'=>'84'),
        'VU'=>array('name'=>'VANUATU','code'=>'678'),
        'WF'=>array('name'=>'WALLIS AND FUTUNA','code'=>'681'),
        'WS'=>array('name'=>'SAMOA','code'=>'685'),
        'XK'=>array('name'=>'KOSOVO','code'=>'381'),
        'YE'=>array('name'=>'YEMEN','code'=>'967'),
        'YT'=>array('name'=>'MAYOTTE','code'=>'262'),
        'ZA'=>array('name'=>'SOUTH AFRICA','code'=>'27'),
        'ZM'=>array('name'=>'ZAMBIA','code'=>'260'),
        'ZW'=>array('name'=>'ZIMBABWE','code'=>'263')
    );


    foreach ($countryArray as $countryCode => $details) {

        if($details['code'] == $phoneIndicative) {
            return $countryCode;
            exit;
        }
    }

    return null;
}

/**
 * Format a string to a phone number
 *
 * @param type $string
 * @return type
 */
function phone_format($string)
{
    return str_separe($string, 3, ' ');
}

/**
 * Swap two variables
 *
 * @param type $value1
 * @param type $value2
 */
function swap(&$value1, &$value2)
{
    $tmp = $value1;
    $value1 = $value2;
    $value2 = $tmp;
}

/**
 * dd function for google chrome inspector
 * @param $value
 */
function cdd($value)
{
    var_dump($value);
    die();
}

/**
 * Validate emails
 * @param string $emails
 * @return array
 */
function validateNotificationEmails($emails, $returnOnlyValidEmails = false)
{

    if (!is_array($emails)) {
        $emails = trim(str_replace(' ', '', $emails));
        $emails = str_replace(',', ';', $emails);
        $emails = explode(';', $emails);
    }

    $errorMobiles = $validEmails = [];
    foreach ($emails as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMobiles[] = trim($email);
        } else {
            $validEmails[] = trim($email);
        }
    }

    if ($returnOnlyValidEmails) {
        return $validEmails;
    }

    return [
        'valid' => $validEmails,
        'error' => $errorMobiles
    ];
}


/**
 * Validate mobile phones
 * @param string $mobiles
 * @return array
 */
function validateNotificationMobiles($mobiles)
{

    $to = explode(';', str_replace(' ', '', trim($mobiles)));

    $validMobiles = $errorMobiles = [];
    foreach ($to as $item) {

        if (strlen($item) == 9 && in_array(substr($item, 0, 2), ['91', '92', '93', '96'])) {
            $item = '+351' . $item;
            $validMobiles[] = $item;
        }

        if (substr($item, 0, 1) != '+') {
            $errorMobiles[] = $item;
        } else {
            $validMobiles[] = $item;
        }
    }

    $validMobiles = array_unique($validMobiles);
    $errorMobiles = array_unique($errorMobiles);

    return [
        'valid' => $validMobiles,
        'error' => $errorMobiles
    ];
}

/**
 * Return translated view to email
 * @param $view
 * @param string $locale
 * @return string
 */
function transEmail($view, $locale = 'pt')
{

    if (in_array($locale, ['ao', 'br', 'mz'])) {
        $locale = 'pt';
    }

    $emailView = $view . '_' . $locale;

    if (view()->exists($emailView)) {
        return $emailView;
    }

    return $view . '_en';
}

/**
 * Translate to specified locale
 * @param $id
 * @param $locale
 * @param array $params
 * @return string|\Symfony\Component\Translation\TranslatorInterface
 */
function transLocale($id, $locale, $params = [])
{
    return trans($id, $params, 'messages', $locale);
}

/**
 * Return file extension icon based on fontawesome
 *
 * @param string $extension
 * @param boolean $colors
 * @return string
 */
function extensionIcon($extension, $colors = true)
{

    switch ($extension) {
        case 'pdf':
            $extension = 'fa-file-pdf';
            if ($colors) $extension .= ' text-red';
            break;
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'bmp':
            $extension = 'fa-file-image';
            if ($colors) $extension .= ' text-yellow';
            break;
        case 'xls':
        case 'xlsx':
        case 'csv':
            $extension = 'fa-file-excel';
            if ($colors) $extension .= ' text-green';
            break;
        case 'web':
            $extension = 'fa-globe';
            if ($colors) $extension .= ' text-blue';
            break;
        case 'doc':
        case 'docx':
        case 'odt':
            $extension = 'fa-file-word';
            if ($colors) $extension .= ' text-blue';
            break;
        case 'ppt':
        case 'pptx':
        case 'ppts':
            $extension = 'fa-file-powerpoint';
            if ($colors) $extension .= ' text-orange';
            break;
        case 'mp3':
        case 'wav':
        case 'wma':
            $extension = 'fa-file-audio';
            break;
        case '3gp':
        case 'mp4':
        case 'avi':
        case 'flv':
        case 'mpg':
        case 'wmv':
        case 'mov':
        case 'm4v':
            $extension = 'fa-file-movie';
            break;
        case 'zip':
        case '7z':
        case 'zipx':
        case 'rar':
            $extension = 'fa-file-archive';
            break;
        case 'log':
        case 'txt':
        case 'rtf':
            $extension = 'fa-file-alt';
            break;
        case '':
            $extension = 'fa-folder-open';
            if ($colors) $extension .= ' text-yellow';
            break;
        default:
            $extension = 'fa-file-o';
            break;
    }

    return '<i class="fas fa-fw ' . $extension . '"></i>';
}

/**
 * Sort multidimensional array by a given key
 *
 *
 * @param type $array
 * @param type $key
 * @param type $direction [SORT_ASC|SORT_SORT_DESC]
 */
function aasort(&$arr, $col, $dir = SORT_ASC)
{

    if (!empty($arr)) {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }
}

/**
 * Divie array in N parts
 * @param $arr
 * @param $parts
 * @param $preserveKeys
 * @return array
 */
function array_cut(&$arr, $parts, $preserveKeys = false)
{
    if (is_array($arr)) {
        $arr = array_chunk($arr, (int)ceil(count($arr) / $parts), $preserveKeys);
    }
}

/**
 * Search a given value into a multidimensional array by a given key
 * @param $array
 * @param $key key to search
 * @param $value value to search
 * @param $returnKey If return key is false,
 * @return false|int|string
 */
function search_multidimensional_array($array, $key, $value, $returnKey = false)
{

    $arrKey = array_search($value, array_column($array, $key));

    if ($returnKey) {
        return $arrKey;
    } else {
        if ($arrKey) {
            return $array[$arrKey];
        }
    }
    return [];
}
/**
 * Search on a multidimensional array by a given key
 *
 * @param array $array
 * @param $search
 * @return bool|mixed
 */
function searchArrayValueByKey(array $array, $searchKey)
{
    foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $key => $value) {
        if ($searchKey === $key)
            return $value;
    }
    return false;
}

/**
 * Convert an object to array
 * @param $object
 * @param bool $preserveKeys
 * @return mixed
 */
function object2array($object, $preserveKeys = true)
{
    return json_decode(json_encode($object), $preserveKeys);
}

/**
 * Return array with hours
 *
 * @return string
 */
function listDates($daysJumpEach = 1, $startDate = null, $endDate = null)
{
    if(is_null($startDate)) {
        $startDate = date('Y-m-d');
    }

    if(is_null($endDate)) {
        $endDate = date('Y').'-12-31';
    }

    $period = new DatePeriod(
        new DateTime($startDate),
        new DateInterval('P1D'),
        new DateTime($endDate)
    );

    $arr = [];
    foreach ($period as $key => $value) {
        $arr[$value->format('Y-m-d')] = $value->format('d').' '.trans('datetime.month.'.$value->format('m'));
    }

    return $arr;
}

/**
 * Return array with hours
 *
 * @return string
 */
function listHours($minutesJumpEach = 15, $hoursJumpEach = 1, $startHour = 0, $startMinutes = 0, $maxHours = 23, $maxMinutes = 59)
{

    $hours = array();

    for ($h = $startHour; $h <= $maxHours; $h += $hoursJumpEach) {

        $h < 10 ? $h = '0' . $h : $h;

        for ($m = $startMinutes; $m <= $maxMinutes; $m += $minutesJumpEach) {

            $m < 10 ? $m = '0' . $m : $m;

            $hours += array($h . ':' . $m => $h . ':' . $m);
        }
    }
    return $hours;
}

/**
 * Create a list of minutes
 * @param int $minutesJumpEach
 * @param int $minMinute
 * @param int $maxMinute
 * @return array
 */
function listNumeric($valueJumpEach = 15, $minValue = 0, $maxValue = 60, $prefix = '')
{
    $values = array();
    for ($m = $minValue; $m <= $maxValue; $m += $valueJumpEach) {
        $values += array($m => $m . $prefix);
    }
    return $values;
}

/**
 * Return last hour of last 15 minutes
 * @return string
 */
function lastHour($default = null)
{
    $lastHour = Date::now();

    switch ($lastHour->minute) {
        case $lastHour->minute >= 0 && $lastHour->minute < 15:
            $minute = '00';
            break;
        case $lastHour->minute >= 15 && $lastHour->minute < 30:
            $minute = '15';
            break;
        case $lastHour->minute >= 30 && $lastHour->minute < 45:
            $minute = '30';
            break;
        case $lastHour->minute >= 45 && $lastHour->minute < 60:
            $minute = '45';
            break;
    }

    return $lastHour->hour . ':' . $minute;
}

function textWithUrls($text)
{
    // The Regular Expression filter
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    // Check if there is a url in the text
    if (preg_match($reg_exUrl, $text, $url)) {

        // make the urls hyper links
        $text = preg_replace($reg_exUrl, '<a href="' . $url[0] . '" target="_blank">' . $url[0] . '</a>', $text);
    }

    return $text;
}


function convertXml2Arr($xml)
{
    $xml = str_replace('v1:', '', $xml);
    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);
    $xml = new \SimpleXMLElement($response);
    $xml = json_decode(json_encode($xml), true);
    return $xml;
}

/**
 * Convert XML string to array
 *
 * @param type $xmlstr
 * @return type
 */
function xmlstr_to_array($xmlstr)
{
    $doc = new DOMDocument();
    $doc->loadXML($xmlstr);
    $root = $doc->documentElement;
    $output = domnode_to_array($root);
    $output['@root'] = $root->tagName;
    return $output;
}

function domnode_to_array($node)
{
    $output = array();
    switch ($node->nodeType) {
        case XML_CDATA_SECTION_NODE:
        case XML_TEXT_NODE:
            $output = trim($node->textContent);
            break;
        case XML_ELEMENT_NODE:
            for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                $child = $node->childNodes->item($i);
                $v = domnode_to_array($child);
                if (isset($child->tagName)) {
                    $t = $child->tagName;
                    if (!isset($output[$t])) {
                        $output[$t] = array();
                    }
                    $output[$t][] = $v;
                } elseif ($v || $v === '0') {
                    $output = (string) $v;
                }
            }
            if ($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
                $output = array('@content' => $output); //Change output into an array.
            }
            if (is_array($output)) {
                if ($node->attributes->length) {
                    $a = array();
                    foreach ($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    $output['@attributes'] = $a;
                }
                foreach ($output as $t => $v) {
                    if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                        $output[$t] = $v[0];
                    }
                }
            }
            break;
    }
    return $output;
}

/**
 * Convert a month from string to decimal
 * @param $month
 */
function convertMonth2Decimal($month, $leadingZeros = true)
{

    if (empty($month)) {
        return '';
    }

    $month = strtolower($month);

    $lists = array_flip(array_map('strtolower', trans('datetime.month-tiny')));

    if ($leadingZeros) {
        return str_pad($lists[$month], 2, "0", STR_PAD_LEFT);
    }

    return $lists[$month];
}

/**
 * Remove accents in a string
 *
 * @param $string
 * @return string
 */
function removeAccents($string)
{
    $unwanted_array = array(
        'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
        'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
        'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
        'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'º' => '', 'ª' => '', '&' => 'e'
    );
    return strtr($string, $unwanted_array);
}


function removerFormatacaoNumero($strNumero)
{
    $strNumero = trim(str_replace(Setting::get('app_currency'), null, $strNumero));
    $vetVirgula = explode(",", $strNumero);

    if (count($vetVirgula) == 1) {
        $acentos = array(".");
        $resultado = str_replace($acentos, "", $strNumero);
        return $resultado;
    } else if (count($vetVirgula) != 2) {
        return $strNumero;
    }

    $strNumero = $vetVirgula[0];
    $strDecimal = mb_substr($vetVirgula[1], 0, 2);
    $acentos = array(".");
    $resultado = str_replace($acentos, "", $strNumero);
    $resultado = $resultado . "." . $strDecimal;
    return $resultado;
}

/**
 * Convert um numero décimal para texto por extenso
 * @param int $valor
 * @param bool $bolExibirMoeda
 * @param bool $bolPalavraFeminina
 * @return string
 */
function float2Text($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false)
{

    $valor = (float) $valor;
    if ($valor < 0.00) {
        $valor = $valor * -1;
    }

    if (is_float($valor)) {
        $valor = str_replace('.', ',', $valor);
    }

    $valor = (string) removerFormatacaoNumero($valor);

    $singular = null;
    $plural = null;
    if ($bolExibirMoeda) {
        $singular = array("cêntimos", "euro", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural   = array("cêntimos", "euros", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
    } else {
        $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural   = array("", "", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
    }
    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", " ", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

    if ($bolPalavraFeminina) {
        if ($valor == 1)
            $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
        else {
            $u = array("", "um", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
        }
    }

    $z = 0;
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++)
        for ($ii = mb_strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
    $rt = null;
    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);

    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++;
        elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    $rt = mb_substr($rt, 1);
    return ($rt ? trim($rt) : "zero");
}

function human_money($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false)
{
    if ($valor < 0)
        $valor = $valor * (-1);
    return float2Text($valor, $bolExibirMoeda, $bolPalavraFeminina);
}

/**
 * Return next
 * @param $date
 * @return mixed
 */
function getNextUsefullDate($date)
{

    $date = new Date($date);

    if ($date->isWeekend()) {
        $nextDate = $date->addWeek(1)->startOfWeek()->format('Y-m-d');
    } else {
        $nextDate = $date->format('Y-m-d');
    }

    return $nextDate;
}

/**
 * Return label format config
 *
 * @param $date
 * @return mixed
 */
function getLabelFormat($format = null)
{

    if (!empty($format)) {
        $format = 'labels.' . $format;
    } else {
        if (empty(Setting::get('shipment_label_size'))) {
            $format = 'labels.default';
        } else {
            $format = 'labels.' . Setting::get('shipment_label_size');
        }
    }

    return config($format);
}

/**
 * Get label view
 *
 * @param null $format
 * @return mixed
 */
function getLabelView($format = null)
{

    if (!empty($format)) {
        $view = $format;
    } else {
        if (empty(Setting::get('shipment_label_size'))) {
            $view = 'label';
        } else {
            $view = 'label_' . Setting::get('shipment_label_size');
        }
    }

    return 'admin.printer.shipments.labels.' . $view;
}

/**
 * Force zip code to CP4
 * @param $zipCode
 * @return mixed
 */
function zipcodeCP4($zipCode)
{

    $zipCode = explode('-', $zipCode);

    return $zipCode[0];
}

/**
 * Sinonym to trans
 *
 * @param $code
 * @param null $locale
 * @param array $params
 * @return string|\Symfony\Component\Translation\TranslatorInterface
 */
function translation($code, $locale = null, $params = [])
{
    return trans($code, $params, 'messages', $locale);
}

/**
 * Calculate reminder
 *
 * @param $dividend
 * @param $divisor
 * @return bool|float|int|string
 */
function remainder($dividend, $divisor, $decimals = 0)
{
    if ($dividend == 0 || $divisor == 0) return 0;

    $dividend .= '';
    $remainder = 0;
    $division = '';

    // negative case
    while ($dividend < 0) {
        $dividend += $divisor;
        if ($dividend >= 0) return $dividend;
    }

    // positive case
    while (($remainder . $dividend) * 1 > $divisor) {
        // get remainder big enough to divide
        while ($remainder * 1 < $divisor) {
            $remainder .= $dividend[0];
            $remainder *= 1;
            $dividend = substr($dividend, 1);
        }

        // get highest multiplicator for remainder
        $mult = floor($remainder / $divisor);

        // add multiplicator to division
        $division .= $mult . '';

        // subtract from remainder
        $remainder -= $mult * $divisor;
    }

    // add remaining zeros if any, to division
    if (strlen($dividend) > 0 && $dividend * 1 == 0) {
        $division .= $dividend;
    }

    return number_format($remainder, $decimals, '.', '');
}

function cropImageRounded($filepath, $destinationFilepath, $newwidth = 200, $newheight = 200)
{

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

    if ($ext == "jpg" || $ext == "jpeg") {
        $image_s = imagecreatefromjpeg($filepath);
    } else if ($ext == "png") {
        $image_s = imagecreatefrompng($filepath);
    }

    $width = imagesx($image_s);
    $height = imagesy($image_s);

    $newwidth = 38;
    $newheight = 38;

    $image = imagecreatetruecolor($newwidth, $newheight);
    imagealphablending($image, true);
    imagecopyresampled($image, $image_s, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    // create masking
    $mask = imagecreatetruecolor($width, $height);
    $mask = imagecreatetruecolor($newwidth, $newheight);


    $transparent = imagecolorallocate($mask, 255, 0, 0);
    imagecolortransparent($mask, $transparent);


    imagefilledellipse($mask, $newwidth / 2, $newheight / 2, $newwidth, $newheight, $transparent);


    $red = imagecolorallocate($mask, 0, 0, 0);
    imagecopy($image, $mask, 0, 0, 0, 0, $newwidth, $newheight);
    imagecolortransparent($image, $red);
    imagefill($image, 0, 0, $red);


    $dest = imagecreatefrompng(asset('assets/img/default/marker.png'));

    imagecopymerge($dest, $image, 13, 4.5, 0, 0, 150, 150, 100);

    // output and free memory
    imagepng($dest, $destinationFilepath);
    imagedestroy($dest);
    imagedestroy($mask);
}

class MimeStreamWrapper
{
    const WRAPPER_NAME = 'mime';

    /**
     * @var resource
     */
    public $context;

    /**
     * @var bool
     */
    private static $isRegistered = false;

    /**
     * @var callable
     */
    private $callBackFunction;

    /**
     * @var bool
     */
    private $eof = false;

    /**
     * @var resource
     */
    private $fp;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $fileStat;

    /**
     * @return array
     */
    private function getStat()
    {
        if ($fStat = fstat($this->fp)) {
            return $fStat;
        }

        $size = 100;
        if ($headers = get_headers($this->path, true)) {
            $head = array_change_key_case($headers, CASE_LOWER);
            $size = (int)$head['content-length'];
        }
        $blocks = ceil($size / 512);
        return array(
            'dev' => 16777220,
            'ino' => 15764,
            'mode' => 33188,
            'nlink' => 1,
            'uid' => 10000,
            'gid' => 80,
            'rdev' => 0,
            'size' => $size,
            'atime' => 0,
            'mtime' => 0,
            'ctime' => 0,
            'blksize' => 4096,
            'blocks' => $blocks,
        );
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
        $this->fp = fopen($this->path, 'rb') or die('Cannot open file:  ' . $this->path);
        $this->fileStat = $this->getStat();
    }

    /**
     * @param int $count
     * @return string
     */
    public function read($count)
    {
        return fread($this->fp, $count);
    }

    /**
     * @return string
     */

    public function getStreamPath()
    {
        return str_replace(array('ftp://', 'http://', 'https://'), self::WRAPPER_NAME . '://', $this->path);
    }

    /**
     * @return resource
     */
    public function getContext()
    {
        if (!self::$isRegistered) {
            stream_wrapper_register(self::WRAPPER_NAME, get_class());
            self::$isRegistered = true;
        }
        return stream_context_create(
            array(
                self::WRAPPER_NAME => array(
                    'cb' => array($this, 'read'),
                    'fileStat' => $this->fileStat,
                )
            )
        );
    }

    /**
     * @param $path
     * @param $mode
     * @param $options
     * @param $opened_path
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        if (!preg_match('/^r[bt]?$/', $mode) || !$this->context) {
            return false;
        }
        $opt = stream_context_get_options($this->context);
        if (
            !is_array($opt[self::WRAPPER_NAME]) ||
            !isset($opt[self::WRAPPER_NAME]['cb']) ||
            !is_callable($opt[self::WRAPPER_NAME]['cb'])
        ) {
            return false;
        }
        $this->callBackFunction = $opt[self::WRAPPER_NAME]['cb'];
        $this->fileStat = $opt[self::WRAPPER_NAME]['fileStat'];

        return true;
    }

    /**
     * @param int $count
     * @return mixed|string
     */
    public function stream_read($count)
    {
        if ($this->eof || !$count) {
            return '';
        }
        if (($s = call_user_func($this->callBackFunction, $count)) == '') {
            $this->eof = true;
        }
        return $s;
    }

    /**
     * @return bool
     */
    public function stream_eof()
    {
        return $this->eof;
    }

    /**
     * @return array
     */
    public function stream_stat()
    {
        return $this->fileStat;
    }

    /**
     * @param int $castAs
     *
     * @return resource
     */
    public function stream_cast($castAs)
    {
        $read = null;
        $write  = null;
        $except = null;
        return @stream_select($read, $write, $except, $castAs);
    }
}

/**
 * Return client ip
 * @return bool
 */
function client_ip()
{
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = false;
    }
    return $ipaddress;
}


/**
 * Get string between
 * @param $string
 * @param $start
 * @param $end
 * @return bool|string
 */
function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

/**
 * Check if value is between
 * @param $startValue
 * @param $endValue
 */
function valueBetween($compareValue, $startValue, $endValue)
{
    if ($compareValue >= $startValue && $compareValue <= $endValue) {
        return true;
    }
    return false;
}


/**
 * Return max value between two values
 * @param $value1
 * @param $value2
 */
function valueMax($value1, $value2)
{
    return $value1 > $value2 ? $value1 : $value2;
}

/**
 * Check if hour is between two hours
 *
 * @param $compareValue
 * @param $minHour
 * @param $maxHour
 * @return bool
 */
function hourBetween($compareValue, $minHour, $maxHour)
{
    $f = \DateTime::createFromFormat('!H:i', $minHour);
    $t = \DateTime::createFromFormat('!H:i', $maxHour);
    $i = \DateTime::createFromFormat('!H:i', $compareValue);
    if ($f > $t) $t->modify('+1 day');
    return ($f <= $i && $i <= $t) || ($f <= $i->modify('+1 day') && $i <= $t);
}

/**
 * Convert xml response to array
 *
 * @param $xml
 * @param string $explodeElement
 * @return mixed
 */
function xml2Arr($xml, $explodeElement = 'soapBody')
{
    if(empty($xml)) {
        return [];
    }

    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);
    $xml = simplexml_load_string($response);
    $body = $xml->xpath('//' . $explodeElement);
    $array = json_decode(json_encode((array)$body), TRUE);
    return @$array[0];
}

/**
 * Active menu
 * @param $menuOption
 * @param $optionName
 * @return string
 */
function activeMenu($menuOption, $optionName)
{
    if ($menuOption == $optionName) {
        return 'active';
    }
}

/**
 * Return URL route to custom page
 * @param $pageCode
 */
function routeToPage($pageCode)
{

    try {
        $locale = \Illuminate\Support\Facades\App::getLocale();
        $path = json_decode(File::get(storage_path() . '/pages_routes.json'), true);

        if (isset($path[$locale][$pageCode])) {
            return url($path[$locale][$pageCode]);
        }

        return '#';
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Create UUID
 * @return string
 */
function uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

/**
 * Group multidimensional array by key
 *
 * @param $array
 * @param $key
 * @return array
 */
function array_group_by($array, $key)
{
    $return = array();
    foreach ($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}

/**
 * Combine multiple arraus
 * @param $arrays [ [],[], [], ... ]
 * @param int $i
 * @return array
 */
function combinations($arrays, $i = 0)
{
    if (!isset($arrays[$i])) {
        return array();
    }
    if ($i == count($arrays) - 1) {
        return $arrays[$i];
    }

    // get combinations from subsequent arrays
    $tmp = combinations($arrays, $i + 1);

    $result = array();

    // concat each array from tmp with each element from $arrays[$i]
    foreach ($arrays[$i] as $v) {
        foreach ($tmp as $t) {
            $result[] = is_array($t) ?
                array_merge(array($v), $t) :
                array($v, $t);
        }
    }

    return $result;
}

function randomPassword($length = 9, $lowercase = true, $uppercase = true, $numbers = true, $specialChars = true, $add_dashes = false)
{

    $sets = [];

    if ($lowercase) {
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    }

    if ($uppercase) {
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    }

    if ($numbers) {
        $sets[] = '0123456789';
    }

    if ($specialChars) {
        $sets[] = '!@#$%&*?';
    }


    /*$available_sets = 'luds';
    if(strpos($available_sets, 'l') !== false)
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    if(strpos($available_sets, 'u') !== false)
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    if(strpos($available_sets, 'd') !== false)
        $sets[] = '23456789';
    if(strpos($available_sets, 's') !== false)
        $sets[] = '!@#$%&*?';*/

    $all = '';
    $password = '';
    foreach ($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }

    $all = str_split($all);
    for ($i = 0; $i < $length - count($sets); $i++)
        $password .= $all[array_rand($all)];

    $password = str_shuffle($password);

    if (!$add_dashes)
        return $password;

    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while (strlen($password) > $dash_len) {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }
    $dash_str .= $password;
    return $dash_str;
}

/**
 * Return color for percent value
 * @param $percent
 * @return string
 */
function getPercentColor($percent, $reverse=false)
{

    if($reverse) {
        $percent = 100-$percent;

        if($percent == 0 || $percent < 0) {
            $percent = 0.01; //para nao ficar cinzento quando é 100%
        }
    }

    if ($percent == 0.00 || $percent == 0 || $percent < 0 || $percent > 100) {
        $color = '#999999';
    }
    if (valueBetween($percent, 0.01, 9.9999)) {
        $color = '#FF0000';
    } elseif (valueBetween($percent, 10, 19.9999)) {
        $color = '#FF3B00';
    } elseif (valueBetween($percent, 20, 29.9999)) {
        $color = '#FF7600';
    } elseif (valueBetween($percent, 30, 39.9999)) {
        $color = '#FFB000';
    } elseif (valueBetween($percent, 40, 49.9999)) {
        $color = '#FFEB00';
    } elseif (valueBetween($percent, 50, 59.9999)) {
        $color = '#E3DF12';
    } elseif (valueBetween($percent, 60, 69.9999)) {
        $color = '#ADD20E';
    } elseif (valueBetween($percent, 70, 79.9999)) {
        $color = '#77C609';
    } elseif (valueBetween($percent, 80, 89.9999)) {
        $color = '#41B905';
    } elseif (valueBetween($percent, 90, 100)) {
        $color = '#0BAC00';
    }

    return $color;
}



/**
 * Return knowledge article URL
 *
 * @param $articleId
 * @param false $autoRedirect
 * @return string
 */
function knowledgeArticle($articleId) {
    return '/admin/helpcenter/article?article='.$articleId;
}

/**
 * Get next possible time
 * 
 * @param string $currentTime
 * @return string
 */
function getNextTimePossible($currentTime = null, $timeWanted = null) {
    $currentTime = $currentTime ?? date('H:i');
    if ($timeWanted && $timeWanted > $currentTime) {
        return $timeWanted;
    }

    $hour    = explode(':', $currentTime);
    $minutes = $hour[1];
    $hour    = $hour[0];

    if (($minutes % 5) > 0 && $minutes < 50) {
        // Add 10 minutes to the hour but maintain right zero
        $minutes = (substr(date('i'), 0, 1) + 1) . '0';
    } else if ($minutes > 50) {
        $hour += 1;
        $minutes = '00';
    }

    $hour = sprintf('%02d', $hour);
    return $hour . ':' . $minutes;
}

/**
 * Write exception full message
 *
 * @param [type] $e
 * @return void
 */
function exceptionMsg($e) {
    return $e->getMessage(). ' on ' . $e->getFile() . ' line '. $e->getLine();
}

/**
 * Convert string into slug
 *
 * @param [type] $text
 * @param string $divider
 * @return void
 */
function slugify($text, string $divider = '-') {

    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

/*
 * Check if string contains letters
 * 
 * @param string $string
 * @return bool
 */
function stringContainsLetters($string) {
    return preg_match("/[A-Z]/i", $string);
}

/**
 * Remove string whitespaces
 * 
 * @param string $string
 * @return string
 */
function removeWhiteSpaces($string) {
    return str_replace(' ', '', $string);
}

function convertHoursToMinutes($time) {
    $arrayTime = explode(':', $time);

    $minutes = intval($arrayTime[0]) * 60 + intval($arrayTime[1]);

    return $minutes;
}

function convertMinutesToHours($minutes) {
    $hour = 0;
    if($minutes >= 60){
        $hour = intval($minutes / 60);
        $newMinutes = $minutes - ($hour * 60);
    }else{
        $newMinutes = $minutes;
    }
    
    if($newMinutes < 10){
        $time = strval($hour) . ':0' . strval($newMinutes);
    }else{
        $time = strval($hour) . ':' . strval($newMinutes);
    }

    return $time;
}

function removeDialCode($phoneNumber, $country){

    $dialCode = ZipCode::getDialCode($country);
    
    if (strpos($phoneNumber, $dialCode) === 0) {
        return substr($phoneNumber, strlen($dialCode));
    } else {
        return $phoneNumber;
    }

}
