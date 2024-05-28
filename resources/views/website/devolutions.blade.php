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
<section class="topo-about" style="height: 380px; position:relative; background: url('/assets/website/img/topo-servicos.png'); background-size: cover; background-position: bottom 0px left 0px; display: flex; align-items: flex-end; display: flex; align-items: center;">
    <div class="col-sm-12 todos-topos">
        <h1 class="text-top-about">{{trans('website.services.devolutions.title')}}</h1>
        <div style="display: flex; align-items: center; margin-top:15px;">
            <p class="text-top2">HOME > </p>
            <p class="text-top2">&nbspServiços > </p>
            <p class="text-top2" style="text-decoration: underline;">&nbsp{{trans('website.services.devolutions.title')}}</p>
        </div>
    </div>
</section> 
<section class="sobre row" >
    <div class="col-12 col-sm-12 col-md-7 col-xl-7 class-imagea">
        <img src="{{ asset('assets/website/img/armazenagem1.png') }}" alt="sobre-nos" class="img2-about" style="width:100%;">
    </div>
    <div class="col-12 col-sm-12 col-md-5 col-xl-5 class-recabout">
        <div class="rectangleab-storage">
            <h4 class="about-home">{{trans('website.services.devolutions.title')}}</h4>
            <h2 class="about2-home">Otimizamos espaço para o seu sucesso</h2>
            <p class="textabout-home">{{trans('website.services.devolutions.longDescription')}} </p>
        </div>
        {{-- <img src="{{ asset('assets/website/img/sobre.png') }}" alt="sobre-nos"> --}}
    </div>
</section>
<section style="padding: 68px 84px 50px 84px;">
    <div class="row">
        <div class="col-12 col-xs-12 col-sm-12 col-md-8 col-xl-8" style="padding-left:0px;">
            <h4 class="about-home">{{trans('website.services.qualities.title')}}</h4>
            <h2 class="storage-text2">{{trans('website.services.qualities.subtitle')}}</h2>
            <div class="row" style="margin-top: 30px;">
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-xl-6" style="padding-left:0px;">
                    <div class="rectangle-storage">
                        <div class="icone-storage">
                            <svg class="img2-about"  width="65"  height="65" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1400_5347)">
                                <path d="M50.7065 7.56075C51.1041 6.99625 50.9687 6.2165 50.4042 5.819C44.9984 2.01212 38.6342 0 32 0C27.847 0 23.8059 0.78425 19.9887 2.33087C19.349 2.59012 19.0405 3.319 19.2997 3.95875C19.5589 4.5985 20.2874 4.90712 20.9276 4.64787C24.4451 3.22262 28.1702 2.5 32 2.5C38.1162 2.5 43.9825 4.3545 48.9649 7.863C49.1837 8.017 49.4349 8.09112 49.6835 8.09112C50.0764 8.09112 50.4631 7.90637 50.7065 7.56075Z"/>
                                <path d="M64 32C64 25.6044 62.1191 19.4309 58.5606 14.1469C58.1749 13.5741 57.398 13.4228 56.8255 13.8083C56.2529 14.1939 56.1012 14.9706 56.4869 15.5433C59.7665 20.4133 61.5 26.1039 61.5 32C61.5 38.2699 59.5761 44.2139 55.9286 49.2551C55.3309 48.9834 54.6681 48.8308 53.9699 48.8308C51.3507 48.8308 49.2199 50.9616 49.2199 53.5808C49.2199 56.1999 51.3507 58.3308 53.9699 58.3308C56.589 58.3308 58.7199 56.1999 58.7199 53.5808C58.7199 52.567 58.3996 51.6271 57.8564 50.855C61.878 45.3564 64 38.8576 64 32ZM53.9699 55.8308C52.7292 55.8308 51.7199 54.8214 51.7199 53.5808C51.7199 52.3401 52.7292 51.3308 53.9699 51.3308C55.2105 51.3308 56.2199 52.3401 56.2199 53.5808C56.2199 54.8214 55.2105 55.8308 53.9699 55.8308Z" />
                                <path d="M43.0723 59.352C39.555 60.7774 35.8298 61.5 32 61.5C15.7336 61.5 2.5 48.2664 2.5 32C2.5 25.7301 4.42387 19.7861 8.07137 14.7449C8.66912 15.0166 9.33187 15.1693 10.0301 15.1693C12.6493 15.1693 14.7801 13.0384 14.7801 10.4193C14.7801 7.80013 12.6493 5.66925 10.0301 5.66925C7.411 5.66925 5.28013 7.80013 5.28013 10.4193C5.28013 11.433 5.60037 12.3729 6.14362 13.145C2.122 18.6436 0 25.1424 0 32C0 40.5475 3.32863 48.5834 9.3725 54.6274C15.4166 60.6715 23.4525 64 32 64C36.153 64 40.1941 63.2157 44.0112 61.6691C44.651 61.4099 44.9595 60.681 44.7002 60.0412C44.4409 59.4012 43.7119 59.0929 43.0723 59.352ZM10.0301 8.16925C11.2708 8.16925 12.2801 9.17863 12.2801 10.4193C12.2801 11.6599 11.2708 12.6693 10.0301 12.6693C8.7895 12.6693 7.78013 11.6599 7.78013 10.4193C7.78013 9.17863 8.7895 8.16925 10.0301 8.16925Z"/>
                                <path d="M32 29.2213C29.3606 29.2213 27.2134 31.3685 27.2134 34.0078C27.2134 35.102 27.5781 36.1401 28.25 36.9834V40.7161C28.25 42.7839 29.9322 44.4661 32 44.4661C34.0677 44.4661 35.75 42.7839 35.75 40.7161V36.9834C36.4219 36.14 36.7866 35.102 36.7866 34.0078C36.7866 31.3685 34.6394 29.2213 32 29.2213ZM33.6164 35.625C33.3819 35.8595 33.25 36.1775 33.25 36.5091V40.7161C33.25 41.4054 32.6892 41.9661 32 41.9661C31.3107 41.9661 30.75 41.4054 30.75 40.7161V36.5091C30.75 36.1775 30.6181 35.8594 30.3836 35.625C29.9514 35.193 29.7134 34.6188 29.7134 34.0078C29.7134 32.747 30.7391 31.7213 32 31.7213C33.2609 31.7213 34.2866 32.747 34.2866 34.0078C34.2866 34.6188 34.0486 35.1931 33.6164 35.625Z"/>
                                <path d="M43.4939 24.4526V21.8899C43.4939 15.5521 38.3377 10.396 32 10.396C25.6623 10.396 20.5061 15.5521 20.5061 21.8899V24.4526C18.8877 25.1415 17.75 26.7476 17.75 28.6146V45.0727C17.75 47.5656 19.7781 49.5937 22.2709 49.5937H41.7291C44.2219 49.5937 46.25 47.5656 46.25 45.0727V28.6146C46.25 26.7476 45.1122 25.1415 43.4939 24.4526ZM23.0061 21.8899C23.0061 16.9306 27.0408 12.896 32 12.896C36.9592 12.896 40.9939 16.9306 40.9939 21.8899V24.0937H38.3719V21.8899C38.3719 18.3764 35.5135 15.518 31.9999 15.518C28.4862 15.518 25.6279 18.3765 25.6279 21.8899V24.0937H23.0059V21.8899H23.0061ZM28.128 24.0937V21.8899C28.128 19.7549 29.865 18.018 32 18.018C34.135 18.018 35.872 19.7549 35.872 21.8899V24.0937H28.128ZM43.75 45.0727C43.75 46.1871 42.8434 47.0937 41.7291 47.0937H22.2709C21.1566 47.0937 20.25 46.1871 20.25 45.0727V28.6146C20.25 27.5002 21.1566 26.5937 22.2709 26.5937H41.7291C42.8434 26.5937 43.75 27.5004 43.75 28.6146V45.0727Z" />
                                <path d="M53.97 11.735C54.2987 11.735 54.6212 11.6025 54.8537 11.37C55.0861 11.1375 55.22 10.815 55.22 10.4863C55.22 10.1563 55.0862 9.83377 54.8537 9.60127C54.6212 9.36877 54.2986 9.23627 53.97 9.23627C53.6412 9.23627 53.3187 9.36877 53.0862 9.60127C52.8539 9.83377 52.72 10.1563 52.72 10.4863C52.72 10.815 52.8537 11.1363 53.0862 11.37C53.3187 11.6025 53.6412 11.735 53.97 11.735Z" />
                                </g>
                                <defs>
                                <clipPath id="clip0_1400_5347">
                                <rect width="64" height="64" />
                                </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <p class="qualitis-storage">{{trans('website.services.qualities.security')}}</p>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-xl-6" style="padding-right:0px;">
                    <div class="rectangle-storage">
                        <div class="icone-storage">
                            <svg class="img2-about"  width="65"  height="65" viewBox="0 0 52 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50.7148 0H48.7893C48.0818 0 47.5041 0.585022 47.5041 1.30435V11.2876H23.6739V1.13914C23.6739 0.558243 23.2091 0.0869293 22.6383 0.0869293C21.9315 0.0869293 8.36058 0.0869293 7.6457 0.0869293C7.07452 0.0869293 6.6089 0.558243 6.6089 1.13914V11.2876H4.49551V7.17764C4.49551 6.73888 4.14477 6.38292 3.71284 6.38292C3.27928 6.38292 2.92975 6.73888 2.92975 7.17764V52.4106C2.33747 52.4106 2.17468 52.4106 1.56616 52.4106V1.58945H2.92975V3.99875C2.92975 4.43834 3.27928 4.79347 3.71284 4.79347C4.14477 4.79347 4.49551 4.43834 4.49551 3.99875V1.30435C4.49551 0.585022 3.91825 0 3.21027 0H1.28524C0.577264 0 0 0.585022 0 1.30435V52.6956C0 53.415 0.577264 54 1.28524 54H50.7148C51.4227 54 52 53.415 52 52.6956V1.30435C52 0.585022 51.4227 0 50.7148 0ZM13.752 1.67638H16.5316V4.24471L15.7298 3.60777C15.384 3.3338 14.8988 3.33421 14.5542 3.60777L13.752 4.24471V1.67638ZM8.17506 1.67638H12.1863V5.52022C12.1863 6.05333 12.6141 6.48756 13.1403 6.48756C13.5661 6.48756 13.6465 6.34625 15.142 5.15891L16.5563 6.28239C17.1807 6.77719 18.0977 6.32401 18.0977 5.52022V1.67638H22.1077V11.2876H8.17506V1.67638ZM4.49551 12.8771H47.5041V14.2609H4.49551V12.8771ZM4.49551 15.85H47.5041V30.3631H40.8997V18.1612C40.8997 17.5136 40.3796 16.9871 39.7419 16.9871C38.6564 16.9871 13.5495 16.9871 12.2581 16.9871C11.6204 16.9871 11.1003 17.5136 11.1003 18.1612V30.3631H4.49551V15.85ZM24.2167 24.1095L26 22.6939L27.7833 24.1095C28.4268 24.6208 29.3775 24.1552 29.3775 23.3214V18.5765H39.3335V30.3631H12.6665V18.5765H22.6225V23.3214C22.6225 24.149 23.5647 24.6237 24.2167 24.1095ZM24.1886 22.1142V18.5765H27.8114V22.1142C26.5415 21.1061 26.434 20.9454 26 20.9454C25.5616 20.9454 25.4495 21.1131 24.1886 22.1142ZM4.49551 31.9525H47.5041V33.3351C46.6447 33.3351 5.40119 33.3351 4.49551 33.3351V31.9525ZM34.8128 39.8408C35.2395 39.8408 35.3121 39.7061 36.858 38.4784L38.3145 39.6344V39.6352C38.9454 40.1325 39.8608 39.6748 39.8608 38.8694V34.9246H43.2497V49.4385H30.4663V34.9246H33.8556V38.8694C33.8556 39.4053 34.2847 39.8408 34.8128 39.8408ZM35.4213 37.6008V34.9246H38.2947V37.6008C37.4259 36.9116 37.2765 36.7225 36.858 36.7225C36.4435 36.7225 36.3124 36.8943 35.4213 37.6008ZM22.3164 45.7586C21.8841 45.7586 21.5333 46.1146 21.5333 46.5533V49.4385H8.74989V34.925H12.1392V38.8694C12.1392 39.4053 12.5683 39.8408 13.0964 39.8408C13.5219 39.8408 13.5909 39.7094 15.142 38.4784L16.5973 39.6344C16.5982 39.6344 16.5982 39.6352 16.5982 39.6352C17.2286 40.1321 18.1444 39.6748 18.1444 38.8694V34.925H21.5333V43.3748C21.5333 43.8136 21.8841 44.1696 22.3164 44.1696C22.7491 44.1696 23.0995 43.8136 23.0995 43.3748V34.9246H28.9005V49.4385C27.83 49.4385 23.6292 49.4385 23.0995 49.4385V46.5533C23.0995 46.1146 22.7487 45.7586 22.3164 45.7586ZM13.7053 37.6008V34.925H16.5783V37.6008C15.7245 36.9235 15.5666 36.7225 15.142 36.7225C14.7206 36.7225 14.5708 36.9141 13.7053 37.6008ZM4.49551 34.9246H7.18372V49.4385H4.49551V34.9246ZM47.5041 52.4106H4.49551V51.0279H47.5041V52.4106ZM50.4338 52.4106H49.0702C49.0702 51.6838 49.0702 46.4713 49.0702 45.7627C49.0702 45.3231 48.7207 44.9684 48.2872 44.9684C47.8552 44.9684 47.5041 45.3231 47.5041 45.7627V49.4385H44.8159V34.9246H47.5041V42.5842C47.5041 43.023 47.8552 43.379 48.2872 43.379C48.7207 43.379 49.0702 43.023 49.0702 42.5842C49.0702 40.5869 49.0702 3.24934 49.0702 1.58945H50.4338V52.4106Z" />
                            </svg>

                        </div>
                        <p class="qualitis-storage">{{trans('website.services.qualities.organization')}}</p>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-xl-6 class-qualidades3" style="padding-left:0px;">
                    <div class="rectangle-storage">
                        <div class="icone-storage">
                            <svg class="img2-about"  width="65"  height="65" viewBox="0 0 63 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M33.6818 46.6362H1.29545C0.951879 46.6362 0.622375 46.4997 0.37943 46.2567C0.136485 46.0138 0 45.6843 0 45.3407V41.4543H2.59091V44.0453H33.6818V46.6362Z" />
                                <path d="M2.59091 38.8636H0V1.29545C0 0.951879 0.136485 0.622375 0.37943 0.37943C0.622375 0.136485 0.951879 0 1.29545 0H45.3409C45.6845 0 46.014 0.136485 46.2569 0.37943C46.4999 0.622375 46.6364 0.951879 46.6364 1.29545V16.8409H44.0455V2.59091H2.59091V38.8636Z" />
                                <path d="M29.7957 20.7273H16.8411C16.4976 20.7273 16.168 20.5908 15.9251 20.3478C15.6822 20.1049 15.5457 19.7754 15.5457 19.4318V1.29545C15.5457 0.951879 15.6822 0.622375 15.9251 0.37943C16.168 0.136485 16.4976 0 16.8411 0H29.7957C30.1392 0 30.4688 0.136485 30.7117 0.37943C30.9546 0.622375 31.0911 0.951879 31.0911 1.29545V19.4318C31.0911 19.7754 30.9546 20.1049 30.7117 20.3478C30.4688 20.5908 30.1392 20.7273 29.7957 20.7273ZM18.1366 18.1364H28.5002V2.59091H18.1366V18.1364Z"/>
                                <path d="M5.18268 38.8636H7.77359V41.4545H5.18268V38.8636Z" />
                                <path d="M10.363 38.8636H18.1357V41.4545H10.363V38.8636Z" />
                                <path d="M5.18268 33.6821H12.9554V36.273H5.18268V33.6821Z" />
                                <path d="M1.29567 5.18207H25.9093V7.77298H1.29567V5.18207Z" />
                                <path d="M29.7957 5.18207H45.3411V7.77298H29.7957V5.18207Z" />
                                <path d="M46.6368 57C46.297 56.9986 45.9713 56.8637 45.73 56.6244C45.1263 56.031 31.0913 41.9961 31.0913 33.6819C31.0913 29.559 32.7292 25.6049 35.6445 22.6896C38.5598 19.7742 42.5139 18.1364 46.6368 18.1364C50.7597 18.1364 54.7138 19.7742 57.6291 22.6896C60.5444 25.6049 62.1823 29.559 62.1823 33.6819C62.1823 41.9961 48.1473 56.031 47.5436 56.6244C47.3023 56.8637 46.9767 56.9986 46.6368 57ZM46.6368 20.7273C43.2022 20.7311 39.9094 22.0972 37.4807 24.5258C35.0521 26.9544 33.686 30.2473 33.6823 33.6819C33.6823 39.2627 42.3165 49.3737 46.6368 53.8573C50.9571 49.3737 59.5913 39.2627 59.5913 33.6819C59.5876 30.2473 58.2215 26.9544 55.7929 24.5258C53.3643 22.0972 50.0714 20.7311 46.6368 20.7273Z"/>
                                <path d="M46.6357 41.4547C45.0984 41.4547 43.5957 40.9988 42.3174 40.1448C41.0392 39.2907 40.043 38.0767 39.4547 36.6565C38.8664 35.2362 38.7124 33.6734 39.0124 32.1656C39.3123 30.6578 40.0525 29.2729 41.1396 28.1858C42.2266 27.0988 43.6116 26.3585 45.1193 26.0586C46.6271 25.7587 48.1899 25.9126 49.6102 26.5009C51.0305 27.0892 52.2444 28.0855 53.0985 29.3637C53.9526 30.6419 54.4085 32.1447 54.4085 33.682C54.4064 35.7428 53.5868 37.7186 52.1296 39.1758C50.6724 40.6331 48.6966 41.4526 46.6357 41.4547ZM46.6357 28.5002C45.6109 28.5002 44.609 28.8041 43.7569 29.3734C42.9047 29.9428 42.2406 30.7521 41.8484 31.699C41.4562 32.6458 41.3535 33.6877 41.5535 34.6929C41.7534 35.6981 42.2469 36.6214 42.9716 37.3461C43.6963 38.0708 44.6196 38.5643 45.6248 38.7642C46.63 38.9642 47.6719 38.8615 48.6187 38.4693C49.5656 38.0771 50.3749 37.413 50.9443 36.5608C51.5136 35.7087 51.8176 34.7068 51.8176 33.682C51.8176 32.3077 51.2716 30.9897 50.2998 30.0179C49.3281 29.0461 48.01 28.5002 46.6357 28.5002Z"/>
                            </svg>

                        </div>
                        <p class="qualitis-storage">{{trans('website.services.qualities.rastreability')}}</p>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-xl-6 class-qualidades3" style="padding-right:0px;">
                    <div class="rectangle-storage">
                        <div class="icone-storage">
                            <svg class="img2-about"  width="65"  height="65"  viewBox="0 0 65 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M59.9842 44.678L38.1814 50.1662C37.9942 49.5246 37.6489 48.9184 37.1449 48.4144L30.1987 41.4682C29.337 40.6071 28.1919 40.133 26.9737 40.133H13.1182C12.8568 37.7446 10.828 35.8793 8.3698 35.8793H4.77685C2.1428 35.8793 0 38.0216 0 40.6562V50.2698C0 50.792 0.423943 51.2154 0.945643 51.2154C1.46783 51.2154 1.89178 50.792 1.89178 50.2698V40.6562C1.89178 39.0646 3.18669 37.7711 4.77685 37.7711H8.3698C9.78114 37.7711 10.9601 38.7909 11.2072 40.133C11.2775 40.5108 11.2554 39.3794 11.2554 57.4366C11.2554 59.0282 9.96044 60.3221 8.3698 60.3221H4.77685C3.18669 60.3221 1.89178 59.0282 1.89178 57.4366V54.0529C1.89178 53.5312 1.46783 53.1072 0.945643 53.1072C0.423943 53.1072 0 53.5312 0 54.0529V57.4366C0 60.0711 2.1428 62.2139 4.77685 62.2139H8.3698C11.0043 62.2139 13.1471 60.0711 13.1471 57.4366V55.7624C20.113 56.9655 18.6142 56.5273 28.9961 61.6067C30.683 62.4325 32.31 64 35.7522 64C37.5875 64 39.3761 63.4838 40.9294 62.5062L58.9197 53.3843C59.3859 53.148 59.5721 52.5786 59.3358 52.113C59.0995 51.6473 58.5301 51.4611 58.0644 51.6969C57.9996 51.7298 40.0147 60.8458 39.9538 60.8846C38.6952 61.6853 37.2426 62.1083 35.7522 62.1083C32.8828 62.1083 31.6365 60.7932 29.8273 59.9075C19.271 54.7431 20.7712 55.1596 13.1471 53.8431V42.0243H26.9742C27.6879 42.0243 28.3575 42.3018 28.862 42.8064L35.8067 49.7511C37.1748 51.1192 36.2547 53.4147 34.3605 53.4147C33.7951 53.4147 33.263 53.1942 32.8622 52.7943L26.6057 46.5354C26.2363 46.166 25.637 46.166 25.2676 46.5354C24.8982 46.9048 24.8982 47.5041 25.2676 47.8735L31.525 54.1325C33.7808 56.3809 37.6641 55.2333 38.2718 52.0943C61.6456 46.2102 60.3551 46.5157 60.5899 46.5157C62.5023 46.5157 63.1498 49.1183 61.4393 49.9859C60.9731 50.2222 60.7869 50.7915 61.0232 51.2572C61.259 51.7234 61.8283 51.9096 62.2945 51.6733C63.6877 50.9669 64.3234 49.6047 64.3234 48.3633C64.3234 45.7975 61.8126 44.2177 59.9842 44.678Z"/>
                                <path d="M63.1204 23.2524L58.5597 21.5925C58.5902 21.0487 58.5902 20.5118 58.5597 19.9655L63.12 18.3056C64.1206 17.9421 64.6354 16.8378 64.2714 15.8371L62.7225 11.5815C62.3595 10.5838 61.2518 10.067 60.254 10.43L55.6938 12.0899C55.3691 11.6561 55.0194 11.2401 54.6475 10.8441L57.0742 6.64105C57.6053 5.72144 57.2889 4.54098 56.3693 4.00995L52.4472 1.74581C52.0016 1.4884 51.4829 1.41963 50.9858 1.55324C50.4891 1.68637 50.0735 2.00519 49.8166 2.45074L47.3908 6.65136C46.8637 6.52806 46.3278 6.43423 45.7874 6.37086L44.9449 1.59156C44.7602 0.545704 43.7596 -0.154809 42.7137 0.0294074L38.2537 0.815396C37.2079 1.0001 36.5069 2.00077 36.6916 3.04662L37.5341 7.82396C37.0502 8.06909 36.5791 8.34124 36.1247 8.63942L32.4074 5.52003C31.5905 4.83426 30.3771 4.94331 29.6943 5.75779L26.7832 9.22695C26.0989 10.0419 26.205 11.2558 27.0205 11.9401L30.7362 15.058C30.522 15.5571 30.3359 16.069 30.1792 16.5887H25.3286C24.2666 16.5887 23.403 17.4528 23.403 18.5149V23.0432C23.403 24.1052 24.2666 24.9693 25.3286 24.9693H30.1792C30.3359 25.4896 30.522 26.0009 30.7362 26.5L27.0205 29.6175C26.207 30.3003 26.1004 31.5176 26.7832 32.3316L29.6943 35.8007C30.3766 36.6137 31.5939 36.7203 32.4074 36.038L36.1247 32.9186C36.5791 33.2168 37.0502 33.489 37.5341 33.7341L36.6916 38.5114C36.5069 39.5597 37.2054 40.5574 38.2537 40.7426L42.7137 41.5286C43.7542 41.7133 44.7597 41.0168 44.9449 39.9665L45.7874 35.1872C46.3278 35.1238 46.8637 35.03 47.3913 34.9067L49.8166 39.1073C50.3476 40.0274 51.5276 40.3438 52.4477 39.8127L56.3698 37.5481C57.2894 37.0171 57.6053 35.8371 57.0742 34.9175L54.6475 30.7139C55.0194 30.3185 55.3691 29.9019 55.6938 29.4681L60.254 31.128C61.2527 31.4911 62.3575 30.9792 62.7225 29.9766L64.2714 25.7214C64.6354 24.7208 64.1211 23.6164 63.1204 23.2524ZM62.4941 25.074L60.9452 29.3296C60.9433 29.334 60.9403 29.3429 60.927 29.3488C60.9143 29.3551 60.9054 29.3517 60.901 29.3502L55.6639 27.4437C55.2552 27.2954 54.7978 27.4442 54.5551 27.8053C54.0516 28.5549 53.4656 29.253 52.8127 29.8798C52.4988 30.1814 52.431 30.6579 52.6486 31.0347L55.4359 35.8626C55.4458 35.8793 55.4399 35.9005 55.4236 35.9098L51.5016 38.1739C51.4854 38.1838 51.4642 38.1779 51.4544 38.1617L48.6686 33.3367C48.4514 32.9599 48.0049 32.7801 47.5868 32.9009C46.7223 33.151 45.8248 33.3082 44.9189 33.3681C44.4846 33.3966 44.1255 33.7184 44.0499 34.1477L43.0821 39.6378C43.0787 39.6565 43.061 39.6688 43.0419 39.6658C42.9898 39.6565 38.545 38.8921 38.5544 38.8396L39.5221 33.3524C39.5973 32.924 39.3708 32.4991 38.9734 32.3232C38.1481 31.9577 37.3592 31.5014 36.6287 30.9669C36.2765 30.709 35.7951 30.7257 35.462 31.0052L31.1916 34.5884C31.1774 34.6006 31.1558 34.5987 31.1435 34.5844L28.2324 31.1153C28.2201 31.1005 28.222 31.0789 28.2363 31.0666L32.5047 27.485C32.8387 27.205 32.938 26.7334 32.7454 26.3428C32.3465 25.5318 32.0346 24.6756 31.8194 23.7977C31.7158 23.3748 31.3365 23.0775 30.9008 23.0775H25.3286C25.3095 23.0775 25.2942 23.0623 25.2942 23.0432V18.5144C25.2942 18.4957 25.3095 18.48 25.3286 18.48H30.9008C31.3365 18.48 31.7153 18.1828 31.8194 17.7598C32.0346 16.8825 32.3465 16.0262 32.7454 15.2152C32.938 14.8242 32.8387 14.3526 32.5047 14.0726L28.2363 10.4909C28.2329 10.4885 28.2255 10.4821 28.224 10.4678C28.223 10.4536 28.2289 10.4462 28.2324 10.4428L31.143 6.97362C31.1464 6.97018 31.1523 6.96281 31.1666 6.96134C31.1808 6.96036 31.1882 6.96625 31.1916 6.9692L35.462 10.5523C35.7955 10.8328 36.2775 10.8485 36.6287 10.5911C37.3592 10.0562 38.1481 9.5998 38.9734 9.23481C39.3708 9.05895 39.5973 8.63402 39.5221 8.20566L38.5544 2.71847C38.5509 2.6998 38.5637 2.68163 38.5824 2.67868L43.0424 1.8922C43.0468 1.89171 43.0561 1.88974 43.0679 1.8981C43.0797 1.90596 43.0812 1.91578 43.0821 1.9202L44.0499 7.41033C44.1255 7.83919 44.4846 8.16144 44.9189 8.18994C45.8253 8.24987 46.7228 8.40707 47.5868 8.65711C48.0049 8.77795 48.4514 8.59816 48.6691 8.22187L51.4544 3.39639C51.4569 3.39246 51.4618 3.38411 51.4755 3.38067C51.4893 3.37674 51.4976 3.38165 51.5016 3.38411L55.4236 5.64825C55.4399 5.65758 55.4458 5.6787 55.4364 5.6954L52.6486 10.5233C52.431 10.9001 52.4988 11.3766 52.8127 11.6783C53.4656 12.3056 54.0521 13.0036 54.5551 13.7528C54.7978 14.1138 55.2552 14.2622 55.6639 14.1138L60.901 12.2078C60.9187 12.2019 60.9383 12.2108 60.9452 12.2284L62.4941 16.4841C62.4956 16.4885 62.499 16.4973 62.4926 16.5101C62.4867 16.5234 62.4779 16.5263 62.4735 16.5283L57.2354 18.4343C56.8271 18.5832 56.5727 18.9914 56.6188 19.4237C56.7117 20.2937 56.7191 21.1951 56.6188 22.1344C56.5727 22.5667 56.8271 22.9749 57.2354 23.1237L62.4735 25.0297C62.4779 25.0317 62.4867 25.0347 62.4926 25.0479C62.499 25.0607 62.4956 25.0695 62.4941 25.074Z"/>
                                <path d="M51.4532 14.6239L40.8192 25.2579L36.3376 20.7763C35.9682 20.4073 35.3694 20.4073 35 20.7763C34.6305 21.1457 34.6305 21.7445 35 22.1139L39.5872 26.7011C40.2681 27.382 41.3704 27.382 42.0513 26.7011L52.7908 15.9616C53.1602 15.5922 53.1602 14.9933 52.7908 14.6239C52.4214 14.2545 51.8226 14.2545 51.4532 14.6239Z"/>
                            </svg>

                        </div>
                        <p class="qualitis-storage">{{trans('website.services.qualities.eficiency')}}</p>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-xl-6 class-qualidades3" style="padding-left:0px;">
                    <div class="rectangle-storage">
                        <div class="icone-storage">
                            <svg class="img2-about"  width="65"  height="65" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1400_5391)">
                                <path d="M44.2562 24.6855C44.9604 24.9737 45.7822 24.5615 45.9751 23.8279C46.2719 22.6964 46.4226 21.5238 46.4226 20.3432C46.4226 19.1706 46.2737 18.0048 45.98 16.8778C45.7885 16.1423 44.965 15.7293 44.2595 16.0186C43.3239 16.4029 42.2359 16.0304 41.7289 15.1526C41.2216 14.2744 41.4434 13.1469 42.2447 12.5299C42.8474 12.066 42.9048 11.1466 42.3645 10.6112C40.689 8.9506 38.6056 7.74571 36.3399 7.12653C35.6047 6.92504 34.8369 7.43708 34.7373 8.19135C34.6048 9.19577 33.7388 9.95323 32.7228 9.95323C31.7067 9.95323 30.8407 9.19577 30.7082 8.19135C30.6086 7.4372 29.8409 6.92504 29.1056 7.12653C26.8404 7.74558 24.7573 8.95047 23.0817 10.6112C22.5418 11.1464 22.5988 12.0654 23.2012 12.5294C24.0022 13.1469 24.2241 14.2748 23.7173 15.1527C23.2103 16.0306 22.1227 16.4025 21.187 16.0186C20.4818 15.7292 19.6583 16.1423 19.4664 16.8778C19.1726 18.0044 19.0237 19.1705 19.0237 20.3433C19.0237 21.524 19.1743 22.6966 19.4714 23.828C19.664 24.562 20.4859 24.9735 21.1903 24.6855C22.1248 24.303 23.2108 24.6758 23.7171 25.5524C24.2218 26.4273 24.003 27.553 23.208 28.1709C22.9177 28.3966 22.7387 28.7363 22.7167 29.1033C22.6949 29.4702 22.8322 29.8289 23.0936 30.0873C24.7681 31.743 26.8479 32.944 29.1085 33.5609C29.8405 33.7604 30.6061 33.2547 30.7097 32.5029C30.8467 31.5051 31.7122 30.7526 32.7228 30.7526C33.7333 30.7526 34.5987 31.5051 34.7358 32.5027C34.8389 33.2535 35.6046 33.7607 36.3369 33.5609C38.5978 32.9441 40.678 31.7431 42.3527 30.0873C42.8923 29.5537 42.8378 28.6367 42.2383 28.1709C41.4429 27.5528 41.2239 26.4273 41.7291 25.5526C42.2354 24.6755 43.322 24.3027 44.2562 24.6855ZM39.5177 24.2755C38.6373 25.7997 38.7328 27.6625 39.65 29.0755C38.7896 29.7586 37.8326 30.3118 36.8144 30.7142C36.0499 29.213 34.484 28.1986 32.7229 28.1986C30.962 28.1986 29.3961 29.2131 28.6315 30.7142C27.6134 30.3115 26.6568 29.7588 25.7964 29.0754C26.7132 27.6627 26.8087 25.8001 25.9289 24.2756C25.0469 22.7476 23.3821 21.8987 21.6984 21.9899C21.6181 21.447 21.5776 20.8965 21.5776 20.3432C21.5776 19.7968 21.6173 19.2523 21.6959 18.715C23.3823 18.8076 25.0461 17.9583 25.9289 16.4297C26.8127 14.8991 26.7137 13.0312 25.7886 11.6174C26.6485 10.9334 27.6049 10.3796 28.623 9.97596C29.3836 11.4863 30.9524 12.5071 32.7228 12.5071C34.4931 12.5071 36.0619 11.4862 36.8225 9.97596C37.8409 10.3795 38.7974 10.9333 39.6573 11.6173C38.7315 13.031 38.6329 14.8991 39.5175 16.4301C40.4001 17.9584 42.0625 18.8062 43.7503 18.7151C43.8288 19.2526 43.8685 19.7971 43.8685 20.3435C43.8685 20.8966 43.8281 21.4474 43.7477 21.9902C42.0635 21.8999 40.3993 22.7479 39.5177 24.2755Z" />
                                <path d="M32.7227 14.8083C29.671 14.8083 27.1881 17.291 27.1881 20.3429C27.1881 23.3947 29.6708 25.8775 32.7227 25.8775C35.7745 25.8775 38.2573 23.3947 38.2573 20.3429C38.2573 17.291 35.7743 14.8083 32.7227 14.8083ZM32.7227 23.3236C31.0791 23.3236 29.742 21.9864 29.742 20.3427C29.742 18.6992 31.0791 17.362 32.7227 17.362C34.3663 17.362 35.7035 18.6992 35.7035 20.3427C35.7033 21.9865 34.3663 23.3236 32.7227 23.3236Z" />
                                <path d="M63.0964 53.1157C62.4707 50.1056 59.4074 48.1354 56.4045 48.8226L43.623 51.749C41.3198 52.2764 38.9436 52.164 36.7109 51.4283C37.5818 50.8162 38.2978 49.9568 38.7461 48.8977C39.9959 45.9753 38.6776 42.5121 35.7135 41.2854L22.6944 35.8967C22.5396 35.8327 22.3736 35.7996 22.2061 35.7996H10.0554V34.0653C10.0554 33.3701 9.47377 32.7884 8.77849 32.7884H-0.887342C-1.59245 32.7884 -2.16425 33.3602 -2.16425 34.0653V56.5105C-2.16425 57.2156 -1.59245 57.7874 -0.887342 57.7874H8.77837C9.48348 57.7874 10.0553 57.2156 10.0553 56.5105V55.2968H15.1004C15.8055 55.2968 16.3773 54.725 16.3773 54.0199C16.3773 53.3148 15.8055 52.743 15.1004 52.743H10.0553V46.0131V38.3533H21.9521L34.7367 43.645C36.4051 44.3355 37.1015 46.2722 36.3941 47.9018C35.7169 49.5013 33.9629 50.2954 32.3439 49.7659L23.638 46.4188C22.9795 46.1656 22.241 46.4942 21.9879 47.1524C21.7348 47.8106 22.0634 48.5495 22.7215 48.8026C22.7217 48.8027 27.5659 50.6651 31.2599 52.0898C31.3335 52.1183 31.4072 52.145 31.4811 52.1703L35.197 53.5989C38.0757 54.7057 41.1864 54.9266 44.1925 54.2385L56.974 51.3121C58.5989 50.9397 60.2571 52.0064 60.5956 53.6354C60.9246 55.2181 59.9549 56.7698 58.3883 57.1681L43.1374 61.044C40.505 61.713 37.777 61.5423 35.2485 60.5508L26.222 57.0103C25.5654 56.7526 24.8245 57.0763 24.567 57.7328C24.3094 58.3892 24.633 59.1302 25.2895 59.3878L34.316 62.9283C36.133 63.6409 38.0357 63.9996 39.9483 63.9996C41.2242 63.9996 42.5048 63.8398 43.7664 63.5191L59.0172 59.6431C61.9128 58.9074 63.7043 56.0401 63.0964 53.1157ZM0.38957 55.2336V35.3421H7.50158V47.0442V54.0199V55.2336H0.38957Z" />
                                <path d="M10.1373 25.8177L13.7371 27.939C14.3076 28.2755 15.0647 28.1067 15.4387 27.5606L17.8172 24.0892C18.2158 23.5075 18.0673 22.7127 17.4856 22.3142C16.9042 21.9156 16.1092 22.0641 15.7105 22.6458L14.9495 23.7565C14.3126 20.3322 14.6859 16.7667 16.0469 13.5298C19.877 4.41969 30.4045 0.124161 39.5148 3.95477C43.928 5.81012 47.3544 9.27298 49.1629 13.7054C50.9714 18.1379 50.9456 23.0094 49.0901 27.4225C48.1671 29.6183 46.8462 31.5727 45.1647 33.2314C44.6626 33.7266 44.657 34.5351 45.1523 35.0372C45.6402 35.532 46.4633 35.5375 46.9581 35.0496C48.8806 33.1533 50.39 30.9202 51.4445 28.4123C53.5643 23.3705 53.5938 17.8048 51.5275 12.7407C49.4612 7.67671 45.5467 3.7202 40.5048 1.60053C30.0967 -2.77545 18.0687 2.13198 13.6926 12.5401C12.1402 16.2328 11.7129 20.3004 12.4363 24.2082L11.4339 23.6175C10.826 23.2594 10.0435 23.4616 9.68554 24.0693C9.3275 24.6767 9.52976 25.4596 10.1373 25.8177Z"/>
                                <path d="M42.5791 36.3681C42.1938 35.789 41.3873 35.6309 40.8094 36.0155C40.2309 36.4008 40.0714 37.2081 40.4556 37.7866C40.8405 38.3661 41.6499 38.5232 42.2267 38.1391C42.8094 37.751 42.9601 36.9491 42.5791 36.3681Z" />
                                <path d="M21.3646 55.3509C20.9792 54.7718 20.1727 54.6137 19.5949 54.9983C19.0163 55.3836 18.8568 56.1909 19.2411 56.7694C19.6259 57.3489 20.4354 57.506 21.0122 57.1219C21.5947 56.7338 21.7454 55.932 21.3646 55.3509Z" />
                                </g>
                                <defs>
                                <clipPath id="clip0_1400_5391">
                                <rect width="64" height="64" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <p class="qualitis-storage">{{trans('website.services.qualities.flexibility')}}</p>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-xl-6 class-qualidades3" style="padding-right:0px;">
                    <div class="rectangle-storage">
                        <div class="icone-storage">
                            <svg class="img2-about" width="65"  height="65" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1400_5411)">
                                <path d="M63.3867 53.7383C58.409 34.807 58.4021 28.3172 63.3404 10.3378C64.1061 7.5503 63.5407 4.71688 61.3541 2.33541C59.0133 0.108702 56.0495 -0.404947 53.4215 0.28657C34.4899 5.26419 28 5.271 10.0209 0.332827C7.23298 -0.432817 4.04839 0.236881 2.00215 2.33541C-0.0441011 4.43393 -0.749845 7.5503 0.0158285 10.3378C4.95414 28.3169 4.94728 34.8067 -0.0304279 53.7383C-0.721393 56.3669 -0.129021 59.1404 1.55401 61.1571C3.99818 63.8956 7.23423 64.4232 10.0209 63.6574C27.9997 58.719 34.4897 58.726 53.4215 63.7035C55.7795 64.3806 59.0133 63.9764 61.3377 61.6712C63.5407 59.4086 64.0778 56.3666 63.3867 53.7383ZM60.2145 60.5315C58.5687 62.1978 55.9567 62.7155 53.8283 62.1561C44.7736 59.7754 38.2305 58.3919 31.5804 58.3647C26.5539 58.2768 19.5199 59.3889 9.59708 62.1145C7.33794 62.7348 4.95312 62.2203 3.14168 60.5315C1.33024 58.6406 0.957363 56.2739 1.51709 54.1452C3.89781 45.0906 5.28131 38.5474 5.30848 31.8974C5.33286 25.9127 4.31656 19.9546 1.55864 9.91397C0.938308 7.65529 1.50333 5.097 3.14168 3.45865C4.78003 1.8203 7.33809 1.25545 9.59708 1.87569C19.5206 4.60136 25.4561 5.62583 31.3709 5.62583C31.4406 5.62583 31.5106 5.62568 31.5805 5.62536C38.2305 5.59834 44.7736 4.21469 53.8283 1.83398C55.918 1.28425 58.5865 1.67539 60.2147 3.45888C61.8429 5.24237 62.4179 7.65538 61.7976 9.91397C59.0398 19.9547 58.0235 25.9128 58.0479 31.8974C58.0749 38.5475 59.4584 45.0906 61.8391 54.1452C62.3988 56.2737 61.8604 58.8651 60.2145 60.5315Z"/>
                                <path d="M50.4131 19.902C50.7652 20.4378 51.3047 20.8045 51.9324 20.9346C52.5603 21.0646 53.2011 20.9424 53.7369 20.5905C54.2726 20.2385 54.6392 19.699 54.7694 19.0713L56.6756 9.86993C56.8066 9.23787 56.7021 8.60323 56.381 8.08307C55.8511 7.22427 54.8337 6.78457 53.8478 6.9881L44.6016 8.90358C43.9739 9.03365 43.4344 9.40038 43.0824 9.93629C42.7305 10.4722 42.6083 11.1131 42.7383 11.7407C43.0067 13.0365 44.2791 13.8726 45.5754 13.604L46.2486 13.4645L31.6779 28.0351L17.1075 13.4646L17.7803 13.6039C19.0769 13.8722 20.3489 13.0366 20.6176 11.7407C20.8859 10.4447 20.0501 9.17198 18.7543 8.90361L9.55251 6.99729C8.91982 6.86612 8.28576 6.97097 7.76542 7.29208C6.9068 7.82217 6.46692 8.84019 6.67083 9.82516L8.58637 19.0713C8.85483 20.3672 10.128 21.2027 11.4235 20.9346C12.0512 20.8046 12.5907 20.4378 12.9426 19.902C13.2945 19.3661 13.4167 18.7253 13.2867 18.0976L13.1472 17.4244L27.7179 31.9951L13.1472 46.5658L13.2866 45.8927C13.4167 45.2649 13.2945 44.6241 12.9426 44.0881C12.5906 43.5523 12.0511 43.1856 11.4234 43.0556C10.7958 42.9256 10.1549 43.0477 9.61893 43.3997C9.08314 43.7516 8.71655 44.2911 8.5864 44.9188L6.67912 54.1246C6.62053 54.4078 6.46372 55.4877 7.3126 56.3365C8.20189 57.2258 9.42535 57.0191 9.50797 57.002L18.7543 55.0865C20.0501 54.818 20.8859 53.5453 20.6175 52.2492C20.3489 50.9535 19.0755 50.1175 17.7805 50.3861L17.1073 50.5256L31.6779 35.955L46.2487 50.5257L45.5754 50.3862C44.9477 50.2562 44.3068 50.3783 43.771 50.7303C43.235 51.0822 42.8685 51.6217 42.7385 52.2494C42.6083 52.8772 42.7305 53.5181 43.0824 54.0539C43.4343 54.5898 43.9738 54.9566 44.6015 55.0866L53.848 57.0021C54.008 57.0352 55.2538 57.1652 55.992 56.3871C56.8101 55.5281 56.7978 54.7101 56.6765 54.1239L54.7694 44.9189C54.6393 44.2912 54.2727 43.7517 53.7369 43.3997C53.2009 43.0478 52.5601 42.9254 51.9324 43.0556C51.3048 43.1856 50.7651 43.5524 50.4132 44.0882C50.0613 44.6241 49.9391 45.2649 50.0691 45.8926L50.2085 46.5655L35.6379 31.9951L50.2086 17.4245L50.0692 18.0974C49.9391 18.7253 50.0612 19.3661 50.4131 19.902ZM49.8339 15.5364L33.9408 31.4293C33.6176 31.6889 33.6479 32.2649 33.9408 32.5608L49.8339 48.4537C50.2372 48.8569 50.845 48.9458 51.3469 48.6751C51.8487 48.4043 52.108 47.8472 51.9924 47.2889L51.6358 45.5679C51.5925 45.3588 51.6049 44.7722 52.2571 44.6223C52.9092 44.4725 53.1591 45.0342 53.2024 45.2435L55.1095 54.4483C55.2032 54.7904 55.082 55.4573 54.1724 55.4353L44.9262 53.5198C44.7169 53.4764 44.183 53.1836 44.3052 52.574C44.471 52.0012 44.9409 51.8951 45.2507 51.953L46.972 52.3096C47.5304 52.4254 48.0874 52.1658 48.3581 51.6639C48.6289 51.1621 48.5399 50.5541 48.1367 50.151L32.2435 34.2579C31.9313 33.9455 31.4244 33.9455 31.1122 34.2579L15.2192 50.1509C14.8159 50.5541 14.727 51.1622 14.9978 51.664C15.2686 52.1658 15.8255 52.4254 16.3839 52.3095L18.1051 51.9529C18.5356 51.8635 18.9612 52.1421 19.0506 52.574C19.1402 53.006 18.8616 53.4302 18.4295 53.5198L9.18322 55.4353C8.51509 55.5786 8.06035 54.8813 8.24599 54.449L10.1533 45.2434C10.2426 44.8115 10.6673 44.5328 11.0988 44.6224C11.759 44.7757 11.7632 45.3587 11.7197 45.568L11.3635 47.289C11.2477 47.8474 11.5071 48.4045 12.0088 48.6753C12.5109 48.9462 13.119 48.8571 13.5218 48.4539L29.415 32.5608C29.7275 32.2484 29.7275 31.7418 29.415 31.4294L13.5219 15.5362C13.1187 15.1329 12.5107 15.0441 12.0088 15.3149C11.5071 15.5857 11.2477 16.1427 11.3633 16.7009L11.7199 18.4223C11.7632 18.6314 11.6782 19.1882 11.099 19.3678C10.666 19.4566 10.2427 19.1786 10.1533 18.7467L8.23776 9.50058C8.12104 8.98143 8.6364 8.43572 9.22779 8.56404L18.4297 10.4704C18.8616 10.5598 19.1402 10.9841 19.0508 11.4159C18.9613 11.8479 18.536 12.1264 18.105 12.0371L16.384 11.6806C15.8254 11.5649 15.2687 11.8243 14.9979 12.3261C14.7271 12.8279 14.816 13.4359 15.2193 13.8392L31.1122 29.7322C31.4244 30.0445 31.9313 30.0445 32.2435 29.7322L48.1368 13.8392C48.5401 13.436 48.629 12.828 48.3582 12.3262C48.0874 11.8243 47.5307 11.5645 46.9719 11.6806L45.2509 12.0371C44.8184 12.1262 44.3946 11.8479 44.3051 11.4159C44.178 10.9015 44.717 10.5136 44.9264 10.4703L54.1722 8.55488C54.6879 8.43569 55.2033 8.82983 55.1088 9.54509L53.2025 18.7467C53.1131 19.1786 52.6878 19.4567 52.257 19.3677C51.636 19.2285 51.5926 18.6314 51.636 18.4221L51.9923 16.7012C52.1081 16.1429 51.8487 15.5858 51.3469 15.3151C50.8452 15.0442 50.2372 15.1333 49.8339 15.5364Z"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_1400_5411">
                                <rect width="64" height="64" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <p class="qualitis-storage">{{trans('website.services.qualities.maintenence')}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xs-12 col-ms-12 col-md-4 col-xl-4">
            <img src="{{ asset('assets/website/img/servicos-armazenagem.png') }}" alt="sobre-nos" class="img2-about" style="width:100%;">
        </div>
    
    </div>
</section>
{{-- @include('partials.recruitment') --}}

@stop
@section ('styles')
    <style>
        .row {
            margin-right: 0px;
            margin-left: 0px;
        }
    </style>

@stop
{{-- @section ('scripts')
    <script>
        Copy code$("#myImage").hover(function() { 
            $(this).attr("src", "/assets/website/img/seguranca-storagebranco.svg"); 
        }); 

    </script>

@stop --}}
