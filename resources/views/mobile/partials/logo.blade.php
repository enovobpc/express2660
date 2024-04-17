<div class="logo">
    @if(Session::has('source_user_id'))
        <div style="    position: absolute;
    background: #fcd435;
    padding: 5px 17px;
    border-radius: 40px;
    margin: -4px 38px;">
            <i class="fas fa-exclamation-triangle"></i> Sessão iniciada como {{ Auth::user()->name }}. <a href="{{ route('admin.users.remote-logout',  Session::get('source_user_id')) }}">Voltar à minha sessão</a>
        </div>
    @endif
    <a href="{{ route('mobile.index') }}">
        @if(File::exists(public_path() . '/assets/img/logo/logo_white.svg'))
            <img src="{{ asset('assets/img/logo/logo_white.svg') }}" style="height: 30px; margin-top: -3px;">
        @else
            <img src="{{ asset('assets/img/logo/logo_white_sm.png') }}" style="height: 30px; margin-top: -3px;">
        @endif
    </a>
</div>