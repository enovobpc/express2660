<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class ZipCode extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_zip_codes';

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_core';

    /**
     * Zip code formats
     *
     * @var array
     */
    protected $formats = [
        'AC' => [],                            # Ascension
        'AD' => ['AD###', '#####'],            # ANDORRA
        'AE' => [],                            # UNITED ARAB EMIRATES
        'AF' => ['####'],                      # AFGHANISTAN
        'AG' => [],                            # ANTIGUA AND BARBUDA
        'AI' => ['AI-2640'],                   # ANGUILLA
        'AL' => ['####'],                      # ALBANIA
        'AM' => ['####'],                      # ARMENIA
        'AN' => [],                            # NETHERLANDS ANTILLES
        'AO' => [],                            # ANGOLA
        'AQ' => ['BIQQ 1ZZ'],                  # ANTARCTICA
        'AR' => ['####', '@####@@@'],          # ARGENTINA
        'AS' => ['#####', '#####-####'],       # AMERICAN SAMOA
        'AT' => ['####'],                      # AUSTRIA
        'AU' => ['####'],                      # AUSTRALIA
        'AW' => [],                            # ARUBA
        'AX' => ['#####', 'AX-#####'],         # Åland
        'AZ' => ['AZ ####'],                   # AZERBAIJAN
        'BA' => ['#####'],                     # BOSNIA AND HERZEGOWINA
        'BB' => ['BB#####'],                   # BARBADOS
        'BD' => ['####'],                      # BANGLADESH
        'BE' => ['####'],                      # BELGIUM
        'BF' => [],                            # BURKINA FASO
        'BG' => ['####'],                      # BULGARIA
        'BH' => ['###', '####'],               # BAHRAIN
        'BI' => [],                            # BURUNDI
        'BJ' => [],                            # BENIN
        'BL' => ['#####'],                     # Sankt Bartholomäus
        'BM' => ['@@ ##', '@@ @@'],            # BERMUDA
        'BN' => ['@@####'],                    # BRUNEI DARUSSALAM
        'BO' => [],                            # BOLIVIA
        'BQ' => [],                            # Karibische Niederlande
        'BR' => ['#####-###', '#####'],        # BRAZIL
        'BS' => [],                            # BAHAMAS
        'BT' => ['#####'],                     # BHUTAN
        'BV' => [],                            # BOUVET ISLAND
        'BW' => [],                            # BOTSWANA
        'BY' => ['######'],                    # BELARUS
        'BZ' => [],                            # BELIZE
        'CA' => ['@#@ #@#'],                   # CANADA
        'CC' => ['####'],                      # COCOS (KEELING) ISLANDS
        'CD' => [],                            # CONGO, Democratic Republic of (was Zaire)
        'CF' => [],                            # CENTRAL AFRICAN REPUBLIC
        'CG' => [],                            # CONGO, People's Republic of
        'CH' => ['####'],                      # SWITZERLAND
        'CI' => [],                            # COTE D'IVOIRE
        'CK' => [],                            # COOK ISLANDS
        'CL' => ['#######', '###-####'],       # CHILE
        'CM' => [],                            # CAMEROON
        'CN' => ['######'],                    # CHINA
        'CO' => ['######'],                    # COLOMBIA
        'CR' => ['#####', '#####-####'],       # COSTA RICA
        'CU' => ['#####'],                     # CUBA
        'CV' => ['####'],                      # CAPE VERDE
        'CW' => [],                            # Curaçao
        'CX' => ['####'],                      # CHRISTMAS ISLAND
        'CY' => ['####'],                      # Cyprus
        'CZ' => ['### ##'],                    # Czech Republic
        'DE' => ['#####'],                     # GERMANY
        'DJ' => [],                            # DJIBOUTI
        'DK' => ['####'],                      # DENMARK
        'DM' => [],                            # DOMINICA
        'DO' => ['#####'],                     # DOMINICAN REPUBLIC
        'DZ' => ['#####'],                     # ALGERIA
        'EC' => ['######'],                    # ECUADOR
        'EE' => ['#####'],                     # ESTONIA
        'EG' => ['#####'],                     # EGYPT
        'EH' => [],                            # WESTERN SAHARA
        'ER' => [],                            # ERITREA
        'ES' => ['#####'],                     # SPAIN
        'ET' => ['####'],                      # ETHIOPIA
        'FI' => ['#####'],                     # FINLAND
        'FJ' => [],                            # FIJI
        'FK' => ['FIQQ 1ZZ'],                  # FALKLAND ISLANDS (MALVINAS)
        'FM' => ['#####', '#####-####'],       # MICRONESIA
        'FO' => ['###'],                       # FAROE ISLANDS
        'FR' => ['#####'],                     # FRANCE
        'FX' => [],                            # FRANCE, METROPOLITAN
        'GA' => [],                            # GABON
        'GB' => ['@@## #@@', '@#@ #@@', '@@# #@@', '@@#@ #@@', '@## #@@', '@# #@@'], # UK
        'GD' => [],                            # GRENADA
        'GE' => ['####'],                      # GEORGIA
        'GF' => ['973##'],                     # FRENCH GUIANA
        'GG' => ['GY# #@@', 'GY## #@@'],       # Guernsey
        'GH' => [],                            # GHANA
        'GI' => ['GX11 1AA'],                  # GIBRALTAR
        'GL' => ['####'],                      # GREENLAND
        'GM' => [],                            # GAMBIA
        'GN' => ['###'],                       # GUINEA
        'GP' => ['971##'],                     # GUADELOUPE
        'GQ' => [],                            # EQUATORIAL GUINEA
        'GR' => ['### ##'],                    # GREECE
        'GS' => ['SIQQ 1ZZ'],                  # SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS
        'GT' => ['#####'],                     # GUATEMALA
        'GU' => ['#####', '#####-####'],       # GUAM
        'GW' => ['####'],                      # GUINEA-BISSAU
        'GY' => [],                            # GUYANA
        'HK' => [],                            # HONG KONG
        'HM' => [],                            # HEARD AND MC DONALD ISLANDS
        'HN' => ['@@####', '#####'],           # HONDURAS
        'HR' => ['#####'],                     # CROATIA
        'HT' => ['####'],                      # HAITI
        'HU' => ['####'],                      # HUNGARY
        'IC' => ['#####'],                     # THE CANARY ISLANDS
        'ID' => ['#####'],                     # INDONESIA
        'IE' => ['@** ****'],                  # IRELAND
        'IL' => ['#######'],                   # ISRAEL
        'IM' => ['IM# #@@', 'IM## #@@'],       # Isle of Man
        'IN' => ['######', '### ###'],         # INDIA
        'IO' => ['BBND 1ZZ'],                  # BRITISH INDIAN OCEAN TERRITORY
        'IQ' => ['#####'],                     # IRAQ
        'IR' => ['##########', '#####-#####'], # IRAN
        'IS' => ['###'],                       # ICELAND
        'IT' => ['#####'],                     # ITALY
        'JE' => ['JE# #@@', 'JE## #@@'],       # Jersey
        'JM' => ['##'],                        # JAMAICA
        'JO' => ['#####'],                     # JORDAN
        'JP' => ['###-####', '###'],           # JAPAN
        'KE' => ['#####'],                     # KENYA
        'KG' => ['######'],                    # KYRGYZSTAN
        'KH' => ['#####'],                     # CAMBODIA
        'KI' => [],                            # KIRIBATI
        'KM' => [],                            # COMOROS
        'KN' => [],                            # SAINT KITTS AND NEVIS
        'KO' => [],                            # Kosovo
        'KP' => [],                            # NORTH KOREA
        'KR' => ['###-###', '#####'],          # SOUTH KOREA
        'KW' => ['#####'],                     # KUWAIT
        'KY' => ['KY#-####'],                  # CAYMAN ISLANDS
        'KZ' => ['######'],                    # KAZAKHSTAN
        'LA' => ['#####'],                     # LAO PEOPLE'S DEMOCRATIC REPUBLIC
        'LB' => ['#####', '#### ####'],        # LEBANON
        'LC' => ['LC## ###'],                  # SAINT LUCIA
        'LI' => ['####'],                      # LIECHTENSTEIN
        'LK' => ['#####'],                     # SRI LANKA
        'LR' => ['####'],                      # LIBERIA
        'LS' => ['###'],                       # LESOTHO
        'LT' => ['LT-#####'],                  # LITHUANIA
        'LU' => ['####', 'L-####'],            # LUXEMBOURG
        'LV' => ['LV-####'],                   # LATVIA
        'LY' => [],                            # LIBYAN ARAB JAMAHIRIYA
        'MA' => ['#####'],                     # MOROCCO
        'MC' => ['980##'],                     # MONACO
        'MD' => ['MD####', 'MD-####'],         # MOLDOVA
        'ME' => ['#####'],                     # MONTENEGRO
        'MF' => ['97150'],                     # Saint-Martin
        'MG' => ['###'],                       # MADAGASCAR
        'MH' => ['#####', '#####-####'],       # MARSHALL ISLANDS
        'MK' => ['####'],                      # MACEDONIA
        'ML' => [],                            # MALI
        'MM' => ['#####'],                     # MYANMAR
        'MN' => ['#####'],                     # MONGOLIA
        'MO' => [],                            # MACAU
        'MP' => ['#####', '#####-####'],       # SAIPAN, NORTHERN MARIANA ISLANDS
        'MQ' => ['972##'],                     # MARTINIQUE
        'MR' => [],                            # MAURITANIA
        'MS' => [],                            # MONTSERRAT
        'MT' => ['@@@ ####'],                  # MALTA
        'MU' => ['#####'],                     # MAURITIUS
        'MV' => ['#####'],                     # MALDIVES
        'MW' => [],                            # MALAWI
        'MX' => ['#####'],                     # MEXICO
        'MY' => ['#####'],                     # MALAYSIA
        'MZ' => ['####'],                      # MOZAMBIQUE
        'NA' => [],                            # NAMIBIA
        'NC' => ['988##'],                     # NEW CALEDONIA
        'NE' => ['####'],                      # NIGER
        'NF' => ['####'],                      # NORFOLK ISLAND
        'NG' => ['######'],                    # NIGERIA
        'NI' => ['#####'],                     # NICARAGUA
        'NL' => ['#### @@'],                   # NETHERLANDS
        'NO' => ['####'],                      # NORWAY
        'NP' => ['#####'],                     # NEPAL
        'NR' => [],                            # NAURU
        'NU' => [],                            # NIUE
        'NZ' => ['####'],                      # NEW ZEALAND
        'OM' => ['###'],                       # OMAN
        'PA' => ['####'],                      # PANAMA
        'PE' => ['#####', 'PE #####'],         # PERU
        'PF' => ['987##'],                     # FRENCH POLYNESIA
        'PG' => ['###'],                       # PAPUA NEW GUINEA
        'PH' => ['####'],                      # PHILIPPINES
        'PK' => ['#####'],                     # PAKISTAN
        'PL' => ['##-###'],                    # POLAND
        'PM' => ['97500'],                     # ST. PIERRE AND MIQUELON
        'PN' => ['PCRN 1ZZ'],                  # PITCAIRN
        'PR' => ['#####', '#####-####'],       # PUERTO RICO
        'PS' => ['###'],                       # PALESTINIAN TERRITORY
        'PT' => ['####-###', '####'],          # PORTUGAL
        'PW' => ['#####', '#####-####'],       # PALAU
        'PY' => ['####'],                      # PARAGUAY
        'QA' => [],                            # QATAR
        'RE' => ['974##'],                     # REUNION
        'RO' => ['######'],                    # ROMANIA
        'RS' => ['#####'],                     # SERBIA
        'RU' => ['######'],                    # RUSSIA
        'RW' => [],                            # RWANDA
        'SA' => ['#####', '#####-####'],       # SAUDI ARABIA
        'SB' => [],                            # SOLOMON ISLANDS
        'SC' => [],                            # SEYCHELLES
        'SD' => ['#####'],                     # SUDAN
        'SE' => ['### ##'],                    # SWEDEN
        'SG' => ['######'],                    # SINGAPORE
        'SH' => ['@@@@ 1ZZ'],                  # ST. HELENA
        'SI' => ['####', 'SI-####'],           # SLOVENIA
        'SJ' => ['####'],                      # SVALBARD AND JAN MAYEN ISLANDS
        'SK' => ['### ##'],                    # SLOVAKIA
        'SL' => [],                            # SIERRA LEONE
        'SM' => ['4789#'],                     # SAN MARINO
        'SN' => ['#####'],                     # SENEGAL
        'SO' => ['@@ #####'],                  # SOMALIA
        'SR' => [],                            # SURINAME
        'SS' => ['#####'],                     # SOUTH SUDAN
        'ST' => [],                            # SAO TOME AND PRINCIPE
        'SV' => ['####'],                      # EL SALVADOR
        'SX' => [],                            # Sint Maarten
        'SY' => [],                            # SYRIAN ARAB REPUBLIC
        'SZ' => ['@###'],                      # SWAZILAND
        'TA' => [],                            # Tristan da Cunha
        'TC' => ['TKCA 1ZZ'],                  # TURKS AND CAICOS ISLANDS
        'TD' => [],                            # CHAD
        'TF' => [],                            # FRENCH SOUTHERN TERRITORIES
        'TG' => [],                            # TOGO
        'TH' => ['#####'],                     # THAILAND
        'TJ' => ['######'],                    # TAJIKISTAN
        'TK' => [],                            # TOKELAU
        'TL' => [],                            # EAST TIMOR
        'TM' => ['######'],                    # TURKMENISTAN
        'TN' => ['####'],                      # TUNISIA
        'TO' => [],                            # TONGA
        'TR' => ['#####'],                     # TURKEY
        'TT' => ['######'],                    # TRINIDAD AND TOBAGO
        'TV' => [],                            # TUVALU
        'TW' => ['###', '###-##'],             # TAIWAN
        'TZ' => ['#####'],                     # TANZANIA
        'UA' => ['#####'],                     # UKRAINE
        'UG' => [],                            # UGANDA
        'UM' => [],                            # UNITED STATES MINOR OUTLYING ISLANDS
        'US' => ['#####', '#####-####'],       # USA
        'UY' => ['#####'],                     # URUGUAY
        'UZ' => ['######'],                    # USBEKISTAN
        'VA' => ['00120'],                     # VATICAN CITY STATE
        'VC' => ['VC####'],                    # SAINT VINCENT AND THE GRENADINES
        'VE' => ['####', '####-@'],            # VENEZUELA
        'VG' => ['VG####'],                    # VIRGIN ISLANDS (BRITISH)
        'VI' => ['#####', '#####-####'],       # VIRGIN ISLANDS (U.S.)
        'VN' => ['######'],                    # VIETNAM
        'VU' => [],                            # VANUATU
        'WF' => ['986##'],                     # WALLIS AND FUTUNA ISLANDS
        'WS' => ['WS####'],                    # SAMOA
        'YE' => [],                            # YEMEN
        'YT' => ['976##'],                     # MAYOTTE
        'ZA' => ['####'],                      # SOUTH AFRICA
        'ZM' => ['#####'],                     # ZAMBIA
        'ZW' => [],                            # ZIMBABWE
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'zip_codes';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_code', 'county_code', 'city_code', 'city', 'address', 'zip_code', 'zip_code_extension','postal_designation', 'country'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'zip_code'  => 'required',
        'country'   => 'required'
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'zip_code'  => 'Código Postal',
        'city'      => 'Localidade',
    );


    public function isValid($countryCode, $postalCode, $ignoreSpaces = false)
    {
        if(!isset($this->formats[$countryCode]))
        {
            throw new \Exception('País '.strtoupper($countryCode).' inválido');
        }

        foreach($this->formats[$countryCode] as $format)
        {
            #echo $postalCode . ' - ' . $this->getFormatPattern($format)."\n";
            if(preg_match($this->getFormatPattern($format, $ignoreSpaces), $postalCode))
            {
                return true;
            }
        }

        if(!count($this->formats[$countryCode]))
        {
            return true;
        }

        return false;
    }

    public function getFormats($countryCode)
    {
        if(!isset($this->formats[$countryCode]))
        {
            throw new \Exception('País '.strtoupper($countryCode).' inválido');
        }

        return $this->formats[$countryCode];
    }

    public function hasCountry($countryCode)
    {
        return (isset($this->formats[$countryCode]));
    }

    public static function getDialCode($countryCode) {
        
        $dialCodes = [
            "bd" => "+880", 
            "be" => "+32", 
            "bf" => "+226", 
            "bg" => "+359", 
            "ba" => "+387", 
            "bb" => "+1-246", 
            "wf" => "+681", 
            "bl" => "+590", 
            "bm" => "+1-441", 
            "bn" => "+673", 
            "bo" => "+591", 
            "bh" => "+973", 
            "bi" => "+257", 
            "bj" => "+229", 
            "bt" => "+975", 
            "jm" => "+1-876", 
            "bw" => "+267", 
            "ws" => "+685", 
            "bq" => "+599", 
            "br" => "+55", 
            "bs" => "+1-242", 
            "je" => "+44-1534", 
            "by" => "+375", 
            "bz" => "+501", 
            "ru" => "+7", 
            "rw" => "+250", 
            "rs" => "+381", 
            "tl" => "+670", 
            "re" => "+262", 
            "tm" => "+993", 
            "tj" => "+992", 
            "ro" => "+40", 
            "tk" => "+690", 
            "gw" => "+245", 
            "gu" => "+1-671", 
            "gt" => "+502", 
            "gr" => "+30", 
            "gq" => "+240", 
            "gp" => "+590", 
            "jp" => "+81", 
            "gy" => "+592", 
            "gg" => "+44-1481", 
            "gf" => "+594", 
            "ge" => "+995", 
            "gd" => "+1-473", 
            "gb" => "+44", 
            "ga" => "+241", 
            "sv" => "+503", 
            "gn" => "+224", 
            "gm" => "+220", 
            "gl" => "+299", 
            "gi" => "+350", 
            "gh" => "+233", 
            "om" => "+968", 
            "tn" => "+216", 
            "jo" => "+962", 
            "hr" => "+385", 
            "ht" => "+509", 
            "hu" => "+36", 
            "hk" => "+852", 
            "hn" => "+504", 
            "hm" => " ", 
            "ve" => "+58", 
            "pr" => "+1-787 and 1-939", 
            "ps" => "+970", 
            "pw" => "+680", 
            "pt" => "+351", 
            "sj" => "+47", 
            "py" => "+595", 
            "iq" => "+964", 
            "pa" => "+507", 
            "pf" => "+689", 
            "pg" => "+675", 
            "pe" => "+51", 
            "pk" => "+92", 
            "ph" => "+63", 
            "pn" => "+870", 
            "pl" => "+48", 
            "pm" => "+508", 
            "zm" => "+260", 
            "eh" => "+212", 
            "ee" => "+372", 
            "eg" => "+20", 
            "za" => "+27", 
            "ec" => "+593", 
            "it" => "+39", 
            "vn" => "+84", 
            "sb" => "+677", 
            "et" => "+251", 
            "so" => "+252", 
            "zw" => "+263", 
            "sa" => "+966", 
            "es" => "+34", 
            "er" => "+291", 
            "me" => "+382", 
            "md" => "+373", 
            "mg" => "+261", 
            "mf" => "+590", 
            "ma" => "+212", 
            "mc" => "+377", 
            "uz" => "+998", 
            "mm" => "+95", 
            "ml" => "+223", 
            "mo" => "+853", 
            "mn" => "+976", 
            "mh" => "+692", 
            "mk" => "+389", 
            "mu" => "+230", 
            "mt" => "+356", 
            "mw" => "+265", 
            "mv" => "+960", 
            "mq" => "+596", 
            "mp" => "+1-670", 
            "ms" => "+1-664", 
            "mr" => "+222", 
            "im" => "+44-1624", 
            "ug" => "+256", 
            "tz" => "+255", 
            "my" => "+60", 
            "mx" => "+52", 
            "il" => "+972", 
            "fr" => "+33", 
            "io" => "+246", 
            "sh" => "+290", 
            "fi" => "+358", 
            "fj" => "+679", 
            "fk" => "+500", 
            "fm" => "+691", 
            "fo" => "+298", 
            "ni" => "+505", 
            "nl" => "+31", 
            "no" => "+47", 
            "na" => "+264", 
            "vu" => "+678", 
            "nc" => "+687", 
            "ne" => "+227", 
            "nf" => "+672", 
            "ng" => "+234", 
            "nz" => "+64", 
            "np" => "+977", 
            "nr" => "+674", 
            "nu" => "+683", 
            "ck" => "+682", 
            "ci" => "+225", 
            "ch" => "+41", 
            "co" => "+57", 
            "cn" => "+86", 
            "cm" => "+237", 
            "cl" => "+56", 
            "cc" => "+61", 
            "ca" => "+1", 
            "cg" => "+242", 
            "cf" => "+236", 
            "cd" => "+243", 
            "cz" => "+420", 
            "cy" => "+357", 
            "cx" => "+61", 
            "cr" => "+506", 
            "cw" => "+599", 
            "cv" => "+238", 
            "cu" => "+53", 
            "sz" => "+268", 
            "sy" => "+963", 
            "sx" => "+599", 
            "kg" => "+996", 
            "ke" => "+254", 
            "ss" => "+211", 
            "sr" => "+597", 
            "ki" => "+686", 
            "kh" => "+855", 
            "kn" => "+1-869", 
            "km" => "+269", 
            "st" => "+239", 
            "sk" => "+421", 
            "kr" => "+82", 
            "si" => "+386", 
            "kp" => "+850", 
            "kw" => "+965", 
            "sn" => "+221", 
            "sm" => "+378", 
            "sl" => "+232", 
            "sc" => "+248", 
            "kz" => "+7", 
            "ky" => "+1-345", 
            "sg" => "+65", 
            "se" => "+46", 
            "sd" => "+249", 
            "do" => "1-809 and 1-829", 
            "dm" => "+1-767", 
            "dj" => "+253", 
            "dk" => "+45", 
            "vg" => "+1-284", 
            "de" => "+49", 
            "ye" => "+967", 
            "dz" => "+213", 
            "us" => "+1", 
            "uy" => "+598", 
            "yt" => "+262", 
            "um" => "+1", 
            "lb" => "+961", 
            "lc" => "+1-758", 
            "la" => "+856", 
            "tv" => "+688", 
            "tw" => "+886", 
            "tt" => "+1-868", 
            "tr" => "+90", 
            "lk" => "+94", 
            "li" => "+423", 
            "lv" => "+371", 
            "to" => "+676", 
            "lt" => "+370", 
            "lu" => "+352", 
            "lr" => "+231", 
            "ls" => "+266", 
            "th" => "+66", 
            "tg" => "+228", 
            "td" => "+235", 
            "tc" => "+1-649", 
            "ly" => "+218", 
            "va" => "+379", 
            "vc" => "+1-784", 
            "ae" => "+971", 
            "ad" => "+376", 
            "ag" => "+1-268", 
            "af" => "+93", 
            "ai" => "+1-264", 
            "vi" => "+1-340", 
            "is" => "+354", 
            "ir" => "+98", 
            "am" => "+374", 
            "al" => "+355", 
            "ao" => "+244", 
            "as" => "+1-684", 
            "ar" => "+54", 
            "au" => "+61", 
            "at" => "+43", 
            "aw" => "+297", 
            "in" => "+91", 
            "ax" => "+358-18", 
            "az" => "+994", 
            "ie" => "+353", 
            "id" => "+62", 
            "ua" => "+380", 
            "qa" => "+974", 
            "mz" => "+258" 
         ]; 

         $countryCode = strtolower($countryCode);
         return @$dialCodes[$countryCode];
    }

    protected function getFormatPattern($format, $ignoreSpaces = false)
    {
        $pattern = str_replace('#', '\d', $format);
        $pattern = str_replace('@', '[a-zA-Z]', $pattern);
        $pattern = str_replace('*', '[a-zA-Z0-9]', $pattern);
        if ($ignoreSpaces)
        {
            $pattern = str_replace(' ', ' ?', $pattern);
        }
        return '/^' . $pattern . '$/';
    }

    /**
     * Convert zip code to integer
     * 
     * @param string $zipCode
     * @return int
     */
    public static function toInteger($zipCode) {
        $zipCode = removeWhiteSpaces($zipCode);

        $ascii = NULL;
        for ($i = 0; $i < strlen($zipCode); $i++) {
            // Convert char to ASCII (integer)
            $charAscii = ord($zipCode[$i]);

            /**
             * @author Daniel Almeida
             * --
             * Ao não convertermos números para ASCII
             * conseguimos poupar vários bytes ao converter para int,
             * visto que, o tipo int tem um limite e pode causar problemas
             * quando usado o valor a retornar para fazer comparações
             */
            if ($charAscii >= 48 && $charAscii <= 57) {
                $ascii .= $zipCode[$i];
                continue;
            }

            $ascii .= $charAscii;
        }

        // Adiciona um "1" à esquerda para não remover os zeros à esquerda ao converter para inteiro
        $ascii = "1" . $ascii;

        return intval($ascii);
    }

    /**
     * 
     * Relashionships
     * 
     */
    public function agency()
    {
        return $this->belongsTo('App\Models\Agency');
    }
}
