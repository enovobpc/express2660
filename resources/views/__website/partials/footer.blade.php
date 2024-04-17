<footer class="footer" id="footer">
    <div class="container d-md-flex">

        <div class="col-md-4 mr-md-auto text-center text-md-left">
            <div class="copyright p-t-10">
                &copy; {{ date('Y') }} <strong><span>2660 Express</span></strong>. All Rights Reserved.
            </div>
        </div>
        <div class="col-md-4 col-xs-12 text-right">
            <div class="row">
                <div class="col-sm-6 text-center p-t-10">
                    {{--<a href="https://www.enovo.pt" class="credits" title="Enovo - Webdesign, E-commerce e Soluções Web">
                            <img src="https://enovo.pt/assets/img/signatures/enovo_white.svg" style="height: 16px; margin-top: 10px; ">
                    </a>--}}
                    2660 Unipessoal Lda
                </div>
                <div class="col-sm-6">
                    <a href="https://www.livroreclamacoes.pt/" class="credits">
                        <img src="https://www.livroreclamacoes.pt/Pedido/img/LRE_Theme.Logo_White.png?05CfBRVXskp07svwn4m+4A" style="height: 16px; margin-top: 15px;" alt="livro de reclamacoes">
                    </a>
                </div>
            </div>
        </div>
        
        <div class="social-links text-center text-md-right pt-3 pt-md-0 col-md-4">
            @if(!empty(Setting::get('facebook')))
                <a href="{{ Setting::get('facebook') }}" class="facebook"><i class="bx bxl-facebook"></i></a>
            @endif
            @if(!empty(Setting::get('linkedin')))
                <a href="{{ Setting::get('linkedin') }}" class="linkedin"><i class="bx bxl-linkedin"></i></a>
            @endif
            @if(!empty(Setting::get('instagram')))
                <a href="{{ Setting::get('instagram') }}" class="instagram"><i class="bx bxl-instagram"></i></a>
            @endif

        </div>
    </div>
</footer>
<a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>