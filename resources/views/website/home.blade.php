@section('metatags')
    <meta name="description" content="{{ trans('website.seo.home.description') }}">
    <meta property="og:title" content="2660 Express - Logistica e Distribuição">
    <meta property="og:description" content="{{ trans('website.seo.home.description') }}">
    <meta property="og:image" content="{{ trans('website.seo.image.url') }}">
    <meta property="og:image:width" content="{{ trans('website.seo.image.width') }}">
    <meta property="og:image:height" content="{{ trans('website.seo.image.height') }}">
    <meta name="description" content="{{ trans('website.seo.home.description') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="2660 Express - Logistica e Distribuição">
    <meta name="reply-to" content="geral@2660express.pt">
    <meta name="keywords" content="logistica, distribuicao">
@stop

@section('content')
<section class="home-page">
    <div id="slider" class="carousel carousel-fade slide" data-ride="carousel" style="position: relative;">  
        <div class="carousel-inner" role="listbox">
            <div class="text-carousel col-12 col-md-12 col-xl-8">
                <h2 class="text-green">
                        A cada entrega, <br> solidificamos a confiança. 
                </h2> 
                {{-- tracking --}}
                <div id="follow" >
                    <div id="pointer">
                        <div class="container">
                            <div class="follow-text">
                                <form method="GET" action="{{ route('trk.index') }}" accept-charset="UTF-8">
                                    <h3 class="green-tracking">Seguir Envio</h3>
                                    <div class="tracking-class">
                                        <input type="text" class="from-control form" value="{{ @$tracking }}" placeholder="Número de envio: VD0000000000" name="tracking">
                                        <button class="btn btn-follow">SEGUIR ENVIO ></button>
                                    </div>
                                    @if(!empty(@$tracking) && empty(@$shipmentsResults))
                                    <p class="feedback"><i class="fas fa-exclamation-circle"></i> Nenhum serviço encontrado com o código indicado.</p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="under"> 
                    </div>
                </div>
            </div>
            @foreach ($sliders as $key => $slide)
                <div id="img_carousel" class="item imgcarousel {{ $key == 0 ? 'active' : '' }} " style="background-image: url({{ asset($slide->filepath) }}); background-size: cover; background-repeat: no-repeat;">
                </div>
            @endforeach
            </div>
        </div>
    </div>
    <div class="justify-content-center row before2-rectangle">  
        <div class="justify-content-center col-12 col-xl-2 before-rectangle">
            <div class="rectangle rec1 col-12 col-md-12 col-sm-12">
            <div class="vertical-line"></div>
                <div>
                    <div class="col-12 col-xs-12 col-sm-12 col-md-12  values-class">
                        <div>
                            <svg class="icones-rectangle" width="100" height="60" viewBox="0 0 67 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M63.9413 69.3004C63.9413 69.4661 63.8189 69.6004 63.6678 69.6004H33.3047V72.0004H63.6678C65.0267 71.9986 66.128 70.7908 66.1297 69.3004V26.4004H63.9413V69.3004Z" />
                                <path d="M63.6675 0H23.7304C22.3715 0.0018 21.2702 1.2096 21.2686 2.7V24H23.4569V2.7C23.4569 2.53425 23.5793 2.4 23.7304 2.4H63.6675C63.8186 2.4 63.941 2.53425 63.941 2.7V24H66.1294V2.7C66.1277 1.2096 65.0264 0.0018 63.6675 0Z"/>
                                <path d="M59.884 29.1514L52.9986 36.703L50.4898 33.9514L48.9424 35.6485L52.2249 39.2485C52.6522 39.7171 53.3449 39.7171 53.7723 39.2485L61.4315 30.8485L59.884 29.1514Z" />
                                <path d="M45.3398 43.1998C45.3398 43.8625 45.8298 44.3998 46.434 44.3998H57.3757V41.9998H47.5282V31.1998H55.1873V28.7998H46.434C45.8298 28.7998 45.3398 29.3371 45.3398 29.9998V43.1998Z" />
                                <path d="M50.4898 54.3518L48.9424 56.0489L52.2249 59.6489C52.6522 60.1175 53.3449 60.1175 53.7723 59.6489L61.4315 51.2489L59.884 49.5518L52.9986 57.1034L50.4898 54.3518Z" />
                                <path d="M45.3398 63.6002C45.3398 64.2629 45.8298 64.8002 46.434 64.8002H57.3757V62.4002H47.5282V51.6002H55.1873V49.2002H46.434C45.8298 49.2002 45.3398 49.7375 45.3398 50.4002V63.6002Z" />
                                <path d="M59.884 7.55176L52.9986 15.1034L50.4898 12.3518L48.9424 14.0489L52.2249 17.6489C52.6522 18.1175 53.3449 18.1175 53.7723 17.6489L61.4315 9.24886L59.884 7.55176Z" />
                                <path d="M45.3398 8.40019V21.6002C45.3398 22.2629 45.8298 22.8002 46.434 22.8002H57.3757V20.4002H47.5282V9.60019H55.1873V7.2002H46.434C45.8298 7.2002 45.3398 7.7375 45.3398 8.40019Z" />
                                <path d="M30.7955 12.3518L29.248 14.0489L32.5305 17.6489C32.9578 18.1175 33.6506 18.1175 34.078 17.6489L41.7371 9.24886L40.1897 7.55176L33.3043 15.1034L30.7955 12.3518Z" "/>
                                <path d="M25.6455 8.40019V21.6002C25.6455 22.2629 26.1354 22.8002 26.7397 22.8002H37.6813V20.4002H27.8338V9.60019H35.493V7.2002H26.7397C26.1354 7.2002 25.6455 7.7375 25.6455 8.40019Z" />
                                <path d="M42.0578 49.2004C42.0578 36.6284 32.7318 26.4004 21.2687 26.4004C9.80548 26.4004 0.479492 36.6284 0.479492 49.2004H2.66782C2.66782 37.9519 11.0121 28.8004 21.2687 28.8004C31.5252 28.8004 39.8695 37.9519 39.8695 49.2004C39.8695 60.4489 31.5252 69.6004 21.2687 69.6004C11.8003 69.5869 3.84706 61.7863 2.78189 51.4679L0.607236 51.7328C1.79577 63.2665 10.6853 71.9866 21.2687 72.0004C32.7318 72.0004 42.0578 61.7723 42.0578 49.2004Z" />
                                <path d="M20.1748 51.0229L15.1143 55.4628L16.482 57.3369L21.9528 52.5369C22.2122 52.3091 22.3632 51.9642 22.3631 51.5996V39.5996H20.1748V51.0229Z" />
                                <path d="M22.3629 33.646C23.3695 33.73 24.3649 33.9321 25.3323 34.2486L25.9585 31.9488C17.2575 29.0923 8.09251 34.5124 5.48798 44.055C2.88346 53.5975 7.82553 63.649 16.5265 66.5054C25.2274 69.3619 34.3924 63.9418 36.997 54.3993C39.3241 45.8734 35.6328 36.7605 28.3042 32.9388L27.3632 35.1055C28.5009 35.7001 29.5611 36.4588 30.5158 37.3612L29.0057 39.0174L30.553 40.7146L32.0599 39.0625C34.0211 41.5752 35.2077 44.7034 35.4501 48.0003H33.3046V50.4003H35.4519C35.2215 53.7013 34.0355 56.8352 32.0668 59.345L30.5537 57.6854L29.0064 59.3827L30.5194 61.0421C28.2309 63.2015 25.3731 64.5025 22.3629 64.7552V62.4002H20.1746V64.7552C17.1648 64.5026 14.3072 63.2018 12.0188 61.0429L13.5317 59.3834L11.9845 57.6862L10.4714 59.345C8.50269 56.8352 7.31661 53.7013 7.08629 50.4003H9.2329V48.0003H7.08629C7.31661 44.6992 8.50269 41.5653 10.4714 39.0555L11.9845 40.7151L13.5317 39.0178L12.0188 37.3584C14.3072 35.1994 17.1648 33.8986 20.1746 33.646V36.0003H22.3629V33.646Z" />
                            </svg>

                        </div>
                        {{-- <img src="{{ asset('assets/website/img/eficiencia.svg') }}" style="width:45%; margin-bottom:15px;" class="icon" alt="eficiencia">  --}}
                        <h2 class="text-rectangle">Eficiência</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-center col-12 col-xl-2 before-rectangle">
            <div class="rectangle  col-12 col-md-12 col-sm-12" style="box-shadow: 20px 4px 13px 0px rgb(0 0 0 / 5%);">
            <div class="vertical-line"></div>
                <div>
                    <div class="col-12 col-xs-12 col-sm-12 col-md-12  values-class">
                        <svg class="icones-rectangle" width="100" height="60" viewBox="0 0 74 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.31151 24.9291L8.12497 27.4444C8.89317 27.7766 9.73074 27.6011 10.3298 27.0505L14.9336 30.2347L15.7231 30.8435C14.7216 32.5819 14.9782 34.9575 16.501 36.3736C17.3377 37.1517 18.401 37.4838 19.4417 37.3594C19.5029 38.5592 20.0004 39.6278 20.81 40.3805C21.6057 41.1205 22.6444 41.4994 23.7506 41.367C23.8121 42.5664 24.3093 43.6347 25.119 44.3875C25.9221 45.1344 26.9628 45.5053 28.0614 45.3739C28.1188 46.5037 28.5809 47.6067 29.428 48.3945C31.0644 49.9165 33.5261 49.6958 34.9136 47.9012L35.8374 46.712L37.3942 47.972C40.0857 50.1509 43.8899 48.3659 44.2408 44.7442C47.0158 45.8501 49.9624 43.7172 50.0051 40.4416C52.6209 40.9809 54.942 38.8973 55.0877 36.1336C58.4806 37.1275 61.5495 33.3672 60.0513 29.6903L64.2114 26.7599C64.8071 27.5338 65.797 27.8261 66.6796 27.4442L72.493 24.9291C73.612 24.445 74.1635 23.0527 73.722 21.8252L71.0649 14.4386C70.8484 13.8365 70.2276 13.5411 69.6788 13.7786C69.1298 14.0161 68.8604 14.6968 69.0769 15.2988L71.7338 22.6852C71.7429 22.7103 71.7317 22.7389 71.7087 22.7489L65.8953 25.2641C65.8722 25.2739 65.8464 25.2617 65.8373 25.2366C65.6337 24.6707 59.1085 6.53006 58.5315 4.926C58.5293 4.92038 58.5246 4.90725 58.532 4.88866C58.5395 4.87007 58.5516 4.86491 58.5565 4.86257L64.3702 2.34726C64.3751 2.34492 64.387 2.33976 64.4044 2.34789C64.4212 2.35601 64.4259 2.36914 64.428 2.37476L67.2448 10.2051C67.4615 10.8072 68.082 11.1026 68.6308 10.8651C69.1799 10.6276 69.4493 9.94693 69.2328 9.3449L66.416 1.51476C65.9748 0.287577 64.7051 -0.317109 63.5861 0.167109L57.7724 2.68242C56.7337 3.13179 56.1853 4.35429 56.464 5.51319L53.3474 6.77475C52.3777 7.16741 51.3248 7.28131 50.303 7.10366L47.1527 6.556C46.57 6.45491 46.0224 6.891 45.9302 7.52991C45.8379 8.16912 46.2354 8.76959 46.8182 8.87084L49.9685 9.4185C51.3509 9.65865 52.775 9.50475 54.0876 8.97334L57.2323 7.70022C57.5737 8.64928 62.9603 23.6239 63.3137 24.6066L58.7285 27.8364L58.6507 27.7735C58.6504 27.7731 58.65 27.7728 58.6497 27.7727C58.1585 27.375 50.1858 20.9222 49.8116 20.6193C49.5826 20.4338 49.2979 20.3505 48.9705 20.4114C44.544 21.2369 40.5781 19.5107 37.6604 16.1696C36.7526 15.13 35.2715 15.0743 34.288 16.0427C31.4576 18.8307 27.5022 21.5493 24.7348 18.6647C24.4598 18.3783 24.4578 17.8493 24.8754 17.5536C28.4006 15.0666 32.0526 11.5746 34.9914 8.31397C35.7228 7.50225 36.7748 7.12491 37.8073 7.30444L41.8633 8.00959C42.4473 8.111 42.9935 7.67475 43.0858 7.03569C43.1781 6.39647 42.7806 5.79601 42.1978 5.69476L38.1418 4.9896C36.4316 4.69241 34.686 5.31929 33.4717 6.66647C33.1318 7.0435 32.7297 7.48131 32.2764 7.96147C30.9081 7.42944 29.482 7.11163 28.0314 7.02225C23.7852 6.76069 23.4169 6.71131 22.4527 6.77928C21.6959 6.83225 20.926 6.69116 20.2257 6.36991L18.3426 5.50569C18.6144 4.35351 18.0669 3.1296 17.0323 2.6821L11.2188 0.167109C10.0995 -0.317265 8.83005 0.287421 8.38868 1.51476L1.08271 21.8252C0.640344 23.0552 1.18956 24.4438 2.31151 24.9291ZM20.3584 34.3624C19.7322 35.1724 18.622 35.2728 17.8831 34.5859C17.1394 33.8944 17.0551 32.6788 17.6778 31.8733L20.3208 28.4711C20.9321 27.68 22.0454 27.5497 22.796 28.2475C23.5363 28.9358 23.6278 30.1502 22.9998 30.9622C22.9995 30.9625 22.9993 30.9628 22.9991 30.9631L20.3584 34.3624ZM24.6672 38.3695C24.0396 39.1813 22.9328 39.2816 22.1921 38.593C21.4484 37.9014 21.3641 36.6858 21.9868 35.8803L24.6298 32.4781C25.2861 31.6288 26.5027 31.5545 27.2351 32.3886C27.2399 32.3941 27.2441 32.4002 27.2489 32.4058C27.8521 33.1094 27.8915 34.2156 27.3088 34.9694L24.6672 38.3695ZM28.9762 42.3764C28.3464 43.1912 27.2384 43.2855 26.5011 42.5998C25.7574 41.9084 25.6729 40.6928 26.2957 39.8873L28.9387 36.485C29.5666 35.6731 30.681 35.5794 31.414 36.2616C32.1526 36.9486 32.2439 38.1663 31.6177 38.9762C31.6175 38.9766 31.6173 38.9769 31.617 38.9772L28.9762 42.3764ZM35.9411 42.9627C35.9367 42.9686 35.9328 42.9752 35.9283 42.9809C35.502 43.5297 34.3274 45.0417 33.2852 46.3833C32.659 47.1934 31.5488 47.2936 30.8101 46.6069C30.0714 45.92 29.98 44.7023 30.6046 43.8944L33.2477 40.492C33.8774 39.6773 34.9864 39.5834 35.7228 40.2686C36.4474 40.942 36.5584 42.1439 35.9411 42.9627ZM17.5747 7.69444L19.4007 8.5324C20.4028 8.99225 21.5055 9.1949 22.5893 9.1185C23.4065 9.06084 23.6793 9.10178 27.9111 9.36256C28.78 9.41615 29.6391 9.56365 30.4781 9.80303C28.3874 11.8736 26.0395 13.9491 23.6877 15.6054C22.18 16.6669 21.9566 19.0032 23.2626 20.3644C25.0476 22.2246 29.0395 24.363 35.7159 17.7869C35.8355 17.6691 36.0116 17.6707 36.1166 17.7911C39.3507 21.4933 43.9348 23.5613 48.8852 22.7983C49.4461 23.2307 48.36 22.3578 57.3803 29.6589C58.3457 30.4402 58.6158 31.9767 57.86 33.0372C57.1421 34.0445 55.8449 34.2397 54.9076 33.4808C53.6414 32.4558 54.5762 33.2359 50.1248 29.4742C49.6569 29.0791 48.9863 29.1745 48.626 29.6872C48.266 30.1999 48.3529 30.9356 48.8202 31.3308L52.5134 34.4517C53.1497 35.3711 53.0739 36.6997 52.3207 37.5238C51.5801 38.3342 50.3611 38.4108 49.4851 37.702C48.6381 37.0166 49.3418 37.6039 45.4572 34.3239C44.9896 33.9289 44.3186 34.0249 43.9587 34.5377C43.5987 35.0505 43.6859 35.7863 44.1535 36.1811L47.4283 38.9462C48.0467 39.8236 48.0164 41.0855 47.3411 41.9252C46.6145 42.8286 45.3271 42.9544 44.4099 42.2119C42.9361 41.0191 43.6758 41.635 40.8375 39.2258C40.3708 38.8297 39.6997 38.9234 39.3384 39.4353C38.9773 39.9472 39.0628 40.6831 39.5295 41.0792L41.7997 43.0062C42.3162 43.9076 42.215 45.0883 41.5359 45.8673C40.7946 46.7178 39.5596 46.812 38.6628 46.0862L37.2265 44.9237L37.5564 44.4989C38.2144 43.6483 38.554 42.5466 38.4671 41.3873C38.3788 40.2131 37.87 39.1928 37.1048 38.4809C36.3088 37.7408 35.2692 37.3616 34.1634 37.4931C34.1052 36.3609 33.6318 35.2517 32.7958 34.4739C32.0028 33.7366 30.9961 33.3822 29.9585 33.4752C29.9239 33.4783 29.8893 33.4819 29.855 33.4861C29.853 33.4486 29.8518 33.4111 29.849 33.3734C29.781 32.4674 29.459 31.6281 28.9298 30.9511C28.9265 30.9469 28.9235 30.9427 28.9202 30.9385C28.0782 29.8702 26.8248 29.3305 25.5473 29.4811C25.4871 28.3044 25.0007 27.2253 24.1778 26.46C22.5402 24.9374 20.0787 25.1596 18.6921 26.9533L17.0817 29.0263L16.1404 28.3006C16.1259 28.2894 16.1111 28.2785 16.096 28.2681L11.3538 24.988C14.939 15.0213 13.5961 18.7546 17.5747 7.69444ZM3.07059 22.6855L10.3764 2.37507C10.3834 2.3557 10.4002 2.34398 10.418 2.34398C10.4234 2.34398 10.429 2.34507 10.4344 2.34742L16.2479 4.86272C16.2708 4.87257 16.2821 4.90116 16.2731 4.92616C15.9216 5.90335 9.19705 24.5975 8.96711 25.2367C8.9577 25.2622 8.93263 25.2742 8.90926 25.2642L3.0958 22.7489C3.07272 22.7389 3.06133 22.7107 3.07059 22.6855Z" />
                        </svg>
                        <h2 class="text-rectangle">Confiança</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-center col-12 col-xl-2 before-rectangle">
            <div class="rectangle  col-12 col-md-12 col-sm-12" style="box-shadow: 20px 4px 13px 0px rgb(0 0 0 / 5%);">
                <div class="vertical-line"></div>
                <div>
                    <div class="col-12 col-xs-12 col-sm-12 col-md-12  values-class">
                        <svg class="icones-rectangle1" width="100" height="60" viewBox="0 0 65 76" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M59.6181 35.3662C57.5954 35.3662 55.6649 37.1657 55.328 39.3653L53.5493 50.9801L45.9522 53.9251L48.9613 49.8495C50.3645 47.9491 50.1319 45.1508 48.4179 43.5654C46.7964 42.0655 44.387 42.2359 42.8798 43.8744L39.7038 47.3267C36.7182 50.4586 35.3544 54.8842 35.125 59.3439V70.7767C35.125 72.5572 36.4338 74.0004 38.0482 74.0004H46.2302C48.0288 74.0004 49.4009 72.226 49.1167 70.2672L48.9502 69.1195C48.718 67.5193 49.3727 65.9208 50.6143 65.0541C53.6105 62.9626 56.5377 60.8097 58.5814 59.2834C60.3555 57.9584 61.5115 55.8423 61.7456 53.4936C62.126 49.6779 62.7206 43.7144 63.1228 39.6789C63.3595 37.3067 61.7823 35.3662 59.6181 35.3662Z"  stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.1095 30.7443L11.9985 32.5167C10.7554 33.2249 9.30252 32.0811 9.53989 30.581L10.134 26.8267L7.61707 24.168C6.61136 23.1056 7.16638 21.2546 8.5562 21.0358L12.0346 20.4881L13.5901 17.0724C14.2117 15.7076 16.0075 15.7076 16.6291 17.0724L18.1847 20.4881L21.663 21.0358C23.0528 21.2546 23.6079 23.1056 22.6022 24.168L20.0852 26.8267L20.6793 30.581C20.9167 32.0811 19.4639 33.2251 18.2208 32.5167L15.1095 30.7443Z"  stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M49.5355 30.7443L46.4242 32.5167C45.1811 33.2249 43.7283 32.0811 43.9657 30.581L44.5598 26.8267L42.0429 24.168C41.0371 23.1056 41.5922 21.2546 42.982 21.0358L46.4603 20.4881L48.0159 17.0724C48.6375 15.7076 50.4333 15.7076 51.0549 17.0724L52.6104 20.4881L56.0888 21.0358C57.4786 21.2546 58.0337 23.1056 57.0279 24.168L54.511 26.8267L55.1051 30.581C55.3425 32.0811 53.8896 33.2251 52.6465 32.5167L49.5355 30.7443Z"  stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M32.3235 16.6955L29.368 18.4679C28.187 19.1761 26.8068 18.0322 27.0323 16.5322L27.5968 12.7779L25.2057 10.1191C24.2502 9.0568 24.7775 7.20582 26.0978 6.98695L29.4023 6.43926L30.88 3.02359C31.4706 1.6588 33.1766 1.6588 33.7671 3.02359L35.2449 6.43926L38.5493 6.98695C39.8696 7.20582 40.3969 9.0568 39.4415 10.1191L37.0504 12.7779L37.6148 16.5322C37.8403 18.0322 36.4601 19.1762 35.2792 18.4679L32.3235 16.6955Z"  stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M34.5414 42.3903H36.4091C37.7093 42.3903 38.6422 40.9757 38.2298 39.605C37.6394 37.6424 36.4429 36.0519 34.9261 35.1907C36.8002 34.134 38.0852 31.9732 38.0852 29.4775C38.0852 25.9405 35.5058 23.0732 32.3239 23.0732C29.142 23.0732 26.5626 25.9405 26.5626 29.4775C26.5626 31.9732 27.8477 34.1339 29.7217 35.1907C28.2049 36.0519 27.0086 37.6424 26.418 39.605C26.0056 40.9757 26.9384 42.3903 28.2387 42.3903H30.1067"  stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2.52958 49.7799L2.89973 53.4931C3.13387 55.8418 4.28997 57.9584 6.06405 59.2834C8.10779 60.8097 11.0349 62.9626 14.0312 65.0541C15.2728 65.9208 15.9274 67.5193 15.6953 69.1195L15.5287 70.2672C15.2446 72.226 16.6166 74.0004 18.4153 74.0004H26.5972C28.2117 74.0004 29.5204 72.557 29.5204 70.7767V59.3439C29.291 54.8842 27.9272 50.4585 24.9416 47.3267L21.7656 43.8744C20.2584 42.2361 17.849 42.0656 16.2275 43.5654C14.5135 45.1508 14.281 47.9489 15.6841 49.8495L18.6932 53.9251L11.0961 50.9801L9.31743 39.3653C8.98055 37.1659 7.05007 35.3662 5.02739 35.3662C2.86316 35.3662 1.286 37.3073 1.52251 39.6796L2.02617 44.7314" stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2 class="text-rectangle">Compromisso</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-center col-12 col-xl-2 before-rectangle">
            <div class="rectangle  col-12 col-md-12 col-sm-12" style="box-shadow: 20px 4px 13px 0px rgb(0 0 0 / 5%);">
            <div class="vertical-line"></div>
                <div>
                    <div class="col-12 col-xs-12 col-sm-12 col-md-12  values-class">
                        <svg class="icones-rectangle" width="100" height="60" viewBox="0 0 49 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M47.2153 32.6769H43.2646V18.3396C43.2646 12.9714 40.8843 7.88653 36.7337 4.38891C36.2023 3.9413 35.4037 4.00331 34.9503 4.5277C34.4968 5.05209 34.5596 5.8403 35.0909 6.28805C38.6776 9.3105 40.7345 13.7032 40.7345 18.3395V32.6768H36.9585V18.3396C36.9585 11.6592 31.4529 6.2242 24.6859 6.2242C17.9179 6.2242 12.4117 11.6592 12.4117 18.3396V29.971C12.4117 30.6606 12.978 31.2196 13.6767 31.2196C14.3754 31.2196 14.9417 30.6606 14.9417 29.971V18.3396C14.9417 13.0362 19.3129 8.72142 24.6859 8.72142C30.058 8.72142 34.4285 13.0361 34.4285 18.3396V32.6769H8.6357V18.3396C8.6357 9.60398 15.8358 2.49708 24.686 2.49708C27.1294 2.49708 29.4763 3.02752 31.6618 4.07348C32.2911 4.37484 33.0472 4.11525 33.352 3.49481C33.6569 2.87437 33.3944 2.12752 32.7658 1.82644C30.2334 0.614531 27.5151 0 24.6862 0C14.4409 0 6.10587 8.22713 6.10587 18.3398V32.677H2.15561C1.45694 32.677 0.890625 33.236 0.890625 33.9256V57.4381C0.890625 58.1278 1.45694 58.6868 2.15561 58.6868C2.85428 58.6868 3.4206 58.1278 3.4206 57.4381V35.1741H45.9502V69.5028H3.4206V61.7605C3.4206 61.0709 2.85428 60.5119 2.15561 60.5119C1.45694 60.5119 0.890625 61.0709 0.890625 61.7605V70.7514C0.890625 71.441 1.45694 72 2.15561 72H47.2153C47.9141 72 48.4803 71.441 48.4803 70.7514V33.9255C48.4803 33.2359 47.9141 32.6769 47.2153 32.6769Z" />
                            <path d="M28.5003 53.9621C30.6544 52.6668 31.9931 50.3604 31.9931 47.8081C31.9931 43.8306 28.7145 40.5947 24.6849 40.5947C20.6553 40.5947 17.377 43.8306 17.377 47.8081C17.377 50.3604 18.7156 52.667 20.8699 53.9621L17.9498 62.4314C17.8185 62.8126 17.8813 63.2332 18.1188 63.5606C18.3563 63.8881 18.7392 64.0823 19.1473 64.0823H30.2229C30.6309 64.0823 31.0137 63.888 31.2514 63.5606C31.489 63.2331 31.552 62.8126 31.4203 62.4314L28.5003 53.9621ZM20.914 61.5851L23.6188 53.7408C23.8351 53.1139 23.5177 52.4294 22.8957 52.181C21.0802 51.456 19.9071 49.7397 19.9071 47.8079C19.9071 45.2075 22.0505 43.0918 24.6851 43.0918C27.3198 43.0918 29.4632 45.2075 29.4632 47.8079C29.4632 49.7396 28.2901 51.456 26.4746 52.181C25.8526 52.4293 25.5353 53.1137 25.7516 53.7408L28.4562 61.5851H20.914Z" />
                        </svg>
                        <h2 class="text-rectangle">Segurança</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-center col-12 col-xl-2 before-rectangle" >
            <div class="rectangle rec2 col-12 col-md-12 col-sm-12">
                <div>
                    <div class="col-12 col-xs-12 col-sm-12 col-md-12  values-class">
                        <svg class="icones-rectangle" width="100" height="60"viewBox="0 0 67 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M33.784 72C15.6839 72 0.958984 55.8508 0.958984 36C0.958984 16.1492 15.6839 0 33.784 0C39.9155 0 45.8962 1.86625 51.0806 5.39784C51.9866 6.01446 52.2643 7.32023 51.702 8.31407C51.1388 9.30776 49.9482 9.61449 49.043 8.99566C44.4712 5.88088 39.1945 4.23541 33.784 4.23541C17.8135 4.23541 4.82072 18.4854 4.82072 36.0002C4.82072 53.5149 17.8135 67.7647 33.784 67.7647C49.7541 67.7647 62.7473 53.5147 62.7473 36C62.7473 32.4795 62.2263 29.0228 61.1986 25.7262C60.8537 24.6199 61.3927 23.4155 62.4015 23.0372C63.4122 22.6611 64.5083 23.2501 64.8533 24.3564C66.0179 28.0953 66.6092 32.013 66.6092 36.0002C66.609 55.8508 51.8841 72 33.784 72Z"/>
                            <path d="M33.7842 48.2138C33.2944 48.2138 32.8208 48.0098 32.4623 47.6392L17.0152 31.7249C16.2379 30.9243 16.1996 29.5841 16.9298 28.7316C17.66 27.8801 18.8818 27.836 19.6592 28.639L33.6204 43.0224L58.9893 9.9937C59.6752 9.09921 60.894 8.98456 61.7096 9.73885C62.5252 10.4922 62.6297 11.8278 61.942 12.7223L35.2605 47.4606C34.9196 47.905 34.4258 48.1764 33.8968 48.2107C33.8597 48.2127 33.8215 48.2138 33.7842 48.2138Z" />
                        </svg>
                        <h2 class="text-rectangle">Simplicidade</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</section>
<section class="sobre">
    <div class="col-12 col-sm-12 col-md-7 col-xl-7 class-imagea">
        <img src="{{ asset('assets/website/img/sobre.png') }}" alt="sobre-nos" style="width:100%;">
    </div>
    <div class="col-12 col-sm-12 col-md-5 col-xl-5 class-recabout">
        <div class="rectangle-about">
            <h4 class="about-home">SOBRE NÓS</h4>
            <h2 class="about2-home">A Viagem Cativante da 2660Express</h2>
            <p class="textabout-home">2660 Express é uma empresa fundada em 2021 por profissionais com mais de 20 anos de experiência no setor da logística e distribuição.
            Desde 2010, focamos o nosso negócio na área de ecommerce e vendas online, prestando serviços a nível nacional e internacional.
            <br>A 2660 Express é especialista na Logística, Embalamento, Etiquetagem e distribuição para os produtos de E-Commerce para clientes B2B e B2C.
            </p>
        </div>
        {{-- <img src="{{ asset('assets/website/img/sobre.png') }}" alt="sobre-nos"> --}}
    </div>
</section>
<section class="services-home">
    <div>
        <div class="col-12 col-sm-12 col-md-12 col-xl-4">
            <h4 class="servicestitle-home">SERVIÇOS</h4>
            <h2 class="servecessubtitle-home">Descubra a Excelência <br> dos Nossos Serviços de Transporte <br>  
            </h2>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-xl-4">
            <div class="section-sobre" style="background:url(/assets/website/img/services-bg/armazenagem.svg); background-size: contain; min-height: 300px; background-repeat: no-repeat;">
                <div class="class-texto2">
                    <h4 class="text-arma">Armazenagem</h4>
                    <p class="texte-armaze-1">Oferecemos soluções de armazenagem adaptadas às suas necessidades. 
                    Garantimos um controlo rigoroso na segurança dos seus envios. Conte com a nossa equipa especializada.
                    Descubra o profissionalismo e segurança dos nossos serviços de armazenagem.</p>
                </div>  
                <button class="btn btn-services">
                    <a  class="nav-link">VER MAIS ></a>
                </button>
            </div>          
        </div>
        <div class=" col-12 col-sm-12 col-md-4 col-xl-4">
            <div class="section-sobre" style="background:url(/assets/website/img/services-bg/embalamento.svg); background-size: contain; min-height: 300px; background-repeat: no-repeat;">
                <div class="class-texto2">
                    <h4 class="text-arma">Embalamento e etiquetagem</h4>
                    <p class="texte-armaze-1">
                    Oferecemos soluções de armazenagem adaptadas às suas necessidades. 
                    Garantimos um controlo rigoroso na segurança dos seus envios. Conte com a nossa equipa especializada.
                    Descubra o profissionalismo e segurança dos nossos serviços de armazenagem.</p>
                </div> 
                <button class="btn btn-services">
                    <a  class="nav-link">VER MAIS ></a>
                </button> 
            </div>
        </div>
        
        <div class="col-12 col-sm-12 col-md-4 col-xl-4" style="margin-top:50px">
            <div class="section-sobre" style="background:url(/assets/website/img/services-bg/distribuicao.svg); background-size: contain; min-height: 300px; background-repeat: no-repeat;">
                <div class="class-texto2">
                    <h4 class="text-arma">Distribuição</h4>
                    <p class="texte-armaze-1">Oferecemos soluções de armazenagem adaptadas às suas necessidades. 
                    Garantimos um controlo rigoroso na segurança dos seus envios. Conte com a nossa equipa especializada.
                    Descubra o profissionalismo e segurança dos nossos serviços de armazenagem.</p>
                </div> 
                <button class="btn btn-services">
                    <a  class="nav-link">VER MAIS ></a>
                </button> 
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-xl-4" style="margin-top:50px">
            <div class="section-sobre" style="background:url(/assets/website/img/services-bg/callcenter.svg); background-size: contain; min-height: 300px; background-repeat: no-repeat;">
                <div class="class-texto2">
                    <h4 class="text-arma">Call Center</h4>
                    <p class="texte-armaze-1">Oferecemos soluções de armazenagem adaptadas às suas necessidades. 
                    Garantimos um controlo rigoroso na segurança dos seus envios. Conte com a nossa equipa especializada.
                    Descubra o profissionalismo e segurança dos nossos serviços de armazenagem.</p>
                </div>  
                <button class="btn btn-services">
                    <a  class="nav-link">VER MAIS ></a>
                </button>
            </div>
        </div>
        <div class=" col-12 col-sm-12 col-md-4 col-xl-4" style="margin-top:50px">
            <div class="section-sobre" style="background:url(/assets/website/img/services-bg/ecommerce.svg); background-size: contain; min-height: 300px; background-repeat: no-repeat;">
                <div class="class-texto2">
                    <h4 class="text-arma">E-Commerce</h4>
                    <p class="texte-armaze-1">Oferecemos soluções de armazenagem adaptadas às suas necessidades. 
                    Garantimos um controlo rigoroso na segurança dos seus envios. Conte com a nossa equipa especializada.
                    Descubra o profissionalismo e segurança dos nossos serviços de armazenagem.</p>
                </div>  
                <button class="btn btn-services">
                    <a  class="nav-link">VER MAIS ></a>
                </button>
            </div>
        </div>
    </div>
</section>
<section class="recrutment-home">
    <div class="recrutamento10-home">
        <img src="{{ asset('assets/website/img/recrutamento-1.png') }}" alt="sobre-nos" >
        <div class="home2-recruitment">
            <h4 class="title-recrutamento">Recrutamento</h4>
            <h1 class="sub-recrutamento">Venha fazer parte da nossa equipa(.......<br>............)</h1>
            <button class="btn btn-recrutamento">
                <a  class="nav-link" style="font-weight: 400; color: #69B539 !important;">Saber Mais</a>
            </button>
        </div>
    </div>
</section>


@stop
@section ('styles')
    <style>
        .container {
            width: 100%;
            padding-right: 0px;
            padding-left: 0px;
            margin-right: auto;
            margin-left: auto;
        }
        .row {
            margin-right: 0px;
            margin-left: 0px;
        }
    </style>

@stop
