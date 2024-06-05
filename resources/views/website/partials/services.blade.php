<section class="services-home">
   <div class=" col-sm-12"> 
    <div class="col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20">
        <h4 class="servicestitle-home">{{ trans('website.services.title') }}</h4>
        <h2 class="servecessubtitle-home">{{ trans('website.services.storage.description') }}</h2>
    </div>
    <div class=" col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20">
        <div class="section-sobre">

            <img class="service-icon armazenagem-icon" src="/assets/website/img/services-icons/armazenagem-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2" style="padding:0;">
                <h4 class="text-arma">{!! trans('website.services.storage.title') !!}</h4>
                <p class="texte-armaze-1">{!! trans('website.services.storage.description') !!}</p>
            </div>  
            <button class="btn btn-services">
                <a href="{{route('storage.index')}}" class="nav-link text-uppercase">{!!trans('website.btns.see-more')!!}</a>
            </button>
        </div>          
    </div>
    <div class=" col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20">
        <div class="section-sobre">
            <img class="service-icon embalamento-icon" src="/assets/website/img/services-icons/embalamento-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2" style="padding:0;" >
                <h4 class="text-arma">{!! trans('website.services.packing.title') !!}</h4>
                <p class="texte-armaze-1">{!! trans('website.services.packing.description') !!}</p>
            </div> 
            <button class="btn btn-services">
                <a  href="{{route('packaging.index')}}" class="nav-link text-uppercase">{!!trans('website.btns.see-more')!!}</a>
            </button> 
        </div>
    </div>
   </div>

   <div class=" col-sm-12">
    <div class=" col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20">
        <div class="section-sobre">
            
            <img class="service-icon distribuicao-icon" src="/assets/website/img/services-icons/distribuicao-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2" style="padding:0;">
                <h4 class="text-arma">{!! trans('website.services.distribution.title') !!}</h4>
                <p class="texte-armaze-1">{!! trans('website.services.distribution.description') !!}</p>
            </div> 
            <button class="btn btn-services">
                <a href="{{route('distribution.index')}}" class="nav-link text-uppercase">{!!trans('website.btns.see-more')!!}</a>
            </button> 
        </div>
    </div>
    

    <div class=" col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20">
        <div class="section-sobre">
            
            <img class="service-icon devolution-icon" src="/assets/website/img/services-icons/devolucoes-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2" style="padding:0;">
                <h4 class="text-arma">{!! trans('website.services.devolutions.title') !!}</h4>
                <p class="texte-armaze-1">{!! trans('website.services.devolutions.description') !!}</p>
            </div> 
            <button class="btn btn-services">
                <a href="{{route('devolutions.index')}}" class="nav-link text-uppercase">{!!trans('website.btns.see-more')!!}</a>
            </button> 
        </div>
    </div>
    <div class=" col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20">
        <div class="section-sobre" >
            
            <img class=" service-icon charge-icon" src="/assets/website/img/services-icons/cobranca-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2" style="padding:0;">
                <h4 class="text-arma">{!! trans('website.services.charge.title') !!}</h4>
                <p class="texte-armaze-1">{!! trans('website.services.charge.description') !!}</p>
            </div>  
            <button class="btn btn-services">
                <a href="{{route('charge.index')}}" class="nav-link text-uppercase">{!!trans('website.btns.see-more')!!}</a>
            </button>
        </div>
    </div>


   </div>

   <div class=" col-sm-12">
    <div class=" col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20">
        <div class="section-sobre" >
            
            <img class=" service-icon callcenter-icon" src="/assets/website/img/services-icons/callcenter-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2" style="padding:0;">
                <h4 class="text-arma">{{ trans('website.services.callcenter.title') }}</h4>
                <p class="texte-armaze-1">{{ trans('website.services.callcenter.description') }}</p>
            </div>  
            <button class="btn btn-services">
                <a href="{{route('callcenter.index')}}" class="nav-link text-uppercase">{{trans('website.btns.see-more')}}</a>
            </button>
        </div>
    </div>
    <div class=" col-sm-12 col-md-12 col-xl-4  m-t-20 m-b-20" >
        <div class="section-sobre">
            
            <img class="service-icon ecommerce-icon" src="/assets/website/img/services-icons/ecommerce-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2">
                <h4 class="text-arma" style="padding:0;">{{ trans('website.services.ecommerce.title') }}</h4>
                <p class="texte-armaze-1">{{ trans('website.services.ecommerce.description') }}</p>
            </div>  
            <button class="btn btn-services">
                <a href="{{route('ecommerce.index')}}" class="nav-link text-uppercase">{{trans('website.btns.see-more')}}</a>
            </button>
        </div>
    </div>
    <div class=" col-sm-12 col-xl-4  m-t-20 m-b-20" >
        <div class="section-sobre">
            
            <img class="service-icon api-icon" src="/assets/website/img/services-icons/api-xs.svg" height="100%" width="100%" style="opacity: 0.4;">
            <div class="class-texto2">
                <h4 class="text-arma" style="padding:0;">{{ trans('website.services.api.title') }}</h4>
                <p class="texte-armaze-1">{{ trans('website.services.api.description') }}</p>
            </div>  
            <button class="btn btn-services">
                <a href="{{route('api.index')}}" class="nav-link text-uppercase">{{trans('website.btns.see-more')}}</a>
            </button>
        </div>
    </div>
   </div>
</section>



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
                $(".armazenagem-icon").attr("src","/assets/website/img/services-icons/armazenagem-xs.svg");
                $(".embalamento-icon").attr("src","/assets/website/img/services-icons/embalamento-xs.svg");
                $(".distribuicao-icon").attr("src","/assets/website/img/services-icons/distribuicao-xs.svg");
                $(".charge-icon").attr("src","/assets/website/img/services-icons/cobranca-xs.svg");
                $(".devolution-icon").attr("src","/assets/website/img/services-icons/devolucoes-xs.svg");
                $(".api-icon").attr("src","/assets/website/img/services-icons/api-xs.svg");
                $(".callcenter-icon").attr("src","/assets/website/img/services-icons/callcenter-xs.svg");
                $(".ecommerce-icon").attr("src","/assets/website/img/services-icons/ecommerce-xs.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 550 && $(window).width() <= 768){
                $(".armazenagem-icon").attr("src","/assets/website/img/services-icons/armazenagem-lg.svg");
                $(".embalamento-icon").attr("src","/assets/website/img/services-icons/embalamento-lg.svg");
                $(".charge-icon").attr("src","/assets/website/img/services-icons/cobranca-lg.svg");
                $(".devolution-icon").attr("src","/assets/website/img/services-icons/devolucoes-lg.svg");
                $(".api-icon").attr("src","/assets/website/img/services-icons/api-lg.svg");
                $(".distribuicao-icon").attr("src","/assets/website/img/services-icons/distribuicao-lg.svg");
                $(".callcenter-icon").attr("src","/assets/website/img/services-icons/callcenter-lg.svg");
                $(".ecommerce-icon").attr("src","/assets/website/img/services-icons/ecommerce-lg.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 768 && $(window).width() <= 1200){
                $(".armazenagem-icon").attr("src","/assets/website/img/services-icons/armazenagem-xl.svg");
                $(".embalamento-icon").attr("src","/assets/website/img/services-icons/embalamento-xl.svg");
                $(".distribuicao-icon").attr("src","/assets/website/img/services-icons/distribuicao-xl.svg");
                $(".charge-icon").attr("src","/assets/website/img/services-icons/cobranca-xl.svg");
                $(".devolution-icon").attr("src","/assets/website/img/services-icons/devolucoes-xl.svg");
                $(".api-icon").attr("src","/assets/website/img/services-icons/api-xl.svg");
                $(".callcenter-icon").attr("src","/assets/website/img/services-icons/callcenter-xl.svg");
                $(".ecommerce-icon").attr("src","/assets/website/img/services-icons/ecommerce-xl.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 1200 && $(window).width() <= 1300){
                $(".armazenagem-icon").attr("src","/assets/website/img/services-icons/armazenagem-xs.svg");
                $(".embalamento-icon").attr("src","/assets/website/img/services-icons/embalamento-xs.svg");
                $(".distribuicao-icon").attr("src","/assets/website/img/services-icons/distribuicao-xs.svg");
                $(".charge-icon").attr("src","/assets/website/img/services-icons/cobranca-xs.svg");
                $(".devolution-icon").attr("src","/assets/website/img/services-icons/devolucoes-xs.svg");
                $(".api-icon").attr("src","/assets/website/img/services-icons/api-xs.svg");
                $(".callcenter-icon").attr("src","/assets/website/img/services-icons/callcenter-xs.svg");
                $(".ecommerce-icon").attr("src","/assets/website/img/services-icons/ecommerce-xs.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 1300 && $(window).width() <= 1580){
                $(".armazenagem-icon").attr("src","/assets/website/img/services-icons/armazenagem-sm.svg");
                $(".embalamento-icon").attr("src","/assets/website/img/services-icons/embalamento-sm.svg");
                $(".charge-icon").attr("src","/assets/website/img/services-icons/cobranca-sm.svg");
                $(".devolution-icon").attr("src","/assets/website/img/services-icons/devolucoes-sm.svg");
                $(".api-icon").attr("src","/assets/website/img/services-icons/api-sm.svg");
                $(".distribuicao-icon").attr("src","/assets/website/img/services-icons/distribuicao-sm.svg");
                $(".callcenter-icon").attr("src","/assets/website/img/services-icons/callcenter-sm.svg");
                $(".ecommerce-icon").attr("src","/assets/website/img/services-icons/ecommerce-sm.svg");
                $(".class-texto2").attr("style","padding: 70px 50px 30px;");
            }
            else if($(window).width() > 1580 && $(window).width() <= 1800){
                $(".armazenagem-icon").attr("src","/assets/website/img/services-icons/armazenagem-md.svg");
                $(".embalamento-icon").attr("src","/assets/website/img/services-icons/embalamento-md.svg");
                $(".charge-icon").attr("src","/assets/website/img/services-icons/cobranca-md.svg");
                $(".devolution-icon").attr("src","/assets/website/img/services-icons/devolucoes-md.svg");
                $(".api-icon").attr("src","/assets/website/img/services-icons/api-md.svg");
                $(".distribuicao-icon").attr("src","/assets/website/img/services-icons/distribuicao-md.svg");
                $(".callcenter-icon").attr("src","/assets/website/img/services-icons/callcenter-md.svg");
                $(".ecommerce-icon").attr("src","/assets/website/img/services-icons/ecommerce-md.svg");
                $(".class-texto2").attr("style","padding: 80px 50px 30px;");
            }
            else if($(window).width() > 1800){
                $(".armazenagem-icon").attr("src","/assets/website/img/services-icons/armazenagem-xl.svg");
                $(".embalamento-icon").attr("src","/assets/website/img/services-icons/embalamento-xl.svg");
                $(".distribuicao-icon").attr("src","/assets/website/img/services-icons/distribuicao-xl.svg");
                $(".charge-icon").attr("src","/assets/website/img/services-icons/cobranca-xl.svg");
                $(".devolution-icon").attr("src","/assets/website/img/services-icons/devolucoes-xl.svg");
                $(".api-icon").attr("src","/assets/website/img/services-icons/api-xl.svg");
                $(".callcenter-icon").attr("src","/assets/website/img/services-icons/callcenter-xl.svg");
                $(".ecommerce-icon").attr("src","/assets/website/img/services-icons/ecommerce-xl.svg");
                $(".class-texto2").attr("style","padding: 100px 50px 30px;");
            }
        }



        function opacity(){
            $(".armazenagem-icon").parent().on("mouseover", function(){
                $(".armazenagem-icon").attr("style","opacity: 1;");
            });
            $(".armazenagem-icon").parent().on("mouseout", function(){
                $(".armazenagem-icon").attr("style","opacity: 0.4;");
            });


            $(".embalamento-icon").parent().on("mouseover", function(){
                $(".embalamento-icon").attr("style","opacity: 1;");
            });
            $(".embalamento-icon").parent().on("mouseout", function(){
                $(".embalamento-icon").attr("style","opacity: 0.4;");
            });



            $(".distribuicao-icon").parent().on("mouseover", function(){
                $(".distribuicao-icon").attr("style","opacity: 1;");
            });
            $(".distribuicao-icon").parent().on("mouseout", function(){
                $(".distribuicao-icon").attr("style","opacity: 0.4;");
            });


            $(".charge-icon").parent().on("mouseover", function(){
                $(".charge-icon").attr("style","opacity: 1;");
            });
            $(".charge-icon").parent().on("mouseout", function(){
                $(".charge-icon").attr("style","opacity: 0.4;");
            });

            $(".devolution-icon").parent().on("mouseover", function(){
                $(".devolution-icon").attr("style","opacity: 1;");
            });
            $(".devolution-icon").parent().on("mouseout", function(){
                $(".devolution-icon").attr("style","opacity: 0.4;");
            });

            $(".api-icon").parent().on("mouseover", function(){
                $(".api-icon").attr("style","opacity: 1;");
            });
            $(".api-icon").parent().on("mouseout", function(){
                $(".api-icon").attr("style","opacity: 0.4;");
            });


            $(".callcenter-icon").parent().on("mouseover", function(){
                $(".callcenter-icon").attr("style","opacity: 1;");
            });
            $(".callcenter-icon").parent().on("mouseout", function(){
                $(".callcenter-icon").attr("style","opacity: 0.4;");
            });



            $(".ecommerce-icon").parent().on("mouseover", function(){
                $(".ecommerce-icon").attr("style","opacity: 1;");
            });
            $(".ecommerce-icon").parent().on("mouseout", function(){
                $(".ecommerce-icon").attr("style","opacity: 0.4;");
            });
        }
    </script>
@stop