<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-3">
                        <h5 class="bold text-uppercase text-primary"><i class="fas fa-link"></i> Tráfego</h5>
                        <ul class="nav nav-stacked analytics-menu">
                            <li class="active"><a href="{{ route('admin.website.visits.get', 'fullReferrer') }}" class="load-analytics">Origem das visitas <i class="fas fa-angle-right pull-right"></i></a></li>
                            <li><a href="{{ route('admin.website.visits.get', 'keyword') }}" class="load-analytics">Palavras-chave <i class="fas fa-angle-right pull-right"></i></a></li>
                            <li><a href="{{ route('admin.website.visits.get', 'pagePath') }}" class="load-analytics">Páginas mais Vistas <i class="fas fa-angle-right pull-right"></i></a></li>
                        </ul>

                        <h5 class="bold text-uppercase text-primary"><i class="fas fa-globe"></i> Dados demográficos</h5>
                        <ul class="nav nav-stacked analytics-menu">
                            <li><a href="{{ route('admin.website.visits.get', 'language') }}" class="load-analytics">Idioma <i class="fas fa-angle-right pull-right"></i></a></li>
                            <li><a href="{{ route('admin.website.visits.get', 'country') }}" class="load-analytics">País <i class="fas fa-angle-right pull-right"></i></a></li>
                            <li><a href="{{ route('admin.website.visits.get', 'city') }}" class="load-analytics">Cidade <i class="fas fa-angle-right pull-right"></i></a></li>
                        </ul>

                        <h5 class="bold text-uppercase text-primary"><i class="fas fa-desktop"></i> Sistema</h5>
                        <ul class="nav nav-stacked">
                            <li><a href="{{ route('admin.website.visits.get', 'browser') }}" class="load-analytics">Navegador <i class="fas fa-angle-right pull-right"></i></a></li>
                            <li><a href="{{ route('admin.website.visits.get', 'operatingSystem') }}" class="load-analytics">Sistema Operativo <i class="fas fa-angle-right pull-right"></i></a></li>
                            <li><a href="{{ route('admin.website.visits.get', 'screenResolution') }}" class="load-analytics">Resolução de Ecrã <i class="fas fa-angle-right pull-right"></i></a></li>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="analytics-result">
                            @include('admin.website.visits.partials.full_referrer')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>