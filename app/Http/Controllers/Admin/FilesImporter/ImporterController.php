<?php

namespace App\Http\Controllers\Admin\FilesImporter;

use App\Models\Billing\Item;
use App\Models\Billing\ItemStockHistory;
use App\Models\Billing\VatRate;
use App\Models\BillingZone;
use App\Models\BrandModel;
use App\Models\Core\ProviderAgency;
use App\Models\CustomerService;
use App\Models\CustomerType;
use App\Models\CustomerWebservice;
use App\Models\FleetGest\FuelLog;
use App\Models\FleetGest\Vehicle;
use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ReceptionOrder;
use App\Models\Logistic\ReceptionOrderLine;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ShippingOrderLine;
use App\Models\Logistic\ShippingOrderStatus;
use App\Models\PaymentMethod;
use App\Models\PriceTable;
use App\Models\Route;
use App\Models\ShipmentPackDimension;
use App\Models\User;
use App\Models\Webservice\Base;
use App\Models\Agency;
use App\Models\Customer;
use App\Models\FleetGest\TollLog;
use App\Models\ImporterModel;
use App\Models\Logistic\Brand;
use App\Models\Logistic\Category as LogisticCategory;
use App\Models\Logistic\Family;
use App\Models\Logistic\LocationType;
use App\Models\Logistic\Model;
use App\Models\Logistic\SubCategory;
use App\Models\Logistic\Warehouse;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\AgencyZipCode;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Html, Log, Response, Excel, Setting, Auth;
use DateTime;

class ImporterController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'importer';

    /**
     * Excel column mapping
     * @var array
     */
    protected $columnMapping = [
        "a" => 0, "b" => 1, "c" => 2, "d" => 3, "e" => 4, "f" => 5, "g" => 6, "h" => 7, "i" => 8, "j" => 9, "k" => 10, "l" => 11, "m" => 12, "n" => 13, "o" => 14,
        "p" => 15, "q" => 16, "r" => 17, "s" => 18, "t" => 19, "u" => 20, "v" => 21, "w" => 22, "x" => 23, "y" => 24, "z" => 25,
        "aa" => 26, "ab" => 27, "ac" => 28, "ad" => 29, "ae" => 30, "af" => 31, "ag" => 32, "ah" => 33, "ai" => 34, "aj" => 35, "ak" => 36, "al" => 37,
        "am" => 38, "an" => 39, "ao" => 40, "ap" => 41, "aq" => 42, "ar" => 43, "as" => 44, "at" => 45, "au" => 46, "av" => 47, "aw" => 48, "ax" => 49,
        "ay" => 50, "az" => 51, "ba" => 52, "bb" => 53, "bc" => 54, "bd" => 55, "be" => 56, "bf" => 57, "bg" => 58, "bh" => 59, "bi" => 60, "bj" => 61,
        "bk" => 62, "bl" => 63, "bm" => 64, "bn" => 65, "bo" => 66, "bp" => 67, "bq" => 68, "br" => 69, "bs" => 70, "bt" => 71, "bu" => 72, "bv" => 73,
        "bw" => 74, "bx" => 75, "by" => 76, "bz" => 77, "ca" => 78, "cb" => 79, "cc" => 80, "cd" => 81, "ce" => 82, "cf" => 83, "cg" => 84, "ch" => 85,
        "ci" => 86, "cj" => 87, "ck" => 88, "cl" => 89, "cm" => 90, "cn" => 91, "co" => 92, "cp" => 93, "cq" => 94, "cr" => 95, "cs" => 96, "ct" => 97,
        "cu" => 98, "cv" => 99, "cw" => 100, "cx" => 101, "cy" => 102, "cz" => 103, "da" => 104, "db" => 105, "dc" => 106, "dd" => 107, "de" => 108,
        "df" => 109, "dg" => 110, "dh" => 111, "di" => 112, "dj" => 113, "dk" => 114, "dl" => 115, "dm" => 116, "dn" => 117, "do" => 118, "dp" => 119,
        "dq" => 120, "dr" => 121, "ds" => 122, "dt" => 123, "du" => 124, "dv" => 125, "dw" => 126, "dx" => 127, "dy" => 128, "dz" => 129, "ea" => 130,
        "eb" => 131, "ec" => 132, "ed" => 133, "ee" => 134, "ef" => 135, "eg" => 136, "eh" => 137, "ei" => 138, "ej" => 139, "ek" => 140, "el" => 141,
        "em" => 142, "en" => 143, "eo" => 144, "ep" => 145, "eq" => 146, "er" => 147, "es" => 148, "et" => 149, "eu" => 150, "ev" => 151, "ew" => 152,
        "ex" => 153, "ey" => 154, "ez" => 155, "fa" => 156, "fb" => 157, "fc" => 158, "fd" => 159, "fe" => 160, "ff" => 161, "fg" => 162, "fh" => 163,
        "fi" => 164, "fj" => 165, "fk" => 166, "fl" => 167, "fm" => 168, "fn" => 169, "fo" => 170, "fp" => 171, "fq" => 172, "fr" => 173, "fs" => 174,
        "ft" => 175, "fu" => 176, "fv" => 177, "fw" => 178, "fx" => 179, "fy" => 180, "fz" => 181, "ga" => 182, "gb" => 183, "gc" => 184, "gd" => 185,
        "ge" => 186, "gf" => 187, "gg" => 188, "gh" => 189, "gi" => 190, "gj" => 191, "gk" => 192, "gl" => 193, "gm" => 194, "gn" => 195, "go" => 196,
        "gp" => 197, "gq" => 198, "gr" => 199, "gs" => 200, "gt" => 201, "gu" => 202, "gv" => 203, "gw" => 204, "gx" => 205, "gy" => 206, "gz" => 207,
        "ha" => 208, "hb" => 209, "hc" => 210, "hd" => 211, "he" => 212, "hf" => 213, "hg" => 214, "hh" => 215, "hi" => 216, "hj" => 217, "hk" => 218,
        "hl" => 219, "hm" => 220, "hn" => 221, "ho" => 222, "hp" => 223, "hq" => 224, "hr" => 225, "hs" => 226, "ht" => 227, "hu" => 228, "hv" => 229,
        "hw" => 230, "hx" => 231, "hy" => 232, "hz" => 233, "ia" => 234, "ib" => 235, "ic" => 236, "id" => 237, "ie" => 238, "if" => 239, "ig" => 240,
        "ih" => 241, "ii" => 242, "ij" => 243, "ik" => 244, "il" => 245, "im" => 246, "in" => 247, "io" => 248, "ip" => 249, "iq" => 250, "ir" => 251,
        "is" => 252, "it" => 253, "iu" => 254, "iv" => 255, "iw" => 256, "ix" => 257, "iy" => 258, "iz" => 259, "ja" => 260, "jb" => 261, "jc" => 262,
        "jd" => 263, "je" => 264, "jf" => 265, "jg" => 266, "jh" => 267, "ji" => 268, "jj" => 269, "jk" => 270, "jl" => 271, "jm" => 272, "jn" => 273,
        "jo" => 274, "jp" => 275, "jq" => 276, "jr" => 277, "js" => 278, "jt" => 279, "ju" => 280, "jv" => 281, "jw" => 282, "jx" => 283, "jy" => 284,
        "jz" => 285, "ka" => 286, "kb" => 287, "kc" => 288, "kd" => 289, "ke" => 290, "kf" => 291, "kg" => 292, "kh" => 293, "ki" => 294, "kj" => 295,
        "kk" => 296, "kl" => 297, "km" => 298, "kn" => 299, "ko" => 300, "kp" => 301, "kq" => 302, "kr" => 303, "ks" => 304, "kt" => 305, "ku" => 306,
        "kv" => 307, "kw" => 308, "kx" => 309, "ky" => 310, "kz" => 311, "la" => 312, "lb" => 313, "lc" => 314, "ld" => 315, "le" => 316, "lf" => 317,
        "lg" => 318, "lh" => 319, "li" => 320, "lj" => 321, "lk" => 322, "ll" => 323, "lm" => 324, "ln" => 325, "lo" => 326, "lp" => 327, "lq" => 328,
        "lr" => 329, "ls" => 330, "lt" => 331, "lu" => 332, "lv" => 333, "lw" => 334, "lx" => 335, "ly" => 336, "lz" => 337, "ma" => 338, "mb" => 339,
        "mc" => 340, "md" => 341, "me" => 342, "mf" => 343, "mg" => 344, "mh" => 345, "mi" => 346, "mj" => 347, "mk" => 348, "ml" => 349, "mm" => 350,
        "mn" => 351, "mo" => 352, "mp" => 353, "mq" => 354, "mr" => 355, "ms" => 356, "mt" => 357, "mu" => 358, "mv" => 359, "mw" => 360, "mx" => 361,
        "my" => 362, "mz" => 363, "na" => 364, "nb" => 365, "nc" => 366, "nd" => 367, "ne" => 368, "nf" => 369, "ng" => 370, "nh" => 371, "ni" => 372,
        "nj" => 373, "nk" => 374, "nl" => 375, "nm" => 376, "nn" => 377, "no" => 378, "np" => 379, "nq" => 380, "nr" => 381, "ns" => 382, "nt" => 383,
        "nu" => 384, "nv" => 385, "nw" => 386, "nx" => 387, "ny" => 388, "nz" => 389, "oa" => 390, "ob" => 391, "oc" => 392, "od" => 393, "oe" => 394,
        "of" => 395, "og" => 396, "oh" => 397, "oi" => 398, "oj" => 399, "ok" => 400, "ol" => 401, "om" => 402, "on" => 403, "oo" => 404, "op" => 405,
        "oq" => 406, "or" => 407, "os" => 408, "ot" => 409, "ou" => 410, "ov" => 411, "ow" => 412, "ox" => 413, "oy" => 414, "oz" => 415, "pa" => 416,
        "pb" => 417, "pc" => 418, "pd" => 419, "pe" => 420, "pf" => 421, "pg" => 422, "ph" => 423, "pi" => 424, "pj" => 425, "pk" => 426, "pl" => 427,
        "pm" => 428, "pn" => 429, "po" => 430, "pp" => 431, "pq" => 432, "pr" => 433, "ps" => 434, "pt" => 435, "pu" => 436, "pv" => 437, "pw" => 438,
        "px" => 439, "py" => 440, "pz" => 441, "qa" => 442, "qb" => 443, "qc" => 444, "qd" => 445, "qe" => 446, "qf" => 447, "qg" => 448, "qh" => 449,
        "qi" => 450, "qj" => 451, "qk" => 452, "ql" => 453, "qm" => 454, "qn" => 455, "qo" => 456, "qp" => 457, "qq" => 458, "qr" => 459, "qs" => 460,
        "qt" => 461, "qu" => 462, "qv" => 463, "qw" => 464, "qx" => 465, "qy" => 466, "qz" => 467, "ra" => 468, "rb" => 469, "rc" => 470, "rd" => 471,
        "re" => 472, "rf" => 473, "rg" => 474, "rh" => 475, "ri" => 476, "rj" => 477, "rk" => 478, "rl" => 479, "rm" => 480, "rn" => 481, "ro" => 482,
        "rp" => 483, "rq" => 484, "rr" => 485, "rs" => 486, "rt" => 487, "ru" => 488, "rv" => 489, "rw" => 490, "rx" => 491, "ry" => 492, "rz" => 493,
        "sa" => 494, "sb" => 495, "sc" => 496, "sd" => 497, "se" => 498, "sf" => 499, "sg" => 500, "sh" => 501, "si" => 502, "sj" => 503, "sk" => 504,
        "sl" => 505, "sm" => 506, "sn" => 507, "so" => 508, "sp" => 509, "sq" => 510, "sr" => 511, "ss" => 512, "st" => 513, "su" => 514, "sv" => 515,
        "sw" => 516, "sx" => 517, "sy" => 518, "sz" => 519, "ta" => 520, "tb" => 521, "tc" => 522, "td" => 523, "te" => 524, "tf" => 525, "tg" => 526,
        "th" => 527, "ti" => 528, "tj" => 529, "tk" => 530, "tl" => 531, "tm" => 532, "tn" => 533, "to" => 534, "tp" => 535, "tq" => 536, "tr" => 537,
        "ts" => 538, "tt" => 539, "tu" => 540, "tv" => 541, "tw" => 542, "tx" => 543, "ty" => 544, "tz" => 545, "ua" => 546, "ub" => 547, "uc" => 548,
        "ud" => 549, "ue" => 550, "uf" => 551, "ug" => 552, "uh" => 553, "ui" => 554, "uj" => 555, "uk" => 556, "ul" => 557, "um" => 558, "un" => 559,
        "uo" => 560, "up" => 561, "uq" => 562, "ur" => 563, "us" => 564, "ut" => 565, "uu" => 566, "uv" => 567, "uw" => 568, "ux" => 569, "uy" => 570,
        "uz" => 571, "va" => 572, "vb" => 573, "vc" => 574, "vd" => 575, "ve" => 576, "vf" => 577, "vg" => 578, "vh" => 579, "vi" => 580, "vj" => 581,
        "vk" => 582, "vl" => 583, "vm" => 584, "vn" => 585, "vo" => 586, "vp" => 587, "vq" => 588, "vr" => 589, "vs" => 590, "vt" => 591, "vu" => 592,
        "vv" => 593, "vw" => 594, "vx" => 595, "vy" => 596, "vz" => 597, "wa" => 598, "wb" => 599, "wc" => 600, "wd" => 601, "we" => 602, "wf" => 603,
        "wg" => 604, "wh" => 605, "wi" => 606, "wj" => 607, "wk" => 608, "wl" => 609, "wm" => 610, "wn" => 611, "wo" => 612, "wp" => 613, "wq" => 614,
        "wr" => 615, "ws" => 616, "wt" => 617, "wu" => 618, "wv" => 619, "ww" => 620, "wx" => 621, "wy" => 622, "wz" => 623, "xa" => 624, "xb" => 625,
        "xc" => 626, "xd" => 627, "xe" => 628, "xf" => 629, "xg" => 630, "xh" => 631, "xi" => 632, "xj" => 633, "xk" => 634, "xl" => 635, "xm" => 636,
        "xn" => 637, "xo" => 638, "xp" => 639, "xq" => 640, "xr" => 641, "xs" => 642, "xt" => 643, "xu" => 644, "xv" => 645, "xw" => 646, "xx" => 647,
        "xy" => 648, "xz" => 649, "ya" => 650, "yb" => 651, "yc" => 652, "yd" => 653, "ye" => 654, "yf" => 655, "yg" => 656, "yh" => 657, "yi" => 658,
        "yj" => 659, "yk" => 660, "yl" => 661, "ym" => 662, "yn" => 663, "yo" => 664, "yp" => 665, "yq" => 666, "yr" => 667, "ys" => 668, "yt" => 669,
        "yu" => 670, "yv" => 671, "yw" => 672, "yx" => 673, "yy" => 674, "yz" => 675, "za" => 676, "zb" => 677, "zc" => 678, "zd" => 679, "ze" => 680,
        "zf" => 681, "zg" => 682, "zh" => 683, "zi" => 684, "zj" => 685, "zk" => 686, "zl" => 687, "zm" => 688, "zn" => 689, "zo" => 690, "zp" => 691,
        "zq" => 692, "zr" => 693, "zs" => 694, "zt" => 695, "zu" => 696, "zv" => 697, "zw" => 698, "zx" => 699, "zy" => 700, "zz" => 701
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',importer']);
        validateModule('importer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code')
            ->get());

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $customerTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $models = ImporterModel::filterSource()->get();
        $models = $this->listModels($models);

        $hasErrors   = false;
        $previewRows = false;

        $data = compact(
            'agencies',
            'providers',
            'customerTypes',
            'models',
            'hasErrors',
            'previewMode',
            'previewRows',
            'pricesTables',
            'modelsArr'
        );

        return $this->setContent('admin.files_importer.index', $data);
    }


    /**
     * Init importation process
     *
     * @return \Illuminate\Http\Response
     */
    public function executeImportation(Request $request)
    {
        $file         = $request->file('file');
        $filepath     = $request->get('filepath');
        $previewMode  = $request->get('preview_mode');
        $directImport = $request->get('direct_import', false);

        $model = ImporterModel::filterSource()
            ->find($request->import_model);

        if (!$model) {
            return Redirect::back()->with('error', 'O método escolhido não foi encontrado no sistema.');
        }
        /* try {*/
        $model->mapping = (array) $model->mapping;
        $method = $model->type ? $model->type : 'shipments';

        if (!empty($file)) {
            $destinationPath = storage_path() . '/importer/';
            $filename        = 'temporary.' . $file->getClientOriginalExtension();
            $filepath        = $destinationPath . $filename;

            if (!$file->move($destinationPath, $filename)) {
                return Redirect::back()->with('error', 'Não foi possível validar o ficheiro a carregar. Verifique se o tamanho é inferior a 2MB ou a extensão.');
            }
            $previewMode = 1;
        } elseif (empty($filepath)) {
            return Redirect::back()->withInput()->with('error', 'Não foi encontrado o ficheiro para imporar. Tente de novo.');
        }

        return $this->{$method}($request, $model, $filepath, $previewMode, $directImport);
        /* } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Falha na leitura do ficheiro. Algum campo ou o modelo de importação estão mal configurados. [' . $e->getMessage().' on line '.$e->getLine().']');
        }*/
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function shipments(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $str = file_get_contents($filepath);
        $enc = mb_detect_encoding($str, mb_list_encodings(), true);

        /*if($enc != 'UTF-8') {
            return Redirect::back()->with('error', 'Codificação do ficheiro incorreta ('.$enc.', quando é esperado UTF-8)');
        }*/

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }


        $headerRow = [];
        foreach (trans('admin/importer.shipments') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $agencies       = Agency::get();
        $services       = Service::filterSource()->get();
        $shipmentStatus = ShippingStatus::pluck('name', 'id')->toArray();
        $operators      = User::filterSource()->where('code', '<>', '')->pluck('id', 'code')->toArray();
        $customersIds   = [];
        $customersArr   = [];
        $printIds       = [];
        $hasErrors      = 0;
        $rpackArr       = explode(',', $request->get('rpack'));
        $rguideArr      = explode(',', $request->get('rguide'));
        $rcheckArr      = explode(',', $request->get('rcheck'));
        $autoSubmit     = $request->get('auto_submit', false);
        $providerId     = $model->provider_id ? $model->provider_id : @$request->provider_id;
        $webservice     = null;
        if (!empty($providerId)) {
            $webservice = WebserviceConfig::where('provider_id', $providerId)->first();
        }

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, $services, $operators, &$printIds, $agencies, $model, $autoSubmit, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$customersArr, &$hasErrors, $shipmentStatus, $rcheckArr, $rpackArr, $rguideArr, $providerId, $webservice) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, $services, $operators, &$printIds, $agencies, $model, $autoSubmit, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$customersArr, &$hasErrors, $shipmentStatus, &$i, $rcheckArr, $rpackArr, $rguideArr, $providerId, $webservice) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['rpack']  = empty($row['rpack']) ? 0 : 1;
                    $row['rcheck'] = empty($row['rcheck']) ? 0 : 1;
                    $row['rguide'] = empty($row['rguide']) ? 0 : 1;

                    $row['customer_code'] = $model->customer_code ?  $model->customer_code : @$row['customer_code'];
                    $input['service_id']  = $model->servide_id;

                    $row['sender_address']      = @$row['sender_address'] . ' ' . @$row['sender_address_2'];
                    $row['recipient_address']   = @$row['recipient_address'] . ' ' . @$row['recipient_address_2'];
                    $row['sender_address']      = trim($row['sender_address']);
                    $row['recipient_address']   = trim($row['recipient_address']);

                    if ($row['customer_code'] == 005 && config('app.source') == 'categorinauta') {

                        if (!empty($row['sender_city']) || !empty($row['sender_address'])) {
                            if ($row['sender_city']) {
                                $row['sender_address'] = $row['sender_city'];
                            } else {
                                $row['sender_city'] = $row['sender_address'];
                            }
                        }

                        if (!empty($row['recipient_address']) || !empty($row['recipient_city'])) {
                            if ($row['recipient_address']) {
                                $row['recipient_city'] = $row['recipient_address'];
                            } else {
                                $row['recipient_address'] = $row['recipient_city'];
                            }
                        }

                        if (empty($row['recipient_name'])) {
                            $row['recipient_name'] = 'AGUARDA NOVOS DADOS';
                        }

                        $row['sender_zip_code'] = $row['recipient_zip_code'] = '0000-000';
                    }


                    if (@$row['charge_price']) {
                        $row['charge_price'] = str_replace('no', '', $row['charge_price']);
                        $row['charge_price'] = str_replace('€', '', $row['charge_price']);
                        $row['charge_price'] = str_replace('eur', '', $row['charge_price']);
                    }

                    if (@$row['shipping_price']) {
                        $row['shipping_price'] = forceDecimal($row['shipping_price']);
                    }

                    if (empty(@$row['recipient_name']) || empty($row['recipient_address']) || empty(@$row['recipient_zip_code'])) {
                        $errors[] = 'Há dados do destinatário obrigatórios em falta (Nome ou Morada ou Código Postal ou País).';
                    }

                    if (@$row['operator_id']) {
                        $row['operator_id'] = @$operators[@$row['operator_id']];
                    } else {
                        $row['operator_id'] = null;
                    }

                    $row['trk'] = null;
                    if (substr(strtolower(@$row['reference']), 0, 3) === "trk") {
                        $row['trk'] = substr(@$row['reference'], 3, 12);
                    }

                    try {
                        if (!empty($row['date'])) {
                            if ($row['date'] && in_array($model->date_format, ['d.m.Y'])) {
                                $row['date'] = str_replace('.', '-', $row['date']);
                                $row['date'] = new Carbon($row['date']);
                            } elseif ($row['date'] && in_array($model->date_format, ['dmY'])) {
                                $dt = str_pad($row['date'], 8, '0', STR_PAD_LEFT);
                                $row['date'] = substr($dt, 0, 2) . '-' . substr($dt, 2, 2) . '-' . substr($dt, 4);
                            }
                        }
                    } catch (\Exception $e) {
                        $errors[] = 'Erro ao ler formato da data. Verifique se a data no ficheiro corresponde ao formato configurado no modelo.';
                    }

                    try {
                        if (isset($row['delivery_date']) && !empty($row['delivery_date'])) {
                            $row['delivery_date'] = new Carbon($row['delivery_date']);
                            $row['delivery_date'] = $row['delivery_date']->format('Y-m-d');
                        } else {
                            unset($row['delivery_date']);
                        }
                    } catch (\Exception $e) {
                        $errors[] = 'Data de entrega inválida: ' . $e->getMessage();
                    }


                    $shipment = null;

                    if (isset($row['trk']) && !empty($row['trk'])) {
                        $trk = @$row['trk'];

                        $myAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

                        $shipment = Shipment::where('tracking_code', $trk)
                            ->where(function ($q) use ($myAgencies) {
                                $q->whereIn('agency_id', $myAgencies);
                                $q->orWhereIn('sender_agency_id', $myAgencies);
                                $q->orWhereIn('recipient_agency_id', $myAgencies);
                            })
                            ->first();
                    } elseif (isset($row['provider_tracking_code']) && !empty($row['provider_tracking_code'])) {
                        $providerTrk = @$row['provider_tracking_code'];

                        $myAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

                        $shipment = Shipment::where('provider_tracking_code', 'like', '%' . $providerTrk . '%') //procura envio com o trk do fornecedor apenas para envios onde a minha agencia esteja envolvida.
                            ->where(function ($q) use ($myAgencies) {
                                $q->whereIn('agency_id', $myAgencies);
                                $q->orWhereIn('sender_agency_id', $myAgencies);
                                $q->orWhereIn('recipient_agency_id', $myAgencies);
                            })
                            ->first();
                    } else if ($row['customer_code'] == 005 && config('app.source') == 'categorinauta') {

                        $reference = $row['reference'];

                        $myAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

                        $shipment = Shipment::where('reference', $reference) //procura envio com o trk do fornecedor apenas para envios onde a minha agencia esteja envolvida.
                            ->where(function ($q) use ($myAgencies) {
                                $q->whereIn('agency_id', $myAgencies);
                                $q->orWhereIn('sender_agency_id', $myAgencies);
                                $q->orWhereIn('recipient_agency_id', $myAgencies);
                            })
                            ->first();
                    }

                    if (!$shipment) {
                        $shipment = new Shipment();
                    }

                    $isCollection = false;

                    /**
                     * SERVICE
                     */
                    if (!$shipment->exists) {

                        $code = isset($row['service_code']) && !empty($row['service_code']) ? strtoupper($row['service_code']) : null;

                        /**
                         * Get service based on service id and time from GLS
                         */
                        $auxShipment = new Shipment();

                        $zipCode = $this->getZipCode($shipment, @$row['sender_country'], @$row['sender_zip_code']);
                        $auxShipment->sender_zip_code = $zipCode['zip_code'];
                        $auxShipment->sender_country  = $zipCode['country'];

                        $zipCode = $this->getZipCode($shipment, @$row['recipient_country'], @$row['recipient_zip_code']);
                        $auxShipment->recipient_zip_code = $zipCode['zip_code'];
                        $auxShipment->recipient_country  = $zipCode['country'];

                        $foundGlsService = false;
                        if (!empty($row['recipient_country']) && $webservice && in_array($webservice->method, ['gls_zeta'])) {
                            $glsTime = $row['gls_time'] ?? null;

                            foreach (($webservice->mapping_services ?? []) as $id => $map) {
                                $country = strtolower($row['recipient_country']);
                                if (!in_array($country, ['pt', 'es'])) {
                                    $country = 'int';
                                }

                                $value      = $map[$country] ?? null;
                                $checkValue = $code . '#' . $glsTime;
                                if ($checkValue != $value) {
                                    continue;
                                }

                                $service = $services->filter(function ($item) use ($id) {
                                    return $item->id == $id;
                                })->first();

                                if (!$service) {
                                    continue;
                                }

                                /**
                                 * @author Daniel Almeida
                                 * 
                                 * Get the shipment billing_zone and check if the service has that billing zone configured
                                 * because GLS service (id:time) can be linked to multiple services (ENOVO)
                                 * and without this the last service with that configuration would be selected
                                 * --
                                 * Attention that this is not perfect because it can also select the wrong service
                                 * but atleast it tries to guess it based on the services configuration
                                 */
                                $auxShipment->service = $service;
                                $billingZone = @Shipment::getPricesZone($auxShipment)['billing_zone'];
                                if (in_array($billingZone, $service->zones)) {
                                    $foundGlsService = true;
                                    break;
                                }
                            }
                        }
                        /**-- */

                        if (!$foundGlsService) {
                            if (empty($code) && $model->service_id) { //obtem serviço pelo seu ID
                                $service = $services->filter(function ($item) use ($model) {
                                    return $item->id == $model->service_id;
                                })->first();
                            } else { //caso contrário, obtem pelo codigo de serviço

                                $code = empty($code) ? '24H' : $code;
                                $service = $services->filter(function ($item) use ($code) {
                                    return $item->display_code == $code;
                                })->first();
                            }
                        }

                        if ($service) {
                            $shipment->service_id    = $service->id;
                            $shipment->is_collection = $service->is_collection;

                            if (isset($row['is_collection'])) {
                                $shipment->is_collection = $row['is_collection'];
                            }
                        } else {
                            $errors[] = 'Não existe nenhum serviço com o código ' . $code . '.';
                        }
                    }
                    /**-- */


                    /**
                     * CUSTOMER
                     */
                    if (!$shipment->exists && !empty($row['customer_code'])) {
                        if (@$customersArr[$row['customer_code']]) {
                            $customer = @$customersArr[$row['customer_code']];
                        } else {
                            $customer = Customer::where('code', $row['customer_code'])
                                ->whereSource(config('app.source'))
                                ->whereNull('customer_id')
                                ->first(['id', 'code', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email']);

                            $customersArr[$row['customer_code']] = $customer;
                        }

                        if ($customer) {
                            $customersIds[] = $customer->id;
                            $row['customer_id']  = $customer->id;
                            $row['requested_by'] = $customer->id;
                            $row['sender_name'] = isset($row['sender_name']) && !empty($row['sender_name']) ? $row['sender_name'] : $customer->name;
                            $row['sender_address'] = isset($row['sender_address']) && !empty($row['sender_address']) ? $row['sender_address'] : $customer->address;
                            $row['sender_zip_code'] = isset($row['sender_zip_code']) && !empty($row['sender_zip_code']) ? $row['sender_zip_code'] : $customer->zip_code;
                            $row['sender_city'] = isset($row['sender_city']) && !empty($row['sender_city']) ? $row['sender_city'] : $customer->city;
                            $row['sender_country'] = isset($row['sender_country']) && !empty($row['sender_country']) ? $row['sender_country'] : $customer->country;
                            $row['sender_phone'] = isset($row['sender_phone']) && !empty($row['sender_phone']) ? $row['sender_phone'] : $customer->phone;
                        } else {
                            $row['customer_id'] = null;
                        }
                    } else {
                        $customersIds[] = $shipment->customer_id;
                    }
                    /**-- */


                    /**
                     * WEBSERVICE
                     */
                    if (!$shipment->exists && !empty($providerId) && !empty(@$row['provider_tracking_code'])) {
                        $customerWebservice = CustomerWebservice::where('customer_id', (@$customer->id ?? @$shipment->customer_id))
                            ->where('provider_id', $providerId)
                            ->first();

                        if ($customerWebservice) {
                            $row['webservice_method'] = $customerWebservice->method;
                            $row['submited_at']       = Carbon::now();
                        }
                    }
                    /**-- */


                    /**
                     * DEPARTMENT
                     */

                    if (!empty($row['department_code'])) {
                        $department = Customer::where('code', $row['department_code'])
                            ->where('customer_id', $row['customer_id'])
                            ->whereSource(config('app.source'))
                            ->first(['id', 'customer_id', 'code', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email']);

                        if ($department) {
                            $customersIds[] = $department->id;
                            $row['department_id'] = $department->id;
                            $row['requested_by'] = $department->customer_id;
                            $row['sender_name'] =  $department->name;
                            $row['sender_address'] =  $department->address;
                            $row['sender_zip_code'] = $department->zip_code;
                            $row['sender_city'] =  $department->city;
                            $row['sender_country'] = $department->country;
                            $row['sender_phone'] = $department->phone;
                        } else {
                            $row['department_id'] = null;
                        }
                    }

                    /**
                     * DATE
                     */

                    $date = $shipment->date ? $shipment->date : date('Y-m-d');
                    $row['date'] = isset($row['date']) && !empty($row['date']) ? $row['date'] : $date;

                    try {
                        $shipment->date = new Carbon($row['date']);
                        $shipment->date = $shipment->date->format('Y-m-d');
                    } catch (\Exception $e) {
                        $errors[] = 'Data do envio inválida: ' . $e->getMessage();
                    }


                    if (@$row['start_hour']) {
                        $row['start_hour'] = trim(@$row['start_hour']);
                        $row['start_hour'] = new Carbon($row['start_hour']);
                        $row['start_hour'] = $row['start_hour']->format('H:i');
                    }

                    if (@$row['end_hour']) {
                        $row['end_hour'] = trim(@$row['end_hour']);
                        $row['end_hour'] = new Carbon($row['end_hour']);
                        $row['end_hour'] = $row['end_hour']->format('H:i');
                    }

                    /**
                     * VOLUMES
                     */
                    $shipment->volumes = isset($row['volumes']) && !empty($row['volumes']) ? (int)$row['volumes'] : 1;

                    /**
                     * KM
                     */
                    $shipment->kms = isset($row['km']) && !empty($row['km']) ? (float) $row['km'] : null;


                    /**
                     * WEIGHT
                     */
                    $shipment->weight = $this->getWeight($shipment, @$row['weight'], @$row['original_weight'], @$row['volumetric_weight'], @$row['fator_m3']);
                    if (empty($shipment->weight) || $shipment->weight == 0.00) {
                        $errors[] = 'Envio sem peso.';
                    }

                    /**
                     * CHARGE PRICE
                     */
                    $shipment->charge_price = $shipment->exists ? $shipment->charge_price : @$row['charge_price'];
                    $shipment->total_price_for_recipient = $shipment->exists ? $shipment->total_price_for_recipient : @$row['total_price_for_recipient'];
                    $shipment->payment_at_recipient = !empty($shipment->total_price_for_recipient) ? true : false;

                    /**
                     * STATUS
                     */
                    if (!$shipment->exists) {

                        $statusId = isset($row['status_code']) && !empty($row['status_code']) ? $row['status_code'] : Setting::get('importer_default_status', ShippingStatus::ACCEPTED_ID);

                        if (!empty($row['status_code'])) {
                            if ($model->mapping_method) {
                                $providerStatus = config('shipments_import_mapping.' . $model->mapping_method . '-status');

                                if (isset($providerStatus[$statusId])) {
                                    $statusId = $providerStatus[$statusId];
                                } else {
                                    $errors[] = 'O estado da ' . $model->mapping_method . ' com o código ' . @$row['status_code'] . ' não tem correspondencia com os estados da plataforma.';
                                }
                            }
                        }

                        if (isset($shipmentStatus[$statusId])) {
                            $shipment->status_id = $statusId;
                        } else {
                            $errors[] = 'Não existe nenhum estado com o código ' . $code . '.';
                        }
                    }

                    /**
                     * SENDER COUNTRY
                     */
                    $shipment->mapping_method = $model->mapping_method;

                    $zipCode = $this->getZipCode($shipment, @$row['sender_country'], @$row['sender_zip_code']);
                    $shipment->sender_zip_code = $zipCode['zip_code'] ? $zipCode['zip_code'] : $shipment->sender_zip_code;
                    $shipment->sender_country  = $zipCode['country'] ? $zipCode['country'] : $shipment->sender_country;

                    $zipCode = $this->getZipCode($shipment, @$row['recipient_country'], @$row['recipient_zip_code']);
                    $shipment->recipient_zip_code = $zipCode['zip_code'] ? $zipCode['zip_code'] : $shipment->recipient_zip_code;
                    $shipment->recipient_country  = $zipCode['country'] ? $zipCode['country'] : $shipment->recipient_country;

                    /**
                     * OBS
                     */
                    $row['obs'] = @$row['obs'] ? substr($row['obs'], 0, 150) : '';

                    /**
                     * AGENCY
                     */
                    $agencies = $this->getAgencies($shipment, $agencies, @$request->provider_id, @$request->agency_id, @$request->recipient_agency_id);
                    $shipment->fill($agencies);

                    /**
                     * PROVIDER
                     * Agency already put the default provider id and the zip_code zone provider_id
                     * This override if the server has a provider_id
                     */
                    if (!empty($service->provider_id)) {
                        $shipment->provider_id = $service->provider_id;
                    }

                    if (empty($shipment->provider_id) && !empty($providerId)) {
                        $shipment->provider_id = $providerId;
                    }

                    /**
                     * Expenses in the Excel
                     */
                    if (!$shipment->exists) {

                        $value = Setting::get('expense1');
                        if (!empty($row['expense1']) && $value) {
                            $row['complementar_services'][] = $value;
                            $row['complementar_services_qty'][$value] = $row['expense1'];
                        }

                        $value = Setting::get('expense2');
                        if (!empty($row['expense2']) && $value) {
                            $row['complementar_services'][] = $value;
                            $row['complementar_services_qty'][$value] = $row['expense1'];
                        }

                        $value = Setting::get('expense3');
                        if (!empty($row['expense3']) && $value) {
                            $row['complementar_services'][] = $value;
                            $row['complementar_services_qty'][$value] = $row['expense1'];
                        }
                    }


                    /**
                     * CATEGORIA NAUTA E CLIENTE LUÍS SIMÕES - SEGUNDA IMPORTAÇÃO
                     */
                    if ($shipment->exists && $row['customer_code'] == 005 && config('app.source') == 'categorinauta') {
                        $row['recipient_name'] = $shipment->recipient_name ? $shipment->recipient_name : $row['recipient_name'];
                    }

                    /**
                     * Verifica se o reboque e viatura existem em sistema
                     */
                    // //VECHILE AND TRAILER
                    // if(@$row['trailer']){                 

                    //     $trailer = Vehicle::where(function($q) use ($row){
                    //         $q->where('license_plate', $row['trailer']);
                    //         $q->orWhere('name', $row['trailer']);
                    //     })->first();

                    //     if($trailer){
                    //        $row['trailer'] = $trailer->name; 
                    //     }else{
                    //         $errors[] = 'Este reboque não se encontra em sistema';
                    //     }

                    // }

                    // if(@$row['vechile']){          

                    //     $vechile = Vehicle::where(function($q) use ($row){
                    //         $q->where('license_plate', $row['trailer']);
                    //         $q->orWhere('name', $row['trailer']);
                    //     })->first();
                    //     if($vechile){
                    //         $row['vechile'] = $vechile->name; 
                    //     }else{
                    //         $errors[] = 'Esta viatura não se encontra em sistema';
                    //     }
                    // }

                    /**
                     * Atualiza os campos do envio
                     */
                    unset($shipment->mapping_method);
                    unset(
                        $row['volumes'],
                        $row['weight'],
                        $row['date'],
                        $row['charge_price'],
                        $row['sender_country'],
                        $row['sender_zip_code'],
                        $row['recipient_country'],
                        $row['recipient_zip_code'],
                        $row['provider_id'],
                        $row['agency_id'],
                        $row['recipient_agency_id']
                    );

                    $shipment->fill($row);
                    $shipment->sender_phone    = nospace($shipment->sender_phone);
                    $shipment->recipient_phone = nospace($shipment->recipient_phone);

                    $saveIds = (!$shipment->exists && $request->print_labels) ? true : false;

                    if (empty($shipment->customer_id)) {
                        $errors[] = 'Este envio não está associado a nenhum cliente.';
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $shipment->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $shipment->rpack  = @$row['rpack'];
                        $shipment->rguide = @$row['rguide'];
                        $shipment->rcheck = @$row['rcheck'];

                        if (@$row['status_date']) {
                            $shipment->status_date = @$row['status_date']->format('Y-m-d');
                        }

                        if (@$row['status_hour']) {
                            $shipment->status_hour = @$row['status_hour']->format('H:i');
                        }

                        $previewRows[] = $shipment->toArray();
                    } else {

                        $hasReturn = [];

                        if (in_array((string) $i, $rpackArr, true)) {
                            $hasReturn[] = 'rpack';
                        }

                        if (in_array((string) $i, $rcheckArr, true)) {
                            $hasReturn[] = 'rcheck';
                        }

                        if (in_array((string) $i, $rguideArr, true)) {
                            $hasReturn[] = 'rguide';
                        }

                        $shipment->has_return = $hasReturn;


                        //CALC PRICE
                        /**visto que o preço vem no excel, então assumimos como valor fixo e cálculo é feito a partir desse preço*/
                        if (@$row['shipping_price']) {
                            $shipment->price_fixed = 1;
                        }

                        $prices = Shipment::calcPrices($shipment);
                        $shipment->fill($prices['fillable']);


                        if ($shipment->exists) {
                            $shipment->save();
                        } else {
                            @$shipment->has_assembly = 0;
                            if (@$shipment->has_assembly == null) {
                                @$shipment->has_assembly = 0;
                            }
                            $shipment->setTrackingCode();
                            $shipment->storeExpenses($prices);
                            $shipment->scheduleNotification();
                        }

                        if ($saveIds) {
                            $printIds[] = $shipment->id;
                        }

                        /**
                         * Atualiza o estado do envio
                         */
                        if (@$row['status_date']) {
                            $dt = @$row['status_date']->format('Y-m-d');

                            if (@$row['status_hour']) {
                                $dt .= ' ' . @$row['status_hour']->format('H:i:s');
                            }

                            ShipmentHistory::insert([
                                'shipment_id'   => $shipment->id,
                                'user_id'       => Auth::user()->id,
                                'status_id'     => ShippingStatus::DELIVERED_ID,
                                'operator_id'   => $shipment->operator_id,
                                'created_at'    => $dt
                            ]);

                            $shipment->update(['status_id' => ShippingStatus::DELIVERED_ID]);
                        } else {

                            $history = ShipmentHistory::firstOrNew([
                                'shipment_id' => $shipment->id,
                                'status_id' => $shipment->status_id
                            ]);

                            $history->shipment_id = $shipment->id;
                            $history->status_id   = $shipment->status_id;
                            $history->operator_id = $shipment->operator_id;
                            $history->save();

                            //SUBMIT BY WEBSERVICE
                            if ($autoSubmit) {
                                try {
                                    $debug = $request->get('debug', false);
                                    $webservice = new Base($debug);
                                    $webservice->submitShipment($shipment);
                                } catch (\Exception $e) {
                                    //dd($e->getMessage());
                                }
                            }
                        }
                    }
                } //fim do if(!empty($row))

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $operators = User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->pluck('name', 'id')
                ->toArray();

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $services = $services->pluck('name', 'id')->toArray();

            $customers = Customer::whereIn('id', $customersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewMode',
                'agencyId',
                'filepath',
                'operators'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function shipments_weights(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $str = file_get_contents($filepath);
        $enc = mb_detect_encoding($str, mb_list_encodings(), true);


        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.shipments_weights') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $hasErrors      = 0;
        $agencies       = Agency::get();
        $services       = Service::filterSource()->get();
        $shipmentStatus = ShippingStatus::pluck('name', 'id')->toArray();
        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$customersArr, &$hasErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$customersArr, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $originalCostSubtotal = null;
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    if (empty(@$row['tracking_code']) && empty(@$row['tracking_code']) && empty(@$row['tracking_code'])) {
                        $errors[] = 'Código do Envio ou Código Fornecedor ou Referência são obrigatórios';
                    } else {

                        //codigo para localizar envio e atualizar
                        //localiza o envio
                        $shipment = Shipment::where('is_collection', 0);

                        if (@$row['tracking_code']) {
                            $shipment->where('tracking_code', $row['tracking_code']);
                        } elseif (@$row['provider_tracking_code']) {
                            $shipment->where('provider_tracking_code', $row['provider_tracking_code']);
                        } elseif ($row['reference']) {
                            $shipment->where('reference', $row['reference']);
                        }

                        $shipment = $shipment->first();

                        if (!$shipment) {
                            $errors[] = 'Envio não encontrado';
                        }

                        $originalCostSubtotal = $shipment->cost_billing_subtotal;

                        $shipment->provider_weight      = @$row['provider_weight'] ?  @$row['provider_weight'] : $shipment->weight;
                        $shipment->provider_weight      = number($shipment->provider_weight);
                        $shipment->volumes              = @$row['volumes'] ?  @$row['volumes'] : $shipment->volumes;
                        $shipment->fator_m3             = @$row['fator_m3'] ?  @$row['fator_m3'] : $shipment->fator_m3;
                        $shipment->cost_shipping_price  = @$row['cost_shipping_price'] ?  @$row['cost_shipping_price'] : $shipment->cost_shipping_price;
                        $shipment->cost_shipping_price  = number($shipment->cost_shipping_price);
                        $shipment->cost_expenses_price  = @$row['cost_expenses_price'] ?  @$row['cost_expenses_price'] : $shipment->cost_expenses_price;
                        $shipment->cost_expenses_price  = number($shipment->cost_expenses_price);
                        //$shipment->volumetric_weight  = @$row['volumetric_weight'] ?  @$row['volumetric_weight'] : $shipment->volumetric_weight;
                    }


                    if ($previewMode) {
                        if (!empty($errors)) {
                            $shipment->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $previewRows[] = $shipment->toArray();
                    } else {

                        $hasReturn = [];

                        if ($shipment->exists) {

                            //atualiza pesos
                            if ($shipment->provider_weight > $shipment->weight) {
                                $shipment->weight = $shipment->provider_weight;
                            }

                            //Atualiza preços
                            $prices = Shipment::calcPrices($shipment);
                            $shipment->fill($prices['fillable']);

                            //subscreve preço do envio
                            if (!empty(@$row['cost_shipping_price'])) {
                                $shipment->cost_shipping_price  = @$row['cost_shipping_price'];
                            }

                            //subscreve preço das taxas
                            if (!empty(@$row['cost_expenses_price'])) {
                                $shipment->cost_expenses_price = @$row['cost_expenses_price'];
                            }

                            //atualiza iva
                            if (!empty(@$row['cost_shipping_price']) || !empty(@$row['cost_expenses_price'])) {
                                $vatPercent = $shipment->vat_rate / 100;

                                $shipment->cost_billing_subtotal   = $shipment->cost_shipping_price + $shipment->cost_expenses_price;
                                $shipment->cost_billing_vat        = $shipment->cost_billing_subtotal * $vatPercent;
                                $shipment->cost_billing_total      = $shipment->cost_billing_subtotal + $shipment->cost_billing_vat;
                                $shipment->cost_billing_subtotal   = $shipment->cost_shipping_price + $shipment->cost_expenses_price;
                                $shipment->conferred_original_cost = $originalCostSubtotal;
                                $shipment->provider_conferred      = true;
                            }

                            $shipment->save();
                        }
                    }
                } //fim do if(!empty($row))

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }


        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $operators = User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->pluck('name', 'id')
                ->toArray();

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $services = $services->pluck('name', 'id')->toArray();

            //$customers = Customer::whereIn('id', $customersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewMode',
                'agencyId',
                'filepath',
                'operators'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function shipments_fast(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $importerCollection = new ImporterController();
                $columnMapping = $importerCollection->getColumnMapping();

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.shipments') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        if (empty($model->customer_code)) {
            return Redirect::back()->with('error', 'Não configurou o cliente no modelo de importação. Este campo é obrigatório.');
        }

        $customer = Customer::where('code', $model->customer_code)
            ->whereSource(config('app.source'))
            ->whereNull('customer_id')
            ->first(['id', 'code', 'agency_id', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email']);

        $services = Service::filterSource()->pluck('id', 'display_code')->toArray();


        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, $customer, $services, $model, $directImport) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $operationHash = str_random(10);
            $shipmentsIds  = [];
            $updateData    = [];
            $insertArr     = [];
            $i = 1;

            $reader->each(function ($row) use ($mapAttrs, $customer, $request, $model, &$shipmentsIds, &$updateData, &$insertArr, &$i, $operationHash, $services, $directImport) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['is_collection']       = 1;
                    $row['status_id']           = ShippingStatus::ACCEPTED_ID;
                    $row['date']                = @$row['date'] ? $row['date'] : date('Y-m-d');
                    $row['billing_date']        = $row['date'];
                    $row['agency_id']           = $customer->agency_id;
                    $row['sender_agency_id']    = $customer->agency_id;
                    $row['recipient_agency_id'] = $customer->agency_id;

                    $row['service_id']          = @$row['service_code'] ? @$services[$row['service_code']] : $model->service_id;
                    $row['provider_id']         = $request->provider_id ? $request->provider_id : $model->provider_id;

                    if (!$directImport && empty($row['service_id'])) {
                        $errors[] = 'Serviço com o código ' . $row['service_code'] . ' não existente.';
                    }

                    $row['volumes'] = @$row['volumes'] ? $row['volumes'] : 1;
                    $row['weight']  = @$row['weight'] ? (float) $row['weight'] : 1;

                    $row['customer_id']     = $customer->id;
                    $row['sender_name']     = @$row['sender_name'] ? $row['sender_name'] : $customer->name;
                    $row['sender_address']  = @$row['sender_address'] ? $row['sender_address'] : $customer->address;
                    $row['sender_zip_code'] = @$row['sender_zip_code'] ? $row['sender_zip_code'] :  $customer->zip_code;
                    $row['sender_city']     = @$row['sender_city'] ? $row['sender_city'] : $customer->city;
                    $row['sender_country']  = @$row['sender_country'] ? $row['sender_country'] : $customer->country;
                    $row['sender_phone']    = @$row['sender_phone'] ? $row['sender_phone'] : $customer->phone;
                    $row['created_at']      = date('Y-m-d H:i:s');
                    $row['reference3']      = $operationHash;

                    $insertArr[] = $row;

                    if ($directImport && empty($row['service_id'])) {
                        return Redirect::back()->with('error', 'Importação abortada. Serviço com o código ' . @$row['service_code'] . ' não existente.');
                    }
                }
            });

            $result = Shipment::insert($insertArr);

            if (!$result) {
                return Redirect::back()->with('error', 'A importação falhou');
            }

            $shipments = Shipment::where('reference3', $operationHash)->get(['id', 'agency_id']);
            foreach ($shipments as $shipment) {
                $code = str_pad($shipment->agency_id, 3, "0", STR_PAD_LEFT);
                $code .= str_pad($shipment->id, 9, "0", STR_PAD_LEFT);

                $shipment->tracking_code = $code;
                $shipment->reference3   = null;
                $shipment->save();
            }
        });


        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }


        if ($previewMode && !$directImport) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $services = $services->pluck('name', 'id')->toArray();

            $importType = 'shipments';

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewMode',
                'agencyId',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function shipments_logistic(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        $autoSubmit = $request->get('auto_submit', false);

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $str = file_get_contents($filepath);
        $enc = mb_detect_encoding($str, mb_list_encodings(), true);

        /*  if($enc != 'UTF-8') {
            return Redirect::back()->with('error', 'Codificação do ficheiro incorreta ('.$enc.', quando é esperado UTF-8)');
        }*/

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }


        $headerRow = [];
        foreach (trans('admin/importer.shipments_logistic') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $agencies       = Agency::get();
        $services       = Service::filterSource()->get();
        $shipmentStatus = ShippingStatus::pluck('name', 'id')->toArray();
        $customersIds   = [];
        $customersArr   = [];
        $printIds       = [];
        $hasErrors      = 0;
        $rpackArr       = explode(',', $request->get('rpack'));
        $rguideArr      = explode(',', $request->get('rguide'));
        $rcheckArr      = explode(',', $request->get('rcheck'));
        $providers      = Provider::filterSource()->isCarrier()->pluck('id', 'code')->toArray();
        $previewRows    = [];
        $excelRows      = [];

        //read excel
        $excel = Excel::load($filepath, function ($reader) use (&$excelRows, $mapAttrs, $enc) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $reader->each(function ($row) use (&$excelRows, $mapAttrs) {
                if (!empty($row)) {
                    $excelRows[] = mapArrayKeys(array_values($row->toArray()), $mapAttrs);
                }
            });
        });

        //group results by owner
        $rowsGrouped = array_group_by($excelRows, 'reference');

        //prepare items
        $rows = $itemsSKU = $itemsSN = $itemsLote = $customersCodes = [];
        foreach ($rowsGrouped as $key => $items) {
            $dimensions = [];
            foreach ($items as $item) {
                $item['customer_code'] = @$item['customer_code'] ? $item['customer_code'] : $model->user;

                $itemsSKU[$item['sku']]         = @$item['sku'];
                $customersCodes[]               = @$item['customer_code'];

                if (@$item['serial_no']) {
                    $itemsSN[$item['serial_no']] = @$item['serial_no'];
                }

                if (@$item['lote']) {
                    $itemsLote[$item['lote']] = @$item['lote'];
                }

                $dimensions[] = [
                    'sku'       => @$item['sku'],
                    'qty'       => @$item['qty'],
                    'weight'    => @$item['item_weight'],
                    'lote'      => @$item['lote'],
                    'serial_no' => @$item['serial_no'],
                ];
            }
            $items[0]['dimensions'] = $dimensions;
            $rows[] = $items[0];
        }

        $itemsSKU  = array_filter($itemsSKU);
        $itemsSN   = array_filter($itemsSN);
        $itemsLote = array_filter($itemsLote);


        //Get Customers from DB
        $customersIds = Customer::filterSource()->whereIn('code', $customersCodes)->pluck('id', 'code')->toArray();

        //Get stocks from DB
        $items = Product::where(function ($q) use ($itemsSKU, $itemsSN, $itemsLote) {
            $q->whereIn('sku', $itemsSKU);
            //$q->orWhereIn('serial_no', $itemsSN);
            //$q->orWhereIn('lote', $itemsLote);
        });

        /*if(!empty($customersIds)) {
            $items = $items->whereIn('customer_id', $customersIds);
        }*/

        $allProducts = $items->get([
            'customer_id', 'sku', 'serial_no', 'lote', 'stock_status', 'expiration_date',
            'stock_total', 'width', 'height', 'length', 'weight', 'name', 'id'
        ]);

        $i = 0;
        $totalStocks = [];
        foreach ($rows as $row) {

            if (@$row['reference']) {
                $errors = [];
                $dimensions = [];
                $weight = 0;

                //process dimensions
                foreach ($row['dimensions'] as $dimension) {

                    if (!empty(@$dimension['sku'])) { //SE DEVE PREENCHER COM BASE NA LOGISTICA

                        $products = $allProducts->filter(function ($q) use ($dimension) {
                            if (@$dimension['serial_no']) {
                                return $q->serial_no == strtoupper(@$dimension['serial_no']) && $q->sku == strtoupper(@$dimension['sku']);;
                            } elseif (@$dimension['lote']) {
                                return $q->lote == strtoupper(@$dimension['lote']) && $q->sku == strtoupper(@$dimension['sku']);;
                            } else {
                                return $q->sku == strtoupper(@$dimension['sku']);
                            }
                        });

                        //escolhe só 1 produto caso existam vários
                        $products = $allProducts->filter(function ($q) use ($dimension) {
                            if (@$dimension['serial_no']) {
                                return $q->serial_no == strtoupper(@$dimension['serial_no']) && $q->sku == strtoupper(@$dimension['sku']);;
                            } elseif (@$dimension['lote']) {
                                return $q->lote == strtoupper(@$dimension['lote']) && $q->sku == strtoupper(@$dimension['sku']);;
                            } else {
                                return $q->sku == strtoupper(@$dimension['sku']);
                            }
                        });


                        //escolhe só 1 produto caso existam vários
                        $stkTotal = 0;
                        if ($products->count() == 1) {
                            $product  = $products->first();
                            $stkTotal = $product->stock_total;
                        } else {

                            //escolhe o que tem validade menor
                            $productTmp = $products->filter(function ($q) {
                                return $q->stock_total > 0;
                            })->sortBy('expiration_date')->first();

                            if (empty($product)) { //acontece quando todas as linhas com o mesmo SKU têm todas stock a 0
                                $productTmp = $products->first();
                            }

                            $product  = $productTmp;
                            $stkTotal = $products->sum('stock_total'); //coloca no somatório o stock total global do artigo
                        }

                        $totalStocks[@$product->id] = @$totalStocks[@$product->id] + @$dimension['qty'];

                        if (empty($product)) {
                            $errors[] = 'Artigo ' . $dimension['sku'] . ': Não encontrado em sistema. Verifique SKU, Nº Série ou Lote';
                        }

                        /*elseif($dimension['qty'] > $product->stock_total) {
                            $errors[] = 'Artigo '.$dimension['sku'].': Stock indisponível. Máximo: ' .$product->stock_total;
                        }*/ elseif ($product->stock_status == 'blocked') {
                            $errors[] = 'Artigo ' . $dimension['sku'] . ': Artigo bloqueado';
                        } elseif (!empty($product->serial_no) && $dimension['qty'] > 1) {
                            $errors[] = 'Artigo ' . $dimension['sku'] . ': Só é possível encomendar 1 unidade do artigo.';
                        } elseif ($dimension['qty'] > $stkTotal) {
                            $errors[] = 'Artigo ' . $dimension['sku'] . ': Stock disponível insuficiente (Máximo disponível: ' . $stkTotal . ')';
                        } elseif ($dimension['qty'] > $stkTotal) {
                            $errors[] = 'Artigo ' . $dimension['sku'] . ': Stock disponível insuficiente (Máximo disponível: ' . $stkTotal . ')';
                        }

                        if (@$product->id && @$totalStocks[@$product->id] > $stkTotal && $stkTotal > 0) {
                            $errors[] = 'Artigo ' . $dimension['sku'] . ': Sem stock disponível (stock totalmente alocado).';
                        }

                        $dimension['stock_total'] = @$product->stock_total;
                        $dimension['sku']         = @$product->sku;
                        $dimension['serial_no']   = @$product->serial_no;
                        $dimension['lote']        = @$product->lote;
                        $dimension['weight']      = @$product->weight;
                        $dimension['width']       = @$product->width;
                        $dimension['length']      = @$product->length;
                        $dimension['height']      = @$product->height;
                        $dimension['product_id']  = @$product->id;
                        $dimension['name']        = @$product->name;
                    }
                    $dimensions[] = $dimension;

                    $weight += @$product->weight ? @$product->weight : 1;
                }

                $row['volumes']    = @$row['volumes'] ? $row['volumes'] : count($dimensions);
                $row['weight']     = @$row['weight'] ? @$row['weight'] : $weight;
                $row['dimensions'] = $dimensions;

                $row['rpack']  = empty($row['rpack']) ? 0 : 1;
                $row['rcheck'] = empty($row['rcheck']) ? 0 : 1;
                $row['rguide'] = empty($row['rguide']) ? 0 : 1;

                $row['provider_id'] = @$row['provider_id'] ? @$providers[@$row['provider_id']] : ($model->provider_id ? $model->provider_id : @$request->provider_id);
                if (empty($row['provider_id'])) {
                    $errors[] = 'Fornecedor não encontrado.';
                }

                $row['customer_code'] = $model->customer_code ?  $model->customer_code : @$row['customer_code'];
                $input['service_id']  = $model->servide_id;

                $row['sender_address']      = @$row['sender_address'] . ' ' . @$row['sender_address_2'];
                $row['recipient_address']   = @$row['recipient_address'] . ' ' . @$row['recipient_address_2'];
                $row['sender_address']      = trim($row['sender_address']);
                $row['recipient_address']   = trim($row['recipient_address']);

                if (@$row['charge_price']) {
                    $row['charge_price'] = str_replace('no', '', $row['charge_price']);
                    $row['charge_price'] = str_replace('€', '', $row['charge_price']);
                    $row['charge_price'] = str_replace('eur', '', $row['charge_price']);
                }

                if (@$row['total_price']) {
                    $row['total_price'] = number(forceDecimal($row['total_price']));
                }

                if (empty(@$row['recipient_name']) || empty(@$row['recipient_address']) || empty(@$row['recipient_zip_code'])) {
                    $errors[] = 'Há dados do destinatário obrigatórios em falta (Nome ou Morada ou Código Postal ou País).';
                }


                $row['trk'] = null;
                if (substr(strtolower(@$row['reference']), 0, 3) === "trk") {
                    $row['trk'] = substr(@$row['reference'], 3, 12);
                }

                try {
                    if (!empty($row['date'])) {
                        if ($row['date'] && in_array($model->date_format, ['d.m.Y'])) {
                            $row['date'] = str_replace('.', '-', $row['date']);
                            $row['date'] = new Carbon($row['date']);
                        } elseif ($row['date'] && in_array($model->date_format, ['dmY'])) {
                            $dt = str_pad($row['date'], 8, '0', STR_PAD_LEFT);
                            $row['date'] = substr($dt, 0, 2) . '-' . substr($dt, 2, 2) . '-' . substr($dt, 4);
                        }

                        if (is_string($row['date'])) {
                            $row['date'] = Date::createFromFormat($model->date_format, $row['date']);
                            $row['date'] = $row['date']->format('Y-m-d');
                        }
                    } else {
                        $row['date'] = date('Y-m-d');
                    }
                    
                    if(is_object($row['date'])) {
                        $row['date'] = $row['date']->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao ler formato da data. Verifique se a data no ficheiro corresponde ao formato configurado no modelo. ' . $e->getMessage() . ' - ' . $e->getLine();
                }

                $shipment = null;

                if (isset($row['trk']) && !empty($row['trk'])) {
                    $trk = @$row['trk'];

                    $myAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

                    $shipment = Shipment::where('tracking_code', $trk)
                        ->where(function ($q) use ($myAgencies) {
                            $q->whereIn('agency_id', $myAgencies);
                            $q->orWhereIn('sender_agency_id', $myAgencies);
                            $q->orWhereIn('recipient_agency_id', $myAgencies);
                        })
                        ->first();
                } elseif (isset($row['provider_tracking_code']) && !empty($row['provider_tracking_code'])) {
                    $providerTrk = @$row['provider_tracking_code'];

                    $myAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

                    $shipment = Shipment::where('provider_tracking_code', 'like', '%' . $providerTrk . '%') //procura envio com o trk do fornecedor apenas para envios onde a minha agencia esteja envolvida.
                        ->where(function ($q) use ($myAgencies) {
                            $q->whereIn('agency_id', $myAgencies);
                            $q->orWhereIn('sender_agency_id', $myAgencies);
                            $q->orWhereIn('recipient_agency_id', $myAgencies);
                        })
                        ->first();
                }

                if (!$shipment) {
                    $shipment = new Shipment();
                }

                $isCollection = false;

                /**
                 * SERVICE
                 */
                if (!$shipment->exists) {
                    $code = isset($row['service_code']) && !empty($row['service_code']) ? strtoupper($row['service_code']) : null;
                    if ($model->mapping_method) {
                        if ($isCollection) {
                            $providerServices = config('shipments_import_mapping.' . $model->mapping_method . '-services-collection');
                        } else {
                            $providerServices = config('shipments_import_mapping.' . $model->mapping_method . '-services');
                        }

                        if (isset($providerServices[$code])) {
                            $code = $providerServices[$code];
                        }
                    }

                    if (empty($code) && $model->service_id) { //obtem serviço pelo seu ID
                        $service = $services->filter(function ($item) use ($model) {
                            return $item->id == $model->service_id;
                        })->first();
                    } else { //caso contrário, obtem pelo codigo de serviço

                        $code = empty($code) ? '24H' : $code;

                        $service = $services->filter(function ($item) use ($code) {
                            return $item->code == $code;
                        })->first();
                    }

                    if ($service) {
                        $shipment->service_id    = $service->id;
                        $shipment->is_collection = $service->is_collection;
                    } else {
                        $errors[] = 'Não existe nenhum serviço com o código ' . $code . '.';
                    }
                }

                /**
                 * CUSTOMER
                 */
                if (!$shipment->exists && !empty($row['customer_code'])) {

                    if (@$customersArr[$row['customer_code']]) {
                        $customer = @$customersArr[$row['customer_code']];
                    } else {
                        $customer = Customer::where('code', $row['customer_code'])
                            ->whereSource(config('app.source'))
                            ->whereNull('customer_id')
                            ->first(['id', 'code', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email', 'agency_id']);

                        $customersArr[$row['customer_code']] = $customer;
                    }

                    if ($customer) {
                        $customersIds[] = $customer->id;
                        $row['customer_id']  = $customer->id;
                        $row['requested_by'] = $customer->id;
                        $row['sender_name'] = isset($row['sender_name']) && !empty($row['sender_name']) ? $row['sender_name'] : $customer->name;
                        $row['sender_address'] = isset($row['sender_address']) && !empty($row['sender_address']) ? $row['sender_address'] : $customer->address;
                        $row['sender_zip_code'] = isset($row['sender_zip_code']) && !empty($row['sender_zip_code']) ? $row['sender_zip_code'] : $customer->zip_code;
                        $row['sender_city'] = isset($row['sender_city']) && !empty($row['sender_city']) ? $row['sender_city'] : $customer->city;
                        $row['sender_country'] = isset($row['sender_country']) && !empty($row['sender_country']) ? $row['sender_country'] : $customer->country;
                        $row['sender_phone'] = isset($row['sender_phone']) && !empty($row['sender_phone']) ? $row['sender_phone'] : $customer->phone;
                    } else {
                        $row['customer_id'] = null;
                        $errors[] = 'Não existe nenhum cliente associado';
                    }
                } else {
                    $customersIds[] = $shipment->customer_id;
                }

                /**
                 * DATE
                 */
                $row['date'] = isset($row['date']) && !empty($row['date']) ? $row['date'] : date('Y-m-d');

                try {
                    $shipment->date = new Carbon($row['date']);
                    $shipment->date = $shipment->date->format('Y-m-d');
                } catch (\Exception $e) {
                    $errors[] = 'Data do envio inválida: ' . $e->getMessage();
                }

                if (isset($row['delivery_date']) && !empty($row['delivery_date'])) {
                    try {
                        $shipment->delivery_date = new Carbon($row['delivery_date']);
                    } catch (\Exception $e) {
                        $errors[] = 'Data de entrega inválida: ' . $e->getMessage();
                    }
                }

                /* if(@$row['start_hour']) {
                    $row['start_hour'] = trim(@$row['start_hour']);
                    $row['start_hour'] = new Carbon($row['start_hour']);
                    $row['start_hour'] = $row['start_hour']->format('H:i');
                }

                if(@$row['end_hour']) {
                    $row['end_hour'] = trim(@$row['end_hour']);
                    $row['end_hour'] = new Carbon($row['end_hour']);
                    $row['end_hour'] = $row['end_hour']->format('H:i');
                }*/

                /**
                 * VOLUMES
                 */
                $shipment->volumes = isset($row['volumes']) && !empty($row['volumes']) ? (int)$row['volumes'] : 1;

                /**
                 * WEIGHT
                 */
                $shipment->weight = $this->getWeight($shipment, @$row['weight'], @$row['original_weight'], @$row['volumetric_weight'], @$row['fator_m3']);
                if (empty($shipment->weight) || $shipment->weight == 0.00) {
                    $errors[] = 'Envio sem peso.';
                }

                /**
                 * CHARGE PRICE
                 */
                $shipment->charge_price = $shipment->exists ? $shipment->charge_price : @$row['charge_price'];
                $shipment->total_price_for_recipient = $shipment->exists ? $shipment->total_price_for_recipient : @$row['total_price_for_recipient'];
                $shipment->payment_at_recipient = !empty($shipment->total_price_for_recipient) ? true : false;

                /**
                 * STATUS
                 */
                if (!$shipment->exists) {

                    $statusId = isset($row['status_code']) && !empty($row['status_code']) ? $row['status_code'] : Setting::get('importer_default_status', ShippingStatus::ACCEPTED_ID);

                    if ($model->mapping_method) {
                        $providerStatus = config('shipments_import_mapping.' . $model->mapping_method . '-status');

                        if (isset($providerStatus[$statusId])) {
                            $statusId = $providerStatus[$statusId];
                        } else {
                            $errors[] = 'O estado da ' . $model->mapping_method . ' com o código ' . @$row['status_code'] . ' não tem correspondencia com os estados da plataforma.';
                        }
                    }

                    if (isset($shipmentStatus[$statusId])) {
                        $shipment->status_id = $statusId;
                    } else {
                        $errors[] = 'Não existe nenhum estado com o código ' . $code . '.';
                    }
                }

                /**
                 * SENDER COUNTRY
                 */
                $shipment->mapping_method = $model->mapping_method;

                $zipCode = $this->getZipCode($shipment, @$row['sender_country'], @$row['sender_zip_code']);
                $shipment->sender_zip_code = $zipCode['zip_code'];
                $shipment->sender_country  = $zipCode['country'];

                $zipCode = $this->getZipCode($shipment, @$row['recipient_country'], @$row['recipient_zip_code']);
                $shipment->recipient_zip_code = $zipCode['zip_code'];
                $shipment->recipient_country  = $zipCode['country'];

                /**
                 * OBS
                 */
                $row['obs'] = @$row['obs'] ? substr(@$row['obs'], 0, 150) : '';

                /**
                 * AGENCY
                 */
                if (!$previewMode) {
                    $shipment->agency_id = @$customer->agency_id;
                    $shipment->recipient_agency_id = @$customer->agency_id;
                    $agencies = $this->getAgencies($shipment, $agencies, (@$model->provider_id ? @$model->provider_id : @$request->provider_id), @$shipment->agency_id, @$shipment->recipient_agency_id);
                    $shipment->fill($agencies);
                }

                /**
                 * CALC PRICE
                 */
                if (!$previewMode) {
                    $prices = Shipment::calcPrices($shipment);
                    $shipment->fill($prices['fillable']);
                }

                /**
                 * Atualiza os campos do envio
                 */
                unset($shipment->mapping_method);
                unset(
                    $row['volumes'],
                    $row['weight'],
                    $row['charge_price'],
                    $row['sender_country'],
                    $row['sender_zip_code'],
                    $row['recipient_country'],
                    $row['recipient_zip_code'],
                    $row['agency_id'],
                    $row['recipient_agency_id']
                );

                $shipment->fill($row);
                $shipment->sender_phone    = nospace($shipment->sender_phone);
                $shipment->recipient_phone = nospace($shipment->recipient_phone);

                $saveIds = (!$shipment->exists && $request->print_labels) ? true : false;

                if (empty($shipment->customer_id)) {
                    $errors[] = 'Este envio não está associado a nenhum cliente.';
                }

                if ($previewMode) {
                    if (!empty($errors)) {
                        $shipment->errors = $errors;
                        $hasErrors = $hasErrors + 1;
                    }

                    $shipment->dimensions = $row['dimensions'];

                    $shipment->rpack  = $row['rpack'];
                    $shipment->rguide = $row['rguide'];
                    $shipment->rcheck = $row['rcheck'];

                    $previewRows[] = $shipment->toArray();
                } else {

                    $hasReturn = [];

                    if (in_array((string) $i, $rpackArr, true)) {
                        $hasReturn[] = 'rpack';
                    }

                    if (in_array((string) $i, $rcheckArr, true)) {
                        $hasReturn[] = 'rcheck';
                    }

                    if (in_array((string) $i, $rguideArr, true)) {
                        $hasReturn[] = 'rguide';
                    }

                    $shipment->has_return = $hasReturn;
                    $shipment->has_assembly = 0;

                    if ($shipment->exists) {
                        $shipment->save();
                        
                    } else {
                        $dimensions = $shipment->dimensions;
                        unset($shipment->dimensions);
                        $shipment->setTrackingCode();

                        // Store adicional services
                        if ($prices) {
                            $shipment->storeExpenses($prices);
                        }
                        $shipment->scheduleNotification();
                    }

                    //save dimensions
                    foreach ($dimensions as $dimension) {
                        $pack = new ShipmentPackDimension();
                        $pack->shipment_id = $shipment->id;
                        $pack->qty         = @$dimension['qty'] ? @$dimension['qty'] : 1;
                        $pack->weight      = @$dimension['weight'] ? @$dimension['weight'] : 1;
                        $pack->width       = @$dimension['width'];
                        $pack->height      = @$dimension['height'];
                        $pack->length      = @$dimension['length'];
                        $pack->description = @$dimension['name'];

                        $pack->product_id   = @$dimension['product_id'];
                        $pack->sku          = @$dimension['sku'];
                        $pack->serial_no    = @$dimension['serial_no'];
                        $pack->lote         = @$dimension['lote'];
                        $pack->validity     = @$dimension['validity'];

                        $pack->save();
                    }
                    
                    
                    if ($saveIds) {
                        $printIds[] = $shipment->id;
                    }

                    /**
                     * Atualiza o estado do envio
                     */
                    $history = ShipmentHistory::firstOrNew([
                        'shipment_id' => $shipment->id,
                        'status_id' => $shipment->status_id
                    ]);

                    $history->shipment_id = $shipment->id;
                    $history->status_id = $shipment->status_id;
                    $history->save();


                    //SUBMIT BY WEBSERVICE
                    if ($autoSubmit) {
                        try {
                            $debug = $request->get('debug', false);
                            $webservice = new Base($debug);
                            $webservice->submitShipment($shipment);
                        } catch (\Exception $e) {
                        }
                    }

                    //STORE SHIPPING ORDER
                    $logisticError = false;
                    if (hasModule('logistic')) {
                        $shipment->customer = $customer;
                        unset($shipment->pack_dimensions);
                        $result = $shipment->storeShippingOrder();
                        if (!$result['result']) {
                            $logisticError = true;
                        }
                    }
                }
            }
        }

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $services = $services->pluck('name', 'id')->toArray();

            $customers = Customer::whereIn('id', $customersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewMode',
                'agencyId',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {

            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                if ($logisticError) {
                    return Redirect::route('admin.importer.index')->with('error', 'Não foi possível comunicar alguma expedição.');
                }
                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Import customers file
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return \App\Http\Controllers\Admin\type|\App\Models\type
     */
    public function customers(Request $request, $model, $filepath, $previewMode)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.customers') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $agencies      = Agency::get();
        $customerTypes = CustomerType::pluck('id', 'name')->toArray();
        $customersIds  = [];
        $printIds      = [];
        $hasErrors     = 0;

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, &$printIds, $agencies, &$customerTypes, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $agencies, &$customerTypes, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['agency_id']   = $model->agency_id ? $model->agency_id : @$row['customer_agency_id'];
                    $row['type_id']     = $model->type_id   ? $model->type_id : @$row['type_id'];

                    if (empty($row['code'])) {
                        $row['code'] = sprintf('%03d', $i + 1);
                    } else {
                        $row['code']     = strtoupper(trim(@$row['code']));
                        $row['code']     = str_replace("'", "", $row['code']);
                    }

                    $row['name']     = trim(@$row['name']);
                    $row['address']  = trim(@$row['address']);
                    $row['zip_code'] = trim(@$row['zip_code']);
                    $row['city']     = trim(@$row['city']);
                    $row['country']  = strtolower(trim(@$row['country']));
                    $row['vat']      = trim(str_replace(' ', '', @$row['vat']));
                    $row['payment_method'] = @$row['payment_method'] ? trim($row['payment_method']) : '30d';
                    $row['default_invoice_type'] = 'invoice';
                    $row['category'] = trim($row['category'] ?? '');
                    $row['default_payment_method'] = trim(@$row['default_payment_method']);
                    $row['billing_country'] = strtolower(@$row['billing_country']);

                    if (!empty($row['billing_name'])) {
                        $row['has_billing_info'] = true;
                    }

                    try {
                        // $customer = new Customer();
                        $customer = Customer::firstOrNew([
                            'code' => $row['code'],
                            'name' => $row['name'],
                            'vat'  => $row['vat'],
                        ]);

                        $customer->fill($row);
                        $customer->source = config('app.source');
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    $paymentMethod = PaymentMethod::filterSource()
                        ->where('code', $row['default_payment_method'])
                        ->orWhere('name', $row['default_payment_method'])
                        ->first();

                    $customer->default_payment_method = @$paymentMethod->name;
                    $customer->default_payment_method_id = @$paymentMethod->id;

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $customer->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $customer->category = $row['category'];

                        $previewRows[] = $customer->toArray();
                    } else {
                        unset($customer->default_payment_method);

                        if (empty($row['type_id']) && !empty($row['category'])) {
                            if (empty($customerTypes[$row['category']])) {
                                $newCustomerType = CustomerType::create([
                                    'source'    => config('app.source'),
                                    'name'      => $row['category']
                                ]);

                                $customerTypes[$row['category']] = $newCustomerType->id;
                            }

                            $customer->type_id = $customerTypes[$row['category']];
                        }

                        $customer->save();
                    }
                } //fim do if(!empty($row))

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $customers = Customer::whereIn('id', $customersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'previewMode',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Import providers file
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return \App\Http\Controllers\Admin\type|\App\Models\type
     */
    public function providers(Request $request, $model, $filepath, $previewMode)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.providers') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $isCarrierArr  = explode(',', $request->get('is_carrier'));
        $agencies      = Agency::get();
        $providersIds  = [];
        $printIds      = [];
        $hasErrors     = 0;

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, &$printIds, $agencies, $model, $previewMode, &$previewRows, &$headerRow, &$providersIds, &$hasErrors, $isCarrierArr) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $agencies, $model, $previewMode, &$previewRows, &$headerRow, &$providersIds, &$hasErrors, &$i, $isCarrierArr) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['agencies'] = $model->agencies;
                    $row['code']     = strtoupper(trim(@$row['code']));
                    $row['code']     = str_replace("'", "", $row['code']);
                    $row['name']     = str_limit(trim(@$row['name']), 15, '');
                    $row['company']  = trim(@$row['company']);
                    $row['address']  = trim(@$row['address']);
                    $row['zip_code'] = trim(@$row['zip_code']);
                    $row['city']     = trim(@$row['city']);
                    $row['country']  = strtolower(trim(@$row['country']));
                    $row['vat']      = trim(str_replace(' ', '', @$row['vat']));
                    $row['type']     = 'others';

                    if (in_array((string) $i, $isCarrierArr, true)) {
                        $row['type'] = 'carrier';
                    }

                    try {
                        $provider = new Provider();
                        $provider->fill($row);
                        $provider->source = config('app.source');
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $provider->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $previewRows[] = $provider->toArray();
                    } else {
                        $provider->save();
                    }
                } //fim do if(!empty($row))

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $providers = Provider::whereIn('id', $providersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'previewMode',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Import operators file
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return \App\Http\Controllers\Admin\type|\App\Models\type
     */
    public function operators(Request $request, $model, $filepath, $previewMode)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.operators') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $operatorsIds  = [];
        $printIds      = [];
        $hasErrors     = 0;

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, &$printIds, $model, $previewMode, &$previewRows, &$headerRow, &$operatorsIds, &$hasErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $model, $previewMode, &$previewRows, &$headerRow, &$operatorsIds, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['agency_id'] = $request->operators_agency_id;
                    $row['code']      = strtoupper(trim(@$row['code']));
                    $row['code']      = str_replace("'", "", $row['code']);
                    $row['name']      = trim(@$row['name']);
                    $row['address']   = trim(@$row['address']);
                    $row['zip_code']  = trim(@$row['zip_code']);
                    $row['city']      = trim(@$row['city']);
                    $row['country']   = strtolower(trim(@$row['country']));
                    $row['fiscal_address']  = trim(@$row['fiscal_address']);
                    $row['fiscal_zip_code'] = trim(@$row['fiscal_zip_code']);
                    $row['fiscal_city']     = trim(@$row['fiscal_city']);
                    $row['fiscal_country']  = strtolower(trim(@$row['fiscal_country']));
                    $row['vat']      = trim(str_replace(' ', '', @$row['vat']));

                    try {
                        $user = new User();
                        $user->fill($row);
                        $user->source = config('app.source');
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $user->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $previewRows[] = $user->toArray();
                    } else {
                        $user->save();
                    }
                }

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $operators = Customer::whereIn('id', $operatorsIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'importType',
                'operators',
                'hasErrors',
                'previewMode',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    public function routes(Request $request, $model, $filepath, $previewMode) {
        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.routes') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $hasErrors = 0;
        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, &$printIds, $previewMode, &$previewRows, &$headerRow, &$hasErrors) {
            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, &$printIds, $previewMode, &$previewRows, &$headerRow, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    if (@$row['type'] == 'R') {
                        $row['type'] = 'pickup';
                    } else if (@$row['type'] == 'E') {
                        $row['type'] = 'delivery';
                    } else {
                        $row['type'] = null;
                    }

                    $route = Route::filterSource()
                        ->firstOrNew([
                            'code' => @$row['code'],
                            'name' => @$row['name']
                        ]);

                    $route->fill($row);
                    $route->color = '#195181';
                    $route->source = config('app.source');

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $route->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        if ($route->type == 'pickup') {
                            $route->type = 'Recolha';
                        } else if ($route->type == 'delivery') {
                            $route->type = 'Entrega';
                        } else {
                            $route->type = 'Recolha + Entrega';
                        }

                        $previewRows[] = $route->toArray();
                    } else {
                        $route->save();
                    }
                }

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            return $this->preview($model, $headerRow, $previewRows, $filepath, $hasErrors);
        } else {
            return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
        }
    }

    /**
     * Update routes
     * 
     * @param \Illuminate\Http\Request $request
     * @param mixed $model
     * @param string $filepath
     * @param boolean $previewMode
     * @return string
     */
    public function update_routes(Request $request, $model, $filepath, $previewMode) {
        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.update_routes') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $hasErrors = 0;
        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, &$printIds, $previewMode, &$previewRows, &$headerRow, &$hasErrors) {
            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, &$printIds, $previewMode, &$previewRows, &$headerRow, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    // try {
                    //     $user = new User();
                    //     $user->fill($row);
                    //     $user->source = config('app.source');
                    // } catch (\Exception $e) {
                    //     $errors[] = $e->getMessage();
                    // }

                    if ($previewMode) {
                        // if (!empty($errors)) {
                        //     $user->errors = $errors;
                        //     $hasErrors = $hasErrors + 1;
                        // }

                        // $previewRows[] = $user->toArray();
                    } else {
                        // $user->save();
                    }
                }

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            return $this->preview($model, $headerRow, $previewRows, $filepath, $hasErrors);
        } else {
            return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
        }
    }

    /**
     * Import providers file
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return \App\Http\Controllers\Admin\type|\App\Models\type
     */
    public function providers_agencies(Request $request, $model, $filepath, $previewMode)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);


        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.providers_agencies') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $agencies      = Agency::get();
        $printIds      = [];
        $hasErrors     = 0;

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, &$printIds, $model, $previewMode, &$previewRows, &$headerRow, &$hasErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $model, $previewMode, &$previewRows, &$headerRow, &$providersIds, &$hasErrors, &$i) {


                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    if (!empty($row['code']) && !empty($row['name'])) {
                        $row['provider'] = $model->provider_slug;
                        $row['code'] = strtoupper(trim(@$row['code']));
                        $row['country'] = strtolower(trim(@$row['country']));

                        if (empty($row['country'])) {
                            if (strlen(@$row['zip_code']) == 5) {
                                $row['country'] = 'es';
                            } else {
                                $row['country'] = Setting::get('app_country');
                            }
                        }

                        $row['is_active'] = 1;
                        if ($row['provider'] == 'envialia') {
                            if (str_contains($row['name'], '******')) {
                                //$row['name'] = str_replace('******', '', $row['name']);
                                $row['is_active'] = 0;
                            }

                            $nmParts = explode('-', $row['name']);
                            $row['name'] = @$nmParts[1];

                            if (str_contains(@$nmParts[0], 'PTF')) {
                                $row['name'] = 'PTF ' . $row['name'];
                                if (in_array($row['code'], ['000027', '000022', '000037'])) {
                                    $row['country'] = Setting::get('app_country');
                                }
                            }

                            if (str_contains(@$nmParts[0], '***')) {
                                $row['name'] = '***' . $row['name'];
                            }
                        }

                        if ($row['provider'] == 'tipsa') {
                            $row['address'] = ucwords(strtolower($row['address']));
                            $row['city']    = ucwords(strtolower($row['city']));
                            $row['company'] = ucwords(strtolower($row['company']));
                            $row['email']   = strtolower($row['email']);
                            $row['responsable'] = ucwords(strtolower($row['responsable']));

                            $row['country'] = 'es';
                            if (substr($row['zip_code'], 0, 1) == '6') {
                                $row['country'] = Setting::get('app_country');
                                $row['zip_code'] = substr($row['zip_code'], 1);
                            }
                        }

                        if ($row['provider'] == 'gls') {
                            $row['name'] = str_replace('PT.GLS.', '', $row['name']);

                            if ($row['email'] == 'portugal@gls-spain.es') {
                                $agencyEmailName = str_replace(' ', '', strtolower($row['name']));
                                $row['email'] = 'apoiocliente.' . $row['code'] . $agencyEmailName . '@gls-portugal.com';
                            }
                        }


                        $row['email_provider'] = $row['email'];

                        try {
                            $provider = ProviderAgency::firstOrNew([
                                'provider' => $model->provider_slug,
                                'code'     => $row['code']
                            ]);

                            if ($provider->exists) {
                                $provider->name = $row['name'];
                                $provider->code = $row['code'];
                                $provider->email_provider = $row['email_provider'];
                            } else {
                                $provider->fill($row);
                            }
                        } catch (\Exception $e) {
                            $errors[] = $e->getMessage();
                        }

                        if ($previewMode) {
                            if (!empty($errors)) {
                                $provider->errors = $errors;
                                $hasErrors = $hasErrors + 1;
                            }

                            $previewRows[] = $provider->toArray();
                        } else {
                            $provider->save();
                        }
                    } //fim do if(!empty($row))

                    $i++;
                }
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            //$services = $services->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewMode',
                'agencyId',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Import customers file
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return \App\Http\Controllers\Admin\type|\App\Models\type
     */
    public function fleet_fuel(Request $request, $model, $filepath, $previewMode)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.fleet_fuel') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $operators      = User::filterSource()->isOperator()->pluck('id', 'code')->toArray();
        $providers      = Provider::categoryGasStation()->pluck('id', 'code')->toArray();
        $vehicles       = Vehicle::filterSource()->pluck('id', 'license_plate')->toArray();
        $hasErrors      = 0;


        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, $model, $operators, $providers, $vehicles, $previewMode, &$previewRows, &$headerRow, &$hasErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $model, $operators, $providers, $vehicles, $previewMode, &$previewRows, &$headerRow, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['license_plate']   = @$row['license_plate'];
                    $row['vehicle_id']      = @$vehicles[@$row['license_plate']];
                    $row['provider_id']     = $model->provider_id ? $model->provider_id : @$providers[@$row['provider_code']];
                    $row['provider_code']   = $row['provider_id'];
                    $row['km']              = number(@$row['km'], 0);
                    $row['adblue']          = (@$row['adblue'] == 1);
                    $row['product']         = $row['adblue'] ? 'adblue' : 'fuel';
                    $row['liters']          = number(@$row['liters']);
                    $row['total']           = number(@$row['total']);
                    if (empty(@$row['price_per_liter'])) {
                        $row['price_per_liter'] = empty(@$row['price_per_liter']) ? number($row['total'] / $row['liters'], 3) : @$row['price_per_liter'];
                    } else {
                        $row['price_per_liter'] = number($row['price_per_liter']);
                    }                    //$row['price_per_liter'] = empty(@$row['total']) ? 0 : number($row['liters'] / $row['total'], 3);

                    try {
                        if (!empty($row['date'])) {
                            if ($row['date'] instanceof Carbon) {
                                $row['date'] = $row['date']->format($model->date_format);
                            }

                            /**
                             * Removido este if porque estava
                             * a colocar a data igual a 20 de maio de 2020.
                             * 
                             * Caso estivesse a fazer algo dêm uma vassourada no Daniel
                             */
                            // if ($row['date'] && is_int($row['date']) && strlen($row['date']) == 5) {
                            //     $row['date'] = date('Y-m-d', (43981 - 25569) * 86400);
                            // } else 

                            if ($row['date'] && in_array($model->date_format, ['d.m.Y'])) {
                                $row['date'] = str_replace('.', '-', $row['date']);
                                $row['date'] = new Carbon($row['date']);
                            } elseif ($row['date'] && in_array($model->date_format, ['dmY'])) {
                                $dt = str_pad($row['date'], 8, '0', STR_PAD_LEFT);
                                $row['date'] = substr($dt, 0, 2) . '-' . substr($dt, 2, 2) . '-' . substr($dt, 4);
                            } elseif ($row['date'] && in_array($model->date_format, ['d/m/Y'])) {
                                $row['date'] = DateTime::createFromFormat('d/m/Y', $row['date']);
                                $row['date'] = $row['date']->format('Y-m-d');
                            }
                        } else {
                            $row['date'] = date('Y-m-d');
                            $errors[] = 'Erro ao ler a data. Data com problemas no ficheiro ou formato incortreto no modelo de importação.';
                        }
                    } catch (\Exception $e) {
                        $errors[] = 'Erro ao ler formato da data. Verifique se a data no ficheiro corresponde ao formato configurado no modelo.';
                    }

                    if (empty(@$row['vehicle_id'])) {
                        $errors[] = 'Viatura ' . @$row['license_plate'] . ' não encontrada em sistema.';
                    }

                    if (empty(@$row['provider_id'])) {
                        $errors[] = 'Fornecedor não encontrado em sistema.';
                    }

                    try {
                        $fuelLog = new FuelLog();
                        $fuelLog->fill($row);
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $fuelLog->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $fuelLogArr = $fuelLog->toArray();
                        /**
                         * Adicionar adblue ao array
                         * para poder mostrar a checkbox corretamente no modo de preview
                         */
                        $fuelLogArr['adblue']   = $fuelLog->is_adblue;
                        $previewRows[]          = $fuelLogArr;
                    } else {
                        /*
                         * Alterar o código do motorista para o id apenas ao gravar
                         * para evitar confusão nos clientes no modo de preview
                         */
                        $fuelLog->operator_id = @$operators[$fuelLog->operator_id];
                        $fuelLog->save();

                        FuelLog::updateVehicleCounters($fuelLog->vehicle_id);
                    }
                } //fim do if(!empty($row))

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $vehicles = Vehicle::filterSource()
                ->pluck('license_plate', 'id')
                ->toArray();

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->categoryGasStation()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $customers = [];
            $importType = $model->type;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'vehicles',
                'importType',
                'customers',
                'hasErrors',
                'previewMode',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }



    /**
     * Import customers file
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return \App\Http\Controllers\Admin\type|\App\Models\type
     */
    public function tolls_logs(Request $request, $model, $filepath, $previewMode)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.tolls_logs') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $providers      = Provider::CategoryTolls()->pluck('id', 'code')->toArray();
        $vehicles       = Vehicle::filterSource()->pluck('id', 'license_plate')->toArray();
        $hasErrors      = 0;

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, $model, $providers, $vehicles, $previewMode, &$previewRows, &$headerRow, &$hasErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $model, $providers, $vehicles, $previewMode, &$previewRows, &$headerRow, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);
                    $row['provider_id']     = $model->provider_id ? $model->provider_id : @$providers[@$row['provider_code']];
                    $row['provider_code']   = $row['provider_id'];
                    $row['vehicle_id']      = @$vehicles[@$row['license_plate']];
                    $row['entry_date']      = @$row['entry_date'];
                    $row['exit_date']       = @$row['exit_date'];
                    $row['entry_point']     = @$row['entry_point'];
                    $row['exit_point']      = @$row['exit_point'];
                    $row['total']           = @$row['total'];
                    $row['payment_date']    = @$row['payment_date'];
                    $row['toll_provider']   = @$row['toll_provider'];

                    if (empty(@$row['vehicle_id'])) {
                        $errors[] = 'Viatura ' . @$row['license_plate'] . ' não encontrada em sistema.';
                    }

                    if (empty(@$row['provider_id'])) {
                        $errors[] = 'Fornecedor não encontrado em sistema.';
                    }

                    try {
                        $tollsLog = new TollLog();
                        $tollsLog->fill($row);
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $tollsLog->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $tollsLogArr = $tollsLog->toArray();

                        $previewRows[]          = $tollsLogArr;
                    } else {
                        $tollsLog->save();
                    }
                } //fim do if(!empty($row))

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $vehicles = Vehicle::filterSource()
                ->pluck('license_plate', 'id')
                ->toArray();

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->CategoryTolls()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $customers = [];
            $importType = $model->type;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'vehicles',
                'importType',
                'customers',
                'hasErrors',
                'previewMode',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }




    /**
     * Import Reception Orders
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return \App\Http\Controllers\Admin\type|\App\Models\type
     */
    public function reception_orders(Request $request, $model, $filepath, $previewMode)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $createdAt = date('Y-m-d H:i:s');

        if (!$request->get('reception_order_customer') && !$model->customer_code) {
            return Redirect::route('admin.importer.index')->with('error', 'Tem de indicar o cliente.');
        } else {

            $customerCode = $model->customer_code ? $model->customer_code : $request->get('reception_order_customer');
            $customerCode = trim($customerCode);

            $customer = Customer::filterSource()
                ->where('code', $customerCode)
                ->isActive()
                ->first();

            if (!$customer) {
                return Redirect::route('admin.importer.index')->with('error', 'O cliente indicado não existe ou está inativo');
            }
        }


        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.reception_orders') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $hasErrors = 0;
        $providersIds  = [];
        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, $model, $previewMode, &$previewRows, &$headerRow, &$hasErrors, &$providersIds, $customer) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, $model, $previewMode, &$previewRows, &$headerRow, &$hasErrors, &$i, &$providersIds, $customer) {

                if (!empty($row)) {

                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);
                    $errors = [];

                    $row['sku']       = trim(@$row['sku']);
                    $row['lote']      = trim(@$row['lote']);
                    $row['serial_no'] = trim(@$row['serial_no']);
                    $row['qty']       = trim(@$row['qty']);


                    //verifica se os produtos existem
                    $product = Product::where('customer_id', $customer->id)
                        ->where('sku', $row['sku']);

                    if ($row['lote']) {
                        $product->where('lote', $row['lote']);
                    }

                    if ($row['serial_no']) {
                        $product->where('serial_no', $row['serial_no']);
                    }

                    $product = $product->first();

                    if (empty($product)) {
                        if ($row['lote']) {
                            $errors[] = 'Artigo com o SKU ' . $row['sku'] . ' e lote ' . $row['lote'] . ' inexistente para o cliente.';
                        } else if ($row['serial_no']) {
                            $errors[] = 'Artigo com o SKU ' . $row['sku'] . ' e N.º Série ' . $row['serial_no'] . ' inexistente para o cliente.';
                        } else {
                            $errors[] = 'Artigo com o SKU ' . $row['sku'] . ' inexistente para o cliente.';
                        }

                        $product = new Product();
                        $product->fill($row);
                    }

                    if ($row['lote']) {
                        $product->lote = $row['lote'];
                    } else {
                        $product->lote = null;
                    }

                    $product->qty = $row['qty'];
                    if ($product->qty <= 0) {
                        $errors[] = 'Stock a receber tem de ser maior ou igual que zero.';
                    }

                    $product->customer = $customer->name;

                    if ($previewMode) {

                        if (!empty($errors)) {
                            $product->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $previewRows[] = $product->toArray();
                    } else {
                        $previewRows[] = $product->toArray();
                    }
                } //fim do if(!empty($row))

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $providers = Provider::whereIn('id', $providersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'providers',
                'hasErrors',
                'previewMode',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {

            try {
                //cria ordem de saida
                $receptionOrder = new ReceptionOrder();
                $receptionOrder->source         = config('app.source');
                $receptionOrder->customer_id    = $customer->id;
                $receptionOrder->requested_date = $request->get('reception_order_date');
                $receptionOrder->document       = $request->get('reception_order_doc');
                $receptionOrder->status_id      = 1; //ReceptionOrderStatus::STATUS_REQUESTED;
                $receptionOrder->setCode();

                //insere linhas
                foreach ($previewRows as $row) {

                    $insertArr = [
                        'reception_order_id' => $receptionOrder->id,
                        'product_id'         => $row['id'],
                        'qty'                => $row['qty'],
                        // 'lote'               => $row['lote'],
                        'created_at'         => $createdAt
                    ];

                    ReceptionOrderLine::insert($insertArr);
                }

                $receptionOrder->updateReceptionOrderProducts();

                if (config('app.source') == 'activos24') {
                    $receptionOrder->storeOnS3Document();
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

            return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
        }
    }

    public function getWeight($shipment, $weight, $originalWeight, $volumetricWeight, $fatorM3)
    {

        $weight             = empty($weight) ? 1 : $weight;
        $weight             = (float) $weight;
        $originalWeight     = (float) $originalWeight;
        $volumetricWeight   = (float) $volumetricWeight;
        $fatorM3            = (float) $fatorM3;

        if (empty($weight)) {
            $weight = 0;
        } else {
            $weight = str_replace(',', '.', $weight);
        }


        if ($fatorM3) {
            $volumetricWeight = $fatorM3 * 167;
        }

        if ($shipment->exists) {
            $weight = $weight > $shipment->weight ? $weight : $shipment->weight;
        }

        $weight = $originalWeight > $weight ? $originalWeight : $weight;

        $weight = $volumetricWeight > $weight ? $volumetricWeight : $weight;

        return (float) $weight;
    }


    /**
     * Obtém o país e o código postal em formato de 4 digítos
     * @param $shipment
     * @param $country
     * @param $zipCode
     */
    public function getZipCode($shipment, $country, $zipCode)
    {

        $fullZipCode = $zipCode;
        if (!$shipment->exists) {
            if (empty($zipCode) && empty($country)) {
                $country = Setting::get('app_country');
            } else {

                $country = strtolower($country);

                $fullZipCode = str_replace(' ', '', $zipCode);

                if ($zipCode) {
                    $zipCode = explode('-', $zipCode);
                    $zipCode = trim(@$zipCode[0]);
                }

                if ($shipment->mapping_method == 'tipsa' && strlen($zipCode) == 5 && $zipCode[0] == '6') {
                    $zipCode = substr($zipCode, 1);
                }

                if (empty($country)) {
                    $country = strlen($zipCode) == 4 ? 'pt' : 'es';
                }
            }
        }

        return [
            'zip_code' => $fullZipCode,
            'country'  => $country,
        ];
    }

    /**
     * Obtem agencias e fornecedor
     *
     * @param $shipment
     * @param $agencies
     * @param $providerId
     * @param $agencyId
     * @param $recipientAgencyId
     * @return array
     */
    public function getAgencies($shipment, $agencies, $providerId, $agencyId, $recipientAgencyId)
    {


        if (!$shipment->exists) {
            if (empty($recipientAgencyId)) { //detencao automatica
                $agency = Shipment::getAgencyByZipCode($shipment->recipient_zip_code, $providerId);
                $detectedAgencyId   = @$agency['agency_id'];
                $detectedProviderId = @$agency['provider_id'];

                if (empty($detectedAgencyId)) { //codigo postal não encontrado. Agencia destino igual à agencia de origem. Envio via Rangel
                    $recipientAgencyId = $agencyId;
                } else {
                    $recipientAgencyId = $detectedAgencyId;
                }

                if (!empty($detectedProviderId)) {
                    $providerId = $detectedProviderId;
                }
            }
        } else {
            $agencyId   = $shipment->agency_id;
            $recipientAgencyId = $shipment->recipient_agency_id;
            $providerId = $shipment->provider_id;
        }

        $result = [
            'agency_id'             => $agencyId,
            'sender_agency_id'      => $agencyId,
            'recipient_agency_id'   => $recipientAgencyId,
            'provider_id'           => $providerId ? $providerId : Setting::get('shipment_default_provider')
        ];

        if (config('app.source') == 'volumedourado' && $providerId == '112') {
            //força envios pela envialia da volumedourado a ficarem com serviço 24H
            $result['service_id'] = '179';
        }

        return $result;
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function logistic_products(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $importerCollection = new ImporterController();
                $columnMapping = $importerCollection->getColumnMapping();

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.logistic_products') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        if (empty($model->customer_code)) {
            return Redirect::back()->with('error', 'Não configurou o cliente no modelo de importação. Este campo é obrigatório.');
        }

        $customer = Customer::where('code', $model->customer_code)
            ->whereSource(config('app.source'))
            ->whereNull('customer_id')
            ->first(['id', 'code', 'agency_id', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email']);

        $hasErrors = false;
        $result = true;
        $previewRows   = [];
        $excel = Excel::load($filepath, function ($reader) use (&$hasErrors, $mapAttrs, $request, $customer, $model, &$previewRows, &$previewMode, $directImport) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }


            $shipmentsIds  = [];

            $updateData    = [];
            $insertArr     = [];
            $i = 1;

            $reader->each(function ($row) use ($mapAttrs, $customer, $request, $model, &$shipmentsIds, &$updateData, &$previewRows, &$previewMode, &$insertArr, &$i, $directImport) {

                if (!empty($row)) {

                    //$errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    if (!empty($row['name'])) {
                        $row['source']      = config('app.source');
                        $row['customer_id'] = $customer->id;
                        $row['price']       = @$row['price'] ? (float)trim(str_replace('€', '', @$row['price'])) : null;
                        $row['created_at']  = date('Y-m-d H:i:s');

                        if ($previewMode) {
                            $previewRows[] = $row;
                        } else {
                            // Only call this function on insert mode or else it will break preview mode
                            $this->importLogisticProductsChangeNamesToIds($row);
                            $insertArr[] = $row;
                        }
                    }
                }
            });

            $result = true;
            if (!empty($insertArr) && !$previewMode) {
                $result = Product::insert($insertArr);
            }

            if (!$result) {
                return Redirect::back()->with('error', 'A importação falhou');
            }
            /*
                        $shipments = Shipment::where('reference3', $operationHash)->get(['id', 'agency_id']);
                        foreach ($shipments as $shipment) {
                            $code = str_pad($shipment->agency_id, 3, "0", STR_PAD_LEFT);
                            $code .= str_pad($shipment->id, 9, "0", STR_PAD_LEFT);

                            $shipment->tracking_code = $code;
                            $shipment->reference3   = null;
                            $shipment->save();
                        }*/
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewRows && !$directImport) {

            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $services = Service::filterSource()->pluck('id', 'display_code')->toArray();

            $importType = 'logistic_products';

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewRows',
                'agencyId',
                'filepath',
                'hasErrors'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function logistic_stocks(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $importerCollection = new ImporterController();
                $columnMapping = $importerCollection->getColumnMapping();

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.logistic_stocks') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        /*if(empty($model->customer_code)) {
            return Redirect::back()->with('error', 'Não configurou o cliente no modelo de importação. Este campo é obrigatório.');
        }*/

        $customer = Customer::where('code', $model->customer_code)
            ->whereSource(config('app.source'))
            ->whereNull('customer_id')
            ->first(['id', 'code', 'agency_id', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email']);

        $hasErrors = false;
        $result = true;
        $previewRows   = [];
        $excel = Excel::load($filepath, function ($reader) use (&$hasErrors, $mapAttrs, $request, $customer, $model, &$previewRows, &$previewMode, $directImport) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }


            $shipmentsIds  = [];

            $updateData    = [];
            $insertArr     = [];
            $i = 1;

            $reader->each(function ($row) use ($mapAttrs, $customer, $request, $model, &$shipmentsIds, &$updateData, &$previewRows, &$previewMode, &$insertArr, &$i, $directImport, &$hasErrors) {

                if (!empty($row)) {

                    //$errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $product = Product::where('sku', $row['sku'])->first();

                    if (!$product) {
                        $errors[] = 'Produto com SKU ' . @$row['sku'] . ' não encontrado';
                    }


                    if (!empty($row['sku'])) {
                        $row['source'] = config('app.source');

                        if ($previewMode) {
                            if (!empty($errors)) {
                                $hasErrors += count($errors);
                                $row['errors'] = $errors;
                            }

                            $previewRows[] = $row;
                        } else {
                            $insertArr[] = $row;
                        }
                    }
                }
            });

            $result = true;
            if (!empty($insertArr) && !$previewMode) {

                $result = true;
                try {
                    foreach ($insertArr as $item) {
                        $product  = Product::where('sku', $item['sku'])->first();
                        $location = Location::where('barcode', $item['product_location'])->first();

                        $productLocation = ProductLocation::where('product_id', $product->id)
                            ->where('location_id', $location->id)
                            ->first();

                        if ($productLocation) {
                            $productLocation->stock = $item['stock_available'];
                            $productLocation->save();

                            $product->updateStockTotal();
                        }
                    }
                } catch (\Exception $e) {
                }
            }

            if (!$result) {
                return Redirect::back()->with('error', 'A importação falhou');
            }
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewRows && !$directImport) {

            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $services = Service::filterSource()->pluck('id', 'display_code')->toArray();

            $importType = 'logistic_stocks';

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewRows',
                'agencyId',
                'filepath',
                'hasErrors'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }


    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function equipments(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $importerCollection = new ImporterController();
                $columnMapping = $importerCollection->getColumnMapping();

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.equipments') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }


        $customer = Customer::where('code', $model->customer_code)
            ->whereSource(config('app.source'))
            ->first(['id', 'code', 'agency_id', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email']);

        if (empty($customer)) {
            return Redirect::back()->with('error', 'Não configurou o cliente no modelo de importação ou o cliente com o código ' . $model->customer_code . ' não foi encontrado.');
        }

        $categories = \App\Models\Equipment\Category::filterSource()
            ->pluck('id', 'code')
            ->toArray();

        $locations = \App\Models\Equipment\Location::filterSource()
            ->pluck('id', 'code')
            ->toArray();

        $locationWarehouses = \App\Models\Equipment\Location::filterSource()
            ->pluck('warehouse_id', 'id')
            ->toArray();

        $hasErrors = 0;
        $result = true;
        $previewRows = [];
        $excel = Excel::load($filepath, function ($reader) use (&$hasErrors, $mapAttrs, $request, $customer, $categories, $locations, $locationWarehouses, $model, $previewMode, &$previewRows, &$errors, $directImport) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $insertArr     = [];
            $reader->each(function ($row) use ($mapAttrs, $customer, $request, $model, $categories, $locations, $locationWarehouses, $previewMode, &$previewRows, &$insertArr, &$i, &$errors, &$hasErrors, $directImport) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    if (!empty($row['sku']) || !empty(@$row['serial_no']) || !empty(@$row['lote'])) {
                        $row['source']      = config('app.source');
                        $row['customer_id'] = $customer->id;
                        $row['created_at']  = date('Y-m-d H:i:s');
                        $row['status']      = 'available';


                        if (empty(@$row['sku'])) {
                            $row['sku']  = @$row['serial_no'] ? @$row['serial_no'] : @$row['lote'];
                        }

                        if (empty(@$row['name'])) {
                            $row['name']  = $row['sku'];
                        }

                        if (!empty(@$row['category_id']) && empty(@$categories[$row['category_id']])) {
                            $errors[] = 'Categoria com o código ' . @$row['category_id'] . ' não encontrada';
                        }

                        if (!empty(@$row['location_id']) && empty(@$locations[$row['location_id']])) {
                            $errors[] = 'Localização com o código ' . @$row['location_id'] . ' não encontrada';
                        }

                        if ($previewMode) {
                            if (!empty($errors)) {
                                $hasErrors += count($errors);
                                $row['errors'] = $errors;
                            }

                            $previewRows[] = $row;
                        } else {

                            //quando for para inserir de verdade, substitui os valores pelos ID's correspondentes
                            if (!empty(@$row['category_id'])) {
                                $row['category_id']  = @$categories[$row['category_id']];
                            }

                            if (!empty(@$row['location_id'])) {
                                $row['location_id']  = @$locations[$row['location_id']];
                                $row['warehouse_id'] = @$locationWarehouses[$row['location_id']];
                            }

                            $row['customer_id'] = $customer->id;

                            if (empty($errors)) {
                                $insertArr[] = $row;
                            }
                        }
                    }
                }
            });


            $result = true;
            if (!empty($insertArr) && !$previewMode) {
                $result = \App\Models\Equipment\Equipment::insert($insertArr);
            }

            if (!$result) {
                return Redirect::back()->with('error', 'A importação falhou');
            }
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewRows && !$directImport) {

            $customers = [$customer->id => $customer->name];

            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $importType = 'equipments';

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'importType',
                'previewRows',
                'agencyId',
                'filepath',
                'hasErrors',
                'customers'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }


    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function prices_table(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $importerCollection = new ImporterController();
                $columnMapping = $importerCollection->getColumnMapping();

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.prices_table') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }


        $services = Service::pluck('id', 'display_code')->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $zones    = BillingZone::pluck('code', 'code')->toArray();

        $hasErrors = 0;
        $result = true;
        $previewRows = [];
        $excel = Excel::load($filepath, function ($reader) use (&$hasErrors, $mapAttrs, $request, $model, $previewMode, &$previewRows, &$errors, $directImport, $services, $zones, $pricesTables) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $insertArr = [];
            $servicesArr = [];
            $minValue  = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, $model, $previewMode, &$previewRows, &$insertArr, &$i, &$errors, &$hasErrors, $directImport, &$minValue, $services, $zones, $pricesTables, &$servicesArr) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);


                    if (!empty($row['service_id']) && !empty(@$row['zone']) && !empty(@$row['max'])) {

                        $row['price_table_id'] = $request->prices_table_id;
                        if (empty($row['price_table_id'])) {
                            $errors[] = 'Tabela de preços não especificada';
                        }

                        $serviceCode = $row['service_id'];
                        $row['service_id'] = @$services[$serviceCode];
                        if (empty(@$row['service_id'])) {
                            $errors[] = 'Serviço com o código ' . $serviceCode . ' não encontrado';
                        } else {
                            $servicesArr[] = $row['service_id'];
                        }

                        $row['zone'] = strtolower(trim($row['zone']));
                        $row['zone'] = @$zones[$row['zone']];

                        if (empty(@$row['zone'])) {
                            $errors[] = 'Não existe nenhuma zona de faturação com o código: ' . @$row['zone'];
                        }


                        $row['is_adicional'] = @$row['is_adicional'] ? 1 : 0;
                        $row['adicional_unity'] = 1;

                        $row['min'] = $minValue;
                        $row['max'] = (float) $row['max'];

                        //atualiza o proximo minimo valor
                        if ($row['max'] <= 99999.99) {
                            $minValue = ((float)$row['max']) + 0.01;
                        } else {
                            $minValue = 0.00;
                        }


                        if ($previewMode) {
                            if (!empty($errors)) {
                                $hasErrors += count($errors);
                                $row['errors'] = $errors;
                            }

                            $previewRows[] = $row;
                        } else {

                            if (empty($errors)) {
                                $insertArr[] = $row;
                            }
                        }
                    }
                }
            });

            $result = true;
            if (!empty($insertArr) && !$previewMode) {
                //apaga da tabela de preços os preços antigos
                CustomerService::where('price_table_id', $request->prices_table_id)
                    ->whereIn('service_id', $servicesArr)
                    ->forceDelete();

                //insere os preços
                $result = CustomerService::insert($insertArr);
            }

            if (!$result) {
                return Redirect::back()->with('error', 'A importação falhou');
            }
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewRows && !$directImport) {

            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $services = Service::remember(config('cache.query_ttl'))
                ->cacheTags(Service::CACHE_TAG)
                ->filterSource()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();


            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $importType = 'prices_table';

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'importType',
                'previewRows',
                'agencyId',
                'filepath',
                'hasErrors',
                'services',
                'pricesTables'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function shipments_dimensions(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {
        $autoSubmit = $request->get('auto_submit', false);

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $str = file_get_contents($filepath);
        $enc = mb_detect_encoding($str, mb_list_encodings(), true);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.shipments_dimensions') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $agencies       = Agency::get();
        $services       = Service::filterSource()->get();
        $shipmentStatus = ShippingStatus::pluck('name', 'id')->toArray();
        $customersIds   = [];
        $customersArr   = [];
        $printIds       = [];
        $hasErrors      = 0;
        $allExpenses    = ShippingExpense::filterSource()->get(['id', 'code', 'name', 'type']);
        $rpackArr       = explode(',', $request->get('rpack'));
        $rguideArr      = explode(',', $request->get('rguide'));
        $rcheckArr      = explode(',', $request->get('rcheck'));
        $providers      = Provider::filterSource()->isCarrier()->pluck('id', 'code')->toArray();
        $previewRows    = [];
        $excelRows      = [];

        //read excel
        $excel = Excel::load($filepath, function ($reader) use (&$excelRows, $mapAttrs, $enc) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $reader->each(function ($row) use (&$excelRows, $mapAttrs) {
                if (!empty($row)) {
                    $excelRows[] = mapArrayKeys(array_values($row->toArray()), $mapAttrs);
                }
            });
        });
        //group results by owner
        $rowsGrouped = array_group_by($excelRows, 'reference');

        //prepare items
        $rows = $customersCodes = [];
        foreach ($rowsGrouped as $key => $items) {
            $dimensions = [];
            foreach ($items as $item) {

                $item['customer_code'] = @$item['customer_code'] ? $item['customer_code'] : $model->customer_code;
                $customersCodes[] = @$item['customer_code'];

                $dimensions[] = [
                    'sku'       => @$item['sku'],
                    'qty'       => @$item['qty'],
                    'assembly' => @$item['assembly'],
                    'weight'  => @$item['article_weight'],
                    'article_name' => @$item['article_name'],
                    'article_height' =>  @$item['article_height'],
                    'article_width'  =>  @$item['article_width'],
                    'article_length' => @$item['article_length'],
                    'article_m3'     => @$item['article_m3']
                ];
            }
            $items[0]['dimensions'] = $dimensions;
            $rows[] = $items[0];
        }

        //Get Customers from DB
        $customerId = Customer::filterSource()->whereIn('code', $customersCodes)->pluck('id', 'code')->first();
        foreach ($rows as $row) {
            $shipment = new Shipment();
            if (!empty($customerId)) {
                $customer = Customer::where('id', $customerId)
                    ->whereSource(config('app.source'))
                    ->whereNull('customer_id')
                    ->first(['id', 'code', 'name', 'address', 'zip_code', 'city', 'country', 'phone', 'email', 'agency_id']);
            }
            if ($customer) {
                $row['customer_id'] = $customer->id;
                $row['requested_by'] = $customer->id;
                $row['sender_name'] = isset($row['sender_name']) && !empty($row['sender_name']) ? $row['sender_name'] : $customer->name;
                $row['sender_address'] = isset($row['sender_address']) && !empty($row['sender_address']) ? $row['sender_address'] : $customer->address;
                $row['sender_zip_code'] = isset($row['sender_zip_code']) && !empty($row['sender_zip_code']) ? $row['sender_zip_code'] : $customer->zip_code;
                $row['sender_city'] = isset($row['sender_city']) && !empty($row['sender_city']) ? $row['sender_city'] : $customer->city;
                $row['sender_country'] = isset($row['sender_country']) && !empty($row['sender_country']) ? $row['sender_country'] : $customer->country;
                $row['sender_phone'] = isset($row['sender_phone']) && !empty($row['sender_phone']) ? $row['sender_phone'] : $customer->phone;
            } else {
                $row['customer_id'] = null;
                $errors[] = 'Não existe nenhum cliente associado';
            }

            try {
                $shipment->date = new Carbon($row['date']);
                $shipment->date = $shipment->date->format('Y-m-d');
            } catch (\Exception $e) {
                $errors[] = 'Data do envio inválida: ' . $e->getMessage();
            }

            if (isset($row['delivery_date']) && !empty($row['delivery_date']) && $row['delivery_date'] != "") {
                try {
                    $shipment->delivery_date = new Carbon($row['delivery_date']);
                } catch (\Exception $e) {
                    $errors[] = 'Data de entrega inválida: ' . $e->getMessage();
                }
            } else {
                $shipment->delivery_date = NULL;
            }

            $totalWeight = 0.00;
            $totalVolumes = 0;
            $totalM3      = 0.00;
            foreach ($row['dimensions'] as $item) {
                if ($item['qty'] != null) {
                    $totalVolumes += $item['qty'];
                }
                if ($item['weight'] != null) {
                    if ($item['qty'] != null) {
                        $itemWeight  = $item['qty'] * $item['weight'];
                        $totalWeight += $itemWeight;
                    } else {
                        $totalWeight += $item['weight'];
                    }
                }

                if ($item['article_m3'] != null) {
                    if ($item['qty'] != null) {
                        $itemM3  = $item['qty'] * $item['article_m3'];
                        $totalM3 += $itemM3;
                    } else {
                        $totalM3 += $item['article_m3'];
                    }
                }
            }

            $row['rpack']  = empty($row['rpack']) ? 0 : 1;
            $row['rcheck'] = empty($row['rcheck']) ? 0 : 1;
            $row['rguide'] = empty($row['rguide']) ? 0 : 1;

            //Customer
            $shipment->customer_id = @$customer->id;

            //VOLUME AND WEIGHT AND VOLUME
            $shipment->volumes  = $totalVolumes;
            $shipment->weight = $totalWeight;
            $shipment->volume_m3 = $totalM3;

            if ($shipment->weight == NULL) {
                $shipment->weight = '1';
            }


            //SERVICE
            $shipment->service_id = $model->service_id;
            //PRICES
            $shipment->charge_price = @$row['charge_price'];
            $shipment->total_price_for_recipient = @$row['total_price_for_recipient'];
            $shipment->payment_at_recipient = !empty($shipment->total_price_for_recipient) ? true : false;

            //STATUS
            $shipment->status_id = ShippingStatus::ACCEPTED_ID;

            //IMPORTATION MODEL
            $shipment->mapping_method = $model->type;

            //SENDER COUNTRY
            $zipCode = $this->getZipCode($shipment, @$row['sender_country'], @$row['sender_zip_code']);
            $shipment->sender_zip_code = $zipCode['zip_code'];
            $shipment->sender_country  = $zipCode['country'];

            //RECIPIENT COUNTRY
            $zipCode = $this->getZipCode($shipment, @$row['recipient_country'], @$row['recipient_zip_code']);
            $shipment->recipient_zip_code = $zipCode['zip_code'];
            $shipment->recipient_country  = $zipCode['country'];

            //OBS
            $row['obs'] = @$row['obs'] ? substr(@$row['obs'], 0, 150) : '';

            //AGENCY
            if (!$previewMode) {
                $shipment->agency_id = 106;
                $shipment->recipient_agency_id = 106;

                $senderZipCode = $this->getZipCode($shipment, @$row['sender_country'], @$row['sender_zip_code']);
                $senderZipCode = $senderZipCode['zip_code'];
                $senderZipCode = explode("-", $senderZipCode);

                $recipientZipCode = $this->getZipCode($shipment, @$row['recipient_country'], @$row['recipient_zip_code']);
                $recipientZipCode = $recipientZipCode['zip_code'];
                $recipientZipCode = explode("-", $recipientZipCode);

                $zipCodeSenderAgencyId = AgencyZipCode::where('zip_code', $senderZipCode[0])->first();
                $zipCodeRecipientAgencyId = AgencyZipCode::where('zip_code', $recipientZipCode[0])->first();

                if (!empty($zipCodeSenderAgencyId)) {
                    $shipment->agency_id = $zipCodeSenderAgencyId->agency_id;
                }

                if (!empty($zipCodeRecipientAgencyId)) {
                    $shipment->recipient_agency_id = $zipCodeRecipientAgencyId->agency_id;
                }

                // $shipment->agency_id = @$customer->agency_id;
                // $shipment->recipient_agency_id = @$customer->agency_id;
                $agencies = $this->getAgencies($shipment, $agencies, (@$model->provider_id ? @$model->provider_id : @$request->provider_id), @$shipment->agency_id, @$shipment->recipient_agency_id);
                $shipment->fill($agencies);
            }
            /**
             * CALC PRICE
             */
            if (!$previewMode) {
                $prices = Shipment::calcPrices($shipment);
                if (empty(@$row['total_price'])) {
                    $shipment->total_price = @$prices['total'];
                }
                $shipment->cost_price     = @$prices['cost'];
                $shipment->fuel_tax       = @$prices['fuelTax'];
                $shipment->extra_weight   = @$prices['extraKg'];
            }
            $dimensions = $row['dimensions'];
            /**
             * Atualiza os campos do envio
             */
            unset($shipment->mapping_method);
            unset(
                $row['volumes'],
                $row['weight'],
                $row['date'],
                $row['delivery_date'],
                $row['charge_price'],
                $row['sender_country'],
                $row['sender_zip_code'],
                $row['recipient_country'],
                $row['recipient_zip_code'],
                $row['provider_id'],
                $row['agency_id'],
                $row['recipient_agency_id'],
                $row['dimensions']
            );


            $shipment->fill($row);
            $shipment->sender_phone    = nospace($shipment->sender_phone);
            $shipment->recipient_phone = nospace($shipment->recipient_phone);

            if (empty($shipment->customer_id)) {
                $errors[] = 'Este envio não está associado a nenhum cliente.';
            }

            if ($previewMode) {
                if (!empty($errors)) {
                    $shipment->errors = $errors;
                    $hasErrors = $hasErrors + 1;
                }

                $shipment->dimensions =   $dimensions;
                $previewRows[] = $shipment->toArray();
            } else {
                $shipment->setTrackingCode();
                $shipment->scheduleNotification();
                foreach ($dimensions as $item) {
                    $dimension = new ShipmentPackDimension();
                    $dimension->shipment_id =  $shipment->id;
                    $dimension->qty    = @$item['qty'] ??  1;
                    $dimension->weight      = @$item['weight'] ?? 0.00;
                    $dimension->description = @$item['article_name'] ?? "";
                    $dimension->length = @$item['article_length'] ?? 0.00;
                    $dimension->width = @$item['article_width'] ?? 0.00;
                    $dimension->height = @$item['article_height'] ?? 0.00;
                    $dimension->volume   = @$item['article_m3'] ?? 0.00;

                    $dimension->optional_fields = null;
                    $shipment->has_assembly     = 0;
                    if (@$item['assembly'] == 'Sim' ||  @$item['assembly'] == 'sim' || @$item['assembly'] == 1 || @$item['assembly'] == '1') {
                        $dimension->optional_fields = '{"Montagem":"1"}';
                        $shipment->has_assembly = 1;
                        $shipment->save();
                    }
                    $dimension->save();
                }

                /**
                 * Atualiza o estado do envio
                 */
                $history = ShipmentHistory::firstOrNew([
                    'shipment_id' => $shipment->id,
                    'status_id' => $shipment->status_id
                ]);

                $history->shipment_id = $shipment->id;
                $history->status_id = $shipment->status_id;
                $history->save();

                //SUBMIT BY WEBSERVICE
                if ($autoSubmit) {
                    try {
                        $debug = $request->get('debug', false);
                        $webservice = new Base($debug);
                        $webservice->submitShipment($shipment);
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $services = $services->pluck('name', 'id')->toArray();

            $customers = Customer::whereIn('id', $customersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $agencyId = $request->agency_id;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'shipmentStatus',
                'previewMode',
                'agencyId',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {

            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {
                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

    /**
     * Import logistic locations
     * 
     * @param Request $request
     * @param ImporterModel $model
     * @param string $filepath
     * @param bool $previewMode
     * @param bool $directImport
     */
    public function logistic_locations(Request $request, $model, $filepath, $previewMode)
    {
        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $importerCollection = new ImporterController();
                $columnMapping = $importerCollection->getColumnMapping();

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.logistic_locations') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $status = array_flip(trans('admin/logistic.locations.status') ?? []);
        $colors = array_flip(trans('admin/global.colors-tiny') ?? []);

        $hasErrors = false;
        $previewRows = [];
        $excel = Excel::load($filepath, function ($reader) use (&$hasErrors, $mapAttrs, &$previewRows, &$previewMode, $status, $colors) {
            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $insertArr = [];
            $reader->each(function ($row) use (&$hasErrors, $mapAttrs, &$previewRows, &$previewMode, &$insertArr, $status, $colors) {

                if (!empty($row)) {
                    //$errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    if (!empty($row['rack']) && !empty($row['bay']) && !empty($row['level'])) {
                        $locationType = LocationType::where('name', $row['type_id'])->first();
                        if (empty($locationType)) {
                            $row['errors'][] = 'Tipo de localização "' . $row['type_id'] . '" não encontrado.';
                        }

                        $warehouse = Warehouse::where('code', $row['warehouse_id'])
                            ->orWhere('name', $row['warehouse_id'])
                            ->first();

                        if (empty($warehouse)) {
                            $row['errors'][] = 'Armazém com o código/nome "' . $row['warehouse_id'] . '" não encontrado.';
                        }

                        $row['color']       = $row['color'] ?? 'Amarelo';
                        $row['status']      = $row['status'] ?? 'Livre';
                        $row['code']        = $row['code'] ?? $row['rack'] . '-' . $row['bay'] . '-' . $row['level'] . ($row['position'] ?? '');
                        $row['barcode']     = $row['barcode'] ?? @$warehouse->code . str_replace('-', '', $row['code']);
                        $row['created_at']  = date('Y-m-d H:i:s');

                        if ($previewMode) {
                            if (!empty($row['errors'])) {
                                $hasErrors = $hasErrors + 1;
                            }

                            $previewRows[] = $row;
                        } else {
                            if (!empty($row['errors'])) {
                                return;
                            }

                            $row['type_id'] = $locationType->id;
                            $row['warehouse_id'] = $warehouse->id;
                            $row['color'] = $colors[$row['color']] ?? $colors['Amarelo'];
                            $row['status'] = $status[$row['status']] ?? 'free';
                            $insertArr[] = $row;
                        }
                    }
                }
            });

            $result = true;
            if (!empty($insertArr) && !$previewMode) {
                $result = Location::insert($insertArr);
            }

            if (!$result) {
                return Redirect::back()->with('error', 'A importação falhou');
            }
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewRows) {
            $this->preview($model, $headerRow, $previewRows, $filepath, $hasErrors);
        } else {
            return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
        }
    }

    /**
     * Import shipping order
     * 
     * @param Request $request
     * @param ImporterModel $model
     * @param string $filepath
     * @param bool $previewMode
     */
    public function shipping_orders(Request $request, $model, $filepath, $previewMode)
    {
        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        if (!$request->get('shipping_order_customer') && !$model->customer_code) {
            return Redirect::route('admin.importer.index')->with('error', 'Tem de indicar o cliente.');
        } else {

            $customerCode = $model->customer_code ? $model->customer_code : $request->get('shipping_order_customer');
            $customerCode = trim($customerCode);

            $customer = Customer::filterSource()
                ->where('code', $customerCode)
                ->isActive()
                ->first();

            if (!$customer) {
                return Redirect::route('admin.importer.index')->with('error', 'O cliente indicado não existe ou está inativo');
            }
        }

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.shipping_orders') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $hasErrors = 0;
        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $previewMode, &$previewRows, &$hasErrors, $customer) {
            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $previewMode, &$previewRows, &$hasErrors, &$i, $customer) {
                if (!empty($row)) {
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);
                    if (empty($row['sku'])) {
                        return;
                    }

                    $errors = [];
                    $row['customer']            = $customer->name;
                    $row['sku']                 = trim(@$row['sku']);
                    $row['lote']                = trim(@$row['lote']);
                    $row['qty']                 = trim(@$row['qty']);
                    $row['qty_satisfied']       = !empty($row['qty_satisfied']) ? $row['qty_satisfied'] : 0;
                    $row['location']            = trim($row['location'] ?? null);
                    $row['lote']                = trim($row['lote'] ?? null);
                    $row['serial_no']           = trim($row['serial_no'] ?? null);
                    $row['price']               = !empty($row['price']) ? $row['price'] : null;
                    $row['location_id']         = null;
                    $row['product_location_id'] = null;

                    $product = Product::where('customer_id', $customer->id)->where('sku', $row['sku']);
                    if ($row['lote']) {
                        $product->where('lote', $row['lote']);
                    }
                    if ($row['serial_no']) {
                        $product->where('serial_no', $row['serial_no']);
                    }
                    $product = $product->first();

                    $row['product'] = $product ? $product->toArray() : null;
                    $row['name']    = $product ? $product->name : '';

                    if (empty($product)) {
                        if (!empty($row['lote'])) {
                            $errors[] = 'Artigo com o SKU ' . $row['sku'] . ' e lote ' . $row['lote'] . ' inexistente para o cliente.';
                        } else if (!empty($row['serial_no'])) {
                            $errors[] = 'Artigo com o SKU ' . $row['sku'] . ' e N.º Série ' . $row['serial_no'] . ' inexistente para o cliente.';
                        } else {
                            $errors[] = 'Artigo com o SKU ' . $row['sku'] . ' inexistente para o cliente.';
                        }
                    }

                    $location = Location::where('code', $row['location'])
                        ->orWhere('barcode', $row['location'])
                        ->first();

                    if (!$location) {
                        $errors[] = 'Localização com o código ' . $row['location'] . ' inexistente.';
                    } else {
                        $row['location_id'] = $location->id;
                        $productLocation = ProductLocation::where('product_id', $product->id)
                            ->where('location_id', $location->id)
                            ->first();

                        if (!$productLocation) {
                            $errors[] = 'O produto não tem stock na localização ' . $row['location'] . '.';
                        } else if ($row['qty'] > $productLocation->stock_available) {
                            $errors[] = 'A localização ' . $row['location'] . ' não tem stock suficiente (' . $productLocation->stock_available . ') para alocar ' . $row['qty'] . ' artigos.';
                        } else {
                            $row['product_location_id'] = $productLocation->id;
                        }
                    }

                    if ($row['qty'] <= 0) {
                        $errors[] = 'Stock a sair tem de ser maior que zero.';
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $row['errors'] = $errors;
                            $hasErrors = $hasErrors + 1;
                        }
                    }

                    $previewRows[] = $row;
                }

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $this->preview($model, $headerRow, $previewRows, $filepath, $hasErrors);
        } else {
            // dd($previewRows);

            try {
                // Create shipping order
                $shippingOrder = new ShippingOrder();
                $shippingOrder->source         = config('app.source');
                $shippingOrder->customer_id    = $customer->id;
                $shippingOrder->date           = $request->get('shipping_order_date');
                $shippingOrder->document       = $request->get('shipping_order_doc');
                $shippingOrder->status_id      = ShippingOrderStatus::STATUS_PENDING;
                $shippingOrder->user_id        = Auth::user()->id;
                $shippingOrder->setCode();

                // Insert shipping order lines
                $count = $qty = $qtySat = $vols = $weight = $price = $volume = 0;
                foreach ($previewRows as $row) {
                    $shippingOrderLine = ShippingOrderLine::firstOrNew([
                        'shipping_order_id'   => $shippingOrder->id,
                        'product_id'          => $row['product']['id'],
                        'location_id'         => $row['location_id'],
                        'product_location_id' => $row['product_location_id'],
                        'lote'                => $row['lote'],
                        'serial_no'           => $row['serial_no']
                    ]);

                    if ($shippingOrderLine->exists) {
                        $shippingOrderLine->qty           = ($shippingOrderLine->qty ?? 0) + $row['qty'];
                        $shippingOrderLine->qty_satisfied = ($shippingOrderLine->qty_satisfied ?? 0) + $row['qty_satisfied'];
                    } else {
                        $shippingOrderLine->qty           = $row['qty'];
                        $shippingOrderLine->qty_satisfied = $row['qty_satisfied'];
                    }

                    if ($shippingOrderLine->qty_satisfied > $shippingOrderLine->qty) {
                        $shippingOrderLine->qty = $shippingOrderLine->qty_satisfied;
                    }

                    $shippingOrderLine->price = $row['price'];
                    $shippingOrderLine->save();
                    $shippingOrderLine->updateStockTotals();

                    $productWeight = $row['product']['weight'] ?? 0;

                    $count++;
                    $qty    += $shippingOrderLine->qty;
                    $qtySat += $shippingOrderLine->qty_satisfied;
                    $vols   += $shippingOrderLine->qty;
                    $weight += $productWeight * $shippingOrderLine->qty;
                    $volume *= $shippingOrderLine->qty;
                    $price  += $shippingOrderLine->price * $shippingOrderLine->qty;
                }

                $shippingOrder->total_items   = $count;
                $shippingOrder->qty_total     = $qty;
                $shippingOrder->qty_satisfied = $qtySat;
                $shippingOrder->total_volumes = $vols;
                $shippingOrder->total_weight  = $weight;
                $shippingOrder->total_price   = $price;
                $shippingOrder->total_volume  = $volume;
                $shippingOrder->save();
            } catch (\Exception $e) {
                Log::error($e);
                dd($e->getMessage());
            }

            return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
        }
    }

    public function billing_products(Request $request, $model, $filePath, $previewMode) {
        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.billing_products') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $unities = trans('admin/billing.items-unities');

        $hasErrors = 0;
        $excel = Excel::load($filePath, function ($reader) use ($mapAttrs, $previewMode, &$previewRows, &$hasErrors, $unities) {
            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $previewMode, &$previewRows, &$hasErrors, &$i, $unities) {
                if (!empty($row)) {
                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);
                    
                    $billingProduct = Item::filterSource()->where('reference', @$row['reference'])->first();
                    if ($billingProduct) {
                        return;
                    }

                    /**
                     * Tax Rate
                     */
                    $taxRate = VatRate::getByCode(@$row['tax_rate_code']);
                    if ($taxRate) {
                        $row['tax_rate']      = $taxRate->code;
                        $row['tax_rate_code'] = $taxRate->name;
                    } else {
                        // $errors[] = 'Taxa de IVA com o código '. @$row['tax_rate_code'] .' não encontrada';
                        return;
                    }
                    /**-- */

                    /**
                     * Provider
                     */
                    if (!empty($row['provider_code'])) {
                        $provider = Provider::filterSource()
                            ->where('code', $row['provider_code'])
                            ->first();

                        if ($provider) {
                            $row['provider_id']   = $provider->id;
                            $row['provider_code'] = $provider->name;
                        } else {
                            $row['provider_code'] = '';
                        }
                    }
                    /**-- */

                    /**
                     * Price
                     */
                    if (!empty($row['price'])) {
                        $row['price'] = round($row['price'], 2);
                    }

                    if (!empty($row['sell_price'])) {
                        $row['sell_price'] = round($row['sell_price'], 2);
                    }
                    /**-- */

                    /**
                     * Unity
                     */
                    if (@$row['has_stock']) {
                        $unity        = @$unities[$row['unity']];
                        $row['unity'] = $unity ? $row['unity'] : 'un';
                    } else {
                        $row['has_stock']   = false;
                        $row['stock_total'] = 0.00;
                        $row['unity']       = null;
                    }
                    /**-- */

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $row['errors'] = $errors;
                            $hasErrors = $hasErrors + 1;
                        }
                    } else {
                        /**
                         * Brand
                         */
                        if (!empty($row['brand'])) {
                            $brand = Brand::filterSource()->firstOrNew([
                                'name' => $row['brand']
                            ]);

                            $brand->is_active = true;
                            $brand->save();

                            $row['brand_id'] = $brand->id;

                            if (!empty($row['brand_model'])) {
                                $brandModel = BrandModel::filterSource()->firstOrNew([
                                    'brand_id' => $brand->id,
                                    'name' => $row['brand']
                                ]);

                                $brandModel->save();
                                $row['brand_model_id'] = $brandModel->id;
                            }
                        }
                        /**-- */

                        $billingProduct = new Item;
                        unset($billingProduct->api_key);
                        $billingProduct->fill($row);
                        $billingProduct->source = config('app.source');
                        $billingProduct->save();

                        /**
                         * Stock
                         */
                        if (@$row['has_stock']) {
                            $stockTotal = @$row['stock_total'] ?? 0.00;
                            $price      = @$row['price'] ?? 0.00;
                            ItemStockHistory::setInitial($billingProduct, $stockTotal, $price);
                        }
                        /**-- */
                    }

                    $previewRows[] = $row;
                }

                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            return $this->preview($model, $headerRow, $previewRows, $filePath, $hasErrors);
        }

        return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
    }




    /**
     * Return list of services with data attributes
     *
     * @param type $allServices
     * @return type
     */
    public function listModels($allModels)
    {

        $models[] = ['value' => '', 'display' => ''];
        foreach ($allModels as $model) {
            $models[] = [
                'value'     => $model->id,
                'display'   => $model->name,
                'data-type' => $model->type,
            ];
        }

        return $models;
    }

    public function getColumnMapping()
    {
        return $this->columnMapping;
    }

    /**
     * Removes import products names (family_name, category_name, subcategory_name, brand_name, model_name)
     * from array and replaces them by their ids (family_id, category_id, subcategory_id, brand_id, model_id)
     * 
     * @param array $row
     * @return void
     */
    public function importLogisticProductsChangeNamesToIds(array &$row)
    {
        $customerId = $row['customer_id'] ?? null;

        // Process family_name
        if (empty($row['family_name'])) {
            $row['family_id'] = null;
        } else {
            $family = Family::filterSource()
                ->firstOrNew(['name' => $row['family_name']]);

            if (!$family->id) {
                $family->source         = config('app.source');
                $family->name           = $row['family_name'];
                $family->customer_id    = $customerId;
                $family->save();
            }

            $row['family_id'] = $family->id;
        }
        //--

        // Process category_name and subcategory_name
        if (empty($row['category_name'])) {
            $row['category_id']     = null;
            $row['subcategory_id']  = null;
        } else {
            $category = LogisticCategory::filterSource()
                ->firstOrNew(['name' => $row['category_name']]);

            if (!$category->id) {
                $category->source       = config('app.source');
                $category->name         = $row['category_name'];
                $category->family_id    = $row['family_id'];
                $category->customer_id  = $customerId;
                $category->save();
            }

            $row['category_id'] = $category->id;

            // Process subcategory_name
            if (empty($row['subcategory_name'])) {
                $row['subcategory_id'] = null;
            } else {
                $subCategory = SubCategory::filterSource()
                    ->where('category_id', $row['category_id'])
                    ->firstOrNew(['name' => $row['subcategory_name']]);

                if (!$subCategory->id) {
                    $subCategory->source        = config('app.source');
                    $subCategory->name          = $row['subcategory_name'];
                    $subCategory->category_id   = $row['category_id'];
                    $subCategory->customer_id   = $customerId;
                    $subCategory->save();
                }

                $row['subcategory_id'] = $subCategory->id;
            }
            //--
        }
        //--

        // Process brand_name and model_name
        if (empty($row['brand_name'])) {
            $row['brand_id'] = null;
            $row['model_id'] = null;
        } else {
            $brand = Brand::filterSource()
                ->firstOrNew(['name' => $row['brand_name']]);

            if (!$brand->id) {
                $brand->source      = config('app.source');
                $brand->name        = $row['brand_name'];
                $brand->customer_id = $customerId;
                $brand->save();
            }

            $row['brand_id'] = $brand->id;

            // Process model_name
            if (empty($row['model_name'])) {
                $row['model_id'] = null;
            } else {
                $model = Model::filterSource()
                    ->where('brand_id', $row['brand_id'])
                    ->firstOrNew(['name' => $row['model_name']]);

                if (!$model->id) {
                    $model->source      = config('app.source');
                    $model->name        = $row['model_name'];
                    $model->brand_id    = $row['brand_id'];
                    $model->customer_id = $customerId;
                    $model->save();
                }

                $row['model_id'] = $model->id;
            }
            //--
        }
        //--

        // Remove names from $row
        unset($row['family_name']);
        unset($row['category_name']);
        unset($row['subcategory_name']);
        unset($row['brand_name']);
        unset($row['model_name']);
    }

    public function preview($model, $headerRow, $previewRows, $filepath, $hasErrors, $services = [], $providersIds = [])
    {
        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code')
            ->get());

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $customerTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $models = ImporterModel::filterSource()->get();
        $models = $this->listModels($models);

        $providers = Provider::whereIn('id', $providersIds)->pluck('name', 'id')->toArray();

        $importType = $model->type;

        $data = compact(
            'previewRows',
            'headerRow',
            'agencies',
            'providers',
            'customerTypes',
            'models',
            'services',
            'importType',
            'providers',
            'hasErrors',
            'filepath'
        );

        $data['previewMode'] = true;

        return $this->setContent('admin.files_importer.index', $data);
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function fleet_vehicles(Request $request, $model, $filepath, $previewMode, $directImport = false)
    {
        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);

        $mapAttrs = [];
        foreach ($model->mapping as $key => $value) {
            if (!empty($value)) {

                $columnMapping = $this->columnMapping;

                if (in_array($value, array_keys($columnMapping))) {
                    $mapAttrs[$columnMapping[$value]] = $key;
                } else {
                    $value = (int) $value;
                    $mapAttrs[$value - 1] = $key;
                }
            }
        }

        $headerRow = [];
        foreach (trans('admin/importer.fleet_vehicles') as $key => $value) {
            if ($value['preview']) {
                $headerRow[] = $key;
            }
        }

        $agencies      = Agency::get();
        $customersIds  = [];
        $printIds      = [];
        $hasErrors     = 0;

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, &$printIds, $agencies, &$customerTypes, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors) {
            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;

            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $agencies, &$customerTypes, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors, &$i) {
                if (!empty($row)) {
                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);
                    $row['agency_id'] = $model->agency_id;
                    unset($row['brand_id']);
                    try {
                        $vehicle = Vehicle::firstOrNew([
                            'license_plate'     => $row['license_plate']
                        ]);

                        $vehicle->fill($row);
                        $vehicle->brand_id = 1;
                        if (isset($row['is_trailer']) && $row['is_trailer'] == 1) {
                            $vehicle->type = 'trailer';
                        } else {
                            $vehicle->type = 'truck';
                        }

                        $vehicle->source = config('app.source');
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $vehicle->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }
                        $previewRows[] = $vehicle->toArray();
                    } else {
                        $vehicle->save();
                    }
                }
                $i++;
            });
        });

        if (!$excel) {
            return Redirect::back()->with('error', 'Impossível abrir o ficheiro. A extensão não é reconhecida.');
        }

        if ($previewMode) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->orderBy('code')
                ->get());

            $providers = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->filterAgencies()
                ->isCarrier()
                ->ordered()
                ->pluck('name', 'id')
                ->toArray();

            $customerTypes = CustomerType::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerType::CACHE_TAG)
                ->filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $models = ImporterModel::filterSource()->get();
            $models = $this->listModels($models);

            $customers = Customer::whereIn('id', $customersIds)->pluck('name', 'id')->toArray();

            $importType = $model->type;

            $data = compact(
                'previewRows',
                'headerRow',
                'agencies',
                'providers',
                'customerTypes',
                'models',
                'services',
                'importType',
                'customers',
                'hasErrors',
                'previewMode',
                'filepath'
            );

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }
}
