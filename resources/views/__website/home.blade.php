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
<div class="container" style="height: 0px">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="group-track">
                <div class="row">
                    <div class="col-sm-12">
                        <!--<p class="main-text hidden-xs hidden-sm">{{ trans('website.banner.title') }}</p>
                        <p class="second-text hidden-xs hidden-sm">{{ trans('website.banner.subtitle') }}</p>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="slider" class="carousel carousel-fade slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        @foreach ($sliders as $key => $slide)
            <div id="img_carousel" class="item imgcarousel {{ $key == 0 ? 'active' : '' }} " style="background-image: url({{ asset($slide->filepath) }}); background-size: cover; background-repeat: no-repeat;">
            </div>
        @endforeach
        <a class="carousel-control-prev" href="#slider" role="button" data-slide="prev">
            <span class="fa fa-angle-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#slider" role="button" data-slide="next">
            <span class="fa fa-angle-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
        </div>
    </div>
</div>


    <main id="main">

        <!-- ======= About Us Section ======= -->
        <section id="{{ trans('website.routes.about') }}" class="about-us">
            <div class="container" data-aos="fade-up">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-xs-12 col-sm-7">
                                <h1>{{ trans('website.about.title') }}</h1><br>
                                <p class="about">{{ trans('website.about.text01') }}
                                    <br>
                                    <br>{{ trans('website.about.text02') }}
                                    <br>
                                    <br>{{ trans('website.about.text03') }}
                                    <br>
                                    <br>{{ trans('website.about.text04') }}
                            </div>
                            <div class="col-xs-12 col-sm-5">
                                <img class="img-about" src="{{ asset('assets/website/img/sobre-2660express.jpg') }}" alt="transportadoras de Santa Iria de Azoia">
                            </div>
                        </div>
                    </div>
                </div>
        </section>

        <div class="site-section bg-image overlay" style="background-image: url({{ asset('assets/website/img/background-about.jpg') }})"
            id="section-how-it-works">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-7 text-center border-primary">
                        <h2 class="font-weight-light text-white mb-4" data-aos="fade">{{ trans('website.about.subtitle') }}</h2>
                        <p>

                                <div class="col-md-5ths" style="color:white;margin-bottom: 30px">
                                   <i class="fas fa-check" style="margin-left:0%;"></i>	&nbsp;	&nbsp;&nbsp;&nbsp;{{ trans('website.about.value01') }}<br/> 
                                </div>
                                <div class="col-md-5ths" style="color:white;margin-bottom: 30px">
                                    <i class="fas fa-check"></i> &nbsp;&nbsp;&nbsp;{{ trans('website.about.value02') }}<br/>
                                </div>
                                <div class="col-md-5ths" style="color:white;margin-bottom: 30px">
                                    <i class="fas fa-check"></i> {{ trans('website.about.value03') }}<br/>
                                </div>
                                <div class="col-md-5ths" style="color:white;margin-bottom: 30px">
                                    <i class="fas fa-check"></i> {{ trans('website.about.value04') }}<br/>
                                </div>
                                <div class="col-md-5ths" style="color:white;margin-bottom: 30px">
                                    <i class="fas fa-check"></i> {{ trans('website.about.value05') }}<br/>
                                </div>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======= Services Section ======= -->

        <section id="{{ trans('website.routes.services') }}" class="services section-bg">
            <div class="container" data-aos="fade-up">
                <div class="row">
                    <h1>{{ trans('website.services.title') }}</h1><br>
                </div>
                <div class="row">
                    <div class="col-md-5ths d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                        <div class="icon-box iconbox-blue">
                            <div class="col-sm-12">
                                <div class="icon">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h4 class="service-title">{{ trans('website.services.service01.title') }}</h4>
                                <p>{{ trans('website.services.service01.subtitle') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5ths d-flex align-items-stretch" data-aos="zoom-in"
                        data-aos-delay="200">
                        <div class="icon-box iconbox-orange ">
                            <div class="col-sm-12">
                                <div class="icon">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h4 class="service-title">{{ trans('website.services.service02.title') }}</h4>
                                <p>{{ trans('website.services.service02.subtitle') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5ths d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in"
                        data-aos-delay="300">
                        <div class="icon-box iconbox-pink">
                            <div class="col-sm-12">
                                <div class="icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h4 class="service-title">{{ trans('website.services.service03.title') }}</h4>
                                <p>{{ trans('website.services.service03.subtitle') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5ths d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in"
                        data-aos-delay="300">
                        <div class="icon-box iconbox-pink">
                            <div class="col-sm-12">
                                <div class="icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h4 class="service-title">{{ trans('website.services.service04.title') }}</h4>
                                <p>{{ trans('website.services.service04.subtitle') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5ths d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in"
                        data-aos-delay="300">
                        <div class="icon-box iconbox-pink">
                            <div class="col-sm-12">
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h4 class="service-title">{{ trans('website.services.service05.title') }}</h4>
                                <p>{{ trans('website.services.service05.subtitle') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ======= Trace & Trace Section ======= -->

        <section id="{{ trans('website.routes.tracking') }}" class="track-trace section-bg">
            <div class="container" data-aos="fade-up">
                <div class="row">
                    <div class="col-md-3 hidden-xs hidden-sm">
                        <i class="fas fa-shipping-fast truck-track"></i>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <h1>{{ trans('website.tracking.title') }}</h1>
                                </div>
                                <div class="row">
                                    <p>{{ trans('website.tracking.subtitle') }}</p>
                                </div>
                            </div>
                        </div>
                        <form method="GET" action="{{ route('website.tracking.index') }}" accept-charset="UTF-8">

                            <div class="row track-group">
                                <div class="col-sm-10" style="padding-left:0px">
                                    <div class="grouptrack">
                                        <input type="text" name="tracking" required>
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label>{{ trans('website.tracking.text01') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-info btn-track">{{ trans('website.tracking.follow') }}  <i class="fas fa-location-arrow"></i></button>
                                </div>
                            </div>
                        </form>
                    
                    </div>
                </div>
            </div>

        </section>


        <!-- ======= Contacts Section ======= -->
        <section id="{{ trans('website.routes.contacts') }}" class="about-us map-background">
            <div class="container" data-aos="fade-up">
                    <h1>{{ trans('website.contacts.title') }}</h1>
                <div class="row">
                    <div class="col-sm-12 col-md-4 contacts-right-col">
                        <ul class="list-unstyled">
                            <li>
                                <p class="info"><i class="fa fa-map-marker-alt contacts-background-icon" aria-hidden="true"></i>
                                <span class="hidden-xs"></span>{{ Setting::get('address_1') }}<br>
                                <span class="hidden-xs"></span></span>{{ Setting::get('zip_code_1') }} {{ Setting::get('city_1') }}</p><br>
                            </li>
                            <li>
                                <p class="info"><i class="fa fa-fw fa-phone contacts-background-icon"></i>
                                <span class="hidden-xs"></span>{{ Setting::get('support_phone_1') }}</p><br>
                            </li>
                            <li>
                                <p class="info"><i class="fa fa-fw fa-envelope contacts-background-icon"></i>
                                    <span class="hidden-xs"></span>{{ Setting::get('support_email_1') }}<br>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-12 col-md-8">
                       <h1>{{ trans('website.seo.contactform.title') }}</h1>
                            <div class="margin-top-20 visible-sm visible-xs"></div>
                            <form method="POST" action="{{ route('website.contacts.mail') }}" accept-charset="UTF-8"
                                class="ajax-form" required=""><input name="_token" type="hidden"
                                    value="7t1ccYMhNaZlKlC7MNac3jAxQNqF8o6jgmqmaOPX">
                            <div class="col-sm-12 col-md-6 form-group">
                                <input class="form-control"
                                    placeholder="{{ trans('website.word.name') }}" required name="name" type="text">
                            </div>
                            <div class="col-sm-12 col-md-6 form-group">
                                <input class="form-control" placeholder="{{ trans('website.word.email') }}" required name="email" type="text">
                            </div>
                            <div class="col-sm-12 form-group">
                                <input class="form-control" placeholder="{{ trans('website.word.subject') }}" required name="subject" type="text">
                            </div>
                            <div class="col-sm-12 form-group">
                                <textarea class="form-control"
                                    placeholder="{{ trans('website.word.message') }}" rows="8" required name="message" cols="50"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-submit"
                                data-loading-text="A submeter...">{{ trans('website.word.send') }}</button>
                            <input name="source" type="hidden" value="">
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <div id="map"></div>
    </main>
@stop
@section('scripts')

<script>
    $(document).ready(function(){
      if($(window).width() < 768) {
         console.log($(window).width());
        $('#img_carousel').css("background-image: url({{ asset($slide->filepath_xs) }})!important; height:600px;"));
      }
    
    });
</script>

<script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB199E8Ikr-c8ojPBrpyBVbZkSE2PPv3T0&callback=initMap&libraries=&v=weekly"
      async></script>
      
<script>
    
    // Styles a map in.
        "use strict";
        function initMap() {
            
            const myLatLng = {
            lat: 38.83953123092138,
            lng: -9.081733587114503
            };

            var map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 38.83953123092138, lng: -9.081733587114503 },
            zoom: 10,
            disableDefaultUI: true,
            styles: [
                {elementType: 'geometry', stylers: [{color: '#20a54f'}]},
                {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
                {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
                {
                featureType: 'administrative.locality',
                elementType: 'labels.text.fill',
                stylers: [{color: '#d59563'}]
                },
                {
                featureType: 'poi',
                elementType: 'labels.text.fill',
                stylers: [{color: '#d59563'}]
                },
                {
                featureType: 'road',
                elementType: 'geometry',
                stylers: [{color: '#333333'}]
                },
                {
                featureType: 'road',
                elementType: 'geometry.stroke',
                stylers: [{color: '#333333'}]
                },
                {
                featureType: 'road',
                elementType: 'labels.text.fill',
                stylers: [{color: '#333333'}]
                },
                {
                featureType: 'road.highway',
                elementType: 'geometry',
                stylers: [{color: '#333333'}]
                },
                {
                featureType: 'road.highway',
                elementType: 'geometry.stroke',
                stylers: [{color: '#333333'}]
                },
                {
                featureType: 'road.highway',
                elementType: 'labels.text.fill',
                stylers: [{color: '#333333'}]
                },
                {
                featureType: 'transit',
                elementType: 'geometry',
                stylers: [{color: '#EB0029'}]
                },
                {
                featureType: 'transit.station',
                elementType: 'labels.text.fill',
                stylers: [{color: '#EB0029'}]
                },
                {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{color: '#0d4023'}]
                },
                {
                featureType: 'water',
                elementType: 'labels.text.fill',
                stylers: [{color: '#515c6d'}]
                },
                {
                featureType: 'water',
                elementType: 'labels.text.stroke',
                stylers: [{color: '#0d4023'}]
                }
            ]
            
            });
            new google.maps.Marker({
            position: myLatLng,
            map,
            title: "Hello World!"
            });
            function toggleBounce() {
                if (marker.getAnimation() != null) {
                    marker.setAnimation(null);
                } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            }
        }
</script>
@stop
