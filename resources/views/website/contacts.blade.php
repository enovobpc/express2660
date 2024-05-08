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
<section class="topo-about" style="height: 380px; position:relative; background: url('/assets/website/img/contactos-topo.png'); background-size: cover; background-position: bottom 0px left 0px; display: flex; align-items: flex-end; display: flex; align-items: center;">
    <div class="col-sm-12 todos-topos">
        <h1 class="text-top-about">{{trans('website.contacts.maintitle')}}</h1>
        <div style="display: flex; align-items: center; margin-top:15px;">
            <p class="text-top2">HOME > </p>
            <p class="text-top2" style="text-decoration: underline;">&nbspContactos</p>
        </div>
    </div>
</section> 
<section class="container contacts-container">
    
    <div class="row" style="margin-top: 50px;">
        <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
            <div class="rectangleab-contacts1">
                <h4 class="about-home text-uppercase">{{trans('website.contacts.title')}}</h4>
                <h2 class="about2-home text-uppercase">{!!trans('website.contacts.subtitle')!!}</h2>
                <div class="contact-form">
                        {{ Form::open([ 'class' => 'ajax-form', 'required']) }}
                        <div class="row">
                            <div class="col-sm-12 m-b-10" style="padding-left: 0px;">
                            <div class="row" style="margin-right:0px !important; margin-left: 0px !important;">
                                    <div class=" col-sm-12 col-md-12 col-12 col-xs-12 form-group form-group-lg is-required" style="padding-left: 0px !important; padding-right: 0px !important;">
                                        {{ Form::label('name', trans('website.contacts.form-placeholders.name'), ['style' => 'font-weight: normal'] ) }}
                                        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 m-b-10" style="padding-left: 0px;">
                                <div class="row" style="margin-right:0px !important; margin-left: 0px !important;">
                                    <div class="col-sm-12 col-md-12 col-12 col-xs-12 form-group form-group-lg is-required" style="padding-left: 0px !important; padding-right: 0px !important;">
                                        {{ Form::label('email', trans('website.contacts.form-placeholders.email'), ['style' => 'font-weight: normal'] ) }}
                                        {{ Form::text('email', null, ['class' => 'form-control', 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 m-b-10" style="padding-left: 0px;">
                                <div class="row" style="margin-right:0px !important; margin-left: 0px !important;">
                                    <div class="col-sm-12 col-md-12 col-12 col-xs-12 form-group form-group-lg is-required" style="padding-left: 0px !important; padding-right: 0px !important;">
                                        {{ Form::label('phone', trans('website.contacts.form-placeholders.phone'), ['style' => 'font-weight: normal'] ) }}
                                        {{ Form::text('phone', null, ['class' => 'form-control', 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 m-b-10" style="padding-left: 0px;">
                                <div class="row" style="margin-right:0px !important; margin-left: 0px !important;">
                                    <div class="col-sm-12 col-md-12 col-12 col-xs-12 form-group form-group-lg is-required" style="padding-left: 0px !important; padding-right: 0px !important;">
                                        {{ Form::label('message', trans('website.contacts.form-placeholders.message'), ['style' => 'font-weight: normal'] ) }}
                                        {{ Form::textarea('message', null, ['class' => 'form-control', 'required', 'rows' => 4]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="button-submeter">     
                            <div style="display: flex; font-size: 15px;">
                                <p style="color:#054F0D;">*</p><p style="color:#9F9F9F">{{trans('website.contacts.required')}}</p>
                            </div>
                            <div class="row row-0">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <div class="checkbox" style="margin-left: 20px; margin-top: 0">
                                            <label style="padding: 0; margin-bottom: 5px; ">
                                                <input type="checkbox" style="display: inline; width: auto;" name="accept" required>  {!!trans('website.contacts.rgpd')!!}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" style="display: block; width: 200px; margin-bottom: 1%;" class="btn btn-pedir btn-form-orange btn-candidatura">Enviar</button>                                       
                        </div>
                        {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6 classse-contacts">
            <div class="card-contacts text-white">
                <h4 class="fs-20 text-uppercase">{{trans('website.contacts.card-contacts.title')}}</h4>
                <h2 class="fs-25 text-uppercase">{{trans('website.contacts.card-contacts.subtitle')}}</h2>
                <div class="m-t-30">
                    <div class="locat-footer" style="margin-bottom:10px;">
                        <img src="{{ asset('assets/website/img/localizacao2.svg') }}"  style="margin-right:15px; width: 40px; padding: 5px 0;">
                        <p>Estrada Nacional 3792970-129 Sesimbra</p>
                    </div>
                    <div class="locat-footer" style="margin-bottom:10px;">
                        <img src="{{ asset('assets/website/img/mensagens-footer.svg') }}"  style="margin-right:15px; width: 40px; padding: 5px 0;">
                        <p>+351 960399003</p>
                    </div>
                    <div class="locat-footer" style="margin-bottom:30px;">
                        <img src="{{ asset('assets/website/img/telefone-footer.svg') }}"  style="margin-right:15px; width: 40px; padding: 5px 0;">
                        <p>geral@2660express.pt</p>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</section>


<div class="shadowContainer">
    <div class="shadow"></div>
    <div class="shadowContent">
        <div class="containerSmall">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ac consequat ligula. Aliquam finibus risus a rutrum volutpat. Donec tempus iaculis maximus. Ut scelerisque tortor in semper dictum. Fusce ullamcorper risus eget diam fermentum ultrices. Maecenas pharetra pellentesque urna, bibendum volutpat justo. Aliquam egestas odio quis purus ornare sollicitudin.
Etiam mattis orci id ante vehicula vehicula. Sed consequat interdum orci aliquet dapibus. Proin pharetra luctus pharetra. Sed iaculis nibh nulla, eu consectetur libero vulputate at. Interdum et malesuada fames ac ante ipsum primis in faucibus. In eu risus justo. Aliquam egestas risus mi, sit amet vehicula nibh lobortis rhoncus. Nullam justo justo, faucibus tristique aliquam a, tristique imperdiet tellus. Nulla facilisi.
Proin mauris libero, blandit nec risus eget, efficitur laoreet magna. Donec nulla sapien, laoreet mattis sem eu, ultrices luctus turpis. Donec sed leo nec nibh dignissim placerat auctor at ante. Nunc faucibus sit amet libero et luctus. Pellentesque non nulla scelerisque, sollicitudin dolor posuere, blandit libero. Donec ullamcorper leo eget semper egestas. Phasellus pharetra lacus sapien, at sagittis libero molestie sit amet. In sodales neque sit amet blandit aliquet. Proin vitae dolor nisi. Nunc aliquam felis aliquam, ornare neque luctus, blandit neque.
        </div>
    </div>
    <div class="shadowCover"></div>
</div>


<div>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3124.7851645946657!2d-9.138837406828122!3d38.44643099917402!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd192f2546eb7a4f%3A0xcdd780f948ddb587!2s2660EXPRESS%20-%20Log%C3%ADstica%20e%20Distribui%C3%A7%C3%A3o%20Fulfillment%20Ecommerce!5e0!3m2!1spt-PT!2spt!4v1713882770518!5m2!1spt-PT!2spt" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

@stop
@section ('styles')
    <style>
        .row {
            margin-right: 0px;
            margin-left: 0px;
        }

        .form-control {
            background-color: transparent;
            border-radius: 0!important;
            border: none;
            border-bottom: 2px solid #337731;
            padding-left: 0;
            box-shadow: none;
            font-size: 10px!important;
        }
        
        .form-control:focus {
            border-bottom: 2px solid #28a745;
            background: transparent;
        }
        textarea.form-control {
            height: 36px;
        }
        .btn-pedir{
            display: block;
            color: #69B539;
            font-weight: 600;
            border-width: 2px;
            width: 200px;
            margin-bottom: 1%;
            border-color: #69B539;
            font-size: 18px;
            border-radius: 11px;
            padding: 10px;
        }
        .btn-pedir:hover{
            display: block;
            color: #69B539;
            font-weight: 600;
            border-width: 2px;
            width: 200px;
            margin-bottom: 1%;
            border-color: #69B539;
            font-size: 18px;
            border-radius: 11px;
            padding: 10px;
            box-shadow: rgb(11 131 28 / 31%) 0px 5px 15px;
        }
        .btn-pedir:focus{
            display: block;
            color: #69B539;
            font-weight: 600;
            border-width: 2px;
            width: 200px;
            margin-bottom: 1%;
            border-color: #69B539;
            font-size: 18px;
            border-radius: 11px;
            padding: 10px;
            box-shadow: rgb(11 131 28 / 31%) 0px 5px 15px;
        }
        .btn-pedir:focus-visible{
            display: block;
            color: #69B539;
            font-weight: 600;
            border-width: 2px;
            width: 200px;
            margin-bottom: 1%;
            border-color: #69B539;
            font-size: 18px;
            border-radius: 11px;
            padding: 10px;
            box-shadow: rgb(11 131 28 / 31%) 0px 5px 15px;
        }
        .rua {
            color: #A29F9F;
            font-size: 17px;
            font-style: normal;
            font-weight: 500;
            line-height: 23px;
        }
        @media (min-width:1365px ) and (max-width:1440px){
            #background-contactos {
                height: 157vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -447px right -776px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1330px ) and (max-width:1365px){
            #background-contactos {
                height: 157vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -447px right -736px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1310px ) and (max-width:1330px){
            #background-contactos {
                height: 157vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -447px right -717px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1300px ) and (max-width:1310px){
            #background-contactos {
                height: 157vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -459px right -760px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1290px ) and (max-width:1300px){
            #background-contactos {
                height: 157vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -447px right -771px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1280px ) and (max-width:1290px){
            #background-contactos {
                height: 171vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -354px right -881px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1255px ) and (max-width:1280px){
            #background-contactos {
                height: 171vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -354px right -874px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1225px ) and (max-width:1255px){
            #background-contactos {
                height: 171vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -335px right -883px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1200px ) and (max-width:1225px){
            #background-contactos {
                height: 171vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -314px right -891px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1181px ) and (max-width:1200px){
            #background-contactos {
                height: auto;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -293px right -684px;
                background-repeat: no-repeat;
                /*background-image: url('/assets/website/img/fundocarga.png') !important;*/
            }
            .section-map {
                margin-top: 0px; 
            }
        }
        @media (min-width:1170px ) and (max-width:1181px){
            #background-contactos {
                height: 171vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -276px right -676px;
                background-repeat: no-repeat;
            }
        }
        @media (min-width:1160px ) and (max-width:1170px){
            #background-contactos {
                height: 171vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -276px right -672px;
                background-repeat: no-repeat;
            }
        }
        @media (max-width:1199px){
            #background-contactos {
                height: auto;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top 369px right -470px;
                display: flex;
                background-repeat: no-repeat;
                justify-content: center;
                background-image: url(/assets/website/img/contactos-xs.png) !important;
            }
            .section-map {
                margin-top: 0px; 
            }
            .class-contactos1{
                padding-left: 0% !important;
                display: flex;
                margin-top: 5%;
                flex-direction: column;
                align-items: center !important;
                align-content: center !important;
            }
            .dept-contact {
                display: flex;
                flex-direction: row;
            }
            .dept.comercial{
                margin-top:0%;
            }
            .form-contactos {
                /* align-items: flex-start; */
                justify-content: flex-start !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
            }
            .contact-form{
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .button-submeter{
                display: flex;
                flex-direction: column;
                align-items: center; 
            }
        }
         @media (max-width:820px){
            .title-contacts {
                font-size: 22px;
            }
            .contact-departamento {
                color: #ffffff;
                font-weight: 600;
                font-size: 16px;
            }
            .contactos-contactos {
                color: #ffffff;
                /* width: 100%; */
                font-size: 13px;
            }
            #background-contactos {
                height: auto;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top 345px right -470px;
                display: flex;
                background-repeat: no-repeat;
                justify-content: center;
                background-image: url(/assets/website/img/contactos-xs.png) !important;
            }
        }
        @media (max-width:768px){
            .contact-contact{
                align-items: center !important;
                justify-content: flex-start !important;
                display: flex !important;
                padding-top: 2%;
                flex-direction: column;
            }
            .rua {
                font-size: 15px;
            }
            .transnos-nunber {
                font-size: 15px;
            }
            .form-contactos {
                /* align-items: flex-start; */
                justify-content: flex-start !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                margin-bottom: 32px !important;
            }
        }
        .contact-contact{
            align-items: flex-start; 
            justify-content: flex-start; 
            display: flex; 
            padding-top: 2%; 
            flex-direction: column;
        }
        @media (max-width:580px){
            .dept-contact {
                display: flex !important;
                flex-direction: column !important;
            }
            #background-contactos {
                height: auto;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top 346px right -470px !important;
                display: flex;
                background-repeat: no-repeat;
                justify-content: center;
                background-image: url(/assets/website/img/contactos-xs.png) !important;
            }
            .dep-trafego{
                display: flex;
                flex-direction: column;
                align-items: center;  
            }
            .dept.comercial{
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
            }
        }
         @media (max-width:394px){
            .contactos-contactos {
                font-size: 10px !important;
            }
        }

        .class-contactos1{
            display: flex; 
            flex-direction: column;
            align-items: flex-start;
            padding-left: 25%;
        }
        .dept.comercial{
            margin-top: 6%;
        }
        .form-contactos{
            align-items: flex-start; 
            justify-content: flex-start; 
            display: flex; 
            flex-direction: column; 
        }
        label {
            display: inline-block;
            margin-bottom: 0rem;
            font-size: 17px;
            color: #828282;
        }
        .form-group-lg .form-control {
            height: 23px;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
        }
        .form-group-lg textarea.form-control {
            height: 35px;
        }
        .form-group.is-required > label:after, .label-required:after {
            color: #337731;
            vertical-align: super;
            font-size: 60%;
            content: " *";
            text-shadow: 0 0 2px #337731;
        }
        .form-group {
            margin-bottom: 2rem;
        }
        input {
            font-size: 15px;
            padding: 10px;
            display: block;
            margin-right: 15px;
            width: 50px;
            border: none;
            border-radius: 7px;
            background-color: #ffffff;
            background: #ffffff;
        }
        .checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] {
            position: relative;
            margin-top: 4px\9;
            /* margin-left: -20px; */
        }
        @media (min-width:1675px) and (max-width:1950px){
            #background-contactos {
                height: 157vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -478px right -1003px;
                background-repeat: no-repeat;
            }
        }
        @media (width:1280px) and (height:800px){
            #background-contactos {
                height: 171vh;
                /* margin-top: -55%; */
                /* margin-left: 59%; */
                background-size: cover;
                background-position: top -354px right -792px !important;
                background-repeat: no-repeat;
            }
        }
        

        .shadowContainer {
  max-width: 400px;
  margin: 40px auto;
  padding: 30px;
  overflow: visible;
  position: relative;
  background-color: white;
}
.shadowContainer .shadow {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  -webkit-box-shadow: 0 0 13px 5px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: 0 0 13px 5px rgba(0, 0, 0, 0.1);
  box-shadow: 0 0 13px 5px rgba(0, 0, 0, 0.1);
  z-index: 1;
}
.shadowContainer .shadowContent {
  position: relative;
  z-index: 3;
}
.shadowContainer .shadowCover {
  position: absolute;
  top: -30px;
  bottom: -30px;
  left: -30px;
  right: -30px;
  /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#1e5799+0,ffffff+100&0+0,1+100 */
  background: -moz-linear-gradient(top, rgba(30, 87, 153, 0) 0%, white 100%);
  /* FF3.6-15 */
  background: -webkit-linear-gradient(top, rgba(30, 87, 153, 0) 0%, white 100%);
  /* Chrome10-25,Safari5.1-6 */
  background: linear-gradient(to bottom, rgba(30, 87, 153, 0) 0%, white 100%);
  /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#001e5799', endColorstr='#ffffff',GradientType=0);
  /* IE6-9 */
  z-index: 2;
}
    </style>
@stop

