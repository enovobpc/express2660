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
<section class="topo-about" style="height: 380px; position:relative; background: url('/assets/website/img/topo-about.png'); background-size: cover; background-position: bottom 0px left 0px; display: flex; align-items: flex-end; display: flex; align-items: center;">
    <div class="col-sm-12 todos-topos">
        <h1 class="text-top-about text-uppercase">{{trans('website.about.maintitle')}}</h1>
        <div style="display: flex; align-items: center; margin-top:15px;">
            <p class="text-top2"><a class="text-whitep" href="{{route('home.index')}}">HOME > </a></p>
            <p class="text-top2" style="text-decoration: underline;">&nbspEMPRESA</p>
        </div>
    </div>
</section> 

<section class="row service-page-row">
{{-- <section class="sobre row" > --}}
    <div class="col-12 col-sm-12 col-md-6 col-xl-6 class-imagea hidden-xs" style="height: 620px; overflow: hidden;">
        <img src="{{ asset('assets/website/img/img-about.png') }}" alt="sobre-nos" class="img2-about" style="height:100%;">
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-xl-6 class-recabout" style="display: flow-root; align-content: center;">
        <div class="rectangleab-about">
            <h4 class="about-home">{{ trans('website.about.title') }}</h4>
            <h2 class="about2-home">{{ trans('website.about.subtitle') }}</h2>
            <p class="textabout-home">{!! trans('website.about.description') !!}</p>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-xl-6 class-imagea visible-xs" style="height: 620px; overflow: hidden; margin-top:20px;">
        <img src="{{ asset('assets/website/img/img-about-md.png') }}" alt="sobre-nos" class="img2-about" style="height:100%;">
    </div>
</section>
<section class="mission-vision" style="width: 100%">
        <div class="row" style="padding-left: 0px; padding-right: 0px;">
                <div class="h-75 d-inline-block col-md-1 col-xs-12 mission-side-text"  style="padding: 0 25px;">
                    <h1 class="text-vertical1 text-uppercase">{{trans('website.about.mission.title')}}</h1>
                </div>
                <div class="col-md-10 col-sm-12">
                    <div class="col-sm-12" style="position: relative">
                        <div class="shadowContainerMission">
                            <div class="shadowContent col-md-6 col-xs-12 ">
                                        <h4 class="about-home">{{ trans('website.about.mission.title') }}</h4>
                                        <h2 class="about2-home">{{ trans('website.about.mission.subtitle') }}</h2>
                                        <p class="textabout-home">{{ trans('website.about.mission.description') }}</p>
                            </div>
                        </div>
                        <div class="col-xs-12 icone-missionvission" style="z-index: 5; position:absolute; margin-left:auto; margin-right: auto; display:flex; justify-content:center;">
                            <img src="{{ asset('assets/website/img/mission-vision.svg') }}" alt="mission-vision" style="height: 380px; margin-top: 120px; padding-right: 50px;">
                        </div>
                        <div class="h-auto d-inline-block vision-up-text col-sm-1 col-xs-12" style="align-content: flex-end;" >
                            <h1 class="text-vertical2 text-uppercase">{{trans('website.about.vision.title')}}</h1>
                        </div>   
                        <div class="shadowContainerVision">
                            <div class="shadowContent  col-md-6 col-xs-12 ">
                                <h4 class="about-home">{{ trans('website.about.vision.title') }}</h4>
                                <h2 class="about2-home">{{ trans('website.about.vision.subtitle') }}</h2>
                                <p class="textabout-home">{{ trans('website.about.vision.description') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-auto d-inline-block vision-side-text col-md-1 col-xs-12" style="align-content: flex-end; padding: 0 25px;" >
                    <h1 class="text-vertical2 text-uppercase">{{trans('website.about.vision.title')}}</h1>
                </div>      
        </div>

        

        <div class="values row">
            <div class="col-sm-12 col-md-12 col-xl-4 m-t-20 m-b-20">
                <h4 class="values-about text-uppercase">{{trans('website.about.values.title')}}</h4>
                <h2 class="values-aboutsu text-uppercase">{!!trans('website.about.values.subtitle')!!}</h2>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-4 m-t-20 m-b-20">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <img class="values-icon eficiency-icon" src="/assets/website/img/values-icons/eficiencia-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
                    <div class="class-texto2">
                        <h4 class="text-valuestext">{{trans('website.about.values.value01.title')}}</h4>
                        <p class="text-valuesblak">{{trans('website.about.values.value01.description')}}</p>
                    </div>  
                </div>          
            </div>
            <div class=" col-sm-12 col-md-12 col-xl-4 m-t-20 m-b-20">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <img class="values-icon trust-icon" src="/assets/website/img/values-icons/confianca-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
                    <div class="class-texto2">
                        <h4 class="text-valuestext">{{trans('website.about.values.value02.title')}}</h4>
                        <p class="text-valuesblak">{{trans('website.about.values.value02.description')}}</p>
                    </div>  
                </div>
            </div>
            
            <div class="col-sm-12 col-md-12 col-xl-4 m-t-20 m-b-20" >
                <div style="display: flex; justify-content: center; align-items: center;">
                    <img class="values-icon compromise-icon" src="/assets/website/img/values-icons/compromisso-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
                    <div class="class-texto2">
                        <h4 class="text-valuestext">{{trans('website.about.values.value03.title')}}</h4>
                        <p class="text-valuesblak">{{trans('website.about.values.value03.description')}}</p>
                    </div>  
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-4 m-t-20 m-b-20">
                <div style="display: flex; compromise-content: center; align-items: center;">
                    <img class="values-icon security-icon" src="/assets/website/img/values-icons/seguranca-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
                    <div class="class-texto2">
                        <h4 class="text-valuestext">{{trans('website.about.values.value04.title')}}</h4>
                        <p class="text-valuesblak">{{trans('website.about.values.value04.description')}}</p>
                    </div>  
                </div>
            </div>
            <div class=" col-sm-12 col-md-12 col-xl-4 m-t-20 m-b-20" >
                <div style="display: flex; justify-content: center; align-items: center;">
                    <img class="values-icon simplicity-icon" src="/assets/website/img/values-icons/simplicidade-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
                    <div class="class-texto2">
                        <h4 class="text-valuestext">{{trans('website.about.values.value05.title')}}</h4>
                        <p class="text-valuesblak">{{trans('website.about.values.value05.description')}}</p>
                    </div>  
                </div>
            </div>
        </div>
</section>
{{-- <section class="comments">
    <div>
        <div class="col-12 col-sm-12 col-md-3 col-xl-3">
            <h4 class="servicestitle-home text-uppercase">{{trans('website.about.partners.title')}}</h4>
            <h2 class="servecessubtitle-home text-uppercase">{{trans('website.about.partners.subtitle')}}</h2>
        </div>
        <div class="col-12 col-sm-12 col-md-3 col-xl-3">
            <div class="rectangle-comments">
                <div class="row" style="display:flex; align-items: flex-start;">
                    <img class="col-4"src="{{ asset('assets/website/img/asterisco.svg') }}" alt="asterisco" style="width:100%; padding-left:0px; padding-right:0px; ">
                    <div class="col-8" style="display: flex; flex-direction: column; align-items: flex-end;">
                        <p class="text2-comments" >Acabei de receber a minha encomenda, empresa idónea, eficiente, personalizada simpáticos no atendimento ao cliente.
                        </p>
                        <h4 class="name-comments">Paula Martins</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-3 col-xl-3">
            <div class="rectangle-comments">
                <div class="row" style="display:flex; align-items: flex-start;">
                    <img class="col-4"src="{{ asset('assets/website/img/asterisco.svg') }}" alt="asterisco" style="width:100%; padding-left:0px; padding-right:0px; ">
                    <div class="col-8" style="display: flex; flex-direction: column; align-items: flex-end;">
                        <p class="text2-comments" >Acabei de receber a minha encomenda, empresa idónea, eficiente, personalizada simpáticos no atendimento ao cliente.
                        </p>
                        <h4 class="name-comments">Paula Martins</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-3 col-xl-3">
           <div class="rectangle-comments">
                <div class="row" style="display:flex; align-items: flex-start;">
                    <img class="col-4"src="{{ asset('assets/website/img/asterisco.svg') }}" alt="asterisco" style="width:100%; padding-left:0px; padding-right:0px;">
                    <div class="col-8" style="display: flex; flex-direction: column; align-items: flex-end;">
                        <p class="text2-comments" >Acabei de receber a minha encomenda, empresa idónea, eficiente, personalizada simpáticos no atendimento ao cliente.
                        </p>
                        <h4 class="name-comments">Paula Martins</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <div class="empresas row">
        <img class="col-3"src="{{ asset('assets/website/img/via-direta.svg') }}" alt="asterisco" style="width:100%;">
        <img class="col-3"src="{{ asset('assets/website/img/via-direta.svg') }}" alt="asterisco" style="width:100%;">
        <img class="col-3"src="{{ asset('assets/website/img/via-direta.svg') }}" alt="asterisco" style="width:100%;">
        <img class="col-3"src="{{ asset('assets/website/img/via-direta.svg') }}" alt="asterisco" style="width:100%;">
    </div>
    
</section> --}}

@include('website.partials.contact')
@stop
@section('styles')
    <style>
         .row {
            margin-right: 0px;
            margin-left: 0px;
        }
    </style>
@stop
@section('scripts')
    <script>
        $(window).resize(function() {
            handleSize();
        });

        $(document).ready(function() {
            handleSize();
            opacity();
        });

        function handleSize() {
                if($(window).width() >= 0 && $(window).width() <= 550){
                $(".eficiency-icon").attr("src","/assets/website/img/values-icons/eficiencia-xs.svg");
                $(".trust-icon").attr("src","/assets/website/img/values-icons/confianca-xs.svg");
                $(".compromise-icon").attr("src","/assets/website/img/values-icons/compromisso-xs.svg");
                $(".security-icon").attr("src","/assets/website/img/values-icons/seguranca-xs.svg");
                $(".simplicity-icon").attr("src","/assets/website/img/values-icons/simplicidade-xs.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 550 && $(window).width() <= 768){
                $(".eficiency-icon").attr("src","/assets/website/img/values-icons/eficiencia-lg.svg");
                $(".trust-icon").attr("src","/assets/website/img/values-icons/confianca-lg.svg");
                $(".compromise-icon").attr("src","/assets/website/img/values-icons/compromisso-lg.svg");
                $(".security-icon").attr("src","/assets/website/img/values-icons/seguranca-lg.svg");
                $(".simplicity-icon").attr("src","/assets/website/img/values-icons/simplicidade-lg.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 768 && $(window).width() <= 1200){
                $(".eficiency-icon").attr("src","/assets/website/img/values-icons/eficiencia-xl.svg");
                $(".trust-icon").attr("src","/assets/website/img/values-icons/confianca-xl.svg");
                $(".compromise-icon").attr("src","/assets/website/img/values-icons/compromisso-xl.svg");
                $(".security-icon").attr("src","/assets/website/img/values-icons/seguranca-xl.svg");
                $(".simplicity-icon").attr("src","/assets/website/img/values-icons/simplicidade-xl.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 1200 && $(window).width() <= 1300){
                $(".eficiency-icon").attr("src","/assets/website/img/values-icons/eficiencia-xs.svg");
                $(".trust-icon").attr("src","/assets/website/img/values-icons/confianca-xs.svg");
                $(".compromise-icon").attr("src","/assets/website/img/values-icons/compromisso-xs.svg");
                $(".security-icon").attr("src","/assets/website/img/values-icons/seguranca-xs.svg");
                $(".simplicity-icon").attr("src","/assets/website/img/values-icons/simplicidade-xs.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 1300 && $(window).width() <= 1580){
                $(".eficiency-icon").attr("src","/assets/website/img/values-icons/eficiencia-sm.svg");
                $(".trust-icon").attr("src","/assets/website/img/values-icons/confianca-sm.svg");
                $(".compromise-icon").attr("src","/assets/website/img/values-icons/compromisso-sm.svg");
                $(".security-icon").attr("src","/assets/website/img/values-icons/seguranca-sm.svg");
                $(".simplicity-icon").attr("src","/assets/website/img/values-icons/simplicidade-sm.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 1580 && $(window).width() <= 1800){
                $(".eficiency-icon").attr("src","/assets/website/img/values-icons/eficiencia-md.svg");
                $(".trust-icon").attr("src","/assets/website/img/values-icons/confianca-md.svg");
                $(".compromise-icon").attr("src","/assets/website/img/values-icons/compromisso-md.svg");
                $(".security-icon").attr("src","/assets/website/img/values-icons/seguranca-md.svg");
                $(".simplicity-icon").attr("src","/assets/website/img/values-icons/simplicidade-md.svg");
                $(".class-texto2").attr("style","padding: 80px 50px 30px;");
            }
            else if($(window).width() > 1800){
                $(".eficiency-icon").attr("src","/assets/website/img/values-icons/eficiencia-xl.svg");
                $(".trust-icon").attr("src","/assets/website/img/values-icons/confianca-xl.svg");
                $(".compromise-icon").attr("src","/assets/website/img/values-icons/compromisso-xl.svg");
                $(".security-icon").attr("src","/assets/website/img/values-icons/seguranca-xl.svg");
                $(".simplicity-icon").attr("src","/assets/website/img/values-icons/simplicidade-xl.svg");
                $(".class-texto2").attr("style","padding: 100px 50px 30px;");
            }
        }

        function opacity(){

            $(".eficiency-icon").parent().on("mouseover", function(){
                $(".eficiency-icon").attr("style","opacity: 1;");
            });
            $(".eficiency-icon").parent().on("mouseout", function(){
                $(".eficiency-icon").attr("style","opacity: 0.4;");
            });

            $(".trust-icon").parent().on("mouseover", function(){
                $(".trust-icon").attr("style","opacity: 1;");
            });
            $(".trust-icon").parent().on("mouseout", function(){
                $(".trust-icon").attr("style","opacity: 0.4;");
            });

            $(".compromise-icon").parent().on("mouseover", function(){
                $(".compromise-icon").attr("style","opacity: 1;");
            });
            $(".compromise-icon").parent().on("mouseout", function(){
                $(".compromise-icon").attr("style","opacity: 0.4;");
            });


            $(".security-icon").parent().on("mouseover", function(){
                $(".security-icon").attr("style","opacity: 1;");
            });
            $(".security-icon").parent().on("mouseout", function(){
                $(".security-icon").attr("style","opacity: 0.4;");
            });



            $(".simplicity-icon").parent().on("mouseover", function(){
                $(".simplicity-icon").attr("style","opacity: 1;");
            });
            $(".simplicity-icon").parent().on("mouseout", function(){
                $(".simplicity-icon").attr("style","opacity: 0.4;");
            });
        }

    </script>
@stop

