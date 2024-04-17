<header id="header" class="header" style="background: #fff; border-bottom: 5px solid {{ env('APP_MAIL_COLOR_PRIMARY') }}; border-top: none;">
    <div class="container">
        <div class="branding">
            <h3 class="text-black pull-right m-t-15 m-b-0">API Version
                <div class="btn-group pull-right m-l-10" style="margin-top: -4px">
                    <button type="button" class="btn btn-sm btn-orange dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        1.1 <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="">1.1</a></li>
                    </ul>
                </div>
            </h3>
            <h1 class="logo">
                <a href="http://www.quickbox.pt">
                   {{-- <span aria-hidden="true" class="icon_documents_alt icon"></span>
                    <span class="text-highlight text-bold">SIGEST</span>
                    <span class="text-white">Documentação API</span>--}}
                    <img src="{{ asset('assets/img/logo/logo_sm.png') }}" onerror="this.src = '{{ asset('assets/img/default/logo/logo_sm.png') }}'" class="height-40 pull-left" style="margin-top: 0; margin-bottom: -10px;">
                    <div class="pull-left" style="
                    margin-top: 12px;
                    margin-left: 10px;
                    font-size: 18px;
                    text-transform: none;
                    background: {{ env('APP_MAIL_COLOR_PRIMARY') }};
                    padding: 5px 15px;
                    color: #fff;
                    border-radius: 50px 0;">API Documentation</div>
                </a>
            </h1>
        </div>
    </div>
</header>