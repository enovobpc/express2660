<script>
    var APP_COUNTRY = "{{ setting('app_country') }}";
    var LOCALE      = "{{ Lang::locale() }}";
    var DIALCODES   = {"bd": "+880", "be": "+32", "bf": "+226", "bg": "+359", "ba": "+387", "bb": "+1-246", "wf": "+681", "bl": "+590", "bm": "+1-441", "bn": "+673", "bo": "+591", "bh": "+973", "bi": "+257", "bj": "+229", "bt": "+975", "jm": "+1-876", "bw": "+267", "ws": "+685", "bq": "+599", "br": "+55", "bs": "+1-242", "je": "+44-1534", "by": "+375", "bz": "+501", "ru": "+7", "rw": "+250", "rs": "+381", "tl": "+670", "re": "+262", "tm": "+993", "tj": "+992", "ro": "+40", "tk": "+690", "gw": "+245", "gu": "+1-671", "gt": "+502", "gr": "+30", "gq": "+240", "gp": "+590", "jp": "+81", "gy": "+592", "gg": "+44-1481", "gf": "+594", "ge": "+995", "gd": "+1-473", "gb": "+44", "ga": "+241", "sv": "+503", "gn": "+224", "gm": "+220", "gl": "+299", "gi": "+350", "gh": "+233", "om": "+968", "tn": "+216", "jo": "+962", "hr": "+385", "ht": "+509", "hu": "+36", "hk": "+852", "hn": "+504", "hm": " ", "ve": "+58", "pr": "+1-787 and 1-939", "ps": "+970", "pw": "+680", "pt": "+351", "sj": "+47", "py": "+595", "iq": "+964", "pa": "+507", "pf": "+689", "pg": "+675", "pe": "+51", "pk": "+92", "ph": "+63", "pn": "+870", "pl": "+48", "pm": "+508", "zm": "+260", "eh": "+212", "ee": "+372", "eg": "+20", "za": "+27", "ec": "+593", "it": "+39", "vn": "+84", "sb": "+677", "et": "+251", "so": "+252", "zw": "+263", "sa": "+966", "es": "+34", "er": "+291", "me": "+382", "md": "+373", "mg": "+261", "mf": "+590", "ma": "+212", "mc": "+377", "uz": "+998", "mm": "+95", "ml": "+223", "mo": "+853", "mn": "+976", "mh": "+692", "mk": "+389", "mu": "+230", "mt": "+356", "mw": "+265", "mv": "+960", "mq": "+596", "mp": "+1-670", "ms": "+1-664", "mr": "+222", "im": "+44-1624", "ug": "+256", "tz": "+255", "my": "+60", "mx": "+52", "il": "+972", "fr": "+33", "io": "+246", "sh": "+290", "fi": "+358", "fj": "+679", "fk": "+500", "fm": "+691", "fo": "+298", "ni": "+505", "nl": "+31", "no": "+47", "na": "+264", "vu": "+678", "nc": "+687", "ne": "+227", "nf": "+672", "ng": "+234", "nz": "+64", "np": "+977", "nr": "+674", "nu": "+683", "ck": "+682", "ci": "+225", "ch": "+41", "co": "+57", "cn": "+86", "cm": "+237", "cl": "+56", "cc": "+61", "ca": "+1", "cg": "+242", "cf": "+236", "cd": "+243", "cz": "+420", "cy": "+357", "cx": "+61", "cr": "+506", "cw": "+599", "cv": "+238", "cu": "+53", "sz": "+268", "sy": "+963", "sx": "+599", "kg": "+996", "ke": "+254", "ss": "+211", "sr": "+597", "ki": "+686", "kh": "+855", "kn": "+1-869", "km": "+269", "st": "+239", "sk": "+421", "kr": "+82", "si": "+386", "kp": "+850", "kw": "+965", "sn": "+221", "sm": "+378", "sl": "+232", "sc": "+248", "kz": "+7", "ky": "+1-345", "sg": "+65", "se": "+46", "sd": "+249", "do": "1-809 and 1-829", "dm": "+1-767", "dj": "+253", "dk": "+45", "vg": "+1-284", "de": "+49", "ye": "+967", "dz": "+213", "us": "+1", "uy": "+598", "yt": "+262", "um": "+1", "lb": "+961", "lc": "+1-758", "la": "+856", "tv": "+688", "tw": "+886", "tt": "+1-868", "tr": "+90", "lk": "+94", "li": "+423", "lv": "+371", "to": "+676", "lt": "+370", "lu": "+352", "lr": "+231", "ls": "+266", "th": "+66", "tg": "+228", "td": "+235", "tc": "+1-649", "ly": "+218", "va": "+379", "vc": "+1-784", "ae": "+971", "ad": "+376", "ag": "+1-268", "af": "+93", "ai": "+1-264", "vi": "+1-340", "is": "+354", "ir": "+98", "am": "+374", "al": "+355", "ao": "+244", "as": "+1-684", "ar": "+54", "au": "+61", "at": "+43", "aw": "+297", "in": "+91", "ax": "+358-18", "az": "+994", "ie": "+353", "id": "+62", "ua": "+380", "qa": "+974", "mz": "+258"}
    var DIALCODES00 = {"bd": "00880","be": "0032","bf": "00226", "bg": "00359","ba": "00387","bb": "0111-246","wf": "00681","bl": "00590", "bm": "001-441", "bn": "00673", "bo": "00591", "bh": "00973", "bi": "00257", "bj": "00229", "bt": "00975", "jm": "0111-876", "bw": "00267", "ws": "00685", "bq": "00599", "br": "0055", "bs": "0111-242", "je": "0044-1534", "by": "00375", "bz": "00501", "ru": "8107", "rw": "00250", "rs": "00381", "tl": "00670", "re": "00262", "tm": "00993", "tj": "00992", "ro": "0040", "tk": "00690", "gw": "00245", "gu": "001-671", "gt": "00502", "gr": "0030", "gq": "00240", "gp": "00590", "jp": "01081", "gy": "00592", "gg": "0044-1481", "gf": "00594", "ge": "00995", "gd": "001-473", "gb": "0044", "ga": "00241", "sv": "00503", "gn": "00224", "gm": "00220", "gl": "00299", "gi": "00350", "gh": "00233", "om": "00968", "tn": "00216", "jo": "00962", "hr": "00385", "ht": "00509", "hu": "0036", "hk": "00852", "hn": "00504", "hm": " ", "ve": "0058", "pr": "001-787 and 1-939", "ps": "00970", "pw": "00680", "pt": "00351", "sj": "0047", "py": "00595", "iq": "00964", "pa": "00507", "pf": "00689", "pg": "00675", "pe": "0051", "pk": "0092", "ph": "0063", "pn": "00870", "pl": "0048", "pm": "00508", "zm": "00260", "eh": "00212", "ee": "00372", "eg": "0020", "za": "0027", "ec": "00593", "it": "0039", "vn": "0084", "sb": "00677", "et": "00251", "so": "00252", "zw": "00263", "sa": "00966", "es": "0034", "er": "00291", "me": "00382", "md": "00373", "mg": "00261", "mf": "00590", "ma": "00212", "mc": "00377", "uz": "00998", "mm": "0095", "ml": "00223", "mo": "00853", "mn": "001976", "mh": "00692", "mk": "00389", "mu": "00230", "mt": "00356", "mw": "00265", "mv": "00960", "mq": "00596", "mp": "001-670", "ms": "001-664", "mr": "00222", "im": "0044-1624", "ug": "00256", "tz": "00255", "my": "0060", "mx": "0052", "il": "00972", "fr": "0033", "io": "00246", "sh": "00290", "fi": "00358", "fj": "00679", "fk": "00500", "fm": "00691", "fo": "00298", "ni": "00505", "nl": "0031", "no": "0047", "na": "00264", "vu": "00678", "nc": "00687", "ne": "00227", "nf": "00672", "ng": "009234", "nz": "0064", "np": "00977", "nr": "00674", "nu": "00683", "ck": "00682", "ci": "00225", "ch": "0041", "co": "0057", "cn": "0086", "cm": "00237", "cl": "0056", "cc": "0061", "ca": "00111", "cg": "00242", "cf": "00236", "cd": "00243", "cz": "00420", "cy": "00357", "cx": "0061", "cr": "00506", "cw": "00599", "cv": "00238", "cu": "11953", "sz": "00268", "sy": "00963", "sx": "00599", "kg": "00996", "ke": "000254", "ss": "00211", "sr": "00597", "ki": "00686", "kh": "00855", "kn": "001-869", "km": "00269", "st": "00239", "sk": "00421", "kr": "0082", "si": "00386", "kp": "00850", "kw": "00965", "sn": "00221", "sm": "00378", "sl": "00232", "sc": "00248", "kz": "8107", "ky": "001-345", "sg": "0065", "se": "0046", "sd": "00249", "do": "001-809 and 1-829", "dm": "001-767", "dj": "00253", "dk": "0045", "vg": "001-284", "de": "0049", "ye": "00967", "dz": "00213", "us": "0111", "uy": "00598", "yt": "00262", "um": "001", "lb": "00961", "lc": "001-758", "la": "00856", "tv": "00688", "tw": "00886", "tt": "001-868", "tr": "0090", "lk": "0094", "li": "00423", "lv": "00371", "to": "00676", "lt": "00370", "lu": "00352", "lr": "00231", "ls": "00266", "th": "0066", "tg": "00228", "td": "00235", "tc": "001-649", "ly": "00218", "va": "00379", "vc": "001-784", "ae": "00971", "ad": "00376", "ag": "001-268", "af": "0093", "ai": "001-264", "vi": "001-340", "is": "00354", "ir": "0098", "am": "00374", "al": "00355", "ao": "00244", "as": "001-684", "ar": "0054", "au": "001161", "at": "0043", "aw": "00297", "in": "0091", "ax": "00358-18", "az": "00994", "ie": "00353", "id": "0062", "ua": "00380", "qa": "00974", "mz": "00258"}
    var DATATABLE_SEARCH_ON_ENTER = "{{ Setting::get('datatable_search_enter') }}"
    var NOTIFICATION_SOUND = "{{ app_notification_sound() }}"

    var SOURCE                  = "{{ config('app.source') }}";
    var APP_MODE                = "{{ Setting::get('app_mode') }}";
    var ROUTE_GET_PRICE         = "{{ route('admin.shipments.get.price') }}";
    var ROUTE_GET_AGENCY        = "{{ route('admin.shipments.get.agency') }}";
    var ROUTE_GET_PUDOS         = "{{ route('admin.shipments.get.pudos') }}";
    var ROUTE_SEARCH_SENDER     = "{{ route('admin.shipments.search.sender') }}";
    var ROUTE_SEARCH_RECIPIENT  = "{{ route('admin.shipments.search.recipient') }}";
    var ROUTE_SEARCH_CUSTOMER   = "{{ route('admin.shipments.search.customer') }}";
    var ROUTE_SEARCH_SKU        = "{{ route('admin.shipments.search.sku') }}";
    var ROUTE_GET_CUSTOMER      = "{{ route('admin.shipments.get.customer') }}";
    var ROUTE_GET_RECIPIENT     = "{{ route('admin.shipments.get.recipient') }}";
    var ROUTE_COMPARE_PRICES    = "{{ route('admin.shipments.compare.prices') }}";
    var ROUTE_OPTIMIZE_DELIVERY = "{{ route('admin.shipments.get.delivery-route') }}";
    var ROUTE_GET_DISTANCE_KM   = "{{ config('app.core') . '/helper/maps/distance' }}";
    var ROUTE_SYNC_RESET        = "";
    var ROUTE_GET_EXPENSE_PRICE = "";
    var SHIPMENT_EXISTS         = "0";
    var SHIPMENT_CALC_AUTO_KM   = "{{ Setting::get('shipments_km_calc_auto') }}";
    var SHIPMENT_KM_RETURN_BACK = "{{ Setting::get('shipments_km_return_back') }}";
    var VOLUMES_MESURE_UNITY    = "{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}"
    var STR_HASH_ID             = ""
    var IS_PICKUP               = "0"
    var VAT_PERCENT             = "{{ Setting::get('vat_rate_normal')/100 }}"
    var AUTOFILL_SENDER_INFO    = {{ Setting::get('shipments_sender_fields_empty') ? 0 : 1 }}
</script>

{!! Minify::javascript([
    '/vendor/jQuery/jquery-3.4.0.min.js',
    '/vendor/bootstrap/dist/js/bootstrap.min.js',
    '/vendor/datatables/jquery.dataTables.min.js',
    '/vendor/datatables/dataTables.bootstrap.min.js',
    '/vendor/pace/pace.min.js',
    '/vendor/bootstrap-growl/jquery.bootstrap-growl.min.js',
    //'/vendor/iCheck/icheck.min.js',
    '/vendor/slimScroll/jquery.slimscroll.min.js',
    '/vendor/select2/dist/js/select2.js',
    '/vendor/select2/dist/js/i18n/pt.js',
    '/vendor/select2-multiple/select2-multiple.js',
    '/vendor/magicsuggest/magicsuggest-min.js',
    '/vendor/datepicker/bootstrap-datepicker.js',
    '/vendor/datepicker/locales/bootstrap-datepicker.pt.js',
    '/vendor/bootbox/bootbox.js',
    '/vendor/jasny-bootstrap/js/fileinput.js',
    '/vendor/jquery-ujs/src/rails.js',
    '/vendor/moment/moment.min.js',
    '/vendor/push.js/bin/push.js',
    '/vendor/pusher/pusher.min.js',
    '/vendor/devbridge-autocomplete/dist/jquery.autocomplete.js',
/*    '/vendor/intl-tel-input/build/js/utils.js',
    '/vendor/intl-tel-input/build/js/intlTelInput-jquery.js',*/
    '/vendor/jquery.nicescroll-master/jquery.nicescroll.js',
    '/vendor/jquery-mask-plugin/dist/jquery.mask.js',
    '/vendor/jsvat/jsvat.js',

    '/assets/admin/js/template.min.js',
    '/assets/admin/js/helper.js',
    '/assets/admin/js/validator.js',
    '/assets/admin/js/main.js',

    //load json files
    '/assets/admin/json/zipcodes-regex.js',
    '/assets/admin/js/shipments.js'

    ])->withFullUrl()
!!}



@yield('plugins')

<script>
    $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');

    $(document).ready(function () {
        var $option = $('#sidebar-{{ @$sidebarActiveOption  }}');
        $option.addClass('active');
        $option.closest('.treeview').addClass('active');

        $('.helpcenter-search').on('submit', function(e){
            setTimeout(function() {
                $('.helpcenter-search [name="search"]').val('');
                console.log(1111);
            }, 1);
        })
    })

    $(".select2").select2(Init.select2())

    //inicializa modal shipments
    $(document).ajaxSuccess(function(event, jqXHR, settings) {

        url         = settings.url;
        url         = Url.removeQueryString(url);
        urlParts    = url.split('/');
        partsLength = urlParts.length;

        isCreateShipment = (urlParts[partsLength-1] == 'create' && urlParts[partsLength-3]+urlParts[partsLength-2] == 'adminshipments');
        isEditShipment   = (urlParts[partsLength-1] == 'edit' && urlParts[partsLength-4]+urlParts[partsLength-3] == 'adminshipments');
        isReturnShipment   = (urlParts[partsLength-1] == 'return' && urlParts[partsLength-4]+urlParts[partsLength-3] == 'adminshipments');
        isCreatePickup   = (urlParts[partsLength-1] == 'create' && urlParts[partsLength-3]+urlParts[partsLength-2] == 'adminpickups')
        isEditPickup     = ((urlParts[partsLength-1] == 'edit' || urlParts[partsLength-1] == 'shipment') && urlParts[partsLength-4]+urlParts[partsLength-3] == 'adminpickups');

        if (settings.type == 'GET' && (isCreateShipment || isEditShipment || isCreatePickup || isEditPickup || isReturnShipment)) {
            initShipmentScripts();
        }
    });


    @if(Auth::user()->isAdmin())
    $('[name="app_switcher"]').on('change', function () {
        var target = $(this).val();
        var url  = $(this).data('base-url');
        var url  = url+"/" + target;
        var win  = window.open(url, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Popups bloqueados para esta plataforma.');
        }
    })
    @endif

    @if(!Auth::user()->hasRole(config('permissions.role.admin')) && (isset($license) && @$license))
    $('#licence-expired').modal({
        backdrop: 'static',
        keyboard: false
    })
    @endif

    @if(isset($enovoPayments) && @$enovoPayments->content)
    $('#enovo-pending-payments').modal({
        backdrop: 'static',
        keyboard: false
    })
    @endif

    // Enable pusher logging - don't include this in production
    //Pusher.logToConsole = true;
    @if(Setting::get('notification_push_messages') && config('app.env') != 'local')
        var pusher = new Pusher('{{ env('PUSHER_KEY') }}', {
            cluster: '{{ env('PUSHER_CLUSTER') }}',
            encrypted: '{{ env('PUSHER_ENCRYPTION') }}'
        });

        var channelGlobal = pusher.subscribe('channel-{{ config('app.source') }}');
        channelGlobal.bind('notifications-event', function(data) {
            if(data.total) {
                Notifier.set(data.total, "{{ Setting::get('notification_sound') }}");
            } else {
                Notifier.increment(true)
            }

            if(data.message) {
                Notifier.pushAlert(data.title, data.message);
            }
        });

        @if(@$pusherChannel)
        var channel = pusher.subscribe('{{ @$pusherChannel }}');
        channel.bind('notifications-event', function(data) {
            if(data.total) {
                Notifier.set(data.total, {{ Setting::get('notification_sound') }});
            } else {
                Notifier.increment(true)
            }

            if(data.message) {
                Notifier.pushAlert(data.title, data.message);
            }
        });
        @endif
    @endif


    /**
     *  Scheduled tasks
     */
    @if(@$scheduledNotifications)
        var alerts = [
            @foreach($scheduledNotifications as $notification)
            moment("{{ $notification->alert_at }}"),
            @endforeach
        ];

        var alertsTitles = [
            @foreach($scheduledNotifications as $notification)
            "{{ $notification->message }}",
            @endforeach
        ];
    @endif

    var notifierScript
    notifierScript = window.setInterval(function(){

        $.each(alerts, function(key, value){
            diff = parseInt(moment().diff(value, 'seconds'));
            //console.log(diff)
            if(diff >= 0) {//evento passado
                alerts = alerts.slice(key + 1);
                alertsTitles = alertsTitles.slice(key + 1);
                Notifier.increment();
                Notifier.soundAlert();
                Notifier.pushAlert('NOTIFICAÇÃO', 'teste');
            }
        });

        if(alerts.length <= 0) {
            window.clearInterval(notifierScript);
        }
    }, 3000);

    /**
     * Idle timer
     * @type {number}
     */
    var idleTime = 0;
    $(document).ready(function () {
        //Increment the idle time counter every minute.
        var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

        //Zero the idle timer on mouse movement.
        $(this).mousemove(function (e) {idleTime = 0; });
        $(this).keypress(function (e) {idleTime = 0; });
    });

    function timerIncrement() {
        idleTime = idleTime + 1;
        if (idleTime > 25) { // 25 minutes
            window.location.reload();
        }
    }
</script>
@yield('scripts')