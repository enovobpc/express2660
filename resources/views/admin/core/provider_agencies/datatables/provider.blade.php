@if(@$row->provider == 'envialia')
    <label class="label providerlbl" style="background: #ff5818">
        Enviália
    </label>
@elseif(@$row->provider == 'tipsa')
    <label class="label providerlbl" style="background: #005fd8">
        Tipsa
    </label>
@elseif(@$row->provider == 'gls')
    <label class="label providerlbl" style="background: #ffba2a">
        GLS
    </label>
@elseif(@$row->provider == 'estafetas')
    <label class="label providerlbl" style="background: #aa785c">
        Estafetas
    </label>
@elseif(@$row->provider == 'expresso')
    <label class="label providerlbl" style="background: #ed3c31">
        Expresso
    </label>
@elseif(@$row->provider == 'pesados')
    <label class="label providerlbl" style="background: #2fb0fa">
        Estafetas
    </label>
@else
    <label class="label providerlbl" style="background: #8625de">
        Outros
    </label>
@endif