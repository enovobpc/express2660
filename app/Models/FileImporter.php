<?php

namespace App\Models;

use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use Excel, Setting, Auth;

class FileImporter extends BaseModel
{

    /**
     * Excel column mapping
     * @var array
     */
    public $columnMapping = [
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
     * Import Shipments
     *
     * @param Request $request
     * @param $model
     * @param $filepath
     * @param $previewMode
     * @return type
     */
    public function shipments(Request $request, $model, $filepath, $previewMode, $source = 'admin', $importationHashId = null)
    {

        set_time_limit(3000000);
        ini_set('max_execution_time', 3000000);
        ini_set('memory_limit', -1);


        if ($model->start_row > 1) {
            config(['excel.import.startRow' => $model->start_row]);
        }

        $customerCollection = null;
        if ($source == 'account') {
            $customerCollection = Auth::guard('customer')->user();
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
        foreach (trans('admin/importer.shipments') as $key => $value) {

            if ($source == 'account') {
                if (@$value['preview_customer']) {
                    $headerRow[] = $key;
                }
            } else if ($value['preview']) {
                $headerRow[] = $key;
            }
        }


        $agencies       = Agency::get();
        $services       = Service::filterSource()->get();
        $shipmentStatus = ShippingStatus::pluck('name', 'id')->toArray();
        $customersIds   = [];
        $printIds       = [];
        $hasErrors      = 0;
        $allExpenses    = ShippingExpense::filterSource()->get(['id', 'code', 'name', 'price', 'zones', 'type']);
        $rpackArr       = explode(',', $request->get('rpack'));
        $rguideArr      = explode(',', $request->get('rguide'));
        $rcheckArr      = explode(',', $request->get('rcheck'));
        $countries      = trans('country');

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, $services, &$printIds, $agencies, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors, $shipmentStatus, $allExpenses, $rcheckArr, $rpackArr, $rguideArr, $customerCollection, $countries, $source, $importationHashId) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, $services, &$printIds, $agencies, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors, $shipmentStatus, $allExpenses, &$i, $rcheckArr, $rpackArr, $rguideArr, $customerCollection, $countries, $source, $importationHashId) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['trailer'] = $importationHashId ? $importationHashId : null;
                    $row['rpack']   = empty($row['rpack']) ? 0 : 1;
                    $row['rcheck']  = empty($row['rcheck']) ? 0 : 1;
                    $row['rguide']  = empty($row['rguide']) ? 0 : 1;

                    $row['customer_code']     = $model->customer_code ?  $model->customer_code : @$row['customer_code'];
                    $input['provider_id']     = $model->provider_id ? $model->provider_id : @$request->provider_id;
                    $input['service_id']      = $model->service_id;


                    $row['sender_address']    = trim(@$row['sender_address'] . ' ' . @$row['sender_address_2']);
                    $row['recipient_address'] = trim(@$row['recipient_address'] . ' ' . @$row['recipient_address_2']);
                    $row['sender_address']    = trim($row['sender_address']);
                    $row['recipient_address'] = trim($row['recipient_address']);

                    $row['sender_country']    = trim(strtolower(@$row['sender_country']));
                    $row['recipient_country'] = trim(strtolower(@$row['recipient_country']));
                    $row['recipient_country'] = empty($row['recipient_country']) ? Setting::get('app_country') : $row['recipient_country'];

                    if ($source == 'account') {
                        $row['sender_name']     = empty($row['sender_name']) ? $customerCollection->name : $row['sender_name'];
                        $row['sender_address']  = empty($row['sender_address']) ? $customerCollection->address : $row['sender_address'];
                        $row['sender_zip_code'] = empty($row['sender_zip_code']) ? $customerCollection->zip_code : $row['sender_zip_code'];
                        $row['sender_city']     = empty($row['sender_city']) ? $customerCollection->city : $row['sender_city'];
                        $row['sender_country']  = empty($row['sender_country']) ? $customerCollection->country : $row['sender_country'];
                        $row['sender_phone']    = empty($row['sender_phone']) ? $customerCollection->phone : $row['sender_phone'];
                    }

                    if (@$row['start_hour']) {
                        $row['start_hour'] = trim(@$row['start_hour']);
                        $row['start_hour'] = new Date($row['start_hour']);
                        $row['start_hour'] = $row['start_hour']->format('H:i');
                    }

                    if (@$row['end_hour']) {
                        $row['end_hour'] = trim(@$row['end_hour']);
                        $row['end_hour'] = new Date($row['end_hour']);
                        $row['end_hour'] = $row['end_hour']->format('H:i');
                    }

                    if (empty(@$row['service_code']) && empty($input['service_id'])) {
                        $errors[] = 'Não indicou o tipo serviço que pretende usar.';
                    }

                    if (!isset($countries[$row['sender_country']])) {
                        $errors[] = 'Código do país de origem inválido. O código deve cumprir a norma ISO 3166-1 alfa-2 (Ex: PT, ES, FR,...)';
                    }

                    if (!isset($countries[$row['recipient_country']])) {
                        $errors[] = 'Código do país de destino inválido. O código deve cumprir a norma ISO 3166-1 alfa-2 (Ex: PT, ES, FR,...)';
                    }

                    if (empty(@$row['sender_zip_code'])) {
                        $errors[] = 'Não indicou o código postal de origem.';
                    }

                    if (empty(@$row['recipient_zip_code'])) {
                        $errors[] = 'Não indicou o código postal de destino.';
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
                        } else {
                            $row['date'] = date('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $errors[] = 'Erro ao ler formato da data. Verifique se a data no ficheiro corresponde ao formato configurado no modelo.';
                    }

                    $shipment = null;

                    if (isset($row['provider_tracking_code']) && !empty($row['provider_tracking_code'])) {
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
                                return $item->display_code == $code;
                            })->first();
                        }

                        if ($service) {
                            $shipment->service_id    = $service->id;
                            $shipment->is_collection = $service->is_collection;

                            if (empty(@$input['provider_id'])) {
                                $input['provider_id'] = $service->provider_id;
                            }
                        } else {
                            $errors[] = 'Não existe nenhum serviço com o código ' . $code . '.';
                        }

                        //create array of complementar services
                        if (!empty($row['charge_price'])) {
                            //charge expense
                            empty($row['charge_price']) ?: $row['complementar_services'][] = Shipment::getChargeExpense($allExpenses, $service);
                        }
                    }

                    /**
                     * CUSTOMER
                     */
                    if ((!$shipment->exists && !empty($row['customer_code'])) || ($source == 'account')) {

                        if ($source == 'account') { //area de cliente assume os dados do cliente
                            $customer = $customerCollection;
                        } else {
                            $customer = Customer::where('code', $row['customer_code'])
                                ->where('agency_id', $request->agency_id)
                                ->whereNull('customer_id')
                                ->first();
                        }

                        if ($customer) {
                            $customersIds[]         = $customer->id;
                            $row['customer_id']     = $customer->id;
                            $row['sender_name']     = isset($row['sender_name']) && !empty($row['sender_name']) ? $row['sender_name'] : $customer->name;
                            $row['sender_address']  = isset($row['sender_address']) && !empty($row['sender_address']) ? $row['sender_address'] : $customer->address;
                            $row['sender_zip_code'] = isset($row['sender_zip_code']) && !empty($row['sender_zip_code']) ? $row['sender_zip_code'] : $customer->zip_code;
                            $row['sender_city']     = isset($row['sender_city']) && !empty($row['sender_city']) ? $row['sender_city'] : $customer->city;
                            $row['sender_country']  = isset($row['sender_country']) && !empty($row['sender_country']) ? $row['sender_country'] : $customer->country;
                            $row['sender_phone']    = isset($row['sender_phone']) && !empty($row['sender_phone']) ? $row['sender_phone'] : $customer->phone;
                        } else {
                            $row['customer_id'] = null;
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

                        $statusId = isset($row['status_code']) && !empty($row['status_code']) ? $row['status_code'] : (Setting::get('shipment_status_after_create') ? Setting::get('shipment_status_after_create') : 2);

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
                     * AGENCY
                     */
                    if ($source == 'account') {
                        $agencyId = $customer->agency_id;
                        $recipientAgencyId = null;
                    } else {
                        $agencyId = $request->agency_id;
                        $recipientAgencyId = $request->recipient_agency_id;
                    }

                    $agencies = $this->getAgencies($shipment, $agencies, @$input['provider_id'], @$agencyId, @$recipientAgencyId);
                    $shipment->fill($agencies);

                    /**
                     * ATUALIZA OS CAMPOS TODOS DO ENVIO E PRÉ-PREENCHE A VARIAVEL SHIPMENT
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
                        $row['agency_id'],
                        $row['recipient_agency_id']
                    );

                    //dd($row['provider_id']);
                    $shipment->fill($row);
                    $shipment->sender_phone    = nospace($shipment->sender_phone);
                    $shipment->recipient_phone = nospace($shipment->recipient_phone);

                    /**
                     * CALC PRICE
                     */
                    if (!$previewMode) {
                        $prices = Shipment::calcPrices($shipment);
                        $shipment->total_price    = @$prices['total'];
                        $shipment->cost_price     = @$prices['cost'];
                        $shipment->fuel_tax       = @$prices['fuelTax'];
                        $shipment->extra_weight   = @$prices['extraKg'];
                    }

                    $saveIds = (!$shipment->exists && $request->print_labels) ? true : false;

                    if (empty($shipment->customer_id)) {
                        $errors[] = 'Este envio não está associado a nenhum cliente.';
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $shipment->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $shipment->rpack  = $row['rpack'];
                        $shipment->rguide = $row['rguide'];
                        $shipment->rcheck = $row['rcheck'];
                        $shipment->service_code = $code;

                        $shipmentArr = $shipment->toArray();
                        $shipmentArr['errors'] = $errors;
                        $previewRows[] = $shipmentArr;
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

                        if ($shipment->exists) {
                            $shipment->save();
                        } else {
                            $shipment->setTrackingCode();

                            // Store adicional services
                            Shipment::assignExpenses($shipment, $row, $allExpenses);
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

            if ($source == 'account') {
                $models = ImporterModel::where(function ($q) {
                    $q->whereNull('source');
                    $q->orWhere('source', config('app.source'));
                })
                    ->where('available_customers', 1)
                    ->pluck('name', 'id')
                    ->toArray();
            } else {
                $models = ImporterModel::filterSource()->get();
                $models = @$this->listModels($models);
            }

            $services = $services->pluck('name', 'id')->toArray();

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
                'shipmentStatus',
                'previewMode',
                'filepath'
            );

            if ($source == 'account') {
                return $data;
            }

            return $this->setContent('admin.files_importer.index', $data);
        } else {
            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {

                if ($source == 'account') {
                    return true;
                }

                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
    }

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
        $customerTypes = CustomerType::pluck('name', 'id')->toArray();
        $customersIds  = [];
        $printIds      = [];
        $hasErrors     = 0;

        $excel = Excel::load($filepath, function ($reader) use ($mapAttrs, $request, &$printIds, $agencies, $customerTypes, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $i = 0;
            $reader->each(function ($row) use ($mapAttrs, $request, &$printIds, $agencies, $model, $previewMode, &$previewRows, &$headerRow, &$customersIds, &$hasErrors, &$i) {

                if (!empty($row)) {

                    $errors = [];
                    $row = mapArrayKeys(array_values($row->toArray()), $mapAttrs);

                    $row['agency_id'] = $model->agency_id ?  $model->agency_id : @$row['agency_id'];
                    $row['type_id'] = $model->type_id ?  $model->type_id : @$row['type_id'];

                    $row['name']     = trim(@$row['name']);
                    $row['address']  = trim(@$row['address']);
                    $row['zip_code'] = trim(@$row['zip_code']);
                    $row['city']     = trim(@$row['city']);
                    $row['vat']      = trim(str_replace(' ', '', @$row['vat']));

                    try {
                        $customer = new Customer();
                        $customer->fill($row);
                        $customer->source = config('app.source');
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    if ($previewMode) {
                        if (!empty($errors)) {
                            $customer->errors = $errors;
                            $hasErrors = $hasErrors + 1;
                        }

                        $previewRows[] = $customer->toArray();
                    } else {
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

        if (!$shipment->exists) {

            if (empty($zipCode) && empty($country)) {
                $fullZipCode = '';
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
                    $country = strlen($zipCode) == 4 ? 'pt' : '';
                }
            }

            return [
                'zip_code' => $fullZipCode,
                'country'  => $country,
            ];
        }

        return [
            'zip_code' => $zipCode,
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

        $providerId = empty($providerId) ? Setting::get('shipment_default_provider') : $providerId;

        $result = [
            'agency_id'             => $agencyId,
            'sender_agency_id'      => $agencyId,
            'recipient_agency_id'   => $recipientAgencyId,
            'provider_id'           => $providerId
        ];

        if (config('app.source') == 'volumedourado' && $providerId == '112') {
            //força envios pela envialia da volumedourado a ficarem com serviço 24H
            $result['service_id'] = '179';
        }

        return $result;
    }

    public function shipments_dimensions(Request $request, $model, $filepath, $previewMode, $source = 'admin', $importationHashId = null)
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
                    'article_length' => @$item['article_length']
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
            }

            $row['rpack']  = empty($row['rpack']) ? 0 : 1;
            $row['rcheck'] = empty($row['rcheck']) ? 0 : 1;
            $row['rguide'] = empty($row['rguide']) ? 0 : 1;

            //Customer
            $shipment->customer_id = @$customer->id;

            //VOLUME AND WEIGHT
            $shipment->volumes  = $totalVolumes;
            $shipment->weight = $totalWeight;
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

            if ($source == 'account') {
                $models = ImporterModel::where(function ($q) {
                    $q->whereNull('source');
                    $q->orWhere('source', config('app.source'));
                })
                    ->where('available_customers', 1)
                    ->pluck('name', 'id')
                    ->toArray();
            } else {
                $models = ImporterModel::filterSource()->get();
                $models = $this->listModels($models);
            }
            $services = $services->pluck('name', 'id')->toArray();

            $customers = Customer::whereIn('id', $customersIds)->pluck('name', 'id')->toArray();

            if ($source == 'account') {
                $customers = Auth::guard('customer')->user()->pluck('name', 'id')->toArray();
            }

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
            if ($source == 'account') {
                return $data;
            }

            return $this->setContent('admin.files_importer.index', $data);
        } else {

            if (!empty($printIds)) {
                return Shipment::printAdhesiveLabels($printIds);
            } else {
                if ($source == 'account') {
                    return;
                }
                return Redirect::route('admin.importer.index')->with('success', 'Ficheiro importado com sucesso.');
            }
        }
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
}
