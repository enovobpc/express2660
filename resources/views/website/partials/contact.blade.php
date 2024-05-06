<section>
    <div class="contact-us row">
        <div class="col-sm-12 col-md-4 col-lg-3 p-0">
            <img src="{{ asset('assets/website/img/recrutamento-1.png') }}" alt="sobre-nos" >
        </div>
        <div class="home2-recruitment col-sm-12 col-md-8 col-lg-9 p-t-20 p-b-20">
            <h4 class="title-recrutamento" style="text-transform: uppercase;">{{trans('website.contacts.section.title')}}</h4>
            <h1 class="sub-recrutamento" style="color:#69B539;">{!!trans('website.contacts.section.subtitle')!!}</h1>
            <button class="btn btn-recrutamento">
                <a href="{{route(('contacts.index'))}}"" class="nav-link" style="font-weight: 400; color: #69B539 !important;">CONTACTAR</a>
            </button>
        </div>
    </div>
</section>