<footer>
    <div class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 hidden-xs hidden-sm">
                    <img src="{{ asset('assets/img/logo/logo_white_sm.png') }}" class="m-b-10"/>
                    <p>{{ trans('website_about.we-are.text2') }}</p>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-7 col-lg-offset-1">
                    <div class="row">
                        <div class="col-sm-4">
                    <h4 class="text-white text-uppercase">{{ trans('website_global.footer.services') }}</h4>
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('services.index') }}" class="link-animated" title="{{ trans('website_services.service-type.express.title') }}">
                                {{ trans('website_services.service-type.express.title') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('services.index') }}" class="link-animated" title="{{ trans('website_services.service-type.peninsular.title') }}">
                                {{ trans('website_services.service-type.peninsular.title') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('delivery-areas.index') }}" class="link-animated" title="{{ trans('website_global.menu.delivery-areas') }}">
                                {{ trans('website_global.menu.delivery-areas') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('quotation.index') }}" class="link-animated" title="{{ trans('website_global.menu.quotation-request') }}">
                                {{ trans('website_global.menu.quotation-request') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-4">
                    <h4 class="text-white text-uppercase">{{ trans('website_global.footer.join-us') }}</h4>
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('about.index') }}" class="link-animated" title="{{ trans('website_global.menu.about-us') }}">
                                {{ trans('website_global.menu.about-us') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('be-customer.index') }}" class="link-animated" title="{{ trans('website_global.menu.be-customer') }}">
                                {{ trans('website_global.menu.be-customer') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('recruitment.index') }}" class="link-animated" title="{{ trans('website_global.menu.recruitment') }}">
                                {{ trans('website_global.menu.recruitment') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contacts.index') }}" class="link-animated" title="{{ trans('website_global.menu.contacts') }}">
                                {{ trans('website_global.menu.contacts') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-4">
                    <h4 class="text-white text-uppercase">{{ trans('website_global.footer.customer-support') }}</h4>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-fw fa-phone"></i> (+351) {{ phone_format(Setting::get('phone_1')) }}
                        </li>
                        <li>
                            <i class="fas fa-mobile bigger-140 p-l-5 p-r-5"></i> (+351) {{ phone_format(Setting::get('mobile_1')) }}
                        </li>
                        <li>
                            <i class="fas fa-fw fa-envelope"></i> {{ Setting::get('email_1') }}</a>
                        </li>
                        <li>
                            <a href="{{ Setting::get('facebook') }}" class="link-animated" target="_blank"><i class="fas fa-fw fa-facebook-official"></i> Facebook</a>
                        </li>
                    </ul>
                </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="credits">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    ©{{ date('Y') }}. Asfaltolargo Unipessoal Lda. |
                    <a href="{{ route('legal.show') }}">{{ trans('website_global.footer.legal-notices') }}</a>
                </div>
                <div class="col-md-4">
                    <div class="poweredby pull-right">
                        <a href="http://www.enovo.pt/" target="_blank" alt="ENOVO - Web Design, E-commerce e Marketing Digital">
                            <img src="http://enovo.pt/assets/img/signatures/enovo_white.png" alt="ENOVO - Web Design, E-commerce e Marketing Digital">
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</footer>