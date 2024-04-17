
<div>
    <table>
        <tr>
            <td>
                <img src="{{ asset('assets/img/default/flag_ue.png') }}" style="width: 100px; margin-left: 30px"/>
            </td>
            <td>
                <h1>
                    Certificado de trabalhador de transportes internacional
                </h1>
            </td>
        </tr>
    </table>
</div>

<br>

<div style="margin-left: 40px">
    <p style="font-size: 14px; font-weight: bold">
      O presente documento confirma que:
    </p>

    <table>
        <tr>
            <td style="font-size: 12px;">
                Nome próprio e apelido:
            </td>
            <td style="font-size: 12px; font-weight: bold">
                @if(!empty($user->name)){{trim($user->name)}}@else N/A @endif
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px;">
                Data de nascimento:
            </td>
            <td style="font-size: 12px; font-weight: bold">
                @if(!empty($user->birthdate)){{trim($user->birthdate)}}@else N/A @endif
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px;">
                Residência:
            </td>
            <td style="font-size: 12px; font-weight: bold">
                @if(!empty($user->address)){{trim($user->address)}}@else N/A @endif, 
                @if(!empty($user->zip_code)){{trim($user->zip_code)}}@else N/A @endif, 
                @if(!empty($user->city)){{trim($user->city)}}@else N/A @endif
            </td>
        </tr>
    </table>
    <br>
    <p style="font-size: 12px; font-weight: bold">
        desempenha atividades no domínio dos transportes internacionais enquanto *
    </p>
</div>

<div style="margin-left: 40px;">
    <table style="font-size: 12px">
        @foreach($activitiesPT as $activitiesPT)
                <tr>
                    <td>
                        <img style="height: 10px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAdgAAAHYBTnsmCAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAABXSURBVDiN7cyhDkBgGIXh5zczbsOmEBT3fwWKDdFtMEmgSV8TvO2E8yR3HUqxDiwJDSasQaBFn6PAhiEIzCiy4OnVD/zAN4AcJ2qMwW+NMz2jRRUEdqwX7wwLPZa20SQAAAAASUVORK5CYII=">                    </td>
                    <td>
                        {{$activitiesPT}}
                    </td>
                </tr>
        @endforeach
        <tr>
            <td>
                <img style="height: 10px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAdgAAAHYBTnsmCAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAABXSURBVDiN7cyhDkBgGIXh5zczbsOmEBT3fwWKDdFtMEmgSV8TvO2E8yR3HUqxDiwJDSasQaBFn6PAhiEIzCiy4OnVD/zAN4AcJ2qMwW+NMz2jRRUEdqwX7wwLPZa20SQAAAAASUVORK5CYII=">
            </td>
            </td>
            <td>
                outro trabalhador no setor dos transportes, especificar:
            </td>
        </tr>
    </table>
    <p>
        ________________________________________________________________________________________________________________________________
    </p>
</div>

<div style="margin-left: 40px;">
    <p style="font-size: 12px">
        * Assinalar com uma cruz
    </p>
    <p style="font-size: 12px">
        <span style="font-weight: bold">
            Local, data:
        </span>
        @if(!empty($user->city)){{trim($user->city)}}@else N/A @endif,
        @if(!empty($user->country)){{trim( trans('country.'.$user->country) )}}@else N/A @endif,
        {{\Carbon\Carbon::now()->format('Y')}}
    </p>
</div>
<br>

<div style="align-content: center; text-align: center">
    <p style="font-size: 12px">
        <span style="font-weight: bold;">
            Pela empresa/agência/organismo
        </span>
        <em>
            (Nome e assinatura):
        </em>
    </p>
    <br>
    <br>

    <p>
        ______________________________________________________
    </p>
</div>

<div style="page-break-after: always"></div>
<div>
    <table>
        <tr>
            <td>
                <img src="{{ asset('assets/img/default/flag_ue.png') }}" style="width: 100px; margin-left: 30px"/>
            </td>
            <td>
                <h1>
                    Certificate for International Transport Worker
                </h1>
            </td>
        </tr>
    </table>
</div>

<br>


<div style="margin-left: 40px">
    <p style="font-size: 14px; font-weight: bold">
        It is hereby confirmed that the person:
    </p>

    <table>
        <tr>
            <td style="font-size: 12px;">
                Name and surname: 
            </td>
            <td style="font-size: 12px; font-weight: bold">
                @if(!empty($user->name)){{trim($user->name)}}@else N/A @endif
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px;">
                Birth date:
            </td>
            <td style="font-size: 12px; font-weight: bold">
                @if(!empty($user->birthdate)){{trim($user->birthdate)}}@else N/A @endif
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px;">
                Residence:
            </td>
            <td style="font-size: 12px; font-weight: bold">
                @if(!empty($user->address)){{trim($user->address)}}@else N/A @endif, 
                @if(!empty($user->zip_code)){{trim($user->zip_code)}}@else N/A @endif, 
                @if(!empty($user->city)){{trim($user->city)}}@else N/A @endif
            </td>
        </tr>
    </table>
    <br>
    <p style="font-size: 12px; font-weight: bold">
        carries out activities in international transport as *
    </p>
</div>

<div style="margin-left: 40px;">
    <table style="font-size: 12px">
        @foreach($activitiesEn as $activitiesEn)
                <tr>
                    <td>
                        <img style="height: 10px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAdgAAAHYBTnsmCAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAABXSURBVDiN7cyhDkBgGIXh5zczbsOmEBT3fwWKDdFtMEmgSV8TvO2E8yR3HUqxDiwJDSasQaBFn6PAhiEIzCiy4OnVD/zAN4AcJ2qMwW+NMz2jRRUEdqwX7wwLPZa20SQAAAAASUVORK5CYII=">                    </td>
                    <td>
                        {{$activitiesEn}}
                    </td>
                </tr>
        @endforeach
        <tr>
            <td>
                <img style="height: 10px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAdgAAAHYBTnsmCAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAABXSURBVDiN7cyhDkBgGIXh5zczbsOmEBT3fwWKDdFtMEmgSV8TvO2E8yR3HUqxDiwJDSasQaBFn6PAhiEIzCiy4OnVD/zAN4AcJ2qMwW+NMz2jRRUEdqwX7wwLPZa20SQAAAAASUVORK5CYII=">
            </td>
            </td>
            <td>
                other transport worker, please specify:
            </td>
        </tr>
    </table>
    <p>
        ________________________________________________________________________________________________________________________________
    </p>
</div>

<div style="margin-left: 40px;">
    <p style="font-size: 12px">
        * Mark with a cross
    </p>
    <p style="font-size: 12px">
        <span style="font-weight: bold">
            Place, date:
        </span>
        @if(!empty($user->city)){{trim($user->city)}}@else N/A @endif,
        @if(!empty($user->country)){{trim( trans('country.'.$user->country) )}}@else N/A @endif,
        {{\Carbon\Carbon::now()->format('Y')}}
    </p>
</div>
<br>

<div style="align-content: center; text-align: center">
    <p style="font-size: 12px">
        <span style="font-weight: bold;">
            For the company/office/organization
        </span>
        <em>
            (Name and signature):
        </em>
    </p>
    <br>
    <br>

    <p>
        ______________________________________________________
    </p>
</div>