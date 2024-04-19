<header>

    <nav class="nav navbar-expand-lg w-100 fixed-top">
        <div class="container nav-container">
          <div class="navbar-short">
            <a class="navbar-brand" href="/">
              <img class="logo_nav" src="/assets/img/logo/logo_sm.png" alt="Transcruzado Logo">         
            </a>
        
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="margin-left: auto;">
              <span class="navbar-toggler-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" class="bi bi-list" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
              </span>
            </button>
          </div>
          <div class="navbar-right m-auto navbar-collapse collapse p-15" id="navbarNav">
            <ul class="navbar-nav">
                <!--<li class="nav-item dropdown">-->
                <!--    <a href="/" class="nav-link" style="color: #cf1300;">Início</a>-->
                <!--</li>-->
                <li class="nav-item dropdown">
                    <a href="{{ route('about.index')}}" class="nav-link" style="color: #cf1300;">Empresa</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link" style="color: #cf1300; display:flex; flex-direction:row; align-items:center;">
                        <snap>Serviços</snap>
                        <span class="caret"></span> 
                    </a>  
                    <div class="dropdown-menu nav-tabs" aria-labelledby="navbarDropdown">
                        <a href="{{route('services.index')}}" class="dropdown-item" title="Carga Completa">Serviços</a>
                        <a href="{{route('storage.index')}}" class="dropdown-item" title="Carga Completa">Armazenagem</a>
                        <a href="{{route('packaging.index')}}"  class="dropdown-item" title="Temperatura Controlada">Embalamento e Etiquetagem</a>
                        <a href="{{route('distribution.index')}}" class="dropdown-item" title="Transporte de Mudanças">Distribuição</a>
                        <a href="{{route('callcenter.index')}}" class="dropdown-item" title="Serviço de Estafetagem">Call Center</a>
                        <a href="{{route('ecommerce.index')}}" class="dropdown-item" title="Serviço de Grupagem">E-Commerce</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{ route('tracking.index')}}" class="nav-link"style="color: #cf1300;">Seguir Envio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" style="color: #cf1300; display:flex; flex-direction:row; align-items:center;">
                        <snap>Contactos</snap>
                        <span class="caret"></span> 
                    </a>             
                    <div class="dropdown-menu nav-tabs" aria-labelledby="navbarDropdown">
                        <a href="{{route('contacts.index')}}"  class="dropdown-item" title="Carga Completa">Contactos</a>
                        <a  href="{{route('budget.index')}}" class="dropdown-item" title="Temperatura Controlada">Pedir Orçamento</a>
                        <a  href="{{route('recruitment.index')}}" class="dropdown-item" title="Transporte de Mudanças">Recrutamento</a>
                    </div>
                </li>
                

                <li class="nav-item">
                    <button class="btn btn-navcolor m-l-15">
                      <a href="{{ route('account.login')}}" class="nav-link" style="font-weight: 400; color: white !important;">{{ trans('website.navbar.account') }}</a>
                    </button> 
                </li>

                <li class="nav-item dropdown" >
                  <a href="#" style="color: #cf1300;" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="padding-right: 0!important">
                      <i class="flag-icon flag-icon-{{ App::getLocale() }}"></i>
                      <span class="text-uppercase">{{ App::getLocale() }}</span>
                      {{-- <span class="caret text-white"></span> --}}
                  </a>
                  <ul class="dropdown-menu" style="background: #ffffff !important;">
                      @foreach(trans('locales') as $key => $locale)
                          <li>
                              <a href="/{{ $key }}" class="{{ $key == App::getLocale() ? 'active' : '' }}">
                                <i class="flag-icon flag-icon-{{ $key }}"></i> {{ $locale }}
                              </a>
                          </li>
                      @endforeach
                  </ul>
              </li>
            </ul>
          </div>        
        </div>
    </nav>

    <style>
        .container {
            display: flex;
            align-content: center;
            flex-direction: row;
            align-items: center;
        }
        .p-15 {
            padding: 0px;
        }
        .flag-icon-pt{
            background-image:none;
        }
        .flag-icon {
            width: 0em;
        }
        .navbar-toggler:focus{
            box-shadow:none;
        }

    </style>
</header>

