<footer>
    <div class="row footer-img">
            <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-xl-6 logo-footer">
                <img src="{{ asset('assets/img/logo/logo.svg') }}" class="logo">  
                <div class="locat-footer" style="margin-bottom:10px;">
                    <img src="{{ asset('assets/website/img/localizacao2-green.svg') }}"  style="margin-right:15px; width: 40px;">
                    <p class="text-footer">Estrada Nacional 3792970-129 Sesimbra</p>
                </div>
                <div class="locat-footer" style="margin-bottom:10px;">
                    <img src="{{ asset('assets/website/img/mensagens-footer-green.svg') }}"  style="margin-right:15px; width: 45px;">
                    <p class="text-footer">+351 960399003</p>
                </div>
                <div class="locat-footer" style="margin-bottom:30px;">
                    <img src="{{ asset('assets/website/img/telefone-footer-green.svg') }}"  style="margin-right:15px; width: 40px;">
                    <p class="text-footer">geral@2660express.pt</p>
                </div> 
                <div class="locat-footer" style="margin-bottom:10px;">
                    <a href="https://www.instagram.com/2660express/" target="_blank">
                        <img src="{{ asset('assets/website/img/facebook-1.svg') }}"  style="margin-right:15px; width: 40px;">
                    </a>
                    <a href="https://www.instagram.com/2660express/" target="_blank">
                        <img src="{{ asset('assets/website/img/instagram-5.svg') }}"  style="margin-right:15px; width: 40px;">
                    </a>
                    <a href="https://pt.linkedin.com/company/2660express" target="_blank">
                        <img src="{{ asset('assets/website/img/social-1.svg') }}"  style="margin-right:15px; width: 40px;">
                    </a>
                </div>                 
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-2 col-xl-2 footer-list" style="display:flex; justify-content: flex-start!important;">
                <ul class="small-txt af-light">
                    <li>
                        <a style="font-size: 20px; font-weight: 500; color:#00491A !important;" href="{{route('services.index')}}">
                            {{ trans('website.footer.services.title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('storage.index')}}">
                            {{ trans('website.footer.services.storage') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('distribution.index')}}">
                            {{ trans('website.footer.services.distribution') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('callcenter.index')}}">
                            {{ trans('website.footer.services.callcenter') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('ecommerce.index')}}">
                            {{ trans('website.footer.services.ecommerce') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('packaging.index')}}">
                            {{ trans('website.footer.services.packing') }}
                        </a>
                    </li>                                    
                </ul>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-2 col-xl-2 footer-list" style="display:flex; justify-content: flex-start!important;">
                <ul class="small-txt af-light">
                    <li>
                        <a href="{{route('contacts.index')}}" style="font-size: 20px; font-weight: 500; color:#00491A !important;">
                            {{ trans('website.footer.contacts.title') }}
                        </a>
                    </li>
                     <li>
                        <a href="{{route('contacts.index')}}">
                            {{ trans('website.footer.contacts.contacts') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('budget.index')}}">
                            {{ trans('website.footer.contacts.budget') }}
                        </a>
                    </li>                                                
                </ul>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-2 col-xl-2 footer-list" style="display:flex; justify-content: flex-start!important;">
                <ul class="small-txt af-light">
                    <li>
                        <a  style="font-size: 20px; font-weight: 500; color:#00491A !important;">
                            {{ trans('website.footer.company.title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('about.index')}}">
                            {{ trans('website.footer.company.about') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('about.index')}}">
                            {{ trans('website.footer.company.mission') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('about.index')}}">
                            {{ trans('website.footer.company.values') }}
                        </a>
                    </li>                                                
                </ul>
            </div>
        
        </div>
        <div class="row parte-baixo bottom-info-left" style="">
            <div class=" col-12 col-sm-12 col-md-8 col-xl-8 class-avisos">
                <span class="small-txt af-light" style="color:#ffffff">© {{ date('Y') }}. 2660 Express
                    <a class="baixo" href="{{ route('legal.show') }}">| Avisos Legais</a>
                    <a  class="baixo" target="_blank" href="https://www.livroreclamacoes.pt/Inicio/">| Livro Reclamações</a>    
                </span>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-xl-4 class-avisos" style="justify-content: flex-end;">
                <p  class="baixo2">Desenvolvido por 
                    <a style="margin-left: 10px;" class="bottom-info-right text-right" href="https://www.enovo.pt/" target="_blank">
                        <img src="{{ asset('assets/website/img/logo-enovo.svg') }}"  style="width: 100%;">
                    </a>  
                </p>
            </div>
        </div>
</footer>

